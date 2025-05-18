<?php
/*
 * Template Name: Result Template Digital SAT
 * Template Post Type: digitalsat-result
 */

$post_id = get_the_ID();

$post_id = get_the_ID();
$user_id = get_current_user_id();
//$custom_number = get_post_meta($post_id, '_digitalsat_custom_number', true);

global $wpdb; // Use global wpdb object to query the DB

$testsavenumber = get_query_var('testsavedigitalsatnumber');


$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM save_user_result_digital_sat WHERE testsavenumber = %s",
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

// Set custom_number as id_test
$id_test = $custom_number;

// Prepare the SQL statement
$sql = "SELECT testname, time, test_type, question_choose, tag, number_question, book, id_test FROM digital_sat_test_list WHERE id_test = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_test);
$stmt->execute();
$result = $stmt->get_result();
$site_url = get_site_url();



if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();  

      $testname = $data['testname'];
      $id_test = $data['id_test'];
      $question_choose = $data['question_choose'];
      $number_question = $data['number_question'];

      add_filter('document_title_parts', function ($title) use ($testname) {
          $title['title'] = $testname; // Use the $testname variable from the outer scope
          return $title;
      });
}
else {
    echo '<script type="text/javascript">console.error("Không tìm thấy test với custom number: ' . $custom_number . '");</script>';
}
echo '
    <script>
       
        
        var linkTest = "'.$site_url .'/test/digitalsat/' . $id_test . '";
       
        var currentLink = "'.$site_url .'/digitalsat/result/' . $testsavenumber . '";

    </script>
';


get_header(); // Gọi phần đầu trang (header.php)

?>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
    table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
}

.close {
    border-color: transparent;
    background-color: transparent;
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}
.green-text {
    color: green;
}
.grey-text {
    color: grey;
}

.red-text {
    color: red;
}


body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
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

.popup-content {
    background-color: #fefefe;
    margin: 5% auto; /* 5% từ trên, tự căn giữa */
    padding: 20px;
    border: 1px solid #888;
    max-height: 80%;
    overflow: auto;
    width: 90%; /* Đảm bảo popup đủ rộng */
    box-sizing: border-box; /* Đảm bảo padding không ảnh hưởng tới width */
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

.container-popup {
    display: flex; /* Căn ngang */
    justify-content: space-between; /* Tạo khoảng cách đều giữa các phần */
    gap: 10px; /* Thêm khoảng cách giữa các cột nếu cần */
    width: 100%;
    box-sizing: border-box;
}

.left-popup, .right-popup {
    width: 50%; /* Mỗi cột chiếm 50% */
    padding: 10px;
    box-sizing: border-box; /* Đảm bảo padding không làm tràn width */
}

.left-popup {
    background-color: #f9f9f9; /* Màu nền nhẹ */
    border-right: 1px solid #ddd; /* Đường chia cột (tuỳ chọn) */
}

.right-popup {
    background-color: #ffffff; /* Màu nền trắng */
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
    transition: 0.4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
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
<head>
    <title>Digital SAT Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    
</body>
<?php

    // Get current user's username
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
 
    $review = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM digital_sat_test_list WHERE id_test = %d",
            $id_test // Replace with the correct variable holding the id_test
        )
    );
    

// Ensure that you have fetched the correct questions from your database

