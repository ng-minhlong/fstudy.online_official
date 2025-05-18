<?php
/*
 * Template Name: Doing Template Speaking
 * Template Post Type: ieltsspeakingtests
 
 */


if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();

    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;
    // Lấy giá trị custom number từ custom field
   
// Get the custom number field value
    //$custom_number = get_post_meta($post_id, '_ieltsspeakingtests_custom_number', true);
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

 // Generate random two-digit number
 $random_number = rand(10, 99);
 // Handle user_id and id_test error, set to "00" if invalid
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
$sql_test = "SELECT *  FROM ielts_speaking_test_list WHERE id_test = ?";
$stmt_test = $conn->prepare($sql_test);

$stmt_test->bind_param("s", $custom_number);
$stmt_test->execute();
$result_test = $stmt_test->get_result();




// Query to fetch token details for the current username
$sql_user = "SELECT token, token_use_history 
         FROM user_token 
         WHERE username = ?";

if ($result_test->num_rows > 0) {
    // Lấy các ID từ question_choose (ví dụ: "1001,2001,3001")
    $data = $result_test->fetch_assoc();
    $testname = $data['testname'];
    $token_need = $data['token_need'];
    $time_allow = $data['time_allow'];
    $permissive_management = $data['permissive_management'];
    $question_choose = explode(',', $data['question_choose']);    
    $test_type = $data['test_type'];



    add_filter('document_title_parts', function ($title) use ($testname) {
        $title['title'] = $testname; // Use the $testname variable from the outer scope
        return $title;
    });
    get_header();
    $stmt_user = $conn->prepare($sql_user);
    if (!$stmt_user) {
        die("Error preparing statement 2: " . $conn->error);
    }

    $stmt_user->bind_param("s", $current_username);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $token_data = $result_user->fetch_assoc();
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
            






// Prepare an array to store the questions
$questions = [];
$topic = ''; // Variable to store the topic

// Fetch speaking_part 1 questions based on question_choose
if (!empty($question_choose)) {
    $placeholders = implode(',', array_fill(0, count($question_choose), '?'));
    $sql1 = "SELECT id_test, speaking_part, topic, stt, question_content, sample, important_add 
              FROM ielts_speaking_part_1_question WHERE id_test IN ($placeholders)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param(str_repeat("i", count($question_choose)), ...$question_choose); // Bind as integers
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    while ($row = $result1->fetch_assoc()) {
        $questions[] = [
            "question" => addslashes($row['question_content']),
            "part" => $row['speaking_part'],
            "stt" => $row['stt'],
            "topic" => $row['topic'],
            "id" => $row['id_test'],
            "sample" => $row['sample']

        ];
    }

    // Fetch speaking_part 2 questions based on question_choose
    $sql2 = "SELECT id_test, speaking_part, topic, question_content, sample 
              FROM ielts_speaking_part_2_question WHERE id_test IN ($placeholders)";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param(str_repeat("i", count($question_choose)), ...$question_choose); // Bind as integers
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    while ($row = $result2->fetch_assoc()) {
        $questions[] = [
            "question" => addslashes($row['question_content']),
            "part" => $row['speaking_part'],
            "id" => $row['id_test'],
            "stt" => 1,
            "topic" => $row['topic'],
            "sample" => $row['sample']
        ];
    }


    // Fetch speaking_part 2 questions based on question_choose
    $sql3 = "SELECT id_test, speaking_part, topic, stt, question_content, sample 
              FROM ielts_speaking_part_3_question WHERE id_test IN ($placeholders)";
    $stmt3 = $conn->prepare($sql3);
    $stmt3->bind_param(str_repeat("i", count($question_choose)), ...$question_choose); // Bind as integers
    $stmt3->execute();
    $result3 = $stmt3->get_result();

    while ($row = $result3->fetch_assoc()) {
        $questions[] = [
            "question" => addslashes($row['question_content']),
            "part" => $row['speaking_part'],
            "stt" => $row['stt'],
            "id" => $row['id_test'],
            "topic" => $row['topic'],
            "sample" => $row['sample']
        ];

        // Capture the topic (assuming the same topic for all rows)
        if (!$topic) {
            $topic = $row['topic'];
        }
    }
}



// Output the quizData as JavaScript
echo '<script type="text/javascript">
const quizData = {
    "title": "' . htmlspecialchars($testname) . '",
    "testtype": "' . htmlspecialchars($test_type) . '",
    "questions": ' . json_encode($questions) . '
};
console.log(quizData);
</script>';



// Close the database connection
$conn->close();

    ?>
    
              


<html lang="en" dir="ltr">

<head>
     
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=qt48zGVy"></script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Speaking Ielts Simulation</title>
    <script src="/contents/themes/tutorstarter/scan-device/system_check_.js"></script>
    <script src="/contents/themes/tutorstarter/scan-device/location_ip_.js"></script>
    <script src="/contents/themes/tutorstarter/scan-device/browser_check.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const systemInfo = {
            os: getOS(),
            browser: checkBrowser(),
            network: checkLocationAndIpAddress(),
            timestamp: new Date().toISOString()
        };
        
        console.log('ℹ️ Tổng hợp thông tin hệ thống:', systemInfo);
        // Có thể gửi systemInfo này về server nếu cần
    });
    </script>

    <link rel="stylesheet" href="\contents\themes\tutorstarter\ielts-speaking-toolkit\style\style1.css">

    <style>
        .browser-warning {
            text-align: center;
            padding: 20px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 8px;
            max-width: 600px;
            margin: 50px auto;
            color: #856404;
            }
            
            .browser-warning h2 {
            color: #856404;
            margin-top: 0;
            }
            
            .supported-browsers {
            margin-top: 15px;
            text-align: left;
            display: inline-block;
            }
        .parent-container {
            display: flex; /* Use Flexbox */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
        }
        .video-container-2 {
            border: 1px solid rgb(73, 61, 61);

            border-radius: 10%;
            width: 40%;   
            overflow: hidden; /* Ensure video does not overflow container */
        }
        .video-container-intro{
         
            overflow: hidden; /* Ensure video does not overflow container */
        }
        

        .video-container-2 video {

            width: 100%; /* Make the video responsive within its container */
            height: 100%;
            display: block;
        }
        .submit-modify-answer{    
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            border: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .main-button:disabled {
            background-color: #888;  /* màu xám nhạt */
            color: #ccc;
            cursor: not-allowed;
            opacity: 0.6;
        }
        

        
   /*Ẩn footer mặc định */
   #colophon.site-footer { display: none !important; }

    </style>
    <style>
        .overlay{
              background-image: radial-gradient(circle farthest-corner at center, #3C4B57 0%, #1C262B 100%);
        }
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(63 75 75 / 60%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
                
        .loader {
        position: absolute;
        top: calc(30%);
        left: calc(50% - 32px);
        width: 100px;
        height: 100px;
        border-radius: 50%;
        perspective: 800px;
    }

        .inner {
            position: absolute;
            box-sizing: border-box;
            width: 100%;
            height: 100%;
            border-radius: 50%;  
        }

        .inner.one {
            left: 0%;
            top: 0%;
            animation: rotate-one 1s linear infinite;
            border-bottom: 3px solid #EFEFFA;
        }

        .inner.two {
            right: 0%;
            top: 0%;
            animation: rotate-two 1s linear infinite;
            border-right: 3px solid #EFEFFA;
        }

        .inner.three {
            right: 0%;
            bottom: 0%;
            animation: rotate-three 1s linear infinite;
            border-top: 3px solid #EFEFFA;
        }

        @keyframes rotate-one {
            0% {
                transform: rotateX(35deg) rotateY(-45deg) rotateZ(0deg);
            }
            100% {
                transform: rotateX(35deg) rotateY(-45deg) rotateZ(360deg);
            }
        }

        @keyframes rotate-two {
            0% {
                transform: rotateX(50deg) rotateY(10deg) rotateZ(0deg);
            }
            100% {
                transform: rotateX(50deg) rotateY(10deg) rotateZ(360deg);
            }
        }

        @keyframes rotate-three {
            0% {
                transform: rotateX(35deg) rotateY(55deg) rotateZ(0deg);
            }
            100% {
                transform: rotateX(35deg) rotateY(55deg) rotateZ(360deg);
            }
        }
        .loader-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .loading-text {
            text-align: center;
            margin-top: 15px;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }

    </style>
    
</head>

<body onload ="main()">
    

<!-- Thêm div thông báo trình duyệt không hỗ trợ (ban đầu ẩn đi) -->
<div id="browserWarning" class="browser-warning" style="display: none;">
  <h2>Trình duyệt không được hỗ trợ</h2>
  <p>Xin lỗi, hệ thống của chúng tôi hiện không hỗ trợ trình duyệt Opera.</p>
  <p>Vui lòng sử dụng một trong các trình duyệt sau để tiếp tục:</p>
  
  <div class="supported-browsers">
    <p>✓ Google Chrome</p>
    <p>✓ Microsoft Edge</p>
    <p>✓ Mozilla Firefox</p>
    <p>✓ Safari (trên Mac/iOS)</p>
    <p>✓ Cốc Cốc</p>
  </div>
</div>




    <div class="container1" id ="container1"  style="display: none;">
       

     <!-- Add "Get Started" button in the right column -->
        <div class ="getstartpage" id ="column0">
            <div class="column0" > 
                <div class="video-container-intro">
                    <video id="examinerVideo_homepage" class="video-background" autoplay playsinline style="pointer-events: none;">
                        <source src="\contents\themes\tutorstarter\ielts-speaking-toolkit\examiner.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <p > <b>Chọn chế độ làm bài thi</b></p>
                <select id="exam-option">
                    <option value="practice">Chế độ luyện tập</option>
                    <option value="real-test">Chế độ phòng thi</option>                
                  </select>

                  <p><b>Chế độ phòng thi: </b><br> - Sau 3 giây sẽ tự động ghi âm từng câu hỏi<br> - Không cho phép trả lời lại</p><br>
                  <p><b>Chế độ luyện tập: </b><br> - Cho phép thí sinh suy nghĩ trước mỗi câu hỏi rồi ghi âm<br> - Cho phép thí sinh trả lời lại</p>
                  
                  
                
                <p > <b>Thiết lập giám khảo</b></p>
                <button id ="change_examiner"> Change examiner</button>
                <button id ="change_examiner_voice"> Change examiner's voice</button>
                <button id="change_examiner_speed">Change examiner's voice speed</button>
                <div id="data-save-full-speaking" ></div>


             </div>

            <div  class="column0" > 
                
                <div id ="intro_test">
                    <h3 id="title"></h3>
                    <div id="id_test"></div>
                    <div id="testtype"></div>
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



                     <h2  style="font: bold;" id ="title"></h2>
                     <h3>Hướng dẫn</h3>
                     <p>     Trước hết, bạn cần cho phép truy cập microphone xuyên suốt quá trình làm bài để không ảnh hưởng đến kết quả</p>
                     <p>     Sau đó, bên dưới có phần mic check, hãy kiểm tra mic của bạn bằng cách ấn RECORD rồi STOP để nghe đoạn ghi âm</p>
                     <p>     Tại mỗi câu hỏi, sau khi examiner đọc xong câu hỏi, hãy nhấn nút START RECORD để bắt đầu ghi âm. Nếu bạn trả lời xong, hãy ấn SUBMIT ANSWER để chuyển sang câu tiếp theo</p>
                     <p>     Bạn có thể yêu cầu examiner đọc lại câu hỏi/ nhắc lại câu hỏi bằng cách ấn nút COMMAND GUILDLINE bên dưới examiner </p>
                     
                     
                     <div id="list-question">
                        <h2  style="font: bold;">List question</h2>
                        <button id ="show-list-popup-btn">See question</button>
                    </div>
                    <div id= "show-list-popup" class="popup">
                        <div class="practice-pronunciation-content">
                    
                            <span class="close" onclick="closeListQuestion()">&times;</span>
                             <h2>Danh sách câu hỏi</h2>
                             <div id="question-list"></div>
                        </div>
                     </div>




                     <div id="mic-check" >

                         <h2 style="font: bold;"> Mic check</h2>
                         <p> Before starting exam, you can check your mic </p>
                         <audio id='audioPlayer'></audio>
                         <button class ="recorder">Record</button>
                         <button class="stopRecorder" >Stop</button>
                         
                         

                     </div>


                     <button id="getStartedButton" onclick="prestartTest()">Get Started</button>
                </div>
             </div>


        </div>


    <div id ="virtual-room">
            <div class="column1 " id="column1">
                <h2 id ="speaking-part"></h2>

            <div class="parent-container">
                <div class="video-container-2">
                    <video id="examinerVideo" class="video-background" autoplay playsinline style="pointer-events: none;">
                        <source src="\contents\themes\tutorstarter\ielts-speaking-toolkit\examiner.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>

            


             <!--  <button id ="hide_show_question" onclick="toggle_question()">Show/Hide question</button>-->
                <p id="question" class="question"></p>

                <div class="button-container">

                    <button class="main-button" id="startButton" onclick="startRecording()">Start Record</button>
                    <button class="main-button" id="stopButton" disabled onclick="stopRecording()">Submit Answer</button>
                    <button class="main-button" id="reAnswerButton"  onclick="reAnswerQuestion()">Reanswer question</button>
                    
                </div>
                <div class="timer-display-id">
                    <p id="timer">00:00:00 </p>
                </div>
                <br>
                <textarea id="answerTextarea"  class="answerTextarea" ></textarea>
               
                

                <div id ="check-answer"></div>
                <div id ="set-time-to-record" style="display: none;">Recording ... You should say now !</div>


                <div id ="confidence-level"></div>



                <button id="submitButton" onclick="endTest()" style="display: none;">Submit</button>




                
                
            </div>
    </div>
    <div id="loadingOverlay" style="display: none;">
        <div class="loader-wrapper">
            <div class="loader">
                <div class="inner one"></div>
                <div class="inner two"></div>
                <div class="inner three"></div>
            </div>
            <p class="loading-text">Đã ghi nhận câu trả lời của bạn, hệ thống đang chuẩn bị câu tiếp theo</p>
        </div>
    </div>



    <div id="review-page" style="display: none">
        <b>Review Page</b>
        <div id="preface">
            <i>Due to user's confusing pronunciation, some user's answer may lack of meaning. You now have another chance to make sure your answer correctly. </i>
            <p>We provide this feature and we want your band show accurately because some other criterias may affect. </p>
            <p style="color: red">Important Note: In real test, you will not have any chance to edit your answer </p>
        </div>
        <div id="edit-ans-area"></div>
        <button id="log-edited-answer" class = "submit-modify-answer">Submit Test and Get Result</button>
        <button style = "display:none " id="log-original-answer">Log Original Answer with Edits</button>
        <button style = "display:none " i id="log-edited-words">Log All Edited Words</button>

    </div>


    <!-- Popup sửa từ -->
    <div id="word-edit-popup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
        background: white; padding: 10px; border: 2px solid black; z-index: 1000;">
        <p>Edit Word:</p>
        <input type="text" id="edit-word-input">
        <button id="save-word-btn">Save</button>
        <button id="cancel-word-btn">Cancel</button>
    </div>


    
    </div>
    <div id="practice-pronunciation-popup" class="popup">
        <div class="practice-pronunciation-content">
    
            <span class="close" onclick="closePracticePronunciation()">&times;</span>
            <h2>Các từ/ cụm từ bạn đọc chưa chính xác</h2>
            <div id="pronunciation-list"></div>
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

            <div id ="final-result"></div>
            <div id ="breakdown"></div>
            <div id ="band-decriptor"></div>
            <h3>General Comment</h3>
            <div id ="general-comment"></div>

        </div>
        <div id ="result-page-tabs-2" class="tabcontent">
            <button id ="btn-show-practice-pronunciation" style="display: none;">Practice to improve your pronunciation</button>
            <div id ="result-tab-your-answer">
                <div id="recordingsList"class="column0"></div>
                <div id="resultColumn"class="column0"></div>
            </div>
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
                <form id="saveUserResultTest" >
                        <div class="card">
                            <div class="card-header">Form lưu kết quả</div>
                            <div class="card-body" >

                    <div class = "form-group" >
                        <textarea   type="text" id="resulttest" name="resulttest" placeholder="Kết quả"  class="form-control form_data" ></textarea>
                        <span id="result_error" class="text-danger" ></span>

                    </div>


                    <div class = "form-group">
                        <textarea type="text" id="dateform" name="dateform" placeholder="Ngày"  class="form-control form_data" ></textarea>
                        <span id="date_error" class="text-danger" ></span>
                    </div>

                    

                    <div class = "form-group" >
                        <textarea type="text" id="idtest" name="idtest" placeholder="Id test"  class="form-control form_data"></textarea>
                        <span id="idtest_error" class="text-danger" ></span>
                    </div>

                 

                    <div class = "form-group"   >
                        <textarea type="text"  id="testname" name="testname" placeholder="Test Name"  class="form-control form_data"></textarea>
                        <span id="testname_error" class="text-danger"></span>
                    </div>


                    <div class = "form-group"   >
                        <textarea type="text"  id="band_detail" name="band_detail" placeholder="Band Detail"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>
                    
                    <div class = "form-group"   >
                        <textarea type="text"  id="test_type" name="test_type" placeholder="Test Type"  class="form-control form_data" ></textarea>
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>

                    <div class = "form-group"   >
                        <textarea type="text"  id="user_answer_and_comment" name="user_answer_and_comment" placeholder="Data Save Speaking"  class="form-control form_data"></textarea>
                        <span id="useranswer_error" class="text-danger"></span>
                    </div>
                     <div class = "form-group"   >
                        <textarea type="text"  id="testsavenumber" name="testsavenumber" placeholder="testsavenumber"  class="form-control form_data"></textarea>
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
    
    <!---
    <div id = "ads-container">
        <p style="font-weight: bold;">This is place for banner ( advertisement)____</p>
         <p style="color: red;font-weight: bold;font-style: italic;">____DEV TAG: POWERED BY NGUYEN MINH LONG</p>
    </div>-->

    <script>
        

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






    let os = null; 
    
    document.addEventListener('DOMContentLoaded', function () {
        getOS();
        checkLocationAndIpAddress();   
            
    // Get the video element
    const videoElement = document.getElementById('examinerVideo');

    // Get the button to change the examiner video
    const changeExaminerButton = document.getElementById('change_examiner');

    // Array of video options
    const videoOptions = [
        { name: 'Examiner 1', src: '\contents\themes\tutorstarter\ielts-speaking-toolkit\examiner.mp4' },
        { name: 'Examiner 2', src: '\contents\themes\tutorstarter\ielts-speaking-toolkit\examiner1.mp4' },
        { name: 'Examiner 3', src: '\contents\themes\tutorstarter\ielts-speaking-toolkit\examiner2.mp4' }
    ];


    // Create the dropdown menu
    const dropdownMenu = document.createElement('select');
    dropdownMenu.setAttribute('id', 'examinerOptions');

    // Populate the dropdown menu with options
    videoOptions.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.textContent = option.name;
        optionElement.setAttribute('value', option.src);
        dropdownMenu.appendChild(optionElement);
    });

    // Add event listener to the button
    changeExaminerButton.addEventListener('click', function () {
        // Replace the button with the dropdown menu
        changeExaminerButton.parentNode.replaceChild(dropdownMenu, changeExaminerButton);
    });

    // Add event listener to the dropdown menu
    dropdownMenu.addEventListener('change', function () {
        // Change the source of the video based on the selected option
        videoElement.src = this.value;
    });

            // Speed selection feature

     const changeSpeedButton = document.getElementById('change_examiner_speed');
            const speedOptions = [1, 1.5, 2, 3, 5];

            // Create the speed dropdown menu
            const speedDropdownMenu = document.createElement('select');
            speedDropdownMenu.setAttribute('id', 'speedOptions');

            // Populate the speed dropdown menu with options
            speedOptions.forEach(speed => {
                const speedOptionElement = document.createElement('option');
                speedOptionElement.textContent = speed;
                speedOptionElement.setAttribute('value', speed);
                speedDropdownMenu.appendChild(speedOptionElement);
            });

            // Add event listener to the speed button
            changeSpeedButton.addEventListener('click', function () {
                // Replace the button with the speed dropdown menu
                changeSpeedButton.parentNode.replaceChild(speedDropdownMenu, changeSpeedButton);
            });


});


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
            table_test: 'ielts_speaking_test_list',

        },
        success: function (response) {
            console.log("Server response:", response);
        },
        error: function (error) {
            console.error("Error updating time_left:", error);
        }
    });
}
initializeTest();
}


    function initializeTest(){
        let timerInterval;
            Swal.fire({
            title: "Đang thiết lập phòng speaking cho bạn",
            html: "Vui lòng đợi trong giây lát.",
            timer: 1000,
            allowOutsideClick: false,

            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
            }).then((result) => {
            /* Read more about handling dismissals below */
            showContent();
            });
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
        

    function endTest(){
        let timerInterval;
            Swal.fire({
            title: "Đang nộp bài cho thí sinh",
            html: "Chúc mừng bạn đã hoàn thành bài thi. Vui lòng đợi trong giây lát.",
            timer: 1000,
            allowOutsideClick: false,

            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            }
            }).then((result) => {
            /* Read more about handling dismissals below */
            submitAnswers();
            });
    }

    
        function showContent() {
        
            ChooseExamOption();
            //mediaRecorderCheck.stop();
           // document.getElementById("ads-container").style.display = "none";
            document.getElementById('column1').style.display = 'block';
            document.getElementById ('column0').style.display = 'none';
            loadQuestion();

        }



        function ChooseExamOption(){
           
            var x = document.getElementById("exam-option").value;
            if(x == "real-test"){
                document.getElementById("startButton").style.display="none";
                document.getElementById("reAnswerButton").style.display ="none";
                
            setInterval(function(){
                
                document.getElementById("stopButton").disabled=false;
                startRecording1();
                
                startRecording();
                console.log("You can start right now !!!");
                document.getElementById("set-time-to-record").style.display="block";
                document.getElementById('submitButton').disabled = false;
                
            },7000);

            }
        }



        function toggle_question(){

            var x = document.getElementById("question");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }

        }


