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
                        $custom_image_path = "/contents/themes/tutorstarter/template/media_img_intest/digital_sat/" . $question_data["id_question"] . ".png";
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
                    echo "['" .
                        $question_data["answer_1"] .
                        "', '" .
                        ($question_data["correct_answer"] == "answer_1"
                            ? "true"
                            : "false") .
                        "'],";
                    echo "['" .
                        $question_data["answer_2"] .
                        "', '" .
                        ($question_data["correct_answer"] == "answer_2"
                            ? "true"
                            : "false") .
                        "'],";
                    echo "['" .
                        $question_data["answer_3"] .
                        "', '" .
                        ($question_data["correct_answer"] == "answer_3"
                            ? "true"
                            : "false") .
                        "'],";
                    echo "['" .
                        $question_data["answer_4"] .
                        "', '" .
                        ($question_data["correct_answer"] == "answer_4"
                            ? "true"
                            : "false") .
                        "']";
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
                        $custom_image_path = "/contents/themes/tutorstarter/template/media_img_intest/digital_sat/" . $question_data["id_question"] . ".png";
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
                        <img class="small-button" src="/contents/themes/tutorstarter/system-test-toolkit/icon-small-button/guide2.png" height="30px" width="30px" id="guide-button"></img>
        
                        <img class="small-button" src="/contents/themes/tutorstarter/system-test-toolkit/icon-small-button/draft2.png" height="30px" width="30px" id="draft-button"></img>
                        <img class="small-button" src="/contents/themes/tutorstarter/system-test-toolkit/icon-small-button/translate2.png" height="30px" width="30px" id="translate-button"></img>                 
                        <img id = "change-mode-button"class="small-button" onclick="DarkMode()" src="/contents/themes/tutorstarter/system-test-toolkit/icon-small-button/dark-mode.png" height="30px" width="30px" ></img>
                        <img id = "change-mode-button"class="small-button" onclick="reloadTest()" src="/contents/themes/tutorstarter/system-test-toolkit/reload.png" height="30px" width="30px" ></img>
                        <img id = "change-mode-button"class="small-button" onclick="timePersonalize()" src="/contents/themes/tutorstarter/system-test-toolkit/sandclock.png" height="30px" width="30px" ></img>

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
                                <button  class ="buttonsidebar" onClick="reloadTest()"><img width="25px"  src="/contents/themes/tutorstarter/system-test-toolkit/reload.png" ></button><br>
                                <button  class ="buttonsidebar" id="print-exam-button" ><img width="28px" src="/contents/themes/tutorstarter/system-test-toolkit/print.png" ></button><br>
                                <button id="change_appearance"  class ="buttonsidebar" ><img width="28px" src="/contents/themes/tutorstarter/system-test-toolkit/setting.png" ></button><br>
                                <button id="quick-view"  class ="buttonsidebar" ><img width="28px" src="/contents/themes/tutorstarter/system-test-toolkit/quick-view.png" ></button>
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

<?php
echo'
<!--<script type="text/javascript" src="'. $site_url .'function/alert_leave_page.js"></script> -->
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/main7.js"></script>

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/translate.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/zoom-text.js"></script>

<!--<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/toggle-time-remaining-container.js"></script>-->
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/report-error.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/note-sidebar.js"></script>

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/submit_ans_1.js"></script>
<!--<script type="module" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/check_dev_tool.js"></script>
    -->
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/highlight_text_3.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/fullscreen.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/format-time-1.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/draft-popup.js"></script>
<script type="text/javascript" src="/contents/themes/tutorstarter/system-test-toolkit/function_explanation/color-background.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/start1.js"></script>
<!-- <script type="text/javascript" src="'. $site_url .'function_explanation/quick-view-answer.js"></script> -->
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/checkbox_and_remember_2.js"></script>

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/reload-test.js"></script>
<script type ="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/change-mode.js"></script> 
<!--
<script type ="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/disable-view-inspect.js"></script> -->

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function_explanation/print-exam.js"></script>
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


 