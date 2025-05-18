<?php
/*
 * Template Name: Result Template
 * Template Post Type: ieltsreadingtest
 */




$post_id = get_the_ID();
$user_id = get_current_user_id();// Get the custom number field value
//$custom_number =intval(get_query_var('id_test'));
//$custom_number = get_post_meta($post_id, '_ieltsreadingtest_custom_number', true);
// Get the custom number field value
global $wpdb; // Use global wpdb object to query the DB

// Get testsavenumber from URL
$testsavenumber = get_query_var('testsavetopikreading');

$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM save_user_result_topik_reading WHERE testsavenumber = %d",
        $testsavenumber
    )
);
// Assign $custom_number using the id_test field from the query result if available
$custom_number = 0; // Default value
if (!empty($results)) {
    // Assuming you want the first result's id_test
    $custom_number = $results[0]->idtest;

}

echo "<script>console.log('Custom Number doing template: " . esc_js($custom_number) . "');</script>";

// Database credentials (update with your own database details)
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

// Truy vấn `question_choose` từ bảng `ielts_reading_test_list` theo `id_test`
$sql_test = "SELECT * FROM topik_reading_test_list WHERE id_test = ?";
$stmt_test = $conn->prepare($sql_test);

if ($stmt_test === false) {
    die('Lỗi MySQL prepare: ' . $conn->error);
}

$stmt_test->bind_param("s", $custom_number);
$stmt_test->execute();
$result_test = $stmt_test->get_result();
$site_url = get_site_url();


echo '
<script>

    
    var linkTestMain = "'.$site_url .'/test/topik/r/' . $custom_number . '";

    var currentLink = "'.$site_url .'/topik/r/result/' . $testsavenumber . '";

</script>
';

