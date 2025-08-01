<?php

namespace Ollyo\PaymentHub\Payments\Alipay;

use Throwable;
use ErrorException;
use GuzzleHttp\Client;
use Ollyo\PaymentHub\Core\Support\Arr;
use Ollyo\PaymentHub\Core\Support\Uri;
use Ollyo\PaymentHub\Core\Support\View;
use Ollyo\PaymentHub\Core\Support\System;
use Ollyo\PaymentHub\Core\Payment\BasePayment;
use Ollyo\PaymentHub\Exceptions\NotFoundException;
use Ollyo\PaymentHub\Exceptions\InvalidDataException;
use Ollyo\PaymentHub\Contracts\Config\RepositoryContract;
use Ollyo\PaymentHub\Exceptions\InvalidSignatureException;

class Alipay extends BasePayment
{
    /**
     * Alipay config Repository instance
     *
     * @var RepositoryContract
     */
    protected $config;

    /**
     * Holds the initial data for the payment processing
     */
    protected $initialData;

    /**
     * Stores the selected payment method
     */
    protected $paymentMethod;

    /**
     * Constants representing the status of API calls
     */
    const API_CALL_SUCCESS = 'S';
    const API_CALL_FAILED  = 'F';
    const API_CALL_PENDING = 'U';

    /**
     * Checks if all necessary configuration keys are present and not empty.
     *
     * @return bool True if all required configuration keys are set and non-empty, false otherwise.
     * @since  1.0.0
     */
    public function check(): bool
    {
        $configKeys = Arr::make(['public_key', 'private_key', 'client_id', 'region_url', 'consult_url', 'pay_url', 'inquiry_url']);

        $isConfigOk = $configKeys->every(function ($key) {
            return $this->config->has($key) && !empty($this->config->get($key));
        });

        return $isConfigOk;
    }

    public function setup(): void
    {

    }

    /**
     * Sets the data for the payment processing, preparing it if no payment method is set.
     *
     * @param  object $data The data to be set.
     * @return void
     * @throws Throwable    If an error occurs while setting the data.
     * @since  1.0.0
     */
    public function setData($data): void
    {
        try {
            parent::setData(is_null($this->paymentMethod) ? $this->prepareData($data) : $data);
        } catch (Throwable $error) {
            throw $error;
        }
    }

    /**
     * Prepares and returns the data object required for payment processing.
     *
     * @param  object $data The initial data to be prepared.
     * @return object       The prepared data object for the payment request.
     * @since  1.0.0
     */
    protected function prepareData($data): object
    {
        $this->initialData  = $data;
        $order              = $this->getOrderData();

        // Set WebhookUrl
        $webhookUrl = Uri::getInstance($this->config->get('webhook_url'));
        $webhookUrl->setVar('payment_method', $this->config->get('name'));
        
        return (object) [
            'productCode'           => 'CASHIER_PAYMENT',
            'paymentRequestId'      => 'PR' . round(microtime(true) * 1000) . '_' . $data->order_id,
            'order'                 => $order,
            'paymentAmount'         => (object) [
                'currency'  => $data->currency->code,
                'value'     => System::getMinorAmountBasedOnCurrency($this->initialData->total_price, $data->currency->code),
            ],
            'paymentRedirectUrl'    => $this->config->get('success_url'),
            'paymentNotifyUrl'      => $webhookUrl->__toString(),
            'env'                   => ['terminalType' => 'WEB'],
            'paymentFactor'         => (object) ['captureMode' => 'AUTOMATIC']
        ];
    }

    /**
     * Sets the order data for payment processing.
     *
     * @return object Returns an object containing order item details.
     * @since  1.0.0
     */
    private function getOrderData()
    {
        $returnData = (object) [
            'orderAmount'       => (object) [
                'currency'  => $this->initialData->currency->code,
                'value'     => (int) $this->initialData->total_price,
            ],
            'referenceOrderId'  => (string) $this->initialData->order_id,
            'orderDescription'  => $this->initialData->order_description,
        ];

        if (isset($this->initialData->items) && !empty($this->initialData->items)) {
            $returnData->goods = $this->getGoodsData();
        }

        if (isset($this->initialData->shipping) && !empty($this->initialData->shipping)) {
            $returnData->shipping = $this->getShippingData();
        }

        return $returnData;
    }

