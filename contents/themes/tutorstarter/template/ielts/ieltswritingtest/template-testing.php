<?php
/*
 * Template Name: Doing Template Writing
 * Template Post Type: ieltswritingtests
 
 */


if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;

    // Get the custom number field value
    //$custom_number = get_post_meta($post_id, '_ieltswritingtests_custom_number', true);
    $custom_number = get_query_var('id_test');

  // Database credentials
  $servername = DB_HOST;
  $username = DB_USER;
  $password = DB_PASSWORD;
  $dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id_test = $custom_number;
 // Get current time (hour, minute, second)
 $hour = date('H'); // Giờ
 $minute = date('i'); // Phút
 $second = date('s'); // Giây

 if (!$user_id) {
    $user_id = '00'; // Set user_id to "00" if invalid
}

if (!$id_test) {
    $id_test = '00'; // Set id_test to "00" if invalid
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



// Fetch the question_choose and time for the given id_test
$sql_test = "SELECT *  FROM ielts_writing_test_list WHERE id_test = ?";
$stmt_test = $conn->prepare($sql_test);

$stmt_test->bind_param("s", $custom_number);
$stmt_test->execute();
$result_test = $stmt_test->get_result();



// Query to fetch token details for the current username
$sql2 = "SELECT token, token_use_history 
         FROM user_token 
         WHERE username = ?";


// Prepare and execute the first query

if ($result_test->num_rows > 0) {
    
   // $question_choose = [];
    $time = ''; // Variable to store the time


    // Lấy các ID từ question_choose (ví dụ: "1001,2001,3001")
    $data = $result_test->fetch_assoc();
    $question_choose = isset($data['question_choose']) ? explode(',', $data['question_choose']) : [];


    $testname = $data['testname'];
    $token_need = $data['token_need'];
    $time_allow = $data['time_allow'];
    $permissive_management = $data['permissive_management'];
    $time = $data['time'];
    $test_type = $data['test_type'];


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
                let time_left = "' . (isset($foundUser['time_left']) ? $foundUser['time_left'] : 10) . '";
            </script>';







get_header(); // Gọi phần đầu trang (header.php)
// Prepare an array to store the questions

// Fetch Task 1 questions based on question_choose
if (!empty($question_choose)) {
    $placeholders = implode(',', array_fill(0, count($question_choose), '?'));

    // Query Task 1
    $sql1 = "SELECT id_test, task, question_type, question_content, image_link, sample_writing, important_add 
              FROM ielts_writing_task_1_question WHERE id_test IN ($placeholders)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param(str_repeat("i", count($question_choose)), ...$question_choose);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    $id_task_1 = []; // Lưu id_test của Task 1
    while ($row = $result1->fetch_assoc()) {
        $id_task_1[] = $row['id_test'];
        $questions[] = [
            "question" => $row['question_content'],
            "part" => $row['task'],
            "sample_essay" => $row['sample_writing'],
            "image" => $row['image_link'],
            "id_question" => $row['id_test'],
            "question_type" => $row['question_type'],
        ];
    }

    // Query Task 2
    $sql2 = "SELECT id_test, task, topic, question_type, question_content, sample_writing, important_add 
              FROM ielts_writing_task_2_question WHERE id_test IN ($placeholders)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param(str_repeat("i", count($question_choose)), ...$question_choose);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $id_task_2 = []; // Lưu id_test của Task 2
    while ($row = $result2->fetch_assoc()) {
        $id_task_2[] = $row['id_test'];
        $questions[] = [
            "question" => $row['question_content'],
            "part" => $row['task'],
            "sample_essay" => $row['sample_writing'],
            "id_question" => $row['id_test'],
            "topic" => $row['topic'],
            "question_type" => $row['question_type'],
            "image" => '',
        ];
    }

    // Echo ra script JS
    echo "<script>
            var id_task_1 = " . json_encode($id_task_1) . ";
            var id_task_2 = " . json_encode($id_task_2) . ";
          </script>";
}


// Output the quizData as JavaScript
echo '<script type="text/javascript">
const quizData = {
    "title": "' . htmlspecialchars($testname) . '",
    "testtype": "' . htmlspecialchars($test_type) . '",
    "duration": "' . htmlspecialchars($time) . '",
    "questions": ' . json_encode($questions) . '

};
console.log("Bài thi: ", quizData);
</script>';


/*
// Truy vấn dữ liệu từ bảng order_and_prompt_api_list
$sql3 = "SELECT list_name_endpoint_order, last_use_end_point 
          FROM order_and_prompt_api_list 
          WHERE number = 1";
$result3 = $conn->query($sql3);

$now_end_point = '';
if ($result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        $list_name_endpoint_order = json_decode($row['list_name_endpoint_order'], true);
        $last_use_end_point = $row['last_use_end_point'];

        if (is_array($list_name_endpoint_order)) {
            // Tìm id của last_use_end_point
            $current_id = null;
            foreach ($list_name_endpoint_order as $item) {
                if ($item['name'] === $last_use_end_point) {
                    $current_id = $item['id'];
                    break;
                }
            }

            // Tìm name kế tiếp
            $now_end_point = '';
            if ($current_id !== null) {
                // Nếu tìm thấy last_use_end_point, lấy id kế tiếp
                $next_id = $current_id + 1;
                $now_end_point = array_reduce($list_name_endpoint_order, function ($carry, $item) use ($next_id) {
                    return ($item['id'] === $next_id) ? $item['name'] : $carry;
                }, '');
            }

            // Nếu không tìm thấy name kế tiếp (id cuối cùng), lấy id 1
            if (!$now_end_point) {
                foreach ($list_name_endpoint_order as $item) {
                    if ($item['id'] === 1) {
                        $now_end_point = $item['name'];
                        break;
                    }
                }
            }

            // Tính next_end_point
            $next_end_point = '';
            if ($now_end_point) {
                foreach ($list_name_endpoint_order as $item) {
                    if ($item['name'] === $now_end_point) {
                        $current_id = $item['id'];
                        break;
                    }
                }

                if ($current_id !== null) {
                    $next_id = $current_id + 1;
                    $next_end_point = array_reduce($list_name_endpoint_order, function ($carry, $item) use ($next_id) {
                        return ($item['id'] === $next_id) ? $item['name'] : $carry;
                    }, '');
                }

                if (!$next_end_point) {
                    foreach ($list_name_endpoint_order as $item) {
                        if ($item['id'] === 1) {
                            $next_end_point = $item['name'];
                            break;
                        }
                    }
                }
            }
        }
    }
}


// Xuất biến JavaScript
echo '<script type="text/javascript">
var now_end_point = "' . htmlspecialchars($now_end_point) . '";
var next_end_point_for_update = "' . htmlspecialchars($next_end_point) . '";



</script>';




// Nếu tìm thấy now_end_point, kiểm tra bảng api_key_route
if ($now_end_point) {
    $sql4 = "SELECT name_end_point, api_endpoint_url, api_key, updated_time, type, all_time_use_number, today_time_use_number 
             FROM api_key_route 
             WHERE name_end_point = ?";
    $stmt = $conn->prepare($sql4);
    $stmt->bind_param("s", $now_end_point);
    $stmt->execute();
    $result4 = $stmt->get_result();

    if ($result4->num_rows > 0) {
        // Xuất dữ liệu ra console
        while ($row = $result4->fetch_assoc()) {
            echo '<script type="text/javascript">
            var url_end_point = "' . htmlspecialchars($row['api_endpoint_url']) . '";
            var all_time_use = "'. (int)$row['all_time_use_number'] .  '";
            var today_use = "'  . (int)$row['today_time_use_number'] . '";
            var type_gate = "' . htmlspecialchars($row['type']) . '";

            //console.log("API Endpoint URL: ", "' . htmlspecialchars($row['api_endpoint_url']) . '");
            //console.log("Updated Time: ", "' . htmlspecialchars($row['updated_time']) . '");
            //console.log("Type: ", "' . htmlspecialchars($row['type']) . '");
            //console.log("All Time Use Number: ", ' . (int)$row['all_time_use_number'] . ');
            //console.log("Today Time Use Number: ", ' . (int)$row['today_time_use_number'] . ');
            </script>';
        }
    } else {
        echo '<script type="text/javascript">console.log("No matching endpoint found in api_key_route.");</script>';
    }
    $stmt->close();
} else {
    echo '<script type="text/javascript">console.log("No now_end_point found.");</script>';
}

// Close the database connection

*/


// Close the database connection
$conn->close();

    ?>
  


<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://smtpjs.com/v3/smtp.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script type="text/javascript" src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/function/handwriting/handwriting.js"></script>

  
    

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ielts Writing Tests</title>
    <link rel="stylesheet" href="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/style/style1.css">
    
</head>
<style>
    

.tooltip {
    position:relative;
    cursor: pointer;
    background-color: yellow;

  }
 
  .tooltip .tooltiptext {
    visibility: hidden;
    width: 150px;
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






    .container {
            margin-bottom: 60px; /* Ensure space for the time-remaining-container */
            display: flex;
}


.buttonsidebar{
    background-color: transparent;
              padding: 8px 12px 8px 12px;
              border: none;
          color: black;
              font-family: sf pro text, -apple-system, BlinkMacSystemFont, Roboto,
                  segoe ui, Helvetica, Arial, sans-serif, apple color emoji,
                  segoe ui emoji, segoe ui symbol;
              margin-top: 20px;
              font-weight: 700;
              font-size: 20px;
}
.buttonsidebar img{
    width: 22px;
    height: 22px;
}
.h1-text{
    font-weight:bold;
    font-size: 25px;
}


.quiz-container {
    padding:10px;
    overflow: auto;
    visibility: visible;
    position: absolute;
    left: 0;
    width: 100%;
  } 

.overall_band_test_container-css{
    width: 100%;
}

  #time-remaining-container {
    width: 100%;
    height: 35px;
    display: flex;
    justify-content: center;
    align-items: center; 
    position: fixed;
    bottom: 0;
    background-color: black;
    color: white;
    padding: 0px;
    left: 0;
}

.question-side-img{
    width: 85%;
}


.chart_overall_full{
    display: flex;
    flex-direction: row;
}

.left_chart {
    width: 70%;
    padding: 10px;
}

.right_chart {
    width: 30%;
    padding: 10px;
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


/* Fixed bottom navigation for questions */

   /*Ẩn footer mặc định */
   #colophon.site-footer { display: none !important; }
</style>
<body onload="main()">
    
    <div class="container"  id ="container">
        <div class="main-block">


        <div id = "test-prepare">
            <div class="loader"></div>
            <h3>Your test will begin shortly</h3>
            <div style="display: none;" id="date" style="visibility:hidden;"></div>
            <div  style="display: none;"  id="id_test"  style="visibility:hidden;"><?php echo esc_html($custom_number);?></div>
            <div  style="display: none;"  id="title"  style="visibility:hidden;"></div>
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
            <div style="display: none;" id="title" style="visibility:hidden;"><?php the_title(); ?></div>
            <div  style="display: none;"  id="id_test"  style="visibility:hidden;"><?php echo esc_html($custom_number);?></div>
            <button  style="display: none;" class ="start_test" id="start_test"  onclick = "prestartTest()">Start test</button>
            <i id = "welcome" style = "display:none">Click Start Test button to start the test now. Good luck</i>
        </div>
         


            <div id="basic-info"  style="display:none">
                <div style="display: flex;">
                    <b style="margin-right: 5px;">Description </b>
                    <div id="description"></div>
                </div>
                <div style="display: flex;">
                    <b style="margin-right: 5px;">Thời gian làm bài: </b>
                    <div id="duration"></div>
                </div>
                <div style="display: flex;">
                    <b style="margin-right: 5px;">ID Test:</b>
                    <div id="id_test_div"></div>
                </div>
                <div style="display: flex;">
                    <b style="margin-right: 5px;">ID Category: </b>
                    <div id="id_category"></div>
                </div>
                





        <div id ="info-div"  style="display:none">
                <div id="band-score"></div> <!--Ex: 8.0-->
                <div id="band-score-expand"></div><!--Ex: 8.0 (Task1): 7.5 Task2: 8.0-->

                <div id="date-div"></div>
                <div id="type_test"></div>
                <div id="time-result" ></div>
                <div id="data-save-task-1" ></div>
                <div id="data-save-task-2" ></div>

                <div id="user-essay-task-1"></div>
                <div id="user-essay-task-2"></div>
                <div id="summary-essay-task-1"></div>
                <div id="summary-essay-task-2"></div>
                <div id="breakdown-task-1"></div>
                <div id="breakdown-task-2"></div>
                <div id="details-comment-task-1"></div>
                <div id="details-comment-task-2"></div>


                <div id="number-of-question-div" ></div>
                <div id="id-category-div"></div>
                <div id="question-test-div" ></div>
                <div id="user-essay-div"></div>
                <div id="sample-essay-div" ></div>
                <div id="overall-band-div" ></div>
                <div id="time-do-test-div" ></div>
                <div id="summarize-test-div" ></div>
                <div id="overall-band-and-comment-div" ></div>
                <div id="analysis-test-div" ></div>
            </div>

                <div style="display: flex;">
                    <b style="margin-right: 5px;">Số câu hỏi: </b>
                    <div id="number-questions"></div>
                </div>
                <div style="display: flex;">
                    <b style="margin-right: 5px;">Percentage of correct answers that pass the test: </b>
                    <div id="pass_percent"></div>
                </div>
                <div style="display: flex;">
                    <b style="margin-right: 5px;">List of question: </b>
                    <button id = "show-list-popup-btn">Show list</button>
                </div>

                <div id= "show-list-popup" class="popup">
                    <div class="popup-content-1">
                
                        <span class="close" onclick="closeListQuestion()">&times;</span>
                         <p class ="h1-text" >Danh sách câu hỏi</p>
                         <div id="question-list"></div>
                    </div>
                 </div>


                 
            </div>
            
            
         <!-- New add sửa đổi 3/8/2024-->

            <div class = "fixedrightsmallbuttoncontainer" style ="display:none">
                <button  class ="buttonsidebar"  id="report-error"><img  width="22px" height="22px" src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/assets/images/report.png" ></button><br>  
                   <button class ="buttonsidebar"  id="full-screen-button">⛶</button><br>
                    <button  onclick=" DarkMode()" class ="buttonsidebar"><img width="30px" height="30px" src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/assets/images/dark-mode.png"></img></button><br>
                </div>
                
         
         
         <div id="report-error-popup" class="popup-report">
                    
            <div class="popup-content-report">
         
         
         <span class="close" onclick="closeReportErorPopup()">&times;</span>
                <section class ="contact">
                <p class ="h1-text" style="text-align: center;" > Báo lỗi đề thi, đáp án </p>
         
         
                <form action="#" reset()>
                <div class="input-box">
                    <div class="input-field field">
                <input type="text" id ="name" class="item" placeholder="Tên của bạn" autocomplete="off">
                            <div class="error-txt">Không được bỏ trống ô này</div>
         
            </div>
         
            <div class="input-field field">
                <input type="email" class="item" id="email" placeholder="Nhập email của bạn" autocomplete="off">
                <div class="error-txt email">Không được bỏ trống ô này</div>
         
            </div>
         </div>
         
         <div class="input-box">
                    <div class="input-field field">
                <input id="testnamereport" class="item" type="text" name="testnamereport" autocomplete="off" placeholder="Nhập tên đề thi báo lỗi" >
                            <div class="error-txt">Không được bỏ trống ô này</div>
         
            </div>
         
         
                    <div class="input-field field">
         
                <input type="text" name="testnumberreport" id="testnumberreport" class="item" autocomplete="off" placeholder="Bạn muốn báo lỗi câu số mấy" >
                            <div class="error-txt">Không được bỏ trống ô này</div>
         
            </div>
         </div>
         
         <div class ="textarea-field field">
                <textarea id = "descriptionreport" autocomplete="off" rows="4" name="description" placeholder="Mô tả thêm về lỗi" class="item"> </textarea>
                <div class="error-txt">Không được bỏ trống ô này</div>
            </div>
                  <div class="g-recaptcha" data-sitekey="6Lc9TNkpAAAAAKCIsj_j8Ket1UH5nBwoO2DmKZ_e
         "></div>
         
                <button type="submit">Gửi báo lỗi</button>
            </form>
         </section>
            </div>
         </div>
         
         
         
         
         
                
                
         </div>
         <!-- End new add sửa đổi 3/8/2024-->
  
            <div id="quiz-container" class ="quiz-container"></div>
            
        </div>
        
        <div id="time-remaining-container">
            <div id="button-container">
                <div id="clock-block">
                    <h3 id="countdown"></h3>
                </div>
                
            </div>
            <div id = "navigation-button" style="display: none;" >
                <button id="prev-button"  onclick="showPrevQuestion()">Quay lại</button>
                <button id="next-button"  onclick="showNextQuestion()">Tiếp theo</button>

                
            </div>

            <div id = "submit-button-container">  
                <button onclick="preSubmitTest()" style="display: none;" id="submit-button">Nộp bài làm</button>                
            </div>
        </div>

        <div id="canvas-container"></div>

 <!-- Next button -->




        <div id="loading-popup" style="display: none;">
            <div id="loading-spinner"></div>
        </div>
    </div>


</div>
 <div id ="result-full-page">
        <div class="tab">
            <button class="tablinks" onclick="changeTabResult(event, 'result-page-tabs-1')">Overall</button>
            <button class="tablinks" onclick="changeTabResult(event, 'result-page-tabs-2')">Your test</button>
            <button class="tablinks" onclick="changeTabResult(event, 'result-page-tabs-3')">Sample</button>
            <button class="tablinks" onclick="changeTabResult(event, 'result-page-tabs-4')">Practice</button>


        </div>

        <div id ="result-page-tabs-1" class="tabcontent">
            <h3>Result </h3>
            <div style="display: none;" id="date" style="visibility:hidden;"></div>

            <div id ="overall_band_test_container" class ="overall_band_test_container-css" style="display:none">

                <div id ="full_band_chart" class="chart_overall_full">
                    <div class ="left_chart">
                        <h3 style="color:red">FULL OVERALL</h3>
                        <canvas id="barchartfull" style="width:100%;max-width:600px"></canvas>
                    </div>
                    <div class ="right_chart">
                        <div id="detail_score"></div>
                    </div>


                </div>
                <div id ="area-final">
                    <button class ="button-3">Rent teacher to mark this essay</button>
                    <button onclick="window.print();" class ="button-3">Print your essay</button>

                </div>
                

            
            </div>
            <div id ="final-result"></div>
            <div id ="breakdown"></div>
            <div id ="band-decriptor"></div>
            <h3>General Comment</h3>
            <div id ="general-comment"></div>

        </div>
        <div id="result-page-tabs-2" class="tabcontent">
            <div id="question-buttons-container"></div>
            <div id="tab2-user-essay-container"></div>
        </div>




        <div id ="result-page-tabs-3"class="tabcontent">
            <h3>Sample Answer</h3>
            <div id = "sample-tab-container"></div>

        </div>
        <div id ="result-page-tabs-4"class="tabcontent">
                <h3>This is tab 4 result</h3>
                <div id="userResult"></div>  
                <div id="userBandDetail"></div>            
                <div id="userAnswerAndComment"></div>   
                
                    <!-- giấu form send kết quả bài thi -->


                    
                
                <span id="message"></span>
                <form id="saveUserWritingTest" >
                        <div class="card">
                            <div class="card-header">Form lưu kết quả 11/5</div>
                            <div class="card-body" >

                    


                    <div class = "form-group">
                        <input type="text" id="dateform" name="dateform" placeholder="Ngày"  class="form-control form_data"  />
                        <span id="date_error" class="text-danger" ></span>
                    </div>

                    

                    <div class = "form-group" >
                        <input type="text" id="idtest" name="idtest" placeholder="Id test"  class="form-control form_data" />
                        <span id="idtest_error" class="text-danger" ></span>
                    </div>

                 

                    <div class = "form-group"   >
                        <input type="text"  id="testname" name="testname" placeholder="Test Name"  class="form-control form_data" />
                        <span id="testname_error" class="text-danger"></span>
                    </div>
                    <div class = "form-group"   >
                        <input type="text"  id="test_type" name="test_type" placeholder="Test Type"  class="form-control form_data" />
                        <span id="testname_error" class="text-danger"></span>
                    </div>
                    <div class = "form-group"  >
                        <input type="text" id="timedotest" name="timedotest" placeholder="Thời gian làm bài"  class="form-control form_data" />
                        <span id="time_error" class="text-danger"></span>
                    </div>
                    
                    <div class = "form-group"   >
                        <textarea type="text"  id="band-score-form" name="band-score-form" placeholder="Band Score Overall"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div class = "form-group"   >
                        <textarea type="text"  id="band-score-expand-form" name="band-score-expand-form" placeholder="Band Score Overall - Expand"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>

                    <div style = "display:none" class = "form-group"   >
                        <textarea type="text"  id="task1userform" name="task1userform" placeholder="User Task 1"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div style = "display:none"  class = "form-group"   >
                        <textarea type="text"  id="task2userform" name="task2userform" placeholder="User Task 2"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>

                    <div style = "display:none" class = "form-group"   >
                        <textarea type="text"  id="task1summaryuserform" name="task1summaryuserform" placeholder="Summary User Task 1"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>

                    <div style = "display:none" class = "form-group"   >
                        <textarea type="text"  id="task2summaryuserform" name="task2summaryuserform" placeholder="Summary User Task 2"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div style = "display:none" class = "form-group"   >
                        <textarea type="text"  id="task1breakdownform" name="task1breakdownform" placeholder="User Breakdown Task 1"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div style = "display:none"  class = "form-group"   >
                        <textarea type="text"  id="task2breakdownform" name="task2breakdownform" placeholder="User Breakdown Task 2"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div class = "form-group"   >
                        <textarea type="text"  id="datasaveformtask1" name="datasaveformtask1" placeholder="datasave task 1"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div class = "form-group"   >
                        <textarea type="text"  id="datasaveformtask2" name="datasaveformtask2" placeholder="datasave task 2"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>

                    <div class = "form-group"   >
                        <textarea type="text"  id="task1detailscommentform" name="task1detailscommentform" placeholder="User Breakdown Task 1"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div class = "form-group"   >
                        <textarea type="text"  id="task2detailscommentform" name="task2detailscommentform" placeholder="User Breakdown Task 2"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>



                <!--New Add 1/3/2025-->
                    <div class = "form-group"   >
                        <textarea type="text"  id="user_essay" name="user_essay" placeholder="User Essay Save JSON"  class="form-control form_data" ></textarea>
                        <span id="user_essay_error" class="text-danger"></span>  
                    </div>

                    <div class = "form-group"   >
                        <textarea type="text"  id="user_band_score_and_suggestion" name="user_band_score_and_suggestion" placeholder="User Band Score and Recommendation"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>

                    

                <!--New Add 1/3/2025-->

                    <div class = "form-group"   >
                        <textarea type="text"  id="testsavenumber" name="testsavenumber" placeholder="testsavenumber"  class="form-control form_data" ></textarea>
                        <span id="testsavenumber_error" class="text-danger"></span>  
                    </div>


                    

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
        
    </div>
    
    <script >
    


    // function save data qua ajax
    jQuery('#saveUserWritingTest').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission
    
     var link = "<?php echo admin_url('admin-ajax.php'); ?>";
    
     var form = jQuery('#saveUserWritingTest').serialize();
     var formData = new FormData();
     formData.append('action', 'save_user_result_ielts_writing');
     formData.append('save_user_result_ielts_writing', form);
    
     jQuery.ajax({
         url: link,
         data: formData,
         processData: false,
         contentType: false,
         type: 'post',
         success: function(result) {
             jQuery('#submit').attr('disabled', false);
             if (result.success == true) {
                 jQuery('#saveUserWritingTest')[0].reset();
             }
             jQuery('#result_msg').html('<span class="' + result.success + '">' + result.data + '</span>');
         }
     });
    });
    
             //end
    

document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("submitForm", function () {
        setTimeout(function () {
            let form = document.getElementById("saveUserWritingTest");
            form.submit(); // This should work now that there's no conflict
        }, 2000); 
    });
});


