<?php
/*
 * Template Name: Result Template
 * Template Post Type: ieltslisteningtest
 */




$post_id = get_the_ID();
$user_id = get_current_user_id();// Get the custom number field value
//$custom_number =intval(get_query_var('id_test'));
//$custom_number = get_post_meta($post_id, '_ieltslisteningtest_custom_number', true);
// Get the custom number field value
global $wpdb;
$testsavenumber = get_query_var('testsaveieltslistening');
// Query database to find results by testsavenumber
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM save_user_result_ielts_listening WHERE testsavenumber = %s",
        $testsavenumber
    )
);




$custom_number = 0; // Default value
if (!empty($results)) {
    // Assuming you want the first result's id_test
    $custom_number = $results[0]->idtest;
    $testname = $results[0]->testname;

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

// Truy vấn `question_choose` từ bảng `ielts_listening_test_list` theo `id_test`
$sql_test = "SELECT testname, question_choose FROM ielts_listening_test_list WHERE id_test = ?";
$stmt_test = $conn->prepare($sql_test);

if ($stmt_test === false) {
    die('Lỗi MySQL prepare: ' . $conn->error);
}

$stmt_test->bind_param("s", $custom_number);
$stmt_test->execute();
$result_test = $stmt_test->get_result();
$site_url = get_site_url();

if ($result_test->num_rows > 0) {
    // Lấy các ID từ question_choose (ví dụ: "1001,2001,3001")
    $row_test = $result_test->fetch_assoc();
    $testname = $row_test['testname'];
    add_filter('document_title_parts', function ($title) use ($testname) {
        $title['title'] = $testname; // Use the $testname variable from the outer scope
        return $title;
    });

    
    $question_choose = $row_test['question_choose'];
    $id_parts = explode(",", $question_choose); // Chuyển thành mảng ID

    $part = []; // Mảng chứa dữ liệu của các phần
    $previous_part_questions = 0; // Biến lưu trữ số câu hỏi của phần trước
    $filterTypeQuestion = [];
    // Lặp qua từng id_part và truy vấn bảng tương ứng
    foreach ($id_parts as $index => $id_part) {
        // Xác định bảng và câu lệnh SQL tương ứng dựa trên index của part
        switch ($index) {
            case 0:
                $sql_part = "SELECT part, duration, number_question_of_this_part, group_question,audio_link, category 
                             FROM ielts_listening_part_1_question WHERE id_part = ?";
                break;
            case 1:
                $sql_part = "SELECT part, duration, number_question_of_this_part, group_question,audio_link, category 
                             FROM ielts_listening_part_2_question WHERE id_part = ?";
                break;
            case 2:
                $sql_part = "SELECT part, duration, number_question_of_this_part, group_question,audio_link, category 
                             FROM ielts_listening_part_3_question WHERE id_part = ?";
                break;
            case 3:
                $sql_part = "SELECT part, duration, number_question_of_this_part, group_question,audio_link, category 
                             FROM ielts_listening_part_4_question WHERE id_part = ?";
                break;
            default:
                continue 2; 
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
                'audio_link' => $row['audio_link'],
                'number_question_of_this_part' => '10',
                'duration' => '10',
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
} else {
    echo '<script type="text/javascript">console.error("Không tìm thấy test với custom number: ' . $custom_number . '");</script>';
}



get_header(); // Gọi phần đầu trang (header.php)



// Đóng kết nối
$conn->close();





// Get testsavenumber from URL
echo '
<script>
    // Truyền giá trị PHP sang JavaScript
    var testSaveNumber = ' . json_encode($testsavenumber) . ';
    console.log("Test Save Number:", testSaveNumber);
</script>
';



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
        var linkTestMain = "'.$site_url .'/test/ielts/l/' . $custom_number . '";

        
        var linkTest = "' . $post_link . '";
        var currentUsername = "' . $test_username . '";
        console.log("Current Username:", currentUsername);

        var testsavenumber = "' . $testsavenumber . '";
        var ajaxurl = "' . admin_url('admin-ajax.php') . '";
        
        console.log("Test Save Number:", testsavenumber);
    </script>
';



    if(($permissionLink == "private" && $test_username == $current_user_name) || $permissionLink == "public" ||  current_user_can('administrator'))
    {
    // Display results
      foreach ($results as $result) {
          echo '<b style = "text-transform: uppercase;">Result and Explanation: ' . esc_html($result->testname) . '</b>';

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
        ';
        echo '<a class="button-main" onclick="redirectToTest()">Làm lại bài thi</a>';
        echo '<a class="button-main" onclick="opensharePermission()">Chia sẻ bài làm</a>';
        echo '<a class="button-main" onclick="openRemarkTest()">Chấm lại</a>';
        echo '<a class="button-main" onclick="redoWrongAns()">Làm lại các câu sai</a>';
        echo '<a class="button-main" onclick="fullExplanation()">Chi tiết đáp án </a><br><br>';


    
        // Process user answers
        $userAnswers = $result->useranswer; // Assuming this is a string
        $questions = explode('Question:', $userAnswers); // Split by "Question:"
    
        // Group questions by parts
        $groupedQuestions = [];  // Initialize as an empty array
        $groupedQuestionsForRemark = []; // Initialize once
        foreach ($questions as $questionData) {
            if (trim($questionData) === '') continue;
    
            // Extract data for each question
            preg_match('/(\d+), Part: (\d+), User Answer: (.*?)(?=Question:|$)/', $questionData, $matches);
    
            if (count($matches) === 4) {
                $questionNumber = $matches[1]; // Question number
                $partNumber = $matches[2];     // Part number
                $userAnswer = trim($matches[3]); // User's answer
    
                $groupedQuestions[$partNumber][] = [
                    'questionNumber' => $questionNumber,
                    'userAnswer' => $userAnswer,
                ];
                $groupedQuestionsForRemark[] = [
                    'questionNumber' => $questionNumber,
                    'userAnswer' => $userAnswer,
                ];
            }
        }
        $encodedGroupedQuestions = json_encode($groupedQuestionsForRemark, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

        echo '<script type="text/javascript">
            // Chuyển dữ liệu PHP thành JSON và gán cho biến JavaScript
            var groupedQuestions = ' . $encodedGroupedQuestions . ';
           
            </script>';


            echo '<b>Thống kê nhanh  </b>';
            echo 
            '<div id = "quick-statistic">
                <div class="tabs" id="tabs-container"></div>
                <div id="tab-content"></div>
            </div>';

        // Display questions grouped by parts
        foreach ($groupedQuestions as $partNumber => $questions) {

            echo '<b>Listening part ' . esc_html($partNumber) . '</b>';
            echo '<div class = "result-answers-list">';
                
                
        
                foreach ($questions as $question) {
                    $questionNumber = $question['questionNumber'];
                    $userAnswer = $question['userAnswer'];
        
                    echo '<div class="result-answers-item" >';
                    echo '<ul style="list-style: none; >';
                        echo '<li style="margin-bottom: 10px;">';
                        echo '<span class="question-number">';
                            echo '<strong>' . esc_html($questionNumber) . '</strong> ';
                        echo '</span>';

                        echo ' <span style="color: green;" id="correct-answer-' . esc_html($questionNumber) . '"></span>: ';
                        echo '<span><i>' . esc_html($userAnswer) . '</i> <div id="check-correct-' . esc_html($questionNumber) . '" style="display: inline;"></div></span>';
                        echo '</li>';
                    echo '</div>';
                }
        
                echo '</ul>';
            echo'</div>';

        }
    }


    ?>



<!DOCTYPE html>
<html lang="en">
<head>
   
    <style>
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
    width: 100%;
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
  flex-direction: column;
  align-items: center;
  padding: 6px 14px;
  font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
  border-radius: 6px;
  border: none;

  color: #fff;
  background: linear-gradient(180deg, #4B91F7 0%, #367AF6 100%);
   background-origin: border-box;
  box-shadow: 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
}

.button-10:focus {
  box-shadow: inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2), 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), 0px 0px 0px 3.5px rgba(58, 108, 217, 0.5);
  outline: 0;
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
    <style>
    .share-container {
        display: flex;
        justify-content: space-between;
    }

    .share-left {
        width: 50%;
        padding-right: 15px;
    }

    .share-right {
        width: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding-left: 15px;
    }

    #qrCodeContainer {
        width: 150px;
        height: 150px;
        margin-bottom: 10px;
    }
</style>
</head>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>

<body onload ="main()">
    
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
        <i>Chia sẻ bài làm</i>
        <div class="share-container">
            <div class="share-left">
                <div id="permissionShareContent"></div>
                <button onclick="coppyShareContentBtn()">Copy Link</button>
                <div id="coppyShareContent"></div>
                <div id="warningRemark"></div>
            </div>
            <div class="share-right">
                <div id="qrCodeContainer"></div>
                <p>Quét mã QR để mở liên kết</p>
            </div>
        </div>
    </div>
</div>



<b>Detail and Explanation</b>
<div id = "detail-display" class = "detail-display">
    <div id="content-details" >
    
        <div class="quiz-container">
            

            <div class="content-left">
                <div id="questions-container">
                </div>
            </div>
        

        </div>
        

        <div id="question-nav-container" class="fixed-bottom">
         
            <div id="part-navigation">
            </div>

        </div>
    </div>

</div>
 

    </body>
    <?php echo'<script src="'. $site_url .'/contents/themes/tutorstarter/ielts-listening-toolkit/script_result_4.js"></script>'?>
    

<script>
        var ajaxUrl = <?php echo json_encode(admin_url("admin-ajax.php")); ?>;
        const oldCorrectNumber = <?php echo json_encode(esc_html($result->correct_number)); ?>;
        const oldIncorrectNumber = <?php echo json_encode(esc_html($result->incorrect_number)); ?>;
        const oldSkipNumber = <?php echo json_encode(esc_html($result->skip_number)); ?>;
        const oldOverallBand = <?php echo json_encode(esc_html($result->overallband)); ?>;

        
        function generateQRCode(text) {
            const qr = qrcode(0, 'L');
            qr.addData(text);
            qr.make();
            
            const qrContainer = document.getElementById('qrCodeContainer');
            qrContainer.innerHTML = qr.createImgTag(5); // Kích thước 5px cho mỗi module QR
            
            // Thêm style cho hình QR code
            const qrImg = qrContainer.querySelector('img');
            qrImg.style.width = '100%';
            qrImg.style.height = 'auto';
        }

        function closesharePermission() {
            document.getElementById('share_popup').style.display = 'none';
        }

        function coppyShareContentBtn() {
            const copyText = document.getElementById("coppyShareContent").innerText;
            navigator.clipboard.writeText(copyText).then(() => {
                alert("Copied the text: " + copyText);
            }).catch((err) => {
                console.error("Failed to copy text: ", err);
                alert("Failed to copy the text.");
            });
        }

        function opensharePermission() {
            document.getElementById('share_popup').style.display = 'block';
            const currentUrl = window.location.href;
            document.getElementById("coppyShareContent").innerHTML = currentUrl;
            
            // Tạo QR code
            generateQRCode(currentUrl);
        }

function redirectToTest(){
    
    window.location.href = `${linkTestMain}`;
//    console.log(`Linh ${linkTestMain}`);

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
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/404");
    die();


    // If no results with testsavenumber
   // echo '<p>Không có test nào với testsavenumber này.</p>';
}

