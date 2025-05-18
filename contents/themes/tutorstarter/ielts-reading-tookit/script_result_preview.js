const currentDate = new Date();

const day = currentDate.getDate();
const month = currentDate.getMonth() + 1; // Adding 1 because getMonth() returns zero-based month index
const year = currentDate.getFullYear();

            // Display the date
const dateElement = document.getElementById('date');
dateElement.innerHTML = `${year}-${month}-${day}`;




let full_time = 0;
let currentPartIndex = 0;


function rememberQuestion(i) {
   

    var bookmarkQuestionElement = document.getElementById("bookmark-question-" + i);

    if (!bookmarkQuestionElement) {
        console.error("Element with id 'bookmark-question-" + i + "' not found.");
        return;
    }

    console.log("Book mark: bookmark-question-" + i);

    // Toggle the background color of the question checkbox container and change the image src
    if (bookmarkQuestionElement.src = '/wordpress/contents/themes/tutorstarter/system-test-toolkit/bookmark_empty.png') {
        bookmarkQuestionElement.style.backgroundColor = '';
        bookmarkQuestionElement.src = '/wordpress/contents/themes/tutorstarter/system-test-toolkit/bookmark_filled.png';
    
    } else {
        //bookmarkQuestionElement.style.backgroundColor = 'yellow';
        bookmarkQuestionElement.src = '/wordpress/contents/themes/tutorstarter/system-test-toolkit/bookmark_filled.png';
    }
}


