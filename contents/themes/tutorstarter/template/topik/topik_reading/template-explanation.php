<?php
/*
 * Template Name: Start Doing Test Topik Reading
 * Template Post Type: Topik Explanation
 
 */


if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();

    $testsavenumber = get_query_var('testsavetopikreading');
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;
    global $wpdb; // Use global wpdb object to query the DB

        
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM save_user_result_topik_reading WHERE testsavenumber = %s",
            $testsavenumber
        )
    );


    // Assign $custom_number using the id_test field from the query result if available
    $custom_number = 0; // Default value
    if (!empty($results)) {
        // Assuming you want the first result's id_test
        $custom_number = $results[0]->idtest;

    }



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



 // Create result_id
 $site_url = get_site_url();

 echo "<script> 
       
        var siteUrl = '" .
        $site_url .
        "';
       


    </script>";



    $id_test = $custom_number;

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

        .loader {
            width: fit-content;
            font-size: 40px;
            font-family: system-ui,sans-serif;
            font-weight: bold;
            text-transform: uppercase;
            color: #0000;
            -webkit-text-stroke: 1px #000;
            background: conic-gradient(#000 0 0) text;
            animation: l8 2s linear infinite;
            }
            .loader:before {
            content: "Loading your result";
            }
            @keyframes l8 {
            0%,2%,8%,11%,15%,21%,30%,32%,35%,40%,46%,47%,53%,61%,70%,72%,77%,80%,86%   {background-size: 0    0   }
            1%,9%,10%,16%,20%,31%,34%,41%,45%,48%,52%,55%,60%,73%,76%,81%,85%,96%,100% {background-size: 100% 100%}
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

                .answer-feedback {
    margin-top: 15px;
    padding: 10px;
    border-radius: 5px;
    background: #f8f9fa;
    border-left: 4px solid #ddd;
}

.answer-row {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
}

.answer-col {
    margin: 5px 0;
    min-width: 120px;
}

.user-answer.correct {
    color: green;
    font-weight: bold;
}

.user-answer.incorrect {
    color: red;
    font-weight: bold;
}

.correct-answer {
    color: green;
    font-weight: bold;
}

.answer-status.correct {
    color: green;
    font-weight: bold;
    text-align: center;
    margin-top: 5px;
}

.answer-status.incorrect {
    color: red;
    font-weight: bold;
    text-align: center;
    margin-top: 5px;
}
    </style>
</head>
<body onload = "main()">

    <div class="container-content">
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


        <div id="quiz-container1" style="display: none;">
            <div class = "header-content"><?php echo $testname ?></div>

            <?php echo html_entity_decode($testcode); ?> <!-- Hiển thị HTML từ database mà không escape -->
            <div id ="userAnsAndCorrectDiv"></div>
            
            <div class="navigation1">
                <button class="nav-btn" id="prevBtn" disabled>Previous</button>
                <button class="nav-btn" id="nextBtn">Next</button>
            </div>
        </div>

        <div id="sidebar1" style="display: none;">
            <h3>Answers</h3>
            <div id="boxanswers"></div>
            <button id="logBtn">Submit Answers</button>


    
  

        </div>
    </div>

    <script>
        function main(){
            console.log("Passed Main");
            

            setTimeout(function(){
                console.log("Show Test!");
                document.getElementById("start_test").style.display="block";
                
                document.getElementById("welcome").style.display="block";

            }, 1000);
            
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
            table_test: 'topik_reading_test_list',

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
    // Ẩn phần chuẩn bị
    document.getElementById("test-prepare").style.display = "none";
    
    // Hiển thị test và sidebar
    document.getElementById("quiz-container1").style.display = "block";
    document.getElementById("sidebar1").style.display = "block";
    
    // Khởi tạo timer và các chức năng khác
}


        
        document.addEventListener("DOMContentLoaded", function () {
            const questions = document.querySelectorAll(".question-wrapper");
            const contexts = document.querySelectorAll(".context-wrapper");
            const groupQuestion = document.querySelectorAll(".question-group-wrapper");

            const sidebar = document.getElementById("boxanswers");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const logBtn = document.getElementById("logBtn");
            let currentQuestion = 0;
            
            // Cài đặt timer 60 phút
          
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
            
            // Hàm hiển thị câu hỏi và context tương ứng
            function showQuestion(index) {
                showAnswerFeedback();
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
            
            // Hàm nộp bài
            const currentDate = new Date();

            const day = currentDate.getDate();
            const month = currentDate.getMonth() + 1; // Adding 1 because getMonth() returns zero-based month index
            const year = currentDate.getFullYear();

                        // Display the date
            const dateElement = `${year}-${month}-${day}`;




            function showAnswerFeedback() {
                const correctAnswers = correct_answer;
                const userAnswers = <?php 
    if (!empty($results) && isset($results[0]->useranswer)) {
        $decoded = json_decode($results[0]->useranswer, true);
        echo json_encode(is_array($decoded) ? $decoded : []);
    } else {
        echo '[]';
    }
?>;


    // Lấy dữ liệu từ các câu hỏi đang được chọn
    const results = [];
    document.querySelectorAll('.question-wrapper').forEach((question, index) => {
        const questionNum = index + 1; // Số thứ tự câu hỏi (bắt đầu từ 1)
        const selectedAnswer = question.querySelector('.form-check-input:checked');
        
        results.push({
            question: questionNum,
            answer: selectedAnswer ? selectedAnswer.value : null,
            status: selectedAnswer ? 'answered' : 'not answered'
        });
    });

    // Lặp qua tất cả các câu hỏi
    document.querySelectorAll('.question-wrapper').forEach((question, index) => {
        const questionNum = index + 1;
        
        // 1. Tìm đáp án đang được chọn
        const currentResult = results.find(r => r.question === questionNum);
        const selectedAnswer = currentResult?.answer || null; // Luôn trả về null nếu undefined
        
        // 2. Tìm đáp án đã lưu
        const userAnswerObj = userAnswers.find(u => u.Question == questionNum);
        const savedUserAnswer = userAnswerObj?.YourAnswer || null;
        
        // 3. Xác định đáp án hiển thị (ưu tiên đáp án đang chọn)
        const displayUserAnswer = selectedAnswer !== null 
            ? selectedAnswer 
            : (savedUserAnswer !== null ? savedUserAnswer : ' ');
        
        // 4. Lấy đáp án đúng với kiểm tra null
        const correctAnswer = (correctAnswers && correctAnswers[questionNum]) || '';
        
        // 5. Kiểm tra đúng/sai AN TOÀN (xử lý cả null/undefined)
        const isCorrect = (displayUserAnswer?.toString()?.trim() || '') === 
                         (correctAnswer?.toString()?.trim() || '');
        
        // Tạo HTML hiển thị
        const feedbackHTML = `
            <div class="answer-feedback">
                <div class="answer-row">
                    <div class="answer-col">
                        <strong>Your answer:</strong> 
                        <span class="user-answer ${isCorrect ? 'correct' : 'incorrect'}">
                            ${displayUserAnswer || 'Not answered'}
                        </span>
                    </div>
                    <div class="answer-col">
                        <strong>Correct answer:</strong> 
                        <span class="correct-answer">${correctAnswer}</span>
                    </div>
                </div>
                <div class="answer-status ${isCorrect ? 'correct' : 'incorrect'}">
                    ${isCorrect ? '✓ Correct' : '✗ Incorrect'}
                </div>
            </div>
        `;
        
        // Chèn vào DOM
        const questionContent = question.querySelector('.question-content') || question;
        
        // Xóa feedback cũ nếu có
        const oldFeedback = questionContent.querySelector('.answer-feedback');
        if (oldFeedback) oldFeedback.remove();
        
        questionContent.insertAdjacentHTML('beforeend', feedbackHTML);
    });
}


            
function submitQuiz() {
    

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
    // Get user answers from PHP with proper error handling
const userAnswers = <?php 
    if (!empty($results) && isset($results[0]->useranswer)) {
        $decoded = json_decode($results[0]->useranswer, true);
        echo json_encode(is_array($decoded) ? $decoded : []);
    } else {
        echo '[]';
    }
?>;
console.log(correct_answer)
const correctAnswers = correct_answer;

    // Validate correctAnswers exists and is an object
    if (typeof correctAnswers !== 'object' || correctAnswers === null) {
        console.error('Correct answers data is invalid:', correctAnswers);
        alert('Error: Could not load correct answers. Please try again.');
        return;
    }
    
    const detailedResults = results.map(item => {
    // Chuẩn hóa key thành string (phòng trường hợp số hoặc chuỗi)
    const questionKey = item.question.toString();
    
    // Lấy correct answer với fallback rõ ràng
    const correctAnswer = correctAnswers?.[questionKey]?.trim() || '[Không có đáp án]';
    
    // Lấy user answer
    const userAnswerObj = userAnswers?.find(u => 
        u.Question == item.question || 
        u.Question?.toString() === questionKey
    );
    const userAnswer = userAnswerObj?.YourAnswer?.trim() || ' ';

    // Xác định trạng thái
    let status = 'Unanswered';
    if (item.status === 'answered') {
        status = (userAnswer === correctAnswer) ? 'Correct' : 'Incorrect';
    }

    return {
        Question: item.question,
        YourAnswer: userAnswer,
        CorrectAnswer: correctAnswer,
        Status: status
    };
});

    // Display the results in a table format
    let resultHTML = '<table class="result-table" style="width:100%; border-collapse:collapse; margin-top:20px;">';
    resultHTML += '<tr><th style="border:1px solid #ddd; padding:8px;">Question</th><th style="border:1px solid #ddd; padding:8px;">Your Answer</th><th style="border:1px solid #ddd; padding:8px;">Correct Answer</th><th style="border:1px solid #ddd; padding:8px;">Result</th></tr>';
    
    detailedResults.forEach(item => {
        const rowColor = item.Status === 'Correct' ? 'background-color:#ddffdd' : 'background-color:#ffdddd';
        resultHTML += `<tr style="${rowColor}">`;
        resultHTML += `<td style="border:1px solid #ddd; padding:8px;">${item.Question}</td>`;
        resultHTML += `<td style="border:1px solid #ddd; padding:8px;">${item.YourAnswer}</td>`;
        resultHTML += `<td style="border:1px solid #ddd; padding:8px;">${item.CorrectAnswer}</td>`;
        resultHTML += `<td style="border:1px solid #ddd; padding:8px;">${item.Status}</td>`;
        resultHTML += '</tr>';
    });
    
    resultHTML += '</table>';
    
    // Create a modal to display the results
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = '0';
    modal.style.left = '0';
    modal.style.width = '100%';
    modal.style.height = '100%';
    modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
    modal.style.zIndex = '2000';
    modal.style.overflow = 'auto';
    modal.style.padding = '20px';
    modal.style.boxSizing = 'border-box';
    
    const modalContent = document.createElement('div');
    modalContent.style.backgroundColor = 'white';
    modalContent.style.padding = '20px';
    modalContent.style.borderRadius = '5px';
    modalContent.style.maxWidth = '800px';
    modalContent.style.margin = '0 auto';
    
    const closeBtn = document.createElement('button');
    closeBtn.textContent = 'Close';
    closeBtn.style.padding = '10px 20px';
    closeBtn.style.backgroundColor = '#4CAF50';
    closeBtn.style.color = 'white';
    closeBtn.style.border = 'none';
    closeBtn.style.borderRadius = '4px';
    closeBtn.style.marginTop = '20px';
    closeBtn.style.cursor = 'pointer';
    
    const summary = document.createElement('div');
    summary.innerHTML = `
        <h2>Quiz Results</h2>
        <p>Total questions: ${results.length}</p>
        <p>Correct answers: </p>
        <p>Incorrect answers: </p>
        <p>Unanswered questions: </p>
        <h3>Detailed Results:</h3>
    `;
    
    modalContent.appendChild(summary);
    modalContent.innerHTML += resultHTML;
    modalContent.appendChild(closeBtn);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    closeBtn.addEventListener('click', () => {
        document.body.removeChild(modal);
    });
    
                
                




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