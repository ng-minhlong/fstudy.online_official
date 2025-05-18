const columnAns = document.getElementById("quick-view-answer");
let questionCount = 0;
let moduleMap = new Map(); // To store questions under each module

columnAns.innerHTML += `<h2 style="text-align: center">Quick View</h2>`;

// Count questions and group them by module
for (let i = 0; i < quizData.questions.length; i++) {
    questionCount++;
    let module = quizData.questions[i].question_category;
    
    if (!moduleMap.has(module)) {
        moduleMap.set(module, []);
    }
    moduleMap.get(module).push(i + 1); // Store question number (i+1) in the module
}

// Display the total question count and number of modules
let moduleCount = moduleMap.size;
columnAns.innerHTML += `Số câu: ${questionCount} <br> Số module: ${moduleCount} <br>`;

// Create a table for each module
moduleMap.forEach((questions, module) => {
    let tableHtml = `<h3>Module: ${module}</h3>`;
    tableHtml += `<table border="1" style="border-collapse: collapse; width: 100%; text-align: center;"><tr>`;
    
    questions.forEach((question, index) => {
        tableHtml += `<td>${question}. </td>`;
        
        // Create a new row after every 10th question
        if ((index + 1) % 10 === 0 && (index + 1) < questions.length) {
            tableHtml += `</tr><tr>`;
        }
    });
    
    tableHtml += `</tr></table><br>`;
    
    columnAns.innerHTML += tableHtml;
});

