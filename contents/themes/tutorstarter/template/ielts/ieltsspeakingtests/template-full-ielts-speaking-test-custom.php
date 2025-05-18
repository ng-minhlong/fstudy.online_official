<?php
/*
 * Template Name: Full Custom Speaking Test
 */
get_header(); // Gọi phần đầu trang (header.php)

require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();

    /*if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['doing_text'])) {
        $textarea_content = sanitize_textarea_field($_POST['doing_text']);
        update_user_meta($user_id, "ieltsspeakingtests_{$post_id}_textarea", $textarea_content);

        wp_safe_redirect(get_permalink($post_id) . 'get-mark-speaking/');
        exit;
    }*/
$post_id = get_the_ID();
// Get the custom number field value

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


// Assuming you are in a PHP file that handles the request
$part1Id = isset($_GET['part1_id']) ? $_GET['part1_id'] : null;
$part2Id = isset($_GET['part2_id']) ? $_GET['part2_id'] : null;
$part3Id = isset($_GET['part3_id']) ? $_GET['part3_id'] : null;








// Prepare the SQL query to fetch question_content, stt, topic, sample, speaking_part where id_test matches the custom_number
$sql = "SELECT stt, question_content, topic, sample, speaking_part FROM ielts_speaking_part_1_question WHERE id_test = ?";
$sql2 = "SELECT question_content, topic, sample, speaking_part FROM ielts_speaking_part_2_question WHERE id_test = ?";
$sql3 = "SELECT stt, question_content, topic, sample, speaking_part FROM ielts_speaking_part_3_question WHERE id_test = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $part1Id); // 'i' is used for integer
$stmt->execute();
$result = $stmt->get_result();

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("i", $part2Id); // 'i' is used for integer
$stmt2->execute();
$result2 = $stmt2->get_result();

$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("i", $part3Id); // 'i' is used for integer
$stmt3->execute();
$result3 = $stmt3->get_result();

// Initialize an empty array to store the questions data
$questions = [];
$topic = ''; // Variable to store the topic

while ($row = $result->fetch_assoc()) {
    // Add each row as an associative array to the $questions array
    $questions[] = [
        "question" => $row['question_content'],
        "part" => $row['speaking_part'],
        "id" => $row['stt'],
        "sample" => $row['sample']
    ];

    // Capture the topic (assumes the topic is the same for all rows)
    if (!$topic) {
        $topic = $row['topic'];
    }
}
while ($row = $result2->fetch_assoc()) {
    // Add each row as an associative array to the $questions array
    $questions[] = [
        "question" => $row['question_content'],
        "part" => $row['speaking_part'],
        "sample" => $row['sample']
    ];

    // Capture the topic (assumes the topic is the same for all rows)
    if (!$topic) {
        $topic = $row['topic'];
    }
}
while ($row = $result3->fetch_assoc()) {
    // Add each row as an associative array to the $questions array
    $questions[] = [
        "question" => $row['question_content'],
        "part" => $row['speaking_part'],
        "id" => $row['stt'],
        "sample" => $row['sample']
    ];

    // Capture the topic (assumes the topic is the same for all rows)
    if (!$topic) {
        $topic = $row['topic'];
    }
}

// Output the quizData as JavaScript
echo '<script type="text/javascript">
const quizData = {
    "title": "' . htmlspecialchars($topic) . '",
    "questions": ' . json_encode($questions) . '
};
console.log(quizData);
</script>';

// Close the database connection
$conn->close();

    ?>
    
              


<html lang="en" dir="ltr">

<head>
    <script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_CHTML">
            if (window.MathJax) {
                    MathJax.Hub.Config({
                    tex2jax: {
                        inlineMath: [["$", "$"], ["\\(", "\\)"]],
                        processEscapes: true
                    }
                });
            }
            </script>    
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.responsivevoice.org/responsivevoice.js?key=qt48zGVy"></script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title> Ielts Speaking Test Simulation</title>
    <link rel="stylesheet" href="\contents\themes\tutorstarter\ielts-speaking-toolkit\style\style.css">

    <style>
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

    </style>
</head>

