<?php

namespace Ollyo\PaymentHub\Payments\Momo;

use Throwable;
use ErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Ollyo\PaymentHub\Core\Payment\BasePayment;
use Ollyo\PaymentHub\Core\Support\System;

class Momo extends BasePayment
{
    protected $initialData;

    public function check(): bool
    {
        return true;
    }

    public function setup(): void
    {
        // No-op: MoMo does not require a persistent client setup for create API
    }

    public function setData($data): void
    {
        $this->initialData = $data;
        parent::setData($data);
    }

    public function createPayment()
    {
        try {   
            // Read credentials and settings from gateway config
            $partnerCode = (string) ($this->config->get('partner_code') ?? '');
            $accessKey   = (string) ($this->config->get('access_key') ?? '');
            $secretKey   = (string) ($this->config->get('secret_key') ?? '');
            $requestType = (string) ($this->config->get('request_type') ?? 'captureWallet');
            if ($requestType === '' || $requestType === 'qrCodeUrl') {
                // 'qrCodeUrl' is not a valid requestType for MoMo create API; it's a response field
                $requestType = 'captureWallet';
            }
            $endpoint    = (string) ($this->config->get('create_url') ?? 'https://test-payment.momo.vn/v2/gateway/api/create');

            // Base order id from Tutor, sanitized
            $orderIdBase = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $this->initialData->order_id);
            if ($orderIdBase === '') {
                $orderIdBase = (string) time();
            }
            // Ensure unique orderId per attempt to avoid duplicates
            $orderSuffix = date('is') . rand(10, 99);
            $orderId     = $orderIdBase . '-' . $orderSuffix;

            $orderInfo   = 'Payment ' . $orderIdBase;
            $amountMajor = (float) $this->initialData->total_price;
            $amount      = (string) (int) $amountMajor;
            $requestId   = (string) time();

            // Use Tutor LMS webhook endpoint for both browser redirect and server IPN.
            // The handler will compute success/failure based on resultCode and redirect accordingly.
            // Use dynamic URLs from config instance so runtime filters (payment_method, order_id) apply
            $redirectUrl = (string) $this->configInstance->getWebhookUrl();
            $ipnUrl      = (string) $this->configInstance->getWebhookUrl();
            // Preserve the original order id for mapping on IPN
            $extraData   = $orderIdBase;

            $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
            $signature = hash_hmac('sha256', $rawHash, $secretKey);

            $payload = [
                'partnerCode' => $partnerCode,
                'partnerName' => 'Test',
                'storeId'     => 'EduHUB',
                'requestId'   => $requestId,
                'amount'      => $amount,
                'orderId'     => $orderId,
                'orderInfo'   => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl'      => $ipnUrl,
                'lang'        => 'vi',
                'extraData'   => $extraData,
                'requestType' => $requestType,
                'signature'   => $signature,
            ];

            $client   = new Client(['timeout' => 30]);
            $response = $client->post($endpoint, [
                'headers' => ['Accept' => 'application/json'],
                'json'    => $payload,
            ]);

            $body = json_decode((string) $response->getBody());
            if (!isset($body->resultCode) || (int) $body->resultCode !== 0) {
                $message = $body->message ?? 'MoMo create payment error';
                throw new ErrorException($message);
            }

            // Prefer qrCodeUrl when available; fall back to payUrl/deeplink
            $checkoutUrl = $body->qrCodeUrl ?? $body->payUrl ?? $body->deeplink ?? null;
            if (!$checkoutUrl) {
                throw new ErrorException('MoMo response missing qrCodeUrl/payUrl/deeplink');
            }

            header("Location: {$checkoutUrl}");
            exit();
        } catch (RequestException $e) {
            throw new ErrorException($e->getMessage());
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function verifyAndCreateOrderData(object $payload): object
    {
            $returnData = System::defaultOrderData();
        try {
            $post = $payload->post;
            if (empty($post)) {
                return $returnData;
            }

            $orderIdRaw  = $post['orderId'] ?? '';
            $extraData   = $post['extraData'] ?? '';
            $originalId  = $extraData ?: explode('-', (string) $orderIdRaw)[0];

            $resultCode   = isset($post['resultCode']) ? (int) $post['resultCode'] : null;
            $message      = $post['message'] ?? '';
            $transId      = $post['transId'] ?? '';
            $amount       = isset($post['amount']) ? (int) $post['amount'] : 0;

            // MoMo: resultCode 0 = success, others = failure
            $status = ($resultCode === 0) ? 'paid' : 'failed';

            $returnData->id                   = (string) $originalId;
            $returnData->payment_status       = $status;
            $returnData->payment_error_reason = $status === 'failed' ? $message : '';
            $returnData->transaction_id       = (string) $transId;
            $returnData->payment_method       = $this->config->get('name');
            $returnData->payment_payload      = json_encode($post);
            $returnData->earnings             = $amount;

            // Prepare redirect URL to Tutor LMS success/failure page with order id
            $successUrl = (string) $this->configInstance->getSuccessUrl();
            $cancelUrl  = (string) $this->configInstance->getCancelUrl();

            // Ensure order_id query arg is set to original order id on both URLs
            $successUrl = \add_query_arg('order_id', (string) $originalId, $successUrl);
            $cancelUrl  = \add_query_arg('order_id', (string) $originalId, $cancelUrl);

            $returnData->redirectUrl = $status === 'paid' ? $successUrl : $cancelUrl;

            return $returnData;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