//end new adding


 
                    document.getElementById("title").innerHTML = quizData.title;
                    document.getElementById("type_test").innerHTML = quizData.testtype;
                    var questionList = document.getElementById("question-list");
                    questionList.innerHTML = ""; // Clear any existing content

                for (let i = 0; i < quizData.questions.length; i++) {
                    questionList.innerHTML += `Question ${i + 1}: ${quizData.questions[i].question}<br>`;
                }
              

               
                    
            

                const dateElement = document.getElementById('date-div');

// Hàm định dạng ngày giờ chuẩn SQL
function formatDateTimeForSQL(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const seconds = String(date.getSeconds()).padStart(2, '0');
  
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

// Sử dụng
const now = new Date();
dateElement.innerHTML = formatDateTimeForSQL(now);
console.log(dateElement.innerHTML)

        
        document.getElementById("show-list-popup-btn").addEventListener('click', openListQuestion);
        
        // Close the draft popup when the close button is clicked
        function closeListQuestion() {
            document.getElementById('show-list-popup').style.display = 'none';
        }

        function openListQuestion() {
            document.getElementById('show-list-popup').style.display = 'block';
          
        }




function openDraft(index) {
  var x = document.getElementById("draft-"+index);
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}

function changeTabResult(evt, tabResultName) {
                var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabResultName).style.display = "block";
        evt.currentTarget.className += " active";
        }

        // Automatically click the first tab when the page loads
        document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.tablinks').click();
    });
        
    