// Check results
if (!empty($results)) {
    $time_data = [];
    if (!empty($results) && !empty($results[0]->save_specific_time)) {
        $decoded_time_data = json_decode($results[0]->save_specific_time, true); // Chuyển JSON thành mảng
        if (is_array($decoded_time_data)) {
            $time_data = $decoded_time_data; // Gán khi JSON hợp lệ
        }
    }



$new_correct_ans = 0;
$new_incorrrect_ans = 0;
$new_skip_ans = 0;


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
                                    <span class="result-stats-text">'. esc_html($result->resulttest) .'</span>
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
                                        <div class="result-score-text text-score">'. esc_html($result->resulttest) .'</div>
                                        <div class="result-score-sub"><span>Overall</span></div>

                                    </div>
                                </div>
                                
                            </div>
                           
                        </div>
                    </div>
                </div>
        ';

        echo '<a class="button-main" onclick="redirectToTest()">Làm lại bài thi</a>';
        echo '<a class="button-main" onclick="opensharePermission()">Chia sẻ bài làm</a>';
        echo '<a class="button-main" onclick="openRemarkTest()">Chấm lại</a>';
        echo '<a class="button-main" onclick="redoWrongAns()">Làm lại các câu sai</a>';
        echo '<a class="button-main" onclick="fullExplanation()">Chi tiết đáp án </a><br><br>';




        if ($review) {
            echo '<p>Type Test: ' . esc_html($review->test_type) . '</p>';
            echo '<p>Resource: ' . esc_html($review->book) . '</p>';
        } else {
            echo '<p>Type Test: N/A</p>';
            echo '<p>Resource: N/A</p>';
        }

        echo'
        <div style="display: flex; justify-content: space-around;">
            <div style="width: 45%;">
                <canvas id="domainRadarChart"></canvas>
            </div>
            <div style="width: 45%;">
                <div style="margin-bottom: 10px;">
                    <label class="switch">
                        <input type="checkbox" id="togglePerformance">
                        <span class="slider round"></span>
                    </label>
                    <span id="toggleLabel">Domain Performance</span>
                </div>
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
        

        ';
        
        // Start the table for answers
        echo '<table border="1">';
echo '<tr>
    <th>Question</th>
    <th>ID Question</th>
    <th>User Answer</th>
    <th>Correct Answer</th>
    <th>Result</th>
    <th>Domain</th>
    <th>Time (seconds)</th>
    <th>Action</th>
</tr>';

// Split the user answers based on the string format
$user_answer_string = $result->useranswer;
$answers_array = preg_split('/Question /', $user_answer_string);
array_shift($answers_array); // Remove first empty element if present

// Counter for question numbering
$question_number = 1;

$questions = explode(",", $data['question_choose']);
// Normalize question IDs to handle spaces
$questions = array_map(function($id) {
    return str_replace(' ', '', trim($id));
}, $questions);

$results_by_domain = [
    'Standard English Conventions' => ['correct' => 0, 'incorrect' => 0, 'not_answered' => 0],
    'Information and Ideas' => ['correct' => 0, 'incorrect' => 0, 'not_answered' => 0],
    'Craft and Structure' => ['correct' => 0, 'incorrect' => 0, 'not_answered' => 0],
    'Expression of Ideas' => ['correct' => 0, 'incorrect' => 0, 'not_answered' => 0]
];

$results_by_category = [];
$graph_data = []; // Array to store data for the graph

// Loop through all question IDs in the questions array
foreach ($questions as $question_id) {
    if (strpos($question_id, "verbal") === 0) {
        $sql_question = "SELECT explanation, id_question, type_question, question_content, answer_1, answer_2, answer_3, answer_4, correct_answer, image_link, category FROM digital_sat_question_bank_verbal WHERE id_question = ?";
        $stmt_question = $conn->prepare($sql_question);
        $stmt_question->bind_param("s", $question_id);
        $stmt_question->execute();
        $result_question = $stmt_question->get_result();
    }
    else if (strpos($question_id, "math") === 0) {
        $sql_question = "SELECT explanation, id_question, type_question, question_content, answer_1, answer_2, answer_3, answer_4, correct_answer, image_link, category FROM digital_sat_question_bank_math WHERE id_question = ?";
        $stmt_question = $conn->prepare($sql_question);
        $stmt_question->bind_param("s", $question_id);
        $stmt_question->execute();
        $result_question = $stmt_question->get_result();
    }

    if ($result_question->num_rows > 0) {
        $question_data = $result_question->fetch_assoc();
        $domain = '';
        if (in_array($question_data['category'], ['Boundaries', 'Form, Structure and Sense'])) {
            $domain = 'Standard English Conventions';
        } else if (in_array($question_data['category'], ['Central ideas and detail', 'Command of Evidence', 'Inferences'])) {
            $domain = 'Information and Ideas';
        } else if (in_array($question_data['category'], ['Cross Text Connections', 'Text Structure and Purpose', 'Words in context'])) {
            $domain = 'Craft and Structure';
        } else if (in_array($question_data['category'], ['Rhetorical Analysis', 'Transition'])) {
            $domain = 'Expression of Ideas';
        }

        if ($question_data['type_question'] == 'multiple-choice') {
            $correct_answer_text = '';
            switch ($question_data['correct_answer']) {
                case 'answer_1': $correct_answer_text = 'A'; break;
                case 'answer_2': $correct_answer_text = 'B'; break;
                case 'answer_3': $correct_answer_text = 'C'; break;
                case 'answer_4': $correct_answer_text = 'D'; break;
            }
        }
        else if ($question_data['type_question'] == 'completion') {
            $correct_answer_text = $question_data['correct_answer'];              
        }

        // User's answer for the current question
        $user_answer = isset($answers_array[$question_number - 1]) ? trim($answers_array[$question_number - 1]) : '';
        if (strpos($user_answer, '.') !== false) {
            $user_answer = substr($user_answer, strpos($user_answer, '.') + 1);
        }
        $user_answer = trim($user_answer);

        // Determine if the answer is correct or incorrect
        if ($user_answer == "") {
            $result_status = "not_answered";
            $result_check = "Not Answer";
            $color_class = 'grey-text';
            $new_skip_ans++;
            $point_color = 'gray';
        }
        else if ($user_answer == $correct_answer_text) {
            $result_status = "correct";
            $result_check = "Correct";
            $color_class = 'green-text';
            $new_correct_ans++;
            $point_color = 'green';
        }
        else {
            $result_status = "incorrect";
            $result_check = "Incorrect";
            $color_class = 'red-text';
            $new_incorrrect_ans++;
            $point_color = 'red';
        }

        if ($domain) {
            $results_by_domain[$domain][$result_status]++;
        }

        $category = $question_data['category'];
        if (!isset($results_by_category[$category])) {
            $results_by_category[$category] = ['correct' => 0, 'incorrect' => 0, 'not_answered' => 0];
        }
        $results_by_category[$category][$result_status]++;

        $time_spent = 'N/A';
        foreach ($time_data as $time_entry) {
            if ($time_entry['question'] == $question_number) {
                $time_spent = $time_entry['time'];
                break;
            }
        }

        // Store data for the graph
        $graph_data[] = [
            'question_number' => $question_number,
            'time_spent' => ($time_spent !== 'N/A') ? $time_spent : 0,
            'color' => $point_color,
            'result' => $result_check
        ];

        // Display each answer in the table
        echo '<tr>';
        echo '<td>Question ' . $question_number . '</td>';
        echo '<td>'. $question_data['id_question']. '</td>'; 
        echo '<td>' . esc_html($user_answer) . '</td>';
        echo '<td>' . $correct_answer_text . '</td>';
        echo '<td class="' . $color_class . '">' . $result_check . '</td>';
        echo '<td>'. $question_data['category']. '</td>'; 
        echo '<td>' . esc_html($time_spent) . '</td>';

        $explanation = isset($question_data['explanation']) ? htmlspecialchars($question_data['explanation'], ENT_QUOTES, 'UTF-8') : 'Explanation not available';
        echo '<td><a onclick="openDetailExplanation(\'' . esc_js($question_number) . '\', \'' . esc_js($question_data['id_question']) . '\', \'' . esc_js(trim(json_encode($question_data['question_content']), '"')) . '\', \'' . esc_js($question_data['image_link']) . '\', \'' . esc_js(trim(json_encode($question_data['answer_1']), '"')) . '\', \'' . esc_js(trim(json_encode($question_data['answer_2']), '"')) . '\', \'' . esc_js(trim(json_encode($question_data['answer_3']), '"')) . '\', \'' . esc_js(trim(json_encode($question_data['answer_4']), '"')) . '\', \'' . esc_js(trim(json_encode($correct_answer_text), '"')) . '\', \'' . esc_js($user_answer) . '\', `' . htmlspecialchars($question_data['explanation'], ENT_QUOTES, 'UTF-8') . '`)" id="quick-view-' . $question_data['id_question'] . '">Review</a></td>';
        
        echo '</tr>';

        $question_number++;
    }
}

echo '</table>';

// Calculate average time
$total_time = 0;
$count_time = 0;
foreach ($graph_data as $data_point) {
    if ($data_point['time_spent'] > 0) {
        $total_time += $data_point['time_spent'];
        $count_time++;
    }
}
$average_time = ($count_time > 0) ? $total_time / $count_time : 0;
$recommended_time = 90;

// Prepare data for the graph
$question_numbers = array_column($graph_data, 'question_number');
$time_values = array_column($graph_data, 'time_spent');
$colors = array_column($graph_data, 'color');

// Generate the line graph
echo '
<div style="width: 80%; margin: 20px auto;">
    <canvas id="timeGraph"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.2"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById("timeGraph").getContext("2d");
        const timeGraph = new Chart(ctx, {
            type: "line",
            data: {
                labels: ' . json_encode($question_numbers) . ',
                datasets: [{
                    label: "Time per question (seconds)",
                    data: ' . json_encode($time_values) . ',
                    backgroundColor: ' . json_encode($colors) . ',
                    borderColor: "rgba(75, 192, 192, 0.7)",
                    borderWidth: 2,
                    pointBackgroundColor: ' . json_encode($colors) . ',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return "Time: " + context.raw + "s - " + ' . json_encode(array_column($graph_data, 'result')) . '[context.dataIndex];
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    annotation: {
                        annotations: {
                            avgLine: {
                                type: "line",
                                yMin: ' . $average_time . ',
                                yMax: ' . $average_time . ',
                                borderColor: "rgb(255, 99, 132)",
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    content: "Avg: " + ' . round($average_time, 2) . ' + "s",
                                    enabled: true,
                                    position: "right",
                                    backgroundColor: "rgba(255, 99, 132, 0.7)"
                                }
                            },
                            recLine: {
                                type: "line",
                                yMin: ' . $recommended_time . ',
                                yMax: ' . $recommended_time . ',
                                borderColor: "rgb(54, 162, 235)",
                                borderWidth: 2,
                                borderDash: [5, 5],
                                label: {
                                    content: "Recommended: 90s",
                                    enabled: true,
                                    position: "right",
                                    backgroundColor: "rgba(54, 162, 235, 0.7)"
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Question Number",
                            font: {
                                weight: "bold"
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: "Time (seconds)",
                            font: {
                                weight: "bold"
                            }
                        },
                        beginAtZero: true,
                        suggestedMax: Math.max(' . max($time_values) . ', 100) * 1.2
                    }
                }
            }
        });
    });
</script>
';
echo "<script> console.log('Results by Domain:', " . json_encode($results_by_domain, JSON_PRETTY_PRINT) . ");\n </script>";
echo "<script> console.log('Results by Category:', " . json_encode($results_by_category, JSON_PRETTY_PRINT) . ");\n </script>";


        
        // Lọc các domain có tổng số câu hỏi > 0
        $filtered_results_by_domain = array_filter($results_by_domain, function($domain) {
            return ($domain['correct'] + $domain['incorrect'] + $domain['not_answered']) > 0;
        });

        // Lọc các category có tổng số câu hỏi > 0
        $filtered_results_by_category = array_filter($results_by_category, function($category) {
            return ($category['correct'] + $category['incorrect'] + $category['not_answered']) > 0;
        });

        // Chuyển dữ liệu sang JavaScript
        echo "<script>";
        echo "const filteredResultsByDomain = " . json_encode($results_by_domain) . ";";
        echo "const filteredResultsByCategory = " . json_encode($filtered_results_by_category) . ";";
        echo "</script>";
        $domainPercentages = [];
        $domainAnswered = [];
        foreach ($results_by_domain as $domain => $results) {
            $total = $results['correct'] + $results['incorrect'] + $results['not_answered'];
            $percentage = ($total > 0) ? ($results['correct'] / $total) * 100 : 0;
            $domainPercentages[$domain] = $percentage;
            $domainAnswered[$domain] = ($total > 0);
        }

        // Tính tỷ lệ phần trăm số câu đúng cho mỗi category
        $categoryPercentages = [];
        $categoryAnswered = [];
        foreach ($results_by_category as $category => $results) {
            $total = $results['correct'] + $results['incorrect'] + $results['not_answered'];
            $percentage = ($total > 0) ? ($results['correct'] / $total) * 100 : 0;
            $categoryPercentages[$category] = $percentage;
            $categoryAnswered[$category] = ($total > 0);
        }

        echo "<script>";
        echo "const domainPercentages = " . json_encode($domainPercentages) . ";";
        echo "const domainAnswered = " . json_encode($domainAnswered) . ";";
        echo "const categoryPercentages = " . json_encode($categoryPercentages) . ";";
        echo "const categoryAnswered = " . json_encode($categoryAnswered) . ";";        echo "</script>";


        remove_filter("the_content", "wptexturize");
        remove_filter("the_title", "wptexturize");
        remove_filter("comment_text", "wptexturize");


        
        echo '<script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_CHTML"></script>';
        echo '<script type="text/javascript">
            window.MathJax = {
                tex2jax: {
                    inlineMath: [["$", "$"], ["\\\(", "\\\)"]],
                    processEscapes: true
                }
            };
            document.addEventListener("DOMContentLoaded", function () {
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

            });
    </script>
    ';
    }
} else {
    // If no results with testsavenumber
    echo '<p>Không có kết quả tìm thấy cho đề thi này.</p>';
}
?>
<body>


