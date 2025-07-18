<?php
/*
 * Template Name: Doing Template Reading Test
 * Template Post Type: ieltsreadingtest
 
 */


if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;
    // Lấy giá trị custom number từ custom field
    $custom_number = get_query_var('id_test');
    $current_user_id = $current_user->ID;
    echo '
    <script>
           
        var CurrentuserID = "' . $user_id . '";
        var Currentusername = "' . $username . '";
    
    </script>
    ';

    //$custom_number = get_post_meta($post_id, '_ieltsreadingtest_custom_number', true);
      // Database credentials
      $servername = DB_HOST;
      $username = DB_USER;
      $password = DB_PASSWORD;
      $dbname = DB_NAME;
    
    // Tạo kết nối
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");
    
    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
$sql_test = "SELECT * FROM ielts_reading_test_list WHERE id_test = ?";
$stmt_test = $conn->prepare($sql_test);

if ($stmt_test === false) {
    die('Lỗi MySQL prepare: ' . $conn->error);
}


 // Get current time (hour, minute, second)
 $hour = date('H'); // Giờ
 $minute = date('i'); // Phút
 $second = date('s'); // Giây

 // Generate random two-digit number
 $random_number = rand(10, 99);
 // Handle user_id and id_test error, set to "00" if invalid
 if (!$user_id) {
    $user_id = '00'; // Set user_id to "00" if invalid
}

if (!$custom_number) {
    $custom_number = '00'; // Set id_test to "00" if invalid
}


 

function generate_uuid_v4() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),         // 32 bits
        mt_rand(0, 0xffff),                             // 16 bits
        mt_rand(0, 0x0fff) | 0x4000,                    // 16 bits, version 4
        mt_rand(0, 0x3fff) | 0x8000,                    // 16 bits, variant
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)  // 48 bits
    );
}

$result_id = generate_uuid_v4();
$site_url = get_site_url();



echo "<script> 
var resultId = '" . $result_id ."';
var siteUrl = '" .$site_url . "';
var id_test = '" . $id_test . "';
console.log('Result ID: ' + resultId);
</script>";



$stmt_test->bind_param("s", $custom_number);
$stmt_test->execute();
$result_test = $stmt_test->get_result();



$current_url = $_SERVER['REQUEST_URI'];


// Query to fetch token details for the current username
$sql2 = "SELECT token, token_use_history 
         FROM user_token 
         WHERE username = ?";


// Prepare and execute the first query


