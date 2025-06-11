let currentPartIndex = 0;

function loadPart(partIndex) {
    const part = quizData.part[partIndex];

    
    // Display the question range
    const questionRange = getQuestionRange(partIndex);

    // Update the question range and add the timer
    const timerHtml = `<span id="timer" style="font-weight: bold></span>`;

    // Start the timer
    // Display the groups and questions
    const questionsContainer = document.getElementById('questions-container');
    questionsContainer.innerHTML = ''; // Clear previous content

    // Calculate the starting question number for this part
    let currentQuestionNumber = getStartingQuestionNumber(partIndex);

    part.group_question.forEach((group, groupIndex) => {
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
                const correctAnswers = question.answers
                    .filter(answer => answer[1] === true)
                    .map(answer => answer[0]);
    
                questionElement.innerHTML = `
                    <p id="question-id-${questionRange}"><b>${questionRange}.</b> ${question.question}</p>
                `;
    
                question.answers.forEach((answer, answerIndex) => {
                    const answerOption = document.createElement('div');
                    const inputId = `answer-input-${questionRange}-${answerIndex + 1}`;
    
                    answerOption.innerHTML = `
                        <label>
                            <input type="checkbox" id="${inputId}" name="question-${currentQuestionNumber}" value="${answer[0]}">
                            ${answer[0]}
                        </label>
                        <br> 
                        <p><b>Correct Answer:</b> ${correctAnswers.length > 0 ? correctAnswers.join(', ') : "Not available"}</p>

                    `;
    
                    const checkbox = answerOption.querySelector('input');
                    checkbox.addEventListener('change', (event) => {
                        saveUserAnswer(partIndex, groupIndex, questionIndex, answerIndex, event.target.checked);
                    });
    
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
                const correctAnswers = question.box_answers.map(box => box.answer || "Not available");
                const completionInputIds = [];
    
                question.box_answers.forEach((boxAnswer, boxIndex) => {
                    const completionNumber = currentQuestionNumber + boxIndex;
                    const inputElementHtml = `<input type="text" id="answer-input-${completionNumber}" class="answer-input" name="question-${completionNumber}" />`;
                    questionContent = questionContent.replace('<input>', inputElementHtml);
                    completionInputIds.push(`answer-input-${completionNumber}`);
                });
    
                questionElement.innerHTML = `
                    <p>${questionContent}</p>
                    <p><b class ="correct-ans">Correct Answer:</b> ${correctAnswers.join(', ')}</p>
                `;
    
    
                currentQuestionNumber += question.box_answers.length;
            }
    
            // Multiple-choice questions
            if (group.type_group_question === "multiple-choice") {
                const correctAnswer = question.answers.find(answer => answer[1] === true)?.[0] || "Not available";
    
                questionElement.innerHTML += `
                    <p id="question-id-${currentQuestionNumber}"><b>${currentQuestionNumber}.</b> ${question.question}</p>
                `;
    
                question.answers.forEach((answer, answerIndex) => {
                    const answerElement = document.createElement('label');
                    const inputId = `${currentQuestionNumber}-${answerIndex + 1}`;
    
                    answerElement.innerHTML = `
                        <input type="radio" name="question-${currentQuestionNumber}" value="${answer[0]}" id="answer-input-${inputId}">
                        ${answer[0]}
                    `;
    
                    answerElement.querySelector('input').addEventListener('change', (event) => {
                        saveUserAnswer(partIndex, groupIndex, questionIndex, answerIndex, event.target.value);
                    });
    
                    if (isAnswerSelected(partIndex, groupIndex, questionIndex, answerIndex)) {
                        answerElement.querySelector('input').checked = true;
                    }
    
                    questionElement.appendChild(answerElement);
                });
                questionElement.innerHTML += `
                <p><b class ="correct-ans">Correct Answer:</b> ${correctAnswer}</p>
            `;
                currentQuestionNumber++;
            }
    
            groupContainer.appendChild(questionElement);
        });
    
        questionsContainer.appendChild(groupContainer);
    });
    
    document.getElementById("content").style.display = "block";
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



