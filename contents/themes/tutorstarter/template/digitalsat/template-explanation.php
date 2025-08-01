<?php
/*
 * Template Name: Result Template Digital SAT
 * Template Post Type: digitalsat-explanation
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
$sql = "SELECT testname, time, test_type, question_choose, tag, number_question, book, id_test, full_test_specific_module FROM digital_sat_test_list WHERE id_test = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_test);
$stmt->execute();
$result = $stmt->get_result();
$site_url = get_site_url();

  echo "<script> 
        var siteUrl = '" .$site_url . "';
        var site_url = '" .$site_url . "';
    </script>";

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
       
        
        var linkTest = "'.$site_url .'/digitalsat/' . $id_test . '";
       
        var currentLink = "'.$site_url .'/digitalsat/result/' . $testsavenumber . '";

    </script>
';

        echo" <link rel='stylesheet' href='" . $site_url . "/contents/themes/tutorstarter/system-test-toolkit/style/style_13.css'>";

get_header(); // Gọi phần đầu trang (header.php)

?>
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

   /*Ẩn footer mặc định */
   #colophon.site-footer { display: none !important; }
</style>
<head>
    <title>Digital SAT Practice</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>

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

    
$questions = explode(",", $data['question_choose']);
// Normalize question IDs to handle spaces
$questions = array_map(function($id) {
    return str_replace(' ', '', trim($id)); // Remove spaces and trim
}, $questions);


$new_correct_ans = 0;
$new_incorrrect_ans = 0;
$new_skip_ans = 0;


    // Display results
    foreach ($results as $result) {
      

        $user_answer_string = $result->useranswer;
    
        // Decode JSON-based useranswer string
        $user_answers_map = [];
        $decoded_answers = json_decode($user_answer_string, true);
        if (is_array($decoded_answers)) {
            foreach ($decoded_answers as $question_label => $answer_data) {
                if (isset($answer_data['user_answer'])) {
                    // Extract question number from 'Question 1', 'Question 2', ...
                    preg_match('/Question (\d+)/', $question_label, $num_match);
                    if (isset($num_match[1])) {
                        $question_num = (int)$num_match[1];
                        $user_answers_map[$question_num] = trim($answer_data['user_answer']);
                    }
                }
            }
        }


      
    
        $incorrectOrSkippedQuestions = [];
    
        // Loop through questions
        $question_number = 1;
        foreach ($questions as $question_id) {
            $sql_question = "SELECT explanation, id_question, type_question, question_content, answer_1, answer_2, answer_3, answer_4, correct_answer, image_link FROM digital_sat_question_bank_verbal WHERE id_question = ?";
            $stmt_question = $conn->prepare($sql_question);
            $stmt_question->bind_param("s", $question_id);
            $stmt_question->execute();
            $result_question = $stmt_question->get_result();
    
            if ($result_question->num_rows > 0) {
                $question_data = $result_question->fetch_assoc();
    
                // Convert correct_answer from column name (e.g., 'answer_1') to A, B, C, D
                $correct_answer_mapping = [
                    'answer_1' => 'A',
                    'answer_2' => 'B',
                    'answer_3' => 'C',
                    'answer_4' => 'D'
                ];
                $correct_answer_text = $correct_answer_mapping[$question_data['correct_answer']] ?? '';
    
                // Get user's answer based on question number
                $user_answer = $user_answers_map[(int)$question_id] ?? '';
    
                // Determine result
                if ($user_answer == '') {
                    $result_status = "Not answered";
                    $color_class = 'grey-text';
                    $new_skip_ans++;
                    $incorrectOrSkippedQuestions[] = $question_data['id_question'];
                } elseif ($user_answer == $correct_answer_text) {
                    $result_status = 'Correct';
                    $color_class = 'green-text';
                    $new_correct_ans++;
                } else {
                    $result_status = 'Incorrect';
                    $color_class = 'red-text';
                    $new_incorrrect_ans++;
                    $incorrectOrSkippedQuestions[] = $question_data['id_question'];
                }
                $time_spent = 'N/A';
                foreach ($time_data as $time_entry) {
                    if ($time_entry['question'] == $question_number) {
                        $time_spent = $time_entry['time'];
                        break;
                    }
                }
                // Display each answer in the table
              

                $question_number++;
            }

            
        }
        echo "<script>";
        echo "const quizData = {";
        echo "    'title': " . json_encode($data["testname"]) . ",";
        echo "    'description': '',";
        echo "    'duration': " . intval($data["time"]) * 60 . ",";
        echo "    'test_type': " . json_encode($data["test_type"]) . ",";
        echo "    'number_questions': " . intval($data["number_question"]) . ",";
        echo "    'category_test': " . json_encode($data["tag"]) . ",";
        echo "    'id_test': " . json_encode($data["tag"] . "_003") . ",";
        echo "    'restart_question_number_for_each_question_catagory': 'Yes',";
        echo "    'data_added_1': '',";
        echo "    'data_added_2': '',";
        echo "    'data_added_3': '',";
        echo "    'data_added_4': '',";
        echo "    'data_added_5': '',";
        echo "    'questions': [";

        // Normalize and split question_choose
        $question_choose_cleaned = preg_replace(
            "/\s*,\s*/",
            ",",
            trim($data["question_choose"])
        );
        $questions = explode(",", $question_choose_cleaned);
        
        
        $full_test_specific_module = json_decode($data["full_test_specific_module"], true);

        $question_category_map = [];

        if (!empty($data["full_test_specific_module"])) { // Kiểm tra nếu tồn tại
            $full_test_specific_module = json_decode($data["full_test_specific_module"], true);
        
            if (is_array($full_test_specific_module)) { // Đảm bảo JSON được giải mã thành mảng
                foreach ($full_test_specific_module as $module => $details) {
                    foreach ($details["question_particular"] as $question_id) {
                        $question_category_map[$question_id] = $module ?: ""; // Nếu module không có, đặt thành ""
                    }
                }
            }
        }

        
        
        
        $first = true;

        foreach ($questions as $question_id) {
            if (strpos($question_id, "verbal") === 0) {
                // Query only from digital_sat_question_bank_verbal table
                $sql_question =
                    "SELECT id_question, type_question, question_content, answer_1, answer_2, answer_3, answer_4, correct_answer, explanation, image_link, category FROM digital_sat_question_bank_verbal WHERE id_question = ?";
                $stmt_question = $conn->prepare($sql_question);
                $stmt_question->bind_param("s", $question_id);
                $stmt_question->execute();
                $result_question = $stmt_question->get_result();

                if ($result_question->num_rows > 0) {
                    $question_data = $result_question->fetch_assoc();

                    if (!$first) {
                        echo ",";
                    }
                    $first = false;

                    echo "{";
                    echo "'type': " .
                        json_encode($question_data["type_question"]) .
                        ",";
                    echo "'question': " .
                        json_encode($question_data["question_content"]) .
                        ",";

                        $user_answer_for_question = $user_answers_map[array_search($question_id, $questions) + 1] ?? '';
                        echo "'user_answer': " . json_encode($user_answer_for_question) . ",";
                    echo "'correct_ans': " . json_encode($correct_answer_mapping[$question_data['correct_answer']] ?? '') . ",";
                        
                    if (!empty($question_data["image_link"])) {
                        $custom_image_path = "/fstudy/contents/themes/tutorstarter/template/media_img_intest/digital_sat/" . $question_data["id_question"] . ".png";
                        echo "'image': " . json_encode($custom_image_path) . ",";
                    } else {
                        echo "'image': " . json_encode(""),
                        ",";
                    }

                    
                    echo "'question_category': " . json_encode($question_category_map[$question_data["id_question"]] ?? 'Practice Test') . ",";

                    echo "'id_question': " .
                        json_encode($question_data["id_question"]) .
                        ",";
                    echo "'category': " .
                        json_encode($question_data["category"]) .
                        ",";

                    echo "\"answer\": [";
                    echo json_encode([$question_data["answer_1"], $question_data["correct_answer"] == "answer_1" ? "true" : "false"]) . ",";
                    echo json_encode([$question_data["answer_2"], $question_data["correct_answer"] == "answer_2" ? "true" : "false"]) . ",";
                    echo json_encode([$question_data["answer_3"], $question_data["correct_answer"] == "answer_3" ? "true" : "false"]) . ",";
                    echo json_encode([$question_data["answer_4"], $question_data["correct_answer"] == "answer_4" ? "true" : "false"]);
                    echo "],";
                        
                    echo "'explanation': " .
                        json_encode($question_data["explanation"]) .
                        ",";
                    echo "'section': '',";
                    echo "'related_lectures': ''";
                    echo "}";
                }
            }
            else if (strpos($question_id, "math") === 0) {
                // Query only from digital_sat_question_bank_verbal table
                $sql_question =
                    "SELECT id_question, type_question, question_content, answer_1, answer_2, answer_3, answer_4, correct_answer, explanation, image_link, category FROM digital_sat_question_bank_math WHERE id_question = ?";
                $stmt_question = $conn->prepare($sql_question);
                $stmt_question->bind_param("s", $question_id);
                $stmt_question->execute();
                $result_question = $stmt_question->get_result();

                if ($result_question->num_rows > 0) {
                    $question_data = $result_question->fetch_assoc();

                    if (!$first) {
                        echo ",";
                    }
                    $first = false;

                    echo "{";
                    echo "'type': " .
                        json_encode($question_data["type_question"]) .
                        ",";
                    echo "'question': " .
                        json_encode($question_data["question_content"]) .
                        ",";
                    
                    echo "'user_answer': " . json_encode($user_answer) . ",";
                    echo "'correct_ans': " . json_encode($correct_answer_text) . ",";

                    if (!empty($question_data["image_link"])) {
                        $custom_image_path = "/fstudy/contents/themes/tutorstarter/template/media_img_intest/digital_sat/" . $question_data["id_question"] . ".png";
                        echo "'image': " . json_encode($custom_image_path) . ",";
                    } else {
                        echo "'image': " . json_encode(""),
                        ",";
                    }
                    
                    
                    echo "'question_category': " . json_encode($question_category_map[$question_data["id_question"]] ?? 'Practice Test') . ",";
                    echo "'id_question': " .
                        json_encode($question_data["id_question"]) .
                        ",";
                    echo "'category': " .
                        json_encode($question_data["category"]) .
                        ",";
                        
  
                     

                    echo "'answer': [";
                    echo "[" .
                    json_encode($question_data["answer_1"]) .
                        "," .
                        json_encode($question_data["correct_answer"] == "answer_1"
                            ? "true"
                            : "false") .
                        "],";


                    echo "[" .
                    json_encode($question_data["answer_2"]) .
                        "," .
                        json_encode($question_data["correct_answer"] == "answer_2"
                            ? "true"
                            : "false") .
                        "],";


                    echo "[" .
                    json_encode($question_data["answer_3"]) .
                        "," .
                        json_encode($question_data["correct_answer"] == "answer_3"
                            ? "true"
                            : "false") .
                        "],";


                    echo "[" .
                    json_encode($question_data["answer_4"]) .
                        "," .
                        json_encode($question_data["correct_answer"] == "answer_4"
                            ? "true"
                            : "false") .
                        "]";
                    echo "],";
                    echo "'explanation': " .
                        json_encode($question_data["explanation"]) .
                        ",";
                    echo "'section': '',";
                    echo "'related_lectures': ''";
                    echo "}";
                }
            }
        }

        // Close the questions array and the main object
        echo "]};";

        remove_filter("the_content", "wptexturize");
        remove_filter("the_title", "wptexturize");
        remove_filter("comment_text", "wptexturize");


        echo "</script>";
        
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
            console.log("Fetch Test: ",quizData);
    </script>';
    
        echo '<script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=TeX-MML-AM_CHTML"></script>';
        echo '<script type="text/javascript">
            window.MathJax = {
                tex2jax: {
                    inlineMath: [["$", "$"], ["\\(", "\\)"]],
                    processEscapes: true
                }
            };
            document.addEventListener("DOMContentLoaded", function () {
                    MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
    
            });
        </script>';
    }
} else {
    // If no results with testsavenumber
    echo '<p>Không có kết quả tìm thấy cho đề thi này.</p>';
}
?>
<script>
    var incorrectOrSkippedQuestions = <?php echo json_encode($incorrectOrSkippedQuestions); ?>;
    console.log("Questions that are incorrect or not answered:", incorrectOrSkippedQuestions);