let currentQuestionIndex = 0;
let userMarkEssay;
function main() {
    document.body.classList.add('watermark');

    setTimeout(function(){
        console.log("Show Test!");
        document.getElementById("start_test").style.display="block";
        
        document.getElementById("welcome").style.display="block";

    }, 1000);
    
    


    //MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
    if (quizData.description !== "")
        document.getElementById("description").innerHTML = quizData.description;

    
    document.getElementById("duration").innerHTML = formatTime(quizData.duration);
    document.getElementById("pass_percent").innerHTML = quizData.pass_percent + "%";
    document.getElementById("number-questions").innerHTML = quizData.number_questions + " question(s)";
    document.getElementById("id_test_div").innerHTML = quizData.id_test;

    let contentQuestions = "";
   

 for (let i = 0; i < quizData.questions.length; i++) {
    let questionId = quizData.questions[i].id_question;
    let part = quizData.questions[i].part;
    let sampleEssay= quizData.questions[i].sample_essay;
    let questioncontent = quizData.questions[i].question;
    let explanationQuiz = quizData.questions[i].explanations;



        // if need bookmark, add to line class="question"<image src="bookmark-1.png" id="imageToggle" onclick="rememberQuestion(${i + 1})"></image>             

    contentQuestions += `


    <div class="questions" id="question-${i}" style="display:none;" >
        



        <div id ="current_band_detail_div-${questionId}" style="display:none;"></div>
        <div class="quiz-section">
            <div class="question-side">
                <p class="header-question">Question ${(i + 1)}:  </p>
                <div id="navigation-buttons" display='none'>
                <!-- Next button -->
           
        
                </div>
                <div id = "questionContextArea-${i}" >
                    <p class="question">
                    <p style="font-style:italic">Writing task ${part}</p> 
                    <b>${questioncontent}</b>
                    ${quizData.questions[i].image ? `<img class="question-side-img" src="${siteUrl}/contents/themes/tutorstarter/template/media_img_intest/ielts_writing/${questionId}.png">` : ''}                    </p>
                </div>
                <button onclick="openDraft(${i})" class ='open-draft-button'>Open Draft</button>

                <textarea class="draft" id="draft-${i}"></textarea>

                <div id ="sample-essay-area-${i}" style="display:none;"><p class ="h1-text" style='color:red'>Sample Essay:</p> <br>${sampleEssay} </div> 

                <div id="overall-band-${questionId}" class="overall-band" style="display:none;"></div>
            </div>


            <div class="answer-side" id = "userEssayTab2-${i}" >
                <div class="answer-list">
                    <button id="input_normal" style ="display:none" onclick="toggleInputMode(${i}, 'normal')">Nhập văn bản bình thường</button>
                    <button id="input_handwrite" style ="display:none" onclick="toggleInputMode(${i}, 'handwrite')">Chuyển sang dạng viết (Phù hợp với thí sinh thi paper exam)</button>
                    <textarea spellcheck="false" class="exam_area" placeholder="You should start your essay here..." id="question-${i}-input" oninput="updateWordCount(${i})"></textarea>
                   <!--16/8/2024 -->
                <div class ="userEssayContainer" id = "userEssayCheckContainer${i+1}">
                    <h3>Your Essay</h3>
                    <div  id ="userEssayCheck-${i+1}" ></div>
                </div>
                <!--16/8/2024 -->
                    <div id="word-count-${i}" class="word-count">Word count: 0 Sentence count: 0 Paragraph: 0</div>
                    <button style="display:none" onclick="checkIncorrectSpellings(${i})" >Check</button>
                    <div id ="correction-${i}"></div>
               
                
                    </div>
                <div class="explain-zone" id="explanation-${questionId}" style="display:none;">
                    <p><b>Giải thích: </b>${explanationQuiz}</p>
                </div>

                <div id="summarize-${i}" style="display:none;"> </div>
                <div id="breakdown-${i}" style="display:none;"> </div>

                <div id="detail-cretaria-${i}" style="display:none;"> </div>
                <div id="recommendation-${i}" style="display:none;"> </div>

            </div>
        </div>
    </div>`;

   
    
}



    document.getElementById("quiz-container").innerHTML = contentQuestions;
    
    document.getElementById("quiz-container").style.display = 'none';
    document.getElementById("submit-button").style.display = 'none';
    document.getElementById("clock-block").style.display = 'none';

   // addEventListenersToInputs();
}




 