// Load the part buttons dynamically
const partNavigation = document.getElementById('part-navigation');
quizData.part.forEach((part, index) => {
    const button = document.createElement('button');
    button.innerText = `Part ${part.part_number}`;
    button.id = 'part-navigation-button';
    button.addEventListener('click', () => {
        currentPartIndex = index;
        loadPart(currentPartIndex);
    });
    partNavigation.appendChild(button);
});


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



function logUserAnswers(partIndex) {
 
    const part = quizData.part[partIndex];
    
    // Initialize variables for correct and incorrect answers
    let correctCount = 0;
    let incorrectCount = 0;
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
                    // Check if this answer is selected by the user
                    if (isAnswerSelected(partIndex, groupIndex, questionIndex, answerIndex)) {
                        selectedAnswers.push(answer[0]);
                    }
                    // Check if this answer is marked as correct
                    if (answer[1] === true) {
                        correctAnswers.push(answer[0]);
                    }
                });

                userAnswer = selectedAnswers.length > 0 ? selectedAnswers.join(', ') : "Not answered";
                correctAnswer = correctAnswers.length > 0 ? correctAnswers.join(', ') : "Not available";

                // Compare userAnswer and correctAnswer, case-insensitive
                if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                    correctCount += answerChoiceCount; // Count as multiple questions
                    correct_answer_array.push(currentQuestionNumber); // Add to correct answers

                } else {
                    incorrectCount += answerChoiceCount;
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers

                }

                console.log(`Question ${questionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswer}`);
                userAnswerdiv.innerHTML += `Question ${questionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswer}<br>`;

                currentQuestionNumber += answerChoiceCount; // Increment the question number
                totalQuestions += answerChoiceCount; // Count total number of questions
            }

            // Handle completion questions
            else if (group.type_group_question === "completion") {
                question.box_answers.forEach((boxAnswer, boxIndex) => {
                    const completionNumber = currentQuestionNumber + boxIndex; // This matches how loadPart handles completion inputs
                    const savedAnswer = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex]?.[boxIndex];

                    userAnswer = savedAnswer ? savedAnswer : "Not answered";
                    correctAnswer = boxAnswer.answer ? boxAnswer.answer : "Not available"; // Assume correct answer is in `boxAnswer.answer`


                    if (Array.isArray(correctAnswer)) {
                        const correctAnswerString = correctAnswer.join(" or ");  // Chuyển các đáp án thành một chuỗi với "or" giữa các đáp án
                        if (correctAnswer.some(answer => userAnswer.toLowerCase() === answer.toLowerCase())) {
                            correctCount++;
                            correct_answer_array.push(completionNumber); // Add to correct answers

                        } else if (userAnswer === "") {
                            skippedCount++; // Increment skipped count
                            incorrect_skip_answer_array.push(completionNumber); // Add to skipped answers

                        } else {
                            incorrectCount++;
                            incorrect_skip_answer_array.push(completionNumber); // Add to skipped answers

                        }
                        console.log(`Question ${completionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswerString}`);
                    } else {
                        // Trường hợp chỉ có 1 đáp án đúng
                        if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                            correctCount++;
                            correct_answer_array.push(completionNumber); // Add to correct answers

                        } else if (userAnswer === "") {
                            skippedCount++; // Increment skipped count
                            incorrect_skip_answer_array.push(completionNumber); // Add to skipped answers

                        } else {
                            incorrectCount++;
                            incorrect_skip_answer_array.push(completionNumber); // Add to skipped answers

                        }
                        console.log(`Question ${completionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswer}`);
                    }
            


                    

                    userAnswerdiv.innerHTML += `Question ${completionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswer}<br>`;
                });

                currentQuestionNumber += question.box_answers.length; // Increment by the number of boxes
                totalQuestions += question.box_answers.length; // Count total number of completion questions
            }

            // Handle multiple-choice questions
            else if (group.type_group_question === "multiple-choice") {
                questionNumber = `${currentQuestionNumber}`;
                const savedAnswerIndex = question.answers.findIndex((answer, answerIndex) => isAnswerSelected(partIndex, groupIndex, questionIndex, answerIndex));
                userAnswer = savedAnswerIndex !== -1 ? question.answers[savedAnswerIndex][0] : "Not answered";
                
                const correctAnswerIndex = question.answers.findIndex(answer => answer[1] === true); // Find the correct answer
                correctAnswer = correctAnswerIndex !== -1 ? question.answers[correctAnswerIndex][0] : "Not available";

                // Compare userAnswer and correctAnswer, case-insensitive
                if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                    correctCount++;
                    correct_answer_array.push(currentQuestionNumber); // Add to correct answers

                } else {
                    incorrectCount++;
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers

                }

                console.log(`Question ${questionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswer}`);
                userAnswerdiv.innerHTML += `Question ${questionNumber}: User Answer: ${userAnswer}, Correct Answer: ${correctAnswer}<br>`;

                currentQuestionNumber++; // Increment the question number for the next question
                totalQuestions++; // Count as a single question
            }
        });
    });
     // Cộng dồn kết quả cho cả bài kiểm tra
     totalCorrectAnswers += correctCount;
     totalIncorrectAnswers += incorrectCount;
     totalQuestionsCount += totalQuestions;

    // Log the number of correct and incorrect answers
    console.log(`Total correct answers: ${correctCount}`);
    console.log(`Total incorrect answers: ${incorrectCount}`);
    console.log(`Total questions: ${totalQuestions}`);

    userAnswerdiv.innerHTML += `<br>Total correct answers: ${correctCount}<br>`;
    userAnswerdiv.innerHTML += `Total incorrect answers: ${incorrectCount}<br>`;
    userAnswerdiv.innerHTML += `Total questions: ${totalQuestions}<br>`;

    console.log(`Overall Total correct answers: ${totalCorrectAnswers}`);
    console.log(`Overall Total incorrect answers: ${totalIncorrectAnswers}`);
    console.log(`Overall Total questions: ${totalQuestionsCount}`);

    
}

