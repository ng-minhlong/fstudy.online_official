var correct_number = 0;
var incorrect_number = 0;
var skip_number = 0;

let incorrect_skip_answer_array = [];
let correct_answer_array = [];
let test_type_sat 

function submitButton() {
    submitTest = true;
    console.log(submitTest)
    CheckSubmit()
    if (isCountdownRunning) {
        clearInterval(countdownInterval);
        isCountdownRunning = false;
    }
    ChangeQuestion(1);
    showTotalTime();

    var explain_zone = document.getElementsByClassName("explain-zone")

    var timeUsed = quizData.duration - countdownValue;
    var formattedTimeUsed = formatTime(timeUsed);

   // for (var i = 0; i < explain_zone.length; i++)
     //   explain_zone[i].style.display = 'block';

    var percent = 0;

    var number_of_correct_answer = 0;
    var percentage_1_sentence = 100 / quizData.number_questions;

    document.getElementById("quiz-container").style.pointerEvents = "none";
    let correctAnswerDiv = document.getElementById("correctanswerdiv");

 

    // Loop through each question
    for (var z = 1; z <= quizData.number_questions; z++) {
        var id_temp = '[id^="question-' + z + '-"]';
        var elements = document.querySelectorAll(id_temp);

        if (elements.length > 0 && elements[0].type === 'text') {
            var userAnswer = document.getElementById('question-' + z + '-input').value.trim();
           // var correctAnswer = quizData.questions[z - 1].answer.map(a => a.toLowerCase());
            var correctAnswer = quizData.questions[z - 1].answer;

            if (userAnswer === "") {
                skip_number++;
                incorrect_skip_answer_array.push(z);
            } else if (correctAnswer.includes(userAnswer.toLowerCase())) {
                percent += percentage_1_sentence;
                correct_number++;
                correct_answer_array.push(z);
                document.getElementById('question-' + z + '-input').classList.add('true');
            } else {
                incorrect_number++;
                incorrect_skip_answer_array.push(z);
                document.getElementById('question-' + z + '-input').classList.add('false');
                var explanationZone = document.querySelector('#question-' + z + '-input').closest('.questions').querySelector('.explain-zone');
                explanationZone.innerHTML = '<p><b>Correct Answer: </b>' + correctAnswer.join(', ') + '</p>' + explanationZone.innerHTML;
            }
        } else if (elements.length > 0 && elements[0].type === 'radio') {
            var correctOptions = [];
            var isAnswered = false;
            var isCorrect = false; 

            for (var i = 0; i < elements.length; i++) {
                if (elements[i].parentElement.classList.contains('true')) {
                    correctOptions.push(elements[i].parentElement.textContent.trim().charAt(0));
                }

                if (elements[i].checked) {
                    isAnswered = true;
                    if (elements[i].parentElement.classList.contains('true')) {
                        isCorrect = true;
                        correct_number++;
                        correct_answer_array.push(z);
                        percent += percentage_1_sentence;
                    } else {
                        incorrect_number++;
                        incorrect_skip_answer_array.push(z);
                    }
                }
            }

            if (!isAnswered) {
                skip_number++;
                incorrect_skip_answer_array.push(z);
            } 
            
            
        } else if (elements.length > 0 && elements[0].type === 'checkbox') {
            var correctOptions = [];
            var selectedCorrectly = true;
            var isAnswered = false;

            for (var i = 0; i < elements.length; i++) {
                if (elements[i].parentElement.classList.contains('true')) {
                    correctOptions.push(String.fromCharCode(65 + i));
                }

                if (elements[i].checked) {
                    isAnswered = true;
                    if (!elements[i].parentElement.classList.contains('true')) {
                        selectedCorrectly = false;
                    }
                } else if (elements[i].parentElement.classList.contains('true')) {
                    selectedCorrectly = false;
                }
            }

            if (!isAnswered) {
                skip_number++;
                incorrect_skip_answer_array.push(z);
            } else if (isAnswered && selectedCorrectly) {
                correct_number++;
                correct_answer_array.push(z);
                percent += percentage_1_sentence;
            } else if (isAnswered && !selectedCorrectly) {
                incorrect_number++;
                incorrect_skip_answer_array.push(z);
            }
            
            
        }
    }

    console.log(`Correct: ${correct_number}, Incorrect: ${incorrect_number}, Skipped: ${skip_number}`);
    

    test_type_sat = document.getElementById("testtype").innerText;
    percent = formatNumber(percent);

    let full_test_res = (number_of_correct_answer/quizData.number_questions)*1600;
    console.log(`Test type hiện tai: ${test_type_sat}`);
    string_final = "Your score: " + percent + "%. ";
    string_final_out_of_10 = `${number_of_correct_answer}/${quizData.number_questions}`;

    
    correctPercentage = percent + "%. ";
    if(test_type_sat == "Practice"){
        string_final =  percent + "%. ";
    }
    else if(test_type_sat == "Full Test"){
        string_final = `${full_test_res}`;

    }
    time_do_full_test = formattedTimeUsed;

    

    document.getElementById("final-result").innerHTML = string_final;



    document.getElementById("final-review-result").innerHTML = string_final_out_of_10;
    document.getElementById("final-review-result-table").innerHTML = string_final_out_of_10;
    document.getElementById("final-result-table").innerHTML = string_final;

    document.getElementById("time-result").innerHTML = time_do_full_test;
    document.getElementById("time-result-table").innerHTML = time_do_full_test;
   // document.getElementById("header-table-test-result").style.display = 'block';

    document.getElementById('final-result').scrollIntoView({
        behavior: 'smooth'
});
}
const answerTypeArray = []; // Tạo mảng để lưu kết quả dưới dạng JSON