    /**
     * Initiates the payment creation process based on available payment methods.
     * Handles different outcomes from retrieving available payment methods.
     *
     * @since 1.0.0
     */
    public function createPayment()
    {
        $availablePaymentMethods = $this->getAvailablePaymentMethods();

        if ($availablePaymentMethods['response'] === 'success') {
           static::viewPaymentSelectionForm($availablePaymentMethods['data']);
        } elseif ($availablePaymentMethods['response'] === 'no_payment_methods') {
            static::handleNoPaymentMethods();
        }
    }

    /**
     * Verifies webhook notification and creates/updates order data based on API responses.
     *
     * @param  object $payload The payload containing server and stream data.
     * @return object          Returns an object containing processed order data.
     * @throws Throwable       Throws any caught exceptions.
     * @since  1.0.0
     */
    public function verifyAndCreateOrderData(object $payload): object
    {
        try {

            if (isset($payload->get['action']) && $payload->get['action'] === 'payment-method-selection') {
                static::handlePaymentMethodSubmission($payload);
            }

            $serverVariables    = $payload->server;
            $rawStream          = $payload->stream;
            $rawSignature       = $serverVariables['HTTP_SIGNATURE'];
            $requestURI         = parse_url($serverVariables['REQUEST_URI'], PHP_URL_PATH);
            $requestTime        = $serverVariables['HTTP_REQUEST_TIME'];
            $signature          = explode("signature=", $rawSignature)[1] ?? null;

            if (is_null($rawSignature) && is_null($signature)) {
                return new \stdClass();
            }

            if (empty($signature)) {
                throw new InvalidSignatureException("Invalid Signature");
            }

            $isVerified = static::verifyWebhookNotification($requestURI, $requestTime, $rawStream, $signature);

            if (!$isVerified) {
                throw new InvalidSignatureException("Invalid Signature");
            }

            Helper::sendSuccessResponse($requestTime, $this->config->get('client_id'));

            $payload            = json_decode($rawStream);
            $payloadStatus      = $payload->result->resultStatus;
            $payloadResultCode  = $payload->result->resultCode;

            if ($payloadStatus === static::API_CALL_FAILED || in_array($payloadResultCode, Helper::$resultCode)) {
                throw new ErrorException($payload->result->resultMessage);
            }

            // Call the inquiry Api
            $paymentRequestID       = $payload->paymentRequestId;
            $inquiryApiResult       = static::inquiryRequest($paymentRequestID);
            $inquiryApiResultStatus = $inquiryApiResult->result->resultStatus;
            $inquiryApiResultCode   = $inquiryApiResult->result->resultCode;
            $amount                 = $inquiryApiResult->paymentAmount->value ?? null;
            $currency               = $inquiryApiResult->paymentAmount->currency ?? null;

            if ($inquiryApiResultStatus === static::API_CALL_FAILED || in_array($inquiryApiResultCode, Helper::$resultCode)) {

                [$status, $errorReason] = Helper::statusMap($inquiryApiResult);
            }

            // Set the return data
            $returnData = System::defaultOrderData();

            $returnData->id                     = explode('_', $inquiryApiResult->paymentRequestId)[1];
            $returnData->payment_status         = $status ?? Helper::statusMap($inquiryApiResult)[0];
            $returnData->payment_error_reason   = $errorReason ?? $inquiryApiResult->paymentResultMessage;
            $returnData->transaction_id         = $inquiryApiResult->paymentId;
            $returnData->payment_method         = $this->config->get('name');
            $returnData->payment_payload        = json_encode($inquiryApiResult);
            $returnData->earnings               = System::convertMinorAmountToMajor($amount, $currency);

            return $returnData;
        } catch (Throwable $error) {
            throw $error;
        }
    }

