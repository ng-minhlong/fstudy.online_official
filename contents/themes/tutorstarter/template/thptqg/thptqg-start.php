<?php
/*
 * Template Name: Doing Template Speaking
 * Template Post Type: thptqg
 
 */


if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();

    $custom_number = get_query_var('id_test');
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;

  // Database credentials
  $servername = DB_HOST;
  $username = DB_USER;
  $password = DB_PASSWORD;
  $dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


 // Get current time (hour, minute, second)
 $hour = date('H'); // Giờ
 $minute = date('i'); // Phút
 $second = date('s'); // Giây


 function generate_uuid_v4() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),         // 32 bits
        mt_rand(0, 0xffff),                             // 16 bits
        mt_rand(0, 0x0fff) | 0x4000,                    // 16 bits, version 4
        mt_rand(0, 0x3fff) | 0x8000,                    // 16 bits, variant
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)  // 48 bits
    );
}

$result_id = generate_uuid_v4();
$site_url = get_site_url();



echo "<script> 
var resultId = '" . $result_id ."';
var siteUrl = '" .$site_url . "';
var id_test = '" . $id_test . "';
console.log('Result ID: ' + resultId);
</script>";


$sql_test = "SELECT * FROM thptqg_question WHERE id_test = ?";


// Query to fetch token details for the current username
$sql2 = "SELECT token, token_use_history 
         FROM user_token 
         WHERE username = ?";



// Use prepared statements to execute the query
$stmt_test = $conn->prepare($sql_test);
$id_test = $custom_number;
$stmt_test->bind_param("s", $id_test);
$stmt_test->execute();

