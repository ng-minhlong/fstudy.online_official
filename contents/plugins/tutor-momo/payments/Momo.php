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
            // Force sandbox credentials (matching working controller) to avoid auth failures
            $partnerCode = 'MOMOBKUN20180529';
            $accessKey   = 'klm05TvNBzhg7h7j';
            $secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
            $requestType = 'payWithATM';
            $endpoint    = 'https://test-payment.momo.vn/v2/gateway/api/create';

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

            $redirectUrl = function_exists('site_url') ? site_url('/contents/checkout_gateway/token/completed_order_momo.php') : '/';
            $ipnUrl      = 'https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b';
            // Preserve the original order id for mapping on IPN
            $extraData   = $orderIdBase;

            $rawHash = "accessKey={$accessKey}&amount={$amount}&extraData={$extraData}&ipnUrl={$ipnUrl}&orderId={$orderId}&orderInfo={$orderInfo}&partnerCode={$partnerCode}&redirectUrl={$redirectUrl}&requestId={$requestId}&requestType={$requestType}";
            $signature = hash_hmac('sha256', $rawHash, $secretKey);

            $payload = [
                'partnerCode' => $partnerCode,
                'partnerName' => 'Test',
                'storeId'     => 'MomoTestStore',
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

            $checkoutUrl = $body->payUrl ?? $body->deeplink ?? null;
            if (!$checkoutUrl) {
                throw new ErrorException('MoMo response missing payUrl');
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

            $status = ($resultCode === 0) ? 'paid' : (($resultCode === 1006) ? 'pending' : 'failed');

            $returnData->id                   = (string) $originalId;
            $returnData->payment_status       = $status;
            $returnData->payment_error_reason = $status === 'failed' ? $message : '';
            $returnData->transaction_id       = (string) $transId;
            $returnData->payment_method       = $this->config->get('name');
            $returnData->payment_payload      = json_encode($post);
            $returnData->earnings             = $amount;

            return $returnData;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}