async function filterAnswerForEachType() {
    console.log(`Incorrect/Skip array: ${incorrect_skip_answer_array}`);
    console.log(`Correct array: ${correct_answer_array}`);
    

    for (let i = 0; i < quizData.questions.length; i++) {
        const questionType = quizData.questions[i].category;
        const questionObject = {};
        questionObject[i + 1] = questionType; // Lưu dạng { "question_number": "category" }
        answerTypeArray.push(questionObject);
    }

    console.log("JSON Filter các loại:", JSON.stringify(answerTypeArray, null, 4)); // Hiển thị JSON dưới dạng chuỗi
    await analyzeAnswersByType();

}


async function analyzeAnswersByType() {
    const analysis = {};

    answerTypeArray.forEach((item) => {
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

    var link = "http://localhost/wp-admin/admin-ajax.php";

    // Gửi dữ liệu tới WordPress qua AJAX
    
    jQuery.ajax({
        url: link, // Sử dụng URL đã chèn từ PHP
        method: 'POST',
        data: {
            action: 'save_analysis_digital_sat_result',
            analysisData: analysis,
        },
        success: function(response) {
            if (response.success) {
                //alert('Dữ liệu đã được lưu thành công!');
                console.log('Dữ liệu đã được lưu thành công!')
            } else {
               // alert('Lỗi: ' + response.data);
                console.log('Lỗi: ' + response.data)
            }
        },
        error: function() {
            alert('Lỗi kết nối tới server');
        }
    });
    

    return analysis;
}


 function formatNumber(number)
  {
            let roundedNumber = parseFloat(number).toFixed(2);
            let resultString = roundedNumber.toString();
            resultString = resultString.replace(/\.?0+$/, '');
            return resultString;
 }

          



 async function  ResultInput() {
    console.log(`${saveSpecificTime}`);

    // Copy the content to the form fields
    var contentToCopy1 = document.getElementById("final-result").textContent;
    var contentToCopy2 = document.getElementById("date").textContent;
    var contentToCopy3 = document.getElementById("time-result").textContent;
    var contentToCopy4 = document.getElementById("title").textContent;
    var contentToCopy6 = document.getElementById("id_test").textContent;
    var contentToCopy7 = document.getElementById("correctanswerdiv").textContent;
    var contentToCopy8 = document.getElementById("useranswerdiv").textContent;


    document.getElementById("resulttest").value = contentToCopy1;
    document.getElementById("testsavenumber").value = resultId;
    document.getElementById("correct_percentage").value = correctPercentage;
    document.getElementById("test_type").value = test_type_sat;

    document.getElementById("correct_number").value = correct_number;
    document.getElementById("incorrect_number").value = incorrect_number;
    document.getElementById("skip_number").value = skip_number;
    document.getElementById("total_question_number").value = quizData.number_questions;

    document.getElementById("dateform").value = contentToCopy2;
    document.getElementById("timedotest").value = contentToCopy3;
    document.getElementById("testname").value = contentToCopy4;
    document.getElementById("idtest").value = contentToCopy6;
    document.getElementById("useranswer").value = contentToCopy8;
    
    document.getElementById("save_specific_time").value = saveSpecificTime;

    
  // Add a delay before submitting the form
  
setTimeout(function() {
// Automatically submit the form
jQuery('#frmContactUs').submit();
}, 1000); // 5000 milliseconds = 5 seconds 

}
          
          