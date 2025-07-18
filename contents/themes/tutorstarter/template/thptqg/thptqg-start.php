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
    $time = $data['time'];; // Fetch the time field
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
        display: inline !important;
        max-width: none !important; /* Cho phép JS can thiệp width thật */
        /*max-width: 80px !important;
        max-height: 30px !important*/
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

    .container-content {
        height: 500px;
        padding: 20px 0px 10px 0px;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        min-height: 100vh;
    }

    .sticky {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .header-info {
        padding: 10px 16px;
        background: #555;
        color: #f1f1f1;
        display: flex;
        justify-content: space-between; /* đẩy right-header sang trái, left-header sang phải */
        align-items: center; /* căn giữa theo chiều dọc */
        flex-wrap: nowrap;
    }

    .right-header {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .left-header {
        display: flex;
        align-items: center;
        gap: 10px; /* khoảng cách giữa icon - thời gian - nút */
    }

    .timer {
        white-space: nowrap;
    }

    .content-body {
        padding-top: 10px;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        flex: 1;
        min-height: 0; /* Cần thiết để scroll bên trong quiz/sidebar hoạt động */
    }

    #quiz-container1 {
        overflow-y: auto;
        flex: 3;
        padding-left: 100px;
        height: 100%;
    }

    #sidebar1 {
        flex: 1;
        padding: 10px;
        border-left: 2px solid #ccc;
        height: 100%;
        overflow-y: auto;
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

    .col-md-6 {
        background-color: white !important;
        border-radius: 10px !important;
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

    .testname {
        justify-content: center;
    }
</style>

<div class="container-content">
    <div class="sticky header-info" id="header-info">
        <div class="right-header">
            <div id="testname" class="testname">Đề thi: <?php echo $testname ?></div>
        </div>
        <div class="left-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                viewBox="0 0 24 24" fill="none" stroke="#ebe8e8"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <div id="timer" class="timer">Thời gian: <?php echo $time ?></div>

            <button id="logButton"
                style="padding: 10px 20px; background-color: #007bff; color: white; border: none;
                border-radius: 4px; cursor: pointer; z-index: 1000;">
                Nộp bài
            </button>
        </div>
    </div>


    <div class="content-body">
        <div class="row" id="quiz-container1">
            <div id="test">Nội dung bài kiểm tra</div>
        </div>
        <div id="sidebar1">
            <h3>Danh sách câu hỏi</h3>
            <div id="timer"></div>
            
            <div id="boxanswers"></div>
            <div class="container-checkbox" id="container-checkbox"></div>
        </div>
    </div>
</div>

        
       
    <script>

        document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("img").forEach(function (img) {
        // Đảm bảo ảnh đã tải xong mới kiểm tra width
        img.onload = function () {
            if (img.naturalWidth > 50) {
                img.style.height = "35px";
            } else {
                img.style.height = "20px";
            }
        };

        // Trường hợp ảnh đã được load sẵn
        if (img.complete) {
            if (img.naturalWidth > 50) {
                img.style.height = "35px";
            } else {
                img.style.height = "20px";
            }
        }
    });
});


(function countdownTimer() {
    // Lấy số phút từ PHP (giá trị ban đầu)
    const initialMinutes = <?php echo $time ?>;
    let timeInSeconds = initialMinutes * 60;

    const timerDisplay = document.getElementById("timer");

    function formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    const timerInterval = setInterval(() => {
        if (timeInSeconds <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = "Hết giờ!";
            alert("Hết giờ! Bài làm sẽ được nộp.");
            // Gọi hành động nộp bài
            document.getElementById("logButton").click();
            return;
        }

        timerDisplay.textContent = `Thời gian: ${formatTime(timeInSeconds)}`;
        timeInSeconds--;
    }, 1000);
})();
</script>

      <script>

var IMAGE_HOST = `http://localhost/fstudy/contents/themes/tutorstarter/template/media_img_intest/thptqg/${id_test}/`;

function replaceImageHost(content) {
    return content.replace(/\$\{IMAGE_HOST\}/g, IMAGE_HOST);
}

const testcode = <?php echo $testcode ?>;
const container = document.getElementById("test");
const checkboxContainer = document.getElementById("container-checkbox");
const userAnswers = {};

let questionIndex = 1;