<body>
    



    <div class="container1" id ="container1">
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


             </div>

            <div  class="column0" > 
                
                <div id ="intro_test">
                    <h3 id="title"></h3>
                    <div id="id_test">0000</div>
                    <div id="testtype"></div>



                     <h2  style="font: bold;" id ="title"></h2>
                     
                     <h3>Hướng dẫn</h3>
                     <p>     Trước hết, bạn cần cho phép truy cập microphone xuyên suốt quá trình làm bài để không ảnh hưởng đến kết quả</p>
                     <p>     Sau đó, bên dưới có phần mic check, hãy kiểm tra mic của bạn bằng cách ấn RECORD rồi STOP để nghe đoạn ghi âm</p>
                     <p>     Tại mỗi câu hỏi, sau khi examiner đọc xong câu hỏi, hãy nhấn nút START RECORD để bắt đầu ghi âm. Nếu bạn trả lời xong, hãy ấn SUBMIT ANSWER để chuyển sang câu tiếp theo</p>
                     <p>     Bạn có thể yêu cầu examiner đọc lại câu hỏi/ nhắc lại câu hỏi bằng cách ấn nút COMMAND GUILDLINE bên dưới examiner </p>
                     
                     
                     <div id="list-question">
                     <h3 id="title"></h3>
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


                     <button id="getStartedButton" onclick="initializeTest()">Get Started</button>
                </div>
             </div>


        </div>


    <div id ="virtual-room">
            <div class="column1" id="column1">
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
        <div style="display: none;" id="date" style="visibility:hidden;"></div>

            <h3>Result </h3>
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
                        <input   type="text" id="resulttest" name="resulttest" placeholder="Kết quả"  class="form-control form_data" />
                        <span id="result_error" class="text-danger" ></span>

                    </div>


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
                        <input type="text"  id="band_detail" name="band_detail" placeholder="Correct Answer"  class="form-control form_data" />
                        <span id="correctanswer_error" class="text-danger"></span>  
                    </div>


                    <div class = "form-group"   >
                        <input type="text"  id="user_answer_and_comment" name="user_answer_and_comment" placeholder="User Answer"  class="form-control form_data" />
                        <span id="useranswer_error" class="text-danger"></span>
                </div>
                
                <div class = "form-group"   >
                        <input type="text"  id="test_type" name="test_type" placeholder="Test Type"  class="form-control form_data" />
                        <span id="correctanswer_error" class="text-danger"></span>  
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
                
// save date (ngày làm bài cho user)
const currentDate = new Date();

// Get day, month, and year
const day = currentDate.getDate();
const month = currentDate.getMonth() + 1; // Adding 1 because getMonth() returns zero-based month index
const year = currentDate.getFullYear();