if ($result_test->num_rows > 0) {
    // Fetch test data if available
    



    
    $row_test = $result_test->fetch_assoc();

    $testname = $row_test['testname'];
    add_filter('document_title_parts', function ($title) use ($testname) {
        $title['title'] = $testname; // Use the $testname variable from the outer scope
        return $title;
    });
    


        // Clean the raw JSON by removing any empty keys
    $cleanedCorrectAnswer = preg_replace('/"":\s?"[^"]*"\s*,?/', '', $row_test['correct_answer']);

    // Decode the cleaned JSON

    // Regular expression to extract question number and answer pairs
    preg_match_all('/"(\d+)":\s?"([A-D])"/', $cleanedCorrectAnswer, $matches);

    // Check the results
    $correctAnswers = array_combine($matches[1], $matches[2]);



    // Debug: Check the cleaned and decoded result
    echo '<script>console.log("Cleaned correct_answer:", ' . json_encode($cleanedCorrectAnswer) . ');</script>';
    echo '<script>console.log("Extracted correctAnswers:", ' . json_encode($correctAnswers) . ');</script>';



    echo '
    <script>
        var linkTestMain = "'.$site_url.'/test/topik/r/' . $custom_number . '";
        var currentLink = "'.$site_url.'/topik/r/result/' . $testsavenumber . '";
    </script>
    ';

get_header(); // Gọi phần đầu trang (header.php)


// Đóng kết nối
$conn->close();





echo '
<script>
    // Truyền giá trị PHP sang JavaScript
    var testSaveNumber = ' . json_encode($testsavenumber) . ';
    console.log("Test Save Number:", testSaveNumber);
</script>
';

// Query database to find results by testsavenumber



if (!empty($results)) {
    $permissionLink = esc_html($results[0]->permission_link); // Lấy giá trị permission_link từ kết quả đầu tiên
    $test_username = esc_html($results[0]->username); // Lấy giá trị permission_link từ kết quả đầu tiên
    $current_user = wp_get_current_user();
    $current_user_name = $current_user->user_login;
    $post_id = get_the_ID();
    $testsavenumber = esc_html($results[0]->testsavenumber);

    // Lấy liên kết của bài viết hiện tại
    $post_link = get_permalink($post_id);
    echo '
    <script>
        var permissionLink = "' . $permissionLink . '";
        console.log("Permission Link:", permissionLink);
        
        var linkTest = "' . $post_link . '";
        var currentUsername = "' . $test_username . '";
        console.log("Current Username:", currentUsername);

        var testsavenumber = "' . $testsavenumber . '";
        var ajaxurl = "' . admin_url('admin-ajax.php') . '";
        
        console.log("Test Save Number:", testsavenumber);
        console.log("AJAX URL:", ajaxurl);
    </script>
';



    if(($permissionLink == "private" && $test_username == $current_user_name) || $permissionLink == "public" ||  current_user_can('administrator'))
    {
    // Display results
      foreach ($results as $result) {
          echo '<b style = "text-transform: uppercase;">Result and Explanation: ' . esc_html($result->testname) . '</b>';
          $userAnswers = json_decode($result->useranswer, true);

          echo '
            <div class="result-score-details">
                    <div class="row">
                        <div class="col-12 col-md-3">
                            <div class="result-stats-box">
                                <div class="result-stats-item">
                                    <i class="fa-solid fa-check" style="color: #0de71c;"></i>
                                    <span class="result-stats-label">Kết quả làm bài</span>
                                    <span class="result-stats-text">'. esc_html($result->correct_number) .'/'. esc_html($result->total_question_number) .'</span>
                                </div>
                                <br>
                                <div class="result-stats-item">
                                   <i class="fa-solid fa-chart-simple" style="color: #63E6BE;"></i>
                                    <span class="result-stats-label">Độ chính xác (#đúng/#tổng)</span>
                                    <span class="result-stats-text">66.7%</span>
                                </div>
                                <br>
                                <div class="result-stats-item">
                                    <i class="fa-solid fa-clock" style="color: #63E6BE;"></i>
                                    <span class="result-stats-label">Thời gian hoàn thành</span>
                                    <span class="result-stats-text">'. esc_html($result->timedotest) .'</span>
                                </div>
                                <br>
                                <div class="result-stats-item">
                                   <i class="fa-solid fa-calendar-days" style="color: #372a60;"></i>
                                    <span class="result-stats-label">Ngày làm bài: '. esc_html($result->dateform) .'</span>
                                </div>
                            </div>
                            <br>
                        </div>
                        <div class="col-12 col-md-9">
                            <div class="row">
                                <div class="col">
                                    <div class="result-score-box">
                                        <div class="result-score-icon text-correct"><span class="fas fa-check-circle"></span></div>
                                        <div class="result-score-icontext text-correct">Trả lời đúng</div>
                                        <div class="result-score-text">'. esc_html($result->correct_number) .'</div>
                                        <div class="result-score-sub"><span>câu hỏi</span></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="result-score-box">
                                        <div class="result-score-icon text-wrong"><span class="fas fa-times-circle"></span></div>
                                        <div class="result-score-icontext text-wrong" >Trả lời sai</div>
                                        <div class="result-score-text">'. esc_html($result->incorrect_number) .'</div>
                                        <div class="result-score-sub"><span>câu hỏi</span></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="result-score-box">
                                        <div class="result-score-icon text-unanswered"><span class="fas fa-minus-circle"></span></div>
                                        <div class="result-score-icontext text-unanswered">Bỏ qua</div>
                                        <div class="result-score-text">'. esc_html($result->skip_number) .'</div>
                                        <div class="result-score-sub"><span>câu hỏi</span></div>
                                    </div>
                                </div>
                                
                                <div class="col">
                                    <div class="result-score-box">
                                        <div class="result-score-icon text-score"><i class="fa-solid fa-flag fa-lg" style="color: #74C0FC;"></i></div>
                                        <div class="result-score-icontext text-score">Điểm</div>
                                        <div class="result-score-text text-score">'. esc_html($result->overallband) .'</div>
                                        <div class="result-score-sub"><span>Overall</span></div>

                                    </div>
                                </div>
                                
                            </div>
                            <br>
                            
                        </div>
                    </div>
                </div>

                <div class="modal-overlay" id="questionModal">
                    <div class="modal-content">
                        <span class="close-modal">&times;</span>
                        <div id="modalQuestionContent"></div>
                        <div class="modal-nav">
                            <button id="prevQuestion">Previous</button>
                            <button id="nextQuestion">Next</button>
                        </div>
                    </div>
                </div>

                <script>
                // Lưu trữ tất cả các câu hỏi và câu trả lời
                    var allQuestions = [];
                    var userAnswersData = {};
                    var correctAnswersData = {};
                    var currentModalQuestion = 0;

                    // Lấy nội dung câu hỏi từ testcode và dữ liệu trả lời
                    document.addEventListener("DOMContentLoaded", function() {
                        // Phân tích testcode để lấy các câu hỏi
                        var testcode = `' . $row_test['testcode'] . '`;
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(testcode, "text/html");
                        
                        var questionWrappers = doc.querySelectorAll(".question-wrapper");
                        questionWrappers.forEach(function(wrapper) {
                            var qid = wrapper.getAttribute("data-qid");
                            var questionNumber = wrapper.querySelector(".question-number strong").textContent;
                            var questionText = wrapper.querySelector(".question-text").innerHTML;
                            var answersHTML = wrapper.querySelector(".question-answers").innerHTML;
                            
                            allQuestions.push({
                                qid: qid,
                                number: questionNumber,
                                text: questionText,
                                answers: answersHTML
                            });
                        });
                        
                        // Xử lý userAnswers từ PHP
                        var userAnswersFromPHP = ' . json_encode($userAnswers) . ';
                        if (userAnswersFromPHP) {
                            userAnswersFromPHP.forEach(function(answer) {
                                var questionKey = answer.Question.toString();
                                userAnswersData[questionKey] = answer.YourAnswer;
                            });
                        }

                        // Xử lý correctAnswers từ PHP
                        var correctAnswersFromPHP = ' . json_encode($correctAnswers) . ';
                        if (correctAnswersFromPHP) {
                            // Đối với object, dùng Object.keys hoặc for...in
                            //Object.keys(correctAnswersFromPHP).forEach(function(key) {
                            //  correctAnswersData[key] = correctAnswersFromPHP[key];
                            //});
                            
                            // Hoặc cách khác:
                            for (var key in correctAnswersFromPHP) {
                                if (correctAnswersFromPHP.hasOwnProperty(key)) {
                                    correctAnswersData[key] = correctAnswersFromPHP[key];
                                }
                            }
                        }

                        
                        // Debug console
                        console.log("User Answers:", userAnswersData);
                        console.log("Correct Answers:", correctAnswersData);
                        
                        // Xử lý sự kiện click nút xem chi tiết
                        document.querySelectorAll(".details").forEach(function(button) {
                            button.addEventListener("click", function() {
                                var questionNum = this.id.replace("dtl-", "");
                                showQuestionModal(parseInt(questionNum) - 1); // Chuyển từ số thứ tự sang index (bắt đầu từ 0)
                            });
                        });
                        
                        // Xử lý sự kiện cho modal
                        var modal = document.getElementById("questionModal");
                        var closeBtn = document.querySelector(".close-modal");
                        
                        closeBtn.onclick = function() {
                            modal.style.display = "none";
                        }
                        
                        window.onclick = function(event) {
                            if (event.target == modal) {
                                modal.style.display = "none";
                            }
                        }
                        
                        // Xử lý nút next/previous
                        document.getElementById("prevQuestion").addEventListener("click", function() {
                            if (currentModalQuestion > 0) {
                                showQuestionModal(currentModalQuestion - 1);
                            }
                        });
                        
                        document.getElementById("nextQuestion").addEventListener("click", function() {
                            if (currentModalQuestion < allQuestions.length - 1) {
                                showQuestionModal(currentModalQuestion + 1);
                            }
                        });
                    });

                    function showQuestionModal(index) {
                        currentModalQuestion = index;
                        var modal = document.getElementById("questionModal");
                        var modalContent = document.getElementById("modalQuestionContent");
                        var prevBtn = document.getElementById("prevQuestion");
                        var nextBtn = document.getElementById("nextQuestion");
                        
                        var questionNum = (index + 1).toString();
                        var question = allQuestions[index];
                        var userAnswer = userAnswersData[questionNum] || "Not answered";
                        var correctAnswer = correctAnswersData[questionNum] || "N/A";
                        
                        // Xác định trạng thái câu trả lời
                        var answerStatus = "";
                        var statusClass = "";
                        
                        if (userAnswer === "Not answered" || userAnswer.trim() === "") {
                            answerStatus = "Not answered";
                            statusClass = "skipped";
                        } else if (userAnswer.toUpperCase() === correctAnswer.toUpperCase()) {
                            answerStatus = "Correct";
                            statusClass = "correct";
                        } else {
                            answerStatus = "Incorrect";
                            statusClass = "incorrect";
                        }
                        
                        // Cách 2: Sử dụng template literals
                        var answersHTML = question.answers;
                        answersHTML = answersHTML.replace(
                            `value="${correctAnswer}"`, 
                            `value="${correctAnswer}" checked style="accent-color: #2e7d32"`
                        );

                        if (userAnswer !== "Not answered" && userAnswer !== correctAnswer) {
                            answersHTML = answersHTML.replace(
                                `value="${userAnswer}"`, 
                                `value="${userAnswer}" checked style="accent-color: #c62828"`
                            );
                        }                  
                                                // Hiển thị câu hỏi và thông tin đáp án
                        modalContent.innerHTML = `
                            <div class="question-number"><strong>${question.number}</strong></div>
                            <div class="question-text">${question.text}</div>
                            <div class="question-answers">${answersHTML}</div>
                            
                            <div class="answer-info correct-answer-info">
                                <strong>Correct Answer:</strong> ${correctAnswer}
                            </div>
                            
                            <div class="answer-info user-answer-info">
                                <strong>Your Answer:</strong> ${userAnswer}
                            </div>
                            
                            <div class="answer-status ${statusClass}">
                                ${answerStatus}
                            </div>
                        `;
                        
                        // Cập nhật trạng thái nút previous/next
                        prevBtn.disabled = index === 0;
                        nextBtn.disabled = index === allQuestions.length - 1;
                        
                        // Hiển thị modal
                        modal.style.display = "flex";
                    }
                </script>

        ';
        echo '<a class="button-main" onclick="redirectToTest()">Làm lại bài thi</a>';
        echo '<a class="button-main" onclick="opensharePermission()">Chia sẻ bài làm</a>';
        echo '<a class="button-main" onclick="openRemarkTest()">Chấm lại</a>';
        echo '<a class="button-main" onclick="redoWrongAns()">Làm lại các câu sai</a>';
        echo '<a class="button-main" onclick="fullExplanation()">Chi tiết đáp án</a><br><br>';

            // Process user answers
                
            $groupedQuestionsForRemark = [];
            
            // Khởi tạo biến đếm
            $correctCount = 0;
            $wrongCount = 0;
            $skipCount = 0;
            $totalQuestions = count($userAnswers);
            
            echo '<div class="result-answers-list">';
            foreach ($userAnswers as $answer) {
                $questionNumber = $answer['Question'];
                $userAnswer = $answer['YourAnswer'];
                
                // Đảm bảo question number là string
                $questionKey = (string)$questionNumber;
                
                // Initialize fallback value for correctAnswer
                $correctAnswer = 'N/A';
            
                // Check if the decoded JSON is an array and if the key exists
                if (is_array($correctAnswers) && array_key_exists($questionKey, $correctAnswers)) {
                    $correctAnswer = isset($correctAnswers[$questionKey]) ? $correctAnswers[$questionKey] : 'N/A';
                }
            
                // Xác định trạng thái câu trả lời và tăng biến đếm
                $isCorrect = false;
                $isSkipped = false;
                
                if (empty(trim($userAnswer))) {
                    $skipCount++;
                    $isSkipped = true;
                    $statusIcon = 'Not answer'; // Biểu tượng skip
                } elseif (strtoupper(trim($userAnswer)) === strtoupper(trim($correctAnswer))) {
                    $correctCount++;
                    $isCorrect = true;
                    $statusIcon = '✅'; // Biểu tượng đúng
                } else {
                    $wrongCount++;
                    $statusIcon = '❌'; // Biểu tượng sai
                }
            
                // Debug: Output the results in the console
                echo '<script>console.log(' . json_encode([
                    'question' => $questionNumber,
                    'key' => $questionKey,
                    'userAnswer' => $userAnswer,
                    'correctAnswer' => $correctAnswer,
                    'isCorrect' => $isCorrect,
                    'isSkipped' => $isSkipped,
                    'status' => $statusIcon
                ], JSON_HEX_TAG) . ');</script>';
            
                // Hiển thị
                echo '
                
                
                <div class="result-answers-item">';
                    echo '<div class="question-row">';
                    echo '<span class="question-number"><strong>' . esc_html($questionNumber) . '</strong></span>';
                    echo '<span class="correct-answer"><strong style="color: green">' . esc_html($correctAnswer) . ': </strong></span>';
                    echo '<span class="user-answer"><em>' . esc_html($userAnswer) . '</em></span>';
                    echo '<span class="result-icon">' . $statusIcon . '</span>';
                    echo '<button class="details" id="dtl-' . esc_html($questionNumber) . '">Xem chi tiết</button>';
                    echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            
           
            
            // Đẩy sang JS nếu cần
            $stats = [
                'correct' => $correctCount,
                'wrong' => $wrongCount,
                'skipped' => $skipCount,
                'total' => $totalQuestions
            ];
            
            $encodedGroupedQuestions = json_encode($groupedQuestionsForRemark, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            echo '<script type="text/javascript">';


            echo 'var totalNewCorrectAns = ' . $correctCount . ';';
            echo 'var totalNewIncorrectAns = ' . $wrongCount . ';';
            echo 'var totalNewSkipAns = ' . $skipCount . ';';

            echo 'var groupedQuestions = ' . $encodedGroupedQuestions . ';';
            echo 'var answerStats = ' . json_encode($stats) . ';';
            echo 'console.log("Thống kê bài làm:", answerStats);';
            echo '</script>';
            }


    ?>




<html lang="en">
<head>
   
<style>
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
    
    .close-modal {
        float: right;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }
    body {
        font-family: Arial, sans-serif;
        font-size: 18px;
        padding:10px;
    }
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -.75rem;
        margin-left: -.75rem;
    }

    .col-12 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .result-stats-box {
        padding: 1.5rem 1rem;
        background-color: #f8f9fa;
        border: 1px solid #efefef;
        box-shadow: 0 2px 8px 0 rgba(0, 0, 0, .05);
        display: flex
    ;
        flex-direction: column;
    }

    .result-stats-item {
        display: flex
    ;
        justify-content: space-between;
    }
    .result-stats-label {
        margin-left: .5rem;
        margin-right: .5rem;
        flex-grow: 1;
    }
    .result-stats-icon {
        width: 25px;
    }
    .result-stats-text {
        width: 70px;
        font-weight: 500;
    }
    @media (min-width: 768px) {
        .col-md-9 {
            flex: 0 0 75%;
            max-width: 75%;
        }
    }

    .col {
        flex-basis: 0;
        flex-grow: 1;
        max-width: 100%;
    }

    .result-score-box {
        display: flex;
        flex-direction: column;
        background-color: #fff;
        padding: 1.5rem 1rem;
        border: 1px solid #efefef;
        box-shadow: 0 2px 8px 0 rgba(0, 0, 0, .05);
        border-radius: .65rem;
        margin-bottom: 1rem;
        align-items: center;
        justify-content: flex-start;
    }
    .text-score {
        color: #35509a;
    }
    .result-score-icon {
        font-size: 1.4rem;
        font-weight: 500;
    }

    .text-correct {
        color: #3cb46e;
    }
    .text-wrong {
        color: #e43a45;
    }
    @media (min-width: 768px) {
        .col-md-3 {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }
    .result-answers-list{
        -webkit-columns:2;
        columns: 2;
    }
    .question-number {
        margin-right: .5rem;
    }
    .question-number strong {
        border-radius: 50%;
        background-color: #e8f2ff;
        color: #35509a;
        width: 35px;
        height: 35px;
        line-height: 35px;
        font-size: 15px;
        text-align: center;
        display: inline-block;
    }
    .result-answers-item {
        margin-bottom: 1rem;
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


    .question-range {
        background-color: #e9ecef; /* Màu xám cho phần câu hỏi */
        padding: 14px; /* Khoảng cách bên trong */
        margin-top: 30px; /* Tăng giá trị để tránh che khuất bởi header */
        width: 97%; /* Không chiếm chiều rộng đầy đủ */
        margin: 0 auto; /* Căn giữa */
    }




    #content-details {
        width: 100%;
        display:block;
    }
    #header{
        height:40px
    }
    .details{

        display: inline-block;
        padding: 5px 15px;
        margin: 5px;
        background-color:rgb(87, 81, 165);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        border: none;
        font-size: 10px;
        transition: background-color 0.3s ease;
    }


    .popup-content {
        background-color: #fefefe;
        margin: 5% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        max-height: 70%;
        overflow: auto;
        width: 70%; /* Could be more or less, depending on screen size */
    }
    .popup {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
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
        bottom: 0;
        width: 100%;
        background-color: #f1f1f1;
        border-top: 1px solid #ccc;
        text-align: center;
        z-index: 1000;
    }

    #part-navigation {
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


    .detail-display{
        border: 3px solid;
    padding: 10px;
    box-shadow: 5px 10px;
        width: 95%;
        margin-left: auto;
        margin-right: auto;
        display: block;
    }
    .correct-ans{
        color: green
    }



    /* CSS */
    .button-10 {
  display: inline-block;
  padding: 10px 20px;
  margin: 5px;
  background-color: #4CAF50;
  color: white;
  text-decoration: none; /* bỏ dấu gạch dưới */
  border-radius: 8px;
  border: none;
  font-size: 14px;
  transition: background-color 0.3s ease;
}

.button-10:hover {
  background-color: #45a049;
  cursor: pointer;
}



    .group-control-part-btn{
        /*position: fixed;*/
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

    .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
    }

    .switch input { 
    opacity: 0;
    width: 0;
    height: 0;
    }

    .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }
    /* Bố trí toggle switch và nhãn trên cùng một dòng */
    .permission-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 10px 0;
    }

    .permission-row p {
    margin: 0;
    }

    input:checked + .slider {
    background-color: #2196F3;
    }

    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }

    .slider.round:before {
    border-radius: 50%;
    }


</style>
</head>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>

<body>
    
<div id="remark_popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closeRemarkTest()">&times;</span>
        <i>Một số đề thi sau khi được sửa lại đáp án nhưng vẫn chưa được cập nhập ở bài làm của bạn<br> Bạn có thể sử dụng nút ở dưới để chấm bài cũ và nhận điểm mới !</i>
        <br>
        <button onclick = "remarkTest()"id = "remarkTest" class = "remarkTestBtn">Chấm lại bài</button>
        <div id = "remarkArea">
            <div id = "remarkPoint" style = "display:none">
                <p id ="test-type">Loại đề</p>
                <div id ="old-res">
                    <b>Lưu đáp án cũ</b>
                    <p id = "old-correct-ans">Số câu đúng cũ: </p> 
                    <p id = "old-incorrect-ans">Số câu sai cũ: </p>
                    <p id = "old-skip-ans">Số câu bỏ qua cũ: </p>
                    <p id = "old-overall">Tổng điểm Overall cũ: </p>
                </div>

                <div id ="new-res">
                    <b>Lưu đáp án mới</b>
                    <p id = "new-correct-ans">Số câu đúng mới: </p>
                    <p id = "new-incorrect-ans">Số câu sai mới: </p>
                    <p id = "new-skip-ans">Số câu bỏ qua mói: </p>
                    <p id = "new-overall">Tổng điểm Overall mới: </p>
                </div>
                <div id ="track_change_ans">
                    <p id = "track_change_note"></p>
                    <button id ="saveNewBtn" style = "display:none">Lưu kết quả mới</button>
                </div>
            </div>
            <div id ="warningRemark"></div>
        </div>
    </div>
</div>




<div id="share_popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closesharePermission()">&times;</span>
        <i>Đáp án bài thi của bạn được đặt mặc định là Private (Riêng tư)</i>
        <div id="permissionShareContent"></div>
        <button onclick="coppyShareContentBtn()">Copy Link</button>
        <div id="coppyShareContent"></div>
        <div id="warningRemark"></div>
    </div>
</div>




    </body>
<script>

function redirectToTest(){
    
    window.location.href = `${linkTestMain}`;
//    console.log(`Linh ${linkTestMain}`);

}



function closeRemarkTest() {
    document.getElementById('remark_popup').style.display = 'none';
}

function openRemarkTest(){
    document.getElementById('remark_popup').style.display = 'block';
}
function opensharePermission() {
    document.getElementById('share_popup').style.display = 'block';

    // Define the initial state of the switch based on permissionLink
    const isPublic = permissionLink === "public";

    // Set the content for the popup dynamically
    document.getElementById('permissionShareContent').innerHTML = `
        <div class="permission-row">
            <p>Private</p>
            <label class="switch">
                <input type="checkbox" id="updatePermission" ${isPublic ? "checked" : ""}>
                <span class="slider round"></span>
            </label>
            <p>Public</p>
        </div>
        Your permission: ${permissionLink}<br>
    `;

    // Display the page URL
    document.getElementById("coppyShareContent").innerHTML =  window.location.href;

    // Attach the event listener to the toggle switch with ID 'updatePermission'
    document.getElementById("updatePermission").addEventListener("change", function () {
        togglePermission(this);
    });
}

function closesharePermission() {
    document.getElementById('share_popup').style.display = 'none';
}


function coppyShareContentBtn() {
    // Lấy nội dung cần sao chép
    const copyText = document.getElementById("coppyShareContent").innerText;

    // Sao chép nội dung vào clipboard
    navigator.clipboard.writeText(copyText).then(() => {
        // Thông báo khi sao chép thành công
        alert("Copied the text: " + copyText);
    }).catch((err) => {
        console.error("Failed to copy text: ", err);
        alert("Failed to copy the text.");
    });
}



document.addEventListener("DOMContentLoaded", function () {
    const updatePermission = document.getElementById("updatePermission");

    if (updatePermission) {
        updatePermission.addEventListener("change", function (event) {
            togglePermission(event.target);
        });
    }
});

function togglePermission(checkbox) {
    const newPermission = checkbox.checked ? "public" : "private";
    console.log('New permission:', newPermission); // In ra quyền mới

    // Gửi yêu cầu AJAX để cập nhật trạng thái
    const data = {
        action: "update_permission_link",
        testsavenumber: testsavenumber, // Giá trị testsavenumber từ server
        permission_link: newPermission,
        type_test: "topik_reading"
    };

    // Gửi yêu cầu AJAX
    fetch(ajaxurl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data) // Sử dụng URLSearchParams để gửi dữ liệu dạng form-urlencoded
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert("Permission updated to: " + newPermission);
        } else {
            alert("Failed to update permission: " + result.message); // Hiển thị thông báo chi tiết lỗi
        }
    })
    .catch(error => {
        console.error("Error updating permission:", error);
    });
}



