/*const vocabList = [
    { vocab: "Faintly", explanation: "In a way that is not clear or strong.", vietnamese_meaning:"mở nhạt" },
    { vocab: "Persevere", explanation: "To persist in doing something despite difficulties.",vietnamese_meaning:"bảo toàn" },
    { vocab: "Eloquent", explanation: "Fluent or persuasive in speaking or writing." ,vietnamese_meaning:"có tài hùng biện"},
    { vocab: "abandon", explanation: "Give up something " ,vietnamese_meaning:"bỏ rơi"},
    { vocab: "antagonist", explanation: "one that contends with or opposes another " ,vietnamese_meaning:"chống đối"},

    { vocab: "baroque", explanation: "(used to describe European architecture, art and music of the 17th and early 18th centuries that has a grand and highly decorated style.", vietnamese_meaning:"cổ điển" },
    { vocab: "abuse", explanation: "(the use of something in a way that is wrong or harmful)",vietnamese_meaning:"lạm dụng" },
    { vocab: "congress", explanation: "a large formal meeting or series of meetings where representatives from different groups discuss ideas, make decisions, etc." ,vietnamese_meaning:"quốc hội"},
    { vocab: "contemplate", explanation: "(to think deeply about something for a long time)" ,vietnamese_meaning:"tận tâm"},
    { vocab: "contemporary", explanation: "belonging to the same time" ,vietnamese_meaning:"đồng thời"},

    
];*/

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