</script>

<html lang="en">
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


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
   
<style type="text/css">


#time-remaining-container {
    /*width: 100%;
    display: flex;
    height: 40px; 
    justify-content: space-between;
    align-items: center;
    position: fixed;
    bottom: 0;
    left: 0;
    flex-wrap: nowrap;
    background-color: black;
    color: white;
    padding: 0 10px;*/
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: url('<?php echo get_template_directory_uri(); ?>/system-test-toolkit/style/long.svg') no-repeat;
    background-position: 0 1px;
    padding: 10px;
    font-size: 13px;
    text-align: center;
    /* box-shadow: #0000004d 0 -3px 3px; */
    box-sizing: border-box;
    display: flex;
    flex-direction: row;
    align-items: center;
    z-index: 2300;
    height: 80px;
  /*  background-color: #fff;*/
    justify-content: flex-end;

    

}


            .quiz-section {
               display: flex;
               flex-direction: row;
           }
           .review-page{
                height: 100%;
                display: flex;
                flex-direction: row;
            }
            .img-ans{
                width: 200px;
                height:200px;
            }
            .question-side, .answer-side {
                overflow-y: auto;
                width: 50%;
                padding: 10px;
                height: 450px; /* Thiết lập chiều cao cố định (có thể điều chỉnh) */
                box-sizing: border-box; /* Đảm bảo padding không ảnh hưởng đến chiều cao */
            }

           .vertical-line {
                width: 3px; /* Độ dày của đường kẻ - có thể điều chỉnh */
                background-color: #ccc; /* Màu của đường kẻ */
                height: 450px; 
                margin: 0 10px; /* Khoảng cách với 2 bên */
            }
           @media (max-width: 768px) {
               .quiz-section {
                   flex-direction: column;
               }
               .review-page{
                    height: 100%;
                    flex-direction: column;

                }
               .question-side, .answer-side {
                   width: 100%;
               }
           }
           #quiz-container {
                overflow: auto;
                height: calc(100% - 140px);

                visibility: visible;
                position: absolute;
                left: 0;
                width: 100%;
            }
            #image-test-exam{
                width: 90px;
                height: 90px;
            }
            .small-button {
                border: none;
                padding: 5px 10px;
                margin-right: 5px;
                border-radius: 5px;
                width: 50px;
                height: 50px;
            }
            table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
  }
  .answer-options {
    width: 100%;
  display: inline-flex; /* Align items horizontally */
  align-items: center; /* Center items vertically */
  margin: 20px 0 0 0;
  position: relative;
  border: 2px solid #2d2f31;
  padding: 16px;
  box-sizing: border-box;
  border-radius: 7px;
}

.answer-options:after {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  width: 100%;
  border-top: 2px solid black; /* Horizontal line */
  transform: translateY(-50%); /* Center the line vertically */
  display: none; /* Hide by default */
}
img {
              max-width: 90%;
              display: block;
          }
.answer-options.active:after {
  display: block; /* Show the line when active */
}
.removeChoiceButton{
    display: inline; /* Make the <p> element inline */
  margin-left: 10px; /* Adjust spacing between the div and the text */
  font-size: 16px; /* Adjust font size if necessary */
}

        
.cb-fm + label {
              padding-left: 20px;
              position: relative;
              cursor: pointer;
          }

         

          .answer-options label {
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
          }

          .answer-options b {
              margin-left: 6px;
          }


          .rd-fm:checked + label:before,
          .cb-fm:checked + label:before {
              background-color:blue;
              border-color: #2d2f31;
              color: white;
          }

          .cb-fm:checked + label:before {
              content: "\1F5F8";
              color: white;
          }

          .cb-fm + label:before {
              content: "";
              display: inline-block;
              width: 16px;
              height: 16px;
              border: 2px solid #2d2f31;
              background-color: #fff;
              position: absolute;
              left: 0;
              top: 50%;
              transform: translateY(-50%);
          }
          .rd-fm + label {
            padding-left: 30px; /* Adjust padding for space */
            position: relative;
            cursor: pointer;
        }
        
        /* Styles for the round radio button */
        .rd-fm + label:before {
            content: attr(data-label); /* Use data-label attribute to insert A, B, C, or D */
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid #2d2f31;
            background-color: #fff;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            font-weight: bold; /* Bold font for the label */
            font-size: 12px; /* Adjust font size */
        }
        