/*
function toggleInputMode(questionIndex, mode) {
    const textarea = document.getElementById(`question-${questionIndex}-input`);
    const canvasContainerId = `canvas-container-${questionIndex}`;
    let canvasContainer = document.getElementById(canvasContainerId);



    if (mode === 'handwrite') {
        
        if (!canvasContainer) {
            canvasContainer = document.createElement('div');
            canvasContainer.id = canvasContainerId;
            canvasContainer.innerHTML = `
                <div class = "canvas_exam_area">
                         <canvas id="canvas-${questionIndex}"   width="590px" height = "700px" style="border: 1px solid; cursor: pointer;"></canvas>
                </div>
                <div class='buttons'>
                    <button onclick="recognizeCanvas(${questionIndex})">Recognize</button>
                    <button onclick="eraseCanvas(${questionIndex})">Erase</button>
                    <button onclick="undoCanvas(${questionIndex})">Undo</button>
                    <button onclick="redoCanvas(${questionIndex})">Redo</button>
                    <button onclick="toggleEraser(${questionIndex})">Toggle Eraser</button>
                    <button onclick="drawLines(${questionIndex})">Draw Lines</button>
                </div>
                <div>Result: <span id='result-${questionIndex}'></span></div>
            `;
            textarea.parentNode.insertBefore(canvasContainer, textarea.nextSibling);
            initializeCanvas(questionIndex);






        }
        textarea.style.display = 'none';
        canvasContainer.style.display = 'block';
    } 
    
    
    else {
        if (canvasContainer) {
            canvasContainer.style.display = 'none';
        }
        textarea.style.display = 'block';
    }



    
} */
let undoStack = [];
let redoStack = [];

