// Add this JavaScript code

// Open the note popup when the Draft button is clicked
document.getElementById('notesidebar').addEventListener('click', openNoteSidebarPopup);

// Close the note popup when the close button is clicked
function closeNoteSidebarPopup() {
    document.getElementById('notesidebar-popup').style.display = 'none';
}

function openNoteSidebarPopup() {
    document.getElementById('notesidebar-popup').style.display = 'block';
}

function changeContent(menuNumber) {
    // Store the content for each menu
    var menuContent = {
        1: "<h2>Hướng dẫn</h2><p>This is the content for Menu 1.</p>",
        2: "<h2>Lưu ghi chú</h2><p>Bạn có thể note lại ghi chú (Ví dụ: Các tips, các cấu trúc,...)<br><button onclick='addNoteTextarea()'>Thêm</button></p>",
        3: "<h2>Lưu công thức</h2><button onclick='addMathTextarea()'>Thêm</button><br><br>",
        4: "<h2>Lưu từ vựng</h2><p>Nhập từ vựng, nghĩa của từ đấy, loại từ vào các cột bên dưới.<br>Ấn nút 'thêm' để tạo ô từ vựng</p><button onclick='addVocabularyTextareas()'>Thêm</button>"
    };

    var content = document.getElementById("content");
    // Check if content for the selected menu already exists
    if (menuContent[menuNumber]) {
        content.innerHTML = menuContent[menuNumber];
    } else {
        // If content doesn't exist, show default message
        content.innerHTML = "<h2>Default Content</h2><p>This is the default content.</p>";
    }
}

function addNoteTextarea() {
    var content = document.getElementById("content");
    var noteTextarea = document.createElement("textarea");
    noteTextarea.setAttribute("name", "noteTextarea");
    content.appendChild(noteTextarea);

    var addButton = document.createElement("button");
    addButton.innerHTML = "Add";
    addButton.onclick = function() {
        saveNoteFull(noteTextarea);
    };
    content.appendChild(addButton);

    content.appendChild(document.createElement("br"));
}

 function addMathTextarea() {
    var content = document.getElementById("content");
    var mathTextareaName = "mathTextarea"; // Define the name for the textarea
    var newTextarea = document.createElement("textarea");
    newTextarea.setAttribute("name", mathTextareaName); // Set the name attribute
    content.insertBefore(newTextarea, content.firstChild.nextSibling.nextSibling.nextSibling);
    content.insertBefore(document.createElement("br"), content.firstChild.nextSibling.nextSibling.nextSibling);
    CKEDITOR.replace("mathTextarea");
}

function addVocabularyTextareas() {
    var content = document.getElementById("content");
    
    var vocabularyDiv = document.createElement("div");
    vocabularyDiv.style.display = "flex";
    vocabularyDiv.style.alignItems = "center";

    var labelWords = document.createElement("label");
    labelWords.innerHTML = "Words:";
    vocabularyDiv.appendChild(labelWords);

    var vocabTextarea1 = document.createElement("textarea");
    vocabTextarea1.setAttribute("id", "vocabTextarea1");
    vocabTextarea1.style.width = "30%";
    vocabularyDiv.appendChild(vocabTextarea1);

    var labelMeaning = document.createElement("label");
    labelMeaning.innerHTML = "Meaning:";
    vocabularyDiv.appendChild(labelMeaning);

    var vocabTextarea2 = document.createElement("textarea");
    vocabTextarea2.setAttribute("id", "vocabTextarea2");
    vocabTextarea2.style.width = "30%";
    vocabularyDiv.appendChild(vocabTextarea2);

    var labelType = document.createElement("label");
    labelType.innerHTML = "Type:";
    vocabularyDiv.appendChild(labelType);

    var vocabTextarea3 = document.createElement("textarea");
    vocabTextarea3.setAttribute("id", "vocabTextarea3");
    vocabTextarea3.style.width = "30%";
    vocabularyDiv.appendChild(vocabTextarea3);

    content.appendChild(vocabularyDiv);

    var addButton = document.createElement("button");
    addButton.innerHTML = "Add";
    addButton.onclick = function() {
        saveVocabulary(vocabTextarea1, vocabTextarea2, vocabTextarea3);
    };
    content.appendChild(addButton);

    content.appendChild(document.createElement("br"));
}

function saveNoteFull(textarea) {
    // Implement save logic for the note textarea
    console.log("Saved note:", textarea.value);
}

function saveVocabulary(textarea1, textarea2, textarea3) {
    // Implement save logic for the vocabulary textareas
    console.log("Saved vocabulary:", "Từ vựng: ",textarea1.value, "Nghĩa: ", textarea2.value, textarea3.value);
}
