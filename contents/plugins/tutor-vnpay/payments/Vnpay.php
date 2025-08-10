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
            $vnp_Locale = $this->config->get('language') ?: 'vn';
            $vnp_BankCode = '';
            $vnp_IpAddr   = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            $startTime = date('YmdHis');
            $expire    = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

            $inputData = [
                'vnp_Version'   => '2.1.0',
                'vnp_TmnCode'   => $vnp_TmnCode,
                'vnp_Amount'    => $vnp_Amount * 100,
                'vnp_Command'   => 'pay',
                'vnp_CreateDate'=> date('YmdHis'),
                'vnp_CurrCode'  => 'VND',
                'vnp_IpAddr'    => $vnp_IpAddr,
                'vnp_Locale'    => $vnp_Locale,
                'vnp_OrderInfo' => $vnp_Item,
                'vnp_OrderType' => 'other',
                'vnp_ReturnUrl' => $success_url,
                'vnp_TxnRef'    => $vnp_TxnRef,
                'vnp_ExpireDate'=> $expire,
            ];

            if (!empty($vnp_BankCode)) {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }

            ksort($inputData);
            $query = '';
            $i = 0;
            $hashdata = '';
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . '=' . urlencode((string) $value);
                } else {
                    $hashdata .= urlencode($key) . '=' . urlencode((string) $value);
                    $i = 1;
                }
                $query .= urlencode($key) . '=' . urlencode((string) $value) . '&';
            }

            $vnp_Url = $vnp_Url . '?' . $query;
            if (!empty($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
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

            $txnRef   = $get['vnp_TxnRef'] ?? '';
            $amount   = isset($get['vnp_Amount']) ? (int) $get['vnp_Amount'] : 0; // in cents
            $amountMajor = (int) round($amount / 100);
            $responseCode = $get['vnp_ResponseCode'] ?? '';
            $transactionNo= $get['vnp_TransactionNo'] ?? ($get['vnp_TransactionStatus'] ?? '');
            $message      = '';

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