    /**
     * Sets and structures goods data for the payment processing from initial item data.
     *
     * @return array an array of objects representing each item's details.
     * @since  1.0.0
     */
    private function getGoodsData(): array
    {
        $subtotal = 0;
        $items = array_map(function($item) use ($subtotal) {
            
            $price = is_null($item['discounted_price']) ? $item['regular_price'] : $item['discounted_price'];

            $subtotal += System::getMinorAmountBasedOnCurrency($price, $this->initialData->currency->code) * (int) $item['quantity'] ;
            
            return (object) [
                'referenceGoodsId'  => $item['item_id'],
                'goodsName'         => $item['item_name'],
                'goodsUnitAmount'   => (object) [
                    'currency'  => $this->initialData->currency->code,
                    'value'     => $price,
                ],
                'goodsQuantity'     => (int) $item['quantity'],
            ];
        } , (array) $this->initialData->items);

        // Add Tax Amount
        if (!empty($this->initialData->tax)) {
           $items[] = [
                'referenceGoodsId' => rand(),
                'goodsName' => 'Tax',
                'goodsUnitAmount' => (object) [
                    'currency' => $this->initialData->currency->code,
                    'value'    => System::getMinorAmountBasedOnCurrency($this->initialData->tax, $this->initialData->currency->code),
                ],
                'goodsQuantity' => 1,
            ];
        }

        $minChargeApplicable = System::isTotalAmountZero($this->initialData);

        if ($minChargeApplicable) {
            $items[] = [
                'referenceGoodsId'  => rand(),
                'goodsName'         => 'Minimum charge to process the payment',
                'goodsUnitAmount'   => (object) [
                    'currency'  => $this->initialData->currency->code,
                    'value'     => 1,
                ],
                'goodsQuantity'     => 1,
            ];

            $this->initialData->total_price = 1;
        }

        return $items;
    }

    /**
     * Retrieves available payment methods.
     *
     * @return array Returns an array indicating the result of the operation:
     *               - If api call is successful, returns available payment options.
     *               - If api call is failed, returns an error message.
     * @since 1.0.0
     */
    private function getAvailablePaymentMethods()
    {
        $consultRequestData = (object) [
            'productCode'   => 'CASHIER_PAYMENT',
            'paymentAmount' => [
                'currency'  => $this->initialData->currency->code,
                'value'     => (int) $this->initialData->total_price,
            ],
            'env'           => ['terminalType' => 'WEB'],
        ];

        $requestData = (object) [
            'api_url'   => $this->config->get('consult_url'),
            'full_url'  => $this->config->get('region_url') . $this->config->get('consult_url'),
            'options'   => ['body' => json_encode($consultRequestData)],
        ];

        $response       = $this->sendHttpRequest($requestData);
        $resultStatus   = $response->result->resultStatus;
        $resultCode     = $response->result->resultCode;
        $message        = $response->result->resultMessage;

        if ($resultStatus === static::API_CALL_FAILED || in_array($resultCode, Helper::$resultCode)) {
            throw new NotFoundException($message);
        }

        return static::setAvailablePayments($response->paymentOptions);
    }

    /**
     * Sends an HTTP POST request with provided request data.
     *
     * @param  object $requestData The request data.
     * @return object Returns the JSON-decoded response object from the HTTP request.
     * @since  1.0.0
     */
    private function sendHttpRequest(object $requestData): object
    {
        $http                               = new Client();
        $requestTime                        = date(DATE_ISO8601);
        $apiUrl                             = $requestData->api_url;
        $requestBody                        = $requestData->options['body'];
        $signValue                          = static::generateSignValue($apiUrl, $requestTime, $requestBody);
        $requestData->options['headers']    = static::buildHeader($requestTime, $signValue);

        $response = $http->post($requestData->full_url, $requestData->options);

        return json_decode((string) $response->getBody());
    }

    /**
     * Generates a signed value for authentication purposes.
     *
     * @param  string $path        The API path.
     * @param  string $requestTime The ISO 8601 formatted request timestamp.
     * @param  string $content     The request body or content to be signed.
     * @return string              Returns the URL-encoded signed value.
     *
     * @since  1.0.0
     */
    private function generateSignValue($path, $requestTime, $content): string
    {
        $privateKey = $this->config->get('private_key');
        $clientID   = $this->config->get('client_id');
        $httpMethod = $this->config->get('http_method');

        $signContent    = "{$httpMethod} {$path}\n{$clientID}.{$requestTime}.{$content}";
        $signValue      = Helper::signWithSHA256RSA($signContent, $privateKey);

        return urlencode($signValue);
    }