if ($result_test->num_rows > 0) {
    // Lấy các ID từ question_choose (ví dụ: "1001,2001,3001")
    $data = $result_test->fetch_assoc();
    $question_choose = $data['question_choose'];
    $testname = $data['testname'];
    $token_need = $data['token_need'];
    $time_allow = $data['time_allow'];
    $permissive_management = $data['permissive_management'];



    add_filter('document_title_parts', function ($title) use ($testname) {
        $title['title'] = $testname; // Use the $testname variable from the outer scope
        return $title;
    });
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        die("Error preparing statement 2: " . $conn->error);
    }

    $stmt2->bind_param("s", $current_username);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows > 0) {
        $token_data = $result2->fetch_assoc();
        $token = $token_data['token'];
        $token_use_history = $token_data['token_use_history'];

        echo "<script>//console.log('Token: $token, Token Use History: $token_use_history, Mày tên: $current_username');</script>";
        echo '<script>
            var currentUsername = "' . $current_username . '";
            var currentUserid = "' . $current_user_id . '";
            console.log("Current Username: " + currentUsername);
            console.log("Current User ID: " + currentUserid);
        </script>';

    } else {
        echo "Lỗi đề thi";
        
    }


      
            $permissiveManagement = json_decode($permissive_management, true);
            
            // Chuyển mảng PHP thành JSON string để có thể in trong console.log
            echo "<script> 
                    console.log('$permissive_management');
                </script>";
            
            
            $foundUser = null;
            if (!empty($permissiveManagement)) {
                foreach ($permissiveManagement as $entry) {
                    if ($entry['username'] === $current_username) {
                        $foundUser = $entry;
                        break;
                    }
                }
            }
        
            $premium_test = "False"; // Default value
            if ($foundUser != null && $foundUser['time_left'] > 0 || $token_need == 0) {
                if ($token_need > 0) {
                    $premium_test = "True";
                }
            
            
                echo '<script>
                let premium_test = "' . $premium_test . '";
                let token_need = "' . $token_need . '";
                let change_content = "' . $testname . '";
                let testname = "' . $testname . '";
                let time_left = "' . (isset($foundUser['time_left']) ? $foundUser['time_left'] : 10) . '";
            </script>';
            









    /* THÊM MỚI PHẦN CHECK ĐÃ MUA TEST CHƯA TẠI ĐÂY */
    

get_header(); // Gọi phần đầu trang (header.php)

// Kiểm tra nếu URL chứa tham số part


if (strpos($current_url, '?part=') !== false) {
    $query_string = $_SERVER['QUERY_STRING']; // Lấy chuỗi truy vấn từ URL
    parse_str($query_string, $query_params); // Chuyển thành mảng

    if (isset($query_params['part'])) {
        // Tách các giá trị từ 'part' (chuỗi, không phải mảng)
        $id_parts_raw = explode(',', $query_params['part']);
        
        // Tạo mảng mặc định với 3 phần tử
        $id_parts = ["0", "0", "0"];

        // Lặp qua các giá trị và đặt vào mảng kết quả
        foreach ($id_parts_raw as $part) {
            $position = intval(substr($part, 0, 1)) - 1; // Xác định vị trí (1003 -> vị trí 1)
            if ($position >= 0 && $position < 3) {
                $id_parts[$position] = $part; // Đặt giá trị vào đúng vị trí
            }
        }

        // Xuất ra JavaScript để kiểm tra
        echo '<script type="text/javascript">
            const idParts = ' . json_encode($id_parts, JSON_UNESCAPED_SLASHES) . ';
            console.log("idParts:", idParts);
        </script>';
    } else {
        $id_parts = [];
    }
}
 else {
    // Nếu không có tham số ?part, dùng giá trị mặc định từ cơ sở dữ liệu
    $id_parts = explode(",", $data['question_choose']);

    echo '<script type="text/javascript">
    const idParts = ' . json_encode($id_parts, JSON_UNESCAPED_SLASHES) . ';
    console.log("idParts cho full test:", idParts);
</script>';

}


    $part = []; // Mảng chứa dữ liệu của các phần
    $previous_part_questions = 0; // Biến lưu trữ số câu hỏi của phần trước
    $filterTypeQuestion = [];

    // Lặp qua từng id_part và truy vấn bảng tương ứng
    foreach ($id_parts as $index => $id_part) {
        // Xác định bảng và câu lệnh SQL tương ứng dựa trên index của part
        switch ($index) {
            case 0:
                $sql_part = "SELECT part, duration, number_question_of_this_part, paragraph, group_question, category 
                             FROM ielts_reading_part_1_question WHERE id_part = ?";
                break;
            case 1:
                $sql_part = "SELECT part, duration, number_question_of_this_part, paragraph, group_question, category 
                             FROM ielts_reading_part_2_question WHERE id_part = ?";
                break;
            case 2:
                $sql_part = "SELECT part, duration, number_question_of_this_part, paragraph, group_question, category 
                             FROM ielts_reading_part_3_question WHERE id_part = ?";
                break;
            default:
                continue 2; // Nếu có nhiều hơn 3 phần, bỏ qua
        }

        // Chuẩn bị và thực thi câu lệnh SQL cho từng phần
        $stmt_part = $conn->prepare($sql_part);
        if ($stmt_part === false) {
            die('Lỗi MySQL prepare: ' . $conn->error);
        }

        $stmt_part->bind_param("i", $id_part);
        $stmt_part->execute();
        $result_part = $stmt_part->get_result();

        // Nếu có kết quả, thêm vào mảng part
        while ($row = $result_part->fetch_assoc()) {
            $entry = [
                'part_number' => $row['part'],
                'paragraph' => $row['paragraph'],
                'number_question_of_this_part' => $row['number_question_of_this_part'],
                'duration' => $row['duration'],
                'category' => $row['category'],
                'group_question' => $row['group_question']
            ];

            if (!empty($row['group_question'])) {
                $decoded = json_decode($row['group_question'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $entry['group_question'] = $decoded;
                } else {
                    // Log the error and set group_question to null
                    error_log('JSON decode error: ' . json_last_error_msg());
                    $entry['group_question'] = null;
                }
            } else {
                $entry['group_question'] = null;
            }
            


            if (!empty($row['category'])) {
                $categoryData = json_decode($row['category'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $entry['question_types'] = [];
                    foreach ($categoryData as $category) {
                        // Cộng thêm số câu hỏi của phần trước đó vào start và end
                        $start = $category['start'] + $previous_part_questions;
                        $end = $category['end'] + $previous_part_questions;

                        for ($i = $start; $i <= $end; $i++) {
                            $entry['question_types'][$i] = $category['type'];
                            $filterTypeQuestion[] = [$i => $category['type']]; // Sử dụng dạng key-value

                        }
                    }
                } else {
                    error_log('JSON decode error in category: ' . json_last_error_msg());
                }
            }

            // Cập nhật số câu hỏi của phần hiện tại để cộng vào phần tiếp theo
            $previous_part_questions += $row['number_question_of_this_part'];


            // Thêm phần vào mảng part
            $part[] = $entry;
        }
    }

    // Xuất mảng quizData dưới dạng JavaScript
    echo '<script type="text/javascript">
    const quizData = {
        part: ' . json_encode($part, JSON_UNESCAPED_SLASHES) . ',
        filterTypeQuestion: ' . json_encode($filterTypeQuestion, JSON_UNESCAPED_SLASHES) . '

    };

    console.log(quizData);
    </script>';


// Đóng kết nối
$conn->close();

?>
    



<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ielts Reading Computer </title>
    <style>
      

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    font-size: 18px;

}

.form-group{
 
 position: relative;
 font-size: 15px;
 color: #666;
 &+&{
   margin-top: 30px;
 }
 
 
}


.form-label{
   position: absolute;
   z-index: 1;
   left: 0;
   top: 5px;
   @include transition(.3s);
   
 }
 
 .form-control{
    width: 150px;

   position: relative;
   z-index: 3;
   height: 35px;
   background: none;
   border:none;
   padding: 5px 0;
   @include transition(.3s);
   border-bottom: 1px solid #777;
   color: #555;
   &:invalid{outline: none;}
   
   &:focus , &:valid{
     outline: none;

     @include box-shadow(0 1px $primary);
     border-color:$primary;
     + .form-label{
       font-size: 12px;
       color: $primary;
       @include translateY(-15px);
     }
   }
 }
.content-left {
    width: 50%;
    padding: 20px;
    border-right: 1px solid #ccc;
}

.content-right {
    width: 50%;
    padding: 20px;
}

.question {
    margin-bottom: 20px;
}

.pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
}

.pagination-container button {
    margin: 0 10px;
    padding: 10px 20px;
    cursor: pointer;
}

#questions-container {
    padding: 20px;
}