let ieltsBandScore;
function fulltestResult(){
    // In kết quả cuối cùng vào HTML nếu cần
    const userAnswerdiv = document.getElementById('useranswerdiv');
    userAnswerdiv.innerHTML += `<br><strong>Overall Total correct answers: ${totalCorrectAnswers}</strong><br>`;
    userAnswerdiv.innerHTML += `<strong>Overall Total incorrect answers: ${totalIncorrectAnswers}</strong><br>`;
    userAnswerdiv.innerHTML += `<strong>Overall Total questions: ${totalQuestionsCount}</strong><br>`;

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
    userAnswerdiv.innerHTML += `<h3>Overall band: ${ieltsBandScore}</h3>`

}
// Khai báo biến lưu trữ đáp án đúng
let correctAnswersData = {};


let totalNewCorrectAns = 0;
let totalNewIncorrectAns = 0;
let totalNewSkipAns = 0;
let newOverallBand;
let incorrect_skip_answer_array = [];
let correct_answer_array = [];

function consoleAns(partIndex) { 
  
    const part = quizData.part[partIndex];
    let currentQuestionNumber = getStartingQuestionNumber(partIndex);

    let result = {
        correct: [],
        incorrect: [],
        skipped: []
    };


    let correctCount = 0; // Initialize correct answer count
    let incorrectCount = 0; // Initialize incorrect answer count
    let skipCount = 0; // Initialize skipped answer count
    let userAnswer;

    part.group_question.forEach((group, groupIndex) => {
        group.questions.forEach((question, questionIndex) => {
            let questionNumber;
            let correctAnswer; // Variable to store the correct answer
            

            // Process multi-select questions
            if (group.type_group_question === "multi-select") {
                userAnswer = groupedQuestions[currentQuestionNumber - 1]?.userAnswer; // Get user's answer

                const answerChoiceCount = parseInt(question.number_answer_choice) || 1;
                questionNumber = `${currentQuestionNumber}-${currentQuestionNumber + answerChoiceCount - 1}`;
                const correctAnswers = [];

                question.answers.forEach((answer) => {
                    if (answer[1] === true) {
                        correctAnswers.push(answer[0]);
                    }
                });
                let checkCorrectAnsIcon = document.getElementById(`check-correct-${questionNumber}`);
                

                correctAnswer = correctAnswers.length > 0 ? correctAnswers.join(', ') : "Not available";
                console.log(`Question: ${questionNumber}, Part: ${partIndex + 1}, Correct Answer: ${correctAnswer}, User Ans: ${userAnswer}`);

                // Check if user's answer is correct
                if (userAnswer == correctAnswer) {
                    correctCount++;
                    checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-check" style="color: #01f905;"></i>';
                    correct_answer_array.push(currentQuestionNumber); // Add to correct answers
                    result.correct.push(currentQuestionNumber);


                } else if (userAnswer == "") {
                    skipCount++;
                    checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-xmark" style="color: #de1b1b;"></i>';
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers
                    result.skipped.push(currentQuestionNumber);


                } else {
                    incorrectCount++;
                    checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-xmark" style="color: #de1b1b;"></i>';
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers
                    result.incorrect.push(currentQuestionNumber);


                }

                correctAnswersData[questionNumber] = {
                    part: partIndex + 1,
                    correctAnswer: correctAnswer
                };

                currentQuestionNumber += answerChoiceCount;
            }

            // Process completion questions
            else if (group.type_group_question === "completion") {
                question.box_answers.forEach((boxAnswer, boxIndex) => {
                    const completionNumber = currentQuestionNumber + boxIndex;
                    userAnswer = groupedQuestions[completionNumber - 1]?.userAnswer; // Get user's answer
            
                    let correctAnswer = boxAnswer.answer ? boxAnswer.answer : "Not available";
            
                    // Xử lý trường hợp đáp án đúng là một mảng (có nhiều đáp án)
                    if (Array.isArray(correctAnswer)) {
                        const correctAnswerString = correctAnswer.join(" or ");  // Nối các đáp án đúng với "or"
                        if (correctAnswer.some(answer => userAnswer.toLowerCase() === answer.toLowerCase())) {
                            correctCount++;
                            console.log(`Question: ${completionNumber}, Part: ${partIndex + 1}, Correct Answer: ${correctAnswerString}, User Ans: ${userAnswer}`);
                            let checkCorrectAnsIcon = document.getElementById(`check-correct-${completionNumber}`);
                            checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-check" style="color: #01f905;"></i>';
                            result.correct.push(completionNumber);

                        } else if (userAnswer === "") {
                            skipCount++;
                            console.log(`Question: ${completionNumber}, Part: ${partIndex + 1}, Correct Answer: ${correctAnswerString}, User Ans: ${userAnswer}`);
                            let checkCorrectAnsIcon = document.getElementById(`check-correct-${completionNumber}`);
                            checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-xmark" style="color: #de1b1b;"></i>';
                            result.skipped.push(completionNumber);

                        } else {
                            incorrectCount++;
                            console.log(`Question: ${completionNumber}, Part: ${partIndex + 1}, Correct Answer: ${correctAnswerString}, User Ans: ${userAnswer}`);
                            let checkCorrectAnsIcon = document.getElementById(`check-correct-${completionNumber}`);
                            checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-xmark" style="color: #de1b1b;"></i>';
                            result.incorrect.push(completionNumber);

                        }
                    } else {
                        // Trường hợp chỉ có một đáp án đúng
                        if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
                            correctCount++;
                            console.log(`Question: ${completionNumber}, Part: ${partIndex + 1}, Correct Answer: ${correctAnswer}, User Ans: ${userAnswer}`);
                            let checkCorrectAnsIcon = document.getElementById(`check-correct-${completionNumber}`);
                            checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-check" style="color: #01f905;"></i>';
                            correct_answer_array.push(completionNumber); // Add to correct answers
                            result.correct.push(completionNumber);

                        } else if (userAnswer === "") {
                            skipCount++;
                            console.log(`Question: ${completionNumber}, Part: ${partIndex + 1}, Correct Answer: ${correctAnswer}, User Ans: ${userAnswer}`);
                            let checkCorrectAnsIcon = document.getElementById(`check-correct-${completionNumber}`);
                            checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-xmark" style="color: #de1b1b;"></i>';
                            incorrect_skip_answer_array.push(completionNumber); // Add to skipped answers
                            result.skipped.push(completionNumber);

                        } else {
                            incorrectCount++;
                            console.log(`Question: ${completionNumber}, Part: ${partIndex + 1}, Correct Answer: ${correctAnswer}, User Ans: ${userAnswer}`);
                            let checkCorrectAnsIcon = document.getElementById(`check-correct-${completionNumber}`);
                            checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-xmark" style="color: #de1b1b;"></i>';
                            incorrect_skip_answer_array.push(completionNumber); // Add to skipped answers
                            result.incorrect.push(completionNumber);

                        }
                    }
            
                    correctAnswersData[completionNumber] = {
                        part: partIndex + 1,
                        correctAnswer: correctAnswer
                    };
                });
            
                currentQuestionNumber += question.box_answers.length;
            }
            

            // Process multiple-choice questions
            else if (group.type_group_question === "multiple-choice") {
                
                userAnswer = groupedQuestions[currentQuestionNumber - 1]?.userAnswer; // Get user's answer

                questionNumber = `${currentQuestionNumber}`;
                const correctAnswerIndex = question.answers.findIndex(answer => answer[1] === true);
                correctAnswer = correctAnswerIndex !== -1 ? question.answers[correctAnswerIndex][0] : "Not available";
                let checkCorrectAnsIcon = document.getElementById(`check-correct-${questionNumber}`);


                console.log(`Question: ${questionNumber}, Part: ${partIndex + 1}, Correct Answer: ${correctAnswer}, User Ans: ${userAnswer}`);

                // Check if user's answer is correct
                if (userAnswer == correctAnswer) {
                    correctCount++;
                    checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-check" style="color: #01f905;"></i>';
                    correct_answer_array.push(currentQuestionNumber); // Add to correct answers
                    result.correct.push(currentQuestionNumber);


                } else if (userAnswer == "") {
                    skipCount++;
                    checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-xmark" style="color: #de1b1b;"></i>';
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers
                    result.skipped.push(currentQuestionNumber);


                } else {
                    incorrectCount++;
                    checkCorrectAnsIcon.innerHTML = '<i class="fa-regular fa-circle-xmark" style="color: #de1b1b;"></i>';
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers
                    result.incorrect.push(currentQuestionNumber);


                }

                correctAnswersData[questionNumber] = {
                    part: partIndex + 1,
                    correctAnswer: correctAnswer
                };

                currentQuestionNumber++;
            }
        });
    }
)