function loadPart(partIndex) {
    const part = quizData.part[partIndex];
    console.log("Passed LoadPart");

    // Display the paragraph
    document.getElementById('paragraph-container').innerHTML = `<p>${part.paragraph}</p>`;

    // Display the question range
    const questionRange = getQuestionRange(partIndex);

    // Update the question range and add the timer
    const timerHtml = `<span id="timer" style="font-weight: bold></span>`;
    document.getElementById('question-range-of-part').innerHTML = `<b>Part ${partIndex + 1}</b> Read the text and answer questions ${questionRange}  `;

    // Start the timer
    // Display the groups and questions
    const questionsContainer = document.getElementById('questions-container');
    questionsContainer.innerHTML = ''; // Clear previous content

    // Calculate the starting question number for this part
    let currentQuestionNumber = getStartingQuestionNumber(partIndex);

    part.group_question.forEach((group, groupIndex) => {
        // Create a group container
        const groupContainer = document.createElement('div');
        groupContainer.classList.add('group-container');
        groupContainer.innerHTML = `
            <h4>Question ${group.group}:</h4>
            <p>${group.question_group_content}</p>
        `;

        group.questions.forEach((question, questionIndex) => {
            const questionElement = document.createElement('div');
            questionElement.classList.add('question');

            // Multi-select questions
            if (group.type_group_question === "multi-select") {
                const answerChoiceCount = parseInt(question.number_answer_choice) || 1;
                const questionRange = `${currentQuestionNumber}-${currentQuestionNumber + answerChoiceCount - 1}`;

                questionElement.innerHTML = `<p  name = "question-id-${currentQuestionNumber}"  id ="question-id-${questionRange}"><b>${questionRange}.</b> ${question.question}</p>`;

                question.answers.forEach((answer, answerIndex) => {
                    const answerOption = document.createElement('div');
                    //const inputId = `multiselect-${partIndex}-${groupIndex}-${questionIndex}-${answerIndex}`;
                    const inputId = `answer-input-${questionRange}-${answerIndex + 1}`;


                    answerOption.innerHTML = `
                        <label>
                            <input type="checkbox" id="${inputId}" name="question-${currentQuestionNumber}" value="${answer[0]}">
                            ${answer[0]}
                        </label>
                    `;

                    const checkbox = answerOption.querySelector('input');
                    checkbox.addEventListener('change', (event) => {
                        saveUserAnswer(partIndex, groupIndex, questionIndex, answerIndex, event.target.checked);
                        checkboxCurrent(currentQuestionNumber); // Log số câu vừa hoàn thành

                    });

                    // Restore the saved answer
                    if (isAnswerSelected(partIndex, groupIndex, questionIndex, answerIndex)) {
                        checkbox.checked = true;
                    }

                    questionElement.appendChild(answerOption);
                });

                currentQuestionNumber += answerChoiceCount;
            }

           // Completion questions
           if (group.type_group_question === "completion") {
            let questionContent = question.question;
            const completionInputIds = []; // Lưu trữ danh sách các ID để kiểm tra sau
        
            // Thay thế tất cả placeholder <input> bằng thẻ input động
            question.box_answers.forEach((boxAnswer, boxIndex) => {
                const completionNumber = currentQuestionNumber + boxIndex;
                const inputElementHtml = `
                <div style="display: inline-flex; align-items: center;">
                    <input name = "question-id-${completionNumber}"  type="text" id="answer-input-${completionNumber}" class="answer-input" name="question-${completionNumber}"  placeholder="Question ${completionNumber}"/>
                    <image style = 'display:none' id="bookmark-question-${completionNumber}" 
                            src="/wordpress/contents/themes/tutorstarter/system-test-toolkit/bookmark_empty.png" 
                            class="bookmark-btn" 
                            style="margin-left: 10px; cursor: pointer;" 
                            onclick="rememberQuestion(${completionNumber})">
                        </image>
                    </div>
                    `;
                questionContent = questionContent.replace('<input>', inputElementHtml);
                completionInputIds.push(`answer-input-${completionNumber}`);
                
            });
            let currentIndexQuestion = currentQuestionNumber;
        
            // Gắn nội dung đã thay thế vào DOM
            questionElement.innerHTML = `<p>${questionContent}</p>`;
        
            // Chờ DOM cập nhật hoàn tất rồi mới gán sự kiện
            setTimeout(() => {
                completionInputIds.forEach((inputId, boxIndex) => {
                    const inputElement = document.getElementById(inputId);
        
                    if (!inputElement) {
                        console.error(`Input element with ID "${inputId}" not found.`);
                        return;
                    }
        
                    // Thêm sự kiện khi người dùng nhập
                    inputElement.addEventListener('input', (event) => {
                        checkboxCurrent(currentIndexQuestion + boxIndex);
                        
                        saveCompletionAnswer(partIndex, groupIndex, questionIndex, boxIndex, event.target.value);

                    });
        
                    // Khôi phục dữ liệu đã lưu
                    const savedAnswer = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex]?.[boxIndex];
                    if (savedAnswer) {
                        inputElement.value = savedAnswer;
                    }
                });
            }, 0); // Delay để DOM cập nhật trước khi gán sự kiện
        
            // Tăng số thứ tự câu hỏi
            currentQuestionNumber += question.box_answers.length;
        }
        
        
        
        

            // Multiple-choice questions (single-select)
            if (group.type_group_question === "multiple-choice") {
                questionElement.innerHTML = `
                <div style="display: inline-flex; align-items: center;">
                    <p name="question-id-${currentQuestionNumber}" id="question-id-${currentQuestionNumber}" style="margin: 0;">
                        <b>${currentQuestionNumber}.</b> ${question.question}
                    </p>
                    <image style='display:none' id="bookmark-question-${currentQuestionNumber}" 
                           src="/wordpress/contents/themes/tutorstarter/system-test-toolkit/bookmark_empty.png" 
                           class="bookmark-btn" 
                           style="margin-left: 10px; cursor: pointer;" 
                           onclick="rememberQuestion(${currentQuestionNumber})">
                    </image>
                </div>`;

                
                let currentIndexQuestion = currentQuestionNumber;
                question.answers.forEach((answer, answerIndex) => {
                    const answerElement = document.createElement('label');
                    //const inputId = `${partIndex}-${groupIndex}-${questionIndex}-${answerIndex}`;
                    const inputId = `${currentQuestionNumber}-${answerIndex + 1}`;
                   /* answerElement.innerHTML = `
                        <input type="radio" name="multiplechoice-${partIndex}-${groupIndex}-${questionIndex}" value="${answer[0]}" id="${inputId}">
                        ${answer[0]}
                    `;*/
                     answerElement.innerHTML = `
                        <input type="radio" name="question-${currentQuestionNumber}"  value="${answer[0]}" id="answer-input-${inputId}">
                        ${answer[0]}
                    `;

                    answerElement.querySelector('input').addEventListener('change', (event) => {
                        checkboxCurrent(currentIndexQuestion);
                        saveUserAnswer(partIndex, groupIndex, questionIndex, answerIndex, event.target.value);
                    });
            
                    // Restore the saved answer if it exists
                    if (isAnswerSelected(partIndex, groupIndex, questionIndex, answerIndex)) {
                        answerElement.querySelector('input').checked = true;
                        
                    }
            
                    questionElement.appendChild(answerElement);
                });

                currentQuestionNumber++;
            }

            groupContainer.appendChild(questionElement);
        });

        questionsContainer.appendChild(groupContainer);
    });

    document.getElementById("test-prepare").style.display = "none";
    document.getElementById("content1").style.display = "block";
}

