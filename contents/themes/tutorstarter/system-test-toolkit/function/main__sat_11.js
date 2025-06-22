
function showCompletionGuide() {
    var x = document.getElementById("guide_completion");
    if (x.style.display === "none") {
      x.style.display = "block";
    } else {
      x.style.display = "none";
    }
  }


function convertImageTag(text) {
    return text.replace(/([a-zA-Z0-9_-]+\.png)/g, function(match) {
        return '<img class="img-ans" src="' + siteUrl + '/contents/themes/tutorstarter/template/media_img_intest/digital_sat/' + match + '" />';
    });
}




  let base64Images = []; // Initialize array to store Base64 encoded images

  function encodeImage(imageUrl, index) {
    return fetch(imageUrl)
        .then(response => response.blob())
        .then(blob => {
            const reader = new FileReader();
            return new Promise((resolve, reject) => {
                reader.onloadend = function() {
                    base64Images[index] = reader.result; // Store Base64 string
                    resolve(); // Resolve promise when done
                };
                reader.onerror = reject; // Reject promise on error
                reader.readAsDataURL(blob);
            });
        })
        .catch(error => console.error('Error fetching image:', error));
}




 //Translate tool (Google trans)



// Close the draft popup when the close button is clicked
function closeCalculator() {
    document.getElementById('calculator').style.display = 'none';
}

function openCalculator() {
    document.getElementById('calculator').style.display = 'block';
    
}

function toggleRemoveChoice() {
    // Toggle the state of removeChoice
    removeChoice = !removeChoice;
    console.log(removeChoice ? "Remove Choice feature is on!" : "Remove Choice feature is off now.");

    // Select all .crossing-zone and .answer-options elements
    const crossingZones = document.querySelectorAll('.crossing-zone');
    const answerOptions = document.querySelectorAll('.answer-options');

    // Loop through each element to show/hide based on removeChoice state
    crossingZones.forEach((zone, index) => {
        zone.style.display = removeChoice ? 'block' : 'none'; // Show if removeChoice is true, hide otherwise
       // answerOptions[index].style.width = removeChoice ? '75%' : '100%'; // Adjust the width accordingly
    });

    // Handle the image source toggle
    const imageElement = document.getElementById("removeChoiceImage");
    if (imageElement) {
        imageElement.src = removeChoice
            ? `${site_url}/contents/themes/tutorstarter/system-test-toolkit/crossAbcActive.png`
            : `${site_url}/contents/themes/tutorstarter/system-test-toolkit/crossAbc.png`;
    }
}


// Hàm để lấy nhãn A, B, C, D
function getCrossLabel(ans) {
    switch (ans) {
        case 1: return 'A';
        case 2: return 'B';
        case 3: return 'C';
        case 4: return 'D';
        default: return '';
    }
}


// Close the draft popup when the close button is clicked
function closeDraftPopup() {
    document.getElementById('draft-popup').style.display = 'none';
}

function openDraftPopup() {
    document.getElementById('draft-popup').style.display = 'block';
    // Initialize CKEditor in the textarea with id 'editor'
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
}


// Close the settings popup when the close button is clicked
function closeSettingPopup() {
    document.getElementById('setting-popup').style.display = 'none';
}

function openSettingPopup() {
    document.getElementById('setting-popup').style.display = 'block';
   
}



// Close the settings popup when the close button is clicked
function closeResumePopup() {
    document.getElementById('resume-popup').style.display = 'none';
    startCountdown();
}

function openResumePopup() {
    //document.getElementById('resume-popup').style.display = 'block';
    clearInterval(countdownInterval);
    Swal.fire({
        title: "Stop Timing",
        text: "You won't be able to to do test until continue",
        icon: "warning",
        allowOutsideClick: false,
        showCancelButton: false,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Continue test"
      }).then((result) => {
        if (result.isConfirmed) {
            startCountdown();
        }
      });

    
   
}