// popup open list question
document.getElementById("show-list-popup-btn").addEventListener('click', openListQuestion);
        
        // Close the draft popup when the close button is clicked
        function closeListQuestion() {
            document.getElementById('show-list-popup').style.display = 'none';
        }

        function openListQuestion() {
            document.getElementById('show-list-popup').style.display = 'block';
          
        }


let pre_id_test_ = `<?php echo esc_html($custom_number);?>`;
        console.log(`${pre_id_test_}`)
     
 



// function save data qua ajax


jQuery('#saveUserResultTest').submit(function(event) {
event.preventDefault(); // Prevent the default form submission

 var link = "<?php echo admin_url('admin-ajax.php'); ?>";

 var form = jQuery('#saveUserResultTest').serialize();
 var formData = new FormData();
 formData.append('action', 'save_user_result_ielts_speaking');
 formData.append('save_user_result_ielts_speaking', form);

 jQuery.ajax({
     url: link,
     data: formData,
     processData: false,
     contentType: false,
     type: 'post',
     success: function(result) {
         jQuery('#submit').attr('disabled', false);
         if (result.success == true) {
             jQuery('#saveUserResultTest')[0].reset();
         }
         jQuery('#result_msg').html('<span class="' + result.success + '">' + result.data + '</span>');
     }
 });
});

         //end