function checkboxCurrent(currentQuestionNumber) {
    // Tìm tất cả các phần tử span của câu hỏi
    const allQuestionCheckboxes = document.querySelectorAll('.question-checkbox');
    
    // Xóa class 'highlight-current-question' khỏi tất cả các câu hỏi
    allQuestionCheckboxes.forEach(span => {
        span.classList.remove('highlight-current-question');
    });
    //document.getElementById(`bookmark-question-${currentQuestionNumber}`).style.display = "block";

    // Tìm câu hỏi hiện tại và thêm class 'highlight-current-question'
    const currentCheckbox = document.getElementById(`question-checkbox-${currentQuestionNumber}`);
    if (currentCheckbox) {
        currentCheckbox.classList.add('highlight-current-question');
    }

    console.log(`Current Question :${currentQuestionNumber}`);
}



// Function to calculate the starting question number for a part
function getStartingQuestionNumber(partIndex) {
    let startQuestion = 1; // Start with question 1
    for (let i = 0; i < partIndex; i++) {
        startQuestion += parseInt(quizData.part[i].number_question_of_this_part);
    }
    return startQuestion;
}

// Function to calculate question range for a part
function getQuestionRange(partIndex) {
    const startQuestion = getStartingQuestionNumber(partIndex);
    const endQuestion = startQuestion + parseInt(quizData.part[partIndex].number_question_of_this_part) - 1;
    return `${startQuestion} - ${endQuestion}`;
}


// Navigation buttons
document.getElementById('prev-btn').addEventListener('click', () => {
    if (currentPartIndex > 0) {
        currentPartIndex--;
        loadPart(currentPartIndex);
    }
});

document.getElementById('next-btn').addEventListener('click', () => {
    if (currentPartIndex < quizData.part.length - 1) {
        currentPartIndex++;
        loadPart(currentPartIndex);
    }
});


// Load the part buttons dynamically
const partNavigation = document.getElementById('part-navigation');

// Hàm tính số câu hỏi bắt đầu cho mỗi phần
function getStartingQuestionNumber(partIndex) {
    let startQuestion = 1; // Mặc định bắt đầu từ câu 1
    for (let i = 0; i < partIndex; i++) {
        const part = quizData.part[i];
        part.group_question.forEach(group => {
            // Tính số câu hỏi cho loại "multi-select"
            if (group.type_group_question === "multi-select") {
                group.questions.forEach(question => {
                    const answerChoiceCount = parseInt(question.number_answer_choice) || 1;
                    startQuestion += answerChoiceCount; // Cộng số câu hỏi
                });
            }

            // Tính số câu hỏi cho loại "completion"
            if (group.type_group_question === "completion") {
                group.questions.forEach(question => {
                    startQuestion += question.box_answers.length; // Cộng số box_answers
                });
            }

            // Tính số câu hỏi cho loại "multiple-choice"
            if (group.type_group_question === "multiple-choice") {
                startQuestion += group.questions.length; // Cộng số câu hỏi
            }
        });
    }
    return startQuestion;
}

