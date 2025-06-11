<?php
/*
 * Template Name: Start Doing Test Topik Reading
 * Template Post Type: Topik Reading
 
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


$sql_test = "SELECT * FROM topik_reading_test_list WHERE id_test = ?";


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
    $correct_answer = $data['correct_answer']; // Fetch the testname field
    $test_type = $data['test_type']; // Fetch the testname field
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

    echo "<script>console.log('Token: $token, Token Use History: $token_use_history, Mày tên: $current_username');</script>";
   

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
                    let test_type = "' . $test_type . '";

                    let time_left = "' . (isset($foundUser['time_left']) ? $foundUser['time_left'] : 10) . '";
                    let correct_answer = ' . $correct_answer . ';
                </script>';
                        





// Đóng kết nối
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>

    <title>Topik Reading </title>
    <style>

        #quiz-container1 {
            flex: 3;
            padding: 20px;
            min-height: 100vh;
        }

        #sidebar1 {
            flex: 1;
            padding: 20px;
            border-left: 2px solid #ccc;
            height: auto;
            overflow-y: auto;
        }
        .question-wrapper, .context-wrapper {
            display: none;
        }
        .active {
            display: block;
        }
        .box-answer {
            cursor: pointer;
            width: calc(20% - 10px);
            height: 40px;
            border: 1px solid black;
            margin-bottom: 10px;
            display: inline-block;
            box-sizing: border-box;
            padding: 15px;
        }
        .selected {
            background-color: lightgreen !important;
        }
        .navigation1 {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            width: calc(100% - 40px);
        }
        .nav-btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
        .nav-btn:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        #logBtn {
            margin-top: 10px;
            padding: 10px;
            width: 100%;
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
        }
        #timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #333;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            z-index: 1000;
        }
        .time-warning {
            background-color: #ff9800 !important;
        }
        .time-critical {
            background-color: #f44336 !important;
            animation: blink 1s infinite;
        }
        .question-content{
            height: 300px;
        }
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .container-content {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            min-height: 100vh;
        }

        .header-content {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            padding: 15px;
            background-color: #f8f9fa; /* Màu nền nhẹ */
            border-bottom: 2px solid #ddd; /* Đường kẻ ngăn cách */
        }

    </style>