document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("submitForm", function () {
        setTimeout(function () {
            let form = document.getElementById("saveUserResultTest");
            form.submit(); // This should work now that there's no conflict
        }, 2000); 
    });
});


async function uploadRecording(blob) {
    const formData = new FormData();
    formData.append('file', blob, 'recording.mp3');
    //formData.append('upload_preset', 'ccgbws2m');  // Replace with your preset name

    //const response = await fetch('https://uploaddrive-api.onrender.com/upload', {
        
    const response = await fetch(`${siteUrl}/api/v1/audio/service/upload`, {

    //const response = await fetch('https://api.cloudinary.com/v1_1/dloq2wl7k/video/upload', {  // Replace with your cloud name
    //const response = await fetch('https://api.com/v1_1/dloq2wl7k/upload', {  // Replace with your cloud name

        method: 'POST',
        body: formData,
    });

    const result = await response.json();

    // Check if the upload was successful
    /*if (result.secure_url) {
        return result.secure_url; // Return the URL of the uploaded audio file
    } else {
        console.error('Upload failed:', result);
        return null;
    }*/
    if (result.link) {
        return result.link; // Trả về link file trên Google Drive
    } else {
        console.error('Upload failed:', result);
        return null;
    }
}


        let part1Count = 0;
        let part2Count = 0;
        let part3Count = 0;
        let unknownPartCount = 0;

        // Iterate through each question and count based on the 'part' property
        quizData.questions.forEach((question) => {
        switch (question.part) {
            case 1:
            part1Count++;
            break;
            case 2:
            part2Count++;
            break;
            case 3:
            part3Count++;
            break;
            default:
            unknownPartCount++;
            break;
        }
        });
        let number_of_question = 0;
        for (let i = 1; i <= quizData.questions.length; i++){
            number_of_question ++;
        }



        document.getElementById("title").innerHTML = quizData.title;
        document.getElementById("testtype").innerHTML = quizData.testtype;
        let recognition;
        let textarea = document.getElementById('answerTextarea');
        let questionElement = document.getElementById('question');
        let currentQuestionIndex = 0;
        let answers = {};
        let counters = {};
        let mediaRecorder;
        let interval;

        let recordedChunks = [];
        let recordingsList = [];
        let numRecordings = 0;

        
        let editCounts = {}; // Đếm số lần chỉnh sửa cho mỗi câu trả lời

        let audioChunks = [];
        let recordingInterval;
        

       
        let pronunciation_words_list = {};

        let isAnswerSubmitted = false;