    /**
     * Builds headers for an HTTP request with authentication information.
     *
     * @param  string $requestTime The ISO 8601 formatted request timestamp.
     * @param  string $signValue   The URL-encoded signed value for authentication.
     * @return array               Returns an array of headers to be included in the HTTP request.
     * @since  1.0.0
     */
    private function buildHeader($requestTime, $signValue)
    {
        $keyVersion         = $this->config->get('default_key_version');
        $clientID           = $this->config->get('client_id');
        $signatureHeader    = "algorithm=RSA256,keyVersion={$keyVersion},signature={$signValue}";

        $header = [
            'Content-Type'  => 'application/json; charset=UTF-8',
            'Request-Time'  => $requestTime,
            'client-id'     => $clientID,
            'Signature'     => $signatureHeader,
        ];

        return $header;
    }

    /**
     * Initiates the payment process.
     *
     * @throws InvalidDataException If payment method or data is invalid.
     * @throws ErrorException       If the API call fails or returns an error.
     * @since  1.0.0
     */
    private function initiatePayment()
    {
        if (empty($this->paymentMethod) || empty($this->getData())) {
            throw new InvalidDataException('Invalid Information');
        }

        static::setPaymentMethod();

        $requestData = (object) [
            'api_url'   => $this->config->get('pay_url'),
            'full_url'  => $this->config->get('region_url') . $this->config->get('pay_url'),
            'options'   => ['body' => json_encode($this->getData())],
        ];

        $response = $this->sendHttpRequest($requestData);

        if ($response->result->resultStatus === static::API_CALL_FAILED || in_array($response->result->resultCode, Helper::$resultCode)) {

            throw new ErrorException($response->result->resultMessage);
        }

        $checkoutUrl = $response->normalUrl ?? $response->redirectActionForm->redirectUrl;

        header("Location: {$checkoutUrl}");
        exit();
    }

    /**
     * Sets the payment method in the payment data.
     * @since 1.0.0
     */
    private function setPaymentMethod()
    {
        $paymentData = $this->getData();
        $paymentData->paymentMethod = (object) ['paymentMethodType' => $this->paymentMethod];

        if ($this->paymentMethod === 'CARD') {
            $paymentData->paymentFactor->isAuthorization = true;
        }

        $this->setData($paymentData);
    }

    /**
     * Verifies the authenticity of a webhook notification using signature verification.
     *
     * @param  string $path        The API path or URL path of the webhook notification.
     * @param  string $requestTime The ISO 8601 formatted request timestamp of the webhook notification.
     * @param  string $body        The body or content of the webhook notification.
     * @param  string $signature   The signature received with the webhook notification.
     * @return bool                Returns true if the signature is verified successfully; otherwise, false.
     */
    private function verifyWebhookNotification($path, $requestTime, $body, $signature)
    {
        $httpMethod             = $this->config->get('http_method');
        $clientID               = $this->config->get('client_id');
        $responseSignContent    = "{$httpMethod} {$path}\n{$clientID}.{$requestTime}.{$body}";

        return static::verifySignatureWithSHA256RSA($responseSignContent, $signature);
    }