let Alreadyremark = false;

let newOverallBand;


function checkUpdateOverall()
{
    if (totalNewCorrectAns + totalNewIncorrectAns + totalNewSkipAns == 40)
    {
        const newOverallBand = Math.round((totalNewCorrectAns / 40) * 100);


        document.getElementById("new-overall").innerText += newOverallBand;

    }

}


function remarkTest(){
    if (!Alreadyremark){
      
        // Update the content with the correct number
        document.getElementById("old-correct-ans").innerText += oldCorrectNumber;
        document.getElementById("old-incorrect-ans").innerText += oldIncorrectNumber;
        document.getElementById("old-skip-ans").innerText += oldSkipNumber;
        document.getElementById("old-overall").innerText += oldOverallBand;
        checkUpdateOverall();


        document.getElementById("new-correct-ans").innerText += totalNewCorrectAns;
        document.getElementById("new-incorrect-ans").innerText += totalNewIncorrectAns;
        document.getElementById("new-skip-ans").innerText += totalNewSkipAns;

        if (oldCorrectNumber != totalNewCorrectAns || oldIncorrectNumber != totalNewIncorrectAns || oldSkipNumber != totalNewSkipAns)
        {
            document.getElementById("track_change_note").innerHTML = `Đáp án có sự thay đổi, hãy ấn vào nút Lưu kết quả để cập nhập`;
            document.getElementById("saveNewBtn").style.display = 'block';
        }
        else{
            document.getElementById("track_change_note").innerHTML = `Đáp án vẫn giữ nguyên, không có sự thay đổi nào !!!`;

        }

        // Display the remarkPoint section
        document.getElementById("remarkPoint").style.display = 'block';
        Alreadyremark = true;
    }
    else{
        document.getElementById("warningRemark").innerHTML = "<i>Bạn đã chấm lại kết quả thêm yêu cầu mới nhất và đã lưu vào hệ thống. Nếu phát hiện kết quả có lỗi hãy report !</i>"
    }
}