document.addEventListener('DOMContentLoaded', function () {
    
    const videoElement = document.getElementById('examinerVideo');

    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.ondataavailable = event => {
                recordedChunks.push(event.data);
            };
            let answer = textarea.value.trim();

            answers['answer' + (currentQuestionIndex + 1)] = answer;
            //counters['counter' + (currentQuestionIndex + 1)] = counter;
            if (answer == "can you repeat question"){
             
             speakText(questionElement.textContent);
             //console.log("Answer " + (currentQuestionIndex + 1) + ": " + answer);

         }  
         else{
                
                if (mediaRecorder.state === 'recording') {
                mediaRecorder.stop(); // Ensure the recorder is stopped
            }
       
            clearInterval(interval);

            mediaRecorder.onstop = handleAudioAfterStop;

        }

            document.getElementById('startButton').addEventListener('click', () => {
                isAnswerSubmitted = true; // Reset for next question

                startRecording1();

                document.getElementById('startButton').disabled = true;
                document.getElementById('stopButton').disabled = false;
            });

            document.getElementById('stopButton').addEventListener('click', () => {
                stopRecording();
                isAnswerSubmitted = false; // Reset for next question

                document.getElementById('startButton').disabled = false;
                document.getElementById('stopButton').disabled = true;
            });

            document.getElementById('submitButton').addEventListener('click', () => {
                showRecordings();
            });
        })
        .catch(console.error);
});
// Function to process punctuation rules
function processAnswer(text) {
    const specialWords = ["furthermore", "moreover", "however", "therefore", "consequently", "nevertheless"];
    const conjunctions = ["and", "but"];
    
    // Xử lý từ đặc biệt (viết hoa, thêm dấu . trước, dấu , sau)
    specialWords.forEach(word => {
        const regex = new RegExp(`\\b${word}\\b`, 'gi');
        text = text.replace(regex, match => {
            return `. ${match.charAt(0).toUpperCase() + match.slice(1)},`;
        });
    });

    // Xử lý "and" và "but" (ngoại trừ "but also")
    text = text.replace(/\b(and|but)\b(?! also)/gi, "$1,");

    return text.trim();
}