function openDraft(index) {
    var x = document.getElementById("draft-"+index);
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }

  // Function to map index to letter
function getLabelLetter(index) {
    const letters = ['A', 'B', 'C', 'D'];
    return letters[index] || '';
}

// Hàm để hiển thị checkboxes của category hiện tại và ẩn các category khác
function showCheckboxesForCategory(category) {
   // console.log("currentCategory: oke", currentCategory)
    // Ẩn tất cả các module
    const allModules = document.querySelectorAll('[id^="module-"]');
    const allModules2 = document.querySelectorAll('[id^="module-2-"]');

    allModules.forEach(module => {
        module.style.display = 'none';
    });
    allModules2.forEach(module2 => {
        module2.style.display = 'none';
    });
    // Hiển thị module của category hiện tại
    const currentModule = document.getElementById('module-' + category);
    const currentModule2 = document.getElementById('module-2-' + category);

    if (currentModule) {
        currentModule.style.display = 'block';
    }
    if (currentModule2) {
        currentModule2.style.display = 'block';
    }
}

// Ví dụ sử dụng:
const currentCategory = quizData.questions[currentQuestionIndex].question_category;

function updateReviewPage(module_question) {
    let contentCheckboxes2 = '';
    contentCheckboxes2 += '<div id="module-2-' + module_question +'" >';
    contentCheckboxes2 += '<div id="name-module-checkbox-2"><b>Current Module: ' + module_question + '</b></div>';
    contentCheckboxes2 += '<div class="icon-detail">';
    contentCheckboxes2 += '<div class="single-detail"><i class="fa-regular fa-flag"></i><span>Current</span></div>';
    contentCheckboxes2 += '<div class="single-detail"><div class="dashed-square"></div><span>Unanswered</span></div>';
    contentCheckboxes2 += '<div class="single-detail"><i class="fa-solid fa-bookmark" style="color: #c33228;"></i><span>For Review</span></div>';
    contentCheckboxes2 += '</div>';
    contentCheckboxes2 += '<div class="contents"> <div class="checkbox-module-name"> <div class="question-list">';

    for (let j = 0; j < quizData.questions.length; j++) {
        if (quizData.questions[j].question_category === module_question) {
            contentCheckboxes2 += '<div class="checkbox-container" onclick="ChangeQuestion(' + (j + 1) + ')" id="checkbox-container-2-' + (j + 1) + '">' + (j + 1) + '</div>';
        }
    }

    contentCheckboxes2 += '</div></div> </div></div>';

    document.getElementById('review-content').innerHTML = contentCheckboxes2;
}

