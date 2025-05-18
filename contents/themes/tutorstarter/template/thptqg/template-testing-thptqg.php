<?php
/*
 * Template Name: Doing Template Speaking
 * Template Post Type: thptqg
 
 */


if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();

    $custom_number = get_query_var('id_test');
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;

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


 // Get current time (hour, minute, second)
 $hour = date('H'); // Giờ
 $minute = date('i'); // Phút
 $second = date('s'); // Giây


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


$sql_test = "SELECT * FROM thptqg_question WHERE id_test = ?";


// Query to fetch token details for the current username
$sql2 = "SELECT token, token_use_history 
         FROM user_token 
         WHERE username = ?";



// Use prepared statements to execute the query
$stmt_test = $conn->prepare($sql_test);
$id_test = $custom_number;
$stmt_test->bind_param("s", $id_test);
$stmt_test->execute();

// Get the result
$result_test = $stmt_test->get_result();
if ($result_test->num_rows > 0) {
    $data = $result_test->fetch_assoc();

    $testname = $data['testname']; // Fetch the testname field
    $testcode = $data['testcode']; // Fetch the testname field
    $link_file = $data['link_file']; // Fetch the testname field
    $subject = $data['subject']; // Fetch the testname field
    $year = $data['year']; // Fetch the testname field
    $time = $data['time']; // Fetch the testname field
    $token_need = $data['token_need'];
    $time_allow = $data['time_allow'];
    $permissive_management = $data['permissive_management'];


    add_filter('document_title_parts', function ($title) use ($testname) {
      $title['title'] = $testname; // Use the $testname variable from the outer scope
      return $title;
  });
  

get_header(); // Gọi phần đầu trang (header.php)



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
        















// Output the quizData as JavaScript
echo '<script type="text/javascript">
const questions = 
    ' . $testcode . '
;


console.log(questions);
</script>';



// Đóng kết nối
$conn->close();

?>
<!-- Bản quyền thuộc về HỆ THỐNG GIÁO DỤC Onluyen247-->





<!-- Lưu ý: ĐỌC KĨ TRƯỚC KHI SETUP

    Đây là trang đề thi (Để Làm bài thi)

    Thay lần lượt theo từ khóa " Sửa "

    Tổng thể trang này PHẢI  SỬA 6 LẦN

-->


<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/contents/themes/tutorstarter/dethithptqg_toolkit/style/style3.css">
</head>
<script src ="/contents/themes/tutorstarter/dethithptqg_toolkit/js/toogleCheckbox3.js"></script>
<script src ="/contents/themes/tutorstarter/dethithptqg_toolkit/js/open_fullscreen1.js"></script>
<script src ="/contents/themes/tutorstarter/dethithptqg_toolkit/js/trackingmode5.js"></script> 
<style>

.container1{
  height: 820px;
  /*width: 99%;
  margin-bottom:20px;*/
}

.intro-test{
  height: 5%;
  font-size: 15px;
  text-align:center;
}
.container-test {
  display: flex;
  height: 650px;
  background-color: #ffffff;
}

.content1 {
  box-shadow: 5px 5px 5px 5px #888888;
  height: 100%; /* Replace with the desired height for the content */
  background-color: #ffffff;
  border-radius: 2%;
}
.content2{
  box-shadow: 5px 5px 5px 5px #888888;
  height: 100%; /* Replace with the desired height for the content */
  background-color: #ffffff;
  border-radius: 2%;
  overflow-y: auto;
}
.content3{
  box-shadow: 5px 5px 5px 5px #888888;
  height: 100%; /* Replace with the desired height for the content */
  background-color: #ffffff;
  border-radius: 2%;
  overflow-y: auto;
}
.part1 {
  flex: 0 0 60%;
  height: 100%;
  margin-right:5px
}

.part1-clone {
  display: none;
  flex: 0 0 0%;
  height: 100%;
  margin-right: 5px;
  background-color: #f0f0f0;
}

.part2 {
  flex: 0 0 20%;
  height: 100%;
  background-color:#ffffff;
  /*overflow-y: auto;*/
}


.part3 {
  margin-left: 5px;
   flex: 0 0 20%;
  height: 100%;
  background-color: #ffffff;
  /*overflow-y: auto;*/
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
}
.input-answer {
  margin: 5px;
  width: 80%;
  height: 20px;
}
.true_false{
  display: flex;                /* Sử dụng Flexbox để đặt các phần tử trên cùng một dòng */
  align-items: center;          /* Canh giữa các phần tử theo chiều dọc */
  margin-bottom: 10px;  
}
.true_false  label {
  margin-right: 10px;           /* Thêm khoảng cách giữa label và select */
  font-weight: bold;             /* Làm đậm chữ label */
}
.true_false select {
  padding: 5px;                 /* Thêm padding cho select để nó trông đẹp hơn */
  width: 100px;                 /* Điều chỉnh chiều rộng của select nếu cần */
}

.input-container {
  display: flex;
  align-items: center;
}
.unit-label {
  margin-left: 5px;
  font-size: 14px;
  color: #666;
}

.toggle-active .part1 {
  flex: 0 0 40%;
}

.toggle-active .part1-clone {
  display: block;
  flex: 0 0 40%;
}

.toggle-active .part2 {
  flex: 0 0 20%;
}

.toggle-active .part3 {
 display: none;
}

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

</style>
<body  onload="main()">
<div class = "container1">

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

<div id = "test_screen" style="display: none;">
    <div id ="intro-test" class ="intro-test">
      <b><?php echo htmlspecialchars($testname, ENT_QUOTES, 'UTF-8'); ?></b>
      <button id ="addScreen">Add Screen</button>
    </div>
    <div class="container-test">




    <div class="part1">
       

      <div class="content1">
        <!-- <div id="bottomleft"><p id="time">00:00:00</p></div>  -->
        <div  style="width: 100%; height: 100%; position: relative;">


<!-- src=" <?php echo htmlspecialchars($link_file, ENT_QUOTES, 'UTF-8'); ?> "  -->
          <iframe src=" <?php echo htmlspecialchars($link_file, ENT_QUOTES, 'UTF-8'); ?> "
          id="documentFrame" 
          width="100%" 
          height="100%" 
          allow="fullscreen" 
          frameborder="0" 
          scrolling="no" 
          seamless=""
          sandbox="allow-same-origin allow-scripts allow-popups">
        </iframe>
        
        <!-- Sửa 4: Link Đề thi -->
  </iframe>
  <div style="width: 48px; height: 48px; position: absolute; right: 6px; top: 6px;">
    <!--<img src="https://i.ibb.co/bJNBHXp/guitar-1.png"> -->
  </div>
  </div>



        <!-- Your content for Part 1 goes here -->
    <!--    <iframe src="https://drive.google.com/file/d/1jHgOkoz_6jMzhscvakN-7o3hpOM-TWWK/preview" width="100%" height="100%" allow="autoplay"></iframe> -->


      </div>
    </div>

    <div class="part1-clone">
        <div class="content1">
        <div  style="width: 100%; height: 100%; position: relative;">


          <iframe src=" <?php echo htmlspecialchars($link_file, ENT_QUOTES, 'UTF-8'); ?> "
          id="documentFrame" 
          width="100%" 
          height="100%" 
          allow="fullscreen" 
          frameborder="0" 
          scrolling="no" 
          seamless=""
          sandbox="allow-same-origin allow-scripts allow-popups">
        </iframe>
        
        <!-- Sửa 4: Link Đề thi -->
  </iframe>
  <div style="width: 48px; height: 48px; position: absolute; right: 6px; top: 6px;">
    <!--<img src="https://i.ibb.co/bJNBHXp/guitar-1.png"> -->
  </div>
  </div>



        <!-- Your content for Part 1 goes here -->
    <!--    <iframe src="https://drive.google.com/file/d/1jHgOkoz_6jMzhscvakN-7o3hpOM-TWWK/preview" width="100%" height="100%" allow="autoplay"></iframe> -->


      </div>
      </div>



    <div id="timer"></div>
        <div id="resizableButton"></div>

    <div class="part2">
      <div class="content2">
        
        <!-- Your content for Part 2 goes here -->
    <form id="quiz-form" onsubmit="submitAnswers(event)">
        <div id="questions"></div>
        <div id="loading">
            <div class="spinner"></div>
        <p>Vui lòng đợi trong giây lát....</p>
        
        </div>
        <div id="message-document">
        <p>Bạn cần nộp bài để xem kết quả chi tiết</p>
        </div>
    <br>
  </form>

  
  <div id="result"></div>
</div>
</div>

<div class ="part3"> 
  <div class ="content3"> 
    


<div id ="save-resultform">
    <div id="date" ></div>
    <div id="save-result"></div>
    <div id="timedotest"></div>
    <div id = "name_test"></div>
    <div id = "subject_test"></div>
    <div id = "exam_uuid"></div>

    <div id = "id_test"></div> 
    <div id="finalresult"></div>
    <div id="number_correct_ans"></div>

    <div id="finalresultinput"></div>

</div>
   

    <!-- giấu form send kết quả bài thi -->


                    
                
            <span id="message"></span>     
            <form id="saveUserTHPTQGTest"  style = "display:none">
                <div class="card">
                    <div class="card-header"></div>
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
                        <input type="text"  id="test_type" name="test_type" placeholder="Subject"  class="form-control form_data" />
                        <span id="testname_error" class="text-danger"></span>
                    </div>
                    <div class = "form-group"  >
                        <input type="text" id="timedotestform" name="timedotestform" placeholder="Thời gian làm bài"  class="form-control form_data" />
                        <span id="time_error" class="text-danger"></span>
                    </div>
                    
                    <div class = "form-group"   >
                        <input type="text"  id="band_score_form" name="band_score_form" placeholder="Final Result"  class="form-control form_data" />
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                   
                    <div class = "form-group"   >
                        <textarea type="text"  id="userAnswerSave" name="userAnswerSave" placeholder="User Answer"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>

                    <div class = "form-group"   >
                        <input type="text"  id="number_correct" name="number_correct" placeholder="number correct answer"  class="form-control form_data" />
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div class = "form-group"   >
                        <input type="text"  id="testsavenumber" name="testsavenumber" placeholder="testsavenumber"  class="form-control form_data" />
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    <div class = "form-group"   >
                        <input type="text"  id="incorrect_number" name="incorrect_number" placeholder="incorrect_number"  class="form-control form_data" />
                        <span id="incorrect_number_error" class="text-danger"></span>  
                    </div>
                    <div class = "form-group"   >
                        <input type="text"  id="skip_number" name="skip_number" placeholder="skip_number"  class="form-control form_data" />
                        <span id="skip_number_error" class="text-danger"></span>  
                    </div>
                    <div class = "form-group"   >
                        <input type="text"  id="total_question_number" name="total_question_number" placeholder="total_question_number"  class="form-control form_data" />
                        <span id="total_question_number_error" class="text-danger"></span>  
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








    <div style="display: flex; align-items: center;">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/></svg> 
        <div id="display-time-option" style="margin-left: 8px;"></div>
    </div>
      <p id="time">00:00:00</p>

    <form id="quiz-form" onsubmit="submitAnswers(event)">
      <input id ="myButton" onclick="abort = true" class="submit-button-exam"  type="submit" value="Nộp bài">
    </form>



      
      <button onclick="openFullscreen();">Mở toàn màn hình</button><br>
      <button onclick="closeFullscreen();">Thoát toàn màn hình</button>
        <br>




  <!-- Khung save data -->

      





<!--Hết khung Save Data -->
      
    

      <p style="font-size: 20px;">Danh sách câu hỏi </p>
      <p style ="font-size:14px; color:red; display: none"><i>Ghi chú: Bạn có thể highlight lại câu chưa làm và phân vân bằng cách ấn vào hộp câu hỏi !</i></p>
      <div id="checkboxes"></div>
      




      </div>

    </div>
    </div>               

</div>
</div>
  
<script>


function main(){
    console.log("Passed Main");
    

    setTimeout(function(){
        console.log("Show Test!");
        document.getElementById("start_test").style.display="block";
        
        document.getElementById("welcome").style.display="block";

    }, 0);
    
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
            table_test: 'thptqg_question',

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
var subjectTest = `<?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?>`;        

function startTest(){
  
      countUp();
      setInterval(countUp, 1000);
      
      const optionTimeSet = urlParams.get('option');
        const optionTrackSystem = urlParams.get('optiontrack');

        if (optionTimeSet) {
            option = optionTimeSet; // Override the option if provided in URL
            var timeleft = optionTimeSet / 60 + " phút";
            console.log(`Time left: ${timeleft}`);
        }

        else{
            option = <?php echo htmlspecialchars($time, ENT_QUOTES, 'UTF-8'); ?> * 60;
            var timeleft = option/60 + " phút";
        }

        const initialValue = option;

        // Check if the initial value is greater than 0 before starting the timer
    if (initialValue > 0) {
            startTimer(initialValue, submitFunction);
}

document.getElementById("test-prepare").style.display = "none";
document.getElementById("test_screen").style.display = "block";
  // Display the selected option in the div
document.getElementById('display-time-option').innerText = timeleft ; 






const dateElement = document.getElementById('date');

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


var nametest = document.getElementById('name_test');
var examuuid =  document.getElementById('exam_uuid');
var idtest = document.getElementById('id_test');

idtest.innerHTML = `<?php echo htmlspecialchars($id_test, ENT_QUOTES, 'UTF-8'); ?>`;        
nametest.innerHTML = `<?php echo htmlspecialchars($testname, ENT_QUOTES, 'UTF-8'); ?>`;        

examuuid.innerHTML =  `${resultId}`; 

        
}



  document.getElementById("addScreen").addEventListener("click", function () {
      const containerTest = document.querySelector(".container-test");
      containerTest.classList.toggle("toggle-active");
    });


 // function save data qua ajax
 jQuery('#saveUserTHPTQGTest').submit(function(event) {
    event.preventDefault(); // Prevent the default form submission
    
     var link = "<?php echo admin_url('admin-ajax.php'); ?>";
    
     var form = jQuery('#saveUserTHPTQGTest').serialize();
     var formData = new FormData();
     formData.append('action', 'save_user_result_thptqg');
     formData.append('save_user_result_thptqg', form);
    
     jQuery.ajax({
         url: link,
         data: formData,
         processData: false,
         contentType: false,
         type: 'post',
         success: function(result) {
             jQuery('#submit').attr('disabled', false);
             if (result.success == true) {
                 jQuery('#saveUserTHPTQGTest')[0].reset();
             }
             jQuery('#result_msg').html('<span class="' + result.success + '">' + result.data + '</span>');
         }
     });
    });
    
             //end


// Thông báo xoay màn hình nếu để màn hình điện thoại dọc
  if(window.innerHeight > window.innerWidth){
    alert("Vui lòng xoay ngang màn hình để bài thi hiển thị tốt nhất !");
}






function createQuestionElement(question, isFirstOfPart) {
    const questionElement = document.createElement("div");
    let specificQuestionID = `part_${question.part}_question_${question.question}`;
    questionElement.id = `question-container-number-${specificQuestionID}`;
    questionElement.className = "question-containers";

    // Nếu là câu đầu tiên của part, hiển thị tiêu đề part
    if (isFirstOfPart) {
    const partHeader = document.createElement("div");
    partHeader.className = "part-header";
    partHeader.textContent = `Phần ${question.part}`;

    // Thêm kiểu trực tiếp
    partHeader.style.fontWeight = "bold";
    partHeader.style.fontSize = "20px";
    partHeader.style.textDecoration  = "underline";

    partHeader.style.marginBottom = "10px";
    partHeader.style.color = "#333";

    questionElement.appendChild(partHeader);
}


    // Gắn nội dung câu hỏi
    questionElement.innerHTML += `
        <div id="question-label-container-${specificQuestionID}" class="question-label-container">
            
            <b><span>Câu</span></b>
            <p id="question-${specificQuestionID}" style="margin: 0;"><b>${question.question}</b></p>
            <img id="bookmark-question-${specificQuestionID}" 
                 src="/contents/themes/tutorstarter/dethithptqg_toolkit/assets/bookmark_empty.png" 
                 class="bookmark-btn" 
                 style="cursor: pointer;" 
                 onclick="rememberQuestion('${specificQuestionID}')">
        </div>`;

    // Xử lý câu hỏi dạng "multiple-choice"
    if (question.type === 'multiple-choice') {
        const choices = ['A', 'B', 'C', 'D'];
        for (let i = 0; i < choices.length; i++) {
            const circle = document.createElement("div");
            circle.classList.add("circle");

            const input = document.createElement("input");
            input.type = "radio";
            input.name = `question-${specificQuestionID}`;
            input.value = choices[i];
            input.id = `${specificQuestionID}_${choices[i]}`;

            const label = document.createElement("label");
            label.setAttribute("for", input.id);
            label.textContent = choices[i];

            circle.appendChild(input);
            circle.appendChild(label);
            questionElement.appendChild(circle);
        }
    }

    // Xử lý câu hỏi dạng "completion"
else if (question.type === 'completion') {
    const inputContainer = document.createElement("div");
    inputContainer.className = "input-container";

    const input = document.createElement("input");
    input.type = "text";
    input.className = "input-answer";

    input.name = `question-${specificQuestionID}`;
    input.id = `${specificQuestionID}_completion`;
    inputContainer.appendChild(input);

    // Kiểm tra và thêm đơn vị nếu có
    if (question.don_vi) {
        const unitSpan = document.createElement("span");
        unitSpan.className = "unit-label";
        unitSpan.textContent = ` ${question.don_vi}`;
        inputContainer.appendChild(unitSpan);
    }

    questionElement.appendChild(inputContainer);
}

    else if (question.type === 'true-false') {
    const choices = ['A', 'B', 'C', 'D'];  // Tạo lựa chọn A, B, C, D (có thể thay đổi tùy theo number_choice)
    const numberChoice = parseInt(question.number_choice);  // Số lượng lựa chọn (3, 4, ...)

    for (let i = 0; i < numberChoice; i++) {
        const choiceLetter = choices[i];  // Lấy chữ cái cho lựa chọn (A, B, C, D)

        const true_false = document.createElement("div");
        true_false.classList.add("true_false");

        // Tạo select dropdown cho từng lựa chọn
        const select = document.createElement("select");
        select.id = `${specificQuestionID}_${choiceLetter}`;  // ID cho từng select
        select.name = `question-${specificQuestionID}_${choiceLetter}`;

        // Tạo các option cho select (Đúng/Sai/Chưa chọn)
        const options = ['...', 'Đ', 'S'];  // Đúng, Sai, Chưa chọn
        options.forEach(option => {
            const optionElement = document.createElement("option");
            optionElement.value = option;  // Đặt giá trị cho option (_ cho chưa chọn, Đ cho đúng, S cho sai)
            optionElement.textContent = option === '...' ? ' ' : (option === 'Đ' ? 'Đúng' : 'Sai'); // Hiển thị label cho option
            select.appendChild(optionElement);  // Thêm option vào select
        });

        // Tạo label cho mỗi lựa chọn
        const label = document.createElement("label");
        label.setAttribute("for", select.id);
        label.textContent = choiceLetter;  // Hiển thị chữ cái A, B, C, D

        // Thêm label và select vào div chứa
        true_false.appendChild(label);
        true_false.appendChild(select);
        questionElement.appendChild(true_false);  // Thêm vào phần tử chứa câu hỏi
    }
}


    // Thêm câu hỏi vào container chính
    document.getElementById("questions").appendChild(questionElement);
}



let answerSubmitted = false; // Variable to track if answer has been submitted

function toggleDocument(type) {
  const iframe = document.getElementById('documentFrame');
  const message = document.getElementById('message-document');
  
  if (type === 'default') {
    iframe.src = "https://drive.google.com/file/d/1trIxAoWN_Q_ICi9mlS0ym2CCoBBTO-oM/preview"; //Chú thích: Lại đề thi, bên trên
// Sửa 5: Đề thi (Link giống sửa 4)

  } else if (type === 'new') {
    if (!answerSubmitted) {
      message.style.display = 'block'; // Show the message
      setTimeout(function() {
        message.style.display = 'none'; // Hide the message after a delay
      }, 4000); // Adjust the delay time as needed
      return;
    }
    iframe.src = "https://drive.google.com/file/d/1N9j7VKSZW9q9hsrzdbk1VZaepxEMDRid/preview"; //Sửa 6: Link đáp án chi tiết
  }
}




var elem = document.documentElement;




checktrackingmode();



  // tính thời gian làm bài  
      var hours = 0;
      var minutes = 0;
      var seconds = 0;
      var intervalId;

        function countUp() {
            seconds++;
            if (seconds >= 60) {
                seconds = 0;
                minutes++;
                if (minutes >= 60) {
                    minutes = 0;
                    hours++;
                }
            }
            // add a leading zero if the value is less than 10
            var formattedTime = (hours < 10 ? "0" : "") + hours + ":" +
                                (minutes < 10 ? "0" : "") + minutes + ":" +
                                (seconds < 10 ? "0" : "") + seconds;

            // display the time in the browser
            document.getElementById("time").innerHTML = formattedTime;
        }

           

        function saveTime() {
            var elapsedTime = document.getElementById("time").innerHTML;
            //alert("Time used: " + elapsedTime);
        }

        var incorrectNumber = 0;
        var skipNumber = 0;
        function submitAnswers(event) {
            event.preventDefault();
            var elapsedTime = document.getElementById("time").innerHTML;
            var button = document.getElementById("myButton");
            var loading = document.getElementById("loading");
            var quizContainer = document.getElementById("quiz-form");

            button.style.display = "none";
            loading.style.display = "block";

            answerSubmitted = true;
            setTimeout(function() {
                loading.style.display = "block";
                var quizContainer = document.getElementById("quiz-form");
                var resultElement = document.getElementById("result");
                var resultElementFinal = document.getElementById("finalresult");
                var timedotestFinal = document.getElementById("timedotest");
                let finalRes = document.getElementById("finalresultinput");
                let numberCorrect = document.getElementById('number_correct_ans');

                console.log('Tên subject: ', subjectTest);

                quizContainer.style.display = "none";

                var score = 0;
                var totalScore = 0;
                
                var correctNumberTotal = 0; // Total correct answers for true-false questions
                let SaveResult = {};

                for (let i = 0; i < questions.length; i++) {
                    const question = questions[i];
                    let userAnswer = null;
                    let enteredAnswer = "";
                    let questionScore = 0;
                    let correctNumber = 0; // For true-false questions
                    let isCorrect = false;
                    let isSkipped = false;

                    if (question.type === "multiple-choice") {
                        const specificQuestionID = `part_${question.part}_question_${question.question}`;
                        userAnswer = document.querySelector(`input[name='question-${specificQuestionID}']:checked`);
                        enteredAnswer = userAnswer ? userAnswer.value.toUpperCase() : "";
                        isSkipped = enteredAnswer === "";
                        
                        // Check if answer is correct
                        const correctAnswer = question.answer.toUpperCase();
                        if (enteredAnswer === correctAnswer) {
                            score++;
                            correctNumberTotal++;
                            isCorrect = true;
                            // Calculate score based on subject
                            if (subjectTest === "Địa lý" || subjectTest === "Sinh học" || subjectTest === "Toán học") {
                                questionScore = 0.25;
                            }
                        } else if (!isSkipped) {
                            incorrectNumber++;
                        }
                    } 
                    else if (question.type === "completion") {
                        const specificQuestionID = `part_${question.part}_question_${question.question}_completion`;
                        const inputElement = document.getElementById(specificQuestionID);
                        enteredAnswer = inputElement && inputElement.value ? inputElement.value.toUpperCase() : "";
                        isSkipped = enteredAnswer === "";

                        const validAnswers = Array.isArray(question.answer) 
                            ? question.answer.map(ans => ans.toUpperCase())
                            : [question.answer.toUpperCase()];

                        if (validAnswers.includes(enteredAnswer)) {
                            score++;
                            correctNumberTotal++;
                            isCorrect = true;
                            // Calculate score based on subject
                            if (subjectTest === "Địa lý" || subjectTest === "Sinh học") {
                                questionScore = 0.25;
                            } else if (subjectTest === "Toán học") {
                                questionScore = 0.5;
                            }
                        } else if (!isSkipped) {
                            incorrectNumber++;
                        }
                    }
                    else if (question.type === "true-false") {
                        const selectedAnswers = [];
                        const correctAnswers = question.answer.split('/');
                        const choices = ['A', 'B', 'C', 'D'];
                        const specificQuestionID = `part_${question.part}_question_${question.question}`;
                        isSkipped = true; // Assume skipped until we find an answer

                        for (let i = 0; i < question.number_choice; i++) {
                            const selectElement = document.getElementById(`${specificQuestionID}_${choices[i]}`);
                            if (selectElement && selectElement.value) {
                                isSkipped = false;
                                selectedAnswers.push(selectElement.value);
                                // Compare each answer with correct answer
                                if (selectElement.value === correctAnswers[i]) {
                                    correctNumber++;
                                }
                            }
                        }

                        enteredAnswer = selectedAnswers.join("/");

                        // Calculate score based on correctNumber
                        if (correctNumber > 0) {
                            score++;
                            correctNumberTotal += correctNumber;
                            isCorrect = true;
                            if (correctNumber === 1) {
                                questionScore = 0.1;
                            } else if (correctNumber === 2) {
                                questionScore = 0.25;
                            } else if (correctNumber === 3) {
                                questionScore = 0.5;
                            } else if (correctNumber === 4) {
                                questionScore = 1;
                            }
                        } else if (!isSkipped) {
                            incorrectNumber++;
                        }
                    }

                    if (isSkipped) {
                        skipNumber++;
                    }

                    const part = question.part || '0';

                    if (!SaveResult[part]) {
                        SaveResult[part] = [];
                    }

                    const specificQuestionID = `part_${question.part}_question_${question.question}`;

                    // Add question to SaveResult with type and score
                    const questionResult = {
                        id: specificQuestionID,
                        answer: enteredAnswer,
                        type: question.type,
                        score: questionScore.toFixed(2),
                        status: isCorrect ? "correct" : (isSkipped ? "skipped" : "incorrect")
                    };

                    // Add correct_number for true-false questions
                    if (question.type === "true-false") {
                        questionResult.correct_number = correctNumber.toString();
                    }

                    SaveResult[part].push(questionResult);
                    totalScore += questionScore;

                    // Create and display the answer textarea
                    const textarea = document.createElement("textarea");
                    textarea.setAttribute("readonly", "true");
                    textarea.classList.add("answer-textarea");
                    textarea.value = `${question.question}\nCâu trả lời: ${enteredAnswer}\nLoại câu hỏi: ${question.type}`;
                    
                    if (question.type === "true-false") {
                        textarea.value += `\nSố đáp án đúng: ${correctNumber}`;
                    }
                    textarea.value += `\nĐiểm: ${questionScore.toFixed(2)}`;
                    textarea.value += `\nTrạng thái: ${isCorrect ? "Đúng" : (isSkipped ? "Bỏ qua" : "Sai")}`;

                    if (isCorrect) {
                        textarea.classList.add("correct-answer");
                    } else if (isSkipped) {
                        textarea.classList.add("skipped-answer");
                    } else {
                        textarea.classList.add("wrong-answer");
                        if (question.type === "completion") {
                            const validAnswers = Array.isArray(question.answer) 
                                ? question.answer.map(ans => ans.toUpperCase())
                                : [question.answer.toUpperCase()];
                            textarea.value += `\nĐáp án đúng: ${validAnswers.join(", ")}`;
                        } else if (question.type === "multiple-choice") {
                            textarea.value += `\nĐáp án đúng: ${question.answer.toUpperCase()}`;
                        } else if (question.type === "true-false") {
                            textarea.value += `\nĐáp án đúng: ${question.answer}`;
                        }
                    }

                    resultElement.appendChild(textarea);
                }

                document.getElementById("save-result").innerText = JSON.stringify(SaveResult, null, 2);

                // Calculate final score (scaled to 10)
                let finalscore = parseFloat((totalScore - luotthoat * 0.25)).toFixed(2);
                // Ensure score doesn't go below 0
                finalscore = Math.max(0, finalscore);
                finalRes.innerHTML = `${finalscore}`;

                let trudiemluotthoat = luotthoat * 0.25;
                resultElementFinal.innerHTML = `<p><br> <h2> Tổng kết bài làm </h2><br> 
                - Thời gian làm bài: ${elapsedTime}<br> 
                - Số câu làm đúng: ${score}/${questions.length}<br>
                - Số câu làm sai: ${incorrectNumber}<br>
                - Số câu bỏ qua: ${skipNumber}<br>
                - Điểm bài làm: ${finalscore} /10 <br>
                - Số lượt thoát khỏi màn hình khi bài làm là: ${luotthoat} (- ${trudiemluotthoat} điểm) </p>`;

                resultElementFinal.insertAdjacentHTML("beforeend", `<p><br> <h2> Điểm : ${finalscore} /10  </p>`);
                resultElement.insertAdjacentHTML("beforeend", `<p>${finalscore} </p>`);

                timedotestFinal.innerHTML = `${elapsedTime}`;
                numberCorrect.innerHTML = `${score}`;
                document.getElementById("total_question_number").value = `${questions.length}`;

                ResultInput();

            }, 100);
        }



function ResultInput(){
    var contentToCopy = document.getElementById("date").textContent;
    var contentToCopy2 = document.getElementById("id_test").textContent;
    var contentToCopy3 = document.getElementById("name_test").textContent;
    var contentToCopy6 = document.getElementById("timedotest").textContent;
    var contentToCopy7 = document.getElementById("finalresultinput").textContent;
    var contentToCopy8 = document.getElementById("save-result").textContent;
    var contentToCopy9 = document.getElementById("number_correct_ans").textContent;
    var contentToCopy10 = document.getElementById("exam_uuid").textContent;


    document.getElementById("dateform").value = contentToCopy;
    document.getElementById("idtest").value = contentToCopy2;
    document.getElementById("testname").value = contentToCopy3;
    document.getElementById("test_type").value = subjectTest;
    document.getElementById("timedotestform").value = contentToCopy6;
    document.getElementById("band_score_form").value = contentToCopy7;
    document.getElementById("userAnswerSave").value = contentToCopy8;
    document.getElementById("number_correct").value = contentToCopy9;


    document.getElementById("incorrect_number").value = incorrectNumber;
    document.getElementById("skip_number").value = skipNumber;
    

    document.getElementById("testsavenumber").value = resultId;


}

    function getUserAnswer(question) {
      
      return document.getElementById(question).value.trim();
    }

 
// Hàm khởi tạo
function init() {
    let previousPart = null;
    for (let i = 0; i < questions.length; i++) {
       const currentPart = questions[i].part;
       let specificQuestionID =   `part_${questions[i].part}_question_${questions[i].question}`;

        const isFirstOfPart = currentPart !== previousPart;
        createQuestionElement(questions[i], isFirstOfPart);
        createCheckboxElement(questions[i].question, specificQuestionID); // Tạo checkbox tương ứng
        previousPart = currentPart;
    }
}

init();


function startTimer(durationInSeconds, submitFunction) {
    let seconds = durationInSeconds;

    const countdown = setInterval(function() {
        const minutes = Math.floor(seconds / 60);
        let remainingSeconds = seconds % 60;

        remainingSeconds = remainingSeconds < 10 ? '0' + remainingSeconds : remainingSeconds;


        if (seconds === 0) {
            clearInterval(countdown);
            //submitAnswers(event);
            // Disable input elements (checkboxes) when timer reaches 0

            disableInputElements();
        } else {
            seconds--;
        }
    }, 1000);
}






function disableInputElements() {
    const inputs = document.querySelectorAll('input[type="radio"]');
    inputs.forEach(input => {
        input.disabled = true;
    });
}


function submitFunction() {
    alert('Time is up!'); // Placeholder for your actual submit function
  }

      


    



</script>
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
                    table: 'thptqg_question',
                    change_token: '$token_need',
                    payment_gate: 'token',
                    type_token: 'token',
                    title: 'Renew test $testname with $id_test (THPTQG) with $token_need (Buy $time_allow time do this test)',
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