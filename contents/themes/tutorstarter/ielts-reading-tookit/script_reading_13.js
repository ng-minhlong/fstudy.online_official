let currentQuestionNumber;
const dateElement = document.getElementById('date');

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
console.log(dateElement.innerHTML)


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
                
         

let test_type;

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
    console.log(`Highlights saved: ${JSON.stringify(highlights)}`);

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
     currentQuestionNumber = getStartingQuestionNumber(partIndex);

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

                questionElement.innerHTML = `<span id = "mark-question-${currentQuestionNumber}" class = "number-question"> <p  name = "question-id-${currentQuestionNumber}"  id ="question-id-${questionRange}"> <b>${questionRange}.</b> </p> </span>  <p> ${question.question}</p> `;

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
                        const isAnswered = event.target.checked; // true nếu chọn, false nếu bỏ chọn

                        saveUserAnswer(partIndex, groupIndex, questionIndex, answerIndex, event.target.checked);
                        checkboxCurrent(currentQuestionNumber); // Log số câu vừa hoàn thành
                        updateAnsweredCheckbox(currentQuestionNumber, isAnswered);

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
        
            // Determine if option_choice exists and is a non-empty array
            const hasOptionChoices = Array.isArray(group.option_choice) && group.option_choice.length > 0;
            const optionChoices = hasOptionChoices ? group.option_choice.map(String) : [];
        
            question.box_answers.forEach((boxAnswer, boxIndex) => {
                const completionNumber = currentQuestionNumber + boxIndex;
                const questionNumber = completionNumber;
                let questionType = ""; // Mặc định là chuỗi rỗng
        
                // Kiểm tra trước khi gán giá trị từ part.question_types
                if (part.question_types && part.question_types[completionNumber]) {
                    questionType = part.question_types[questionNumber];
                }
        
                let inputElementHtml = '';
        
                if (hasOptionChoices) {
                    // Build select with an initial empty option
                    let optionsHtml = `<option value="">` + `</option>`; // default empty
                    optionChoices.forEach(opt => {
                        // escape option text to avoid HTML injection
                        const safeOpt = String(opt).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
                        optionsHtml += `<option value="${safeOpt}">${safeOpt}</option>`;
                    });
        
                    inputElementHtml = `
                        <span style="display: inline-flex; align-items: center;">
                            <span id="mark-question-${completionNumber}" class="number-question">
                                <b type="${questionType}" style="margin: 0;">${completionNumber}</b>
                            </span>
                            <select class="form-control completion-select"
                                    name="question-id-${completionNumber}"
                                    id="answer-input-${completionNumber}">
                                ${optionsHtml}
                            </select>
                        </span>
                    `;
                } else {
                    // fallback to text input (original behaviour)
                    inputElementHtml = `
                        <span style="display: inline-flex; align-items: center;">
                            <span id="mark-question-${completionNumber}" class="number-question">
                                <b type="${questionType}" style="margin: 0;">${completionNumber}</b>
                            </span>
                            <input class="form-control completion-input"
                                autocomplete="off"
                                name="question-id-${completionNumber}"
                                type="text"
                                id="answer-input-${completionNumber}" />
                        </span>
                    `;
                }
        
                questionContent = questionContent.replace('<input>', inputElementHtml);
                completionInputIds.push(`answer-input-${completionNumber}`);
            });
        
            let currentIndexQuestion = currentQuestionNumber;
        
            // Gắn nội dung đã thay thế vào DOM
            questionElement.innerHTML = `${questionContent}`;
        
            // Chờ DOM cập nhật hoàn tất rồi mới gán sự kiện
            setTimeout(() => {
                completionInputIds.forEach((inputId, boxIndex) => {
                    const inputElement = document.getElementById(inputId);
        
                    if (!inputElement) {
                        console.error(`Input element with ID "${inputId}" not found.`);
                        return;
                    }
        
                    // Handler chung cho input/select
                    const handler = (value) => {
                        const isAnswered = String(value).trim() !== '';
                        checkboxCurrent(currentIndexQuestion + boxIndex);
                        // Lưu value (select sẽ lưu "" nếu chưa chọn)
                        saveCompletionAnswer(partIndex, groupIndex, questionIndex, boxIndex, value);
                        updateAnsweredCheckbox(currentIndexQuestion + boxIndex, isAnswered);
                    };
        
                    // Gán event: 'input' cho text, 'change' cho select
                    if (inputElement.tagName.toLowerCase() === 'select') {
                        inputElement.addEventListener('change', (event) => {
                            handler(event.target.value);
                        });
                    } else {
                        inputElement.addEventListener('input', (event) => {
                            handler(event.target.value);
                        });
                    }
        
                    // Khôi phục dữ liệu đã lưu
                    const savedAnswer = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex]?.[boxIndex];
                    if (savedAnswer !== undefined && savedAnswer !== null && String(savedAnswer) !== '') {
                        // Nếu là select, set value; nếu input text, set value
                        if (inputElement.tagName.toLowerCase() === 'select') {
                            // đảm bảo giá trị có trong options, nếu không thì giữ rỗng
                            const opt = Array.from(inputElement.options).find(o => o.value === String(savedAnswer));
                            if (opt) inputElement.value = String(savedAnswer);
                            // nếu không tìm thấy, để rỗng (user có thể chọn lại)
                        } else {
                            inputElement.value = String(savedAnswer);
                        }
                        // cập nhật checkbox hiển thị answered
                        updateAnsweredCheckbox(currentIndexQuestion + boxIndex, true);
                    } else {
                        // nếu không có savedAnswer, đảm bảo select mặc định là rỗng
                        if (inputElement.tagName.toLowerCase() === 'select') {
                            inputElement.value = '';
                        }
                        updateAnsweredCheckbox(currentIndexQuestion + boxIndex, false);
                    }
                });
            }, 0); // Delay để DOM cập nhật trước khi gán sự kiện
        
            // Tăng số thứ tự câu hỏi
            currentQuestionNumber += question.box_answers.length;
        }
        
        
        
        

            // Multiple-choice questions (single-select)
            if (group.type_group_question === "multiple-choice") {
           
                


                const questionNumber = currentQuestionNumber; 
                let questionType = ""; // Use `let` because you will reassign it
               // Kiểm tra trước khi gán giá trị từ part.question_types
               if (part.question_types && part.question_types[questionNumber]) {
                questionType = part.question_types[questionNumber];
            }

                questionElement.innerHTML = `
                <div style="display: inline-flex; align-items: center;">
                    <span id = "mark-question-${currentQuestionNumber}" class = "number-question"> <p type = "${questionType}" name="question-id-${currentQuestionNumber}" id="question-id-${currentQuestionNumber}" style="margin: 0;"> <b>${currentQuestionNumber}.</b> </p></span> 
                         <p id = "content-question-${currentQuestionNumber}" > ${question.question} </p>
                    
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
                        const allAnswers = document.querySelectorAll(`input[name="question-${currentIndexQuestion}"]`);
                        const isAnswered = Array.from(allAnswers).some(input => input.checked); // Kiểm tra có radio nào được chọn
                        checkboxCurrent(currentIndexQuestion);
                        saveUserAnswer(partIndex, groupIndex, questionIndex, answerIndex, event.target.value);
                        updateAnsweredCheckbox(currentIndexQuestion, isAnswered);
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
    devOnMode(currentPartIndex + 1, id_test);


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


function updateAnsweredCheckbox(currentQuestionNumber, isAnswered)
{
    const checkboxElement = document.getElementById(`question-checkbox-${currentQuestionNumber}`);
    if (checkboxElement) {
        if (isAnswered) {
            checkboxElement.classList.add('checkbox-answered'); // Đã trả lời, thêm màu
        } else {
            checkboxElement.classList.remove('checkbox-answered'); // Xóa lớp nếu chưa trả lời
        }
    }
}

document.addEventListener('click', (event) => {
    const target = event.target.closest('span'); // Lấy thẻ span gần nhất
    if (target && target.id.startsWith('mark-question-')) {
        toggleHighlight(target.id);
    }
});

function toggleHighlight(markId) {
    const questionElement = document.getElementById(markId);
    const checkboxId = markId.replace('mark-question-', 'question-checkbox-');
    const checkboxElement = document.getElementById(checkboxId);

    if (questionElement) {
        // Toggle màu vàng cho phần tử
        questionElement.classList.toggle('highlight-marked');
    }

    if (checkboxElement) {
        // Đồng bộ màu vàng trong checkbox tương ứng
        checkboxElement.classList.toggle('checkbox-marked');
    }
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
            if (currentPartIndex == part.part_number - 1){
                console.log("currentPartIndex", currentPartIndex);
                console.log(" part.part_number",  part.part_number - 1);

                
            }
            else{
                currentPartIndex = index;

                loadPart(currentPartIndex);

            }
    });
    
    partNavigation.appendChild(button);
});





const submitButton = document.createElement('button');
submitButton.innerHTML = "<i class='fa-solid fa-check'></i>";
submitButton.id = 'submit-btn';
let isSubmitted = false; // Kiểm tra xem đã submit hay chưa

submitButton.addEventListener('click', () => {
        PreSubmit();
       
});
function PreSubmit() {
    Swal.fire({
        title: "Bạn có chắc muốn nộp bài?",
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
                    timerInterval = setInterval(() => {}, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                    DoingTest = false;
                }
            }).then(async () => { // Thêm async ở đây
                try {
                    // Thực hiện tuần tự các hàm xử lý
                    for (let i = 0; i < quizData.part.length; i++) {
                        await logUserAnswers(i); // Thêm await
                    }
                    
                    // Chuyển sang hàm submitAnswerAndGenerateLink
                    await submitAnswerAndGenerateLink();
                } catch (error) {
                    console.error("Error during submission:", error);
                    Swal.fire({
                        title: "Lỗi",
                        text: "Đã có lỗi xảy ra khi nộp bài. Vui lòng thử lại.",
                        icon: "error"
                    });
                }
            });
        }
    });
}

async function submitAnswerAndGenerateLink() {
    try {
        // Thực hiện tuần tự các hàm xử lý kết quả
        await fulltestResult();
        await filterAnswerForEachType();
        await ResultInput();
        
        // Sau khi tất cả đã hoàn thành, hiển thị thông báo
        Swal.fire({
            title: "Kết quả đã có",
            html: "Chúc mừng bạn đã hoàn thành bài thi. Click vào link dưới để nhận kết quả ngay <i class='fa-regular fa-face-smile'></i>",
            allowOutsideClick: false,
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Xem kết quả",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${siteUrl}/ielts/r/result/${resultId}`;
            }
        });
    } catch (error) {
        console.error("Error generating result:", error);
        throw error; // Ném lỗi để hàm gọi xử lý
    }
}


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
    clearInterval(timerInterval);
    startTime = Date.now();
    countdownValue = duration; // Lưu giá trị countdown toàn cục
    
    const timerDisplay = document.getElementById('timer');
    
    if(count_number_part == 3) {
        test_type = "Full Test";
    } else {
        test_type = "Practice";
    }
    
    // Cập nhật ngay lần đầu
    timerDisplay.textContent = formatTime(duration);
    
    timerInterval = setInterval(function() {
        countdownValue = duration - Math.floor((Date.now() - startTime) / 1000);
        
        if(countdownValue <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = "00:00";
            if(!isSubmitted) {
                isSubmitted = true;
                PreSubmit();
            }
            return;
        }
        
        timerDisplay.textContent = formatTime(countdownValue);
    }, 1000);
}