.question {
              margin: 3%;
              font-size: 20px;
              font-family: Arial, Helvetica, sans-serif;
             

              /*
               font-weight: bold;
               height: 100%;
               overflow: scroll; */
          }
.strikethrough {
  text-decoration: line-through;
  color: gray; /* Optional: change text color to gray */
}

.bookmark-btn{
    height: 40px;
}

.crossing-options{
    position: absolute;
    top: 0;
    right: 0;
    height:30px;
}
#tag-report {
            display: flex;               /* Use flexbox to align items */
            align-items: center;         /* Vertically center items */
            justify-content: flex-start; /* Align items to the left */
            height: 30px;
            background-color: #615d5d;
            width: 100%;
            padding: 0px;           /* Add padding for better spacing */
            color: white;                /* Set text color to make it visible */
        }
        
        #tag-report img {
            margin-right: 10px; /* Add space between image and text */
        }
.btn-person {
    display:none;
    border: 1px solid #000;
    height: 40px;
    padding: 2px 12px;
    border-radius: 8px;
    font-size: 1.2rem;
    font-family: roboto;
    font-weight: 700;
}
#questionNumberBox{
    width: 30px;
    height: 30px;
    background-color: #141414;
    font-weight: 900;
    font-size: 20px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
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

.crossing-zone {
    display: none; /* Ẩn mặc định, hiển thị qua JavaScript */

    width: 40px;
    flex-shrink: 0;
    position: relative;
    cursor: pointer;

    user-select: none;
}
.answer-container{
    display: flex; 
    align-items: center;
}
.name-zone {
    height: 100%;
    display: flex;
    flex-direction: row;
    align-items: center;
    position: absolute;
    left: 40px;
    gap: 20px;
}
.cross-label {
    
    position: absolute;
    width: 21px;
    height: 21px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: solid #000 1px;
    border-radius: 50%;
    font-size: 13px;
    font-weight: 600;
    box-sizing: border-box;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.cross-btn-line {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 30px;
    border: solid #000 1px;
    margin: 0;
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
/* Flex container for left and right navigation */

@media (max-width: 768px) {
    
    .navigation-group {
        height: auto;
        overflow-x: auto;
        display: flex;           /* Use flexbox for layout */
        justify-content: space-between; /* Distribute space between left and right */
        align-items: center;     /* Vertically center items */
        margin: 10px 0;          /* Add some margin above and below */
        width: 100%;             /* Ensure full width */
    }

    #checkbox-button{
        font-size: 12px;
            width: 100px;
        display: none;
        position: absolute;
        left: 20%;
        transform: translate(-50%);
        padding: 4px 14px;
        border-radius: 8px;
        border: solid 2px #000;
        font-weight: 700;
        user-select: none;
        cursor: initial;
        flex-direction: row;
        align-items: center;
        gap: 8px;
        opacity: 1;
        pointer-events: all;
        cursor: pointer;
        transition: all .2s 0s ease;
        font-family: Roboto;
        font-size: 1.1rem;
        background-color: #000;
        color: #fff;
    }
    .quick-view-checkbox-button {
        display: flex;
        align-items: center;
        justify-content: center; /* Center content horizontally */
        padding: 10px 18px;
        font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
        border-radius: 6px;
        font-size: 15px;
        border: none;
        color: #fff;
        /*background: linear-gradient(180deg, #4B91F7 0%, #367AF6 100%);*/
        background: black;
        background-origin: border-box;
        box-shadow: 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2);
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        text-align: center; /* Center text horizontally in case of inline elements */
    }
    .btn-person {
        display:none;
        visibility: hidden;
    }

}


#time-personalize-div{
    display:none;
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
  /* The Modal (background) */
  .modal {
    display: none; 
    position: fixed; 
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto; 
    background-color: rgba(0,0,0,0.4); 
}
.navigation-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.left-group {
    padding-left: 10px;
}

.center-group {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.right-group {
    padding-right: 3px;
    display: flex;
    gap: 6px; /* Giãn cách giữa các nút */
}

/* Modal Content */
.modal-content {
    margin: 15% auto; 
    padding: 20px;
    width: 60%; 
   /* 
    background-color: #fefefe;
   border: 1px solid #888; */

}
#modalImage{
    margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);

  height: 60%;
}

/* The Close Button */
.close-modal {
    color: #050505;
   /* float: right; */
   
    font-size: 28px;
    font-weight: bold;
}

.close-modal:hover,
.close-modal:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}



.button-navigate-test{
    
    padding: 0 26px;
    background-color: #324dc6;
    height: 48px;
    border-radius: 23px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    cursor: initial;
    user-select: none;
    cursor: pointer;
    transition: all .2s 0s ease;
}
.ctn-checkbox {
    display: flex;
    align-items: center; /* Căn giữa theo chiều dọc */
}

.ctn-cbx {
    font-size: 19px;
    margin-right: 8px; /* Khoảng cách giữa text và svg */
}
.neutral {
    border-color: #2d2f31 !important;
}
.true {
    border-color: limegreen;
}

.false {
    border-color: red;
}

.pass {
    color: limegreen;
}

.fail {
    color: red;
}
.section-pageview {
    margin-top: 24px;
    display: flex
;
    flex-direction: row;
    justify-content: center;
    width: 100%;
    font-size: 16px;
    font-weight: 700;
}

.ctrl-pageview {
    background-color: #fff;
    border: 1px solid #324dc7;
    color: #324dc7;
    width: 200px;
}
.ctrl-btn {
    padding: 2px 23px;
    /* border-block-color: #324dc7; */
    height: 46px;
    /* width: 86px; */
    font-size: 1rem;
    border-radius: 23px;
    border: 1px solid #2f72dc;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #324dc7;
    font-weight: 700;
    cursor: initial;
    user-select: none;
    opacity: 1;
    cursor: pointer;
    pointer-events: all;
}