// Hàm tính tổng số câu hỏi trong phần hiện tại
function getTotalQuestions(partIndex) {
    const part = quizData.part[partIndex];
    let totalQuestions = 0;

    part.group_question.forEach(group => {
        // Tính số câu hỏi cho loại "multi-select"
        if (group.type_group_question === "multi-select") {
            group.questions.forEach(question => {
                const answerChoiceCount = parseInt(question.number_answer_choice) || 1;
                totalQuestions += answerChoiceCount; // Cộng số câu hỏi cho multi-select
            });
        }

        // Tính số câu hỏi cho loại "completion"
        if (group.type_group_question === "completion") {
            group.questions.forEach(question => {
                totalQuestions += question.box_answers.length; // Số câu hỏi completion = số box_answer
            });
        }

        // Tính số câu hỏi cho loại "multiple-choice"
        if (group.type_group_question === "multiple-choice") {
            totalQuestions += group.questions.length; // Cộng số câu hỏi cho multiple-choice
        }
    });

    return totalQuestions;
}
// Load các button phần với số câu hỏi
quizData.part.forEach((part, index) => {
    const button = document.createElement('button');
    
    // Lấy số câu bắt đầu cho phần này
    const startingQuestionNumber = getStartingQuestionNumber(index);
    
    // Tính số câu hỏi của phần này
    const totalQuestions = getTotalQuestions(index);

    // Tạo các span cho mỗi câu hỏi trong phần
    const questionSpans = [];
    for (let i = 0; i < totalQuestions; i++) {
        const questionNumber = startingQuestionNumber + i;
        const span = document.createElement('span');
        span.id = `question-checkbox-${questionNumber}`;  // Thay id thành name
        span.innerText = questionNumber;
        span.classList.add('question-checkbox');
        
        // Thêm sự kiện click vào span
        span.addEventListener('click', () => {
            // Chuyển đến câu hỏi tương ứng với name là "question-id-{questionNumber}"
            const questionElements = document.getElementsByName(`question-id-${questionNumber}`);
            checkboxCurrent(questionNumber);
            console.log(`scroll smooth ${questionNumber}` )
            // Nếu phần tử câu hỏi tồn tại, cuộn đến phần tử đó
            if (questionElements.length > 0) {
                questionElements[0].scrollIntoView({
                    behavior: 'smooth', // Cuộn mượt mà
                    block: 'center'     // Câu hỏi sẽ được canh giữa màn hình
                });
            }
        });
        
        questionSpans.push(span);
    }

    // Cập nhật nội dung nút bấm để hiển thị các span
    button.innerHTML = `<b> Part ${part.part_number}  </b>`;
    
    // Thêm các span vào nút
    questionSpans.forEach(span => {
        button.appendChild(span);
    });

    button.id = 'part-navigation-button';
    
    button.addEventListener('click', () => {
        currentPartIndex = index;
        loadPart(currentPartIndex);
    });
    
    partNavigation.appendChild(button);
});





const submitButton = document.createElement('button');
submitButton.innerHTML = "<i class='fa-solid fa-check'></i>";
submitButton.id = 'submit-btn';
let isSubmitted = false; // Kiểm tra xem đã submit hay chưa

submitButton.addEventListener('click', () => {
    if (!isSubmitted) {
        isSubmitted = true;
        for (let i = 0; i < quizData.part.length; i++) {
            logUserAnswers(i);
        }
        fulltestResult();
        ResultInput();
        clearInterval(timerInterval); // Ngừng bộ đếm thời gian
    }
});

partNavigation.appendChild(submitButton); // This will place the Submit button at the end

let userAnswers = {}; // Object to store user answers

// Save the user's answer selection for multiple-choice and multi-select
function saveUserAnswer(partIndex, groupIndex, questionIndex, answerIndex, value) {
    // Ensure we save the most recent answer chosen by the user
    if (!userAnswers[partIndex]) {
        userAnswers[partIndex] = [];
    }
    if (!userAnswers[partIndex][groupIndex]) {
        userAnswers[partIndex][groupIndex] = [];
    }
    if (!userAnswers[partIndex][groupIndex][questionIndex]) {
        userAnswers[partIndex][groupIndex][questionIndex] = [];
    }

    // Save the answer, ensuring only the selected one is marked as true
    userAnswers[partIndex][groupIndex][questionIndex] = answerIndex;
    console.log('Answer saved:', userAnswers);
   

    // For multi-select, toggle answer in array; for single-select, save the answer directly
    if (Array.isArray(userAnswers[partIndex][groupIndex][questionIndex])) {
        const index = userAnswers[partIndex][groupIndex][questionIndex].indexOf(answerIndex);
        if (value) {
            if (index === -1) {
                userAnswers[partIndex][groupIndex][questionIndex].push(answerIndex);
            }
        } else {
            if (index !== -1) {
                userAnswers[partIndex][groupIndex][questionIndex].splice(index, 1);
            }
        }
    } else {
        userAnswers[partIndex][groupIndex][questionIndex] = answerIndex;
    }
}