#questions-container label {
    display: block;
    margin: 20px 0;
}

.answer-input {
    width: 250px;
    padding: 8px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

/* Fixed header for question range */
.fixed-above {
    background-color: #f8f9fa; /* Màu nền cho header */
    padding: 10px 20px; /* Khoảng cách bên trong */
    display: flex;
    justify-content: space-between; /* Căn giữa cho các phần tử */
    align-items: center; /* Căn giữa theo chiều dọc */
    position: fixed; /* Để header luôn ở phía trên */
    width: 100%; /* Chiều rộng đầy đủ */
    top: 0; /* Đặt vị trí ở trên cùng */
    z-index: 1000; /* Đảm bảo header nằm trên các phần tử khác */
}
.header-content {
    display: flex;
    flex-direction: column; /* Đặt hướng dọc để timer nằm bên dưới ID */
    align-items: flex-start; /* Căn trái cho cả hai phần tử */
}

.test-taker-id {
    font-weight: bold;
}



.above-test {
    background-color: #e9ecef; /* Màu xám cho phần câu hỏi */
    padding: 14px; /* Khoảng cách bên trong */
    margin-top: 30px; /* Tăng giá trị để tránh che khuất bởi header */
    margin: 0 auto; /* Căn giữa */
    display: flex; /* Sử dụng flexbox để căn chỉnh các phần tử con */
    justify-content: space-between; /* Đưa icon và thời gian ra hai bên */
    align-items: center; /* Căn chỉnh theo chiều dọc */
}

.above-test i {
    margin: 0 10px; /* Tạo khoảng cách giữa các icon */
    cursor: pointer; /* Cho hiệu ứng con trỏ khi hover */

}


.above-test .fa-regular.fa-clock {
    margin-right: 10px; /* Tạo khoảng cách giữa fa-clock và timer */
}

.above-test .timer {
    margin-right: 20px; /* Tạo khoảng cách giữa timer và fa-bug */
}

.above-test .fa-solid.fa-bug.fa-expand {
    margin-right: 20px; /* Tạo khoảng cách giữa fa-bug và fa-circle-info */
}

#question-range-of-part {
    flex-grow: 1; /* Cho phép phần tử này chiếm không gian còn lại */
}
html {
  scroll-behavior: smooth;
}