<div id="explanation_popup" class="popup">
    <div class="popup-content">
        <button class="close" onclick="closeDetailExplanation()">&times;</button>

        <div class="container-popup">
            <!-- Cột bên trái -->
            <div class="left-popup"> 
                <div id="popup_question_id"></div>
                <div id="popup_question_content"></div>
                <div id="popup_question_image_container">
                    <image id = "popup_question_image"></image>
                </div>
                <button class="button-10" onclick = "renderMath()">Fix định dạng đề</button>

            </div>    

            <!-- Cột bên phải -->
            <div class="right-popup">
                <div id="popup_question_answer"></div>
                <div id="popup_question_correct_answer"></div>
                <div id="popup_question_user_answer"></div>
                <div id="popup_question_explanation"></div>
            </div>
        </div>
    </div>
</div>


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


</body>


<script>
  
  document.addEventListener("DOMContentLoaded", function () {
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

            });


   // Hàm để tạo biểu đồ radar
function createRadarChart(canvasId, data, title) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    const labels = Object.keys(data);
    const datasets = [];

    // Tạo dataset cho từng loại kết quả (correct, incorrect, not_answered)
    const resultTypes = ['correct', 'incorrect', 'not_answered'];
    const colors = [
        { bg: 'rgba(75, 192, 192, 0.2)', border: 'rgba(75, 192, 192, 1)' }, // Màu cho correct
        { bg: 'rgba(255, 99, 132, 0.2)', border: 'rgba(255, 99, 132, 1)' }, // Màu cho incorrect
        { bg: 'rgba(201, 203, 207, 0.2)', border: 'rgba(201, 203, 207, 1)' } // Màu cho not_answered
    ];

    resultTypes.forEach((type, index) => {
        const dataset = {
            label: type,
            data: labels.map(label => data[label][type]),
            backgroundColor: colors[index].bg,
            borderColor: colors[index].border,
            borderWidth: 1
        };
        datasets.push(dataset);
    });

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const datasetLabel = context.dataset.label || '';
                            return `${label}: ${datasetLabel} - ${value}`;
                        }
                    }
                }
            },
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: Math.max(...datasets.flatMap(dataset => dataset.data)) + 1
                }
            }
        }
    });
}

