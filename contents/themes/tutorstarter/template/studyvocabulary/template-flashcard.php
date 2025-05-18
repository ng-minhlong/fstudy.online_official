<?php
/*
 * Template Name: Flash Card Vocabulary
 * Template Post Type: studyvocabulary
 
 */


 if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

remove_filter('the_content', 'wptexturize');
remove_filter('the_title', 'wptexturize');
remove_filter('comment_text', 'wptexturize');

//if (is_user_logged_in()) {
$post_id = get_the_ID();
$user_id = get_current_user_id();
$additional_info = get_post_meta($post_id, '_studyvocabulary_additional_info', true); 
//$custom_number = get_post_meta($post_id, '_studyvocabulary_custom_number', true);
$id_package = get_query_var('id_package');
$id_test = get_query_var('id_test');
$site_url = get_site_url(); // tương đương siteUrl trong JS

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

// Get package details
$sql_package = "SELECT id_test, package_category, package_name, package_detail FROM list_vocabulary_package WHERE id_test = ?";
$stmt_package = $conn->prepare($sql_package);

if ($stmt_package === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt_package->bind_param("s", $id_package);
$stmt_package->execute();
$result_package = $stmt_package->get_result();

if ($result_package->num_rows === 0) {
    wp_redirect(home_url('/404'));
    exit;
}

$package_data = $result_package->fetch_assoc();
$package_name = $package_data['package_name'];
$package_detail = json_decode($package_data['package_detail'], true);

// Kiểm tra xem id_test có tồn tại trong package_detail không
if (!in_array($id_test, $package_detail)) {
    wp_redirect(home_url('/404'));
    exit;
}

// Nếu id_test hợp lệ, tiếp tục lấy thông tin test
$sql = "SELECT testname, test_type, question_choose, id_test FROM list_test_vocabulary_book WHERE id_test = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt->bind_param("s", $id_test);
$stmt->execute();
$result = $stmt->get_result();

if ($result ->num_rows === 0) {
    wp_redirect(home_url('/404'));
    exit;
}


if ($result->num_rows > 0) {
    // Fetch test data
    $data = $result->fetch_assoc();
    $testname = $data['testname'];



    add_filter('document_title_parts', function ($title) use ($testname) {
        $title['title'] = $testname; // Use the $testname variable from the outer scope
        return $title;
    });
    

    echo '<script>';
    echo 'const packageDetail = ' . json_encode($package_detail) . ';';
    echo 'const currentIdTest = "' . $id_test . '";';
    echo 'const packageId = "' . $id_package . '";';
    echo 'const siteUrl = "' . $site_url . '";';
    echo '</script>';


    // Output JavaScript array
    echo "<script>";
    echo "const vocabList = [];";

    // Normalize and split question_choose
    $question_choose_cleaned = preg_replace('/\s*,\s*/', ',', trim($data['question_choose']));
    $questions = explode(",", $question_choose_cleaned);

    foreach ($questions as $question_id) {
        if (strpos($question_id, "vocabulary") === 0) {
            // Query list_vocabulary table
            $sql_question = "SELECT id, new_word, language_new_word, vietnamese_meaning, english_explanation, image_link, example 
                             FROM list_vocabulary 
                             WHERE id = ?";
            $stmt_question = $conn->prepare($sql_question);
            $stmt_question->bind_param("s", $question_id);
            $stmt_question->execute();
            $result_question = $stmt_question->get_result();

            if ($result_question->num_rows > 0) {
                $question_data = $result_question->fetch_assoc();
                echo "vocabList.push(" . json_encode([
                    'vocab' => $question_data['new_word'],
                    'explanation' => $question_data['english_explanation'],
                    'vietnamese_meaning' => $question_data['vietnamese_meaning'],
                    'language_vocab' => $question_data['language_new_word'],
                    'example' => $question_data['example']
                ]) . ");";
            }
            $stmt_question->close();
        }
    }

    // Output testname
    $testname = $data['testname'] ?? "Test name not found";
    echo "console.log('Test name: " . addslashes($testname) . "');";
    echo "</script>";

// Close statement and connection
$stmt->close();
$conn->close();
get_header(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcard App</title>
    
    <!--<link rel="stylesheet" href="http://localhost/contents/themes/tutorstarter/study_vocabulary_toolkit/flash-card-app/styles.css">-->
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        /*background-color: #1b1b32; */
        /*color: white;*/
       /* background-color: #f8f9fa!important;*/

        justify-content: center;
        align-items: center;
        width: 100%;
    }


   @media only screen and (max-width: 600px) {
    .flashcard-container .flashcard {
        width: 100% !important;
    }
}


.flashcard-container {
    color: white;
    display: block;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    width: 90%;
    /*max-width: 600px; */
}

.flashcard {
    margin: auto;
    padding: 10px;
    overflow: auto;
    text-align: center;
    justify-content: center;
    font-size: 30px;
    width: 60%;
    height: 400px;
    perspective: 1000px;
   /* margin-bottom: 50px; */
}

.card-inner {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.6s;
}

.flashcard.flipped .card-inner {
    transform: rotateY(180deg);
}

.card-front, .card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    border-radius: 10px;
    padding: 20px;
}

.card-front {
    background-color: #2b2b40;
}

.card-back {
    background-color: #444;
    transform: rotateY(180deg);
}

.audio-button {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: transparent;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}

.audio-button:hover {
    color: #aaa;
}

.controls {
    margin: 20px auto;
    width: 100%;
    max-width: 600px;
    padding: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.control-button {

    background-color: #444;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;


    flex: 1 1 auto;
    min-width: 100px;
    text-align: center;
}


.control-button:hover {
    background-color: #555;
}

.vocab-table-wrapper {
    overflow-x: auto;
    width: 100%;
}


.vocab-table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    background-color: #2b2b40;
    color: white;
    text-align: left;
}

.vocab-table th, .vocab-table td {
    border: 1px solid #444;
    padding: 10px;
}

.vocab-table th {
    background-color: #444;
}

.vocab-table tr:nth-child(even) {
    background-color: #333;
}
</style>
<body>
    <h4 >Flashcard set: <?php echo htmlspecialchars($testname); ?></h4>
    
    <div class="flashcard-container">
        <div id="flashcard" class="flashcard">
            <div class="card-inner">
                <div class="card-front">
                    <button id="audioButton" class="audio-button">🔊</button>
                    <p id="vocabText">Vocab</p>
                </div>
                <div class="card-back">
                    <p id="definitionText">Definition</p>
                    <p id="explanationText">Explanation</p>
                    
                </div>
            </div>
        </div>
        <div class="controls">
            <button id="prev" class="control-button">← Prev</button>
            <span id="progress">1 / 1</span>
            <button id="next" class="control-button">Next →</button>
            <button id="fullscreen" class="control-button">⛶ Fullscreen</button>
            <button id="settings" class="control-button">⚙ Settings</button>
        </div>

        <button id="btn-check-set" class="control-button">Kiểm tra set này</button>

        <div class="controls-">
            <button id="btn-prev-flashcard" class="control-button">Flashcard set trước đó</button> 
            <button id="btn-next-flashcard" class="control-button">Flashcard set tiếp theo</button>
        </div>


        <div class="vocab-table-wrapper">


            <table id="vocabTable" class="vocab-table">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Vocab</th>
                        <th>Vietnamese Meaning</th>
                        <th>Explanation</th>
                        <th>Example</th>
                        <th>Pronunciation</th>
                        
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        
    </div>

    <?php echo'
    <script src="'. $site_url .'/contents/themes/tutorstarter/study_vocabulary_toolkit/flash-card-app/script5.js"></script>
    '
    ?>
</html>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const currentIndex = packageDetail.indexOf(currentIdTest);

    // Nút Flashcard trước đó
    document.getElementById("btn-prev-flashcard").addEventListener("click", () => {
        if (currentIndex > 0) {
            const prevId = packageDetail[currentIndex - 1];
            window.location.href = `${siteUrl}/practice/vocabulary/package/${packageId}/${prevId}/flashcard/`;
        } else {
            alert("Đây là set đầu tiên!");
        }
    });

    // Nút Flashcard tiếp theo
    document.getElementById("btn-next-flashcard").addEventListener("click", () => {
        if (currentIndex < packageDetail.length - 1) {
            const nextId = packageDetail[currentIndex + 1];
            window.location.href = `${siteUrl}/practice/vocabulary/package/${packageId}/${nextId}/flashcard/`;
        } else {
            alert("Đây là set cuối cùng!");
        }
    });

    // Nút Kiểm tra set này
    document.getElementById("btn-check-set").addEventListener("click", () => {
        window.location.href = `${siteUrl}/practice/vocabulary/package/${packageId}/${currentIdTest}/test/`;
    });
});
</script>


<?php

} else {
    echo "No tests found";
}


get_footer();
/*} else {
    get_header();
    echo '<p>Please log in start reading test.</p>';
    get_footer();
}*/