.question-checkbox {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    width: 32px;
    height: 32px;
    font-size: 16px;
    font-weight: 500;
    color: #282828;
    flex-shrink: 0;
    background-color: #fff;
    cursor: pointer;
    opacity: 1;
    font-family: "Montserrat", Helvetica, Arial, sans-serif;
    -moz-transition: all ease 0.2s;
    -o-transition: all ease 0.2s;
    -webkit-transition: all ease 0.2s;
    transition: all ease 0.2s;
    font-size: 12px;
    border: 1px solid #EAECEF;
}

.number-question {
    border-radius: 50%; 
    background-color: #e8f2ff;
    color: #35509a;
    width: 35px;
    height: 35px;
    line-height: 35px;
    font-size: 15px;
    text-align: center;
    display: inline-block;
    cursor: pointer;
}
.highlight-marked {
    background-color: yellow !important;
}

.checkbox-marked {
    background-color: yellow !important;
}



.highlight-current-question {
    font-weight: bold; /* In đậm */
    border: 2px solid #ffcc00; /* Viền ngoài sáng màu vàng */
    border-radius: 5px; /* Bo tròn góc */
    padding: 5px; /* Tạo khoảng cách giữa chữ và viền */
    background-color: #f9f6e8; /* Thêm nền nhạt */
    transition: all 0.3s ease; /* Hiệu ứng mượt */
}
.checkbox-answered {
    background-color: #e6f7ff; /* Màu nền xanh nhạt */
    border-color: #1890ff;    /* Viền xanh đậm */
    color: #1890ff;           /* Chữ màu xanh */
}


#content1 {
    display: none;
    width: 100%;
}
#header{
    height:40px
}



/* Container that includes the quiz content */
.quiz-container {
   /* margin-top: 60px; /* Adjust to fit below the fixed header */
    margin-bottom: 30px; /* Adjust to fit above the fixed footer */
    margin-top: 10px; /* Adjust to fit above the fixed footer */

    display: flex;
    height: calc(100vh - 210px); /* Make sure the container fits within the viewport */
    overflow: hidden; /* Prevent content from overflowing the container */
    width: 100%;
}

/* Left side scrollable content (paragraphs) */
.content-left {
    width: 50%;
    padding: 20px;
    overflow-y: auto; /* Make it scrollable */
    border-right: 1px solid #ccc;
}

/* Right side scrollable content (questions) */
.content-right {
    width: 50%;
    padding: 20px;
    overflow-y: auto; /* Make it scrollable */
}

/* Paragraph container styling (optional, adjust based on content) */
#paragraph-container p {
    margin-bottom: 20px;
}

/* Questions container styling (optional, adjust based on content) */
#questions-container .question {
    margin-bottom: 25px;
}

#question-nav {
  display: inline-block;
  margin: 0 auto;
}

#question-nav span {
  cursor: pointer;
  padding: 10px;
  margin-right: 5px;
  background-color: #fff;
  border: 1px solid #ccc;
  display: inline-block;
  text-align: center;
}

#question-nav span:hover {
  background-color: #ddd;
}
/* HTML: <div class="loader"></div> */
.loader {
  width: 70px;
  aspect-ratio: 1;
  border-radius: 50%;
  border: 8px solid #514b82;
  animation: l20-1 0.8s infinite linear alternate, l20-2 1.6s infinite linear;
}

#test-prepare {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: fixed; /* Giữ loader cố định giữa màn hình */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); /* Căn giữa theo cả chiều ngang và dọc */
    height: 200px;
    z-index: 1001; /* Đảm bảo loader ở trên các phần tử khác */
}


