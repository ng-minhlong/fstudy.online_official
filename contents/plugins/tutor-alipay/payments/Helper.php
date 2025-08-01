<?php

namespace Ollyo\PaymentHub\Payments\Alipay;


final class Helper{

    /**
     * Array of result codes indicating various error conditions.
     * @since 1.0.0
     */
    public static array $resultCode = [
        'UNKNOWN_EXCEPTION',
        'PROCESS_FAIL',
        'PARAM_ILLEGAL',
        'METHOD_NOT_SUPPORTED',
        'MEDIA_TYPE_NOT_ACCEPTABLE',
        'INVALID_API',
        'INVALID_CLIENT',
        'INVALID_SIGNATURE',
        'KEY_NOT_FOUND',
        'ACCESS_DENIED',
        'REQUEST_TRAFFIC_EXCEED_LIMIT'
    ];

    /**
     * Signs content using SHA-256 RSA encryption with the provided private key.
     *
     * @param  string $signContent The content to sign.
     * @param  string $privateKey  The private key used for signing.
     * @return string              Returns the base64-encoded signature value.
     * @since  1.0.0
     */
    public static function signWithSHA256RSA($signContent, $privateKey)
    {
        $key = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($privateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        openssl_sign($signContent, $signValue, $key, OPENSSL_ALGO_SHA256);

        return base64_encode($signValue);
    }

    /**
     * Sends a success JSON response with HTTP status code 200.
     *
     * @param  string $requestTime The timestamp of the request.
     * @param  string $clientID    The client ID associated with the response.
     * @return void
     * @since  1.0.0
     */
    public static function sendSuccessResponse($requestTime,$clientID)
    {       
        http_response_code(200);
        header("Content-Type: application/json,response-time: {$requestTime}, client-id: {$clientID}");
        
        $response = [
            'result' => (object) [
                "resultCode"    => "SUCCESS",
                "resultStatus"  => "S",
                "resultMessage" => "success"
            ]
        ];

        echo json_encode($response);
    }

    /**
     * Maps the payment result status to descriptive status and reason.
     *
     * @param  object $paymentResult The payment result object containing status information.
     * @return array                 Returns an array with the mapped status and reason.
     * @since  1.0.0
     */
    public static function statusMap($paymentResult): array
    {
        $resultMap = [
            'F' => ['failed', $paymentResult->result->resultMessage],
            'U' => ['pending', $paymentResult->result->resultMessage],
            'S' => ['paid', $paymentResult->result->resultMessage]
        ];

        return $resultMap[$paymentResult->result->resultStatus];
    }
}