// Save the user's answer for completion questions
function saveCompletionAnswer(partIndex, groupIndex, questionIndex, boxIndex, value) {
    if (!userAnswers[partIndex]) {
        userAnswers[partIndex] = {};
    }
    if (!userAnswers[partIndex][groupIndex]) {
        userAnswers[partIndex][groupIndex] = {};
    }
    if (!userAnswers[partIndex][groupIndex][questionIndex]) {
        userAnswers[partIndex][groupIndex][questionIndex] = {};
    }

    userAnswers[partIndex][groupIndex][questionIndex][boxIndex] = value; // Store the answer value
}

// Check if an answer is already selected
function isAnswerSelected(partIndex, groupIndex, questionIndex, answerIndex) {
    const selected = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex];
    return userAnswers[partIndex]?.[groupIndex]?.[questionIndex] === answerIndex;

}
let timerInterval; // Declare timer interval globally

let startTime; // Biến để lưu thời gian bắt đầu quiz

function startTimer(duration) {
    clearInterval(timerInterval); // Clear any existing timer
    startTime = Date.now(); // Lưu lại thời gian bắt đầu
    let timer = duration, minutes, seconds;
    const timerDisplay = document.getElementById('timer');

    timerInterval = setInterval(function () {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        seconds = seconds < 10 ? "0" + seconds : seconds;
        timerDisplay.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            clearInterval(timerInterval);
            if (!isSubmitted) {
                isSubmitted = true;
                for (let i = 0; i < quizData.part.length; i++) {
                    logUserAnswers(i); // Log answers for all parts
                }
                fulltestResult();
                ResultInput();
            }
        }
    }, 1000);
}


// Khởi tạo các biến toàn cục để theo dõi tổng số câu đúng, sai và tổng số câu hỏi cho cả bài kiểm tra
let totalCorrectAnswers = 0;
let totalIncorrectAnswers = 0;
let totalQuestionsCount = 0;
let totalSkipAnswers = 0;