var questionList = document.getElementById("question-list");
        questionList.innerHTML = ""; // Clear any existing content

        for (let i = 0; i < quizData.questions.length; i++) {
            questionList.innerHTML += `Question ${i + 1}: ${quizData.questions[i].question}<br>`;
        }




function startRecording1() {
    recordedChunks = [];
    if (mediaRecorder.state !== 'recording') {
        mediaRecorder.start();
    }
    document.getElementById('submitButton').disabled = true;
}




        let wordsSpelling = {};

        function startRecording() {
            
            document.getElementById('startButton').disabled = true;
                document.getElementById('stopButton').disabled = false;

            //startBtn.disabled = true;
            interval = setInterval(function() {
                    ret.innerHTML = convertSec(counter++); // Timer starts counting here...
                }, 1000);

                //resultColumn.innerHTML = ''; // Clear previous content
            const resultContainer = document.getElementById('confidence-level');


            /*recognition = new webkitSpeechRecognition();
            recognition.continuous = true;
            recognition.interimResults = true;
            recognition.lang = 'en-US';
            var finalTranscript = '';
            recognition.onresult = function (event) {
                let interimTranscript = '';
                
                var interTranscript  = '';

                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    let transcript = event.results[i][0].transcript;

                    
                    if(event.results[i].isFinal){
                  finalTranscript += transcript;
                 


                }else{
                  interTranscript += transcript;
                }
                textarea.value = finalTranscript + interTranscript +' ';
                const transcripta = event.results[i][0].transcript;
                const confidence = event.results[i][0].confidence;
                let confidencelevel = (confidence * 100).toFixed(2);
                resultContainer.innerHTML = `
                    <p>Recognizing: <strong>${transcripta}</strong></p>
                    <!--<p>Confidence: <strong>${confidencelevel}%</strong></p>-->
                `;

                if (confidencelevel < 90 && confidencelevel > 50) { 
                
                    wordsSpelling += transcript;
                    
                    console.log(`Confidential error`, wordsSpelling);


                }   


                }
            };*/
            //recognition.start();
            isAnswerSubmitted = true;
        }


        let reanswers = {};

        function reAnswerQuestion()
        {
           

            let answer = textarea.value.trim();

            //answers['answer' + (currentQuestionIndex + 1)] = answer;
            textarea.value = '';
            document.getElementById('startButton').disabled = false;

            console.log("Reanswer for question " + (currentQuestionIndex + 1) + ": " + questionElement.textContent);
            console.log("Old Answer " + (currentQuestionIndex + 1) + ": " + answer);
            
            // Save the reanswer message for the current question
            reanswers['reanswer' + (currentQuestionIndex + 1)] = answer;

        }