.close-review {
    background-color: #fff;
    border: 1px solid #324dc7;
    color: #324dc7;
    width: 200px;
    padding: 2px 23px;
    /* border-block-color: #324dc7; */
    height: 46px;
    /* width: 86px; */
    font-size: 1rem;
    border-radius: 23px;
    border: 1px solid #2f72dc;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    cursor: initial;
    user-select: none;
    opacity: 1;
    cursor: pointer;
    pointer-events: all;
}
.next-module-review {
    background-color: #3e98d5;
    border: 1px solid #324dc7;
    width: 200px;
    padding: 2px 23px;
    /* border-block-color: #324dc7; */
    height: 46px;
    /* width: 86px; */
    font-size: 1rem;
    border-radius: 23px;
    border: 1px solid #2f72dc;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    cursor: initial;
    user-select: none;
    opacity: 1;
    cursor: pointer;
    pointer-events: all;
}
#name-module-checkbox{
    justify-content: center;
    text-align: center;
}
.contents{
    width: 100%;
    display: flex
;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.question-list{
    column-gap: 35px;
    row-gap: 25px;
    display: flex;
    flex-direction: row;
    gap: 18px;
    border: none;
    border-top: solid #b2b2b2 1px;
    width: 100%;
    padding: 28px 10px 0;
    flex-wrap: wrap;
    box-sizing: border-box;
}

   </style>
</head>


<script src="https://kit.fontawesome.com/acfb8e1879.js" crossorigin="anonymous"></script>

    <body onload="main()" >
        
    
    <div class ="content-container">

        <div  class="container">
            <div class="blank-block"></div>

            <div class="main-block" >
                <div id = "test-prepare">
                    <div class="loader"></div>
                    <div id = "checkpoint" class = "checkpoint">
                    </div>



                    <div style="display: none;" id="date" style="visibility:hidden;"></div>
                    <div style="display: none;" id="title" style="visibility:hidden;"><?php the_title(); ?></div>
                    <div  style="display: none;"  id="id_test"  style="visibility:hidden;"><?php echo esc_html(
                        $custom_number
                    ); ?></div>
                    <button  style="display: none;" class ="start_test" id="start_test"  >Start test</button>

                </div>
            
                <p style="text-align: center;" id="title" style="display:none"></p>
                <div id="basic-info" style="display:none">
                    <div id="description"></div>
                    <div style="display: flex;">
                        <b style="margin-right: 5px;">Thời gian làm bài: </b>
                        <div id="duration"></div>
                    </div>

                    <div style="display: flex;">
                        <b style="margin-right: 5px;">Phân loại đề thi: </b>
                        <div id="testtype"></div>
                    </div>

                
                    <div style="display: flex;">
                        <b style="margin-right: 5px;">Loại đề thi: </b>
                        <div id="label"></div>
                    </div>

                

                    <div style="display: flex;">
                        <b style="margin-right: 5px;">ID đề thi: </b>
                        <div id="id_test"></div>
                    </div>
                    <div style="display: flex;">
                        <b style="margin-right: 5px;">Số câu hỏi: </b>
                        <div id="number-questions"></div>
                    </div>
                    <div style="display: flex;">
                        <b style="margin-right: 5px;">Percentage of correct answers that pass the test: </b>
                        <div id="pass_percent"></div>
                    </div>

                    <div id ="select-part-test">
                    </div>


                    <div>
                        <b>Instruction:</b>
                        <br> - You can retake the test as many times as you would like. <br> - If you run out of time, a notification will appear and you will no longer be able to edit your test answer. Pay attention to the time, it's right in the bottom right corner. <br> - You can skip a question to come back to at the end of the exam. <br> - If you want to finish the test and see your results immediately, or you finished the exam, press the "Submit Answers" button. <br>
                    </div>
                    <!-- time otion chọn thời gian bài làm -->
                    <!-- If user want to practice, you can choose this, otherwise  just click button to start test without change time limit-->

                    <h2>Bạn có thể luyện tập bằng cách chọn thời gian làm bài. Nếu không chọn thời gian sẽ mặc định  </h2>
                   


                </div>
               
            
  <!-- Đổi giao diện bài thi 
  <button id="change_appearance" style="display: none;">Đổi giao diện</button>-->

  <div id="change_appearance_popup" class="popup">

      <div class="popup-content">
          <span class="close" onclick="closeChangeDisplayPopup()">&times;</span>
          <image class = "image-test-exam"    id = "change_appearane_default" alt="Change to appearance default" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/appearance_image\appearance_default.png"></image>
          <image class = "image-test-exam"  id = "change_appearane_1" alt="Change to appearance 1" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/appearance_image/appearance_1.png"></image>
          <image class = "image-test-exam"   id = "change_appearane_2" alt="Change to appearance 2" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/appearance_image/appearance_2.png"></image>
      </div>
  </div>

<!-- end đổi giao diện bài thi -->


       
  <!-- Hiện xem nhanh đáp án
  <button id="change_appearance" style="display: none;">Đổi giao diện</button>-->

  <div id="quick-view_popup" class="popup">

    <div class="popup-content">
        <span class="close" onclick="closeQuickViewPopup()">&times;</span>
        <div id ="quick-view-answer"></div>
        
    </div>
</div>

<!-- end Hiện xem nhanh đáp án -->

                <button id="start-test"  style="display:none" onclick="showLoadingPopup()">Bắt đầu làm bài</button>
                <h1 style="display: none;" id="final-result"></h1>
                <h5 style="display: none;" id="time-result"></h5>
                <h5  style="display: none;" id ="useranswerdiv"></h5>
                        <h5   style="display: none;" id ="correctanswerdiv"></h5>

                <h3 style="display: none;" id="final-review-result"></h3>
                <div style="display: none;" id="date" style="visibility:hidden;"></div>
                <div style="display: none;" id ="header-table-test-result">
                        <table>
                            <tr>
                                <th >Ngày làm bài</th>
                                <th>Tên đề thi</th>
                                <th>Review (Câu đúng)</th>
                                <th>Final Kết quả</th>
                                <th>Thời gian làm bài</th>
                            </tr>
                            <tr>
                                <td><p id="date-table-result"></p></td>
                                <td> <p id="title-table-result"></p> </td>
                                <td> <p id="final-review-result-table"></p> </td>
                                <td> <p id="final-result-table"></p> </td>
                                <td> <p id='time-result-table'></p> </td>
                            </tr>
                        </table>
                </div>
                

                            
        <!-- The Modal -->
        <div id="myModal" class="modal">
         <span class="close-modal" style = "display:none">&times;</span> 

            <div class="modal-content">
            <img id="modalImage" src="" >
            </div>
        </div>
  
         
                <div  id="quiz-container" ></div>
              
               <span style="display:none" id="devtools-status">Checking...</span>

            </div>
          
            <div class="blank-block"></div>



            
         
         
                <div id="loading-popup" style="display: none;">
                <div id="loading-spinner"></div>
         
            </div>
         
         
         
            <div id="loading-popup-remember" style="display: none;"></div>
         
         
         
<div id="calculator" style="display: none;">
    <div id="calculator-guide"><p class="drag-text">Click here to drag (Note: For math Digital Sat only) </p> <button id = "close-cal" onclick="closeCalculator()">X</button> </div>
    <iframe src="https://www.desmos.com/calculator" width="100%" height="100%" style="border:0px #ffffff none;" name="myiFrame" scrolling="no" frameborder="1" marginheight="0px" marginwidth="0px" allowfullscreen></iframe> 
  
  </div>



         
                <div id="translate-popup" class="popup-translate">
                    <div class="popup-content-translate">
                        <span class="close-translate" onclick="closeTranslatePopup()">&times;
                        </span>
                       <div id="google_translate_element"></div>
                       <p>Đang phát triển thêm ...</p>
         
                        
                    </div>
                </div>
         
         
         
         <div id="checkbox-popup" class="popup-checkbox">
       


                <span class="close-checkbox" onclick="closeCheckboxPopup()">&times;</span>
                <b style="color: rgb(248, 23, 23);">Bạn có thể chuyển câu hỏi nhanh bằng cách ấn vào các câu tương ứng</b>

                <div class="icon-detail">
		                <div class="single-detail"><i class="fa-regular fa-flag"></i><span>Current</span></div>
                        <div class="single-detail"><div class="dashed-square"></div><span>Unanswered</span></div> 
                        <div class="single-detail"><i class="fa-solid fa-bookmark" style="color: #c33228;"></i><span>For Review</span></div>
                    </div>


                <div id="checkboxes-container" class = "checkboxes-container" ></div>
                <p style="text-align: center; justify-content: center; display: none;">Chú thích</p>

                
                
                
         </div>
         
         
         
         
         
         
         
         
         
         
         <div id="report-error-popup" class="popup-report">
                    
            <div class="popup-content-report">
         
         
         <span class="close" onclick="closeReportErorPopup()">&times;</span>
                <section class ="contact">
                <h2 style="text-align: center;" > Báo lỗi đề thi, đáp án </h2>
         
         
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
         
         
         
         
         <div id="print-popup" class="popup">
         
            
         
                
            <div class="popup-content">
                <span class="close" onclick="closePrintpopup()">&times;</span>
                <h2 style="text-align: center; color: red">Bạn có thể tải đề tại đây </h2>
         
                 <button onclick="printExam()">Tải đề thi </button>
                 <button onclick = "printAnswer()"> Tải đáp án + lời giải </button>
                 <button>Tải full đề + đáp án + lời giải  </button>
             </div>
                
                
         </div>
         
         
         <!-- draft -->
            <div id="draft-popup" class="popup">
         
                <div class="popup-content">
            
            
                    <span class="close" onclick="closeDraftPopup()">&times;</span>
                    <h3 style="text-align: center;" > Ghi chú bài làm </h3>
                    <h5 style="text-align: center; color: red;" > Những ghi chú này sẽ không được lưu lại sau bài làm.</h5><h5 style="text-align: center; color: red;"> Để lưu lại ghi chú trong bài làm, vui lòng ấn vào icon "Note" ở sidebar bên phải ! </h5>
                    <textarea  class="draft-textarea" rows="4"  id="editor" placeholder="Bạn có thể ghi chú tại đây"></textarea>
                </div>
            </div>

             <!-- settings -->
             <div id="setting-popup" class="popup">
         
                <div class="popup-content">
                    <span class="close" onclick="closeSettingPopup()">&times;</span>
                    <h3 style="text-align: center;" > Settings</h3>
                    <div id="button-container">
                        <img class="small-button" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/icon-small-button/guide2.png" height="30px" width="30px" id="guide-button"></img>
        
                        <img class="small-button" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/icon-small-button/draft2.png" height="30px" width="30px" id="draft-button"></img>
                 
                 
                      
                 
                        <img class="small-button" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/icon-small-button/translate2.png" height="30px" width="30px" id="translate-button"></img>
        
                        <!--<img class="small-button" onclick="openColorPopup()" src="icon-small-button/color.png" height="30px" width="30px" id="colors-button"></img> -->
                 
                 
                        <img id = "change-mode-button"class="small-button" onclick="DarkMode()" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/icon-small-button/dark-mode.png" height="30px" width="30px" ></img>
                        <img id = "change-mode-button"class="small-button" onclick="reloadTest()" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/reload.png" height="30px" width="30px" ></img>
                        <img id = "change-mode-button"class="small-button" onclick="timePersonalize()" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/sandclock.png" height="30px" width="30px" ></img>

                        
                      </div>
                   
                   
                </div>
            </div>

             <!-- Resume -->
             <div id="resume-popup" class="popup">
         
                <div class="popup-content">
                    <h3 style="text-align: center;" > Resume Test</h3>
                    <button onclick="closeResumePopup()"> Continue The Test</button>
                    
            </div>
         
         
         <!-- note sidebar -->
          <div id="notesidebar-popup" class="popup">
            <div class="popup-content">
                        
                <div id="sidebar">
                    <span class="close" onclick="closeNoteSidebarPopup()">&times;</span>
         <ul>
            <li onclick="changeContent(1)">Hướng dẫn</li>
            <li onclick="changeContent(2)">Lưu chú thích</li>
            <li onclick="changeContent(3)">Lưu công thức</li>
            <li onclick="changeContent(4)">Lưu từ vựng</li>
         </ul>
         </div>
         
         <div id="content">
         <h2>Lưu công thức/ ghi chú/ từ vựng,... của bạn vào trang cá nhân</h2>
         <p>Các bạn chọn menu 1, menu 2, ,menu 3 rồi ấn save vào từng nút !.</p>
         </div>
         
         
                
            </div>
         </div>
         
         <!-- end note sidebar -->
         
         </div>      
                  <div id="time-remaining-container"  >
                    <div class = "fixedrightsmallbuttoncontainer" style="display: none;">
                        <button  class ="buttonsidebar"  id="report-error"><img width="22px" height="22px" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/report.png" ></button><br>  
                        <button class ="buttonsidebar"  id="full-screen-button">⛶</button><br>
                        <button class ="buttonsidebar" id="zoom-in">+</button><br>
                        <button  class ="buttonsidebar"  id="zoom-out">-</button><br>
                        <button  class ="buttonsidebar"  id="notesidebar"><img width="20px" height="20px" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/notesidebar.png" ></button><br>
                    </div>
                    
         
                    <div class = "fixedleftsmallbuttoncontainer" style="display: none;">
                                <button  class ="buttonsidebar" id="print-exam-button" ><img width="28px" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/print.png" ></button><br>
                                <button id="change_appearance"  class ="buttonsidebar" ><img width="28px" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/setting.png" ></button><br>
                                <button id="quick-view"  class ="buttonsidebar" ><img width="28px" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/quick-view.png" ></button>
                    </div>

                   <div id="center-block" style="display:none"> 
                        <h3 id="countdowns"style="display:none"></h3>
                    </div>
                    <div class="name-zone btn-group dropup">
                        <button type="button" class="btn-person" id="user-button" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-user"></i> Tester</button>
                        <div class="dropdown-menu usermap" style=""></div>
                    </div>
                                
                    <button class="quick-view-checkbox-button" id="checkbox-button"></button>

                    <div id ="navi-button" style="display: none;">
                        <button class="button-navigate-test" id="prev-button" onclick="showPrevQuestion()">Quay lại</button>
                        <button class="button-navigate-test" id="next-button" onclick="showNextQuestion()">Tiếp theo</button>
                    </div>
                </div>

         

        <script>
           

let submitTest = false;
let pre_id_test_ = `<?php echo esc_html($custom_number); ?>`;
//console.log(`preid: ${pre_id_test_}`);



                
         



              
 let demsocau = 0;





for (let i = 1; i <= quizData.questions.length; i++)
{
    demsocau ++
}
//console.log("Hiện tại đang có", demsocau, "/",quizData.number_questions,"câu được khởi tạo" );
    

        let logoname = "";
       // console.log("Logo tên:" ,logoname)

        

function formatAWS(str) {
            if (/^<p>.*<\/p>$/.test(str)) {
                var htmlString = str;
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = htmlString;
                var innerElement = tempDiv.firstChild;
                var innerHtml = innerElement.innerHTML;
                return innerHtml;
            }
            return str;
            }
     
           // console.log(quizData);

            // Sử dụng quizData sau khi đã load xong
            // Ví dụ: hiển thị tiêu đề bài thi
            //document.getElementById('quiz-title').innerText = quizData.title;
        
        
    
          

            countdownValue = quizData.duration;


    //console.log("first question", quizData.questions[1].question)


    document.getElementById('change_appearance').addEventListener('click', openChangeDisplayPopup);
        
        
let currentQuestionIndex = 0;
// save date (ngày làm bài cho user)
const currentDate = new Date();

            // Get day, month, and year
const day = currentDate.getDate();
const month = currentDate.getMonth() + 1; // Adding 1 because getMonth() returns zero-based month index
const year = currentDate.getFullYear();

            // Display the date
const dateElement = document.getElementById('date');
const dateElement2 = document.getElementById('date-table-result');

dateElement.innerHTML = `${year}-${month}-${day}`;
dateElement2.innerHTML = `${year}-${month}-${day}`;






document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);

    const optionTimeSet = urlParams.get('option');
    const optionTrackSystem = urlParams.get('optiontrack');


    if (optionTimeSet) {
        setTimeLimit(optionTimeSet);
        var timeleft = optionTimeSet / 60 + " phút";
        //console.log(`Time left: ${timeleft}`);
        
    }
});