// Khởi tạo các biến toàn cục để theo dõi tổng số câu đúng, sai và tổng số câu hỏi cho cả bài kiểm tra
let totalCorrectAnswers = 0;
let totalIncorrectAnswers = 0;
let totalQuestionsCount = 0;
let totalSkipAnswers = 0;

let incorrect_skip_answer_array = [];
let correct_answer_array = [];
    

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
                    correct_answer_array.push(currentQuestionNumber); // Add to correct answers

                } else if (userAnswer === "") {
                    skippedCount += answerChoiceCount; // Increment skipped count
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers

                } else {
                    incorrectCount += answerChoiceCount;
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers

                }

                userAnswerdiv.innerHTML += `Question: ${questionNumber}, Part: ${partIndex+1}, User Answer: ${userAnswer} <br>`;
                currentQuestionNumber += answerChoiceCount;
                totalQuestions += answerChoiceCount;
            }

            else if (group.type_group_question === "completion") {
                question.box_answers.forEach((boxAnswer, boxIndex) => {
                    const completionNumber = currentQuestionNumber + boxIndex;
                    const savedAnswer = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex]?.[boxIndex];
            
                    userAnswer = savedAnswer ? savedAnswer : "";
                    correctAnswer = boxAnswer.answer ? boxAnswer.answer : "Not available";
            
                    // Xử lý khi có nhiều đáp án đúng (danh sách đáp án)
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
                            incorrect_skip_answer_array.push(completionNumber); // Add to incorrect answers

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
                    correct_answer_array.push(currentQuestionNumber); // Add to correct answers

                } else if (userAnswer === "") {
                    skippedCount++; // Increment skipped count
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers

                } else {
                    incorrectCount++;
                    incorrect_skip_answer_array.push(currentQuestionNumber); // Add to skipped answers

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


async function filterAnswerForEachType()
{
    console.log(`Incorrect/Skip array: ${incorrect_skip_answer_array }`);
    console.log(`Correct array: ${correct_answer_array  }`);
    console.log(`Filter các loại: ${JSON.stringify(quizData.filterTypeQuestion)}`);
    await analyzeAnswersByType();
}

async function analyzeAnswersByType() {
    const analysis = {};

    quizData.filterTypeQuestion.forEach((item) => {
        const questionNumber = Object.keys(item)[0];
        const questionType = item[questionNumber];

        if (!analysis[questionType]) {
            analysis[questionType] = {
                correct: 0,
                incorrect: 0,
                skipped: 0,
            };
        }

        if (correct_answer_array.includes(parseInt(questionNumber))) {
            analysis[questionType].correct++;
        } else if (incorrect_skip_answer_array.includes(parseInt(questionNumber))) {
            analysis[questionType].incorrect++;
        } else {
            analysis[questionType].skipped++;
        }
    });

    console.log("Analysis by question type:", analysis);

    var link = "http://localhost/wordpress/wp-admin/admin-ajax.php";

    // Gửi dữ liệu tới WordPress qua AJAX
    
    jQuery.ajax({
        url: link, // Sử dụng URL đã chèn từ PHP
        method: 'POST',
        data: {
            action: 'save_analysis_result',
            analysisData: analysis,
        },
        success: function(response) {
            if (response.success) {
                console.log('Dữ liệu đã được lưu thành công!');
            } else {
                console.log('Lỗi: ' + response.data);
            }
        },
        error: function() {
            alert('Lỗi kết nối tới server');
        }
    });
    

    return analysis;
}


let ieltsBandScore;
async function fulltestResult(){
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


async function ResultInput() {
    // Copy the content to the form fields
   // var contentToCopy1 = document.getElementById("final-result").textContent;
    var contentToCopy2 = document.getElementById("date").textContent;
    var contentToCopy4 = document.getElementById("title-test").textContent;
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
    document.getElementById("test_type").value = test_type;

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
},0); // 5000 milliseconds = 5 seconds */
}
          
// Event listener for the submit button


function prestartTest()
{
    if(premium_test == "False"){
        console.log("Cho phép làm bài")
    }
    else{
    console.log(premium_test);
    console.log(token_need);
    console.log(change_content);
    console.log(time_left);
    // Giảm time_left tại frontend
    time_left--;
    console.log("Updated time_left:", time_left);

    // Gửi request AJAX đến admin-ajax.php
    jQuery.ajax({
        url: `${siteUrl}/wp-admin/admin-ajax.php`,
        type: "POST",
        data: {
            action: "update_time_left",
           // username: change_content,
            time_left: time_left,
            id_test: id_test,
            table_test: 'ielts_reading_test_list',

        },
        success: function (response) {
            console.log("Server response:", response);
        },
        error: function (error) {
            console.error("Error updating time_left:", error);
        }
    });
}
    startTest();
}


function startTest()
{
    startTimer(full_time * 60); // 1 hour
    loadPart(currentPartIndex, full_time);
    DoingTest = true;
    
}


let count_number_part = 0;
function main(){
    document.body.classList.add('watermark');
    console.log("Passed Main");
    
    for(let i = 0; i < quizData.part.length; i ++){
        //console.log(quizData.part[1].duration);
        count_number_part++;
        full_time += quizData.part[i].duration;
    }
    console.log("Full test time:", full_time)


    setTimeout(function(){
        console.log("Show Test!");
        document.getElementById("start_test").style.display="block";        
        document.getElementById("welcome").style.display="block";
        
    }, 1000);
    
}

function formatTime(countdownValue) {
    const minutes = Math.floor(countdownValue / 60);
    const seconds = countdownValue % 60;
    return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}
function getQuestionNumberFromIndices(partIndex, groupIndex, questionIndex, boxIndex = null) {
    let questionNumber = 1;
    
    // Duyệt qua các part trước part hiện tại
    for (let p = 0; p < partIndex; p++) {
        quizData.part[p].group_question.forEach(group => {
            group.questions.forEach(question => {
                if (group.type_group_question === "multi-select") {
                    questionNumber += parseInt(question.number_answer_choice) || 1;
                } 
                else if (group.type_group_question === "completion") {
                    questionNumber += question.box_answers.length;
                }
                else {
                    questionNumber++;
                }
            });
        });
    }
    
    // Xử lý part hiện tại
    const part = quizData.part[partIndex];
    for (let g = 0; g < groupIndex; g++) {
        part.group_question[g].questions.forEach(question => {
            if (part.group_question[g].type_group_question === "multi-select") {
                questionNumber += parseInt(question.number_answer_choice) || 1;
            } 
            else if (part.group_question[g].type_group_question === "completion") {
                questionNumber += question.box_answers.length;
            }
            else {
                questionNumber++;
            }
        });
    }
    
    // Xử lý nhóm hiện tại
    const group = part.group_question[groupIndex];
    for (let q = 0; q < questionIndex; q++) {
        const question = group.questions[q];
        if (group.type_group_question === "multi-select") {
            questionNumber += parseInt(question.number_answer_choice) || 1;
        } 
        else if (group.type_group_question === "completion") {
            questionNumber += question.box_answers.length;
        }
        else {
            questionNumber++;
        }
    }
    
    // Xử lý boxIndex nếu có (cho completion)
    if (boxIndex !== null) {
        questionNumber += boxIndex;
    }
    
    return questionNumber;
}function saveProgress() {
    const modal = document.getElementById("questionModal");
    const modalContent = document.getElementById("modalQuestionContent");
    const time_left = formatTime(countdownValue);
    
    let number_answered = 0;
    let user_answers = {}; // Đơn giản là object với key là số câu hỏi
    
    // Sử dụng userAnswers toàn cục thay vì query DOM
    quizData.part.forEach((part, partIndex) => {
        part.group_question.forEach((group, groupIndex) => {
            group.questions.forEach((question, questionIndex) => {
                const questionType = group.type_group_question;
                let questionNumber = getQuestionNumberFromIndices(partIndex, groupIndex, questionIndex);
                
                // Xử lý multi-select
                if (questionType === "multi-select") {
                    const answerCount = parseInt(question.number_answer_choice) || 1;
                    const selected = [];
                    
                    for (let i = 0; i < answerCount; i++) {
                        const answer = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex]?.[i];
                        if (answer) {
                            selected.push(answer);
                        }
                    }
                    
                    if (selected.length > 0) {
                        number_answered++;
                        user_answers[questionNumber] = selected;
                    }
                }
                // Xử lý completion
                else if (questionType === "completion") {
                    question.box_answers.forEach((boxAnswer, boxIndex) => {
                        const answer = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex]?.[boxIndex];
                        const currentQNum = questionNumber + boxIndex;
                        if (answer && answer.trim() !== '') {
                            number_answered++;
                            user_answers[currentQNum] = answer.trim();
                        }
                    });
                }
                // Xử lý multiple-choice
                else if (questionType === "multiple-choice") {
                    const answer = userAnswers?.[partIndex]?.[groupIndex]?.[questionIndex];
                    if (answer !== undefined && answer !== null) { // Bao gồm cả trường hợp chọn A (0)
                        number_answered++;
                        user_answers[questionNumber] = answer;
                    }
                }
            });
        });
    });

    const detailData = {
        time_left: countdownValue,
        user_answers: user_answers
    };

    console.log("All answers collected:", user_answers);
    console.log("Detail data (JSON):", JSON.stringify(detailData));

    var totalQuestions = 40;
    var percentCompleted = Math.floor(number_answered / totalQuestions * 100);

    // ... (phần còn lại của hàm saveProgress giữ nguyên)
    const currentDate = new Date();
    const dateCode = `${currentDate.getFullYear()}${String(currentDate.getMonth() + 1).padStart(2, '0')}${String(currentDate.getDate()).padStart(2, '0')}${String(currentDate.getHours()).padStart(2, '0')}${String(currentDate.getMinutes()).padStart(2, '0')}${String(currentDate.getSeconds()).padStart(2, '0')}`;
    const progress_id = `${CurrentuserID}${dateCode}`;

    modalContent.innerHTML = `
        <div>
            <p><strong>Loại test:</strong> Ielts Reading</p>
            <p><strong>Progress:</strong> ${currentQuestionNumber}/${totalQuestions}</p>
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

    document.getElementById('saveProgressBtn').addEventListener('click', function() {
        const btn = this;
        const resultDiv = document.getElementById('saveResult');
        const deleteSection = document.getElementById('deleteProgressSection');

        btn.disabled = true;
        btn.textContent = 'Đang lưu...';
        resultDiv.innerHTML = '<p>Đang xử lý...</p>';
        
        // Dữ liệu cần gửi (bao gồm tất cả câu trả lời)
        const data = {
            username: Currentusername,
            id_test: id_test,
            testname: testname,
            progress: currentQuestionNumber,
            percent_completed: percentCompleted,
            type_test: "ielts_reading",
            date: new Date().toLocaleString(),
            progress_id: progress_id,
            detail_data: detailData,
            user_answers: user_answers
        };

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