@keyframes l20-1{
   0%    {clip-path: polygon(50% 50%,0       0,  50%   0%,  50%    0%, 50%    0%, 50%    0%, 50%    0% )}
   12.5% {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100%   0%, 100%   0%, 100%   0% )}
   25%   {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100% 100%, 100% 100%, 100% 100% )}
   50%   {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100% 100%, 50%  100%, 0%   100% )}
   62.5% {clip-path: polygon(50% 50%,100%    0, 100%   0%,  100%   0%, 100% 100%, 50%  100%, 0%   100% )}
   75%   {clip-path: polygon(50% 50%,100% 100%, 100% 100%,  100% 100%, 100% 100%, 50%  100%, 0%   100% )}
   100%  {clip-path: polygon(50% 50%,50%  100%,  50% 100%,   50% 100%,  50% 100%, 50%  100%, 0%   100% )}
}
@keyframes l20-2{ 
  0%    {transform:scaleY(1)  rotate(0deg)}
  49.99%{transform:scaleY(1)  rotate(135deg)}
  50%   {transform:scaleY(-1) rotate(0deg)}
  100%  {transform:scaleY(-1) rotate(-135deg)}
}
/* Fixed bottom navigation for questions */
.fixed-bottom {
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #f1f1f1;
    border-top: 1px solid #ccc;
    text-align: center;
    z-index: 1000;
}

#part-navigation {
    overflow: auto;
    display: flex;
    justify-content: space-between; /* Ensure even spacing */
    width: 100%;
    align-items: center; /* Align buttons vertically */
}

#part-navigation-button, #submit-btn {
    flex-grow: 1; /* Share remaining width equally for parts */
    padding: 10px;
    margin: 5px;
    background-color: #f1f1f1;
    border: 1px solid #ccc;
    cursor: pointer;
    border-radius: 5px;
    text-align: center;
    height: 40px; /* Fixed height for consistency */
    display: flex;
    justify-content: center;
    align-items: center;
}

#submit-btn {
    width: 150px; /* Fixed width for Submit button */
    flex-grow: 0; /* Prevent it from expanding */
}


#part-navigation button.active {
    background-color: #0073e6;
    color: white;
    border: 1px solid #0073e6;
}
.wp-dark-mode-active .tooltip {
    background-color: #343b47 !important;
}


.tooltip {
    position: relative;
    cursor: pointer;
    background-color: yellow;

  }
  
  .tooltip .tooltiptext {
    visibility: hidden;
    width: 180px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 10px;
    position: absolute;
    z-index: 1;
    bottom: 125%; /* Position above the span */
    left: 50%;
    margin-left: -75px; /* Center the tooltip */
    opacity: 0;
    transition: opacity 0.3s;
  }
  
  .tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%; /* Arrow at the bottom */
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
  }
  
  .tooltip.active .tooltiptext {
    visibility: visible;
    opacity: 1;
  }
  
  .tooltiptext button {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 5px 10px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    margin: 2px 0;
    font-size: 12px;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .tooltiptext button:hover {
    background-color: #3e8e41;
  }

#highlight-icon-modify{
    width : 20px;
    height: 20px;
}
.tooltiptext img {
    display: inline-block;
    width: 20px;  /* Set the desired width */
    height: 20px; /* Set the desired height */
    margin-right: 5px; /* Add some space between images */
    cursor: pointer; /* Change cursor to pointer on hover */
    vertical-align: middle; /* Align images vertically in the middle */
}
.group-control-part-btn{
    position: fixed;
    bottom: 70px;
    right: 10px;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
}
.control-part-btn{
    background-color:black;
    color: #ffffff;
    height:60px;
    width: 60px;
}

.bookmark-btn{
    height: 30px;
}



.start_test {
  appearance: none;
  background-color: #2ea44f;
  border: 1px solid rgba(27, 31, 35, .15);
  border-radius: 6px;
  box-shadow: rgba(27, 31, 35, .1) 0 1px 0;
  box-sizing: border-box;
  color: #fff;
  cursor: pointer;
  display: inline-block;
  font-family: -apple-system,system-ui,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
  font-size: 20px;
  font-weight: 600;
  line-height: 20px;
  padding: 6px 16px;
  position: relative;
  text-align: center;
  text-decoration: none;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  vertical-align: middle;
  white-space: nowrap;
}

.start_test:focus:not(:focus-visible):not(.focus-visible) {
  box-shadow: none;
  outline: none;
}

.start_test:hover {
  background-color: #2c974b;
}

.start_test:focus {
  box-shadow: rgba(46, 164, 79, .4) 0 0 0 3px;
  outline: none;
}

.start_test:disabled {
  background-color: #94d3a2;
  border-color: rgba(27, 31, 35, .1);
  color: rgba(255, 255, 255, .8);
  cursor: default;
}

.start_test:active {
  background-color: #298e46;
  box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
}


</style>
<style>
    

/*Modal Popup Save Progress */

.modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        width: 80%;
        max-width: 700px;
        max-height: 80vh;
        overflow-y: auto;
    }
    
