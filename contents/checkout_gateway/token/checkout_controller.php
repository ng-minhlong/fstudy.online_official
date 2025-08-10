<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
    define('WP_SITEURL', $WP_SITEURL);

    echo "<script>const WP_SITEURL = '" . $WP_SITEURL . "';</script>";


class OnlineCheckoutController
{
    public function online_checkout()
    {
        if (isset($_POST['bankCode'])) {
            $bankCode = $_POST['bankCode'];

            if ($bankCode == "payUrl") {  // payUrl là momo
                $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
                $partnerCode = 'MOMOBKUN20180529';
                $accessKey = 'klm05TvNBzhg7h7j';
                $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

                $orderInfo = $_POST['item'];
                $amount = $_POST['amount'];
                $orderId = $_POST['orderCode'];
                $redirectUrl = WP_SITEURL . "/contents/checkout_gateway/token/completed_order_momo.php";
                $ipnUrl = "https://webhook.site/b3088a6a-2d17-4f8d-a383-71389a6c600b";
                $extraData = "";

                $requestId = time() . "";
                $requestType = "payWithATM";

                $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData .
                    "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
                    "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl .
                    "&requestId=" . $requestId . "&requestType=" . $requestType;

                $signature = hash_hmac("sha256", $rawHash, $secretKey);
                $data = array(
                    'partnerCode' => $partnerCode,
                    'partnerName' => "Test",
                    "storeId" => "MomoTestStore",
                    'requestId' => $requestId,
                    'amount' => $amount,
                    'orderId' => $orderId,
                    'orderInfo' => $orderInfo,
                    'redirectUrl' => $redirectUrl,
                    'ipnUrl' => $ipnUrl,
                    'lang' => 'vi',
                    'extraData' => $extraData,
                    'requestType' => $requestType,
                    'signature' => $signature
                );

                $result = $this->execPostRequest($endpoint, json_encode($data));
                $jsonResult = json_decode($result, true);

                if (isset($jsonResult['payUrl'])) {
                    header('Location: ' . $jsonResult['payUrl']);
                } else {
                    die('Error: Invalid response from MoMo API. Response: ' . $result);
                }
            
            } elseif ($bankCode == "vnpay") {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
                    date_default_timezone_set('Asia/Ho_Chi_Minh');
        
                    
                    require_once("./vnpay_php/config.php");
        
                    $vnp_TxnRef =  $_POST['orderCode']; //Mã giao dịch thanh toán tham chiếu của merchant
                    $vnp_Amount = $_POST['amount']; // Số tiền thanh toán
                    $vnp_Item = $_POST['item']; // Số tiền thanh toán
                    $vnp_Locale = $_POST['language']; //Ngôn ngữ chuyển hướng thanh toán
                    $vnp_BankCode = ""; //Mã phương thức thanh toán
                    $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán
        
                    $inputData = array(
                        "vnp_Version" => "2.1.0",
                        "vnp_TmnCode" => $vnp_TmnCode,
                        "vnp_Amount" => $vnp_Amount* 100,
                        "vnp_Command" => "pay",
                        "vnp_CreateDate" => date('YmdHis'),
                        "vnp_CurrCode" => "VND",
                        "vnp_IpAddr" => $vnp_IpAddr,
                        "vnp_BankCode" => $vnp_BankCode,
                        "vnp_Locale" => $vnp_Locale,
                        //"vnp_OrderInfo" => "Thanh toan GD: $vnp_Item",
                        "vnp_OrderInfo" =>  $vnp_Item,
                        "vnp_OrderType" => "other",
                        "vnp_ReturnUrl" => $vnp_Returnurl,
                        "vnp_TxnRef" => $vnp_TxnRef,
                        "vnp_ExpireDate"=>$expire
                    );
        
                    if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                        $inputData['vnp_BankCode'] = $vnp_BankCode;
                    }
        
                    ksort($inputData);
                    $query = "";
                    $i = 0;
                    $hashdata = "";
                    foreach ($inputData as $key => $value) {
                        if ($i == 1) {
                            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                        } else {
                            $hashdata .= urlencode($key) . "=" . urlencode($value);
                            $i = 1;
                        }
                        $query .= urlencode($key) . "=" . urlencode($value) . '&';
                    }
        
                    $vnp_Url = $vnp_Url . "?" . $query;
                    if (isset($vnp_HashSecret)) {
                        $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
                        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                    }
                    header('Location: ' . $vnp_Url);
                    die();
        
                
            } elseif ($bankCode == "zalopay") {
                echo 'zalopay';
            } else {
                echo 'BankCode is not recognized';
            }
        } else {
            echo 'BankCode is not set';
        }
    }

    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
}

// Khởi tạo và gọi hàm
$checkoutController = new OnlineCheckoutController();
$checkoutController->online_checkout();
?>