totalNewCorrectAns += correctCount;
totalNewIncorrectAns += incorrectCount;
totalNewSkipAns += skipCount;
    // Log the counts for this part
    console.log(`Part ${partIndex + 1} Correct Answers: ${correctCount}`);
    console.log(`Part ${partIndex + 1} Incorrect Answers: ${incorrectCount}`);
    console.log(`Part ${partIndex + 1} Skipped Answers: ${skipCount}`);

    console.log(`Part ${partIndex + 1} Result:`, JSON.stringify(result, null, 2));

}





function updateCorrectAnswers() {
    Object.keys(correctAnswersData).forEach(questionNumber => {
        const correctAnswer = correctAnswersData[questionNumber].correctAnswer;
        const correctAnswerCell = document.getElementById(`correct-answer-${questionNumber}`);
        if (correctAnswerCell) {
            correctAnswerCell.textContent = correctAnswer;
        }
    });
}

function closeRemarkTest() {
    document.getElementById('remark_popup').style.display = 'none';
}

function openRemarkTest(){
    document.getElementById('remark_popup').style.display = 'block';
}
function opensharePermission() {
    document.getElementById('share_popup').style.display = 'block';

    // Define the initial state of the switch based on permissionLink
    const isPublic = permissionLink === "public";

    // Set the content for the popup dynamically
    document.getElementById('permissionShareContent').innerHTML = `
        <div class="permission-row">
            <p>Private</p>
            <label class="switch">
                <input type="checkbox" id="updatePermission" ${isPublic ? "checked" : ""}>
                <span class="slider round"></span>
            </label>
            <p>Public</p>
        </div>
        Your permission: ${permissionLink}<br>
    `;

    // Display the page URL
    document.getElementById("coppyShareContent").innerHTML =  window.location.href;

    // Attach the event listener to the toggle switch with ID 'updatePermission'
    document.getElementById("updatePermission").addEventListener("change", function () {
        togglePermission(this);
    });
}


