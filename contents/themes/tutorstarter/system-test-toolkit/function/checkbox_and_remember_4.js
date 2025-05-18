// Open the checkbox popup when the Checkbox button is clicked
document.getElementById('checkbox-button').addEventListener('click', openCheckboxPopup);

// Close the checkbox popup when the close button is clicked
function closeCheckboxPopup() {
    document.getElementById('checkbox-popup').style.display = 'none';
}

function openCheckboxPopup() {
    const popup = document.getElementById('checkbox-popup');
    popup.style.display = 'block';

    // Lắng nghe sự kiện click ngoài popup
    setTimeout(() => {
        document.addEventListener('click', outsideClickListener);
    }, 0);
}

function outsideClickListener(event) {
    const popup = document.getElementById('checkbox-popup');
    if (!popup.contains(event.target)) {
        closeCheckboxPopup();
        document.removeEventListener('click', outsideClickListener);
    }
}



function addEventListenersToInputs() {
    
    for (var z = 1; z <= quizData.number_questions; z++) {
        var checkboxContainer = document.getElementById('checkbox-container-' + z);
        checkboxContainer.innerText = z;
        var questionInputs = document.querySelectorAll('[name="question-' + z + '"]');
        
        for (var i = 0; i < questionInputs.length; i++) {
            questionInputs[i].addEventListener('change', function (event) {
                var containerId = event.target.name.split('-')[1];
                var isChecked = isQuestionAnswered(containerId);
                updateCheckboxStatus(containerId, isChecked);
            });
        }
    }

    // Listen for input events on completion type questions
    var completionInputs = document.querySelectorAll('input[type="text"]');
    completionInputs.forEach(function(input) {
        input.addEventListener('input', function(event) {
            var questionNumber = event.target.id.split('-')[1];
            var isChecked = isQuestionAnswered(questionNumber);
            updateCheckboxStatus(questionNumber, isChecked);
           
        });
    });
}





function isQuestionAnswered(questionNumber) {
    var questionInputs = document.querySelectorAll('[name="question-' + questionNumber + '"]');


    for (var i = 0; i < questionInputs.length; i++) {
        if (questionInputs[i].type === 'text' && questionInputs[i].value.trim() !== '') {
            return true;
        } else if ((questionInputs[i].type === 'radio' || questionInputs[i].type === 'checkbox') && questionInputs[i].checked) {
            return true;
        }
    }
    return false;
}

/*
function updateCheckboxStatus(questionNumber, isChecked) {
    var checkboxContainer = document.getElementById('checkbox-container-' + questionNumber);
    var isCompleted = false;

    // Check if any input field is filled
    var completionInput = document.getElementById('question-' + questionNumber + '-input');
    if (completionInput && completionInput.value.trim() !== '') {
        isCompleted = true;
    } else {
        var questionInputs = document.querySelectorAll('[name="question-' + questionNumber + '"]');
        
        questionInputs.forEach(function(input) {
            if ((input.type === 'text' && input.value.trim() !== '') || 
                ((input.type === 'radio' || input.type === 'checkbox') && input.checked)) {
                isCompleted = true;
            }
        });
    }

    // Update checkbox container class based on completion status
    if (isCompleted) {
        checkboxContainer.classList.add('answered');
        console.log(questionInputs)
    } else {
        checkboxContainer.classList.remove('answered');
    }
}*/

// start new updatestatus

let countFinishQuestion = 0;
let finishQuestion = [];
let unfinishQuestion = [];
let countUnFinishQuestion = 0;

// Function to update the checkbox status and real-time table
function updateCheckboxStatus(questionNumber) {
    var checkboxContainer = document.getElementById('checkbox-container-' + questionNumber);
    var checkboxContainer2 = document.getElementById('checkbox-container-2-' + questionNumber);

    if (!checkboxContainer) {
        console.error('Checkbox container not found for question number:', questionNumber);
        return;
    }
    if (!checkboxContainer2) {
        console.error('Checkbox container not found for question number:', questionNumber);
        return;
    }

    var isCompleted = false;

    // Check if any input field is filled
    var completionInput = document.getElementById('question-' + questionNumber + '-input');
    let completionValue = '';

    if (completionInput) {
        completionValue = completionInput.value.trim();
        if (completionValue !== '') {
            isCompleted = true;
        }
    }

    var questionInputs = document.querySelectorAll('[name="question-' + questionNumber + '"]');

    // Define the mapping of indexes to alphabetical labels
    const alphabeticalLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

    // To store selected options
    let selectedOptions = [];

    questionInputs.forEach(function(input) {
        if (input.type === 'text') {
            if (input.value.trim() !== '') {
                isCompleted = true;
                completionValue = input.value.trim();
            }
        } else if (input.type === 'radio' || input.type === 'checkbox') {
            if (input.checked) {
                const index = Array.from(questionInputs).indexOf(input);
                if (index >= 0 && index < alphabeticalLabels.length) {
                    const label = alphabeticalLabels[index];
                    selectedOptions.push(label);
                    isCompleted = true;
                }
            }
        }
    });

    // Update checkbox container class based on completion status
    if (isCompleted) {
        checkboxContainer.classList.add('answered');
        checkboxContainer2.classList.add('answered');
    } else {
        checkboxContainer.classList.remove('answered');
        checkboxContainer2.classList.remove('answered');

    }

    // Update the real-time table
    updateRealTimeTable(questionNumber, selectedOptions, completionValue);
}

