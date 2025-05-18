
let currentIndex = 0;
const flashcard = document.getElementById("flashcard");
const vocabText = document.getElementById("vocabText");
const definitionText = document.getElementById("definitionText");
const explanationText = document.getElementById("explanationText");

const progress = document.getElementById("progress");
const audioButton = document.getElementById("audioButton");

// TTS Function (uses your WordPress REST API)
function playTTS(text, lang) {
    const validLang = getLanguageCode(lang);
    const apiUrl = `http://127.0.0.1:5000/tts?text=${encodeURIComponent(text)}&lang=${validLang}`;
    
    // Tạo thẻ audio động
    const audio = new Audio();
    audio.src = apiUrl;
    audio.preload = 'auto';
    
    audio.onerror = function(e) {
        console.error('Audio error:', e);
        alert('Error playing audio. Please try again.');
    };
    
    // Thử phát audio sau khi metadata được tải
    audio.onloadedmetadata = function() {
        audio.play().catch(e => {
            console.error('Playback failed:', e);
            alert('Playback blocked by browser. Please click to play manually.');
        });
    };
    
    // Fallback nếu trình duyệt chặn autoplay
    audio.onplay = function() {
        console.log('Audio started playing');
    };
}

// Helper function to map language names to their respective codes
function getLanguageCode(language) {
    const langMap = {
        'English': 'en',
        'French': 'fr',
        'Chinese': 'zh',
        'Vietnamese': 'vi',
        'Spanish': 'es',
        'German': 'de',
        'Korean': 'ko',
        'Japanese': 'ja',
        'Russian': 'ru'
    };
    return langMap[language] || 'en'; // Default to English if not found
}



function updateFlashcard() {
    flashcard.classList.remove("flipped"); // Reset to vocab side

    setTimeout(function() {
        
    const current = vocabList[currentIndex];
    vocabText.innerHTML = current.vocab;
    definitionText.innerHTML = current.vietnamese_meaning;
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

// Text-to-Speech functionality
audioButton.addEventListener("click", (event) => {
    event.stopPropagation();
    const currentWord = vocabList[currentIndex];
    playTTS(currentWord.vocab, currentWord.language_vocab);
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
        meaningCell.textContent = word.vietnamese_meaning;

        // Explanation
        const explanationCell = document.createElement("td");
        explanationCell.textContent = word.explanation;

        const exampleCell = document.createElement("td");
        exampleCell.textContent = word.example;

        // Phát âm (check confidence level)
        const checkCell = document.createElement("td");
        const checkButton = document.createElement("button");
        checkButton.textContent = "Kiểm tra";
        checkButton.addEventListener("click", () => {
            const currentWord = vocabList[index]; // Get current word
            startSpeechRecognition(currentWord.vocab, checkCell, currentWord.language_vocab); // Pass language_vocab here
        });

        checkCell.appendChild(checkButton);

        row.appendChild(indexCell);
        row.appendChild(vocabCell);
        row.appendChild(meaningCell);
        row.appendChild(explanationCell);
        row.appendChild(exampleCell);
        row.appendChild(checkCell);
        
        vocabTableBody.appendChild(row);
    });
}


async function startSpeechRecognition(targetWord, cell, language) {
    const lang = getLanguageCode(language); // dùng hàm getLanguageCode

    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    const mediaRecorder = new MediaRecorder(stream);
    const audioChunks = [];

    mediaRecorder.ondataavailable = (event) => {
        audioChunks.push(event.data);
    };

    mediaRecorder.onstop = async () => {
        const audioBlob = new Blob(audioChunks, { type: "audio/webm" });

        const formData = new FormData();
        formData.append("audio", audioBlob, "recording.webm");
        formData.append("lang", lang);

        try {
            const response = await fetch("http://127.0.0.1:5000/stt", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();
            const transcript = result.text.toLowerCase();
            const confidence = Math.round(result.confidence * 100);
            console.log(transcript);

            if (transcript === targetWord.toLowerCase() && confidence > 70) {
                cell.textContent = `Bạn phát âm đúng! (Confidence: ${confidence}%)`;
            } else {
                cell.textContent = `Phát âm chưa đúng (Confidence: ${confidence}%)`;
            }
        } catch (error) {
            cell.textContent = "Lỗi khi gửi audio tới server!";
            console.error(error);
        }
    };

    mediaRecorder.start();

    // Ghi âm trong 2 giây rồi dừng lại
    setTimeout(() => {
        mediaRecorder.stop();
    }, 2000);
}


// Initialize the vocab table
populateVocabTable();