for (const partKey in testcode.data) {
    const part = testcode.data[partKey];
    const questions = part.questions;
    
    // Khởi tạo part trong userAnswers
    userAnswers[partKey] = {};

    for (const qKey in questions) {
        const q = questions[qKey];

        // Khởi tạo câu trả lời mặc định rỗng cho từng câu hỏi trong part
        userAnswers[partKey][qKey] = "";

        // Tạo vùng hiển thị câu hỏi
        const qDiv = document.createElement("div");
        qDiv.id = `question-${questionIndex}`;
        qDiv.className = "question-block";
        qDiv.style.padding = "20px";
        qDiv.style.border = "1px solid #ccc";
        qDiv.style.marginBottom = "20px";
        qDiv.style.scrollMarginTop = "100px";

        const qContent = document.createElement("div");
        const questionContext = q.context || '';
        qContent.innerHTML = `<div id = "question-context" class = "question-context">${questionContext}</div><div id = "question-details" class = "question-details"><b style = "display:none">Câu ${questionIndex}:</b> ${replaceImageHost(q.question_content)}</div>`;
        qDiv.appendChild(qContent);

        const answers = q.answer || {};
        const currentIndex = questionIndex; // Capture current index

        // Part 1: multiple_choice
        if (part.type === "multiple_choice") {
            for (const [key, value] of Object.entries(answers)) {
                
                const label = document.createElement("label");
                label.style.display = "block";
                label.style.margin = "5px 0";

                const input = document.createElement("input");
                input.type = "radio";
                input.name = qKey;
                input.value = key;

                input.addEventListener("change", () => {
                    userAnswers[partKey][qKey] = key;
                    const checkboxEl = document.getElementById(`checkbox-${currentIndex}`);
                    if (checkboxEl) {
                        checkboxEl.style.backgroundColor = "#28a745";
                    }
                });

                label.appendChild(input);
                label.insertAdjacentHTML("beforeend", ` ${replaceImageHost(value)}`);
                qDiv.appendChild(label);
            }
        }

        // Part 2: true_false
        else if (part.type === "true_false") {
            for (const [key, value] of Object.entries(answers)) {
                const groupLabel = document.createElement("div");
                groupLabel.innerHTML = `<i>${key}</i>: ${replaceImageHost(value)}`;
                groupLabel.style.marginTop = "8px";
                qDiv.appendChild(groupLabel);

                ["Đúng", "Sai"].forEach(choice => {
                    const label = document.createElement("label");
                    label.style.marginLeft = "15px";
                    label.style.display = "inline-block";

                    const input = document.createElement("input");
                    input.type = "radio";
                    input.name = `${qKey}_${key}`;
                    input.value = choice;

                    input.addEventListener("change", () => {
                        if (!userAnswers[partKey][qKey]) userAnswers[partKey][qKey] = {};
                        userAnswers[partKey][qKey][key] = choice;

                        // Kiểm tra nếu đã chọn đủ các đáp án con
                        const totalSubQuestions = Object.keys(answers).length;
                        const currentAnswers = Object.keys(userAnswers[partKey][qKey]).length;

                        if (currentAnswers === totalSubQuestions) {
                            const checkboxEl = document.getElementById(`checkbox-${currentIndex}`);
                            if (checkboxEl) {
                                checkboxEl.style.backgroundColor = "#28a745";
                            }
                        }
                    });

                    label.appendChild(input);
                    label.append(` ${choice}`);
                    qDiv.appendChild(label);
                });
            }
        }

        // Part 3: completion
        else if (part.type === "completion") {
            const input = document.createElement("input");
            input.type = "text";
            input.style.width = "100%";
            input.style.marginTop = "10px";

            input.addEventListener("input", () => {
                userAnswers[partKey][qKey] = input.value.trim();
                if (input.value.trim() !== "") {
                    const checkboxEl = document.getElementById(`checkbox-${currentIndex}`);
                    if (checkboxEl) {
                        checkboxEl.style.backgroundColor = "#28a745";
                    }
                }
            });

            qDiv.appendChild(input);
        }

        container.appendChild(qDiv);

        // Tạo checkbox chuyển đến câu
        const box = document.createElement("div");
        box.id = `checkbox-${questionIndex}`;
        box.textContent = questionIndex;
        box.style.width = "30px";
        box.style.height = "30px";
        box.style.border = "1px solid #333";
        box.style.display = "inline-flex";
        box.style.justifyContent = "center";
        box.style.alignItems = "center";
        box.style.margin = "5px";
        box.style.cursor = "pointer";
        box.style.borderRadius = "4px";
        box.style.transition = "background 0.3s";

        box.addEventListener("click", () => {
            const target = document.getElementById(`question-${currentIndex}`);
            if (target) {
                target.scrollIntoView({ behavior: "smooth" });
            }
        });

        checkboxContainer.appendChild(box);
        questionIndex++;
    }
}
logButton.addEventListener('click', () => {
            console.log('--- Tất cả lựa chọn ---');
            console.log(JSON.stringify(userAnswers, null, 2));

            console.log('--- Theo từng phần ---');
            console.log('PHẦN I (Trắc nghiệm):', JSON.stringify(userAnswers.part1, null, 2));
            console.log('PHẦN II (Đúng/Sai):', JSON.stringify(userAnswers.part2, null, 2));
            console.log('PHẦN III (Tự luận):', JSON.stringify(userAnswers.part3, null, 2));

            fetch(`${siteUrl}/api/cham-diem/thptqg/`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_test: id_test, // Thay bằng ID bài test thực tế
                    testname: <?php echo "'$testname'"?>,
                    username: <?php echo "'$current_username'"?>, // Thay bằng username thực tế
                    subject: subject, // Ví dụ: 'Toán', 'Văn', 'Anh'
                    result_id: <?php echo "'$result_id '"?>,
                    user_answer: userAnswers
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const result = data.data;
                    console.log('chấm xong');
                } else {
                    alert('Chấm điểm thất bại.');
                }
            })
            .catch(error => {
                console.error('Lỗi khi gửi dữ liệu:', error);
                alert('Đã xảy ra lỗi khi gửi yêu cầu.');
            });
        });
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