// Function to update the real-time table and count finished/unfinished questions
function updateRealTimeTable(questionNumber, selectedOptions, completionValue) {
    const questionCell = document.getElementById(`question-cell-${questionNumber}`);
    
    if (questionCell) {
        // Remove question number from both finish and unfinish arrays
        finishQuestion = finishQuestion.filter(q => q !== questionNumber);
        unfinishQuestion = unfinishQuestion.filter(q => q !== questionNumber);
        
        if (selectedOptions.length > 0 || completionValue) {
            questionCell.innerHTML = `${questionNumber}. ${selectedOptions.length > 0 ? selectedOptions.join(', ') : completionValue}`;
            finishQuestion.push(questionNumber); // Add to finished questions
        } else {
            questionCell.innerHTML = `${questionNumber}. No input`;
            unfinishQuestion.push(questionNumber); // Add to unfinished questions
        }
    }

    // Update counts
    countFinishQuestion = finishQuestion.length;
    countUnFinishQuestion = unfinishQuestion.length;

    // Update the table footer with the current counts
    updateQuickViewFooter();
}

// Function to update the footer of the quick view table with counts
function updateQuickViewFooter() {
    const columnAns = document.getElementById("quick-view-answer");
    const footerHtml = `
        <br><br>
        Number of finished questions: ${countFinishQuestion} (Questions: ${finishQuestion.join(', ')})<br>
       <!-- Number of unfinished questions: ${countUnFinishQuestion} (Questions: ${unfinishQuestion.join(', ')}) -->
    `;
    // Ensure that the footer is added once and updated each time
    const existingFooter = document.getElementById("quick-view-footer");
    if (existingFooter) {
        existingFooter.innerHTML = footerHtml;
    } else {
        const footerDiv = document.createElement("div");
        footerDiv.id = "quick-view-footer";
        footerDiv.innerHTML = footerHtml;
        columnAns.appendChild(footerDiv);
    }
}

// Initialize the table with questions, modules, and user answers
function initializeQuickViewTable(quizData) {
    const columnAns = document.getElementById("quick-view-answer");
    let moduleMap = new Map(); // To store questions under each module

    // Reset the innerHTML to ensure only one table is created
    columnAns.innerHTML = `<h2 style="text-align: center">Quick View</h2>`;

    // Group questions by module
    for (let i = 0; i < quizData.questions.length; i++) {
        let module = quizData.questions[i].question_category || '';

        if (!moduleMap.has(module)) {
            moduleMap.set(module, []);
        }
        moduleMap.get(module).push(i + 1); // Store question number (i+1) in the module
    }
    let moduleCount = moduleMap.size;

    // Create a table for each module
    let moduleNames = Array.from(moduleMap.keys()).join(', ');

    // Display the total number of questions and the types of modules
    columnAns.innerHTML += `Question Number: ${quizData.questions.length} question(s) <br>`;
    columnAns.innerHTML += `Question Type: ${moduleNames} (${moduleCount} module${moduleCount > 1 ? 's' : ''})<br><br>`;
    
    moduleMap.forEach((questions, module) => {
        let tableHtml = `<h3>${module}</h3>`;

        tableHtml += `<table border="1" style="border-collapse: collapse; width: 100%; text-align: center;"><tr>`;

        questions.forEach((question, index) => {
            tableHtml += `<td id="question-cell-${question}">${question}.  </td>`;
            
            // Create a new row after every 10th question
            if ((index + 1) % 10 === 0 && (index + 1) < questions.length) {
                tableHtml += `</tr><tr>`;
            }
        });

        tableHtml += `</tr></table><br>`;
        columnAns.innerHTML += tableHtml;
    });

    // Initialize footer for finish/unfinish count
    updateQuickViewFooter();
}

// Example call to initialize the quick view table with your quiz data
initializeQuickViewTable(quizData);

// end new updatestatus



function CheckSubmit() {
    let resultsParagraph = "Kết quả lưu:\n";

    // Iterate over each question in the quiz
    for (let questionNumber = 1; questionNumber <= quizData.questions.length; questionNumber++) {
        const questionCell = document.getElementById(`question-cell-${questionNumber}`);
        if (questionCell) {
            let userAnswer = questionCell.textContent.trim();
            resultsParagraph += `Question ${userAnswer}\n`;
        } else {
            resultsParagraph += `Question ${questionNumber}: No input\n`;
        }
    }

    // Output the results paragraph to the console or display it in the UI
    console.log(resultsParagraph);
    let userAnswerdiv = document.getElementById('useranswerdiv');
    userAnswerdiv.innerHTML += `${resultsParagraph}`

    // Optionally, you can display the results in an HTML element
    const resultsDiv = document.getElementById('results-output');
    if (resultsDiv) {
        resultsDiv.textContent = resultsParagraph.replace(/\n/g, '<br>');
    }
}




function rememberQuestion(i) {
   

    var checkboxContainer = document.getElementById('checkbox-container-' + i);

    var bookmarkQuestionElement = document.getElementById("bookmark-question-" + i);

    if (!bookmarkQuestionElement) {
        console.error("Element with id 'bookmark-question-" + i + "' not found.");
        return;
    }

    console.log("Book mark: bookmark-question-" + i);

    // Toggle the background color of the question checkbox container and change the image src
    if (checkboxContainer.style.backgroundColor === 'yellow') {
        checkboxContainer.style.backgroundColor = '';
        bookmarkQuestionElement.style.backgroundColor = '';
        bookmarkQuestionElement.src = `${site_url}/contents/themes/tutorstarter/system-test-toolkit/bookmark_empty.png`;
        // Check if the question is answered, then update checkbox status
        var isChecked = isQuestionAnswered(i);
        updateCheckboxStatus(i, isChecked);
    } else {
        checkboxContainer.style.backgroundColor = 'yellow';
        //bookmarkQuestionElement.style.backgroundColor = 'yellow';
        bookmarkQuestionElement.src = `${site_url}/contents/themes/tutorstarter/system-test-toolkit/bookmark_filled.png`;
    }

}