// Tạo biểu đồ radar cho Domain
createRadarChart('domainRadarChart', filteredResultsByDomain, 'Results by Domain');

// Tạo biểu đồ radar cho Category
// Tạo biểu đồ cột ngang
function createHorizontalBarChart(canvasId, data, answered, title) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    const labels = Object.keys(data);
    const percentages = Object.values(data);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Percentage Correct',
                data: percentages,
                backgroundColor: labels.map(label => answered[label] ? 'rgba(75, 192, 192, 0.6)' : 'rgba(201, 203, 207, 0.6)'),
                borderColor: labels.map(label => answered[label] ? 'rgba(75, 192, 192, 1)' : 'rgba(201, 203, 207, 1)'),
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Hiển thị cột ngang
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.raw}% Correct`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    max: 100, // Giới hạn trục x từ 0 đến 100
                    beginAtZero: true
                }
            }
        }
    });
}
// Biến để lưu trữ đối tượng biểu đồ
let performanceChart = null;

// Hàm tạo biểu đồ cột ngang
function createHorizontalBarChart(canvasId, data, answered, title) {
    const ctx = document.getElementById(canvasId).getContext('2d');
    const labels = Object.keys(data);
    const percentages = Object.values(data);

    // Hủy biểu đồ cũ nếu tồn tại
    if (performanceChart) {
        performanceChart.destroy();
    }

    // Tạo biểu đồ mới
    performanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Percentage Correct',
                data: percentages,
                backgroundColor: labels.map(label => answered[label] ? 'rgba(75, 192, 192, 0.6)' : 'rgba(201, 203, 207, 0.6)'),
                borderColor: labels.map(label => answered[label] ? 'rgba(75, 192, 192, 1)' : 'rgba(201, 203, 207, 1)'),
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Hiển thị cột ngang
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.raw}% Correct`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    max: 100, // Giới hạn trục x từ 0 đến 100
                    beginAtZero: true
                }
            }
        }
    });
}

