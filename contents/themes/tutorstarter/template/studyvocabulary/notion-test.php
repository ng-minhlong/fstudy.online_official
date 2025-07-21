<?php
/*
 * Template Name: Flash Card Notion
 * Template Post Type: studyvocabulary
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

remove_filter('the_content', 'wptexturize');
remove_filter('the_title', 'wptexturize');
remove_filter('comment_text', 'wptexturize');

$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$site_url = get_site_url();

add_filter('document_title_parts', function ($title) {
    $title['title'] = 'Flashcard Notion';
    return $title;
});



get_header();
if (!is_user_logged_in()) {
    echo '<p>Vui lòng đăng nhập để sử dụng tính năng này.</p>';
    echo '<a href="' . $site_url . '/dashboard">Đăng nhập</a>';
    get_footer();
    return; // Dừng lại nếu chưa đăng nhập
}


$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Truy vấn dữ liệu
$sql = "SELECT word_save, meaning_and_explanation, number FROM notation WHERE username = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu có ít nhất 1 dòng dữ liệu
if ($result->num_rows > 0) {
    echo '<script>';
    echo 'const siteUrl = ' . json_encode($site_url) . ';';
    echo 'const vocabList = [];';
    
    while ($row = $result->fetch_assoc()) {
        $word = trim($row['word_save']);
        $meaning = trim($row['meaning_and_explanation']);
        $id_vocab = trim($row['number']);

        if ($word !== '' && $meaning !== '') {
            echo "vocabList.push(" . json_encode([
                'id_vocab' => $id_vocab,
                'vocab' => $word,
                'vietnamese_meaning' => $meaning,
                'example' => '', // Giả sử không có ví dụ trong dữ liệu
                'explanation' => $meaning
            ]) . ");";
        }
    }
    echo "console.log('Vocabulary List:', vocabList);"; // Debugging line to check vocabList content

    echo '</script>';


$stmt->close();
$conn->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcard App</title>

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
    <h4 style = "color: black">Test Flashcard Notion</h4>

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
            
            // Nút Kiểm tra set này
            document.getElementById("btn-check-flashcard").addEventListener("click", () => {
                window.location.href = `${siteUrl}/practice/notion/flashcard/`;
            });
        });
        hidePreloader();
        </script>


<script>
    
const quizType1Questions = vocabList.map(vocab => ({
    type: "quiz-type-1",
    level:"medium",
    vocab: vocab.vocab,
    vietnamese_meaning: vocab.vietnamese_meaning,
    explanation: vocab.explanation,
    example: vocab.example
}));

const quizType2Questions = vocabList
    .filter(vocab => vocabList.some(other => other.example.includes(vocab.vocab)))
    .map(vocab => ({
        type: "quiz-type-2",
        level:"advanced",
        explanation: vocab.explanation,
        example: vocab.example.replace(new RegExp(`\\b${vocab.vocab}\\b`, "gi"), "______"),
        vocab: vocab.vocab
    }));

    const quizType3Questions = vocabList
    .filter(vocab => vocab.vocab && vocab.vietnamese_meaning)
    .map(vocab => {
        // Lấy 4 definitionText ngẫu nhiên từ các từ khác
        const otherDefinitions = vocabList
            .filter(other => other.id_vocab !== vocab.id_vocab) // Bỏ qua từ chính
            .map(other => other.vietnamese_meaning);

        const randomDefinitions = otherDefinitions
            .sort(() => Math.random() - 0.5) // Trộn ngẫu nhiên
            .slice(0, 4); // Lấy 4 phần tử đầu tiên

        // Thêm definitionText của từ chính vào danh sách
        const allDefinitions = [
            vocab.vietnamese_meaning,
            ...randomDefinitions
        ].sort(() => Math.random() - 0.5); // Trộn lại ngẫu nhiên

        return {
            type: "quiz-type-3",
            level:"easy",
            vocab: vocab.vocab,
            correctDefinition: vocab.vietnamese_meaning,
            definitions: allDefinitions // Danh sách 5 definitionText
        };
    });

// Thêm quizType3Questions vào danh sách câu hỏi
const allQuestions = [...quizType3Questions, ...quizType1Questions, ...quizType2Questions];
const totalQuestions = allQuestions.length;


let hintStep = 0; // Theo dõi gợi ý hiện tại

let currentIndex = 0;
const flashcard = document.getElementById("flashcard");
const vocabText = document.getElementById("vocabText");
const definitionText = document.getElementById("definitionText");
const explanationText = document.getElementById("explanationText");

const progress = document.getElementById("progress");
const audioButton = document.getElementById("audioButton");
const vocabInput = document.getElementById("vocabInput");
const checkButton = document.getElementById("check");
const resultMessage = document.getElementById("resultMessage");

const resultContainer = document.getElementById("resultContainer");
const correctCountElement = document.getElementById("correctCount");
const incorrectCountElement = document.getElementById("incorrectCount");
const skippedCountElement = document.getElementById("skippedCount");
const accuracyPercentageElement = document.getElementById("accuracyPercentage");
const completionTimeElement = document.getElementById("completionTime");
const statusElement = document.getElementById("status");
let correctAnswers = 0; // Số câu trả lời đúng

let correctCount = 0;
let incorrectCount = 0;
let skippedCount = 0;
let startTime = Date.now();
const questionStatus = document.getElementById("questionStatus");


const hintButton = document.getElementById("hintButton");
const hintMessage = document.getElementById("hintMessage");
let attempts = 0; // Số lần kiểm tra câu hiện tại
function checkAnswer() {
    const current = allQuestions[currentIndex];
    const userAnswer = vocabInput.value.trim();

    if (current.type === "quiz-type-1" || current.type === "quiz-type-2") {
        if (userAnswer.toLowerCase() === current.vocab.toLowerCase()) {
            resultMessage.textContent = "Correct!";
            correctAnswers++;
            correctCount++;
            updateProgressBar();
            currentIndex++;
            attempts = 0; // Reset số lần kiểm tra
            updateFlashcard();
        } else {
            attempts++;
            if (attempts >= 3) {
                resultMessage.textContent = "Incorrect. Moving to the next question.";
                incorrectCount++;
                currentIndex++;
                attempts = 0; // Reset số lần kiểm tra
                updateFlashcard();
            } else {
                resultMessage.textContent = `Incorrect. Attempts left: ${3 - attempts}`;
            }
        }
    } else if (current.type === "quiz-type-3") {
        if (userAnswer === current.correctDefinition) {
            resultMessage.textContent = "Correct!";
            correctAnswers++;
            correctCount++;
            updateProgressBar();
            currentIndex++;
            attempts = 0; // Reset số lần kiểm tra
            updateFlashcard();
        } else {
            attempts++;
            if (attempts >= 3) {
                resultMessage.textContent = "Incorrect. Moving to the next question.";
                incorrectCount++;
                currentIndex++;
                attempts = 0; // Reset số lần kiểm tra
                updateFlashcard();
            } else {
                resultMessage.textContent = `Incorrect. Attempts left: ${3 - attempts}`;
            }
        }
    }
}

checkButton.addEventListener("click", checkAnswer);

hintButton.addEventListener("click", () => {
    const current = allQuestions[currentIndex];
    if (hintStep === 0) {
        hintMessage.innerHTML = `Hint 1: Từ này có ${current.vocab.length} chữ cái.<br>`;
        hintButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="arcs"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/></svg> Gợi ý thêm';
        hintStep++;
    } else if (hintStep === 1) {
        hintMessage.innerHTML += ` Hint 2: Chữ cái đầu là "${current.vocab.charAt(0)}".<br>`;
        hintStep++;
    } else if (hintStep === 2) {
        hintMessage.innerHTML += ` Hint 3: Chữ cái cuối là "${current.vocab.charAt(current.vocab.length - 1)}".<br>`;
        hintStep++;
    }
    else if (hintStep === 3) {
        const shuffledVocabList = vocabList
            .sort(() => Math.random() - 0.5) // Trộn ngẫu nhiên
            .map(vocab => vocab.vocab) // Lấy tên từ vựng
            .join(", "); // Nối thành chuỗi

        hintMessage.innerHTML = `Đáp án là 1 trong các từ: ${shuffledVocabList} <br> Bạn đã hết gợi ý cho câu này. Vui lòng bỏ qua nếu bạn không biết`;
        hintStep++;
    }
     
});


function addNotation(){
    const current = allQuestions[currentIndex];
    //explanationText.textContent = `Explanation: ${current.explanation || ''}`;
    //questionStatus.textContent = `Câu số ${currentIndex + 1}/${totalQuestions} (Level: ${current.level})`;
    if (current.type === "quiz-type-3") {
        let notationWord = current.vocab;
     
        console.log("Add to Notation: ",notationWord);
        (async () => {
            const { value: notationSaveWord } = await Swal.fire({
                title: "Notation",
                html: `Lưu từ <strong>${notationWord}</strong> vào mục Notation?`,
                input: "select",
                inputOptions: {
                    quick_save: "Lưu nhanh",
                    detail_save: "Thêm định nghĩa"
                },
                inputPlaceholder: "Lựa chọn lưu",
                showCancelButton: true,
                inputValidator: (value) => {
                    return !value ? "Vui lòng chọn phương án!" : undefined;
                }
            });
    
            if (notationSaveWord === "quick_save") {
                await saveNotation(notationWord, "", "web", pre_id_test_ || "0", "Vocabulary");
                Swal.fire("Lưu thành công!", "Từ đã được lưu nhanh.", "success");
            } else if (notationSaveWord === "detail_save") {
                const { value: definition } = await Swal.fire({
                    title: "Nhập định nghĩa",
                    input: "text",
                    inputPlaceholder: "Nhập định nghĩa cho từ...",
                    showCancelButton: true,
                    inputValidator: (value) => {
                        return !value ? "Vui lòng nhập định nghĩa!" : undefined;
                    }
                });
    
                if (definition) {
                    await saveNotation(notationWord, definition, "web", pre_id_test_ || "0", "Vocabulary");
                    Swal.fire("Lưu thành công!", "Từ và định nghĩa đã được lưu.", "success");
                }
            }
            resultId++;
            async function saveNotation(word, definition, source, id_test, test_type) {
                const saveTime = new Date().toISOString().split('T')[0]; // Chỉ lấy phần ngày
           

                await fetch(`${siteUrl}/wp-json/api/v1/save-notation`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        action: "save_notation",
                        word_save: word,
                        meaning_or_explanation: definition,
                        save_time: saveTime,
                        is_source: source,
                        username: currentUsername,
                        user_id: currentUserid,
                        id_test: id_test,
                        id_note: resultId,
                        test_type: test_type,
                    })
                });




            }
        })();
    }
    else if (current.type = "quiz-type-2"){
        
    }
}

function updateProgressBar() {
    const progressPercentage = (correctAnswers / totalQuestions) * 100; // Tính phần trăm
    const progressBar = document.getElementById("progressBar");
    progressBar.style.width = `${progressPercentage}%`; // Cập nhật chiều rộng progress bar
}

function updateFlashcard() {
    if (currentIndex >= allQuestions.length) {
        showResults();
        return;
    }

    const current = allQuestions[currentIndex];
    explanationText.textContent = `Explanation: ${current.explanation || ''}`;
    questionStatus.textContent = `Câu số ${currentIndex + 1}/${totalQuestions} (Level: ${current.level})`;

    if (current.type === "quiz-type-1") {
        document.getElementById("addNotationBtn").style.display = "none";
        definitionText.textContent = `Vietnamese Meaning: ${current.vietnamese_meaning}`;
        exampleText.textContent = ``;
        vocabText.textContent = '';
        vocabInput.style.display = "block";
    } else if (current.type === "quiz-type-2") {
        definitionText.textContent = ""; // Không hiển thị nghĩa tiếng Việt
        vocabText.textContent = `What is the vietnamese meaning of this word ?`;
        exampleText.textContent = `Fill in the blank: ${current.example}`;
        vocabInput.style.display = "block";
        document.getElementById("addNotationBtn").style.display = "none";
    } else if (current.type === "quiz-type-3") {
        vocabText.textContent = `What is the meaning of this word: ${current.vocab}`;
        vocabInput.style.display = "none";
        definitionText.innerHTML = current.definitions
            .map(def => `<div class="choice">${def}</div>`)
            .join('');
    
        // Thêm sự kiện click cho mỗi lựa chọn
        document.querySelectorAll('.choice').forEach(choice => {
            choice.addEventListener('click', () => {
                // Xóa background khỏi tất cả các lựa chọn
                document.querySelectorAll('.choice').forEach(c => c.classList.remove('selected'));
                
                // Đặt lớp 'selected' cho lựa chọn hiện tại
                choice.classList.add('selected');
                
                // Cập nhật giá trị input
                vocabInput.value = choice.textContent;
            });
        });
    }
    

    vocabInput.value = "";
    resultMessage.textContent = "";
    hintStep = 0;
    hintMessage.textContent = "";
    //hintButton.textContent = "Gợi ý";
}


// Logic kéo thả cho quiz-type-3
let draggedElement = null;
document.addEventListener('dragstart', event => {
    if (event.target.classList.contains('draggable')) {
        draggedElement = event.target;
    }
});
document.addEventListener('dragover', event => {
    event.preventDefault();
});
document.addEventListener('drop', event => {
    if (draggedElement && event.target === vocabInput) {
        vocabInput.value = draggedElement.textContent;
        draggedElement = null;
    }
});




document.getElementById("next").addEventListener("click", () => {
    console.log(`Câu số ${currentIndex}: Bỏ qua`);
    skippedCount++;
    currentIndex++;
    updateFlashcard();
});

function showResults() {
    const endTime = Date.now();
    const totalTime = Math.floor((endTime - startTime) / 1000);
    const accuracy = Math.floor((correctCount / vocabList.length) * 100);

    resultContainer.style.display = "block";
    document.querySelector(".flashcard-container").style.display = "none";

    correctCountElement.textContent = correctCount;
    incorrectCountElement.textContent = incorrectCount;
    skippedCountElement.textContent = skippedCount;
    accuracyPercentageElement.textContent = accuracy;
    completionTimeElement.textContent = totalTime;
    statusElement.textContent = accuracy > 80 ? "Đạt" : "Không đạt";
}

// Initialize the first flashcard
updateFlashcard();


</script>
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