const ret = document.getElementById("timer");
const startBtn = document.querySelector("#start-timer");
document.getElementById("id_test").innerHTML = pre_id_test_;


let counter = 0;


function convertSec(cnt) {
    let sec = cnt % 60;
    let min = Math.floor(cnt / 60);
    if (sec < 10) {
        if (min < 10) {
            return "0" + min + ":0" + sec;
        } else {
            return min + ":0" + sec;
        }
    } else if ((min < 10) && (sec >= 10)) {
        return "0" + min + ":" + sec;
    } else {
        return min + ":" + sec;
    }
}



async function handleAudioAfterStop() {
        document.getElementById('loadingOverlay').style.display = 'flex';

    document.getElementById('stopButton').disabled = true;
    const audioBlob = new Blob(recordedChunks, { type: 'audio/webm;codecs=opus' });
    recordedChunks = [];

    // Gửi blob đi STT
    const formData = new FormData();
    formData.append("audio", audioBlob, "recording.webm");
    formData.append("lang", "en");

    try {
        const response = await fetch("http://127.0.0.1:5000/stt", {
            method: "POST",
            body: formData,
        });

        const result = await response.json();
        console.log("Speech-to-text result:", result);
        textarea.value = result.text || ""; // Tùy bạn muốn fill hay không
        const resultContainer = document.getElementById('confidence-level');
        resultContainer.innerHTML = ` <p>Confidence: <strong>${result.confidence}%</strong></p>`;
        document.getElementById('startButton').disabled = false;

    } catch (error) {
        console.error("STT error:", error);
    }

    // Nếu đang ở chế độ re-answer thì đẩy tiếp sang câu sau
    if (!isAnswerSubmitted) {
        let answer = textarea.value.trim();
        answers['answer' + (currentQuestionIndex + 1)] = processAnswer(answer);
        const uploadLink = await uploadRecording(audioBlob);
        answers['link_audio' + currentQuestionIndex] = uploadLink;

        counter = 0;
        ret.innerHTML = convertSec(counter);
        currentQuestionIndex++;

        if (currentQuestionIndex < quizData.questions.length) {
            loadQuestion();
        } else {
            document.getElementById('startButton').style.display = 'none';
                        document.getElementById('reAnswerButton').style.display = 'none';
                        document.getElementById('stopButton').style.display = 'none';
                        document.getElementById('review-page').style.display = 'block';
                        document.getElementById('submitButton').style.display = 'block';

                        try {
                            // Đợi tất cả upload hoàn thành
                           // await Promise.all(uploadPromises);
                            console.log("All audio uploads completed");
                            
                            // Sau khi upload xong mới chuyển sang review
                            for (let i = 0; i < quizData.questions.length; i++) {
                                part = quizData.questions[i].part;
                                ReviewPage(i);
                            }
                            
                            showRecordings();
                        } catch (error) {
                            console.error("Error uploading audio:", error);
                            // Xử lý lỗi nếu cần
                        }
                    
        }
    }
        document.getElementById('loadingOverlay').style.display = 'none';

}
async function stopRecording() {
    let questionText = questionElement.textContent;

    let wordCount = questionText.trim().split(/\s+/).length;

    if(isAnswerSubmitted != true){
            Swal.fire({
                title: "Missing Answer",
                text: `Bạn hãy trả lời câu hỏi này trước bằng cách nhấn "Start Record" để ghi âm câu trả lời rồi nhấn "Submit Answer" để chuyển sang câu hỏi tiếp theo nhé !`,
                icon: "question"
            });
    }
    
    else{
        /*if (recognition) {
            recognition.stop();
            counters['counter' + (currentQuestionIndex + 1)] = counter;

            clearInterval(interval);
        
        }*/

        counters['counter' + (currentQuestionIndex + 1)] = counter;
        clearInterval(interval);

        if (mediaRecorder.state === 'recording') {
            const stopped = new Promise(resolve => mediaRecorder.onstop = resolve);
            mediaRecorder.stop();
            await stopped;

            await handleAudioAfterStop(); // Gọi xử lý sau khi ghi xong
        }


        document.getElementById('stopButton').disabled = false;
        isAnswerSubmitted = true; 
    }
}



        async function speakText(text) {
            try {
                const lang = 'en';
                const apiUrl = `http://127.0.0.1:5000/tts?text=${encodeURIComponent(text)}&lang=${lang}`;
                
                const audio = new Audio(apiUrl);
                
                // Đặt tốc độ phát
                const speedDropdown = document.getElementById('speedOptions');
                if (speedDropdown) {
                    audio.playbackRate = parseFloat(speedDropdown.value);
                }
                
                await audio.play();
                console.log('Playback started');
            } catch (error) {
                console.error('Playback error:', error);
                alert('Could not play audio: ' + error.message);
            }
        }



        let examiner_test = document.getElementById("examinerVideo");


        function loadQuestion() {
            isAnswerSubmitted = false;
            if (currentQuestionIndex >= 0) {
                    let questionText = questionElement.textContent;
                    let wordCount = questionText.trim().split(/\s+/).length;

                    // Play corresponding audio file
                    

                    // Play examiner.mp4 in left corner

                    var times = parseInt(wordCount/15)
                     
  
                    examiner_test.addEventListener('ended', function() {
                        if (times >= 1) {
                          times--;
                          examiner_test.play();
                        }
                      });
                      
                      examiner_test.play();
                }



            const currentQuestion = quizData.questions[currentQuestionIndex];
            const currentQuestionPart = quizData.questions.part; 

            questionElement.textContent = `Question ${currentQuestion.stt}: ${currentQuestion.question}`;
            document.getElementById("speaking-part").innerHTML = `Speaking part ${currentQuestion.part} - Topic: ${currentQuestion.topic}`;
            
            
            // Stop any ongoing speech before speaking the new question
            speechSynthesis.cancel(); // This line stops the previous speech
            speakText(currentQuestion.question);
            textarea.value = '';

            document.getElementById('stopButton').disabled = false; // Ensure the stop button is enabled for each new question
            /*if (mediaRecorder.state !== 'recording') {
                mediaRecorder.start(); // Start the recorder for the new question
            }*/
        }

        function showRecordings() {
            const recordingsListDiv = document.getElementById('recordingsList');
            recordingsListDiv.innerHTML = ''; // Clear previous recordings

            recordingsList.forEach((blob, index) => {
                const audioElement = document.createElement('audio');
                audioElement.src = URL.createObjectURL(blob);
                audioElement.controls = true;
                audioElement.preload = 'metadata';

                const recordingLabel = document.createElement('p');
                recordingLabel.textContent = `Question ${index + 1}:`;

                recordingsListDiv.appendChild(recordingLabel);
                recordingsListDiv.appendChild(audioElement);

                // Retrieve and display the saved link from answers
                const link = answers['link_audio' + (index + 1)];
                if (link) {
                    const linkElement = document.createElement('a');
                    linkElement.href = link;
                    linkElement.target = '_blank';
                    linkElement.textContent = 'Download link';
                    recordingsListDiv.appendChild(linkElement);
                }

                recordingsListDiv.appendChild(document.createElement('br'));
                recordingsListDiv.appendChild(document.createElement('br'));
            });

            document.getElementById('submitButton').style.display = 'none';
        }