function setTimeLimit(value) {
    countdownValue = parseInt(value);
    document.getElementById('countdowns').innerHTML = secondsToHMS(countdownValue);
}

let DoingTest = false;
 
    



// Close the draft popup when the close button is clicked
function closeChangeDisplayPopup() {
    document.getElementById('change_appearance_popup').style.display = 'none';
}

function openChangeDisplayPopup() {
    document.getElementById('change_appearance_popup').style.display = 'block';
   
}



document.getElementById('quick-view').addEventListener('click', openQuickViewPopup);

// Close the draft popup when the close button is clicked
function closeQuickViewPopup() {
    document.getElementById('quick-view_popup').style.display = 'none';
}

function openQuickViewPopup() {
    document.getElementById('quick-view_popup').style.display = 'block';
   
}

            
document.getElementById('change_appearane_1').addEventListener('click', function() {
    var elements = document.querySelectorAll('.question-side, .answer-side, .quiz-section');
    elements.forEach(function(element) {
        element.classList.remove('question-side', 'answer-side', 'quiz-section');
    });
    // Save the state to localStorage
    localStorage.setItem('appearanceChanged', 'true');
});  

// css cho questions nếu muốn block/none
let saveSpecificTime = "";
let questionStartTime = {};  // Để lưu trữ thời gian bắt đầu cho từng câu hỏi
let questionTimes = {}; // Đối tượng lưu trữ tổng thời gian của mỗi câu hỏi


let checkBoxBtn = document.getElementById("checkbox-button");
function showPrevQuestion() {
    countTimeSpecific(currentQuestionIndex);

    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        showQuestion(currentQuestionIndex);
        startTimerForQuestion(currentQuestionIndex); // Bắt đầu lại bộ đếm thời gian

    }
}