// Get the result
$result_test = $stmt_test->get_result();
if ($result_test->num_rows > 0) {
    $data = $result_test->fetch_assoc();

    $testname = $data['testname']; // Fetch the testname field
    $testcode = $data['testcode']; // Fetch the testcode field
    $answer = $data['answer']; // Fetch the answer field
    $subject = $data['subject']; // Fetch the subject field
    $year = $data['year']; // Fetch the year field
    $time = "2"; // Fetch the time field
    $token_need = $data['token_need'];
    $time_allow = $data['time_allow'];
    $permissive_management = $data['permissive_management'];


    add_filter('document_title_parts', function ($title) use ($testname) {
      $title['title'] = $testname; // Use the $testname variable from the outer scope
      return $title;
  });
  echo '<script>var subject = "' . $subject .'";
  </script>';
  

get_header(); // Gọi phần đầu trang (header.php)



$stmt2 = $conn->prepare($sql2);
if (!$stmt2) {
    die("Error preparing statement 2: " . $conn->error);
}

$stmt2->bind_param("s", $current_username);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows > 0) {
    $token_data = $result2->fetch_assoc();
    $token = $token_data['token'];
    $token_use_history = $token_data['token_use_history'];

    echo "<script>//console.log('Token: $token, Token Use History: $token_use_history, Mày tên: $current_username');</script>";
   

} else {
    echo "Lỗi đề thi";
    
}


  
        $permissiveManagement = json_decode($permissive_management, true);
        
        // Chuyển mảng PHP thành JSON string để có thể in trong console.log
        echo "<script> 
                console.log('$permissive_management');
            </script>";
        
        
        $foundUser = null;
        if (!empty($permissiveManagement)) {
            foreach ($permissiveManagement as $entry) {
                if ($entry['username'] === $current_username) {
                    $foundUser = $entry;
                    break;
                }
            }
        }
    
        $premium_test = "False"; // Default value
        if ($foundUser != null && $foundUser['time_left'] > 0 || $token_need == 0) {
            if ($token_need > 0) {
                $premium_test = "True";
            }
        
        
            echo '<script>
            let premium_test = "' . $premium_test . '";
            let token_need = "' . $token_need . '";
            let change_content = "' . $testname . '";
            let time_left = "' . (isset($foundUser['time_left']) ? $foundUser['time_left'] : 10) . '";
        </script>';
        





// Đóng kết nối
$conn->close();

?>
<html lang="vi">
    <head>
      
       
    </head>
    <body>
        <style type="text/css">
         

            .answer {
                background: #FEFCF2;
                padding: 10px;
                border-radius: 10px;
                margin-top: 10px;
            }

            .question img {
                max-width: 100%!important;
                height: auto;
            }

            #main-answer .ask img, #main-answer .answer img {
                max-height: 20px;
            }

            img[data-filename], img[width], img[height], img[alt] {
                max-width: 100%!important;
                height: auto!important;
                max-height: unset!important;
            }

            img {
                display: inline !important
            }

            .choice-ans div {
                margin: 5px;
            }

            #ans-table .num {
                font-family: Arial, sans-serif;
                font-size: 13px;
                padding: 0!important;
                border: 1px solid #fff;
                margin: 0!important;
                background: #C8E6C9;
                text-align: center;
                line-height: 26px;
                font-weight: bold;
            }

            .bg-primary {
                background-color: #0D47A1!important;
                color: #fff!important;
            }

            .text-primary {
                color: #1565C0!important;
            }

            @media (max-width: 768px) {
                .hidden-mb {
                    display: none;
                }
            }

            
            #main-answer {
                font-family: Arial, sans-serif;
            }

            #sidebar1 {
                flex: 1;
                padding: 10px;
                border-left: 2px solid #ccc;
                height: 100%;
                overflow-y: auto;
            }
            #quiz-container1 {
                overflow-y: auto;
                flex: 3;
                padding-left: 100px;
                height: 100%;
            }
            .question-wrapper, .context-wrapper {
                display: none;
            }
            .active {
                display: block;
            }
            .box-answer {
                cursor: pointer;
                width: calc(20% - 10px);
                height: 40px;
                border: 1px solid black;
                margin-bottom: 10px;
                display: inline-block;
                box-sizing: border-box;
                padding: 15px;
            }
            .container-content {
                height: 500px;
                padding: 20px 0px 10px 0px;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                min-height: 100vh;
            }

            .container-checkbox {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                max-width: 200px;
                margin-top: 20px;
            }

            .checkbox-box {
                width: 40px;
                height: 40px;
                border: 2px solid black;
                display: flex;
                justify-content: center;
                align-items: center;
                font-weight: bold;
                cursor: pointer;
                user-select: none;
            }
            .col-md-6{
                background-color: white !important;
                border-radius: 10px  !important;
                border-color: grey !important;
            }

            .selected-choice {
                background-color: #d4edda !important;
                border-color: #28a745 !important;
            }
            .checkbox-box.answered {
                background-color: #d4edda;
                border-color: #28a745;
                color: #155724;
            }
        </style>
    
   
            <div class="container-content">

                

                
                <div class="row" id = "quiz-container1">
                     <?php echo $testcode ?>
                </div>
                <div id="sidebar1">
                    <h3>Answers</h3>
                    <div id = "timer"></div>
                    <button id="logButton"
                        style=" padding: 10px 20px;
                            background-color: #007bff; color: white; border: none;
                            border-radius: 4px; cursor: pointer; z-index: 1000;">
                        Nộp bài
                    </button>
                    <div id="boxanswers"></div>
                    <div class = "container-checkbox" id = "container-checkbox"></div>
                   
                </div></div>
                        
            </div>
        
        
       
    
        <script>

    document.addEventListener('DOMContentLoaded', function() {
        function logQuestionsInfo() {
    const questions = document.querySelectorAll('.question');
    const totalQuestions = questions.length;
    const questionIds = Array.from(questions).map((_, index) => index + 1);

    questions.forEach((question, index) => {
        question.setAttribute('q-id', questionIds[index]);
    });

    return { totalQuestions, questionIds };
}

function createCheckboxes() {
    const { questionIds } = logQuestionsInfo();
    const container = document.getElementById('container-checkbox');

    questionIds.forEach(id => {
        const box = document.createElement('div');
        box.className = 'checkbox-box';
        box.textContent = id;

        box.addEventListener('click', () => {
            const targetQuestion = document.querySelector(`.question[q-id="${id}"]`);
            if (targetQuestion) {
                targetQuestion.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });


        container.appendChild(box);
    });
}

            // Get all questions and initialize variables
            const questions = document.querySelectorAll('.question');
            // Thêm q-id vào mỗi câu hỏi khi khởi tạo
            questions.forEach((question, index) => {
                question.setAttribute('q-id', index + 1);
            });
            const totalQuestions = questions.length;
            let currentQuestionIndex = 0;
            let singlePageMode = true;
            
            // Create navigation controls
            const navControls = document.createElement('div');
            navControls.className = 'navigation-controls';
            navControls.style.display = 'none';
            navControls.style.justifyContent = 'space-between';
            navControls.style.margin = '20px 0';
            navControls.style.padding = '10px';
            navControls.style.backgroundColor = '#f5f5f5';
            navControls.style.borderRadius = '5px';
            
            // Create header controls
            const headerControls = document.createElement('div');
            headerControls.className = 'header-controls';
            headerControls.style.display = 'flex';
            headerControls.style.justifyContent = 'center';
            headerControls.style.gap = '10px';
            headerControls.style.marginBottom = '20px';
            
            // Create toggle all answers button
            const toggleAllBtn = document.createElement('button');
            toggleAllBtn.textContent = 'Ẩn/Hiện tất cả đáp án';
            toggleAllBtn.className = 'btn btn-secondary';
            toggleAllBtn.style.padding = '5px 10px';
            toggleAllBtn.style.cursor = 'pointer';
            
            // Create view mode toggle button
            const viewModeBtn = document.createElement('button');
            viewModeBtn.textContent = 'Xem từng câu hỏi';
            viewModeBtn.className = 'btn btn-primary';
            viewModeBtn.style.padding = '5px 10px';
            viewModeBtn.style.cursor = 'pointer';
            
            // Add buttons to header
            //headerControls.appendChild(toggleAllBtn);
            //headerControls.appendChild(viewModeBtn);
            
            // Insert header controls before the first question
            document.querySelector('#main-answer').prepend(headerControls);
            
            // Create navigation buttons
            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Câu trước';
            prevBtn.className = 'btn btn-primary';
            prevBtn.style.padding = '5px 15px';
            prevBtn.style.cursor = 'pointer';
            
            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Câu tiếp';
            nextBtn.className = 'btn btn-primary';
            nextBtn.style.padding = '5px 15px';
            nextBtn.style.cursor = 'pointer';
            
            const questionCounter = document.createElement('span');
            questionCounter.textContent = `Câu ${currentQuestionIndex + 1}/${totalQuestions}`;
            questionCounter.style.alignSelf = 'center';
            
            // Add navigation buttons to controls
            navControls.appendChild(prevBtn);
            navControls.appendChild(questionCounter);
            navControls.appendChild(nextBtn);
            
            // Insert navigation controls after the first question
            questions[0].parentNode.insertBefore(navControls, questions[0].nextSibling);

            function updateCheckboxState(qid) {
                const checkbox = document.querySelector(`.checkbox-box:nth-child(${qid})`);
                if (!checkbox) return;

                let isAnswered = false;

                const key = `q${qid}`;

                if (userChoices.part1.hasOwnProperty(key)) {
                    isAnswered = userChoices.part1[key] !== null;
                }

                if (userChoices.part2.hasOwnProperty(key)) {
                    const answers = Object.values(userChoices.part2[key]);
                    isAnswered = answers.length === 4 && answers.every(val => val === 'true' || val === 'false');
                }

                if (userChoices.part3.hasOwnProperty(key)) {
                    isAnswered = userChoices.part3[key].trim() !== '';
                }

                checkbox.classList.toggle('answered', isAnswered);
            }

            
            // Function to show a specific question
            function showQuestion(index) {
                questions.forEach((q, i) => {
                    if (singlePageMode) {
                        q.style.display = 'block';
                    } else {
                        q.style.display = i === index ? 'block' : 'none';
                    }
                });
                
                questionCounter.textContent = `Câu ${index + 1}/${totalQuestions}`;
                currentQuestionIndex = index;
                
                // Disable/enable navigation buttons
                prevBtn.disabled = index === 0;
                nextBtn.disabled = index === totalQuestions - 1;
            }
            
            // Function to toggle all answers
            function toggleAllAnswers() {
                const answers = document.querySelectorAll('.answer');
                const firstAnswer = answers[0];
                const isHidden = firstAnswer.style.display === 'none';
                
                answers.forEach(answer => {
                    answer.style.display = isHidden ? 'block' : 'none';
                });
                
                toggleAllBtn.textContent = isHidden ? 'Ẩn tất cả đáp án' : 'Hiện tất cả đáp án';
            }
            
            // Function to toggle view mode
            function toggleViewMode() {
                singlePageMode = !singlePageMode;
                
                if (singlePageMode) {
                    // Show all questions
                    questions.forEach(q => q.style.display = 'block');
                    viewModeBtn.textContent = 'Xem từng câu hỏi';
                    navControls.style.display = 'none';
                } else {
                    // Show current question only
                    showQuestion(currentQuestionIndex);
                    viewModeBtn.textContent = 'Xem tất cả câu hỏi';
                    navControls.style.display = 'flex';
                }
            }
            
            // Event listeners
            prevBtn.addEventListener('click', () => {
                if (currentQuestionIndex > 0) {
                    showQuestion(currentQuestionIndex - 1);
                }
            });
            
            nextBtn.addEventListener('click', () => {
                if (currentQuestionIndex < totalQuestions - 1) {
                    showQuestion(currentQuestionIndex + 1);
                }
            });
            
            toggleAllBtn.addEventListener('click', toggleAllAnswers);
            viewModeBtn.addEventListener('click', toggleViewMode);
            
            // Initialize
            
            
            // Hide all answers initially
            document.querySelectorAll('.answer').forEach(answer => {
                answer.style.display = 'none';
            });
            const allParts = document.querySelectorAll('.question-part');

//let currentPartIndex = -1;
const userChoices = {
                part1: {},
                part2: {},
                part3: {}
            };
allParts.forEach((part) => {
   

    const questions = part.querySelectorAll('.question');

     questions.forEach((question) => {
        const qid = parseInt(question.getAttribute('q-id'), 10);
        let currentPartIndex = -1;

        if (subject === "Toán học") {
            if (qid >= 1 && qid <= 12) {
                currentPartIndex = 0;
            } else if (qid >= 13 && qid <= 16) {
                currentPartIndex = 1;
            } else if (qid >= 17 && qid <= 22) {
                currentPartIndex = 2;
            }
        } else if (subject === "Hóa học" || subject === "Sinh học" || subject === "Địa lý") {
            if (qid >= 1 && qid <= 18) {
                currentPartIndex = 0;
            } else if (qid >= 19 && qid <= 22) {
                currentPartIndex = 1;
            } else if (qid >= 23 && qid <= 28) {
                currentPartIndex = 2;
            }
        } else if (subject === "Tiếng anh") {
            if (qid >= 1 && qid <= 40) {
                currentPartIndex = 0;
            }
        }

    if (currentPartIndex === 0) {
        userChoices.part1[`q${qid}`] = null;
        // PHẦN I - Trắc nghiệm
            const choices = question.querySelectorAll('.choice-ans .col-md-6');
            if (choices.length === 0) {
                console.error('Không tìm thấy lựa chọn cho câu hỏi', question);
                return;
            }

            choices.forEach((choice, cIndex) => {
                choice.style.cursor = 'pointer';
                choice.style.padding = '8px';
                choice.style.margin = '4px';
                choice.style.border = '1px solid #ddd';
                choice.style.borderRadius = '4px';
                choice.style.backgroundColor = '#f9f9f9';

                choice.addEventListener('click', () => {
                    choices.forEach(c => {
                        c.style.backgroundColor = '#f9f9f9';
                        c.style.borderColor = '#ddd';
                    });

                    choices.forEach(c => c.classList.remove('selected-choice'));
                    choice.classList.add('selected-choice');


                    const option = String.fromCharCode(65 + cIndex);
                    userChoices.part1[`q${qid}`] = option;
                    updateCheckboxState(qid);

                });
                
        
        });
    } else if (currentPartIndex === 1) {
            userChoices.part2[`q${qid}`] = {
            a: null,
            b: null,
            c: null,
            d: null
        };

        // PHẦN II - Đúng/Sai
            const askDiv = question.querySelector('.ask');
            if (!askDiv) {
                console.error('Không tìm thấy phần ask cho câu hỏi', question);
                return;
            }

            if (askDiv.querySelector('.options-container')) return;

            const optionsContainer = document.createElement('div');
            optionsContainer.className = 'options-container';
            optionsContainer.style.marginTop = '10px';
            optionsContainer.style.padding = '10px';
            optionsContainer.style.borderTop = '1px dashed #ccc';

            for (let i = 0; i < 4; i++) {
                const optionDiv = document.createElement('div');
                optionDiv.style.marginBottom = '8px';

                const label = document.createElement('span');
                label.textContent = `${String.fromCharCode(97 + i)}) `;
                label.style.marginRight = '8px';

                const select = document.createElement('select');
                select.className = 'form-control';
                select.style.width = '120px';
                select.style.display = 'inline-block';

                ['-- Chọn --', 'Đúng', 'Sai'].forEach((text, idx) => {
                    const option = document.createElement('option');
                    option.value = idx === 0 ? '' : idx === 1 ? 'true' : 'false';
                    option.textContent = text;
                    select.appendChild(option);
                });

                select.addEventListener('change', (e) => {
                    if (!userChoices.part2[`q${qid}`]) {  // Sửa qIndex thành qid
                        userChoices.part2[`q${qid}`] = {};  // Sửa qIndex thành qid
                    }
                    userChoices.part2[`q${qid}`][String.fromCharCode(97 + i)] = e.target.value;  // Sửa qIndex thành qid
                    updateCheckboxState(qid);
                });
                

                optionDiv.appendChild(label);
                optionDiv.appendChild(select);
                optionsContainer.appendChild(optionDiv);
            }

            askDiv.appendChild(optionsContainer);
    
    } else if (currentPartIndex === 2) {
        userChoices.part3[`q${qid}`] = "";
        // PHẦN III - Tự luận
            const askDiv = question.querySelector('.ask');
            if (!askDiv) {
                console.error('Không tìm thấy phần ask cho câu hỏi', question);
                return;
            }

            if (askDiv.querySelector('.answer-input-container')) return;

            const inputContainer = document.createElement('div');
            inputContainer.className = 'answer-input-container';
            inputContainer.style.marginTop = '10px';
            inputContainer.style.padding = '10px';
            inputContainer.style.borderTop = '1px dashed #ccc';

            const input = document.createElement('input');
            input.type = 'text';
            input.placeholder = 'Nhập câu trả lời...';
            input.style.width = '100%';
            input.style.padding = '8px';
            input.style.border = '1px solid #ddd';
            input.style.borderRadius = '4px';

            input.addEventListener('input', (e) => {
                userChoices.part3[`q${qid}`] = e.target.value;
                updateCheckboxState(qid);
            });
           
            inputContainer.appendChild(input);
            askDiv.appendChild(inputContainer);
    
    }
});
});
// Create log button
            document.getElementById('logButton').addEventListener('click', () => {
            console.log('--- Tất cả lựa chọn ---');
            console.log(JSON.stringify(userChoices, null, 2));

            console.log('--- Theo từng phần ---');
            console.log('PHẦN I (Trắc nghiệm):', JSON.stringify(userChoices.part1, null, 2));
            console.log('PHẦN II (Đúng/Sai):', JSON.stringify(userChoices.part2, null, 2));
            console.log('PHẦN III (Tự luận):', JSON.stringify(userChoices.part3, null, 2));

            fetch(`${siteUrl}/api/cham-diem/thptqg/`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_test: id_test,
                    testname: <?php echo "'$testname'" ?>,
                    username: <?php echo "'$current_username'" ?>,
                    subject: subject,
                    result_id: <?php echo "'$result_id'" ?>,
                    user_answer: userChoices
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const result = data.data;
                    alert(
                        `Kết quả:\n` +
                        `- Tổng điểm: ${result.total_score}/10\n` +
                        `- Đúng: ${result.correct_count}\n` +
                        `- Sai: ${result.wrong_count}\n` +
                        `- Bỏ qua: ${result.skipped_count}\n` +
                        `- Điểm phần I: ${result.part_scores.part1}\n` +
                        `- Điểm phần II: ${result.part_scores.part2}\n` +
                        `- Điểm phần III: ${result.part_scores.part3}`
                    );
                } else {
                    alert('Chấm điểm thất bại.');
                }
            })
            .catch(error => {
                console.error('Lỗi khi gửi dữ liệu:', error);
                alert('Đã xảy ra lỗi khi gửi yêu cầu.');
            });
        });
        hidePreloader();
            showQuestion(0);
            logQuestionsInfo();
            createCheckboxes();  // Tạo checkbox tương ứng

    });
   
    const totalMinutes = <?php echo $time; ?>;
    let timeLeft = 3600; // đổi phút sang giây
    const timerDisplay = document.getElementById('timer');

    

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.innerHTML = `<div class = "time-control"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ebe8e8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> ${minutes}: ${seconds < 10 ? '0' : ''}${seconds} </div>`;

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            // Tự động nộp bài
            if (typeof logButton !== 'undefined') {
                //logButton.click();
            } else {
                alert("Không tìm thấy nút nộp bài!");
            }
        }

        timeLeft--;
    }

    const timerInterval = setInterval(updateTimer, 1000);
    //updateTimer();

        </script>
        
    </body>