// mic-check
function playAudio(audioChunksCheck) {
    const blob = new Blob(audioChunksCheck, { type: 'audio/x-mpeg-3' });
    const audioPlayer = document.getElementById('audioPlayer');
    audioPlayer.src = URL.createObjectURL(blob);
    audioPlayer.controls = true;
    audioPlayer.autoplay = true;
}

var mediaRecorderCheck;
var audioChunksCheck = [];

const getmiceacesses = function () {
    audioChunksCheck = []; // Reset the audio chunks each time we start recording
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(function (stream) {
            mediaRecorderCheck = new MediaRecorder(stream);

            mediaRecorderCheck.start();
            console.log('Recording started');

            setTimeout(stopRecorder, 10000); // Automatically stop the recorder after 10 seconds

            mediaRecorderCheck.addEventListener("dataavailable", (event) => {
                audioChunksCheck.push(event.data);
                console.log('Data available event fired');
            });

            mediaRecorderCheck.addEventListener("stop", () => {
                playAudio(audioChunksCheck);
                console.log('Recording stopped and audio is playing');
            });
        })
        .catch(function (err) {
            console.error('Error accessing the microphone: ', err);
        });
};

const stopRecorder = function () {
    if (mediaRecorderCheck && mediaRecorderCheck.state === 'recording') {
        mediaRecorderCheck.stop();
        console.log('Recording stopped');
    } else {
        console.error('MediaRecorder is not initialized or not recording.');
    }
};

document.addEventListener('DOMContentLoaded', (event) => {
    const recordingcheck = document.querySelector('.recorder');
    const stopRecordingCheck = document.querySelector('.stopRecorder');

    if (recordingcheck && stopRecordingCheck) {
        recordingcheck.addEventListener('click', getmiceacesses);
        stopRecordingCheck.addEventListener('click', stopRecorder);
    } else {
        console.error('Elements with classes "recorder" and "stopRecorder" not found in the DOM.');
    }
});
// end mic check


let fluency_and_coherence_all_point_part1 = 0;
let lexical_resource_all_point_part1 = 0;
let fluency_and_coherence_all_point_part2 = 0;
let lexical_resource_all_point_part2 = 0;
let fluency_and_coherence_all_point_part3 = 0;
let lexical_resource_all_point_part3 = 0;


let grammatical_range_and_accuracy_all_point_part1 = 0;
let grammatical_range_and_accuracy_all_point_part2 = 0;
let grammatical_range_and_accuracy_all_point_part3 = 0;

let pronunciation_all_point_part1 = 0;
let pronunciation_all_point_part2 = 0;
let pronunciation_all_point_part3 = 0;