document.getElementById("saveNewBtn").addEventListener("click", function () {
    const newCorrectAnsNumber = totalNewCorrectAns;
    const newIncorrectAnsNumber = totalNewIncorrectAns;
    const newSkipAnsNumber = totalNewSkipAns;
    const newOverallBandNumber = newOverallBand; // Giới hạn 1 chữ số thập phân

    // Gửi dữ liệu qua AJAX để cập nhật cơ sở dữ liệu
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'update_topik_reading_results',
            testsavenumber: testSaveNumber,
            correct_number: newCorrectAnsNumber,
            incorrect_number: newIncorrectAnsNumber,
            skip_number: newSkipAnsNumber,
            overallband: newOverallBandNumber
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Làm mới trang nếu cập nhật thành công
                alert('Kết quả đã được lưu thành công!');
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra trong khi cập nhật!');
        });
});



</script>
    <!-- <script src="/wordpress/contents/themes/tutorstarter/ielts-reading-tookit/script_result_3.js"></script> -->
<script>
        var ajaxUrl = <?php echo json_encode(admin_url("admin-ajax.php")); ?>;
        const oldCorrectNumber = <?php echo json_encode(esc_html($result->correct_number)); ?>;
        const oldIncorrectNumber = <?php echo json_encode(esc_html($result->incorrect_number)); ?>;
        const oldSkipNumber = <?php echo json_encode(esc_html($result->skip_number)); ?>;
        const oldOverallBand = <?php echo json_encode(esc_html($result->overallband)); ?>;

        

function redoWrongAns(){
    
  window.location.href = currentLink + "/practice";
}
function fullExplanation(){
   
   window.location.href = currentLink + "/explanation";
 }


</script>
</html>

<?php
if ( comments_open() || get_comments_number() ) :
    comments_template();
endif; 
get_footer();

}
else{
    get_header();
    echo'<b>You have no permission to view this test</b><br>';
    echo"<i>This test's result is on private mode. This mean only owner of this test can view it. If you are owner of this test, you should login at that account. Otherwise, you may contact the owner to change setting of this result to Public mode so anyone can view it</i>";
    
} 
}else {
        echo 'Not found ID test';


    // If no results with testsavenumber
   // echo '<p>Không có test nào với testsavenumber này.</p>';
}
} else {
    echo 'Not found test';
}