function initializeCanvas(questionIndex) {
    const canvasElement = document.getElementById(`canvas-${questionIndex}`);
    const canvas = new handwriting.Canvas(canvasElement, 3);
    canvas.setCallBack((data, err) => {
        if (err) throw err;
        document.getElementById(`result-${questionIndex}`).innerHTML = data;
    });

    canvas.setLineWidth(5);
    canvas.setOptions({
        language: "en",
        numOfReturn: 1
    });

    canvasElement.canvasObj = canvas;

    // Add drawing event listeners
    let isDrawing = false;

    canvasElement.addEventListener('mousedown', () => {
        isDrawing = true;
        saveState(canvasElement);
    });

    canvasElement.addEventListener('mousemove', (e) => {
        if (isDrawing) {
            canvasElement.canvasObj.stroke(e);
        }
    });

    canvasElement.addEventListener('mouseup', () => {
        if (isDrawing) {
            recognizeCanvas(questionIndex);
            isDrawing = false;
        }
    });

    canvasElement.addEventListener('mouseout', () => {
        if (isDrawing) {
            recognizeCanvas(questionIndex);
            isDrawing = false;
        }
    });
}

function saveState(canvasElement) {
    undoStack.push(canvasElement.toDataURL());
    if (undoStack.length > 50) { // Limit the undo stack size
        undoStack.shift();
    }
    redoStack = []; // Clear the redo stack
}