function filterAnswerForEachType()
{
    console.log(`Incorrect/Skip array: ${incorrect_skip_answer_array }`);
    console.log(`Correct array: ${correct_answer_array  }`);
    console.log(`Filter các loại: ${JSON.stringify(quizData.filterTypeQuestion)}`);
    analyzeAnswersByType();
}

function analyzeAnswersByType() {
    const analysis = {};

    // Phân tích số liệu theo loại câu hỏi
    quizData.filterTypeQuestion.forEach((item) => {
        const questionNumber = Object.keys(item)[0];
        const questionType = item[questionNumber];

        if (!analysis[questionType]) {
            analysis[questionType] = {
                correct: 0,
                incorrect: 0,
            };
        }

        if (correct_answer_array.includes(parseInt(questionNumber))) {
            analysis[questionType].correct++;
        } else if (incorrect_skip_answer_array.includes(parseInt(questionNumber))) {
            analysis[questionType].incorrect++;
        }
    });

    console.log("Analysis by question type:", analysis);

    // Hiển thị bảng thống kê
    renderStatisticTable(analysis);
}

// Hàm hiển thị kết quả dưới dạng bảng
function renderStatisticTable(analysis) {
    const container = document.getElementById("quick-statistic");
    container.innerHTML = ""; // Xóa nội dung cũ

    // Tạo bảng
    const table = document.createElement("table");
    table.setAttribute("border", "1");
    table.style.borderCollapse = "collapse";
    table.style.width = "100%";

    // Tạo tiêu đề bảng
    const headerRow = document.createElement("tr");
    const headers = ["Type", "Correct", "Incorrect", "% Correct", "Comment"];
    headers.forEach((header) => {
        const th = document.createElement("th");
        th.innerText = header;
        th.style.padding = "8px";
        th.style.textAlign = "center";
        headerRow.appendChild(th);
    });
    table.appendChild(headerRow);

    // Tạo các dòng dữ liệu
    Object.entries(analysis).forEach(([type, stats]) => {
        const row = document.createElement("tr");

        // Cột loại câu hỏi
        const typeCell = document.createElement("td");
        typeCell.innerText = type;
        typeCell.style.padding = "8px";
        typeCell.style.textAlign = "center";
        row.appendChild(typeCell);

        // Cột số lượng đúng và sai
        ["correct", "incorrect"].forEach((key) => {
            const cell = document.createElement("td");
            cell.innerText = stats[key];
            cell.style.padding = "8px";
            cell.style.textAlign = "center";
            row.appendChild(cell);
        });

        // Cột % Correct
        const total = stats.correct + stats.incorrect;
        const percentCorrect = total > 0 ? ((stats.correct / total) * 100).toFixed(2) : 0;
        const percentCell = document.createElement("td");
        percentCell.innerText = `${percentCorrect}%`;
        percentCell.style.padding = "8px";
        percentCell.style.textAlign = "center";
        row.appendChild(percentCell);

        // Cột Comment
        const commentCell = document.createElement("td");
        commentCell.innerText =
            percentCorrect > 80
                ? "Tốt, hãy duy trì phong độ!"
                : "Hãy luyện thêm.";
        commentCell.style.padding = "8px";
        commentCell.style.textAlign = "center";
        row.appendChild(commentCell);

        table.appendChild(row);
    });

    // Gắn bảng vào container
    container.appendChild(table);
}

