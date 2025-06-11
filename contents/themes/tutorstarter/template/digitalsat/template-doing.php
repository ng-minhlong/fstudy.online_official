<?php
/*
 * Template Name: Doing Template
 * Template Post Type: digitalsat
 
 */

if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly.
}


//include_once get_template_directory() . '/checkpoint_test_permission/checkpoint.php';

if (is_user_logged_in()) {
    

    $post_id = get_the_ID();
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;
    $current_user_id = $current_user->ID;
    echo '
    <script>   
        var CurrentuserID = "' . $user_id . '";
        var Currentusername = "' . $username . '";
    </script>
    ';

    //$custom_number = get_post_meta($post_id, "_digitalsat_custom_number", true);
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
    // Set custom_number as id_test
    $id_test = $custom_number;

    // Get current time (hour, minute, second)
    $hour = date("H"); // Giờ
    $minute = date("i"); // Phút
    $second = date("s"); // Giây

    // Generate random two-digit number
    $random_number = rand(10, 99);
    // Handle user_id and id_test error, set to "00" if invalid
    if (!$user_id) {
        $user_id = "00"; // Set user_id to "00" if invalid
    }

    if (!$id_test) {
        $id_test = "00"; // Set id_test to "00" if invalid
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
        var site_url = '" .$site_url . "';
        var id_test = '" . $id_test . "';
        console.log('Result ID: ' + resultId);
    </script>";
        echo" <link rel='stylesheet' href='" . $site_url . "/contents/themes/tutorstarter/system-test-toolkit/style/style_13.css'>";

    // Query to fetch test details
    $sql = "SELECT testname, time, test_type, question_choose, tag, number_question, token_need, role_access, permissive_management, time_allow, full_test_specific_module
        FROM digital_sat_test_list 
        WHERE id_test = ?";





















/* THÊM MỚI PHẦN CHECK ĐÃ MUA TEST CHƯA TẠI ĐÂY */



// Query to fetch token details for the current username
$sql2 = "SELECT token, token_use_history 
         FROM user_token 
         WHERE username = ?";

// Prepare and execute the first query
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement 1: " . $conn->error);
}
$stmt->bind_param("s", $id_test);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $token_need = $data['token_need'];
    $time_allow = $data['time_allow'];
    

    $testname = $data['testname'];
    $permissive_management = $data['permissive_management'];

    add_filter('document_title_parts', function ($title) use ($testname) {
        $title['title'] = $testname;
        return $title;
    });

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
       
        echo '<script>
            var currentUsername = "' . $current_username . '";
            var currentUserid = "' . $current_user_id . '";
            console.log("Current Username: " + currentUsername);
            console.log("Current User ID: " + currentUserid);
        </script>';

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
                let time_left = "' . (isset($foundUser['time_left']) ? $foundUser['time_left'] : 10) . '";
            </script>';
            









    /* THÊM MỚI PHẦN CHECK ĐÃ MUA TEST CHƯA TẠI ĐÂY */

    //$stmt2->close();

        



        // Initialize quizData structure
        echo "<script>";
        echo "const quizData = {";
        echo "    'title': " . json_encode($data["testname"]) . ",";
        echo "    'full_test_specific_module': " . json_encode($data["full_test_specific_module"]). ",";
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
        
        
        $full_test_specific_module = json_decode($data["full_test_specific_module"] ?? '', true);
        $question_category_map = [];
        $sectionTimes = []; // PHP dùng array() hoặc [] thay vì const {} như JavaScript
        $isFullTest = false; // Mặc định là false

        if (!empty($data["full_test_specific_module"])) {
            $full_test_specific_module = json_decode($data["full_test_specific_module"], true);
            
            if (is_array($full_test_specific_module) && !empty($full_test_specific_module)) {
                $isFullTest = true;

                
                foreach ($full_test_specific_module as $module => $details) {
                    // Lưu thời gian của từng module
                    if (isset($details['time'])) {
                        $sectionTimes[$module] = $details['time'];
                    }
                    
                    // Ánh xạ câu hỏi
                    if (!empty($details["question_particular"])) {
                        foreach ($details["question_particular"] as $question_id) {
                            $question_category_map[$question_id] = $module ?: "";
                        }
                    }
                }
            }
        }

        // Sắp xếp module theo thứ tự chuẩn nếu là full test
        $preferredOrder = [
            "Section 1: Reading And Writing",
            "Section 2: Reading And Writing",
            "Section 1: Math",
            "Section 2: Math"
        ];

        $sortedModules = [];
        foreach ($preferredOrder as $preferredModule) {
            if (isset($full_test_specific_module[$preferredModule])) {
                $sortedModules[$preferredModule] = $full_test_specific_module[$preferredModule];
            }
        }
        $full_test_specific_module = $sortedModules;

        
        
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
                    echo "\"question\": " . json_encode($question_data["question_content"]) . ",";

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
                    /*echo "'image': " .
                        json_encode($question_data["image_link"]) .
                        ",";
                        */
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

       
        // Chuyển sang JavaScript
        
        echo "<script>
            var isFullTest = " . ($isFullTest ? 'true' : 'false') . ";
            var sectionTimes = " . json_encode($sectionTimes)  . ";

            console.log('Object thời gian các section: ', sectionTimes);

            console.log('isFullTest ?',isFullTest)
        </script>";




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
    

   /* } else {
        echo "<script>console.log('No data found for the given id_test');</script>";
    }*/
    get_header(); // Gọi phần đầu trang (header.php)

    // Close statement and connection
    $stmt->close();
    $conn->close();
    ?>



<html lang="en">
<head>

<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>




<style type="text/css">


#time-remaining-container {

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
            .question-side, .answer-side {
                overflow-y: auto;
                width: 50%;
                padding: 10px;
                height: 480px; /* Thiết lập chiều cao cố định (có thể điều chỉnh) */
                box-sizing: border-box; /* Đảm bảo padding không ảnh hưởng đến chiều cao */
            }
           
            .vertical-line {
                width: 3px; /* Độ dày của đường kẻ - có thể điều chỉnh */
                background-color: #ccc; /* Màu của đường kẻ */
                height: 480px; 
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
            .img-ans{
                width: 200px;
                height:200px;
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


/*Modal Popup Save Progress */

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
    
.modal-overlay-progress {
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
    
    .modal-progress {
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
    
    .close-modal-2 {
        float: right;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }
    .close-modal-progress {
        float: right;
        cursor: pointer;
        font-size: 20px;
        font-weight: bold;
    }
    




   /*Ẩn footer mặc định */
   #colophon.site-footer { display: none !important; }
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
                    <div style="display: none;" id="title" style="visibility:hidden;"><?php the_title(); ?></div>
                    <div  style="display: none;"  id="id_test"  style="visibility:hidden;"><?php echo esc_html(
                        $custom_number
                    ); ?></div>
                    <button  style="display: none;" class ="start_test" id="start_test"  onclick = "prestartTest()">Start test</button>
                    <i id = "welcome" style = "display:none">Click Start Test button to start the test now. Good luck</i>

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
            <image class="image-test-exam" id="change_appearane_default" alt="Change to appearance default" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/appearance_image/appearance_default.png"></image>
            <image class="image-test-exam" id="change_appearane_1" alt="Change to appearance 1" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/appearance_image/appearance_1.png"></image>
            <image class="image-test-exam" id="change_appearane_2" alt="Change to appearance 2" src="<?php echo $site_url; ?>/contents/themes/tutorstarter/system-test-toolkit/appearance_image/appearance_2.png"></image>

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
 <!--Save Progress Popup-->
 <div class="modal-overlay" id="questionModal">
                    <div class="modal-content">
                        <span class="close-modal-2">&times;</span>
                        <div id="modalQuestionContent"></div>
                        
                    </div>
                </div>
<!-- end Hiện xem nhanh đáp án -->

                <button id="start-test"  style="display:none" onclick="showLoadingPopup()">Bắt đầu làm bài</button>
                <h1 style="display: none;" id="final-result"></h1>
                <h5 style="display: none;" id="time-result"></h5>
                <h5 style="display: none;" id ="useranswerdiv"></h5>
                <h5 style="display: none;" id ="correctanswerdiv"></h5>
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
       


                <span style = "float: right" class="close-checkbox" onclick="closeCheckboxPopup()">&times;</span>

                


                <div id="checkboxes-container" class = "checkboxes-container" ></div>
                <div id = "review-page-container"></div>

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
                        <button type="button" class="btn-person" id="user-button" data-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-user"></i> <?= $current_username ?></button>
                        <div class="dropdown-menu usermap" style=""></div>
                    </div>
                                
                    <button class="quick-view-checkbox-button" id="checkbox-button"></button>

                    <div id ="navi-button" style="display: none;">
                        <button class="button-navigate-test" id="prev-button" onclick="showPrevQuestion()">Previous</button>
                        <button class="button-navigate-test" id="next-button" onclick="showNextQuestion()">Next</button>
                    </div>
                </div>

     <!-- giấu form send kết quả bài thi -->


    
  
 <span id="message"  style = "display:none" ></span>
 <form id="frmContactUs"  style = "display:none" >
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

     

    <div class = "form-group"  >
         <input type="text" id="timedotest" name="timedotest" placeholder="Thời gian làm bài"  class="form-control form_data" />
         <span id="time_error" class="text-danger"></span>
    </div>

    <div class = "form-group" >
         <input type="text" id="idtest" name="idtest" placeholder="Id test"  class="form-control form_data" />
         <span id="idtest_error" class="text-danger" ></span>
    </div>

    <div class = "form-group" >
         <input   type="text" id="test_type" name="test_type" placeholder="Test type"  class="form-control form_data" />
         <span id="test_type_error" class="text-danger" ></span>

    </div>

     <div class = "form-group"   >
         <input type="text"  id="testname" name="testname" placeholder="Test Name"  class="form-control form_data" />
         <span id="testname_error" class="text-danger"></span>
    </div>



    <div class = "form-group"   >
        <input type="text"  id="useranswer" name="useranswer" placeholder="User Answer"  class="form-control form_data" />
        <span id="useranswer_error" class="text-danger"></span>
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
        <input type="text"  id="save_specific_time" name="save_specific_time" placeholder="save_specific_time"  class="form-control form_data" />
        <span id="save_specific_time_error" class="text-danger"></span>  
    </div>


    <div class = "form-group"   >
        <input type="text"  id="testsavenumber" name="testsavenumber" placeholder="Result Number"  class="form-control form_data" />
        <span id="testsavenumber_error" class="text-danger"></span>  
    </div>
           

    <div class = "form-group"   >
        <input type="text"  id="correct_percentage" name="correct_percentage" placeholder="Correct Percentage"  class="form-control form_data" />
        <span id="correct_percentager_error" class="text-danger"></span>  
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
         
         

        <script>
           

let submitTest = false;
let pre_id_test_ = `<?php echo esc_html($custom_number); ?>`;
//console.log(`preid: ${pre_id_test_}`);


// function save data qua ajax
jQuery('#frmContactUs').submit(function(event) {
event.preventDefault(); // Prevent the default form submission

 var link = "<?php echo admin_url("admin-ajax.php"); ?>";

 var form = jQuery('#frmContactUs').serialize();
 var formData = new FormData();
 formData.append('action', 'contact_us');
 formData.append('contact_us', form);

 jQuery.ajax({
     url: link,
     data: formData,
     processData: false,
     contentType: false,
     type: 'post',
     success: function(result) {
         jQuery('#submit').attr('disabled', false);
         if (result.success == true) {
             jQuery('#frmContactUs')[0].reset();
         }
         jQuery('#result_msg').html('<span class="' + result.success + '">' + result.data + '</span>');
     }
 });
});

         //end


document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("submitForm", function () {
        setTimeout(function () {
            let form = document.getElementById("frmContactUs");
            form.submit(); // This should work now that there's no conflict
        }, 2000); 
    });
});
//end new adding

var modal = document.getElementById("questionModal");
  var closeBtn = document.querySelector(".close-modal-2");
                        
  closeBtn.onclick = function() {
    modal.style.display = "none";
  }
                                      
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
     }
  }
                
         



              
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

    // Display the date
    const dateElement = document.getElementById('date');
    const dateElement2 = document.getElementById('date-table-result');

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
    dateElement2.innerHTML = formatDateTimeForSQL(now);






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

function startTimerForQuestion(questionIndex) {
    clearInterval(timerInterval); // Dừng bộ đếm hiện tại
    secondsElapsed = 0; // Reset về 0
    updateTimerDisplay(); // Cập nhật hiển thị ngay lập tức

    timerInterval = setInterval(() => {
        secondsElapsed++;
        updateTimerDisplay();
    }, 1000);
}

function updateTimerDisplay() {
    document.getElementById("time-personalize-div").innerText = 
        `Specific Time: ${secondsElapsed} s - This feature will not be available in real test`;
}


let currentQuestionStartTime = null;
function showPrevQuestion() {
    if (currentQuestionIndex > 0) {
        showQuestion(currentQuestionIndex - 1);
       // startTimerForQuestion(currentQuestionIndex+1);
    }
}

function showNextQuestion() {
    if (currentQuestionIndex < quizData.questions.length - 1) {
        showQuestion(currentQuestionIndex + 1);
        //startTimerForQuestion(currentQuestionIndex);
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


function calculateTotalTimeForEachQuestion() {
    const questionTimeArray = [];
    for (let questionIndex in questionTimes) {
        const timeSpent = questionTimes[questionIndex].toFixed(2);
        console.log(`Question ${questionIndex}: total time: ${timeSpent} seconds`);
        questionTimeArray.push({ 
            question: parseInt(questionIndex), 
            time: parseFloat(timeSpent) 
        });
    }
    saveSpecificTime = JSON.stringify(questionTimeArray);
    console.log("saveSpecificTime:", saveSpecificTime);
}

function countTimeSpecific(currentQuestionIndex) {
    // Kiểm tra nếu questionStartTime chưa được khởi tạo cho câu hỏi này
    if (questionStartTime[currentQuestionIndex] === undefined) {
        questionStartTime[currentQuestionIndex] = Date.now();
        return; // Nếu đây là lần đầu tiên, chỉ cần khởi tạo thời gian bắt đầu
    }
    questionStartTime[currentQuestionIndex] = Date.now();

    // Tính thời gian đã sử dụng cho câu hỏi hiện tại
    const questionEndTime = Date.now();
    const timeSpent = (questionEndTime - questionStartTime[currentQuestionIndex]) / 1000;

    // Log thời gian cho câu hỏi hiện tại
   // console.log(`Question ${currentQuestionIndex + 1}: time: ${timeSpent.toFixed(3)} seconds`);

    // Cộng thời gian vào tổng thời gian của câu hỏi
    addTimeForQuestion(currentQuestionIndex + 1, timeSpent);

    // Reset thời gian bắt đầu cho câu hỏi hiện tại (nếu người dùng quay lại)
    questionStartTime[currentQuestionIndex] = Date.now();
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
let current_module_element;
var moduleText;
let currentModuleText;

function showQuestion(index) {
    
    // Ghi lại thời gian cho câu hỏi trước đó
    if (currentQuestionStartTime !== null && typeof currentQuestionIndex !== 'undefined') {
        const endTime = Date.now();
        const timeSpent = (endTime - currentQuestionStartTime) / 1000;
        
        if (!questionTimes[currentQuestionIndex + 1]) {
            questionTimes[currentQuestionIndex + 1] = 0;
        }
        questionTimes[currentQuestionIndex + 1] += timeSpent;
        
      //  console.log(`Question ${currentQuestionIndex + 1}: spent ${timeSpent.toFixed(2)} seconds`);
    }

    // Reset và bắt đầu timer mới
    currentQuestionIndex = index;
    currentQuestionStartTime = Date.now();
    startTimerForQuestion(index); // Reset bộ đếm hiển thị
    
    // Phần còn lại của hàm showQuestion...
    closeReviewPage();
    const questions = document.getElementsByClassName("questions");

    
    const currentCategory = quizData.questions[index]?.question_category || '';
    const questionsInModule = quizData.questions.filter(q => q.question_category === currentCategory);
    const currentIndexInModule = questionsInModule.findIndex(q => q === quizData.questions[index]);

    checkBoxBtn.innerHTML = `
        <div class="ctn-checkbox"> 
            <div class="ctn-cbx"> Question ${currentIndexInModule + 1} of ${questionsInModule.length} </div> 

            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 15l-6-6-6 6"/>
            </svg>
        </div>`;

    // Giao diện 2
    document.getElementById('change_appearane_2').addEventListener('click', function() {
        for (let i = 0; i < questions.length; i++) {
            questions[i].style.display = "block";
        }
    });
    
    current_module_element = document.getElementById("current_module");

    // Ẩn tất cả các câu hỏi
    for (let i = 0; i < questions.length; i++) {
        questions[i].style.display = "none";
    }

        

    if (current_module_element) {
        let currentModuleText = current_module_element.textContent.trim();
        let questionCategory = quizData.questions[index]?.question_category || '';

        // Xử lý thời gian module cho Full Test
        if (quizData.isFullTest) {
            // Lưu thời gian còn lại của module trước khi chuyển
            if (currentModuleText && currentModuleText !== questionCategory) {
                moduleTimeRemaining[currentModuleText] = countdownValue;
            }
            
            // Cập nhật thời gian cho module mới
            if (questionCategory && moduleTimeRemaining[questionCategory]) {
                countdownValue = moduleTimeRemaining[questionCategory];
                document.getElementById('countdown').innerHTML = secondsToHMS(countdownValue);
            }
        }



        // Cập nhật module hiện tại nếu khác với module cũ
        if (currentModuleText !== questionCategory) {
            current_module_element.innerText = ` ${questionCategory}`;

            setTimeout(() => {
                currentModuleText = current_module_element.textContent.trim();
               // console.log("After update: ", currentModuleText);

                // Chỉ cập nhật review-page nếu module thay đổi
                updateReviewPage(currentModuleText);
                showCheckboxesForCategory(currentModuleText); // Hiển thị checkboxes cho category
            }, 0);
        } else {
            // Nếu module không thay đổi, chỉ hiển thị checkboxes
            showCheckboxesForCategory(currentModuleText);
        }
    }

    questions[index].style.display = "block";

    // Kiểm tra xem đây có phải là câu hỏi đầu tiên của module không
    const isFirstQuestionInCategory = isFirstQuestionOfCategory(index);
    if (isFirstQuestionInCategory) {
        document.getElementById("prev-button").style.display = "none"; // Ẩn nút "Quay lại"
        document.getElementById("prev-button").onclick = null; // Vô hiệu hóa chức năng "Quay lại"
    } else {
        document.getElementById("prev-button").style.display = "inline-block"; // Hiện nút "Quay lại"
        document.getElementById("prev-button").onclick = () => showQuestion(index - 1); // Bật chức năng "Quay lại"
    }

    // Kiểm tra xem đây có phải là câu hỏi cuối cùng của question_category không
    const isLastQuestionInCategory = isLastQuestionOfCategory(index);
    if (isLastQuestionInCategory) {
        document.getElementById("next-button").textContent = "Review";
        document.getElementById("next-button").onclick = showReviewPage;
    } else {
        document.getElementById("next-button").textContent = "Next";
        document.getElementById("next-button").onclick = () => showQuestion(index + 1);
    }

    //document.getElementById("next-button").style.display = index === questions.length - 1 ? "none" : "inline-block";
    if(index === questions.length - 1){
        document.getElementById("next-button").textContent = "Submit";

    }
    countTimeSpecific(index);
    document.getElementById("next-button").style.display  = "inline-block";

}


// Hàm kiểm tra xem câu hỏi có phải là đầu tiên của question_category không
function isFirstQuestionOfCategory(index) {
    const currentCategory = quizData.questions[index].question_category;
    for (let i = index - 1; i >= 0; i--) {
        if (quizData.questions[i].question_category === currentCategory) {
            return false;
        }
    }
    return true;
}

// Hàm kiểm tra xem câu hỏi có phải là cuối cùng của question_category không
function isLastQuestionOfCategory(index) {
    const currentCategory = quizData.questions[index].question_category;
    for (let i = index + 1; i < quizData.questions.length; i++) {
        if (quizData.questions[i].question_category === currentCategory) {
            return false;
        }
    }
    return true;
}

// Hàm kiểm tra xem câu hỏi có phải là cuối cùng của question_category không
function isLastQuestionOfCategory(index) {
    const currentCategory = quizData.questions[index].question_category;
    for (let i = index + 1; i < quizData.questions.length; i++) {
        if (quizData.questions[i].question_category === currentCategory) {
            return false;
        }
    }
    return true;
}


document.addEventListener("DOMContentLoaded", function () {
    // Listen for the custom event to submit the form
    document.addEventListener("submitForm", function () {
        // Wait for 2 seconds before submitting the form
        setTimeout(function () {
            let form = document.getElementById("frmContactUs");
            if (typeof form.submit === 'function') {
                form.submit();
            } else {
                // If the direct submit fails, use this fallback:
                form.submit();
            }
        }, 2000); // 2000 milliseconds = 2 seconds
    });
});
/*
for (let i = 0; i < quizData.questions.length; i++) {  
            console.log("Current question: ",i)   
            currentModule = quizData.questions[6].question_category;
        } */


//var current_module_element;
let resultJson = {};
var formattedTimeUsed;

function submit_Test() {
    fetch(`${siteUrl}/api/cham-diem/digitalsat/`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id_test: id_test,
            testname: <?php echo json_encode($testname); ?>,
            username: <?php echo json_encode($current_username); ?>,
            result_id: <?php echo json_encode($result_id); ?>,
            saveSpecificTime: saveSpecificTime,
            timedotest: formattedTimeUsed,
            type_test: quizData.test_type,
            user_answer: resultJson
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const result = data.result;
            console.log("Kết quả chấm điểm:", result);
        } else {
            alert('Chấm điểm thất bại.');
        }
    })
    .catch(error => {
        console.error('Lỗi khi gửi dữ liệu:', error);
        alert('Đã xảy ra lỗi khi gửi yêu cầu.');
    });
}

function ChangeQuestion(questionNumber)
        {
            closeReviewPage();
            console.log("Test change question by clicking checkbox"+ questionNumber );

            if (currentQuestionIndex < quizData.questions.length - 1) {
        
        showQuestion(questionNumber-1);    
    }
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

// Hàm để hiển thị checkboxes của category hiện tại và ẩn các category khác
function showCheckboxesForCategory(category) {
   // console.log("currentCategory: oke", currentCategory)
    // Ẩn tất cả các module
    const allModules = document.querySelectorAll('[id^="module-"]');
    const allModules2 = document.querySelectorAll('[id^="module-2-"]');

    allModules.forEach(module => {
        module.style.display = 'none';
    });
    allModules2.forEach(module2 => {
        module2.style.display = 'none';
    });
    // Hiển thị module của category hiện tại
    const currentModule = document.getElementById('module-' + category);
    const currentModule2 = document.getElementById('module-2-' + category);

    if (currentModule) {
        currentModule.style.display = 'block';
    }
    if (currentModule2) {
        currentModule2.style.display = 'block';
    }
}

// Ví dụ sử dụng:
const currentCategory = quizData.questions[currentQuestionIndex].question_category;

function updateReviewPage(module_question) {
    let contentCheckboxes2 = '';
    contentCheckboxes2 += '<div id="module-2-' + module_question +'" >';
    contentCheckboxes2 += '<div id="name-module-checkbox-2"><b>Current Module: ' + module_question + '</b></div>';
    contentCheckboxes2 += '<div class="icon-detail">';
    contentCheckboxes2 += '<div class="single-detail"><i class="fa-regular fa-flag"></i><span>Current</span></div>';
    contentCheckboxes2 += '<div class="single-detail"><div class="dashed-square"></div><span>Unanswered</span></div>';
    contentCheckboxes2 += '<div class="single-detail"><i class="fa-solid fa-bookmark" style="color: #c33228;"></i><span>For Review</span></div>';
    contentCheckboxes2 += '</div>';
    contentCheckboxes2 += '<div class="contents"> <div class="checkbox-module-name"> <div class="question-list">';

    for (let j = 0; j < quizData.questions.length; j++) {
        if (quizData.questions[j].question_category === module_question) {
            contentCheckboxes2 += '<div class="checkbox-container" onclick="ChangeQuestion(' + (j + 1) + ')" id="checkbox-container-2-' + (j + 1) + '">' + (j + 1) + '</div>';
        }
    }

    contentCheckboxes2 += '</div></div> </div></div>';

    document.getElementById('review-content').innerHTML = contentCheckboxes2;
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
    var reviewPageBtn = document.getElementById("review-page-container");

    





    let contentCheckboxes = '';
    let contentCheckboxes2 = '';

    var openReviewPage = "";
    

    let contentQuestions = "";
    let currentCategory = "";
    let questionNumber = 1;
    let checkboxNumber = 1;



/*
    // Create an array of promises to wait for all images to be encoded
    let encodingPromises = quizData.questions.map((question, index) => {
        if (question.image) {
            return encodeImage(question.image, index); // Encode each image
        }
        return Promise.resolve(); // No image to encode
    });
*/

    //Promise.all(encodingPromises).then(() => {
      

        contentQuestions +=`   <div class="navigation-group"> <div class="left-group"><b><p style = "font-size: 26px; font-family: none"  id="current_module" style="display: none;"></p></b></div>`;
            
       
        
        contentQuestions += `<div class = "center-group"><span class="icon-text-wrapper"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/></svg> <h3 id="countdown"></h3></span></div>`;
        
        contentQuestions +=`<div class="right-group"><button  id="right-main-button" onclick = 'openCalculator()'>  <span class="icon-text-wrapper">
 <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-calculator" viewBox="0 0 16 16">
  <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/><path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
</svg> Calculator </span></button>  

<button  id="right-main-button" onclick = 'saveProgress()'>  <span class="icon-text-wrapper">
<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5"/><path d="M13.8 12H3m9 10a10 10 0 1 0 0-20"/>
</svg>  Save Progress </span></button>  

<button  id="right-main-button" onclick = 'openDraftPopup()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-plus" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
</svg>
Note</span></button>  



<button  id="right-main-button" onclick = 'openSettingPopup()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
  <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
  <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
</svg>
Setting </span></button>  

<button  id="right-main-button" onclick = 'openResumePopup()'>  <span class="icon-text-wrapper"><i class="fa-solid fa-hourglass-start"></i>
Resume </span></button>

</div></div><div id="time-personalize-div"></div>`;


for (let i = 0; i < quizData.questions.length; i++) {
    const question = quizData.questions[i];

    const category_question_type = question.type === 'completion' ? 'Điền vào chỗ trống' :
        question.type === 'multiple-choice' ? 'Chọn 1 đáp án đúng' :
        question.type === 'multi-select' ? 'Chọn nhiều đáp án đúng' : '';

    const module_question = question.question_category || '';

    if (quizData.restart_question_number_for_each_question_catagory == "Yes") {
        if (currentCategory !== module_question) {
            // Nếu có module cũ, đóng div của nó trước khi mở module mới
            if (currentCategory !== "") {
                contentCheckboxes += '</div></div>';
            }

            currentCategory = module_question;
            questionNumber = 1;
            checkboxNumber = 1;

            // Mở div mới cho module
            contentCheckboxes += '<div id="module-' + module_question + '" style="display: none;">'; // Ẩn module mặc định
            contentCheckboxes += '<div id="name-module-checkbox"><b>Current Module: ' + module_question + '</b></div>';
            contentCheckboxes += '<div class="icon-detail">';
            contentCheckboxes += '<div class="single-detail"><i class="fa-regular fa-flag"></i><span>Current</span></div>';
            contentCheckboxes += '<div class="single-detail"><div class="dashed-square"></div><span>Unanswered</span></div>';
            contentCheckboxes += '<div class="single-detail"><i class="fa-solid fa-bookmark" style="color: #c33228;"></i><span>For Review</span></div>';
            contentCheckboxes += '</div>'; // Đóng div icon-detail
            contentCheckboxes += '<div class="checkbox-module-name">';
        }

        // Thêm checkbox vào module hiện tại
        contentCheckboxes += '<div class="checkbox-container" onclick="ChangeQuestion(' + (i + 1) + ')" id="checkbox-container-' + (i + 1) + '">' + module_question + ' ' + checkboxNumber + '</div>';
        checkboxNumber++;
    } else {
        questionNumber = i + 1;
    }

    // Khi kết thúc vòng lặp, đóng div cuối cùng
    if (i === quizData.questions.length - 1) {
        contentCheckboxes += '</div></div>'; // Đóng div của category cuối cùng
    }
}

contentQuestions += `
<div class="review-page" id="review-page" style="display: none; justify-content: center; text-align: center">
    <p style="font-size: 30px">Check Your Work</p><br>
    <p>On test day, you won't be able to move to next module until the time expires</p>
    <div id="review-content"></div>
    <div class = "btn-review-page-content">
        <div id="submit-review-content"></div>
    </div>
</div>
`;

for (let i = 0; i < quizData.questions.length; i++) {
    const question = quizData.questions[i];
    
    const category_question_type = question.type === 'completion' ? 'Điền vào chỗ trống' :
        question.type === 'multiple-choice' ? 'Chọn 1 đáp án đúng' :
        question.type === 'multi-select' ? 'Chọn nhiều đáp án đúng' : '';

    const module_question = question.question_category || '';

                // Set the updated text back to the element
            const answerBox = question.answer_box || [];

            //let imageSrc = base64Images[i] || ''; // Get Base64 string for the current image

            let imageSrc = question.image || '';

            let contentCheckboxes2 = '';
            contentCheckboxes2 += '<div id="module-2-' + module_question +'" style="display: none;">';
            contentCheckboxes2 += '<div id="name-module-checkbox-2"><b>Current Module: ' + module_question + '</b></div>';
            contentCheckboxes2 += '<div class="icon-detail">';
            contentCheckboxes2 += '<div class="single-detail"><i class="fa-regular fa-flag"></i><span>Current</span></div>';
            contentCheckboxes2 += '<div class="single-detail"><div class="dashed-square"></div><span>Unanswered</span></div>';
            contentCheckboxes2 += '<div class="single-detail"><i class="fa-solid fa-bookmark" style="color: #c33228;"></i><span>For Review</span></div>';
            contentCheckboxes2 += '</div>';
            contentCheckboxes2 += '<div class="checkbox-module-name">';

            for (let j = 0; j < quizData.questions.length; j++) {
                if (quizData.questions[j].question_category === module_question) {
                    contentCheckboxes2 += '<div class="checkbox-container" onclick="ChangeQuestion(' + (j + 1) + ')" id="checkbox-container-2-' + (j + 1) + '">' + (j + 1) + '</div>';
                }
            }

            contentCheckboxes2 += '</div></div>';

                
                
            
            contentQuestions += '<div class="questions" id="question-' + i + '" style="display:none">';

            contentQuestions +='<hr class="horizontal-line">'+
    '     <div class="quiz-section">' +
    '<div class="question-answer-container">' +  
        '<div class="question-side" id="remove_question_side">' +
         '<div class="answer-box-container">';



answerBox.forEach(answer => {
    contentQuestions += `
        <div class="answer-box">${answer}</div>
    `;
});

    contentQuestions += '</div>       <p class="question">'+ ' Câu ' + questionNumber + '<span class="tex2jax_ignore">(ID: ' + question.id_question + ')'+'</span>'+':'+

    '<br>' +(imageSrc ? '<img width="100%" src="' + imageSrc + '" onclick="openModal(\'' + imageSrc + '\')">' : '') +  question.question +
    '</p></div>' +
    
    '<div class="vertical-line"></div>' +  // Vertical line separator
    
    '<div class="answer-side"><div class="answer-list">';

            
            
            openReviewPage = '<div class="section-pageview"> <button class="ctrl-btn ctrl-pageview" id="btn-review" onclick="showReviewPage()">Go to review page</button></div>';
                
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
        

            if (question.explanation) {
                contentQuestions += '<p class="explain-zone" style="display:none"><b>Giải thích: </b>' + question.explanation + '</p>';
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

        document.getElementById('checkboxes-container').innerHTML = contentCheckboxes;



        contentQuestions += '</div></div>';  // Bottom horizontal line


        document.getElementById("quiz-container").innerHTML = contentQuestions;

        document.getElementById("quiz-container").style.display = 'none';
        document.getElementById("center-block").style.display = 'none';
        document.getElementById("title").style.display = 'none';
        //checkboxesContainer.innerHTML = contentCheckboxes;
        reviewPageBtn.innerHTML = openReviewPage;
        


        addEventListenersToInputs();
        
        
   // });

    


    /*setTimeout(function(){
        console.log("Show Test V1");
        startTest();
    }, 1000);*/

    //showLoadingPopup();
    setTimeout(function(){
        console.log("Show Test!");
        document.getElementById("start_test").style.display="block";
        hidePreloader();
        
        document.getElementById("welcome").style.display="block";

    }, 1000);
}
// Hàm lấy câu trả lời dạng A,B,C,D
function getUserAnswer(questionNumber) {
    const questionIndex = questionNumber - 1;
    const question = quizData.questions[questionIndex];
    
    if (question.type === 'completion') {
        const input = document.getElementById(`question-${questionNumber}-input`);
        return input ? input.value : null;
    } else {
        // Kiểm tra các lựa chọn đã chọn và trả về dạng A,B,C,D
        const selectedAnswers = [];
        for (let i = 0; i < question.answer.length; i++) {
            const checkbox = document.getElementById(`question-${questionNumber}-${i+1}`);
            if (checkbox && checkbox.checked) {
                selectedAnswers.push(getLabelLetter(i)); // Sử dụng hàm getLabelLetter để chuyển thành A,B,C,D
            }
        }
        return selectedAnswers.length > 0 ? selectedAnswers : null;
    }
}
function saveProgress() {
    const modal = document.getElementById("questionModal");
    const modalContent = document.getElementById("modalQuestionContent");
    const currentQuestion = currentQuestionIndex + 1;
    
    // Get remaining time
    const time_left = formatTime(countdownValue);
    
    // Count answered questions
    let number_answered = 0;
    let user_answered = {};
    
    // Group answers by category and log them
    quizData.questions.forEach((question, index) => {
        const questionNumber = index + 1;
        const category = question.question_category;
        const answer = getUserAnswer(questionNumber);
        
        if (answer && (answer !== "" && answer !== null && answer !== undefined && (typeof answer !== 'object' || answer.length > 0))) {
            number_answered++;
            
            if (!user_answered[category]) {
                user_answered[category] = {};
            }
            user_answered[category][questionNumber] = answer;
        }
    });
    
    // Tạo detailData chứa time_left và user_answered
    const detailData = {
        time_left: countdownValue, // Thời gian còn lại dạng số giây
        user_answered: user_answered // Câu trả lời của người dùng
    };
    
    console.log("Detail data (JSON):", JSON.stringify(detailData));
    
    var totalQuestions = quizData.questions.length;
    var percentCompleted = Math.floor(number_answered / totalQuestions * 100);

    
      // Lấy thông tin ngày tháng
      const currentDate = new Date();
      const day = String(currentDate.getDate()).padStart(2, '0');
      const month = String(currentDate.getMonth() + 1).padStart(2, '0'); 
      const year = currentDate.getFullYear();
      const hours = String(currentDate.getHours()).padStart(2, '0');
      const minutes = String(currentDate.getMinutes()).padStart(2, '0');
      const seconds = String(currentDate.getSeconds()).padStart(2, '0');

      
      // Tạo progress_id theo định dạng: user_id + id_test + date_element (dạng YYYYMMDD)
      const dateCode = `${year}${month}${day}${hours}${minutes}${seconds}`;
      const progress_id = `${CurrentuserID}${dateCode}`;


    modalContent.innerHTML = `
        <div>
            <p><strong>Loại test:</strong> Digital Sat</p>
            <p><strong>Progress:</strong> ${currentQuestion}/${totalQuestions}</p>
            <p><strong>ID Test:</strong> ${id_test}</p>
            <p><strong>Time left:</strong> ${time_left}</p>
            <p><strong>Date:</strong> ${new Date().toLocaleString()}</p>
            <p><strong>Answered:</strong> ${number_answered}/${totalQuestions} (${percentCompleted}%)</p>
        </div>
        <button id="saveProgressBtn">Lưu progress</button>
        <div id="saveResult" style="margin-top: 10px;"></div>
        <div id="deleteProgressSection" style="display: none; margin-top: 20px;">
            <button id="showDeleteProgressBtn" style="background-color: #f44336; color: white;">Xóa bớt record</button>
            <div id="progressList" style="margin-top: 10px; display: none;"></div>
        </div>
    `;
    modal.style.display = "flex";

    document.getElementById('saveProgressBtn').addEventListener('click', function () {
        const btn = this;
        const resultDiv = document.getElementById('saveResult');
        const deleteSection = document.getElementById('deleteProgressSection');

        btn.disabled = true;
        btn.textContent = 'Đang lưu...';
        resultDiv.innerHTML = '<p>Đang xử lý...</p>';
        
        // Dữ liệu cần gửi
        const data = {
            username: Currentusername,
            id_test: id_test,
            testname: testname,
            progress: currentQuestion,
            percent_completed: percentCompleted,
            type_test: "digitalsat",
            date: new Date().toLocaleString(),
            progress_id: progress_id,
            detail_data: detailData, // Thêm detailData vào đây
            user_answers: user_answered
        };

        // Gửi request đến REST API
        fetch(`${siteUrl}/api/v1/update-progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                resultDiv.innerHTML = '<p style="color: green;">Lưu thành công!</p>';
            } else {
                if (result.message.includes('tối đa (20)')) {
                    deleteSection.style.display = 'block';
                    loadProgressList();
                }
                throw new Error(result.message || 'Lỗi khi lưu progress');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = `<p style="color: red;">Lỗi: ${error.message}</p>`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Lưu progress';
        });
    });
    
    // Xử lý hiển thị danh sách progress để xóa
    document.getElementById('showDeleteProgressBtn')?.addEventListener('click', function() {
        const progressList = document.getElementById('progressList');
        if (progressList.style.display === 'none') {
            loadProgressList();
            progressList.style.display = 'block';
        } else {
            progressList.style.display = 'none';
        }
    });
}

// Giữ nguyên hàm loadProgressList() từ Code 2
function loadProgressList() {
    fetch(`${siteUrl}/api/v1/get-all-progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: Currentusername
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const progressList = document.getElementById('progressList');
            progressList.innerHTML = '<h4>Danh sách progress (đã lưu ' + result.progress_number + '/20):</h4>';
            
            if (result.data.length === 0) {
                progressList.innerHTML += '<p>Không có progress nào được lưu</p>';
                return;
            }

            // Sắp xếp theo date mới nhất trước
            const sortedProgress = result.data.sort((a, b) => {
                return new Date(b.date) - new Date(a.date);
            });

            sortedProgress.forEach(item => {
                const progressItem = document.createElement('div');
                progressItem.className = 'progress-item';
                progressItem.style.display = 'flex';
                progressItem.style.justifyContent = 'space-between';
                progressItem.style.alignItems = 'center';
                progressItem.style.margin = '5px 0';
                progressItem.style.padding = '10px';
                progressItem.style.backgroundColor = '#f5f5f5';
                progressItem.style.borderRadius = '5px';
                
                progressItem.innerHTML = `
                    <div style="flex: 1;">
                        <div><strong>ID Test:</strong> ${item.id_test}</div>
                        <div><strong>Loại test:</strong> ${item.type_test || 'N/A'}</div>
                        <div><strong>Tiến độ:</strong> ${item.progress} (${item.percent_completed || '0'}%)</div>
                        <div><strong>Ngày lưu:</strong> ${item.date}</div>
                    </div>
                    <button class="delete-progress-btn" data-id="${item.id_test}" 
                            style="background-color: #f44336; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer;">
                        Xóa
                    </button>
                `;
                
                progressList.appendChild(progressItem);
            });

            // Thêm sự kiện click cho các nút xóa
            document.querySelectorAll('.delete-progress-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idToDelete = this.getAttribute('data-id');
                    deleteProgressRecord(idToDelete);
                });
            });
        } else {
            throw new Error(result.message || 'Lỗi khi tải danh sách progress');
        }
    })
    .catch(error => {
        console.error('Error loading progress list:', error);
        const progressList = document.getElementById('progressList');
        progressList.innerHTML = `<p style="color: red;">Lỗi: ${error.message}</p>`;
    });
}
function deleteProgressRecord(idToDelete) {
    if (!confirm('Bạn có chắc chắn muốn xóa progress này?')) return;

    fetch(`${siteUrl}/api/v1/delete-progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: Currentusername,
            id_test: idToDelete
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Hiển thị thông báo thành công
            const notification = document.createElement('div');
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.backgroundColor = '#4CAF50';
            notification.style.color = 'white';
            notification.style.padding = '15px';
            notification.style.borderRadius = '5px';
            notification.style.zIndex = '1000';
            notification.textContent = 'Xóa thành công!';
            
            document.body.appendChild(notification);
            
            // Tự động ẩn thông báo sau 3 giây
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => document.body.removeChild(notification), 500);
            }, 3000);

            // Load lại danh sách
            loadProgressList();
        } else {
            throw new Error(result.message || 'Lỗi khi xóa progress');
        }
    })
    .catch(error => {
        console.error('Error deleting progress:', error);
        alert('Lỗi khi xóa: ' + error.message);
    });
}
function closeModal() {
    const modal = document.getElementById("questionModalProgress");
    modal.style.display = "none";
}
function showReviewPage() {
    let quizSections = document.querySelectorAll(".quiz-section");
    let reviewPage = document.querySelectorAll(".review-page");
    document.getElementById("prev-button").disabled = true; // Disable nút
    document.getElementById("next-button").disabled = true; // Disable nút

    quizSections.forEach(section => {
        section.style.display = "none";
    });
    reviewPage.forEach(section1 => {
        section1.style.display = "block";
    });

    // Hiển thị contentCheckboxes2 của câu hỏi hiện tại
    const currentQuestion = document.querySelector('.questions[style="display:block"]');
    if (currentQuestion) {
        const currentReviewPage = currentQuestion.querySelector('.review-page');
        if (currentReviewPage) {
            const currentModule = currentReviewPage.querySelector('[id^="module-2-"]');
            if (currentModule) {
                currentModule.style.display = "block";
            }
        }
    }

    // Tạo nút "Switch to next module" hoặc "Submit Test"
    const currentModule = document.getElementById("current_module").textContent.trim();
    const nextModule = getNextModule(currentModule);

    const reviewContent = document.getElementById("submit-review-content");
    reviewContent.innerHTML = `
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
            ${nextModule ? 
                `<button id="switch-module-button" class = "next-module-review" style="padding: 10px 20px; font-size: 16px;">Switch to next module</button>` :
                `<button id="submit-test-button" class = "next-module-review"  style="padding: 10px 20px; font-size: 16px;">Submit Test</button>`
            }
            <button class="close-review" onclick="closeReviewPage()" style="padding: 10px 20px; font-size: 16px;">Close Review</button>
        </div>
    `;

    if (nextModule) {
        document.getElementById("switch-module-button").onclick = () => switchToNextModule(nextModule);
    } else {
        document.getElementById("submit-test-button").onclick = PreSubmit;
    }
}

// Hàm lấy module tiếp theo
function getNextModule(currentModule) {
    const modules = [...new Set(quizData.questions.map(q => q.question_category))];
    const currentIndex = modules.indexOf(currentModule);
    return currentIndex < modules.length - 1 ? modules[currentIndex + 1] : null;
}

// Hàm chuyển sang module tiếp theo




let moduleTimeRemaining = {};


let partMod = 0;
let orderedModules = [];

function initializeModuleTimers() {
    console.log("Initializing module timers");

    const fixedOrder = [
        "Section 1: Reading And Writing",
        "Section 2: Reading And Writing",
        "Section 1: Math",
        "Section 2: Math"
    ];


    orderedModules = [];
    fixedOrder.forEach(moduleName => {
        if (sectionTimes[moduleName]) {
            orderedModules.push(moduleName);
            moduleTimeRemaining[moduleName] = parseInt(sectionTimes[moduleName]) * 60;
        } else if (quizData.full_test_specific_module && quizData.full_test_specific_module[moduleName]) {
            orderedModules.push(moduleName);
            moduleTimeRemaining[moduleName] = parseInt(quizData.full_test_specific_module[moduleName].time) * 60;
        }
    });

    if (orderedModules.length > 0) {
        const currentModule = orderedModules[partMod];
        countdownValue = moduleTimeRemaining[currentModule];
        document.getElementById('countdown').innerHTML = secondsToHMS(countdownValue);
    }
}

function switchToNextModule() {
    partMod++;
    if (partMod < orderedModules.length) {
        const nextModule = orderedModules[partMod];
        updateModuleTimer(nextModule);

        const firstQuestionIndex = quizData.questions.findIndex(q => q.question_category === nextModule);
        if (firstQuestionIndex !== -1) {
            showQuestion(firstQuestionIndex);
        }

        console.log("Switched to module:", nextModule, "| partMod:", partMod);
       
    } else {
        console.log("All modules finished.");
        autoSubmitTest(); // hoặc hành động khi hết test
    }

}

function updateModuleTimer(currentModule) {
    if (moduleTimeRemaining[currentModule]) {
        countdownValue = moduleTimeRemaining[currentModule];
        document.getElementById('countdown').innerHTML = secondsToHMS(countdownValue);
    }
}



function closeReviewPage() {
    //let reviewPage = document.getElementById("review-page-" + module_question);
    let quizSections = document.querySelectorAll(".quiz-section");
    let reviewPage = document.querySelectorAll(".review-page");
    document.getElementById("prev-button").disabled = false; // Disable nút
    document.getElementById("next-button").disabled = false; // Disable nút

    /*if (reviewPage) {
        reviewPage.style.display = "none";
    }*/
    quizSections.forEach(section => {
        section.style.display = "block";
    });
    reviewPage.forEach(section1 => {
        section1.style.display = "none";
    });
}

function PreSubmit() {
    Swal.fire({
        title: "Bạn có chắc muốn nộp bài ?",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Nộp bài ngay",
        cancelButtonText: "Hủy"
    }).then(async (result) => { // Thêm async ở đây
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
                    timerInterval = setInterval(() => {}, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                    submitButton();
                    DoingTest = false;
                }
            }).then(async (result) => { // Thêm async ở đây
                if (result.dismiss === Swal.DismissReason.timer) {
                    console.log("Displayed Result");
                    await submitAnswerAndGenerateLink(); // Thêm await
                }
            });
        }
    });
}

async function submitAnswerAndGenerateLink() {
    try {
        // Đảm bảo các hàm này chạy xong trước khi hiển thị thông báo
        await filterAnswerForEachType();
       // await ResultInput();
        
        // Chỉ hiển thị thông báo sau khi mọi thứ đã hoàn thành
        Swal.fire({
            title: "Kết quả đã có",
            html: "Chúc mừng bạn đã hoàn thành bài thi. Click vào link dưới để nhận kết quả ngay <i class='fa-regular fa-face-smile'></i>",
            allowOutsideClick: false,
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Xem kết quả",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${siteUrl}/digitalsat/result/${resultId}`;
            }
        });
    } catch (error) {
        console.error("Error submitting answers:", error);
        Swal.fire({
            title: "Lỗi",
            text: "Đã có lỗi xảy ra khi nộp bài. Vui lòng thử lại.",
            icon: "error"
        });
    }
}
</script>
<?php
echo'
<!--<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/main__sat_11.js"></script> -->

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/translate.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/zoom-text.js"></script>

<!--<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/toggle-time-remaining-container.js"></script>-->
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/report-error.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/note-sidebar.js"></script>

<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/submit_answer_9.js"></script>
<!--<script type="module" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/check_dev_tool.js"></script>
    -->
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/highlight_text_4.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/fullscreen.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/format-time-3.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/draft-popup.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/start-digital-sat-Test-7.js"></script>
<script type="text/javascript" src="'. $site_url .'/contents/themes/tutorstarter/system-test-toolkit/function/checkboxAndRemember.js"></script>

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
    //get_footer();


    
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
                    table: 'digital_sat_test_list',
                    change_token: '$token_need',
                    payment_gate: 'token',
                    type_token: 'token',
                    title: 'Renew test $testname with $id_test (Digital Sat) with $token_need (Buy $time_allow time do this test)',
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
    else{
        get_header();
        echo "<p>Không tìm thấy đề thi.</p>";
        exit();
    }
}


else {
    get_header();
    echo "<p>Please log in to submit your answer.</p>";
    //get_footer();
}
get_footer();