</html>
<?php


}
else{
    get_header();
    if (!$foundUser) {
        echo "
        <div class='checkout-modal-overlay'>
            <div class='checkout-modal'>
                <h3>Bạn chưa mua đề thi này</h3>";     
        } 

    else if ($foundUser['time_left'] <= 0) {
        echo "
        <div class='checkout-modal-overlay'>
            <div class='checkout-modal'>
                <h3> Bạn đã từng mua test này nhưng số lượt làm test này đã hết rồi, vui lòng mua thêm token<i class='fa-solid fa-face-sad-tear'></i></h3>";
    }

    echo"
            <p> Bạn đang có: $token token</p>
            <p> Để làm test này bạn cần $token_need token. Bạn sẽ được làm test này $time_allow lần </p>
            <p class = 'info-buy'>Bạn có muốn mua $time_allow lượt làm test này với $token_need không ?</button>
                <div class='button-group'>
                    <button class='process-token' onclick='preProcessToken()'>Mua ngay</button>
                    <button style = 'display:none' class='close-modal'>Hủy</button>
                </div>  
            </div>
        </div>
        
        <script>
    
    function preProcessToken() {
        if ($token < $token_need) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: 'Bạn không đủ token để mua test này',
                footer: `<a href='${site_url}/dashboard/buy_token/'>Nạp token vào tài khoản ngay</a>`
            });
        } else {
            console.log(`Allow to next step`);
            jQuery.ajax({
                url: `${site_url}/wp-admin/admin-ajax.php`,
                type: 'POST',
                data: {
                    action: 'update_buy_test',
                    type_transaction: 'paid',
                    table: 'thptqg_question',
                    change_token: '$token_need',
                    payment_gate: 'token',
                    type_token: 'token',
                    title: 'Renew test $testname with $id_test (THPTQG) with $token_need (Buy $time_allow time do this test)',
                    id_test: id_test
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Mua test thành công!',
                        text: 'Trang sẽ được làm mới sau 2 giây.',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        willClose: () => location.reload()
                    });
                },
                error: function (error) {
                    console.error('Error updating time_left:', error);
                }
            });
        }
    }
        </script>
        <style>
.checkout-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}

.checkout-modal {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    width: 400px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.checkout-modal h3 {
    font-size: 18px;
    color: #333;
}

.checkout-modal p {
    margin: 10px 0;
    color: #555;
}

.checkout-modal .button-group {
    margin-top: 20px;
}

.process-token {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-right: 10px;
    font-size: 14px;
}

.process-token:hover {
    background-color: #0056b3;
}

.close-modal {
    background-color: #ccc;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
}

.close-modal:hover {
    background-color: #aaa;
}
</style>

<script>
    document.querySelector('.close-modal')?.addEventListener('click', function() {
        document.querySelector('.checkout-modal-overlay').style.display = 'none';
    });
</script>
        ";
        } 
    }
    
 else {
        get_header();
            echo "<p>Không tìm thấy đề thi.</p>";
            exit();
    }

} else {
    get_header();
    echo "<p>Please log in to submit your answer.</p>";

}
get_footer();