function restoreState(stack, canvasElement) {
    if (stack.length > 0) {
        const ctx = canvasElement.getContext('2d');
        const canvasState = stack.pop();
        const img = new Image();
        img.src = canvasState;
        img.onload = () => {
            ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
            ctx.drawImage(img, 0, 0);
            recognizeCanvas(0); // Automatically call recognizeCanvas
        };
    }
}

function recognizeCanvas(questionIndex) {
    document.getElementById(`canvas-${questionIndex}`).canvasObj.recognize();
}

function eraseCanvas(questionIndex) {
    const canvasElement = document.getElementById(`canvas-${questionIndex}`);
    const ctx = canvasElement.getContext('2d');
    ctx.clearRect(0, 0, canvasElement.width, canvasElement.height);
    saveState(canvasElement); // Save state after erasing
    recognizeCanvas(questionIndex);
}

function undoCanvas(questionIndex) {
    const canvasElement = document.getElementById(`canvas-${questionIndex}`);
    if (undoStack.length > 0) {
        redoStack.push(canvasElement.toDataURL());
        restoreState(undoStack, canvasElement);
    }
}

function redoCanvas(questionIndex) {
    const canvasElement = document.getElementById(`canvas-${questionIndex}`);
    if (redoStack.length > 0) {
        undoStack.push(canvasElement.toDataURL());
        restoreState(redoStack, canvasElement);
    }
}