function logUserAnswers(partIndex) {
    let userAnswerdiv = document.getElementById('useranswerdiv');
    const endTime = Date.now(); // Lấy thời gian hiện tại
    const timeSpent = Math.floor((endTime - startTime) / 1000); // Tính số giây đã trôi qua
    const minutesSpent = Math.floor(timeSpent / 60);
    const secondsSpent = timeSpent % 60;
    console.log(`Time spent: ${minutesSpent} minutes and ${secondsSpent} seconds`);
    document.getElementById("time-result").innerHTML = ` ${minutesSpent}:${secondsSpent} `;

    const part = quizData.part[partIndex];
    
    // Initialize variables for correct, incorrect, and skipped answers
    let correctCount = 0;
    let incorrectCount = 0;
    let skippedCount = 0; // New variable for skipped answers
    let totalQuestions = 0; // Total question count

    // Initialize the current question number for this part
    let currentQuestionNumber = getStartingQuestionNumber(partIndex);

    part.group_question.forEach((group, groupIndex) => {
        group.questions.forEach((question, questionIndex) => {
            let questionNumber;
            let userAnswer;
            let correctAnswer; // Variable for correct answer

            // Handle multi-select questions
            if (group.type_group_question === "multi-select") {
                const answerChoiceCount = parseInt(question.number_answer_choice) || 1;
                questionNumber = `${currentQuestionNumber}-${currentQuestionNumber + answerChoiceCount - 1}`;
                const selectedAnswers = [];
                const correctAnswers = [];

                question.answers.forEach((answer, answerIndex) => {
                    if (isAnswerSelected(partIndex, groupIndex, questionIndex, answerIndex)) {
                        selectedAnswers.push(answer[0]);
                    }
                    if (answer[1] === true) {
                        correctAnswers.push(answer[0]);
                    }
                });

                userAnswer = selectedAnswers.length > 0 ? selectedAnswers.join(', ') : "";
                correctAnswer = correctAnswers.length > 0 ? correctAnswers.join(', ') : "Not available";

                if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                    correctCount += answerChoiceCount;
                } else if (userAnswer === "") {
                    skippedCount += answerChoiceCount; // Increment skipped count
                } else {
                    incorrectCount += answerChoiceCount;
                }

                userAnswerdiv.innerHTML += `Question: ${questionNumber}, Part: ${partIndex+1}, User Answer: ${userAnswer} <br>`;
                currentQuestionNumber += answerChoiceCount;
                totalQuestions += answerChoiceCount;
            }

            // Handle completion questions
            else if (group.type_group_question === "completion") {
                question.box_answers.forEach((boxAnswer, boxIndex) => {
                    const completionNumber = currentQuestionNumber + boxIndex;
                    const savedAnswer = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex]?.[boxIndex];

                    userAnswer = savedAnswer ? savedAnswer : "";
                    correctAnswer = boxAnswer.answer ? boxAnswer.answer : "Not available";

                    if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                        correctCount++;
                    } else if (userAnswer === "") {
                        skippedCount++; // Increment skipped count
                    } else {
                        incorrectCount++;
                    }

                    console.log(`Question ${completionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswer}`);
                    userAnswerdiv.innerHTML += `Question: ${completionNumber}, Part: ${partIndex+1}, User Answer: ${userAnswer} <br>`;
                });

                currentQuestionNumber += question.box_answers.length;
                totalQuestions += question.box_answers.length;
            }

            // Handle multiple-choice questions
            else if (group.type_group_question === "multiple-choice") {
                questionNumber = `${currentQuestionNumber}`;
                const savedAnswerIndex = question.answers.findIndex((answer, answerIndex) => isAnswerSelected(partIndex, groupIndex, questionIndex, answerIndex));
                userAnswer = savedAnswerIndex !== -1 ? question.answers[savedAnswerIndex][0] : "";
                
                const correctAnswerIndex = question.answers.findIndex(answer => answer[1] === true);
                correctAnswer = correctAnswerIndex !== -1 ? question.answers[correctAnswerIndex][0] : "Not available";

                if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                    correctCount++;
                } else if (userAnswer === "") {
                    skippedCount++; // Increment skipped count
                } else {
                    incorrectCount++;
                }

                console.log(`Question ${questionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswer}`);
                userAnswerdiv.innerHTML += `Question: ${questionNumber}, Part: ${partIndex+1}, User Answer: ${userAnswer} <br>`;
                currentQuestionNumber++;
                totalQuestions++;
            }
        });
    });

    // Update total counts for the entire test
    totalCorrectAnswers += correctCount;
    totalIncorrectAnswers += incorrectCount;
    totalQuestionsCount += totalQuestions;
    totalSkipAnswers += skippedCount;

    
    // Log the results
    console.log(`Total correct answers: ${correctCount}`);
    console.log(`Total incorrect answers: ${incorrectCount}`);
    console.log(`Total skipped answers: ${skippedCount}`); // Log skipped answers
    console.log(`Total questions: ${totalQuestions}`);

    console.log(`Overall Total correct answers: ${totalCorrectAnswers}`);
    console.log(`Overall Total incorrect answers: ${totalIncorrectAnswers}`);
    console.log(`Overall Total skipped answers: ${totalSkipAnswers}`);
    console.log(`Overall Total questions: ${totalQuestionsCount}`);
}