function closesharePermission() {
    document.getElementById('share_popup').style.display = 'none';
}


function coppyShareContentBtn() {
    // Lấy nội dung cần sao chép
    const copyText = document.getElementById("coppyShareContent").innerText;

    // Sao chép nội dung vào clipboard
    navigator.clipboard.writeText(copyText).then(() => {
        // Thông báo khi sao chép thành công
        alert("Copied the text: " + copyText);
    }).catch((err) => {
        console.error("Failed to copy text: ", err);
        alert("Failed to copy the text.");
    });
}

function redirectToTest(){
    
    window.location.href = `${linkTest}`;

}

document.addEventListener("DOMContentLoaded", function () {
    const updatePermission = document.getElementById("updatePermission");

    if (updatePermission) {
        updatePermission.addEventListener("change", function (event) {
            togglePermission(event.target);
        });
    }
});

function togglePermission(checkbox) {
    const newPermission = checkbox.checked ? "public" : "private";
    console.log('New permission:', newPermission); // In ra quyền mới

    // Gửi yêu cầu AJAX để cập nhật trạng thái
    const data = {
        action: "update_permission_link",
        type_test: "ielts_listening",
        testsavenumber: testsavenumber, // Giá trị testsavenumber từ server
        permission_link: newPermission
    };

    // Gửi yêu cầu AJAX
    fetch(ajaxurl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data) // Sử dụng URLSearchParams để gửi dữ liệu dạng form-urlencoded
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert("Permission updated to: " + newPermission);
        } else {
            alert("Failed to update permission: " + result.message); // Hiển thị thông báo chi tiết lỗi
        }
    })
    .catch(error => {
        console.error("Error updating permission:", error);
    });
}



