<?php
/*
 * Template Name: Gift Receive Template
 * Template Post Type: gift receive page
 */

get_header(); // Gọi phần đầu trang (header.php)
$user_id = get_current_user_id();
$current_username = wp_get_current_user()->user_login;

global $wpdb;

// Fetch user token from the user_token table
$sql = "SELECT token, updated_time, token_use_history FROM user_token WHERE username = %s";
$user_token_result = $wpdb->get_row($wpdb->prepare($sql, $current_username));

if ($user_token_result) {
    $user_token = $user_token_result->token;
    $updated_time = $user_token_result->updated_time;

} else {
    $user_token = '0';
    $updated_time = 'Đang cập nhật';

}

// Display user token information


global $wp_query;
$custom_gift_id = $wp_query->get('custom_gift_id');

// Check if custom_gift_id exists
if ($custom_gift_id) {
    // Query to fetch gift details
    $sql = "SELECT id_gift, gift_send, date_created, date_expired, user_receive,  title_gift, content_gift FROM gift_from_admin WHERE id_gift = %s";
    $result = $wpdb->get_row($wpdb->prepare($sql, $custom_gift_id));

   
    echo "<script> 
    var gift_send = '" . $result->gift_send . "';
  
    console.log('Result ID: ' + gift_send);
</script>";


    if ($result) {

      /*  echo "<div>ID: " . esc_html($result->id_gift ?? 'Không có dữ liệu') . "</div>";
        echo "<div>Gift Send: " . esc_html($result->gift_send ?? 'Không có dữ liệu') . "</div>";
        echo "<div>Date Created: " . esc_html($result->date_created ?? 'Không có dữ liệu') . "</div>";
        echo "<div>Date Expired: " . esc_html($result->date_expired ?? 'Không có dữ liệu') . "</div>";
        echo "<div>User Receive: " . esc_html($result->user_receive ?? 'Không có dữ liệu') . "</div>";*/
    
?>
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<style>
.gift-receive-time{
    font-style: italic;
    font-style: 22px
}
.gift-title{
    font-weight: bold;
    font-size: 28px;
}
.gift-content{
    font-style: 22px
}

.box {
    display: block;
  margin-left: auto;
  margin-right: auto;
  width: 40%;
  position: relative;
}


.box-body {

  position: relative;
  height: 200px;
  width: 200px;
  margin-top: 300.3333333333px;
  background-color: #cc231e;
  border-bottom-left-radius: 5%;
  border-bottom-right-radius: 5%;
  box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0.3);
  background: linear-gradient(#762c2c,#ff0303);
}



.box-body.open .img {
  opacity: 1;
  z-index: 0;
  transform: translateY(-157px);
}

.box-body.open .box-lid {
  animation: box-lid 1s forwards ease-in-out;
}

.box-body.open .box-bowtie::before {
  animation: box-bowtie-left 1.1s forwards ease-in-out;
}

.box-body.open .box-bowtie::after {
  animation: box-bowtie-right 1.1s forwards ease-in-out;
}
.box-body::after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  -webkit-transform: translateX(-50%);
          transform: translateX(-50%);
  width: 50px;
  background: linear-gradient(#ffffff,#ffefa0)
}
.box-lid {
  position: absolute;
  z-index: 1;
  left: 50%;
  -webkit-transform: translateX(-50%);
          transform: translateX(-50%);
  bottom: 90%;
  height: 40px;
  background-color: #cc231e;
  height: 40px;
  width: 220px;
  border-radius: 5%;
  box-shadow: 0 8px 4px -4px rgba(0, 0, 0, 0.3);
}
.box-lid::after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  left: 50%;
  -webkit-transform: translateX(-50%);
          transform: translateX(-50%);
  width: 50px;
  background: linear-gradient(#ffefa0,#fff)
}
.box-bowtie {
  z-index: 1;
  height: 100%;
}
.box-bowtie::before, .box-bowtie::after {
  content: "";
  width: 83.3333333333px;
  height: 83.3333333333px;
  border: 16.6666666667px solid red;
  border-radius: 50% 50% 0 50%;
  position: absolute;
  bottom: 99%;
  z-index: -1;
}
.box-bowtie::before {
  left: 50%;
  -webkit-transform: translateX(-100%) skew(10deg, 10deg);
          transform: translateX(-100%) skew(10deg, 10deg);
}
.box-bowtie::after {
  left: 50%;
  -webkit-transform: translateX(0%) rotate(90deg) skew(10deg, 10deg);
          transform: translateX(0%) rotate(90deg) skew(10deg, 10deg);
}

@-webkit-keyframes box-lid {
  0%,
  42% {
    -webkit-transform: translate3d(-50%, 0%, 0) rotate(0deg);
            transform: translate3d(-50%, 0%, 0) rotate(0deg);
  }
  60% {
    -webkit-transform: translate3d(-85%, -230%, 0) rotate(-25deg);
            transform: translate3d(-85%, -230%, 0) rotate(-25deg);
  }
  90%, 100% {
    -webkit-transform: translate3d(-119%, 225%, 0) rotate(-70deg);
            transform: translate3d(-119%, 225%, 0) rotate(-70deg);
  }
}

@keyframes box-lid {
  0%,
  42% {
    -webkit-transform: translate3d(-50%, 0%, 0) rotate(0deg);
            transform: translate3d(-50%, 0%, 0) rotate(0deg);
  }
  60% {
    -webkit-transform: translate3d(-85%, -230%, 0) rotate(-25deg);
            transform: translate3d(-85%, -230%, 0) rotate(-25deg);
  }
  90%, 100% {
    -webkit-transform: translate3d(-119%, 225%, 0) rotate(-70deg);
            transform: translate3d(-119%, 225%, 0) rotate(-70deg);
  }
}
@-webkit-keyframes box-body {
  0% {
    -webkit-transform: translate3d(0%, 0%, 0) rotate(0deg);
            transform: translate3d(0%, 0%, 0) rotate(0deg);
  }
  25% {
    -webkit-transform: translate3d(0%, 25%, 0) rotate(20deg);
            transform: translate3d(0%, 25%, 0) rotate(20deg);
  }
  50% {
    -webkit-transform: translate3d(0%, -15%, 0) rotate(0deg);
            transform: translate3d(0%, -15%, 0) rotate(0deg);
  }
  70% {
    -webkit-transform: translate3d(0%, 0%, 0) rotate(0deg);
            transform: translate3d(0%, 0%, 0) rotate(0deg);
  }
}
@keyframes box-body {
  0% {
    -webkit-transform: translate3d(0%, 0%, 0) rotate(0deg);
            transform: translate3d(0%, 0%, 0) rotate(0deg);
  }
  25% {
    -webkit-transform: translate3d(0%, 25%, 0) rotate(20deg);
            transform: translate3d(0%, 25%, 0) rotate(20deg);
  }
  50% {
    -webkit-transform: translate3d(0%, -15%, 0) rotate(0deg);
            transform: translate3d(0%, -15%, 0) rotate(0deg);
  }
  70% {
    -webkit-transform: translate3d(0%, 0%, 0) rotate(0deg);
            transform: translate3d(0%, 0%, 0) rotate(0deg);
  }
}
@-webkit-keyframes box-bowtie-right {
  0%,
  50%,
  75% {
    -webkit-transform: translateX(0%) rotate(90deg) skew(10deg, 10deg);
            transform: translateX(0%) rotate(90deg) skew(10deg, 10deg);
  }
  90%,
  100% {
    -webkit-transform: translate(-50%, -15%) rotate(45deg) skew(10deg, 10deg);
            transform: translate(-50%, -15%) rotate(45deg) skew(10deg, 10deg);
    box-shadow: 0px 4px 8px -4px rgba(0, 0, 0, 0.3);
  }
}
@keyframes box-bowtie-right {
  0%,
  50%,
  75% {
    -webkit-transform: translateX(0%) rotate(90deg) skew(10deg, 10deg);
            transform: translateX(0%) rotate(90deg) skew(10deg, 10deg);
  }
  90%,
  100% {
    -webkit-transform: translate(-50%, -15%) rotate(45deg) skew(10deg, 10deg);
            transform: translate(-50%, -15%) rotate(45deg) skew(10deg, 10deg);
    box-shadow: 0px 4px 8px -4px rgba(0, 0, 0, 0.3);
  }
}
@-webkit-keyframes box-bowtie-left {
  0% {
    -webkit-transform: translateX(-100%) rotate(0deg) skew(10deg, 10deg);
            transform: translateX(-100%) rotate(0deg) skew(10deg, 10deg);
  }
  50%,
  75% {
    -webkit-transform: translate(-50%, -15%) rotate(45deg) skew(10deg, 10deg);
            transform: translate(-50%, -15%) rotate(45deg) skew(10deg, 10deg);
  }
  90%,
  100% {
    -webkit-transform: translateX(-100%) rotate(0deg) skew(10deg, 10deg);
            transform: translateX(-100%) rotate(0deg) skew(10deg, 10deg);
  }
}
@keyframes box-bowtie-left {
  0% {
    -webkit-transform: translateX(-100%) rotate(0deg) skew(10deg, 10deg);
            transform: translateX(-100%) rotate(0deg) skew(10deg, 10deg);
  }
  50%,
  75% {
    -webkit-transform: translate(-50%, -15%) rotate(45deg) skew(10deg, 10deg);
            transform: translate(-50%, -15%) rotate(45deg) skew(10deg, 10deg);
  }
  90%,
  100% {
    -webkit-transform: translateX(-100%) rotate(0deg) skew(10deg, 10deg);
            transform: translateX(-100%) rotate(0deg) skew(10deg, 10deg);
  }
}


</style>
<body>
    <div class = "container">
        <div class = "get-user-token-info" >
            <?php echo " <p>Số token hiện tại của bạn: $user_token </p>
            <p>Cập nhật: $updated_time </p>" ?>

        </div>
        <div class = "gift-info">
            <div class = "gift-title" id = "gift-title"> <?php  echo "<div>" . esc_html($result->title_gift ?? 'Không có dữ liệu') . "</div>"; ?></div>
            <div class = "gift-receive-time" id ="gift-eceive-time"><?php  echo "<div> Thời gian nhận quà: Từ:" . esc_html($result->date_created ?? '') . " đến: " . esc_html($result->date_expired ?? '') ."</div>"; ?></div>
            <div class = "gift-content" id ="gift-content"><?php  echo "<div>" . esc_html($result->content_gift ?? 'Không có dữ liệu') . "</div>"; ?></div>
        </div>
        <div class="box">
            <div class="box-body" id = "box-body-btn" onclick ="receiveGift()">
                <div class="box-lid">
                    <div class="box-bowtie"></div>
                </div>
            </div>
        </div>


    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  function receiveGift() {
    const boxBody = document.getElementById("box-body-btn");

    if (!boxBody.classList.contains("open")) {
        boxBody.classList.add("open"); // Kích hoạt class 'open' khi click
        // Gửi yêu cầu AJAX tới server
        setTimeout(function () {
            jQuery.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: 'POST',
                data: {
                    action: 'receive_gift',  // Tên action trong AJAX
                    custom_gift_id: '<?php echo $custom_gift_id; ?>',
                    current_username: '<?php echo $current_username; ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Nếu nhận quà thành công
                        Swal.fire({
                          title: "Nhận phần quà thành công!",
                          text: `Chúc mừng bạn đã nhận thành công ${gift_send} tokens`,
                          icon: "success"

                           
                        });
                    } else {
                        // Nếu đã nhận quà hoặc có lỗi
                        Swal.fire({
                          title: "Thất bại",
                          text: "Bạn đã nhận phần quà này rồi ",
                          icon: "error"
                           
                        });
                    }
                }
            });
        }, 2000);
    }
}

</script>

<?php
} else {
    echo 'Không tồn tại phần quà này. Có thể thời gian nhận phần quà đã kết thúc.';
}
       
} else {
    echo 'Không tìm thấy phần quà nào.';
}


get_footer();
?>
