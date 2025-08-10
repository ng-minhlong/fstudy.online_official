<?php

namespace Ollyo\PaymentHub\Payments\Vnpay;

use Throwable;
use ErrorException;
use Ollyo\PaymentHub\Core\Payment\BasePayment;
use Ollyo\PaymentHub\Core\Support\System;

class Vnpay extends BasePayment
{
    protected $initialData;

    public function check(): bool
    {
        return true;
    }

    public function setup(): void
    {
        // No client setup needed for VNPAY redirect flow
    }

    public function setData($data): void
    {
        $this->initialData = $data;
        parent::setData($data);
    }

    public function createPayment()
    {
        try {
            date_default_timezone_set('Asia/Ho_Chi_Minh');

            $vnp_TmnCode   = $this->config->get('tmn_code') ?: '259FT5RH';
            $vnp_HashSecret= $this->config->get('hash_secret') ?: '4JZ6QHH58FYM3PSN3GF6ZP0UQJWS1WLL';
            $vnp_Url       = $this->config->get('pay_url') ?: 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
            $success_url = $this->config->get('success_url');

            $orderIdBase   = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $this->initialData->order_id);
            if ($orderIdBase === '') {
                $orderIdBase = (string) time();
            }

            $vnp_TxnRef = $orderIdBase;
            $vnp_Amount = (int) ((float) $this->initialData->total_price);
            $vnp_Item   = 'Order ' . $orderIdBase;
            $vnp_Locale = 'vn';
            $vnp_BankCode = '';
            $vnp_IpAddr   = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            // Normalize IP to IPv4 if server provides IPv6-mapped or IPv6
            if (filter_var($vnp_IpAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                if (strpos($vnp_IpAddr, '::ffff:') === 0) {
                    $maybeIpv4 = substr($vnp_IpAddr, 7);
                    if (filter_var($maybeIpv4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        $vnp_IpAddr = $maybeIpv4;
                    } else {
                        $vnp_IpAddr = '127.0.0.1';
                    }
                } else {
                    $vnp_IpAddr = '127.0.0.1';
                }
            }

            $startTime = date('YmdHis');
            $expire    = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

            $inputData = [
                'vnp_Version'   => '2.1.0',
                'vnp_TmnCode'   => $vnp_TmnCode,
                'vnp_Amount'    => $vnp_Amount * 100,
                'vnp_Command'   => 'pay',
                'vnp_CreateDate'=> date('YmdHis'),
                'vnp_CurrCode'  => 'VND',
                'vnp_BankCode'  => $vnp_BankCode,
                'vnp_IpAddr'    => $vnp_IpAddr,
                'vnp_Locale'    => $vnp_Locale,
                'vnp_OrderInfo' => $vnp_Item,
                'vnp_OrderType' => 'other',
                'vnp_ReturnUrl' => $success_url,
                'vnp_TxnRef'    => $vnp_TxnRef,
                'vnp_ExpireDate'=> $expire,
            ];

            // Only include optional fields when they have values
            if (!empty($vnp_BankCode)) {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            ksort($inputData);
            $query = '';
            $i = 0;
            $hashdata = '';
            foreach ($inputData as $key => $value) {
                // Build hashdata WITH URL encoding as per VNPAY PHP example
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . '=' . urlencode((string) $value);
                } else {
                    $hashdata .= urlencode($key) . '=' . urlencode((string) $value);
                    $i = 1;
                }
                // Build query with URL encoding
                $query .= urlencode($key) . '=' . urlencode((string) $value) . '&';
            }

            $vnp_Url = $vnp_Url . '?' . $query;
            if (!empty($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                // Append hash type as recommended by VNPAY docs, do not include it in hashdata
                $vnp_Url .= 'vnp_SecureHashType=HmacSHA512&vnp_SecureHash=' . $vnpSecureHash;
            }

            header('Location: ' . $vnp_Url);
            exit();
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function verifyAndCreateOrderData(object $payload): object
    {
        $returnData = System::defaultOrderData();
        try {
            // VNPAY returns via GET to returnUrl; Tutor passes request payload object
            $get = $payload->get ?? [];
            if (empty($get)) {
                return $returnData;
            }

            // Verify checksum from VNPAY return payload
            $receivedSecureHash = $get['vnp_SecureHash'] ?? '';
            // Only include vnp_ prefixed keys when computing signature
            $paramsForHash = [];
            foreach ($get as $key => $value) {
                if (strpos($key, 'vnp_') === 0) {
                    $paramsForHash[$key] = $value;
                }
            }
            unset($paramsForHash['vnp_SecureHash'], $paramsForHash['vnp_SecureHashType']);
            ksort($paramsForHash);
            $hashdata = '';
            $i = 0;
            foreach ($paramsForHash as $key => $value) {
                // Build hashdata WITH URL encoding as per VNPAY PHP example
                if ($i === 1) {
                    $hashdata .= '&' . urlencode($key) . '=' . urlencode((string) $value);
                } else {
                    $hashdata .= urlencode($key) . '=' . urlencode((string) $value);
                    $i = 1;
                }
            }

            $computedHash = '';
            $hashSecret = (string) ($this->config->get('hash_secret') ?: '');
            if ($hashSecret !== '') {
                $computedHash = hash_hmac('sha512', $hashdata, $hashSecret);
            }

            $txnRef   = $get['vnp_TxnRef'] ?? '';
            $amount   = isset($get['vnp_Amount']) ? (int) $get['vnp_Amount'] : 0; // in cents
            $amountMajor = (int) round($amount / 100);
            $responseCode = $get['vnp_ResponseCode'] ?? '';
            $transactionNo= $get['vnp_TransactionNo'] ?? ($get['vnp_TransactionStatus'] ?? '');
            $message      = '';

            if ($computedHash === '' || strcasecmp($receivedSecureHash, $computedHash) !== 0) {
                // Signature mismatch
                $returnData->id                   = (string) $txnRef;
                $returnData->payment_status       = 'failed';
                $returnData->payment_error_reason = 'Sai chữ ký';
                $returnData->transaction_id       = (string) $transactionNo;
                $returnData->payment_method       = $this->config->get('name');
                $returnData->payment_payload      = json_encode($get);
                $returnData->earnings             = $amountMajor;
                return $returnData;
            }

            $status = ($responseCode === '00') ? 'paid' : (($responseCode === '24') ? 'pending' : 'failed');

            $returnData->id                   = (string) $txnRef;
            $returnData->payment_status       = $status;
            $returnData->payment_error_reason = $status === 'failed' ? $message : '';
            $returnData->transaction_id       = (string) $transactionNo;
            $returnData->payment_method       = $this->config->get('name');
            $returnData->payment_payload      = json_encode($get);
            $returnData->earnings             = $amountMajor;

            return $returnData;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}