let Alreadyremark = false;

function checkUpdateOverall()
{
    if (totalNewCorrectAns + totalNewIncorrectAns + totalNewSkipAns == 40)
    {
        if(totalNewCorrectAns  == 3){
            newOverallBand = 2;
        }
        else if(totalNewCorrectAns  >= 4 && totalNewCorrectAns  <= 5){
            newOverallBand = 2.5;
        }
        else if(totalNewCorrectAns  >= 6 && totalNewCorrectAns  <= 7){
            newOverallBand = 3.0;
        }
        else if(totalNewCorrectAns  >= 8 && totalNewCorrectAns  <= 9){
            newOverallBand = 3.5;
        }
        else if(totalNewCorrectAns  >= 10 && totalNewCorrectAns  <= 12){
            newOverallBand = 4.0;
        }
        else if(totalNewCorrectAns  >= 13 && totalNewCorrectAns  <= 14){
            newOverallBand = 4.5;
        }
        else if(totalNewCorrectAns  >= 15 && totalNewCorrectAns  <= 18){
            newOverallBand = 5.0;
        }
        else if(totalNewCorrectAns  >= 19 && totalNewCorrectAns  <= 22){
            newOverallBand = 5.5;
        }
        else if(totalNewCorrectAns  >= 23 && totalNewCorrectAns  <= 26){
            newOverallBand = 6.0;
        }
        else if(totalNewCorrectAns  >= 29 && totalNewCorrectAns  <= 29){
            newOverallBand = 6.5;
        }
        else if(totalNewCorrectAns  >= 30 && totalNewCorrectAns  <= 32){
            newOverallBand = 7.0;
        }
        else if(totalNewCorrectAns  >= 33 && totalNewCorrectAns  <= 34){
            newOverallBand = 7.5;
        }
        else if(totalNewCorrectAns  >= 35 && totalNewCorrectAns  <= 36){
            newOverallBand = 8.0;
        }
        else if(totalNewCorrectAns  >= 37 && totalNewCorrectAns  <= 38){
            newOverallBand = 8.5;
        }
        else if(totalNewCorrectAns  >= 39 && totalNewCorrectAns  <= 40){
            newOverallBand = 9.0;
        }
        else{
            newOverallBand = 0;
        }


        document.getElementById("new-overall").innerText += newOverallBand;

    }

}