function main() {
    document.body.classList.add('watermark');

   // document.getElementById("start-test").style.display  ='block';

   MathJax.Hub.Queue(["Typeset",MathJax.Hub]);



    if (quizData.logoname == undefined) {
        quizData.logoname = '';
    }
    const fullTitle = quizData.title + quizData.logoname;
    document.getElementById("title").innerHTML = quizData.title;
    document.getElementById("title-table-result").innerHTML = quizData.title;
    
    
    if (quizData.description != "") {
        document.getElementById("description").innerHTML = quizData.description;
    }

    


    document.getElementById("id_test").innerHTML = pre_id_test_;
    document.getElementById("testtype").innerHTML = quizData.test_type;
    document.getElementById("label").innerHTML = quizData.label;
    document.getElementById("duration").innerHTML = formatTime(quizData.duration);
    document.getElementById("pass_percent").innerHTML = quizData.pass_percent + "%";
    document.getElementById("number-questions").innerHTML = quizData.number_questions + " question(s)";
    var checkboxesContainer = document.getElementById("checkboxes-container");
    var reviewPageBtn = document.getElementById("review-page-container");

    





    let contentCheckboxes = '';
    let contentCheckboxes2 = '';

    var openReviewPage = "";
    

    let contentQuestions = "";
    let currentCategory = "";
    let questionNumber = 1;
    let checkboxNumber = 1;



/*
    // Create an array of promises to wait for all images to be encoded
    let encodingPromises = quizData.questions.map((question, index) => {
        if (question.image) {
            return encodeImage(question.image, index); // Encode each image
        }
        return Promise.resolve(); // No image to encode
    });
*/

    //Promise.all(encodingPromises).then(() => {
      

        contentQuestions +=`   <div class="navigation-group"> <div class="left-group"><b><p style = "font-size: 26px; font-family: none"  id="current_module" style="display: none;"></p></b></div>`;
            
       
        
        contentQuestions += `<div class = "center-group"><span class="icon-text-wrapper"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16"><path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/><path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/><path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/></svg> <h3 id="countdown"></h3></span></div>`;
        
        contentQuestions +=`<div class="right-group"><button  id="right-main-button" onclick = 'openCalculator()'>  <span class="icon-text-wrapper">
 <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-calculator" viewBox="0 0 16 16">
  <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/><path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
</svg> Calculator </span></button>  

<button  id="right-main-button" onclick = 'saveProgress()'>  <span class="icon-text-wrapper">
<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5"/><path d="M13.8 12H3m9 10a10 10 0 1 0 0-20"/>
</svg>  Save Progress </span></button>  

<button  id="right-main-button" onclick = 'openDraftPopup()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-plus" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
</svg>
Note</span></button>  



<button  id="right-main-button" onclick = 'openSettingPopup()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
  <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
  <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
</svg>
Setting </span></button>  

<button  id="right-main-button" onclick = 'openResumePopup()'>  <span class="icon-text-wrapper"><i class="fa-solid fa-hourglass-start"></i>
Resume </span></button>

</div></div><div id="time-personalize-div"></div>`;


for (let i = 0; i < quizData.questions.length; i++) {
    const question = quizData.questions[i];

    const category_question_type = question.type === 'completion' ? 'Điền vào chỗ trống' :
        question.type === 'multiple-choice' ? 'Chọn 1 đáp án đúng' :
        question.type === 'multi-select' ? 'Chọn nhiều đáp án đúng' : '';

    const module_question = question.question_category || '';

    if (quizData.restart_question_number_for_each_question_catagory == "Yes") {
        if (currentCategory !== module_question) {
            // Nếu có module cũ, đóng div của nó trước khi mở module mới
            if (currentCategory !== "") {
                contentCheckboxes += '</div></div>';
            }

            currentCategory = module_question;
            questionNumber = 1;
            checkboxNumber = 1;

            // Mở div mới cho module
            contentCheckboxes += '<div id="module-' + module_question + '" style="display: none;">'; // Ẩn module mặc định
            contentCheckboxes += '<div id="name-module-checkbox"><b>Current Module: ' + module_question + '</b></div>';
            contentCheckboxes += '<div class="icon-detail">';
            contentCheckboxes += '<div class="single-detail"><i class="fa-regular fa-flag"></i><span>Current</span></div>';
            contentCheckboxes += '<div class="single-detail"><div class="dashed-square"></div><span>Unanswered</span></div>';
            contentCheckboxes += '<div class="single-detail"><i class="fa-solid fa-bookmark" style="color: #c33228;"></i><span>For Review</span></div>';
            contentCheckboxes += '</div>'; // Đóng div icon-detail
            contentCheckboxes += '<div class="checkbox-module-name">';
        }

        // Thêm checkbox vào module hiện tại
        contentCheckboxes += '<div class="checkbox-container" onclick="ChangeQuestion(' + (i + 1) + ')" id="checkbox-container-' + (i + 1) + '">' + module_question + ' ' + checkboxNumber + '</div>';
        checkboxNumber++;
    } else {
        questionNumber = i + 1;
    }

    // Khi kết thúc vòng lặp, đóng div cuối cùng
    if (i === quizData.questions.length - 1) {
        contentCheckboxes += '</div></div>'; // Đóng div của category cuối cùng
    }
}

contentQuestions += `
<div class="review-page" id="review-page" style="display: none; justify-content: center; text-align: center">
    <p style="font-size: 30px">Check Your Work</p><br>
    <p>On test day, you won't be able to move to next module until the time expires</p>
    <div id="review-content"></div>
    <div class = "btn-review-page-content">
        <div id="submit-review-content"></div>
    </div>
</div>
`;

for (let i = 0; i < quizData.questions.length; i++) {
    const question = quizData.questions[i];
    
    const category_question_type = question.type === 'completion' ? 'Điền vào chỗ trống' :
        question.type === 'multiple-choice' ? 'Chọn 1 đáp án đúng' :
        question.type === 'multi-select' ? 'Chọn nhiều đáp án đúng' : '';

    const module_question = question.question_category || '';

                // Set the updated text back to the element
            const answerBox = question.answer_box || [];

            //let imageSrc = base64Images[i] || ''; // Get Base64 string for the current image

            let imageSrc = question.image || '';

            let contentCheckboxes2 = '';
            contentCheckboxes2 += '<div id="module-2-' + module_question +'" style="display: none;">';
            contentCheckboxes2 += '<div id="name-module-checkbox-2"><b>Current Module: ' + module_question + '</b></div>';
            contentCheckboxes2 += '<div class="icon-detail">';
            contentCheckboxes2 += '<div class="single-detail"><i class="fa-regular fa-flag"></i><span>Current</span></div>';
            contentCheckboxes2 += '<div class="single-detail"><div class="dashed-square"></div><span>Unanswered</span></div>';
            contentCheckboxes2 += '<div class="single-detail"><i class="fa-solid fa-bookmark" style="color: #c33228;"></i><span>For Review</span></div>';
            contentCheckboxes2 += '</div>';
            contentCheckboxes2 += '<div class="checkbox-module-name">';

            for (let j = 0; j < quizData.questions.length; j++) {
                if (quizData.questions[j].question_category === module_question) {
                    contentCheckboxes2 += '<div class="checkbox-container" onclick="ChangeQuestion(' + (j + 1) + ')" id="checkbox-container-2-' + (j + 1) + '">' + (j + 1) + '</div>';
                }
            }

            contentCheckboxes2 += '</div></div>';

                
                
            
            contentQuestions += '<div class="questions" id="question-' + i + '" style="display:none">';

            contentQuestions +='<hr class="horizontal-line">'+
    '     <div class="quiz-section">' +
    '<div class="question-answer-container">' +  
        '<div class="question-side" id="remove_question_side">' +
         '<div class="answer-box-container">';



answerBox.forEach(answer => {
    contentQuestions += `
        <div class="answer-box">${answer}</div>
    `;
});

    contentQuestions += '</div>       <p class="question">'+ ' Câu ' + questionNumber + '<span class="tex2jax_ignore">(ID: ' + question.id_question + ')'+'</span>'+':'+

    '<br>' +(imageSrc ? '<img width="100%" src="' + imageSrc + '" onclick="openModal(\'' + imageSrc + '\')">' : '') +  question.question +
    '</p></div>' +
    
    '<div class="vertical-line"></div>' +  // Vertical line separator
    
    '<div class="answer-side"><div class="answer-list">';

            
            
            openReviewPage = '<div class="section-pageview"> <button class="ctrl-btn ctrl-pageview" id="btn-review" onclick="showReviewPage()">Go to review page</button></div>';
                
                // Đoạn mã HTML để tích hợp nút removeChoiceImage
            contentQuestions += '<div id="tag-report" style="position: relative;">' + '<div id ="questionNumberBox">'+ questionNumber+ '</div>'+
                '<image id="bookmark-question-' + (i + 1) + '" src="' + siteUrl + '/contents/themes/tutorstarter/system-test-toolkit/bookmark_empty.png" class="bookmark-btn" onclick="rememberQuestion(' + (i + 1) + ')"></image>' +
                ' Mark for review ' +
                
                '<image id="removeChoiceImage" onclick="toggleRemoveChoice()" src="' + siteUrl + '/contents/themes/tutorstarter/system-test-toolkit/crossAbc.png" class ="crossing-options" ></image>' +
                '</div>';
                            
            contentQuestions +=`<p style ="font-style: italic">Hướng dẫn:  ${category_question_type}</p>`
            
            if (question.type === 'completion') {
                contentQuestions += '<input type="text" class="input" id="question-' + (i + 1) + '-input" autocomplete="off" placeholder="Nhập câu trả lời của bạn..."><br>' +
                    '<button onclick="showCompletionGuide()">Cách điền số/ đáp án dạng điền</button>' +
                    '<div id="guide_completion" style="display:none">Cách điền số/ đáp án dạng điền</div>' + "<br>" +
                    (answerBox.length ? `<p style="color:red">Nối các ô A,B,C... vào các chỗ trống tương ứng 1,2,3... Đáp án chấp nhận theo mẫu sau: <p style="font-style:italic; font-weight: bold">1A 2B 3C... </p> <p style="color:red"> Lưu ý giữa các ý cách nhau 1 dấu cách. Nhấn nút "Cách điền số/ đáp án dạng điền" để xem thêm minh họa</p></p>` : '');
            } else {
                for (let j = 0; j < question.answer.length; j++) {
                    // Cập nhật HTML cho mỗi lựa chọn
                    contentQuestions += '<div class="answer-container">' +
                    '<div id="answer-question-' + (i + 1) + '-ans-' + (j + 1) + '" class="answer-options neutral ' + question.answer[j][1] + '" style="width: 100%;">' +
                    (question.type === 'multiple-choice' ? 
                        '<input type="radio" name="question-' + (i + 1) + '" id="question-' + (i + 1) + '-' + (j + 1) + '" class="rd-fm">' +
                        '<label data-label="' + getLabelLetter(j) + '" for="question-' + (i + 1) + '-' + (j + 1) + '"><b>' + convertImageTag(formatAWS(question.answer[j][0])) + '</b></label>' :

                        '<input type="checkbox" name="question-' + (i + 1) + '" id="question-' + (i + 1) + '-' + (j + 1) + '" class="cb-fm">' +
                        '<label for="question-' + (i + 1) + '-' + (j + 1) + '"><b>' + formatAWS(question.answer[j][0]) + '</b></label>') +
                    '</div>' +
            
                        '<div class="crossing-zone" id="removeChoiceMainButton-' + (i + 1) + '-ans-' + (j + 1) + '">' +
                            '<div class="cross-label">' + getCrossLabel(j + 1) + '</div>' +
                            '<hr class="cross-btn-line">' +
                        '</div>' +
                    '</div>';
            
                    // Use a timeout to ensure the element exists in the DOM before adding the event listener
                    setTimeout(() => {
                        const crossingZone = document.getElementById('removeChoiceMainButton-' + (i + 1) + '-ans-' + (j + 1));
                        if (crossingZone) {
                            //crossingZone.style.display = 'block'; // Hiển thị phần tử crossing-zone
                            crossingZone.addEventListener('click', function() {
                                const answerDiv = document.getElementById('answer-question-' + (i + 1) + '-ans-' + (j + 1));
                                answerDiv.classList.toggle('active'); // Thêm/xóa class active
                            });
                        }
                    }, 0);
                }
            }
            
              
              
            contentQuestions +=`<br><button onclick="openDraft(${i+1})" style = "display:none" class ="open-draft-button">Quick Draft</button> <textarea class="draft" id="draft-${i+1}"></textarea><br>`

             // Modal functionality
         var modal = document.getElementById("myModal");
         var modalImg = document.getElementById("modalImage");
         var span = document.getElementsByClassName("close-modal")[0];
 
        // Existing code to open the modal
        window.openModal = function(src) {
            modal.style.display = "block";
            modalImg.src = src;
        }

        // Close the modal when the user clicks on <span> (x)
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal when the user clicks anywhere outside of the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        

            if (question.explanation) {
                contentQuestions += '<p class="explain-zone" style="display:none"><b>Giải thích: </b>' + question.explanation + '</p>';
            }
            if (question.section) {
                contentQuestions += '<p class="explain-zone" style="display:none"><b>Section knowledge: </b>' + question.section + '</p>';
            }
            if (question.related_lectures) {
                contentQuestions += '<p class="explain-zone" style="display:none"><b>Related lectures: </b>' + question.related_lectures + '</p>';
            }

            contentQuestions += '</div></div><div class="explain-zone"></div>';
            contentQuestions += '</div></div></div></div>';

            questionNumber++;
            checkboxNumber++;

        }

        document.getElementById('checkboxes-container').innerHTML = contentCheckboxes;



        contentQuestions += '</div></div>';  // Bottom horizontal line


        document.getElementById("quiz-container").innerHTML = contentQuestions;

        document.getElementById("quiz-container").style.display = 'none';
        document.getElementById("center-block").style.display = 'none';
        document.getElementById("title").style.display = 'none';
        //checkboxesContainer.innerHTML = contentCheckboxes;
        reviewPageBtn.innerHTML = openReviewPage;
        


        addEventListenersToInputs();
        
        
   // });

    


    /*setTimeout(function(){
        console.log("Show Test V1");
        startTest();
    }, 1000);*/

    //showLoadingPopup();
    setTimeout(function(){
        console.log("Show Test!");
        document.getElementById("start_test").style.display="block";
        
        document.getElementById("welcome").style.display="block";

    }, 1000);
}
// Hàm lấy câu trả lời dạng A,B,C,D
function getUserAnswer(questionNumber) {
    const questionIndex = questionNumber - 1;
    const question = quizData.questions[questionIndex];
    
    if (question.type === 'completion') {
        const input = document.getElementById(`question-${questionNumber}-input`);
        return input ? input.value : null;
    } else {
        // Kiểm tra các lựa chọn đã chọn và trả về dạng A,B,C,D
        const selectedAnswers = [];
        for (let i = 0; i < question.answer.length; i++) {
            const checkbox = document.getElementById(`question-${questionNumber}-${i+1}`);
            if (checkbox && checkbox.checked) {
                selectedAnswers.push(getLabelLetter(i)); // Sử dụng hàm getLabelLetter để chuyển thành A,B,C,D
            }
        }
        return selectedAnswers.length > 0 ? selectedAnswers : null;
    }
}
function saveProgress() {
    const modal = document.getElementById("questionModal");
    const modalContent = document.getElementById("modalQuestionContent");
    const currentQuestion = currentQuestionIndex + 1;
    
    // Get remaining time
    const time_left = formatTime(countdownValue);
    
    // Count answered questions
    let number_answered = 0;
    let user_answered = {};
    
    // Group answers by category and log them
    quizData.questions.forEach((question, index) => {
        const questionNumber = index + 1;
        const category = question.question_category;
        const answer = getUserAnswer(questionNumber);
        
        if (answer && (answer !== "" && answer !== null && answer !== undefined && (typeof answer !== 'object' || answer.length > 0))) {
            number_answered++;
            
            if (!user_answered[category]) {
                user_answered[category] = {};
            }
            user_answered[category][questionNumber] = answer;
        }
    });
    
    // Tạo detailData chứa time_left và user_answered
    const detailData = {
        time_left: countdownValue, // Thời gian còn lại dạng số giây
        user_answered: user_answered // Câu trả lời của người dùng
    };
    
    console.log("Detail data (JSON):", JSON.stringify(detailData));
    
    var totalQuestions = quizData.questions.length;
    var percentCompleted = Math.floor(number_answered / totalQuestions * 100);

    
      // Lấy thông tin ngày tháng
      const currentDate = new Date();
      const day = String(currentDate.getDate()).padStart(2, '0');
      const month = String(currentDate.getMonth() + 1).padStart(2, '0'); 
      const year = currentDate.getFullYear();
      const hours = String(currentDate.getHours()).padStart(2, '0');
      const minutes = String(currentDate.getMinutes()).padStart(2, '0');
      const seconds = String(currentDate.getSeconds()).padStart(2, '0');

      
      // Tạo progress_id theo định dạng: user_id + id_test + date_element (dạng YYYYMMDD)
      const dateCode = `${year}${month}${day}${hours}${minutes}${seconds}`;
      const progress_id = `${CurrentuserID}${dateCode}`;


    modalContent.innerHTML = `
        <div>
            <p><strong>Loại test:</strong> Digital Sat</p>
            <p><strong>Progress:</strong> ${currentQuestion}/${totalQuestions}</p>
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

    document.getElementById('saveProgressBtn').addEventListener('click', function () {
        const btn = this;
        const resultDiv = document.getElementById('saveResult');
        const deleteSection = document.getElementById('deleteProgressSection');

        btn.disabled = true;
        btn.textContent = 'Đang lưu...';
        resultDiv.innerHTML = '<p>Đang xử lý...</p>';
        
        // Dữ liệu cần gửi
        const data = {
            username: Currentusername,
            id_test: id_test,
            testname: testname,
            progress: currentQuestion,
            percent_completed: percentCompleted,
            type_test: "digitalsat",
            date: new Date().toLocaleString(),
            progress_id: progress_id,
            detail_data: detailData, // Thêm detailData vào đây
            user_answers: user_answered
        };

        // Gửi request đến REST API
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
    
    // Xử lý hiển thị danh sách progress để xóa
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
function showReviewPage() {
    let quizSections = document.querySelectorAll(".quiz-section");
    let reviewPage = document.querySelectorAll(".review-page");
    document.getElementById("prev-button").disabled = true; // Disable nút
    document.getElementById("next-button").disabled = true; // Disable nút

    quizSections.forEach(section => {
        section.style.display = "none";
    });
    reviewPage.forEach(section1 => {
        section1.style.display = "block";
    });

    // Hiển thị contentCheckboxes2 của câu hỏi hiện tại
    const currentQuestion = document.querySelector('.questions[style="display:block"]');
    if (currentQuestion) {
        const currentReviewPage = currentQuestion.querySelector('.review-page');
        if (currentReviewPage) {
            const currentModule = currentReviewPage.querySelector('[id^="module-2-"]');
            if (currentModule) {
                currentModule.style.display = "block";
            }
        }
    }

    // Tạo nút "Switch to next module" hoặc "Submit Test"
    const currentModule = document.getElementById("current_module").textContent.trim();
    const nextModule = getNextModule(currentModule);

    const reviewContent = document.getElementById("submit-review-content");
    reviewContent.innerHTML = `
        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
            ${nextModule ? 
                `<button id="switch-module-button" class = "next-module-review" style="padding: 10px 20px; font-size: 16px;">Switch to next module</button>` :
                `<button id="submit-test-button" class = "next-module-review"  style="padding: 10px 20px; font-size: 16px;">Submit Test</button>`
            }
            <button class="close-review" onclick="closeReviewPage()" style="padding: 10px 20px; font-size: 16px;">Close Review</button>
        </div>
    `;

    if (nextModule) {
        document.getElementById("switch-module-button").onclick = () => switchToNextModule(nextModule);
    } else {
        document.getElementById("submit-test-button").onclick = PreSubmit;
    }
}

// Hàm lấy module tiếp theo
function getNextModule(currentModule) {
    const modules = [...new Set(quizData.questions.map(q => q.question_category))];
    const currentIndex = modules.indexOf(currentModule);
    return currentIndex < modules.length - 1 ? modules[currentIndex + 1] : null;
}

// Hàm chuyển sang module tiếp theo




let moduleTimeRemaining = {};


let partMod = 0;
let orderedModules = [];

function initializeModuleTimers() {
    console.log("Initializing module timers");

    const fixedOrder = [
        "Section 1: Reading And Writing",
        "Section 2: Reading And Writing",
        "Section 1: Math",
        "Section 2: Math"
    ];


    orderedModules = [];
    fixedOrder.forEach(moduleName => {
        if (sectionTimes[moduleName]) {
            orderedModules.push(moduleName);
            moduleTimeRemaining[moduleName] = parseInt(sectionTimes[moduleName]) * 60;
        } else if (quizData.full_test_specific_module && quizData.full_test_specific_module[moduleName]) {
            orderedModules.push(moduleName);
            moduleTimeRemaining[moduleName] = parseInt(quizData.full_test_specific_module[moduleName].time) * 60;
        }
    });

    if (orderedModules.length > 0) {
        const currentModule = orderedModules[partMod];
        countdownValue = moduleTimeRemaining[currentModule];
        document.getElementById('countdown').innerHTML = secondsToHMS(countdownValue);
    }
}

function switchToNextModule() {
    partMod++;
    if (partMod < orderedModules.length) {
        const nextModule = orderedModules[partMod];
        updateModuleTimer(nextModule);

        const firstQuestionIndex = quizData.questions.findIndex(q => q.question_category === nextModule);
        if (firstQuestionIndex !== -1) {
            showQuestion(firstQuestionIndex);
        }

        console.log("Switched to module:", nextModule, "| partMod:", partMod);
       
    } else {
        console.log("All modules finished.");
        autoSubmitTest(); // hoặc hành động khi hết test
    }

}

function updateModuleTimer(currentModule) {
    if (moduleTimeRemaining[currentModule]) {
        countdownValue = moduleTimeRemaining[currentModule];
        document.getElementById('countdown').innerHTML = secondsToHMS(countdownValue);
    }
}



function closeReviewPage() {
    //let reviewPage = document.getElementById("review-page-" + module_question);
    let quizSections = document.querySelectorAll(".quiz-section");
    let reviewPage = document.querySelectorAll(".review-page");
    document.getElementById("prev-button").disabled = false; // Disable nút
    document.getElementById("next-button").disabled = false; // Disable nút

    /*if (reviewPage) {
        reviewPage.style.display = "none";
    }*/
    quizSections.forEach(section => {
        section.style.display = "block";
    });
    reviewPage.forEach(section1 => {
        section1.style.display = "none";
    });
}

function PreSubmit() {
    Swal.fire({
        title: "Bạn có chắc muốn nộp bài ?",
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
                    const timer = Swal.getPopup().querySelector("b");
                    timerInterval = setInterval(() => {}, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                    submitButton();
                    DoingTest = false;
                }
            }).then(async (result) => { // Thêm async ở đây
                if (result.dismiss === Swal.DismissReason.timer) {
                    console.log("Displayed Result");
                    await submitAnswerAndGenerateLink(); // Thêm await
                }
            });
        }
    });
}

async function submitAnswerAndGenerateLink() {
    try {
        // Đảm bảo các hàm này chạy xong trước khi hiển thị thông báo
        await filterAnswerForEachType();
     
        
        // Chỉ hiển thị thông báo sau khi mọi thứ đã hoàn thành
        Swal.fire({
            title: "Kết quả đã có",
            html: "Chúc mừng bạn đã hoàn thành bài thi. Click vào link dưới để nhận kết quả ngay <i class='fa-regular fa-face-smile'></i>",
            allowOutsideClick: false,
            showCancelButton: false,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "Xem kết quả",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `${siteUrl}/digitalsat/result/${resultId}`;
            }
        });
    } catch (error) {
        console.error("Error submitting answers:", error);
        Swal.fire({
            title: "Lỗi",
            text: "Đã có lỗi xảy ra khi nộp bài. Vui lòng thử lại.",
            icon: "error"
        });
    }
}