function timePersonalize() {
  var x = document.getElementById("time-personalize-div");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}
let timerInterval;
let secondsElapsed = 0;

function startTimerForQuestion(currentQuestionIndex) {
    clearInterval(timerInterval); // Dừng bộ đếm hiện tại nếu có
    secondsElapsed = 0; // Reset thời gian về 0
    document.getElementById("time-personalize-div").innerText = `Specific Time: ${secondsElapsed} s - This feature will not be available in real test`;

    timerInterval = setInterval(() => {
        secondsElapsed++;
        document.getElementById("time-personalize-div").innerText = `Specific Time: ${secondsElapsed} s - This feature will not be available in real test`;
    }, 1000);
}




function showNextQuestion() {
    countTimeSpecific(currentQuestionIndex);

    if (currentQuestionIndex < quizData.questions.length - 1) {
        currentQuestionIndex++;
        showQuestion(currentQuestionIndex);
        startTimerForQuestion(currentQuestionIndex); // Bắt đầu lại bộ đếm thời gian

    }
}


// Hàm để cộng thời gian cho từng câu hỏi
function addTimeForQuestion(questionIndex, timeSpent) {
    if (!questionTimes[questionIndex]) {
        questionTimes[questionIndex] = 0;
    }
    questionTimes[questionIndex] += timeSpent;
}
function startQuestionTimer(questionIndex) {
    questionStartTime[questionIndex] = performance.now(); // Lấy thời gian hiện tại chính xác
}

// Hàm tính tổng thời gian cho tất cả các câu hỏi
// Hàm tính tổng thời gian cho tất cả các câu hỏi
function calculateTotalTimeForEachQuestion() {
    const questionTimeArray = []; // Mảng lưu thời gian của từng câu hỏi

    // Duyệt qua từng câu hỏi trong questionTimes và log tổng thời gian của từng câu hỏi
    for (let questionIndex in questionTimes) {
        const timeSpent = questionTimes[questionIndex].toFixed(2); // Lấy thời gian đã tính
        console.log(`Question ${questionIndex}: total time: ${timeSpent} seconds`);
        
        // Thêm đối tượng thời gian vào mảng
        questionTimeArray.push({ question: parseInt(questionIndex), time: parseFloat(timeSpent) });
    }

    // Chuyển mảng thành chuỗi JSON
    saveSpecificTime = JSON.stringify(questionTimeArray);
    console.log("saveSpecificTime:", saveSpecificTime);
}

// Ví dụ gọi hàm
function countTimeSpecific(currentQuestionIndex) {
    // Lưu thời gian bắt đầu cho câu hỏi hiện tại
    if (!questionStartTime[currentQuestionIndex]) {
        questionStartTime[currentQuestionIndex] = Date.now();
    }

    // Đợi cho người dùng chuyển sang câu hỏi tiếp theo hoặc hoàn thành
    const questionEndTime = Date.now();
    const timeSpent = (questionEndTime - questionStartTime[currentQuestionIndex]) / 1000; // Thời gian tính bằng giây

    // Log ra câu hỏi và thời gian đã sử dụng
    console.log(`Question ${currentQuestionIndex + 1}: time: ${timeSpent.toFixed(3)} seconds`);

    // Cộng thời gian cho câu hỏi hiện tại
    addTimeForQuestion(currentQuestionIndex + 1, timeSpent);


    // Cập nhật thời gian bắt đầu cho câu hỏi tiếp theo
    // Cập nhật thời gian bắt đầu cho câu hỏi tiếp theo
    questionStartTime[currentQuestionIndex + 1] = Date.now();
    questionStartTime[currentQuestionIndex] = Date.now();
    questionStartTime[currentQuestionIndex - 1] = Date.now();
}
function showTotalTime() {
    calculateTotalTimeForEachQuestion();
}




dragElement(document.getElementById("calculator"));

function dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
    /* if present, the header is where you move the DIV from:*/
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    /* otherwise, move the DIV from anywhere inside the DIV:*/
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    /* stop moving when mouse button is released:*/
    document.onmouseup = null;
    document.onmousemove = null;
  }
}
function showQuestion(index) {
    const questions = document.getElementsByClassName("questions");
    checkBoxBtn.innerHTML = `
        <div class="ctn-checkbox"> 
            <div class="ctn-cbx"> Question ${index+1} of ${questions.length} </div> 
            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 15l-6-6-6 6"/>
            </svg>
        </div>`;


    // giao diện 2
    document.getElementById('change_appearane_2').addEventListener('click', function() {
        for (let i = 0; i < questions.length; i++) {
            questions[i].style.display = "block";
        }
    });





    for (let i = 0; i < questions.length; i++) {
        questions[i].style.display = "none";
    }
    var current_module_element = document.getElementById("current_module");

if (current_module_element) { // Check if element exists
    var current_module_text = current_module_element.innerText; // Get current text

    if (!current_module_text || current_module_text === 'undefined') { // Check for undefined or empty text
        current_module_text = ''; // Set to empty if undefined
    }

    // Safely get the question_category if it exists
    var questionCategory = quizData.questions[index] && quizData.questions[index].question_category 
        ? quizData.questions[index].question_category 
        : '';

    // Update the text content
    current_module_text = ` ${questionCategory}`;
    current_module_element.innerText = current_module_text;
}




    questions[index].style.display = "block";

    document.getElementById("prev-button").style.display = index === 0 ? "none" : "inline-block";
    document.getElementById("next-button").style.display = index === questions.length - 1 ? "none" : "inline-block";
}


function ChangeQuestion(questionNumber)
        {
        
            console.log("Test change question by clicking checkbox"+ questionNumber );

            if (currentQuestionIndex < quizData.questions.length - 1) {
        
        showQuestion(questionNumber-1);    }
        currentQuestionIndex = questionNumber-1;
    }

// Button functions for testing
function blue_highlight(spanId) {
    document.getElementById(spanId).style.backgroundColor = '#00CED1';
}

function green_highlight(spanId) {
    document.getElementById(spanId).style.backgroundColor = '#90EE90';
}
function yellow_highlight(spanId) {
    document.getElementById(spanId).style.backgroundColor = 'yellow';
}
function purple_highlight(spanId) {
    document.getElementById(spanId).style.backgroundColor = '#FFB6C1';
}

 
    
       let removeChoice = false;

        </script>
<script>
function showCompletionGuide() {
    var x = document.getElementById("guide_completion");
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  }


function convertImageTag(text) {
    return text.replace(/([a-zA-Z0-9_-]+\.png)/g, function(match) {
        return '<img class="img-ans" src="' + siteUrl + '/contents/themes/tutorstarter/template/media_img_intest/digital_sat/' + match + '" />';
    });
}


  let base64Images = []; // Initialize array to store Base64 encoded images

  function encodeImage(imageUrl, index) {
    return fetch(imageUrl)
        .then(response => response.blob())
        .then(blob => {
            const reader = new FileReader();
            return new Promise((resolve, reject) => {
                reader.onloadend = function() {
                    base64Images[index] = reader.result; // Store Base64 string
                    resolve(); // Resolve promise when done
                };
                reader.onerror = reject; // Reject promise on error
                reader.readAsDataURL(blob);
            });
        })
        .catch(error => console.error('Error fetching image:', error));
}




 //Translate tool (Google trans)



// Close the draft popup when the close button is clicked
function closeCalculator() {
    document.getElementById('calculator').style.display = 'none';
}

function openCalculator() {
    document.getElementById('calculator').style.display = 'block';
    
}

function toggleRemoveChoice() {
    // Toggle the state of removeChoice
    removeChoice = !removeChoice;
    console.log(removeChoice ? "Remove Choice feature is on!" : "Remove Choice feature is off now.");

    // Select all .crossing-zone and .answer-options elements
    const crossingZones = document.querySelectorAll('.crossing-zone');
    const answerOptions = document.querySelectorAll('.answer-options');

    // Loop through each element to show/hide based on removeChoice state
    crossingZones.forEach((zone, index) => {
        zone.style.display = removeChoice ? 'block' : 'none'; // Show if removeChoice is true, hide otherwise
       // answerOptions[index].style.width = removeChoice ? '75%' : '100%'; // Adjust the width accordingly
    });

    // Handle the image source toggle
    const imageElement = document.getElementById("removeChoiceImage");
    if (imageElement) {
        imageElement.src = removeChoice
            ? `${site_url}/contents/themes/tutorstarter/system-test-toolkit/crossAbcActive.png`
            : `${site_url}/contents/themes/tutorstarter/system-test-toolkit/crossAbc.png`;
    }
}