// Hàm khởi tạo biểu đồ ban đầu
function initializeChart() {
    const isDomain = !document.getElementById('togglePerformance').checked; // Mặc định là Domain Performance
    const data = isDomain ? domainPercentages : categoryPercentages;
    const answered = isDomain ? domainAnswered : categoryAnswered;
    const title = isDomain ? 'Domain Performance' : 'Category Performance';
    createHorizontalBarChart('performanceChart', data, answered, title);
}

// Xử lý sự kiện toggle
document.getElementById('togglePerformance').addEventListener('change', function() {
    const toggleLabel = document.getElementById('toggleLabel');
    toggleLabel.textContent = this.checked ? 'Category Performance' : 'Domain Performance';
    initializeChart();
});

// Khởi tạo biểu đồ khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    // Đặt toggle mặc định là Domain Performance
    document.getElementById('togglePerformance').checked = false;
    document.getElementById('toggleLabel').textContent = 'Domain Performance';
    initializeChart();
});






// Close the draft popup when the close button is clicked
function closeDetailExplanation() {
    document.getElementById('explanation_popup').style.display = 'none';
}

function renderMath(){
    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
    Swal.fire({
  title: "Thành công",
  text: "Đã tải lại câu hỏi và đáp án đề thi. Nếu đề thi còn lỗi, hãy báo lỗi để chúng tôi fix nhanh nhất !",
  icon: "success"
});
}