let ieltsBandScore;
function fulltestResult(){
    // In kết quả cuối cùng vào HTML nếu cần
    const userAnswerdiv = document.getElementById('useranswerdiv');


    if(totalCorrectAnswers  == 3){
        ieltsBandScore = 2;
    }
    else if(totalCorrectAnswers  >= 4 && totalCorrectAnswers  <= 5){
        ieltsBandScore = 2.5;
    }
    else if(totalCorrectAnswers  >= 6 && totalCorrectAnswers  <= 7){
        ieltsBandScore = 3.0;
    }
    else if(totalCorrectAnswers  >= 8 && totalCorrectAnswers  <= 9){
        ieltsBandScore = 3.5;
    }
    else if(totalCorrectAnswers  >= 10 && totalCorrectAnswers  <= 12){
        ieltsBandScore = 4.0;
    }
    else if(totalCorrectAnswers  >= 13 && totalCorrectAnswers  <= 14){
        ieltsBandScore = 4.5;
    }
    else if(totalCorrectAnswers  >= 15 && totalCorrectAnswers  <= 18){
        ieltsBandScore = 5.0;
    }
    else if(totalCorrectAnswers  >= 19 && totalCorrectAnswers  <= 22){
        ieltsBandScore = 5.5;
    }
    else if(totalCorrectAnswers  >= 23 && totalCorrectAnswers  <= 26){
        ieltsBandScore = 6.0;
    }
    else if(totalCorrectAnswers  >= 29 && totalCorrectAnswers  <= 29){
        ieltsBandScore = 6.5;
    }
    else if(totalCorrectAnswers  >= 30 && totalCorrectAnswers  <= 32){
        ieltsBandScore = 7.0;
    }
    else if(totalCorrectAnswers  >= 33 && totalCorrectAnswers  <= 34){
        ieltsBandScore = 7.5;
    }
    else if(totalCorrectAnswers  >= 35 && totalCorrectAnswers  <= 36){
        ieltsBandScore = 8.0;
    }
    else if(totalCorrectAnswers  >= 37 && totalCorrectAnswers  <= 38){
        ieltsBandScore = 8.5;
    }
    else if(totalCorrectAnswers  >= 39 && totalCorrectAnswers  <= 40){
        ieltsBandScore = 9.0;
    }
    else{
        ieltsBandScore = 0;
    }

}



// Button functions for testing
function blue_highlight(spanId) {
    document.getElementById(spanId).style.backgroundColor = 'blue';
}

function green_highlight(spanId) {
    document.getElementById(spanId).style.backgroundColor = 'green';
}
function yellow_highlight(spanId) {
    document.getElementById(spanId).style.backgroundColor = 'yellow';
}
function purple_highlight(spanId) {
    document.getElementById(spanId).style.backgroundColor = 'purple';
}


function ResultInput() {
    // Copy the content to the form fields
   // var contentToCopy1 = document.getElementById("final-result").textContent;
    var contentToCopy2 = document.getElementById("date").textContent;
    var contentToCopy4 = document.getElementById("title").textContent;
    var contentToCopy6 = document.getElementById("id_test").textContent;
    var contentToCopy8 = document.getElementById("useranswerdiv").textContent;

    var contentToCopy3 = document.getElementById("time-result").textContent;
    /*var contentToCopy5 = document.getElementById("id_category").textContent;
    var contentToCopy7 = document.getElementById("correctanswerdiv").textContent;
*/

    document.getElementById("correct_percentage").value = `${totalCorrectAnswers}/${totalQuestionsCount}`;
    document.getElementById("testsavenumber").value = resultId;


    document.getElementById("total_question_number").value = `${totalQuestionsCount}`;
    document.getElementById("correct_number").value = `${totalCorrectAnswers}`;
    document.getElementById("incorrect_number").value = `${totalIncorrectAnswers}`;
    document.getElementById("skip_number").value = `${totalSkipAnswers}`;


    document.getElementById("overallband").value = `${ieltsBandScore}`;

    document.getElementById("dateform").value = contentToCopy2;
    document.getElementById("testname").value = contentToCopy4;
    document.getElementById("idtest").value = contentToCopy6;
    document.getElementById("useranswer").value = contentToCopy8;
    document.getElementById("timedotest").value = contentToCopy3;
    
  /*  
    document.getElementById("idcategory").value = contentToCopy5;
    document.getElementById("idtest").value = contentToCopy6;
    document.getElementById("correctanswer").value = contentToCopy7;
     */

    
    // Add a delay before submitting the form
setTimeout(function() {
// Automatically submit the form
jQuery('#saveReadingResult').submit();
}, 100000); // 5000 milliseconds = 5 seconds
}
          
// Event listener for the submit button

function startTest()
{
    startTimer(full_time * 60); // 1 hour
    loadPart(currentPartIndex, full_time);
}



function main(){
    console.log("Passed Main");
    
    for(let i = 0; i < quizData.part.length; i ++){
        console.log(quizData.part[1].duration);
        full_time += quizData.part[i].duration;
    }
    console.log("Full test time:", full_time)


    setTimeout(function(){
        console.log("Show Test!");
        document.getElementById("start_test").style.display="block";
        
        document.getElementById("welcome").style.display="block";

    }, 5000);
    
}

