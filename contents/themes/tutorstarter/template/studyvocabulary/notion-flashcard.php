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
$sql = "SELECT word_save, meaning_and_explanation FROM notation WHERE username = ?";
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
        
        if ($word !== '' && $meaning !== '') {
            echo "vocabList.push(" . json_encode([
                'vocab' => $word,
                'explanation' => $meaning
            ]) . ");";
        }
    }
    
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
    <h4 >Luyện FlashCard cho các Notion đã lưu </h4>
    
    <div class="flashcard-container">
        <div id="flashcard" class="flashcard">
            <div class="card-inner">
                <div class="card-front">
                    <p id="vocabText">Vocab</p>
                </div>
                <div class="card-back">
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

    


        <div class="vocab-table-wrapper">


            <table id="vocabTable" class="vocab-table">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Vocab</th>
                        <th>Explanation</th>
                        
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        
    </div>

    
</html>
<script>

    
let currentIndex = 0;
const flashcard = document.getElementById("flashcard");
const vocabText = document.getElementById("vocabText");
const explanationText = document.getElementById("explanationText");

const progress = document.getElementById("progress");

function updateFlashcard() {
    flashcard.classList.remove("flipped"); // Reset to vocab side

    setTimeout(function() {
        
    const current = vocabList[currentIndex];
    vocabText.innerHTML = current.vocab;
    explanationText.innerHTML = current.explanation;
    progress.innerHTML = `${currentIndex + 1} / ${vocabList.length}`;
        }, 500); 
        


}

flashcard.addEventListener("click", () => {
    flashcard.classList.toggle("flipped");
});

document.getElementById("prev").addEventListener("click", () => {
    if (currentIndex > 0) {
        currentIndex--;
        updateFlashcard();
    }
});

document.getElementById("next").addEventListener("click", () => {
    if (currentIndex < vocabList.length - 1) {
        currentIndex++;
        updateFlashcard();
    }
});

document.getElementById("fullscreen").addEventListener("click", () => {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
});

document.getElementById("settings").addEventListener("click", () => {
    alert("Settings feature coming soon!");
});



// Initialize the first flashcard
updateFlashcard();

// Reference to the table body
const vocabTableBody = document.querySelector("#vocabTable tbody");

// Function to populate the vocab table
function populateVocabTable() {
    vocabList.forEach((word, index) => {
        const row = document.createElement("tr");

        // Số thứ tự
        const indexCell = document.createElement("td");
        indexCell.textContent = index + 1;

        // Vocab (double-click to play audio)
        const vocabCell = document.createElement("td");
        vocabCell.textContent = word.vocab;
        vocabCell.addEventListener("dblclick", () => {
            playTTS(word.vocab, word.language_vocab);
        });


        // Vietnamese Meaning
        const meaningCell = document.createElement("td");

        // Explanation
        const explanationCell = document.createElement("td");
        explanationCell.textContent = word.explanation;

      


        row.appendChild(indexCell);
        row.appendChild(vocabCell);
        row.appendChild(explanationCell);
    
        
        vocabTableBody.appendChild(row);
    });
}


// Initialize the vocab table
populateVocabTable();




</script>
<script>
document.addEventListener("DOMContentLoaded", () => {  
    // Nút Kiểm tra set này
    document.getElementById("btn-check-set").addEventListener("click", () => {
        window.location.href = `${siteUrl}/practice/notion/test/`;
    });
});
hidePreloader();
</script>


<?php

} else {
    echo "Bạn chưa có từ nào trong Notion. Vui lòng thêm từ mới trước khi luyện tập flashcard. <a href='" . $site_url . "/dashboard/notion'>Thêm từ mới</a>";
}


get_footer();
/*} else {
    get_header();
    echo '<p>Please log in start reading test.</p>';
    get_footer();
}*/