function  redoWrongAns(){
   
  //var currentLink = "<?= $site_url ?>/digitalsat/result/<?= $testsavenumber ?>";
  window.location.href = currentLink + "/practice";
}
function fullExplanation(){
   
   //var currentLink = "<?= $site_url ?>/digitalsat/result/<?= $testsavenumber ?>";
   window.location.href = currentLink + "/explanation";
 }


function openDetailExplanation(questionNumber, questionId, questionContent, imageQuestion, answer1, answer2, answer3, answer4, correctAnswer, userAnswer, explanationQuestion) {
    console.log('Question ID:', questionId); // Log the received questionId
    document.getElementById('explanation_popup').style.display = 'block';
    document.getElementById('popup_question_id').innerHTML = '<b> Question ' + questionNumber + ' - ID: ' + questionId + '</b>'; // Set the ID in the popup
    document.getElementById('popup_question_content').innerHTML = questionContent; // Set the question content in the popup

    // Set the image source dynamically
    const imageElement = document.getElementById('popup_question_image');
    if (imageQuestion) {
        imageElement.src = imageQuestion; // Assign the image source
        imageElement.style.display = 'block'; // Ensure the image is visible
    } else {
        imageElement.style.display = 'none'; // Hide the image if no source is provided
    }

    document.getElementById('popup_question_answer').innerHTML = `A. ${answer1}<br>B. ${answer2}<br>C. ${answer3}<br>D. ${answer4}`; // Set the question answers in the popup
    document.getElementById('popup_question_correct_answer').innerHTML = `<b style="color:green"> Correct Answer: ${correctAnswer}</b>`; // Use correctAnswer parameter
    document.getElementById('popup_question_user_answer').innerHTML = `Your answer: ${userAnswer}`; // Use correctAnswer parameter
    document.getElementById('popup_question_explanation').innerHTML = `Explanation: ${explanationQuestion}`; // This will render HTML tags properly
    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);

}


