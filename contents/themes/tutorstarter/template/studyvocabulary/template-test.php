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
    // Fetch test data if available
    $data = $result->fetch_assoc();
    $testname = $data['testname'];
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;

    $current_username = $current_user->user_login;


    echo '<script>
        var currentUsername = "' . $current_username . '";
        var currentUserid = "' . $current_user_id . '";
        console.log("Current Username: " + currentUsername);
        console.log("Current User ID: " + currentUserid);
    </script>';

    add_filter('document_title_parts', function ($title) use ($testname) {
        $title['title'] = $testname; // Use the $testname variable from the outer scope
        return $title;
    });
    $site_url = get_site_url();

    echo '<script>';
    echo 'const packageDetail = ' . json_encode($package_detail) . ';';
    echo 'const currentIdTest = "' . $id_test . '";';
    echo 'const packageId = "' . $id_package . '";';
    echo 'const siteUrl = "' . $site_url . '";';
    echo '</script>';
// Initialize quizData structure

echo "<script>";
echo "const vocabList = [";
// Normalize and split question_choose
$question_choose_cleaned = preg_replace('/\s*,\s*/', ',', trim($data['question_choose']));
$questions = explode(",", $question_choose_cleaned);
$first = true;

foreach ($questions as $question_id) {
    if (strpos($question_id, "vocabulary") === 0) {
        // Query only from list_vocabulary table
        $sql_question = "SELECT id, new_word, language_new_word, vietnamese_meaning,english_explanation ,image_link,example FROM list_vocabulary WHERE id = ?";
        $stmt_question = $conn->prepare($sql_question);
        $stmt_question->bind_param("s", $question_id);
        $stmt_question->execute();
        $result_question = $stmt_question->get_result();

        if ($result_question->num_rows > 0) {
            $question_data = $result_question->fetch_assoc();

            if (!$first) echo ",";
            $first = false;
      
            echo "{";
            echo "id_vocab: " . json_encode($question_data['id']) . ",";
            echo "vocab: " . json_encode($question_data['new_word']) . ",";
            echo "language_vocab: " . json_encode($question_data['language_new_word']) . ",";
            echo "vietnamese_meaning: " . json_encode($question_data['vietnamese_meaning']) . ",";
            echo "explanation: " . json_encode($question_data['english_explanation']) . ",";
            echo "example: " . json_encode($question_data['example']) . ",";

            echo "}";
        }
    }
}
$testname = $data['testname'] ?? "Test name not found";
// Close the questions array and the main object
echo "];";
echo "console.log('Vocabulary List:', vocabList);"; // Debugging line to check vocabList content

echo "</script>";





// Close statement and connection
$stmt->close();
$conn->close();
    
    
get_header(); // Gọi phần đầu trang (header.php)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcard App</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<style>
    body {
    font-family: Arial, sans-serif;
    /*background-color: #1b1b32;
    color: white; */

    justify-content: center;
    align-items: center;
    width: 100%;
}
.progress {
    margin-top: 20px;
    width: 100%;
    background-color: #f4f4f4;
    border-radius: 5px;
    overflow: hidden;
    height: 25px;
    margin-bottom: 15px;
  }
  
  .progress-bar {
    height: 100%;
    background-color: #007bff;
    transition: width 0.4s ease;
  }
  

  .flashcard-container {
    display: block;
    margin-left: auto;
    margin-right: auto;
    height: 600px;
    text-align: center;
    width: 90%;
}

.flashcard {
    margin: auto;
    width: 50%;
    padding: 10px;
    overflow: auto;
    text-align: center;
    justify-content: center;
    font-size: 20px;
    width: 60%;
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
    margin: auto;
    width: 60%;
 


    display: flex;
    justify-content: space-between;
    align-items: center;
}

.control-button {
    background-color: #444;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
}

.control-button:hover {
    background-color: #555;
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
/* Style cho nút "Kiểm tra" */
#check {
    display: inline-flex; /* Nội dung nằm ngang */
    align-items: center; /* Căn giữa dọc */
    gap: 8px; /* Khoảng cách giữa icon và chữ */
    padding: 10px 16px;
    font-size: 16px;
    cursor: pointer;
    border: 1px solid #ccc;
    background-color: #f9f9f9;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

#check:hover {
    background-color: #e0e0e0; /* Hiệu ứng khi rê chuột */
}

#check i {
    font-size: 18px; /* Kích thước biểu tượng */
}

/* Style cho nút "Gợi ý" */
#hintButton, #addNotationBtn, #next {
    display: inline-flex; /* Nội dung nằm ngang */
    align-items: center; /* Căn giữa dọc */
    gap: 8px; /* Khoảng cách giữa icon và chữ */
    padding: 10px 16px;
    font-size: 16px;
    cursor: pointer;
    border: 1px solid #ccc;
    background-color: #f9f9f9;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

#hintButton:hover , #addNotationBtn:hover , #next:hover {
    background-color: #e0e0e0; /* Hiệu ứng khi rê chuột */
}

#hintButton, #addNotationBtn, #next i {
    font-size: 18px; /* Kích thước biểu tượng */
}

