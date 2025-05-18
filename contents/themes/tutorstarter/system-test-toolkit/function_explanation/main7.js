
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

function hideCorrectAns(){
    var explain_zone = document.getElementsByClassName("explain-zone");
    var answer_explanation = document.getElementsByClassName("answer_explanation");
    var true_ans = document.querySelectorAll(".answer-options"); // Chọn tất cả, không chỉ .true

    function toggleElements(elements) {
        for (var i = 0; i < elements.length; i++) {
            elements[i].style.display = (elements[i].style.display === 'none') ? '' : 'none';
        }
    }

    function toggleClass(elements, className) {
        elements.forEach(element => {
            if (element.classList.contains(className)) {
                element.classList.remove(className);
            } else {
                element.classList.add(className);
            }
        });
    }

    toggleElements(explain_zone);
    toggleElements(answer_explanation);
    toggleClass([...true_ans], "true"); // Dùng tất cả answer-choice để đảm bảo class có thể thêm lại
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



    var contentCheckboxes = "";
    let contentQuestions = "";
    let currentCategory = "";
    let currentCheckboxCategory = "";
    let questionNumber = 1;
    let checkboxNumber = 1;

    // Create an array of promises to wait for all images to be encoded
    let encodingPromises = quizData.questions.map((question, index) => {
        if (question.image) {
            return encodeImage(question.image, index); // Encode each image
        }
        return Promise.resolve(); // No image to encode
    });

    Promise.all(encodingPromises).then(() => {
        // Ensure this is within the loop where questions are being processed
        
        

        
        contentQuestions +=`   <div class="navigation-group"> <div class="left-group"><b id="current_module" style="display: none;"></b></div>`;
            
       
        
        
        contentQuestions +=`<div class="right-group"><button  id="right-main-button" onclick = 'openCalculator()'>  <span class="icon-text-wrapper">
 <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-calculator" viewBox="0 0 16 16">
  <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/><path d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
</svg> Calculator </span></button>  

<button  id="right-main-button" onclick = 'openDraftPopup()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-plus" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
</svg>
Note</span></button>  

<button  id="right-main-button" onclick = 'hideCorrectAns()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-plus" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 5.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 .5-.5"/>
  <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
  <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
</svg>
Hide Correct Ans and Explanation</span></button>  



<button  id="right-main-button" onclick = 'openSettingPopup()'>  <span class="icon-text-wrapper"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
  <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
  <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
</svg>
Setting </span></button>  


</div></div><div id="time-personalize-div"></div>`;

        for (let i = 0; i < quizData.questions.length; i++) {
            
            const question = quizData.questions[i];

            const category_question_type = question.type === 'completion' ? 'Điền vào chỗ trống' :
                question.type === 'multiple-choice' ? 'Chọn 1 đáp án đúng' :
                question.type === 'multi-select' ? 'Chọn nhiều đáp án đúng' : '';

                


    
                const module_question = question.question_category || '';
                
    
                if (quizData.restart_question_number_for_each_question_catagory == "Yes") {
                    if (currentCategory !== module_question) {
                        currentCategory = module_question;


                        questionNumber = 1;
                        // Đặt lại checkboxNumber khi module thay đổi
                        checkboxNumber = 1;
                       
        
                        // Thêm tên module vào trước checkbox khi thay đổi module
                        if (currentCheckboxCategory !== module_question) {
                            currentCheckboxCategory = module_question;

                        

                            contentCheckboxes += '<div class="checkbox-module-name">' + module_question;
                            //contentQuestions += '<div class="module-intro">This is module ' + module_question + '</div>';

        
                        }
                    }

                    
                } 
                
                else {
                    questionNumber = i + 1;
                    
                }
               
                
                


           

                // Set the updated text back to the element
            const answerBox = question.answer_box || [];

            let imageSrc = base64Images[i] || ''; // Get Base64 string for the current image
            


            //'<h4>' + module_question + '</h4>' +
            contentQuestions += '<div class="questions" id="question-' + i + '" style="display:none">';
            
            
            contentQuestions +='<hr class="horizontal-line">'+
    '<div class="quiz-section">' +  // Updated section wrapper
       // Top horizontal line
    '<div class="question-answer-container">' +  // Container for questions and answers
        '<div class="question-side" id="remove_question_side">' +
         '<div class="answer-box-container">';

answerBox.forEach(answer => {
    contentQuestions += `
        <div class="answer-box">${answer}</div>
    `;
});

    contentQuestions += '</div><p class="question">'+ ' Câu ' + questionNumber + '<span class="tex2jax_ignore">(ID: ' + question.id_question + ')</span>'+':'+

    '<br>' +(imageSrc ? '<img width="100%" src="' + imageSrc + '" onclick="openModal(\'' + imageSrc + '\')">' : '') +  question.question +
    '</p></div>' +
    
    '<div class="vertical-line"></div>' +  // Vertical line separator
    
    '<div class="answer-side"><div class="answer-list">';

            contentCheckboxes += '<div class="checkbox-container" onclick="ChangeQuestion(' + (i + 1) + ')" id="checkbox-container-' + (i + 1) + '">' +
                module_question + ' ' + checkboxNumber + '</div>';
                
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
        
            contentQuestions += '<div class = "answer_explanation"> <b>Correct Answer: </b>' + question.correct_ans + '</p>';
            contentQuestions += '<b>Your Answer: </b>' + question.user_answer + '</p> </div>';

            if (question.explanation) {
                let explanation = question.explanation.replace(/\n/g, ' ').replace(/<br\s*\/?>/g, ' ');
                contentQuestions += '<p class="explain-zone" style="display:none"><b>Giải thích: </b>' + explanation + '</p>';
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
        contentQuestions += '</div></div>';  // Bottom horizontal line

        contentCheckboxes += '</div>'; // Đóng div cho module cuối cùng

        document.getElementById("quiz-container").innerHTML = contentQuestions;

        document.getElementById("quiz-container").style.display = 'none';
        document.getElementById("center-block").style.display = 'none';
        document.getElementById("title").style.display = 'none';
        checkboxesContainer.innerHTML = contentCheckboxes;
        


        addEventListenersToInputs();
    });

    setTimeout(function(){
        console.log("Show Test!");
        document.getElementById("start_test").style.display="block";
        
        startTest();
    }, 1000);
}

function PreSubmit(){

    Swal.fire({
            title: "Bạn có chắc muốn nộp bài ?",
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                  cancelButtonColor: "#d33",
                  confirmButtonText: "Nộp bài ngay",
                  cancelButtonText:"Hủy"
                }).then((result) => {
                  if (result.isConfirmed) {
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
                timerInterval = setInterval(() => {
                  
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
                submitButton();
                DoingTest = false;

              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                console.log("Displayed Result");
                 //ResultInput();
                 submitAnswerAndGenerateLink();

              }
            });
            }

                  }
                });


                




           
          }


          async function submitAnswerAndGenerateLink() {
            await filterAnswerForEachType();
            await ResultInput();
          
            Swal.fire({
              title: "Kết quả đã có",
              html: "Chúc mừng bạn đã hoàn thành bài thi. Click vào link dưới để nhận kết quả ngay <i class='fa-regular fa-face-smile'></i>",
              allowOutsideClick: false,
              showCancelButton: false,
              confirmButtonColor: "#3085d6",
              confirmButtonText: "Xem kết quả",
            }).then((result) => {
              if (result.isConfirmed) {
                // Chuyển đến link chứa resultId
                //const resultId = 123; // Thay bằng giá trị resultId thực tế
                window.location.href = `${siteUrl}/digitalsat/result/${resultId}`;
              }
            });
          }
          
          