// Display the date
const dateElement = document.getElementById('date');
dateElement.innerHTML = `${day}/${month}/${year}`;


    let os = null; 
    
    document.addEventListener('DOMContentLoaded', function () {
        getOS();
        checkLocationAndIpAdress();   
            
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

        let recognition;
        let textarea = document.getElementById('answerTextarea');
        let questionElement = document.getElementById('question');
        let currentQuestionIndex = 0;
        let answers = {};
        let counters = {};
        let mediaRecorder;
        let recordedChunks = [];
        let recordingsList = [];
        let numRecordings = 0;

        

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

            mediaRecorder.onstop = () => {
                if (!isAnswerSubmitted) {
                    const blob = new Blob(recordedChunks, { type: 'audio/mp3' });
                    recordingsList.push(blob);
                    recordedChunks = [];
                    numRecordings++;
                    isAnswerSubmitted = true; // Prevent multiple submissions

                    // Collect the user's answer
                    let answer = textarea.value.trim();
                    answers['answer' + (currentQuestionIndex + 1)] = answer;
                    console.log("Question " + (currentQuestionIndex + 1) + ": " + questionElement.textContent);
                    console.log("Answer " + (currentQuestionIndex + 1) + ": " + answer);
                    console.log(`Time used for question:`+ (currentQuestionIndex + 1) +": "+ counter)
                    counter = 0; // Reset the counter to 0
                    ret.innerHTML = convertSec(counter); // Update the display to show 00:00
                    // Move to next question or end the quiz
                    currentQuestionIndex++;
                   
               
                    if (currentQuestionIndex < quizData.questions.length) {
                        loadQuestion();
                    } else {
                        document.getElementById('startButton').style.display = 'none';
                        document.getElementById('stopButton').style.display = 'none';
                        document.getElementById('submitButton').style.display = 'block';
                        endTest();
                        showRecordings();
                    }
                }
            };
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
            
            //startBtn.disabled = true;
            interval = setInterval(function() {
                    ret.innerHTML = convertSec(counter++); // Timer starts counting here...
                }, 1000);

                //resultColumn.innerHTML = ''; // Clear previous content
            const resultContainer = document.getElementById('confidence-level');


            recognition = new webkitSpeechRecognition();
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
            };
            recognition.start();
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
document.getElementById("testtype").innerHTML = 'Full Test';



let counter = 0;
let interval;


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




    /*    function stopRecording() {
            if (recognition) {
                recognition.stop();
                let answer = textarea.value.trim();

                answers['answer' + (currentQuestionIndex + 1)] = answer;
                counters['counter' + (currentQuestionIndex + 1)] = counter;
                textarea.value = '';

                if (answer == "can you repeat question"){
                     
                    speakText(questionElement.textContent);
                    //console.log("Answer " + (currentQuestionIndex + 1) + ": " + answer);

                }   
                
                else{
                
                    if (mediaRecorder.state === 'recording') {
                    mediaRecorder.stop(); // Ensure the recorder is stopped
                }

                clearInterval(interval);
                //startBtn.disabled = false;
                


                    // Log the question and answer
                console.log("Question " + (currentQuestionIndex + 1) + ": " + questionElement.textContent);
                console.log("Answer " + (currentQuestionIndex + 1) + ": " + answer);
                console.log(`Time used for question:`+ (currentQuestionIndex + 1) +": "+ counter)

               

                mediaRecorder.onstop = () => {
                const blob = new Blob(recordedChunks, { type: 'audio/mp3' });
                recordingsList.push(blob);
                recordedChunks = []; // Clear the chunks for the next recording
                numRecordings++;
                document.getElementById('submitButton').disabled = false;
                currentQuestionIndex++;

                counter = 0; // Reset the counter to 0
                ret.innerHTML = convertSec(counter); // Update the display to show 00:00
               

                if (currentQuestionIndex < quizData.questions.length) {
                    loadQuestion();
                    //mediaRecorder.start(); // Start the recorder for the next question

                } else {
                    document.getElementById('startButton').style.display = 'none';
                    document.getElementById('stopButton').style.display = 'none';
                    
                    document.getElementById('submitButton').style.display = 'block';
                    
                    endTest();
                    showRecordings();
                    
                }
            };
                
              
            }

            }
            document.getElementById('stopButton').disabled = false; // Ensure the stop button is re-enabled
    
        }*/

function stopRecording() {
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
        if (recognition) {
            recognition.stop();
            counters['counter' + (currentQuestionIndex + 1)] = counter;

            clearInterval(interval);
        
        }

        if (mediaRecorder.state === 'recording') {
            mediaRecorder.stop(); // Ensure the recorder is stopped
            
        }

        document.getElementById('stopButton').disabled = false;
        isAnswerSubmitted = true; 
    }
}



        function speakText(text) {
           if(os == 'Windows'){

            
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'en-US';

            /* utterance.voice = speechSynthesis.getVoices().filter(function(voice) {
            return voice.name == "Google UK English Female"
        })[0]; */


            // Get the selected speed value
            const speedDropdown = document.getElementById('speedOptions');


            if (speedDropdown) {
                const selectedSpeed = speedDropdown.value;
                utterance.rate = selectedSpeed;
            }

            speechSynthesis.speak(utterance);
        }
        else if(os == 'iOS'){
            responsiveVoice.speak(text);
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

            questionElement.textContent = `Question ${currentQuestion.id}: ${currentQuestion.question}`;
            document.getElementById("speaking-part").innerHTML = `Speaking part ${currentQuestion.part}`;
            
            
            // Stop any ongoing speech before speaking the new question
            speechSynthesis.cancel(); // This line stops the previous speech
            speakText(currentQuestion.question);
            textarea.value = '';

            document.getElementById('stopButton').disabled = false; // Ensure the stop button is enabled for each new question
            if (mediaRecorder.state !== 'recording') {
                mediaRecorder.start(); // Start the recorder for the new question
            }
        }

        function showRecordings() {
            const recordingsListDiv = document.getElementById('recordingsList');
            recordingsListDiv.innerHTML = ''; // Clear previous recordings
            recordingsList.forEach((blob,index) => {
                const audioElement = document.createElement('audio');
                audioElement.src = URL.createObjectURL(blob);
                audioElement.controls = true;
                audioElement.preload = 'metadata'; // Prevent autoplay

                const recordingLabel = document.createElement('p');
                recordingLabel.textContent = `Question ${index + 1}:`;

                recordingsListDiv.appendChild(recordingLabel);
                recordingsListDiv.appendChild(audioElement);
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
    for (let element of document.getElementsByClassName("video-container")) {
        element.style.display = "none";
    }
    document.getElementById("confidence-level").style.display="none";
    document.getElementById("timer").style.display="none";

    document.getElementById("btn-show-practice-pronunciation").style.display="block";

    document.getElementById("reAnswerButton").style.display="none";

    document.getElementById("check-answer").style.display = "none";
    document.getElementById('answerTextarea').style.display = 'none';
    document.getElementById("result-full-page").style.display="block";
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
    await GetSummaryPart1(i);
    
}
    quizData.questions.forEach((question, i) => {
        
        let answer = answers['answer' + (i + 1)] || "";

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


    function ResultInput() {
        // Copy the content to the form fields

    var contentToCopy1 = document.getElementById("userAnswerAndComment").textContent;
    var contentToCopy2 = document.getElementById("date").textContent;
    var contentToCopy4 = document.getElementById("title").textContent;
    var contentToCopy6 = document.getElementById("id_test").textContent;
    var contentToCopy7 = document.getElementById("userResult").textContent;
    var contentToCopy8 = document.getElementById("userBandDetail").textContent;
    var contentToCopy9 = document.getElementById("testtype").textContent;


    document.getElementById("user_answer_and_comment").value = contentToCopy1;
    document.getElementById("dateform").value = contentToCopy2;
    document.getElementById("testname").value = contentToCopy4;
    document.getElementById("idtest").value = contentToCopy6;
    document.getElementById("resulttest").value = contentToCopy7;
    document.getElementById("band_detail").value = contentToCopy8;
    document.getElementById("test_type").value = contentToCopy9;

    
    // Add a delay before submitting the form
setTimeout(function() {
// Automatically submit the form
jQuery('#saveUserResultTest').submit();
}, 5000); // 5000 milliseconds = 5 seconds
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

<script src = "\contents\themes\tutorstarter\ielts-speaking-toolkit\function\analysis\speaking-part-1\summary2.js"></script>
<script src = "\contents\themes\tutorstarter\ielts-speaking-toolkit\function\analysis\speaking-part-1\overall-tab1.js"></script>
<script src = "\contents\themes\tutorstarter\ielts-speaking-toolkit\function\analysis\speaking-part-1\sample-tab.js"></script>
<script src = "\contents\themes\tutorstarter\scan-device\location_and_ip.js"></script>
<script src = "\contents\themes\tutorstarter\scan-device\system_os_2.js"></script>


<!--
<script src = "function\analysis\speaking-part-1\criteria\fluency_and_coherence.js"></script>
<script src = "function\analysis\speaking-part-1\criteria\lexical_resource.js"></script>
<script src = "function\analysis\speaking-part-1\criteria\pronunciation.js"></script>
<script src = "function\analysis\speaking-part-1\criteria\grammatical_range_and_accuracy.js"></script>
-->
</body>

</html>
<?php
    get_footer();
} else {
    get_header();
    echo '<p>Please log in start speaking test.</p>';
    get_footer();
}