// Hàm để lấy nhãn A, B, C, D
function getCrossLabel(ans) {
    switch (ans) {
        case 1: return 'A';
        case 2: return 'B';
        case 3: return 'C';
        case 4: return 'D';
        default: return '';
    }
}


// Close the draft popup when the close button is clicked
function closeDraftPopup() {
    document.getElementById('draft-popup').style.display = 'none';
}

function openDraftPopup() {
    document.getElementById('draft-popup').style.display = 'block';
    // Initialize CKEditor in the textarea with id 'editor'
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
}


// Close the settings popup when the close button is clicked
function closeSettingPopup() {
    document.getElementById('setting-popup').style.display = 'none';
}

function openSettingPopup() {
    document.getElementById('setting-popup').style.display = 'block';
   
}



// Close the settings popup when the close button is clicked
function closeResumePopup() {
    document.getElementById('resume-popup').style.display = 'none';
    startCountdown();
}

function openResumePopup() {
    //document.getElementById('resume-popup').style.display = 'block';
    clearInterval(countdownInterval);
    Swal.fire({
        title: "Stop Timing",
        text: "You won't be able to to do test until continue",
        icon: "warning",
        allowOutsideClick: false,
        showCancelButton: false,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Continue test"
      }).then((result) => {
        if (result.isConfirmed) {
            startCountdown();
        }
      });

    
   
}

function hideCorrectAns(){
    var explain_zone = document.getElementsByClassName("explain-zone");
    var answer_explanation = document.getElementsByClassName("answer_explanation");
    var true_ans = document.querySelectorAll(".answer-options"); // Chọn tất cả, không chỉ .true

    function toggleElements(elements) {
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.display = (elements[i].style.display === 'none') ? '' : 'none';
        }
    }

    function toggleClass(elements, className) {
        elements.forEach(element => {
            if (element.classList.contains(className)) {
                element.classList.remove(className);
            } else {
                element.classList.add(className);
            }
        });
    }

    toggleElements(explain_zone);
    toggleElements(answer_explanation);
    toggleClass([...true_ans], "true"); // Dùng tất cả answer-choice để đảm bảo class có thể thêm lại
}

function openDraft(index) {
    var x = document.getElementById("draft-"+index);
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }

  // Function to map index to letter
function getLabelLetter(index) {
    const letters = ['A', 'B', 'C', 'D'];
    return letters[index] || '';
}

