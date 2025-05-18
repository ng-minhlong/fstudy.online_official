<?php
/*
 * Template Name: Notification Page Template
 * Template Post Type: notification
 
 */

get_header(); // Gọi phần đầu trang (header.php)
$user_id = get_current_user_id();




// Database credentials (update with your own database details)
$servername = "localhost";
$username = "root";
$password = ""; // No password by default
$dbname = "wordpress";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy giá trị custom_notification_id từ URL
global $wp_query;
$custom_notification_id = $wp_query->get('custom_notification_id');

// Kiểm tra nếu custom_notification_id tồn tại
if ($custom_notification_id) {
  global $wpdb;

  // Thực hiện truy vấn để lấy id_video_orginal 
  $sql = "SELECT id_notification, timestamp, title, content, level_notification, role_receive, user_receive FROM notification_admin WHERE id_notification = %s";
  $result = $wpdb->get_row($wpdb->prepare($sql, $custom_notification_id));

  if ($result) {
    echo "Level Notification: " . ($result->level_notification ?? 'Không có dữ liệu');
    echo "Role Receive: " . ($result->role_receive ?? 'Không có dữ liệu');
    echo "User Receive: " . ($result->user_receive ?? 'Không có dữ liệu');


  // Đóng kết nối
  $conn->close();

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notification</title>
  <style>
   

  </style>
</head>
<body>
  <div id="container">
      <div id ="id_notification"></div>
      <div id ="timestamp"></div>
      <div class ="content">
          <h3 id ="title"></h3>
          <p id ="content"></p>
      </div>
      <div id ="content"></div>


  </div>

  <script>
    let id_notificationDiv = document.getElementById("id_notification");
    let timestampDiv = document.getElementById("timestamp");
    let contentDiv = document.getElementById("content");
    let titleDiv = document.getElementById("title");
    

    const id_notification = "<?php echo $result->id_notification; ?>"; 
    const timestamp = "<?php echo addslashes($result->timestamp); ?>"; 
    const content = "<?php echo addslashes($result->content); ?>"; 
    const title = "<?php echo addslashes($result->title); ?>"; 
   

    id_notificationDiv.innerHTML += `ID Notification: ${id_notification}`;
    timestampDiv.innerHTML += `Hello Ngày cập nhập: ${timestamp}`;
    titleDiv.innerHTML += `Tiêu đề: ${title}`;
    contentDiv.innerHTML += `Nội dung: ${content}`;
  
  </script>
</body>
</html>


<?php
}
else {
  echo 'Lỗi: Không tìm thấy thông báo nào';
}
get_footer();

} else {
    // Xử lý khi custom_notification_id không có trong URL
    echo 'Không tìm thấy thông báo nào.';
}