.modal-overlay-progress {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-progress {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        width: 80%;
        max-width: 700px;
        max-height: 80vh;
        overflow-y: auto;
    }
    .modal-nav {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    
    .modal-nav button {
        padding: 8px 15px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .modal-nav button:hover {
        background-color: #45a049;
    }
    
    .modal-nav button:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }
    
    .close-modal-2 {
        float: right;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }
    .close-modal-progress {
        float: right;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }

    
   /*Ẩn footer mặc định */
   #colophon.site-footer { display: none !important; }
</style>
</head>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script> let pre_id_test_ = `<?php echo esc_html($custom_number); ?>`;</script>
<body onload="main()">
    <div id = "test-prepare">
        <div class="loader"></div>
        <h3>Your test will begin shortly</h3>
        <div id = "checkpoint" class = "checkpoint">
                <?php
                    if($premium_test == "True"){
                        echo "<script >console.log('Thông báo. Bạn còn {$foundUser['time_left']} lượt làm bài. success ');</script>";
                        echo " <p style = 'color:green'> Bạn còn {$foundUser['time_left']} lượt làm bài này <svg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='#7ed321' stroke-width='2' stroke-linecap='round' stroke-linejoin='arcs'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'></path><polyline points='22 4 12 14.01 9 11.01'></polyline></svg> </p> ";
                        echo "<script>console.log('This is premium test');</script>";
                    }
                    else{
                        echo "<script>console.log('This is free test');</script>"; 
                    }
                        ?>
        </div>    
        <div id = "quick-instruction">
            <i>Quick Instruction:<br>
            - If you find any errors from test (image,display,text,...), please let us know by clicking icon <i class="fa-solid fa-bug"></i><br> 
            - Incon <i class="fa-solid fa-circle-info"></i> will give you a guide tour, in which you can understand the structure of test, include test's type, formation and how to answer questions<br>
            - All these two icons are at the right-above side of test.
        </i>

        </div>
        <div style="display: none;" id="date" style="visibility:hidden;"></div>
        <div style="display: none;" id="title-test"><?php echo esc_html($testname);?></div>
        <div  style="display: none;"  id="id_test"  style="visibility:hidden;"><?php echo esc_html($custom_number);?></div>
        <button  style="display: none;" class ="start_test" id="start_test"  onclick = "prestartTest()">Start test</button>
        <i id = "welcome" style = "display:none">Click Start Test button to start the test now. Good luck</i>


    </div>

 <!--Save Progress Popup-->
 <div class="modal-overlay" id="questionModal">
                    <div class="modal-content">
                        <span class="close-modal-2">&times;</span>
                        <div id="modalQuestionContent"></div>
                        
                    </div>
                </div>
    
    
    
    <div id="content1" style="display: none;">
        <div class="above-test">
            <div id="question-range-of-part" class="question-range"></div>
            <i class="fa-regular fa-clock"></i>
            <span id="timer" class="timer" style="font-weight: bold"></span>
            <i class="fa-solid fa-bug"></i>

            <span class="icon-text-wrapper" onclick = 'saveProgress()'>    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5"/><path d="M13.8 12H3m9 10a10 10 0 1 0 0-20"/></svg></span>


            <i class="fa-solid fa-circle-info"></i>
            <i id="fullscreen-toggle" class="fa-solid fa-expand"></i>
        </div>


        <div class="quiz-container">
            <div class = "group-control-part-btn">
                <button id="prev-btn" class= "control-part-btn" ><i class="fa-solid fa-arrow-left fa-xl"></i></button>
                <button id="next-btn" class= "control-part-btn" ><i class="fa-solid fa-arrow-right  fa-xl"></i></button>
            </div>

            <div class="content-left">
                <div class = "top-left"></div>

                <div id="paragraph-container">
                    <!-- Paragraph will be loaded dynamically -->
                </div>
            </div>
            <div class="content-right">
            <div class = "top-right"></div>

                <div id="questions-container">
                    <!-- Questions will be loaded dynamically -->
                </div>


             <div class="pagination-container">
                   

                    <h5  id="time-result"></h5>

                    <h5 id ="useranswerdiv"></h5>
                     <!-- giấu form send kết quả bài thi -->


    
  
     <span id="message" style="display:none" ></span>
     <form id="saveReadingResult"  style="display:none" >
                <div class="card">
                    <div class="card-header">Form lưu kết quả</div>
                    <div class="card-body" >
        
                <div class = "form-group" >
                    <input   type="text" id="overallband" name="overallband" placeholder="Kết quả"  class="form-control form_data" />
                    <span id="result_error" class="text-danger" ></span>
            
                </div>
            
            
                <div class = "form-group">
                    <input type="text" id="dateform" name="dateform" placeholder="Ngày"  class="form-control form_data"  />
                    <span id="date_error" class="text-danger" ></span>
                </div>
            
                
            
                <div class = "form-group"  >
                    <input type="text" id="timedotest" name="timedotest" placeholder="Thời gian làm bài"  class="form-control form_data" />
                    <span id="time_error" class="text-danger"></span>
                </div>
            
                <div class = "form-group" >
                    <input type="text" id="idtest" name="idtest" placeholder="Id test"  class="form-control form_data" />
                    <span id="idtest_error" class="text-danger" ></span>
                </div>

                <div class = "form-group" >
                    <input type="text" id="test_type" name="test_type" placeholder="Type Test"  class="form-control form_data" />
                    <span id="test_type_error" class="text-danger" ></span>
                </div>
            
                <div class = "form-group" >
                    <input type="text" id="idcategory" name="idcategory" placeholder="Id category"  class="form-control form_data" />
                    <span id="idcategory_error" class="text-danger"></span>
                </div>
            
                <div class = "form-group"   >
                    <input type="text"  id="testname" name="testname" placeholder="Test Name"  class="form-control form_data" />
                    <span id="testname_error" class="text-danger"></span>
                </div>
                <div class = "form-group"   >
                    <input type="text"  id="useranswer" name="useranswer" placeholder="User Answer"  class="form-control form_data" />
                    <span id="useranswer_error" class="text-danger"></span>
            </div>
            
            <div class = "form-group"   >
                    <input type="text"  id="correct_percentage" name="correct_percentage" placeholder="Correct percentage"  class="form-control form_data" />
                    <span id="correctanswer_error" class="text-danger"></span>  
                </div>
          

            <div class = "form-group"   >
                    <input type="text"  id="total_question_number" name="total_question_number" placeholder="Total Number"  class="form-control form_data" />
                    <span id="total_question_number_error" class="text-danger"></span>  
                </div>
           

            <div class = "form-group"   >
                    <input type="text"  id="correct_number" name="correct_number" placeholder="Correct Number"  class="form-control form_data" />
                    <span id="correctanswer_error" class="text-danger"></span>  
                </div>
            
            <div class = "form-group"   >
                    <input type="text"  id="incorrect_number" name="incorrect_number" placeholder="Incorrect Number"  class="form-control form_data" />
                    <span id="incorrect_number_error" class="text-danger"></span>  
                </div>
        

            <div class = "form-group"   >
                    <input type="text"  id="skip_number" name="skip_number" placeholder="Skip Number"  class="form-control form_data" />
                    <span id="skip_number_error" class="text-danger"></span>  
                </div>

                <div class = "form-group"   >
                    <input type="text"  id="testsavenumber" name="testsavenumber" placeholder="Result Number"  class="form-control form_data" />
                    <span id="testsavenumber_error" class="text-danger"></span>  
                </div>
           
           
        
        <div class="card-footer">
            <!--  <button type="button" name="submit" id="submit" class="btn btn-primary" onclick="save_data(); return false;">Save</button>-->
                            <td><input type="submit" id="submit" name="submit"/></td> 
    
            </div>
                
        </div>
        <div id="result_msg" ></div>
    </form>
    <!-- kết thúc send form -->

                </div> 
                <div id="results-container"></div>
            </div>       

        </div>
        


        <div id="question-nav-container" class="fixed-bottom">
            <!-- <span id="part-label"></span>
            <div id="question-nav"></div> -->
            <div id="part-navigation"></div>

        </div>
    </div>

    <script>
        let DoingTest = false;
        let highlights = {}; // Object để lưu trữ các highlight
    </script>

<?php
echo'
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://smtpjs.com/v3/smtp.js"></script>
    <script src="'. $site_url .'/contents/themes/tutorstarter/ielts-reading-tookit/script_reading_11.js"></script>
    <script src="'. $site_url .'/contents/themes/tutorstarter/ielts-reading-tookit/highlight-text-1.js"></script>'
    ?>

</body>
<script>
    document.getElementById("fullscreen-toggle").addEventListener("click", function () {
    if (!document.fullscreenElement) {
        // Kích hoạt fullscreen
        document.documentElement.requestFullscreen();
        this.classList.remove("fa-expand");
        this.classList.add("fa-compress");
    } else {
        // Thoát fullscreen
        document.exitFullscreen();
        this.classList.remove("fa-compress");
        this.classList.add("fa-expand");
    }
});


    // function save data qua ajax
jQuery('#saveReadingResult').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission
    
     var link = "<?php echo admin_url('admin-ajax.php'); ?>";
    
     var form = jQuery('#saveReadingResult').serialize();
     var formData = new FormData();
     formData.append('action', 'save_user_result_ielts_reading');
     formData.append('save_user_result_ielts_reading', form);
    
     jQuery.ajax({
         url: link,
         data: formData,
         processData: false,
         contentType: false,
         type: 'post',
         success: function(result) {
             jQuery('#submit').attr('disabled', false);
             if (result.success == true) {
                 jQuery('#saveReadingResult')[0].reset();
             }
             jQuery('#result_msg').html('<span class="' + result.success + '">' + result.data + '</span>');
         }
     });
    });
    
             //end
    

