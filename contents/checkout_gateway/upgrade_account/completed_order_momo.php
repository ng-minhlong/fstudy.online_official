<!DOCTYPE html>
<?php
//include('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary
include($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
get_header(); // Gọi phần đầu trang (header.php)
$user_id = get_current_user_id();
$current_username = wp_get_current_user()->user_login;

global $wpdb;
$site_url = get_site_url();

// Fetch user token from the user_token table
$sql = "SELECT token, updated_time, token_use_history, token_practice FROM user_token WHERE username = %s";

$user_token_result = $wpdb->get_row($wpdb->prepare($sql, $current_username));

if ($user_token_result) {
    $user_token = $user_token_result->token;
    $updated_time = $user_token_result->updated_time;

} else {
    $user_token = '0';
    $updated_time = 'Đang cập nhật';

}

$order_info = urldecode($_GET['orderInfo']);

?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <title>MOMO</title>
        <!-- Bootstrap core CSS -->
      
    </head>
    <style>


    .container1 {
        height: 600px;
        display: block;
        margin: auto;
        top: 50%;
        left: 50%;

      background: #ffffff;
      padding: 30px 40px;
      border-radius: 20px;
      box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.1);
      max-width: 500px;
    }

    .status-success {
      color: #2d8a3a;
      font-weight: bold;
    }

    .status-fail {
      color: #d9534f;
      font-weight: bold;
    }

    .status-info {
      margin-top: 15px;
      color: #555;
    }

    

    .redirect-btn:hover {
      background: #0056b3;
    }
    .button-group {
        display: flex;
        gap: 15px; /* Khoảng cách giữa 2 button */
        margin-top: 20px;
    }

    .redirect-btn {
        display: inline-block;
        padding: 10px 25px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }

    .btn-white {
        background: white;
        color: #333;
        border: 1px solid #ddd;
    }

    .btn-white:hover {
        background: #f8f9fa;
        border-color: #ccc;
    }

    .btn-blue {
        background: #007bff;
        color: white;
    }

    .btn-blue:hover {
        background: #0056b3;
    }
    @media (max-width: 480px) {
    .button-group {
        flex-direction: column;
        gap: 10px;
    }
    
    .redirect-btn {
        width: 100%;
    }
}





    #countdown {
      font-size: 16px;
      color: #777;
      margin-top: 10px;
    }
    .body1{
        height:670px;
    }
  </style>
    <body>
        <?php
               $partnerCode = $_GET["partnerCode"];
            //   $accessKey = $_GET["accessKey"];
               $orderId = $_GET["orderId"];
           //    $localMessage = utf8_encode($_GET["localMessage"]);
               $message = $_GET["message"];
               $transId = $_GET["transId"];
               $orderInfo = utf8_encode($_GET["orderInfo"]);
               $amount = $_GET["amount"];
               $resultCode = $_GET["resultCode"];
             //  $errorCode = $_GET["errorCode"];
               $responseTime = $_GET["responseTime"];
               $requestId = $_GET["requestId"];
               $payType = $_GET["payType"];
               $orderType = $_GET["orderType"];
               $extraData = $_GET["extraData"];
               $m2signature = $_GET["signature"]; //MoMo signature
        ?>


        <!--Begin display -->
        <div class="body1">
            <div class = "container1">
            <div class="header clearfix">
            </div>
            <div class="table-responsive">
                <div class="form-group">
                    <label >Mã đơn hàng:</label>

                    <label><?php echo $_GET['orderId'] ?></label>
                </div>    
                

                <div class="form-group">


                    <label >Số tiền:</label>
                    <label><?php echo $_GET['amount'] ?></label>
                </div>  
                <div class="form-group">
                    <label >Nội dung thanh toán:</label>
                    <label><?php echo $_GET['orderInfo'] ?></label>
                </div> 
                <div class="form-group">
                    <label >Mã GD Tại MOMO:</label>
                    <label><?php echo $_GET['transId'] ?></label>
                </div> 

                <div class="form-group">
                    <label >Mã Ngân hàng:</label>
                    <label><?php echo $_GET['payType'] ?></label>
                </div> 
                <div class="form-group">
                    <label >Thời gian thanh toán:</label>
                    <label><?php echo $_GET['responseTime'] ?></label>
                </div> 

                <div class="form-group">
                    <label style = "display:none">Kết quả:</label>
                    <label>
                        <?php
                       
                    if ($_GET['resultCode'] == '00') {
                        
                    
                        $order_code = $_GET['orderId'];
                    
                        // Tìm giao dịch trong token_transaction
                        $transaction = $wpdb->get_row($wpdb->prepare(
                            "SELECT order_item, order_status, type_item FROM token_transaction WHERE order_code = %s",
                            $order_code
                        ));
                    
                        if ($transaction) {
                                    if ($transaction->order_status === 'success') {
                                        echo "<span style='color:green'>Giao dịch đã thành công trước đó. Không cập nhật thêm.</span>";
                                    } else {
                                        $order_item = json_decode($transaction->order_item, true);
                                        $amount = $order_item['amount'];
                                        $type_item = $order_item['type_item'];
                                        $accountCode = $order_item['accountCode'] ?? null;
                                        
                                        // 1. Cập nhật trạng thái giao dịch
                                        $wpdb->update(
                                            'token_transaction',
                                            ['order_status' => 'success'],
                                            ['order_code' => $order_code]
                                        );
                                        
                                        if ($type_item === 'account_package' && $accountCode) {
                                            $user_id = get_current_user_id();
                                            $api_url = add_query_arg(array(
                                                'id_role' => $accountCode,
                                                'user_id' => $user_id,
                                                'user_name' => $current_username
                                            ), site_url('/api/v1/user/update_role'));

                                            $response = wp_remote_post($api_url, array(
                                                'timeout' => 15
                                            ));



                                            
                                            
                                          
                                            // 3. Lấy và CẬP NHẬT (không ghi đè) token_use_history
                                            $current_history = $wpdb->get_var($wpdb->prepare(
                                                "SELECT token_use_history FROM user_token WHERE username = %s",
                                                $current_username
                                            ));
                                            
                                            $history_array = $current_history ? json_decode($current_history, true) : [];
                                            
                                            $history_array[] = [
                                                "change_token" => 0,
                                                "payment_gate" => "MOMO",
                                                "title" => sanitize_text_field($order_item['title']),
                                                "update_time" => date('Y-m-d H:i:s'),
                                                "amount" => $amount,
                                                "type" => $type_item,
                                                "account_code" => $accountCode // Thêm cả accountCode vào history
                                            ];
                                            
                                            // 4. Update history
                                            $wpdb->update(
                                                'user_token',
                                                ['token_use_history' => json_encode($history_array)],
                                                ['username' => $current_username]
                                            );
                                            
                                            // Thông báo thành công
                                            echo "
                                                <span style='color:blue;font-size:30px;'>GIAO DỊCH THÀNH CÔNG</span>
                                                <svg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 24 24' fill='none' stroke='#7ed321' stroke-width='2' stroke-linecap='round' stroke-linejoin='arcs'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'></path><polyline points='22 4 12 14.01 9 11.01'></polyline></svg>
                                                <span style='color:green'>Gói {$order_item['title']} đã được kích hoạt!</span>
                                            ";
                                        }
                                    }
                                } else {
                                    echo" <span style='color:blue,font-size:30px,'>GIAO DỊCH THẤT BẠI</span>
                                            <svg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 24 24' fill='none' stroke='#d0021b' stroke-width='2' stroke-linecap='round' stroke-linejoin='arcs'><circle cx='12' cy='12' r='10'></circle><line x1='15' y1='9' x2='9' y2='15'></line><line x1='9' y1='9' x2='15' y2='15'></line></svg>
                                            <span style='color:red'>Không tìm thấy giao dịch với mã đơn hàng: $order_code.</span>
                                            Hãy liên hệ admin qua SĐT: 0867052811 để được trợ giúp";
                                }
                    }
                    
                    
                    
                        ?>

                    </label>

                    <div class="button-group">
                        <a class="redirect-btn btn-white" href="<?php echo $site_url; ?>/dashboard/">Về trang cá nhân</a>
                        <a class="redirect-btn btn-blue" href="<?php echo $site_url; ?>/dashboard/user_token">Xem lịch sử giao dịch</a>
                    </div>
                    
                    <p id="countdown"></p>
                </div> 
            </div>
            <p>
                &nbsp;
            </p>
            
        </div>
    </div>  
    </body>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
      let countdown = 10; // Set the countdown duration
      const countdownElement = document.getElementById("countdown");
      const redirectUrl = "<?php echo $site_url; ?>/dashboard/";

      function updateCountdown() {
        countdownElement.textContent = `Redirecting in ${countdown} seconds...`;
        if (countdown === 0) {
          window.location.href = redirectUrl;
        } else {
          countdown--;
          setTimeout(updateCountdown, 1000);
        }
      }

      //updateCountdown();
    });
  </script>
</html>
<?php
get_footer(); // Gọi phần đầu trang (header.php)

?>
