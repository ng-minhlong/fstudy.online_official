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

    document.getElementById("submit-button").style.display = 'none';
    var explain_zone = document.getElementsByClassName("explain-zone")

    var timeUsed = quizData.duration - countdownValue;
    var formattedTimeUsed = formatTime(timeUsed);

   for (var i = 0; i < explain_zone.length; i++)
        explain_zone[i].style.display = 'block';

    var percent = 0;

    var number_of_correct_answer = 0;
    var percentage_1_sentence = 100 / quizData.number_questions;

    //document.getElementById("quiz-container").style.pointerEvents = "none";
    let correctAnswerDiv = document.getElementById("correctanswerdiv");

 

    // Loop through each question
    for (var z = 1; z <= quizData.number_questions; z++) {
        var id_temp = '[id^="question-' + z + '-"]';
        var elements = document.querySelectorAll(id_temp);

        if (elements.length > 0 && elements[0].type === 'text') {
            var userAnswer = document.getElementById('question-' + z + '-input').value.trim();

            var answerRaw = quizData.questions[z - 1].answer;
            var correctAnswer = [];

            if (Array.isArray(answerRaw)) {
                correctAnswer = answerRaw.map(a => typeof a === 'string' ? a.toLowerCase() : String(a).toLowerCase());
            } else if (typeof answerRaw === 'string') {
                correctAnswer = [answerRaw.toLowerCase()];
            } else if (answerRaw != null) {
                correctAnswer = [String(answerRaw).toLowerCase()];
            }

            if (userAnswer === "") {
                skip_number++;
                incorrect_skip_answer_array.push(z);
            } else if (correctAnswer.includes(userAnswer.toLowerCase())) {
                percent += percentage_1_sentence;
                correct_number++;
                correct_answer_array.push(z);
                document.getElementById('question-' + z + '-input').classList.add('true');
                document.getElementById('question-' + z + '-input').classList.remove("neutral");

            } else {
                incorrect_number++;
                incorrect_skip_answer_array.push(z);
                document.getElementById('question-' + z + '-input').classList.add('false');
                document.getElementById('question-' + z + '-input').classList.remove("neutral");

                var explanationZone = document.querySelector('#question-' + z + '-input').closest('.questions').querySelector('.explain-zone');
                explanationZone.innerHTML = '<p><b>Correct Answer: </b>' + correctAnswer.join(', ') + '</p>' + explanationZone.innerHTML;
            }
        } else if (elements.length > 0 && elements[0].type === 'radio') {
            var correctOptions = [];
            var isAnswered = false;
            var isCorrect = false;           


            for (var i = 0; i < elements.length; i++) {
                if (elements[i].parentElement.classList.contains('true')) {
                    elements[i].parentElement.classList.remove('neutral')
                    correctOptions.push(elements[i].parentElement.textContent.trim().charAt(0));
                }

                if (elements[i].checked) {
                    isAnswered = true;
                    if (elements[i].parentElement.classList.contains('true')) {
                        elements[i].parentElement.classList.remove('neutral')

                        isCorrect = true;
                        correct_number++;
                        correct_answer_array.push(z);
                        percent += percentage_1_sentence;
                    } else {
                        elements[i].parentElement.classList.remove('neutral')

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
                    elements[i].parentElement.classList.remove('neutral')

                    correctOptions.push(String.fromCharCode(65 + i));
                }

                if (elements[i].checked) {
                    isAnswered = true;
                    if (!elements[i].parentElement.classList.contains('true')) {
                        elements[i].parentElement.classList.remove('neutral')

                        selectedCorrectly = false;
                    }
                } else if (elements[i].parentElement.classList.contains('true')) {
                    elements[i].parentElement.classList.remove('neutral')

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







 function formatNumber(number)
  {
            let roundedNumber = parseFloat(number).toFixed(2);
            let resultString = roundedNumber.toString();
            resultString = resultString.replace(/\.?0+$/, '');
            return resultString;
 }

          