document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("submitForm", function () {
        setTimeout(function () {
            let form = document.getElementById("saveReadingResult");
            form.submit(); // This should work now that there's no conflict
        }, 2000); 
    });
});


//end new adding

</script>
</html>

<?php


}
else{
    get_header();
    if (!$foundUser) {
        echo "
        <div class='checkout-modal-overlay'>
            <div class='checkout-modal'>
                <h3>Bạn chưa mua đề thi này</h3>";     
        } 

    else if ($foundUser['time_left'] <= 0) {
        echo "
        <div class='checkout-modal-overlay'>
            <div class='checkout-modal'>
                <h3> Bạn đã từng mua test này nhưng số lượt làm test này đã hết rồi, vui lòng mua thêm token<i class='fa-solid fa-face-sad-tear'></i></h3>";
    }

    echo"
            <p> Bạn đang có: $token token</p>
            <p> Để làm test này bạn cần $token_need token. Bạn sẽ được làm test này $time_allow lần </p>
            <p class = 'info-buy'>Bạn có muốn mua $time_allow lượt làm test này với $token_need không ?</button>
                <div class='button-group'>
                    <button class='process-token' onclick='preProcessToken()'>Mua ngay</button>
                    <button style = 'display:none' class='close-modal'>Hủy</button>
                </div>  
            </div>
        </div>
        
        <script>
    
    function preProcessToken() {
        if ($token < $token_need) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: 'Bạn không đủ token để mua test này',
                footer: `<a href='${site_url}/dashboard/buy_token/'>Nạp token vào tài khoản ngay</a>`
            });
        } else {
            console.log(`Allow to next step`);
            jQuery.ajax({
                url: `${site_url}/wp-admin/admin-ajax.php`,
                type: 'POST',
                data: {
                    action: 'update_buy_test',
                    type_transaction: 'paid',
                    table: 'ielts_reading_test_list',
                    change_token: '$token_need',
                    payment_gate: 'token',
                    type_token: 'token',
                    title: 'Renew test $testname with $id_test (Ielts Reading Test) with $token_need (Buy $time_allow time do this test)',
                    id_test: id_test
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mua test thành công!',
                        text: 'Trang sẽ được làm mới sau 2 giây.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        willClose: () => location.reload()
                    });
                },
                error: function (error) {
                    console.error('Error updating time_left:', error);
                }
            });
        }
    }
        </script>
        <style>
.checkout-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.checkout-modal {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    width: 400px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.checkout-modal h3 {
    font-size: 18px;
    color: #333;
}

.checkout-modal p {
    margin: 10px 0;
    color: #555;
}

.checkout-modal .button-group {
    margin-top: 20px;
}

.process-token {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-right: 10px;
    font-size: 14px;
}

.process-token:hover {
    background-color: #0056b3;
}

.close-modal {
    background-color: #ccc;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
}

.close-modal:hover {
    background-color: #aaa;
}
</style>

<script>
    document.querySelector('.close-modal')?.addEventListener('click', function() {
        document.querySelector('.checkout-modal-overlay').style.display = 'none';
    });
</script>
        ";
        } 
    }
    
 else {
        get_header();
            echo "<p>Không tìm thấy đề thi.</p>";
            exit();
    }

} else {
    get_header();
    echo "<p>Please log in to submit your answer.</p>";

}
get_footer();