function remarkTest(){
    if (!Alreadyremark){
      
        // Update the content with the correct number
        document.getElementById("old-correct-ans").innerText += oldCorrectNumber;
        document.getElementById("old-incorrect-ans").innerText += oldIncorrectNumber;
        document.getElementById("old-skip-ans").innerText += oldSkipNumber;
        document.getElementById("old-overall").innerText += oldOverallBand;
        checkUpdateOverall();


        document.getElementById("new-correct-ans").innerText += totalNewCorrectAns;
        document.getElementById("new-incorrect-ans").innerText += totalNewIncorrectAns;
        document.getElementById("new-skip-ans").innerText += totalNewSkipAns;

        if (oldCorrectNumber != totalNewCorrectAns || oldIncorrectNumber != totalNewIncorrectAns || oldSkipNumber != totalNewSkipAns)
        {
            document.getElementById("track_change_note").innerHTML = `Đáp án có sự thay đổi, hãy ấn vào nút Lưu kết quả để cập nhập`;
            document.getElementById("saveNewBtn").style.display = 'block';
        }
        else{
            document.getElementById("track_change_note").innerHTML = `Đáp án vẫn giữ nguyên, không có sự thay đổi nào !!!`;

        }

        // Display the remarkPoint section
        document.getElementById("remarkPoint").style.display = 'block';
        Alreadyremark = true;
    }
    else{
        document.getElementById("warningRemark").innerHTML = "<i>Bạn đã chấm lại kết quả thêm yêu cầu mới nhất và đã lưu vào hệ thống. Nếu phát hiện kết quả có lỗi hãy report !</i>"
    }
}




document.getElementById("saveNewBtn").addEventListener("click", function () {
    const newCorrectAnsNumber = totalNewCorrectAns;
    const newIncorrectAnsNumber = totalNewIncorrectAns;
    const newSkipAnsNumber = totalNewSkipAns;
    const newOverallBandNumber = parseFloat(newOverallBand.toFixed(1)); // Giới hạn 1 chữ số thập phân

    // Gửi dữ liệu qua AJAX để cập nhật cơ sở dữ liệu
    fetch(ajaxUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: 'update_ielts_listening_results',
            testsavenumber: testSaveNumber,
            correct_number: newCorrectAnsNumber,
            incorrect_number: newIncorrectAnsNumber,
            skip_number: newSkipAnsNumber,
            overallband: newOverallBandNumber
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Làm mới trang nếu cập nhật thành công
                alert('Kết quả đã được lưu thành công!');
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra trong khi cập nhật!');
        });
});




function main(){
    console.log("Passed Main");
    console.log(groupedQuestions);
    
    for(let i = 0; i < quizData.part.length; i ++){
        consoleAns(i);
        console.log(quizData.part[1].duration);
    }
    updateCorrectAnswers();
    loadPart(currentPartIndex);
    filterAnswerForEachType();
    hidePreloader();
}