function closeRemarkTest() {
    document.getElementById('remark_popup').style.display = 'none';
}

function openRemarkTest(){
    document.getElementById('remark_popup').style.display = 'block';

}
let Alreadyremark = false;
function remarkTest(){
    if (!Alreadyremark){
        const oldCorrectNumber = <?php echo json_encode(esc_html($result->correct_number)); ?>;
        const oldIncorrectNumber = <?php echo json_encode(esc_html($result->incorrect_number)); ?>;
        const oldSkipNumber = <?php echo json_encode(esc_html($result->skip_number)); ?>;
        const oldResultTest = <?php echo json_encode(esc_html($result->correct_number)); ?>;

        const newCorrectAnsNumber = <?php echo json_encode($new_correct_ans); ?>;
        const newIncorrectAnsNumber = <?php echo json_encode($new_incorrrect_ans); ?>;
        const newSkipAnsNumber = <?php echo json_encode($new_skip_ans); ?>;

        // Update the content with the correct number
        document.getElementById("old-correct-ans").innerText += oldCorrectNumber;
        document.getElementById("old-incorrect-ans").innerText += oldIncorrectNumber;
        document.getElementById("old-skip-ans").innerText += oldSkipNumber;


        document.getElementById("new-correct-ans").innerText += newCorrectAnsNumber;
        document.getElementById("new-incorrect-ans").innerText += newIncorrectAnsNumber;
        document.getElementById("new-skip-ans").innerText += newSkipAnsNumber;
        if (oldCorrectNumber != newCorrectAnsNumber || oldIncorrectNumber != newIncorrectAnsNumber || oldSkipNumber != newSkipAnsNumber)
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

document.getElementById("saveNewBtn").addEventListener("click", function() {
    const newCorrectAnsNumber = <?php echo json_encode($new_correct_ans); ?>;
    const newIncorrectAnsNumber = <?php echo json_encode($new_incorrrect_ans); ?>;
    const newSkipAnsNumber = <?php echo json_encode($new_skip_ans); ?>;

    // Gửi dữ liệu qua AJAX để cập nhật cơ sở dữ liệu
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'update_digital_sat_results',
            testsavenumber: <?php echo json_encode($testsavenumber); ?>,
            correct_number: newCorrectAnsNumber,
            incorrect_number: newIncorrectAnsNumber,
            skip_number: newSkipAnsNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Làm mới trang nếu cập nhật thành công
            alert('Cập nhập kết quả thành công!');
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
function redirectToTest(){
    
    window.location.href = `${linkTest}`;

}

document.addEventListener("DOMContentLoaded", function () {
    const updatePermission = document.getElementById("updatePermission");

    if (updatePermission) {
        updatePermission.addEventListener("change", function (event) {
            togglePermission(event.target);
        });
    }
});

function opensharePermission() {
    document.getElementById('share_popup').style.display = 'block';
    const currentUrl = window.location.href;
    document.getElementById("coppyShareContent").innerHTML = currentUrl;
    
    // Tạo QR code
    generateQRCode(currentUrl);
}
</script>

<?php





get_footer();