</head>
<body>
    <div id="timer"></div>

    <div class="container-content">
        <div id="quiz-container1">
            <div class = "header-content"><?php echo $testname ?></div>

            <?php echo html_entity_decode($testcode); ?> <!-- Hiển thị HTML từ database mà không escape -->
            
            <div class="navigation1">
                <button class="nav-btn" id="prevBtn" disabled>Previous</button>
                <button class="nav-btn" id="nextBtn">Next</button>
            </div>
        </div>

        <div id="sidebar1">
            <h3>Answers</h3>
            <div id="boxanswers"></div>
            <button id="logBtn">Submit Answers</button>
            <!-- giấu form send kết quả bài thi -->


    
  
            <span id="message"  ></span>
            <form id="saveTopikReadingResult"  >
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
                            <textarea type="text"  id="useranswer" name="useranswer" placeholder="User Answer"  class="form-control form_data"></textarea>
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
        
    </div>

    <script>
        hidePreloader();
        // function save data qua ajax
        jQuery('#saveTopikReadingResult').submit(function(event) {
            event.preventDefault(); // Prevent the default form submission
            
            var link = "<?php echo admin_url('admin-ajax.php'); ?>";
            
            var form = jQuery('#saveTopikReadingResult').serialize();
            var formData = new FormData();
            formData.append('action', 'save_user_result_topik_reading');
            formData.append('save_user_result_topik_reading', form);
            
            jQuery.ajax({
                url: link,
                data: formData,
                processData: false,
                contentType: false,
                type: 'post',
                success: function(result) {
                    jQuery('#submit').attr('disabled', false);
                    if (result.success == true) {
                        jQuery('#saveTopikReadingResult')[0].reset();
                    }
                    jQuery('#result_msg').html('<span class="' + result.success + '">' + result.data + '</span>');
                }
            });
            });
            
                    //end
            

        document.addEventListener("DOMContentLoaded", function () {
            document.addEventListener("submitForm", function () {
                setTimeout(function () {
                    let form = document.getElementById("saveTopikReadingResult");
                    form.submit(); // This should work now that there's no conflict
                }, 2000); 
            });
        });


        //end new adding


        
        document.addEventListener("DOMContentLoaded", function () {
            const questions = document.querySelectorAll(".question-wrapper");
            const contexts = document.querySelectorAll(".context-wrapper");
            const groupQuestion = document.querySelectorAll(".question-group-wrapper");

            const sidebar = document.getElementById("boxanswers");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const logBtn = document.getElementById("logBtn");
            const timerElement = document.getElementById("timer");
            let currentQuestion = 0;
            
            // Cài đặt timer 60 phút
            let timeLeft = <?php echo intval($time); ?> * 60; // Chắc chắn rằng $time là số nguyên
            let timerInterval;
            

            let startTime; // Declare globally

            function startTimer() {
                startTime = new Date(); // Record start time at beginning

                timerInterval = setInterval(function() {
                    timeLeft--;
                    
                    // Cập nhật hiển thị timer
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    
                    // Thay đổi màu sắc khi thời gian sắp hết
                    if (timeLeft <= 300) { // 5 phút cuối
                        timerElement.classList.add("time-critical");
                        timerElement.classList.remove("time-warning");
                    } else if (timeLeft <= 900) { // 15 phút cuối
                        timerElement.classList.add("time-warning");
                        timerElement.classList.remove("time-critical");
                    }
                    
                    // Tự động nộp bài khi hết giờ
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        submitQuiz();
                    }
                }, 1000);
            }
            
            // Khởi tạo sidebar với các câu hỏi
            questions.forEach((question, index) => {
                let qid = question.getAttribute("data-qid");
                let box = document.createElement("div");
                box.classList.add("box-answer");
                box.setAttribute("data-qid", qid);
                box.setAttribute("data-index", index);
                box.textContent = `${index + 1}`;
                sidebar.appendChild(box);
                
                // Click vào box answer để chuyển đến câu hỏi tương ứng
                box.addEventListener("click", function() {
                    currentQuestion = parseInt(this.getAttribute("data-index"));
                    showQuestion(currentQuestion);
                });
            });
            
            // Xử lý sự kiện khi chọn đáp án
            document.querySelectorAll(".form-check-input").forEach(input => {
                input.addEventListener("change", function () {
                    let qid = this.getAttribute("data-qid");
                    let box = document.querySelector(`.box-answer[data-qid='${qid}']`);
                    box.classList.add("selected");
                });
            });
            
            // Nút Previous
            prevBtn.addEventListener("click", function() {
                if (currentQuestion > 0) {
                    currentQuestion--;
                    showQuestion(currentQuestion);
                }
            });
            
            // Nút Next
            nextBtn.addEventListener("click", function() {
                if (currentQuestion < questions.length - 1) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                }
            });
            
            // Nút Submit
            logBtn.addEventListener("click", submitQuiz);
            
            // Hiển thị câu hỏi đầu tiên
            showQuestion(currentQuestion);
            
            // Bắt đầu đếm ngược
            startTimer();
            
            // Hàm hiển thị câu hỏi và context tương ứng
            function showQuestion(index) {
                // Ẩn tất cả câu hỏi và context
                questions.forEach(q => q.classList.remove("active"));
                contexts.forEach(c => c.classList.remove("active"));
                groupQuestion.forEach(g => g.classList.remove("active"));
                
                // Hiển thị câu hỏi hiện tại
                if (questions[index]) {
                    questions[index].classList.add("active");
                    
                    // Tìm group cha của câu hỏi hiện tại
                    const currentGroup = questions[index].closest('.question-group-wrapper');
                    if (currentGroup) {
                        // Hiển thị tất cả context-wrapper trong group này
                        const groupContexts = currentGroup.querySelectorAll('.context-wrapper');
                        groupContexts.forEach(context => {
                            context.classList.add("active");
                        });
                        
                        // Đánh dấu group là active
                        currentGroup.classList.add("active");
                    } else if (contexts[index]) {
                        // Nếu không thuộc group nào thì hiển thị context tương ứng
                        contexts[index].classList.add("active");
                    }
                }
                            
                // Cập nhật trạng thái nút
                prevBtn.disabled = index === 0;
                nextBtn.disabled = index === questions.length - 1;
                
                // Cuộn lên đầu trang
                window.scrollTo(0, 0);
            }
            
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
                const dateElement  = formatDateTimeForSQL(now);
                console.log(dateElement)



            function submitQuiz() {
                clearInterval(timerInterval);
                timerElement.textContent = "00:00";
                
                const results = [];
                const questionBoxes = document.querySelectorAll('.box-answer');
                
                questionBoxes.forEach((box, index) => {
                    const qid = box.getAttribute('data-qid');
                    const questionNumber = index + 1;
                    const selectedAnswer = document.querySelector(`.form-check-input[data-qid="${qid}"]:checked`);
                    
                    if (selectedAnswer) {
                        results.push({
                            question: questionNumber,
                            answer: selectedAnswer.value,
                            status: 'answered'
                        });
                    } else {
                        results.push({
                            question: questionNumber,
                            answer: null,
                            status: 'not answered'
                        });
                    }
                });
                
                // Calculate time taken
                const endTime = new Date();
                const timeTaken = (endTime - startTime) / 1000; // in seconds
                const minutes = Math.floor(timeTaken / 60);
                const seconds = Math.floor(timeTaken % 60);
                const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                // Compare with correct answers
                let correctCount = 0;
                let incorrectCount = 0;
                let unansweredCount = 0;
                
                const detailedResults = results.map(item => {
                    const correctAnswer = correct_answer[item.question.toString()];
                    let result = '';
                    
                    if (item.status === 'not answered') {
                        unansweredCount++;
                        result = 'Unanswered';
                    } else if (item.answer === correctAnswer) {
                        correctCount++;
                        result = 'Correct';
                    } else {
                        incorrectCount++;
                        result = 'Incorrect';
                    }
                    
                    return {
                        Question: item.question,
                        YourAnswer: item.answer || '-',
                        CorrectAnswer: correctAnswer,
                        Result: result
                    };
                });



                const userAns = results.map(item => {
                    return {
                        Question: item.question,
                        YourAnswer: item.answer || ' '
                    };
                });




                const estimatedReadingScore = Math.round((correctCount / 40) * 100);



                
                // Display results in console
                console.log("=== QUIZ RESULTS ===");
                console.log(`Time taken: ${timeString}`);
                console.log(`Total questions: ${results.length}`);
                console.log(`Correct answers: ${correctCount}`);
                console.log(`Incorrect answers: ${incorrectCount}`);
                console.log(`Unanswered questions: ${unansweredCount}`);
                console.log(`Your estimate band: ${estimatedReadingScore}`);

                console.table(detailedResults);

                
                // Display alert
                const answeredCount = results.filter(r => r.status === 'answered').length;
                const totalQuestions = results.length;
                
                // Disable all inputs and buttons
                document.querySelectorAll(".form-check-input").forEach(input => {
                    input.disabled = true;
                });
                prevBtn.disabled = true;
                nextBtn.disabled = true;
                logBtn.disabled = true;
                
                alert(`TIME'S UP!\nYou answered ${answeredCount}/${totalQuestions} questions.\nCorrect: ${correctCount}, Incorrect: ${incorrectCount}, Unanswered: ${unansweredCount}\nCheck console for details.`);
                
                



               
                /*var contentToCopy5 = document.getElementById("id_category").textContent;
                var contentToCopy7 = document.getElementById("correctanswerdiv").textContent;
            */

                document.getElementById("correct_percentage").value = `${correctCount}/${results.length}`;
                document.getElementById("testsavenumber").value = resultId;


                document.getElementById("total_question_number").value = `${results.length}`;
                document.getElementById("correct_number").value = `${correctCount}`;
                document.getElementById("incorrect_number").value = `${incorrectCount}`;
                document.getElementById("skip_number").value = `${unansweredCount}`;


                document.getElementById("overallband").value = `${estimatedReadingScore}`;

                document.getElementById("dateform").value = dateElement;
                document.getElementById("test_type").value = test_type;

                document.getElementById("testname").value = testname;
                document.getElementById("idtest").value = id_test;
                document.getElementById("useranswer").value = JSON.stringify(userAns);
                document.getElementById("timedotest").value = timeString;
                
            /*  
                document.getElementById("idcategory").value = contentToCopy5;
                document.getElementById("idtest").value = contentToCopy6;
                document.getElementById("correctanswer").value = contentToCopy7;
                */

                
                // Add a delay before submitting the form
                
            /*setTimeout(function() {
            // Automatically submit the form
            jQuery('#saveTopikReadingResult').submit();
            },0); // 5000 milliseconds = 5 seconds */



            return {
                    results: detailedResults,
                    stats: {
                        total: totalQuestions,
                        correct: correctCount,
                        incorrect: incorrectCount,
                        unanswered: unansweredCount,
                        timeTaken: timeString
                    }
                };

            }
        });





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
                    table: 'topik_reading_test_list',
                    change_token: '$token_need',
                    type_token: 'token',
                    payment_gate: 'token',
                    title: 'Renew test $testname with $id_test (TOPIK Reading) with $token_need (Buy $time_allow time do this test)',
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