.tabs {
    display: flex;
    border-bottom: 1px solid #ccc;
    margin-bottom: 16px;
}

.tabs button {
    flex: 1;
    padding: 10px 16px;
    cursor: pointer;
    border: none;
    background-color: #f1f1f1;
    font-size: 16px;
    text-align: center;
}

.tabs button.active {
    background-color: #ffffff;
    border-bottom: 2px solid #007bff;
    font-weight: bold;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th, table td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
}

table th {
    background-color: #f9f9f9;
}

.choice {
    padding: 10px;
    border: 1px solid #ccc;
    margin-bottom: 5px;
    cursor: pointer;
}

.choice:hover {
    background-color: #f0f0f0; /* Màu khi hover */
}

.choice.selected {
    background-color: #d1e7dd; /* Màu nền cho lựa chọn được chọn */
    border-color: #0f5132;    /* Màu viền */
    color: #0f5132;           /* Màu chữ */
}

</style>

<body>
    <h4 style = "color: black">Vocabulary Quiz: <?php echo htmlspecialchars($testname); ?></h4>

    <div class="flashcard-container">
        <div class="progress">
            <div class="progress-bar" id="progressBar" style="width: 0%;"></div>
          </div>
          <p id="questionStatus"></p> <!-- Hiển thị số câu hiện tại -->

        <div id="flashcard" class="flashcard">
            <p id="explanationText"></p>
            <p id="definitionText"></p>
            <p id="exampleText"></p>
            <p id="vocabText"></p>
            <input type="text" id="vocabInput" placeholder="Nhập câu trả lời của bạn ...">
            <!-- Nút kiểm tra -->
           
            <button id="check">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="arcs"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Kiểm tra
            </button>

            <!-- Nút gợi ý -->
            <button id="hintButton">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="arcs"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/></svg> Gợi ý
            </button>

            <button id="addNotationBtn" onclick = "addNotation()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 11.08V8l-6-6H6a2 2 0 0 0-2 2v16c0 1.1.9 2 2 2h6"/><path d="M14 3v5h5M18 21v-6M15 18h6"/></svg> Notation
            </button>

  
            <button id="next" >
                 <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="arcs"><path d="M10 3H6a2 2 0 0 0-2 2v14c0 1.1.9 2 2 2h4M16 17l5-5-5-5M19.8 12H9"/></svg>Bỏ qua
            </button>
      



            <p id="hintMessage"></p> <!-- Nơi hiển thị gợi ý -->

            <p id="resultMessage"></p>
        </div>


         <button id="btn-check-flashcard" class="control-button">Học lại set này - Flashcard</button>

        <div class="controls-">
            <button id="btn-prev-test" class="control-button">Làm test set trước</button> 
            <button id="btn-next-test" class="control-button">Làm test set tiếp theo</button>
        </div>


        
    </div>
    <div id="resultContainer" style="display: none;">
        <h2>Kết quả</h2>
        <p>Số câu đúng: <span id="correctCount"></span></p>
        <p>Số câu sai: <span id="incorrectCount"></span></p>
        <p>Số câu bỏ qua: <span id="skippedCount"></span></p>
        <p>Phần trăm đúng: <span id="accuracyPercentage"></span>%</p>
        <p>Thời gian hoàn thành: <span id="completionTime"></span> giây</p>
        <p>Trạng thái: <span id="status"></span></p>
    </div>
    <script>
           let pre_id_test_ = `<?php echo esc_html($custom_number); ?>`;
    </script>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const currentIndex = packageDetail.indexOf(currentIdTest);

            // Nút Flashcard trước đó
            document.getElementById("btn-prev-test").addEventListener("click", () => {
                if (currentIndex > 0) {
                    const prevId = packageDetail[currentIndex - 1];
                    window.location.href = `${siteUrl}/practice/vocabulary/package/${packageId}/${prevId}/test/`;
                } else {
                    alert("Đây là set đầu tiên!");
                }
            });

            // Nút Flashcard tiếp theo
            document.getElementById("btn-next-test").addEventListener("click", () => {
                if (currentIndex < packageDetail.length - 1) {
                    const nextId = packageDetail[currentIndex + 1];
                    window.location.href = `${siteUrl}/practice/vocabulary/package/${packageId}/${nextId}/test/`;
                } else {
                    alert("Đây là set cuối cùng!");
                }
            });

            // Nút Kiểm tra set này
            document.getElementById("btn-check-flashcard").addEventListener("click", () => {
                window.location.href = `${siteUrl}/practice/vocabulary/package/${packageId}/${currentIdTest}/flashcard/`;
            });
        });
        hidePreloader();
        </script>

<?php echo'
    <script src="'. $site_url .'/contents/themes/tutorstarter/study_vocabulary_toolkit/test-new-word/script1.js"></script>'
    ?>

</body>
</html>



<?php
} else {
    echo "No tests found !";
    }
get_footer();
/*} else {
    get_header();
    echo '<p>Please log in start reading test.</p>';
    get_footer();
}*/