function toggleEraser(questionIndex) {
    const canvasElement = document.getElementById(`canvas-${questionIndex}`);
    const canvas = canvasElement.canvasObj;
    canvas.isEraser = !canvas.isEraser;
    canvas.setLineWidth(canvas.isEraser ? 30 : 5);
    canvas.setPenColor(canvas.isEraser ? "white" : "black");
}

function drawLines(questionIndex) {
    const canvasElement = document.getElementById(`canvas-${questionIndex}`);
    const ctx = canvasElement.getContext("2d");
    ctx.lineWidth = 1;
    ctx.strokeStyle = "#e0e0e0";
    const lineHeight = 40;
    for (let y = lineHeight; y < canvasElement.height; y += lineHeight) {
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(canvasElement.width, y);
        ctx.stroke();
    }
    saveState(canvasElement); // Save state after drawing lines
}

// Call initializeCanvas for question 0
//initializeCanvas(0);

/*
document.getElementById('input_handwrite').addEventListener('click', () => toggleInputMode(currentQuestionIndex, 'handwrite'));
document.getElementById('input_normal').addEventListener('click', () => toggleInputMode(currentQuestionIndex, 'normal'));
*/

//submitTest();



let duration = quizData.duration * 60; // Convert duration to seconds
let countdownInterval;
let countdownElement; // Declare this to use later for tracking the countdown

document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);

    const optionTimeSet = urlParams.get('option');
    const optionTrackSystem = urlParams.get('optiontrack');

    if (optionTimeSet) {
        duration = optionTimeSet; // Override the duration if provided in URL
        var timeleft = optionTimeSet / 60 + " phút";
        console.log(`Time left: ${timeleft}`);
    }

    // Initialize countdownElement to the total duration initially
    countdownElement =  duration;

    // Example: Start a countdown (You would have your own countdown logic)
    countdownInterval = setInterval(function() {
        if (countdownElement > 0) {
            countdownElement--;
        } else {
            clearInterval(countdownInterval);
        }
    }, 1000);
});
function formatTime(seconds) {
    let minutes = Math.floor(seconds / 60);
    let remainingSeconds = seconds % 60;
    return `${minutes} phút ${remainingSeconds} giây`;
}


function prestartTest()
{
    if(premium_test == "False"){
        console.log("Cho phép làm bài")
    }
    else{
    console.log(premium_test);
    console.log(token_need);
    console.log(change_content);
    console.log(time_left);
    // Giảm time_left tại frontend
    time_left--;
    console.log("Updated time_left:", time_left);

    // Gửi request AJAX đến admin-ajax.php
    jQuery.ajax({
        url: `${siteUrl}/wp-admin/admin-ajax.php`,
        type: "POST",
        data: {
            action: "update_time_left",
           // username: change_content,
            time_left: time_left,
            id_test: id_test,
            table_test: 'ielts_writing_test_list',

        },
        success: function (response) {
            console.log("Server response:", response);
        },
        error: function (error) {
            console.error("Error updating time_left:", error);
        }
    });
}
    startTest();
}


function startTest() {
    startCountdown(duration);
    document.getElementById("test-prepare").style.display = "none";
    document.getElementById("quiz-container").style.display = 'block';
    document.getElementById("clock-block").style.display = 'block';
    document.getElementById("submit-button").style.display = 'block';
    document.getElementById("navigation-buttons").style.display = 'flex';
    
    document.getElementById("navigation-button").style.display = 'block';

    hideBasicInfo();
    showQuestion(currentQuestionIndex);
}
function doit(index, event) {
    event.preventDefault();  // Explicitly prevent form submission

    var langCode = document.getElementById(`lang-${index}`).value;
    tinyMCE.activeEditor.execCommand("mceWritingImprovementTool", langCode);
}

function hideBasicInfo() {
    document.getElementById("basic-info").style.display = 'none';
}

function showQuestion(index) {
    const questions = document.getElementsByClassName("questions");
    for (let i = 0; i < questions.length; i++) {
        questions[i].style.display = "none";
    }
    questions[index].style.display = "block";

    document.getElementById("prev-button").style.display = index === 0 ? "none" : "inline-block";
    document.getElementById("next-button").style.display = index === questions.length - 1 ? "none" : "inline-block";
}

function showPrevQuestion() {
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        showQuestion(currentQuestionIndex);
    }
}

function showNextQuestion() {
    if (currentQuestionIndex < quizData.questions.length - 1) {
        currentQuestionIndex++;
        showQuestion(currentQuestionIndex);
    }
}

function addEventListenersToInputs() {
    const inputs = document.getElementsByClassName('input');
    for (let i = 0; i < inputs.length; i++) {
        inputs[i].addEventListener('focus', function () {
            this.classList.add('input-focused');
        });
        inputs[i].addEventListener('blur', function () {
            this.classList.remove('input-focused');
        });
    }

    for (let i = 0; i < quizData.questions.length; i++) {
        document.getElementById(`input_handwrite-${i}`).addEventListener('click', () => switchToCanvas(i));
        document.getElementById(`input_normal-${i}`).addEventListener('click', () => switchToTextarea(i));
    }
}