    /**
     * Verifies a signature using SHA-256 RSA encryption.
     *
     * @param  string $responseSignContent The content that was signed.
     * @param  string $signature           The signature to be verified.
     * @return int                         Returns 1 if the signature is verified and valid, 0 if it's invalid, or -1 on error.
     */
    private function verifySignatureWithSHA256RSA($responseSignContent, $signature)
    {
        $alipayPublicKey = $this->config->get('public_key');

        $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
        wordwrap($alipayPublicKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        if (strstr($signature, "=") || strstr($signature, "+") || strstr($signature, "/") || $signature == base64_encode(base64_decode($signature))) {
            $originalResponseValue = base64_decode($signature);
        } else {
            $originalResponseValue = base64_decode(urldecode($signature));
        }

        $verifyResult = openssl_verify($responseSignContent, $originalResponseValue, $publicKey, OPENSSL_ALGO_SHA256);

        return $verifyResult;
    }

    /**
     * Makes an inquiry request to retrieve payment details based on the payment request ID.
     *
     * @param  string $paymentRequestID The ID of the payment request to inquire about.
     * @return object                   Returns the response object from the inquiry request.
     * @since  1.0.0
     */
    private function inquiryRequest($paymentRequestID)
    {
        $requestData = (object) [
            'api_url'   => $this->config->get('inquiry_url'),
            'full_url'  => $this->config->get('region_url') . $this->config->get('inquiry_url'),
            'options'   => ['body' => json_encode((object) ['paymentRequestId' => $paymentRequestID])],
        ];

        return static::sendHttpRequest($requestData);
    }

    /**
     * Sets available payment methods based on provided payment options.
     *
     * @param  array $paymentOptions The array containing payment options to filter.
     * @return array                 Returns an array indicating the result of setting available payment methods.
     * @since  1.0.0
     */
    private function setAvailablePayments($paymentOptions)
    {
        $availablePaymentTypes = [];

        if (isset($paymentOptions) && is_array($paymentOptions)) {
            $availablePaymentTypes = array_filter($paymentOptions, function ($payments) {
                return $payments->enabled;
            });
        }

        if (empty($availablePaymentTypes)) {
            return ['response' => 'no_payment_methods'];
        }

        return ['response' => 'success', 'data' => $availablePaymentTypes];
    }

    /**
     * Displays a payment selection form based on available payment methods.
     *
     * @param  array $availablePaymentMethods The array of available payment methods to display.
     * @return void
     * @since  1.0.0
     */
    private function viewPaymentSelectionForm($availablePaymentMethods)
    {
        $formActionUrl = Uri::getInstance($this->config->get('webhook_url'));
        $formActionUrl->setVar('action', 'payment-method-selection');
        $formActionUrl->setVar('payment_method', $this->config->get('name'));


        $data = [
            'available_payment_methods' => $availablePaymentMethods,
            'cancel_url'                => $this->config->get('cancel_url'),
            'payment_data'              => base64_encode(json_encode($this->getData())),
            'form_action_url'           => $formActionUrl,
        ];

        echo View::make('alipay.show-payment-methods', $data)->render();
    }

    /**
     * Handles the scenario where no payment methods are available.
     * Defaults the payment method to 'CARD' and initiates the payment process.
     *
     * @return void
     * @since  1.0.0
     */
    private function handleNoPaymentMethods()
    {
        $this->paymentMethod = 'CARD';
        $this->initiatePayment();
    }

    /**
     * Handles the submission of a payment method.
     *
     * @param  object               $payload The payload containing payment method and payment data.
     * @throws InvalidDataException          If the payment data is invalid.
     * @return void
     * @since  1.0.0
     */
    private function handlePaymentMethodSubmission($payload)
    {
        $this->paymentMethod = $payload->post['payment_method'] ?? null;

        if (isset($payload->post['payment_data'])) {
            static::setData(json_decode(base64_decode($payload->post['payment_data'])));
            static::initiatePayment();

        } else {
            throw new InvalidDataException("Invalid Payment Data");
        }
    }

    /**
     * Sets the shipping data from the initial data.
     *
     * This method retrieves shipping details from the initial data
     * and returns them in a structured object format.
     *
     * @return object The shipping data
     * @since  1.0.0
     */
    private function getShippingData()
    {
        $shipping = $this->initialData->shipping;
        
        [$firstName, $lastName] = System::extractNameParts($shipping->name);
        [$address1, $address2]  = System::splitAddress($shipping, 256);

        return (object) [
            'shippingName'      => (object) [
                'firstName' => $firstName,
                'lastName'  => $lastName,
            ],
            'shippingAddress'   => (object) [
                'region'    => $shipping->region,
                'state'     => $shipping->state ?? '',
                'city'      => $shipping->city ?? '',
                'address1'  => $address1 ?? '',
                'address2'  => $address2 ?? '',
                'zipCode'   => $shipping->zip_code ?? '',
            ],
        ];
    }
}