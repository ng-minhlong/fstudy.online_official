// Function to calculate the starting question number for a part
function getStartingQuestionNumber(partIndex, quizData) {
    let startQuestion = 1; // Start with question 1
    for (let i = 0; i < partIndex; i++) {
        startQuestion += parseInt(quizData.part[i].number_question_of_this_part);
    }
    return startQuestion;
}

// Function to calculate question range for a part
function getQuestionRange(partIndex, quizData) {
    const startQuestion = getStartingQuestionNumber(partIndex, quizData);
    const endQuestion = startQuestion + parseInt(quizData.part[partIndex].number_question_of_this_part) - 1;
    return `${startQuestion} - ${endQuestion}`;
}

// Function to log user answers dynamically for a specific row
function logUserAnswers(partIndex, quizData, rowId) {
    let userAnswerdiv = document.getElementById(`useranswerdiv_${rowId}`); // Target div by row ID
    
    const part = quizData.part[partIndex];
    let totalQuestions = 0; // Total question count

    // Initialize the current question number for this part
    let currentQuestionNumber = getStartingQuestionNumber(partIndex, quizData);

    part.group_question.forEach((group, groupIndex) => {
        group.questions.forEach((question, questionIndex) => {
            let questionNumber;
            let correctAnswer;

            // Handle multi-select questions
            if (group.type_group_question === "multi-select") {
                const answerChoiceCount = parseInt(question.number_answer_choice) || 1;
                questionNumber = `${currentQuestionNumber}-${currentQuestionNumber + answerChoiceCount - 1}`;
              
                const correctAnswers = [];

                question.answers.forEach((answer, answerIndex) => {
                    if (answer[1] === true) {
                        correctAnswers.push(answer[0]);
                    }
                });

                correctAnswer = correctAnswers.length > 0 ? correctAnswers.join(', ') : "Not available";

                userAnswerdiv.innerHTML += `<b> Question ${questionNumber}: </b> ${correctAnswer}<br>`;

                currentQuestionNumber += answerChoiceCount; // Increment the question number
                totalQuestions += answerChoiceCount; // Count total number of questions
            }

            // Handle completion questions
            else if (group.type_group_question === "completion") {
                question.box_answers.forEach((boxAnswer, boxIndex) => {
                    const completionNumber = currentQuestionNumber + boxIndex;

                    correctAnswer = boxAnswer.answer ? boxAnswer.answer : "Not available";

                    userAnswerdiv.innerHTML += `<b>Question ${completionNumber}: </b> ${correctAnswer}<br>`;
                });

                currentQuestionNumber += question.box_answers.length; // Increment by the number of boxes
                totalQuestions += question.box_answers.length; // Count total number of completion questions
            }

            // Handle multiple-choice questions
            else if (group.type_group_question === "multiple-choice") {
                questionNumber = `${currentQuestionNumber}`;

                const correctAnswerIndex = question.answers.findIndex(answer => answer[1] === true);
                correctAnswer = correctAnswerIndex !== -1 ? question.answers[correctAnswerIndex][0] : "Not available";

                userAnswerdiv.innerHTML += `<b>Question ${questionNumber}:</b> ${correctAnswer}<br>`;

                currentQuestionNumber++; // Increment the question number for the next question
                totalQuestions++; // Count as a single question
            }
        });
    });
}









/*





Từ đây xuống là sửa cho Preview full đáp án hoàn chỉnh - như trong test chính thức ( khác với bên trên là show mỗi key )


*/
function getQuestionRange2(partIndex) {
    const startQuestion = getStartingQuestionNumber(partIndex);
    const endQuestion = startQuestion + parseInt(quizData.part[partIndex].number_question_of_this_part) - 1;
    return `${startQuestion} - ${endQuestion}`;
}
function getStartingQuestionNumber2(partIndex) {
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



function loadPart(idPart, partIndex) {
    const part = quizData.part[partIndex];
    console.log("Passed LoadPart");

    // Display the paragraph
    document.getElementById(`paragraph-container-${idPart}`).innerHTML = `<p>${part.paragraph}</p>`;

    // Display the question range
    const questionRange = getQuestionRange2(partIndex);

    // Update the question range and add the timer
    const timerHtml = `<span id="timer" style="font-weight: bold></span>`;

    // Start the timer
    // Display the groups and questions
    const questionsContainer = document.getElementById(`questions-container-${idPart}`);
    questionsContainer.innerHTML = ''; // Clear previous content

    // Calculate the starting question number for this part
    let currentQuestionNumber = getStartingQuestionNumber2(partIndex);

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

}