// khai báo biến chạy xuyên suốt code các file js

        //common information test (Length, Paragraph,Sentence)
let length_essay = ``;
let paragraph_essay =``;
let sentence_count ;


let userLevel;
let band_description;
let overallband  = 0;
let task_achievement_comment =``;
let lexical_resource_comment =``;
let grammatical_range_and_accuracy_comment =``;
let coherence_and_cohesion_comment = ``;
let task_achievement_part_1 = 0;
let grammatical_range_and_accuracy_part_1 = 0;
let lexical_resource_part_1 = 0;
let coherence_and_cohesion_part_1 = 0;
        //linking word
let total_linking_word_count;
let linking_word_array_essay = ``;
let linking_word_to_accumulate = ``;
let unique_linking_word_count = ``;

let spelling_grammar_error_essay =``;
let spelling_grammar_error_count;

let point_for_intro_cheking_part_1_essay = ``;
let point_for_second_paragraph_cheking_part_1_essay = ``;


let position_introduction_task_1;
let position_overall_task_1;
let position_body_task_1;

        /* relation point between user essay and question 
            (* If count_common_number > 4) 
                relation_point_essay_and_question = point_for_intro_cheking_part_1_essay
            */
let relation_point_essay_and_question;


        //increase, decrease,...
let increase_word_array = ``;
let increase_word_count = ``;
let unique_increase_word_count;


let decrease_word_array = ``;
let decrease_word_count = ``;
let unique_decrease_word_count;


let unchange_word_array = ``;
let unchange_word_count = ``;
let unique_unchange_word_count;


let goodVerb_word_array = ``;
let goodVerb_word_count = ``;
let unique_goodVerb_word_count = ``;


let well_adjective_and_adverb_word_array = ``;
let well_adjective_and_adverb_word_count = ``;
let unique_well_adjective_and_adverb_word_count = ``;



let adjective_and_adverb_word_array = ``;
let adjective_and_adverb_word_count = ``;
let unique_adjective_and_adverb_word_count = ``;


let simple_sentences_count ;
let complex_sentences_count;
let compound_sentences_count;

let position_simple_sentences;
let position_complex_sentences;
let position_compound_sentences;
let simple_sentences = ``;
let complex_sentences = ``;
let compound_sentences = ``;




        //data checking  - common_numbers cần thiết trong đoạn
let count_common_number;
let type_of_essay; // agree/disagree, bar chart,...


        //structure Overall - Check thứ tự introduction, overall và detail 
let structure_info;

function startCountdown(duration) {
    const countdownElement = document.getElementById("countdown");
    let timer = duration;

    // Adjust for the initial 2-second delay
    timer -= 3;

    countdownInterval = setInterval(function() {
        console.log(`Timer: ${timer}`);

        if (timer > 0) {
            const minutes = Math.floor(timer / 60);
            const seconds = timer % 60;
            countdownElement.innerHTML = `<div style ="display: inline-block"> <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> ${minutes}:${seconds < 10 ? '0' + seconds : seconds} </div>`;
            timer--;
        } else {
            clearInterval(countdownInterval); // Stop the countdown at 0
            preSubmitTest(); // Call submission function
            console.log("Countdown complete!");
        }
    }, 1000);
}



/*
The given table compares different means of transportation in terms of the annual distance traveled by adults in two separate years, 1977 and 2007. Units are measured in miles. Overall, cars were by far the most soar popular method of transport during the entire 40-year period, witnessing the most dramatic rise. In contrast, bicycles and motorcycles were the least common modes of transportation. Regarding changes in commuting patterns, there was an upward trend in the use of cars, trains, and taxis, while the remaining and rise methods of transport recorded a to soar decline. In 1977, cars occupied the position as the to continuing most prevalent vehicle, with 3500 miles traveled, nearly decrease quadruple the distance of the second and third most popular methods, buses and trains, which ranged from 800 to 900 miles. Meanwhile, the distance traveled on foot was 400 miles on average, twice as high as that of plummet taxis. Bicycles wass as common as motorbikes, with the average distancess for each vehicle standing at 100 miles. By 2007, the distance traveled by car had increase twofold to 7100 miles, solidifying its position as the most preferred mode of transportation. Similar changes were seen in the figures for trains and taxis, with the former witnessing a slight growth to 1000 miles and the latter recording a fourfold rise to 800 miles. In contrast, the other transport methods underwent a descending trend, with the most dramatic drop recorded in buses, falling by 300 miles to reach 500 miles in 2007. The distances traveled by walking, motorbikes, and bicycles dropped to 300, 90, and 80 miles, respectively.

*/



    </script>
    



    <script src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/function/description/mark_description.js"></script> 




    <script src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/function/full_overall_chart/full_band_chart.js"></script>
    <script  src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/function/process11.js"></script>
    


    <script  src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/function/right_bar_feature/reload-test.js"></script>
    <script  src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/function/right_bar_feature/fullscreen.js"></script>
    <script  src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/function/right_bar_feature/report-error.js"></script>
    <script  src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/function/right_bar_feature/change-mode.js"></script>


  <script src="http://localhost/wordpress/contents/themes/tutorstarter/ielts-writing-toolkit/submitTest9.js"></script>





   

</body>
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
                    table: 'ielts_writing_test_list',
                    change_token: '$token_need',
                    payment_gate: 'token',
                    type_token: 'token',
                    title: 'Renew test $testname with $id_test (Ielts Writing Test) with $token_need (Buy $time_allow time do this test)',
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