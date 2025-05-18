function addSampletoTab() {
    let sampleTabs = document.getElementById("sample-tab-container");
    
    // Create a table element
    let table = document.createElement("table");
    table.style.width = "100%";
    table.style.borderCollapse = "collapse";

    // Loop through quiz data to populate the table
    for (let i = 0; i < quizData.questions.length; i++) {
        let row = document.createElement("tr");
        
        // Create first cell for question
        let questionCell = document.createElement("td");
        questionCell.style.border = "1px solid #ddd";
        questionCell.style.padding = "8px";
        questionCell.textContent = quizData.questions[i].question;

        // Create second cell for sample answer
        let answerCell = document.createElement("td");
        answerCell.style.border = "1px solid #ddd";
        answerCell.style.padding = "8px";
        answerCell.textContent = quizData.questions[i].sample;

        // Create button to read the sample answer aloud
        let readButton = document.createElement("button");
        readButton.textContent = "Read Aloud";
        readButton.style.marginLeft = "10px";
        readButton.onclick = () => readAloud(quizData.questions[i].sample);
        answerCell.appendChild(readButton);

        // Append cells to the row
        row.appendChild(questionCell);
        row.appendChild(answerCell);

        // Append the row to the table
        table.appendChild(row);
    }

    // Clear the container and append the table
    sampleTabs.innerHTML = "";
    sampleTabs.appendChild(table);
}

// Function to read the text aloud using the Web Speech API
function readAloud(text) {
    const utterance = new SpeechSynthesisUtterance(text);
    speechSynthesis.speak(utterance);
}