async function submitAnswers() {
    //console.log(wordsSpelling);
    for (let element of document.getElementsByClassName("video-container-2")) {
        element.style.display = "none";
    }
    document.getElementById("confidence-level").style.display="none";
    document.getElementById("timer").style.display="none";

    document.getElementById("btn-show-practice-pronunciation").style.display="block";

    document.getElementById("reAnswerButton").style.display="none";

    document.getElementById("check-answer").style.display = "none";
    document.getElementById('answerTextarea').style.display = 'none';
    document.getElementById("container1").style.display = "none"
    textarea.value = '';

    // mới, cách sửa: resultColumn vào hết submitAnswers => không lưu local storage, dễ sử dụng hơn
    questionElement.textContent = '';
    
    let resultColumn = document.getElementById('resultColumn');
    resultColumn.innerHTML = ''; // Clear previous content


    let res = 0;

    // Tính Điểm/ Nhận xét cho Part 1



for (let i = 0; i < quizData.questions.length; i++){
    part = quizData.questions[i].part;
    //await ReviewPage(i);

    //await GetSummaryPart1(i);
    
}
    quizData.questions.forEach((question, i) => {
        
        let answer = answers['answer' + (i + 1)] || "";
        const link = answers['link_audio' + (i + 1)];

        let counter = counters['counter' + (i + 1)] || "";
        let reanswer = reanswers['reanswer' + (i + 1)] || "";
        let result = '';
        let keywordResult = checkForGreatKeyword(answer);
        let lengthResult = checkAnswerLength(answer);

        // Determine the final result and update res
        if (keywordResult.result === "Good (+1 point)") {
            res += 10;
            result = keywordResult.result;
        } 
        else if (lengthResult.result === "Good length") {
            res += 5;
            result = lengthResult.result;
        } 
        else {
            res += 1;
            result = lengthResult.result;
        }





        let highlightedAnswer = answer;
        let Timeused = counter;
        let wordCount = answer.split(/\s+/).length;

        //console.log(wordsSpellingCheck);


        // Only highlight if there is a reanswer
        if (reanswer) {
            let answerWords = answer.split(/\s+/);
            let reanswerWords = reanswer.split(/\s+/);

            let pronunciation_words_list = [];

            highlightedAnswer = answerWords.map(word => {
                if (reanswerWords.includes(word)) {
                    return word;
                } else {
                    if (!pronunciation_words_list.includes(word)) {
                        pronunciation_words_list.push(word);
                    }
                    return `<span style="color: red;">${word}</span>`;
                }
            }).join(' ');

            // Append reanswer, question, answer, and result to the result column
            resultColumn.innerHTML += `<p><strong>Reanswer for Question ${i + 1}:</strong> ${reanswer}</p>`;

            // Create the table inside the pronunciation-list div if it doesn't exist
            if (!document.querySelector("#pronunciation-list table")) {
                let tableHTML = `
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Words List</th>
                                <th>Pronunciation</th>
                                <th>Listen</th>
                                <th>Record Word</th>
                                <th>Accuaracy</th>
                                <th>Qualified ?</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>`;
                document.getElementById("pronunciation-list").innerHTML += tableHTML;
            }

            // Add each word to a new row in the first column
            pronunciation_words_list.forEach(word => {
                let rowHTML = `
                    <tr>
                        <td>${word}</td>
                        <td>${word}</td>
                        <td><button onclick="speakWord('${word}')">Listen</button></td>
                        <td><button onclick="recordWord('${word}', this)">Record</button></td>
                        <td class="confidence-level"></td>
                        <td class="qualification"></td>
                    </tr>`;
                document.querySelector("#pronunciation-list tbody").innerHTML += rowHTML;
            });
        }
        });

    

        ResultInput();
    }


async function ResultInput() {
        // Copy the content to the form fields

    var contentToCopy1 = document.getElementById("data-save-full-speaking").textContent;
    var contentToCopy2 = document.getElementById("date").textContent;
    var contentToCopy4 = document.getElementById("title").textContent;
    var contentToCopy6 = document.getElementById("id_test").textContent;
    var contentToCopy7 = document.getElementById("userResult").textContent;
    var contentToCopy8 = document.getElementById("userBandDetail").innerHTML;
    var contentToCopy9 = document.getElementById("testtype").textContent;


    document.getElementById("dateform").value = contentToCopy2;
    document.getElementById("testname").value = contentToCopy4;
    document.getElementById("idtest").value = contentToCopy6;
    document.getElementById("test_type").value = contentToCopy9;
    document.getElementById("testsavenumber").value = resultId;

   
    
        setTimeout(function() {
        // Automatically submit the form
        jQuery('#saveUserResultTest').submit();
        },0); // 5000 milliseconds = 5 seconds */


}
          


function checkForGreatKeyword(answer) {
    if (answer.toLowerCase().includes('great')) {
        return { result: "Good (+1 point)", points: 10 };
    } else {
        return { result: "OK", points: 1 };
    }
}


function checkAnswerLength(answer) {
    let wordCount = answer.split(/\s+/).length;
    if (wordCount > 5) {
        return { result: "Good length", points: 5 };
    } else {
        return { result: "Not enough length", points: 1 };
    }
}


function speakWord(word) {
    let utterance = new SpeechSynthesisUtterance(word);
    speechSynthesis.speak(utterance);
}


function recordWord(expectedWord, buttonElement) {
    if (!('webkitSpeechRecognition' in window)) {
        alert('Web Speech API is not supported by this browser.');
        return;
    }

    let recognition = new webkitSpeechRecognition();
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.lang = 'en-US';

    recognition.onstart = function() {
        console.log('Voice recognition started.');
    };

    recognition.onerror = function(event) {
        console.error('Voice recognition error', event);
    };

    recognition.onend = function() {
        console.log('Voice recognition ended.');
    };

    recognition.onresult = function(event) {
        let transcript = event.results[0][0].transcript.trim().toLowerCase();
        let confidence = event.results[0][0].confidence * 100;
        let confidenceCell = buttonElement.parentElement.nextElementSibling;
        let qualificationCell = confidenceCell.nextElementSibling;

        confidenceCell.textContent = confidence.toFixed(2) + '%';

        if (transcript === expectedWord.toLowerCase()) {
            if (confidence > 80) {
                qualificationCell.textContent = "YES";
            } else {
                qualificationCell.textContent = "Try again";
            }
        } else {
            qualificationCell.textContent = "Misunderstand";
        }
    };

    recognition.start();
}

     /*  <!-- // Load the first question but don't display it until "Get Started" is clicked
        document.addEventListener('DOMContentLoaded', (event) => {
            loadQuestion();
        });  */

        document.getElementById("btn-show-practice-pronunciation").addEventListener('click', openPracticePronunciation);
        
            // Close the draft popup when the close button is clicked
            function closePracticePronunciation() {
                document.getElementById('practice-pronunciation-popup').style.display = 'none';
            }

            function openPracticePronunciation() {
                document.getElementById('practice-pronunciation-popup').style.display = 'block';
              
            }



            
</script>

<script src = "\contents\themes\tutorstarter\ielts-speaking-toolkit\function5.js"></script>
<!--
<script src = "function\analysis\speaking-part-1\criteria\fluency_and_coherence.js"></script>
<script src = "function\analysis\speaking-part-1\criteria\lexical_resource.js"></script>
<script src = "function\analysis\speaking-part-1\criteria\pronunciation.js"></script>
<script src = "function\analysis\speaking-part-1\criteria\grammatical_range_and_accuracy.js"></script>
-->
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
                    table: 'ielts_speaking_test_list',
                    change_token: '$token_need',
                    payment_gate: 'token',
                    type_token: 'token',
                    title: 'Renew test $testname with $id_test (Ielts Speaking Test) with $token_need (Buy $time_allow time do this test)',
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