function main() {
    document.body.classList.add('watermark');

   // document.getElementById("start-test").style.display  ='block';

   MathJax.Hub.Queue(["Typeset",MathJax.Hub]);



    if (quizData.logoname == undefined) {
        quizData.logoname = '';
    }
    const fullTitle = quizData.title + quizData.logoname;
    document.getElementById("title").innerHTML = quizData.title;
    document.getElementById("title-table-result").innerHTML = quizData.title;
    
    
    if (quizData.description != "") {
        document.getElementById("description").innerHTML = quizData.description;
    }

    


    document.getElementById("id_test").innerHTML = pre_id_test_;
    document.getElementById("testtype").innerHTML = quizData.test_type;
    document.getElementById("label").innerHTML = quizData.label;
    document.getElementById("duration").innerHTML = formatTime(quizData.duration);
    document.getElementById("pass_percent").innerHTML = quizData.pass_percent + "%";
    document.getElementById("number-questions").innerHTML = quizData.number_questions + " question(s)";
    var checkboxesContainer = document.getElementById("checkboxes-container");



    var contentCheckboxes = "";
    let contentQuestions = "";
    let currentCategory = "";
    let currentCheckboxCategory = "";
    let questionNumber = 1;
    let checkboxNumber = 1;

    // Create an array of promises to wait for all images to be encoded
    let encodingPromises = quizData.questions.map((question, index) => {
        if (question.image) {
            return encodeImage(question.image, index); // Encode each image
        }
        return Promise.resolve(); // No image to encode
    });

    Promise.all(encodingPromises).then(() => {
        // Ensure this is within the loop where questions are being processed
        
        

        
        contentQuestions +=`   <div class="navigation-group"> <div class="left-group"><b id="current_module" style="display: none;"></b></div>`;
            
       
        
        
        contentQuestions +=`<div class="right-group"><button  id="right-main-button" onclick = 'openCalculator()'>  <span class="icon-text-wrapper">
 <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-calculator" viewBox="0 0 16 16">
  <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/><path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
</svg> Calculator </span></button>  

<button  id="right-main-button" onclick = 'openDraftPopup()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-plus" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
</svg>
Note</span></button>  

<button  id="right-main-button" onclick = 'hideCorrectAns()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-plus" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
</svg>
Hide Correct Ans and Explanation</span></button>  



<button  id="right-main-button" onclick = 'openSettingPopup()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
  <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
  <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
</svg>
Setting </span></button>  


</div></div><div id="time-personalize-div"></div>`;

        for (let i = 0; i < quizData.questions.length; i++) {
            
            const question = quizData.questions[i];

            const category_question_type = question.type === 'completion' ? 'Điền vào chỗ trống' :
                question.type === 'multiple-choice' ? 'Chọn 1 đáp án đúng' :
                question.type === 'multi-select' ? 'Chọn nhiều đáp án đúng' : '';

                


    
                const module_question = question.question_category || '';
                
    
                if (quizData.restart_question_number_for_each_question_catagory == "Yes") {
                    if (currentCategory !== module_question) {
                        currentCategory = module_question;


                        questionNumber = 1;
                        // Đặt lại checkboxNumber khi module thay đổi
                        checkboxNumber = 1;
                       
        
                        // Thêm tên module vào trước checkbox khi thay đổi module
                        if (currentCheckboxCategory !== module_question) {
                            currentCheckboxCategory = module_question;

                        

                            contentCheckboxes += '<div class="checkbox-module-name">' + module_question;
                            //contentQuestions += '<div class="module-intro">This is module ' + module_question + '</div>';

        
                        }
                    }

                    
                } 
                
                else {
                    questionNumber = i + 1;
                    
                }
               
                
                


           

                // Set the updated text back to the element
            const answerBox = question.answer_box || [];

            let imageSrc = base64Images[i] || ''; // Get Base64 string for the current image
            


            //'<h4>' + module_question + '</h4>' +
            contentQuestions += '<div class="questions" id="question-' + i + '" style="display:none">';
            
            
            contentQuestions +='<hr class="horizontal-line">'+
    '<div class="quiz-section">' +  // Updated section wrapper
       // Top horizontal line
    '<div class="question-answer-container">' +  // Container for questions and answers
        '<div class="question-side" id="remove_question_side">' +
         '<div class="answer-box-container">';

answerBox.forEach(answer => {
    contentQuestions += `
        <div class="answer-box">${answer}</div>
    `;
});

    contentQuestions += '</div><p class="question">'+ ' Câu ' + questionNumber + '<span class="tex2jax_ignore">(ID: ' + question.id_question + ')</span>'+':'+

    '<br>' +(imageSrc ? '<img width="100%" src="' + imageSrc + '" onclick="openModal(\'' + imageSrc + '\')">' : '') +  question.question +
    '</p></div>' +
    
    '<div class="vertical-line"></div>' +  // Vertical line separator
    
    '<div class="answer-side"><div class="answer-list">';

            contentCheckboxes += '<div class="checkbox-container" onclick="ChangeQuestion(' + (i + 1) + ')" id="checkbox-container-' + (i + 1) + '">' +
                module_question + ' ' + checkboxNumber + '</div>';
                
                // Đoạn mã HTML để tích hợp nút removeChoiceImage
                contentQuestions += '<div id="tag-report" style="position: relative;">' + '<div id ="questionNumberBox">'+ questionNumber+ '</div>'+
                '<image id="bookmark-question-' + (i + 1) + '" src="' + siteUrl + '/contents/themes/tutorstarter/system-test-toolkit/bookmark_empty.png" class="bookmark-btn" onclick="rememberQuestion(' + (i + 1) + ')"></image>' +
                ' Mark for review ' +
                
                '<image id="removeChoiceImage" onclick="toggleRemoveChoice()" src="' + siteUrl + '/contents/themes/tutorstarter/system-test-toolkit/crossAbc.png" class ="crossing-options" ></image>' +
                '</div>';
                            
            contentQuestions +=`<p style ="font-style: italic">Hướng dẫn:  ${category_question_type}</p>`
            
            if (question.type === 'completion') {
                contentQuestions += '<input type="text" class="input" id="question-' + (i + 1) + '-input" autocomplete="off" placeholder="Nhập câu trả lời của bạn..."><br>' +
                    '<button onclick="showCompletionGuide()">Cách điền số/ đáp án dạng điền</button>' +
                    '<div id="guide_completion" style="display:none">Cách điền số/ đáp án dạng điền</div>' + "<br>" +
                    (answerBox.length ? `<p style="color:red">Nối các ô A,B,C... vào các chỗ trống tương ứng 1,2,3... Đáp án chấp nhận theo mẫu sau: <p style="font-style:italic; font-weight: bold">1A 2B 3C... </p> <p style="color:red"> Lưu ý giữa các ý cách nhau 1 dấu cách. Nhấn nút "Cách điền số/ đáp án dạng điền" để xem thêm minh họa</p></p>` : '');
            } else {
                for (let j = 0; j < question.answer.length; j++) {
                    // Cập nhật HTML cho mỗi lựa chọn
                    contentQuestions += '<div class="answer-container">' +
                    '<div id="answer-question-' + (i + 1) + '-ans-' + (j + 1) + '" class="answer-options neutral ' + question.answer[j][1] + '" style="width: 100%;">' +
                    (question.type === 'multiple-choice' ? 
                        '<input type="radio" name="question-' + (i + 1) + '" id="question-' + (i + 1) + '-' + (j + 1) + '" class="rd-fm">' +
                        '<label data-label="' + getLabelLetter(j) + '" for="question-' + (i + 1) + '-' + (j + 1) + '"><b>' + convertImageTag(formatAWS(question.answer[j][0])) + '</b></label>' :
                        '<input type="checkbox" name="question-' + (i + 1) + '" id="question-' + (i + 1) + '-' + (j + 1) + '" class="cb-fm">' +
                        '<label for="question-' + (i + 1) + '-' + (j + 1) + '"><b>' + formatAWS(question.answer[j][0]) + '</b></label>') +
                    '</div>' +
            
                        '<div class="crossing-zone" id="removeChoiceMainButton-' + (i + 1) + '-ans-' + (j + 1) + '">' +
                            '<div class="cross-label">' + getCrossLabel(j + 1) + '</div>' +
                            '<hr class="cross-btn-line">' +
                        '</div>' +
                    '</div>';
            
                    // Use a timeout to ensure the element exists in the DOM before adding the event listener
                    setTimeout(() => {
                        const crossingZone = document.getElementById('removeChoiceMainButton-' + (i + 1) + '-ans-' + (j + 1));
                        if (crossingZone) {
                            //crossingZone.style.display = 'block'; // Hiển thị phần tử crossing-zone
                            crossingZone.addEventListener('click', function() {
                                const answerDiv = document.getElementById('answer-question-' + (i + 1) + '-ans-' + (j + 1));
                                answerDiv.classList.toggle('active'); // Thêm/xóa class active
                            });
                        }
                    }, 0);
                }
            }
            
              
              
            contentQuestions +=`<br><button onclick="openDraft(${i+1})" style = "display:none" class ="open-draft-button">Quick Draft</button> <textarea class="draft" id="draft-${i+1}"></textarea><br>`

             // Modal functionality
         var modal = document.getElementById("myModal");
         var modalImg = document.getElementById("modalImage");
         var span = document.getElementsByClassName("close-modal")[0];
 
        // Existing code to open the modal
        window.openModal = function(src) {
            modal.style.display = "block";
            modalImg.src = src;
        }

        // Close the modal when the user clicks on <span> (x)
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal when the user clicks anywhere outside of the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
            contentQuestions += '<div class = "answer_explanation"> <b>Correct Answer: </b>' + question.correct_ans + '</p>';
            contentQuestions += '<b>Your Answer: </b>' + question.user_answer + '</p> </div>';

            if (question.explanation) {
                let explanation = question.explanation.replace(/\n/g, ' ').replace(/<br\s*\/?>/g, ' ');
                contentQuestions += '<p class="explain-zone" style="display:none"><b>Giải thích: </b>' + explanation + '</p>';
            }
            
            if (question.section) {
                contentQuestions += '<p class="explain-zone" style="display:none"><b>Section knowledge: </b>' + question.section + '</p>';
            }
            if (question.related_lectures) {
                contentQuestions += '<p class="explain-zone" style="display:none"><b>Related lectures: </b>' + question.related_lectures + '</p>';
            }

            contentQuestions += '</div></div><div class="explain-zone"></div>';
            contentQuestions += '</div></div></div></div>';

            questionNumber++;
            checkboxNumber++;

        }
        contentQuestions += '</div></div>';  // Bottom horizontal line

        contentCheckboxes += '</div>'; // Đóng div cho module cuối cùng

        document.getElementById("quiz-container").innerHTML = contentQuestions;

        document.getElementById("quiz-container").style.display = 'none';
        document.getElementById("center-block").style.display = 'none';
        document.getElementById("title").style.display = 'none';
        checkboxesContainer.innerHTML = contentCheckboxes;
        


        addEventListenersToInputs();
    });

    setTimeout(function(){
        console.log("Show Test!");
        document.getElementById("start_test").style.display="block";
        startTest();
    }, 1000);
}

function PreSubmit(){

    Swal.fire({
            title: "Bạn có chắc muốn nộp bài ?",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Nộp bài ngay",
                  cancelButtonText:"Hủy"
                }).then((result) => {
                  if (result.isConfirmed) {
                    if (result.isConfirmed) {
                let timerInterval;
            Swal.fire({
              title: "Đang nộp bài thi",
              html: "Vui lòng đợi trong giây lát, hệ thống sẽ tự động nộp bài cho bạn",
              timer: 2000,
              allowOutsideClick: false,
                showCloseButton: false,


              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
                submitButton();
                DoingTest = false;

              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                console.log("Displayed Result");
                 //ResultInput();
                 submitAnswerAndGenerateLink();

              }
            });
            }

                  }
                });


                




           
          }


          async function submitAnswerAndGenerateLink() {
            await filterAnswerForEachType();
            await ResultInput();
          
            Swal.fire({
              title: "Kết quả đã có",
              html: "Chúc mừng bạn đã hoàn thành bài thi. Click vào link dưới để nhận kết quả ngay <i class='fa-regular fa-face-smile'></i>",
              allowOutsideClick: false,
              showCancelButton: false,
              confirmButtonColor: "#3085d6",
              confirmButtonText: "Xem kết quả",
            }).then((result) => {
              if (result.isConfirmed) {
                // Chuyển đến link chứa resultId
                //const resultId = 123; // Thay bằng giá trị resultId thực tế
                window.location.href = `${siteUrl}/digitalsat/result/${resultId}`;
              }
            });
          }
          
          
    
</script>
<?php
echo'

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/translate.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/zoom-text.js"></script>

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/report-error.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/note-sidebar.js"></script>

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/submit_answer_9.js"></script>
<!--<script type="module" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/check_dev_tool.js"></script>
    -->
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/highlight_text_5.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/fullscreen.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/format-time-4.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/draft-popup.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/start-digital-sat-Test-7.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/checkboxAndRemember_3.js"></script>

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/reload-test.js"></script>
<script type ="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/change-mode.js"></script> 
<!--
<script type ="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/disable-view-inspect.js"></script> -->

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/print-exam.js"></script>
<script src="https://www.google.com/recaptcha/api.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://smtpjs.com/v3/smtp.js">
</script>
'
?>
    </body>


</html>

<?php
    get_footer();


 