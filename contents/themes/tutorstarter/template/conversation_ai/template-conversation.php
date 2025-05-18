<?php
/*
 * Template Name: Doing Template
 * Template Post Type: conversation ai
 
 */

    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly.
    }

    remove_filter('the_content', 'wptexturize');
    remove_filter('the_title', 'wptexturize');
    remove_filter('comment_text', 'wptexturize');

if (is_user_logged_in()) {
    $post_id = get_the_ID();
    $user_id = get_current_user_id();
 //   $custom_number = get_post_meta($post_id, '_digitalsat_custom_number', true);
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

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
// Set custom_number as id_test
$id_test = $custom_number;

// Prepare the SQL statement
$sql_test = "SELECT * FROM conversation_with_ai_list WHERE id_test = ?";
$stmt_test = $conn->prepare($sql_test);
$stmt_test->bind_param("i", $id_test);
$stmt_test->execute();
$result_test = $stmt_test->get_result();


 // Get current time (hour, minute, second)
 $hour = date('H'); // Giờ
 $minute = date('i'); // Phút
 $second = date('s'); // Giây

 // Generate random two-digit number
 $random_number = rand(10, 99);
 // Handle user_id and id_test error, set to "00" if invalid
 if (!$user_id) {
    $user_id = '00'; // Set user_id to "00" if invalid
}

if (!$custom_number) {
    $custom_number = '00'; // Set id_test to "00" if invalid
}


 // Create result_id
 $result_id = $hour . $minute . $second. $user_id . $random_number;
 $site_url = get_site_url();

 echo "<script> 
        var resultId = '" . $result_id . "';
       
        var siteUrl = '" .
        $site_url .
        "';
        var id_test = '" .
        $id_test .
        "';


        console.log('Result ID: ' + resultId);
        console.log('id_test: ' + id_test);

    </script>";


// Query to fetch token details for the current username
$sql2 = "SELECT token, token_use_history, token_practice
         FROM user_token 
         WHERE username = ?";


if ($result_test->num_rows > 0) {
    // Fetch test data if available
    $data = $result_test->fetch_assoc();
    $token_need = $data['token_need'];
    $time_allow = $data['time_allow'];
    $permissive_management = $data['permissive_management'];
    $testname =  $data['testname'];
    $target_1 =  $data['target_1'];
    $target_2 =  $data['target_2'];
    $target_3 =  $data['target_3'];
    $topic =  $data['topic'];
    $ai_role =  $data['ai_role'];
    $user_role =  $data['user_role'];
    $sentence_limit =  $data['sentence_limit'];
    $cover_image =  $data['cover_image'];
    echo '
    <script>
        var sentence_limit = "' . $sentence_limit . '";
        console.log("sentence_limit", sentence_limit);
    </script>';
   

    add_filter('document_title_parts', function ($title) use ($testname) {
      $title['title'] = $testname; // Use the $testname variable from the outer scope
      return $title;
  });
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
      $token_practice  = $token_data['token_practice'];
      $token_use_history = $token_data['token_use_history'];

      echo "<script>//console.log('Token: $token_practice, Token Use History: $token_use_history, Mày tên: $current_username');</script>";
     

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
              let testname = "' . $testname . '";
              let time_left = "' . (isset($foundUser['time_left']) ? $foundUser['time_left'] : 10) . '";
          </script>';
          
          get_header(); // Gọi phần đầu trang (header.php)













    // Close statement and connection
    $stmt_test->close();
    $conn->close();
        
        

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Talk With Edward </title>
    <script src="/contents/themes/tutorstarter/scan-device/system_check_.js"></script>
    <script src="/contents/themes/tutorstarter/scan-device/location_ip_.js"></script>
    <script src="/contents/themes/tutorstarter/scan-device/browser_check.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const systemInfo = {
            os: getOS(),
            browser: checkBrowser(),
            network: checkLocationAndIpAddress(),
            timestamp: new Date().toISOString()
        };
        
        console.log('ℹ️ Tổng hợp thông tin hệ thống:', systemInfo);
        // Có thể gửi systemInfo này về server nếu cần
    });
    </script>



<style>
    /* Thêm style mới cho thông báo trình duyệt không hỗ trợ */
    .browser-warning {
      text-align: center;
      padding: 20px;
      background-color: #fff3cd;
      border: 1px solid #ffeeba;
      border-radius: 8px;
      max-width: 600px;
      margin: 50px auto;
      color: #856404;
    }
    
    .browser-warning h2 {
      color: #856404;
      margin-top: 0;
    }
    
    .supported-browsers {
      margin-top: 15px;
      text-align: left;
      display: inline-block;
    }
    .chat-box {
        width: 100%;
        height: 500px;
        overflow-y: auto;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px;
        background-color: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chat-message {
        margin: 10px 0;
        display: flex;
        align-items: flex-start;
        }

        .chat-message.assistant {
        justify-content: flex-start;
        }

        .chat-message.user {
        justify-content: flex-end;
        }

        .message-content {
        max-width: 70%;
        padding: 10px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.5;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .chat-message.assistant .message-content {
        background-color: #e3f2fd;
        color: #0d47a1;
        }

        .chat-message.user .message-content {
        background-color: #bbdefb;
        color: #0d47a1;
        }

        /* HTML: <div class="loader"></div> */
        .loader {
        width: 70px;
        aspect-ratio: 1;
        border-radius: 50%;
        border: 8px solid #514b82;
        animation: l20-1 0.8s infinite linear alternate, l20-2 1.6s infinite linear;
        }

        #test-prepare {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: fixed; /* Giữ loader cố định giữa màn hình */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%); /* Căn giữa theo cả chiều ngang và dọc */
            height: 200px;
            z-index: 1001; /* Đảm bảo loader ở trên các phần tử khác */
        }


        @keyframes l20-1{
        0%    {clip-path: polygon(50% 50%,0       0,  50%   0%,  50%    0%, 50%    0%, 50%    0%, 50%    0% )}
        12.5% {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100%   0%, 100%   0%, 100%   0% )}
        25%   {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100% 100%, 100% 100%, 100% 100% )}
        50%   {clip-path: polygon(50% 50%,0       0,  50%   0%,  100%   0%, 100% 100%, 50%  100%, 0%   100% )}
        62.5% {clip-path: polygon(50% 50%,100%    0, 100%   0%,  100%   0%, 100% 100%, 50%  100%, 0%   100% )}
        75%   {clip-path: polygon(50% 50%,100% 100%, 100% 100%,  100% 100%, 100% 100%, 50%  100%, 0%   100% )}
        100%  {clip-path: polygon(50% 50%,50%  100%,  50% 100%,   50% 100%,  50% 100%, 50%  100%, 0%   100% )}
        }
        @keyframes l20-2{ 
        0%    {transform:scaleY(1)  rotate(0deg)}
        49.99%{transform:scaleY(1)  rotate(135deg)}
        50%   {transform:scaleY(-1) rotate(0deg)}
        100%  {transform:scaleY(-1) rotate(-135deg)}
        }

        .start_test {
        appearance: none;
        background-color: #2ea44f;
        border: 1px solid rgba(27, 31, 35, .15);
        border-radius: 6px;
        box-shadow: rgba(27, 31, 35, .1) 0 1px 0;
        box-sizing: border-box;
        color: #fff;
        cursor: pointer;
        display: inline-block;
        font-family: -apple-system,system-ui,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
        font-size: 20px;
        font-weight: 600;
        line-height: 20px;
        padding: 6px 16px;
        position: relative;
        text-align: center;
        text-decoration: none;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        vertical-align: middle;
        white-space: nowrap;
        }

        .start_test:focus:not(:focus-visible):not(.focus-visible) {
        box-shadow: none;
        outline: none;
        }

        .start_test:hover {
        background-color: #2c974b;
        }

        .start_test:focus {
        box-shadow: rgba(46, 164, 79, .4) 0 0 0 3px;
        outline: none;
        }

        .start_test:disabled {
        background-color: #94d3a2;
        border-color: rgba(27, 31, 35, .1);
        color: rgba(255, 255, 255, .8);
        cursor: default;
        }

        .start_test:active {
        background-color: #298e46;
        box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
        }
        #quiz-container1 {
            flex: 3;
            padding: 20px;
        }

        #sidebar1 {
            flex: 1;
            padding: 20px;
            border-left: 2px solid #ccc;
            height: auto;
            overflow-y: auto;
        }

        .container-content {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            min-height: 100vh;
        }

      

/* Style for chat messages (you'll need to add classes to your messages) */
.message {
    margin-bottom: 12px;
    padding: 10px 15px;
    border-radius: 18px;
    max-width: 80%;
    line-height: 1.4;
}

.user-message {
    background-color: #007bff;
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 4px;
}
.assistant-message {
        background-color: #f1f1f1;
        margin-right: auto;
    }
.bot-message {
    background-color: #e9ecef;
    color: #333;
    margin-right: auto;
    border-bottom-left-radius: 4px;
}

.input-group {
    display: flex;
    align-items: center; /* Căn giữa các phần tử theo chiều dọc */
    width: 100%; /* Đảm bảo input-group chiếm full width */
}

#micButton {
    flex: 0 0 auto; /* Không co giãn, kích thước tự động */
    margin-right: 8px; /* Khoảng cách với input */
}

#userInput {
    flex: 1 1 auto; /* Chiếm hết không gian còn lại */
    min-width: 0; /* Quan trọng để input không bị tràn */
}

#sendButton {
    flex: 0 0 auto; /* Không co giãn, kích thước tự động */
    margin-left: 8px; /* Khoảng cách với input */
}

.form-control {
    border: none;
    padding: 12px 20px;
    background-color: #fff;
}

.form-control:focus {
    box-shadow: none;
    border-color: #ced4da;
}

.btn {
    padding: 12px 20px;
    border: none;
    transition: all 0.3s ease;
}

.btn-secondary {
    background-color: #f1f1f1;
    color: #555;
}

audio {
        display: block;
        margin-top: 10px;
        width: 100%;
        max-width: 300px;
    }
    .audio-label {
        font-size: 0.8em;
        color: #666;
        margin-bottom: 5px;
    }
    .audio-play-button {
        vertical-align: middle;
        padding: 2px 8px;
        font-size: 0.8rem;
    }



.btn-secondary:hover {
    background-color: #e0e0e0;
}

.btn-primary {
    background-color: #007bff;
}

.btn-primary:hover {
    background-color: #0069d9;
}

/* Scrollbar styling */
.chat-box::-webkit-scrollbar {
    width: 8px;
}

.chat-box::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.chat-box::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.chat-box::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

  </style>
</head>
<body onload = "main()">

<!-- Thêm div thông báo trình duyệt không hỗ trợ (ban đầu ẩn đi) -->
<div id="browserWarning" class="browser-warning" style="display: none;">
  <h2>Trình duyệt không được hỗ trợ</h2>
  <p>Xin lỗi, hệ thống của chúng tôi hiện không hỗ trợ trình duyệt Opera.</p>
  <p>Vui lòng sử dụng một trong các trình duyệt sau để tiếp tục:</p>
  
  <div class="supported-browsers">
    <p>✓ Google Chrome</p>
    <p>✓ Microsoft Edge</p>
    <p>✓ Mozilla Firefox</p>
    <p>✓ Safari (trên Mac/iOS)</p>
    <p>✓ Cốc Cốc</p>
  </div>
</div>


<div id = "test-prepare">
        <div class="loader"></div>
        <h3>Your test will begin shortly</h3>
        <div id = "checkpoint" class = "checkpoint">
                <?php
                    if($premium_test == "True"){
                        echo "<script >console.log('Thông báo. Bạn còn {$foundUser['time_left']} lượt làm bài. success ');</script>";
                        echo " <p style = 'color:green'> Bạn còn {$foundUser['time_left']} lượt làm bài này <svg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='#7ed321' stroke-width='2' stroke-linecap='round' stroke-linejoin='arcs'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'></path><polyline points='22 4 12 14.01 9 11.01'></polyline></svg> </p> ";
                        echo "<script>console.log('This is premium test');</script>";
                    }
                    else{
                        echo "<script>console.log('This is free test');</script>"; 
                    }
                        ?>
        </div>    
        <div id = "quick-instruction">
            <i>Quick Instruction:<br>
            - If you find any errors from test (image,display,text,...), please let us know by clicking icon <i class="fa-solid fa-bug"></i><br> 
            - Incon <i class="fa-solid fa-circle-info"></i> will give you a guide tour, in which you can understand the structure of test, include test's type, formation and how to answer questions<br>
            - All these two icons are at the right-above side of test.
        </i>

        </div>
        <div style="display: none;" id="date" style="visibility:hidden;"></div>
        <div style="display: none;" id="title-test"><?php echo esc_html($testname);?></div>
        <div  style="display: none;"  id="id_test"  style="visibility:hidden;"><?php echo esc_html($custom_number);?></div>
        <button  style="display: none;" class ="start_test" id="start_test"  onclick = "prestartTest()">Start test</button>
        <i id = "welcome" style = "display:none">Click Start Test button to start the test now. Good luck</i>


    </div>

 <div  id = "test_screen" style="display: none;">
    <div>
     

    <div class="container-content">
            <div id="quiz-container1">
                <div id="chatBox" class="chat-box mb-3"></div>
                <div class="input-group">
                    <button id="startRecordButton" class="btn btn-secondary">🎤 Start Record</button>
                    <button id="endRecordButton" class="btn btn-danger" style="display:none;">⏹️ End Record</button>
                    <input id="userInput" type="text" class="form-control" placeholder="Speak your message...">
                    <button id="sendButton" class="btn btn-primary">Send</button>
                </div>
                <div id="attemptCounter" class="mt-2">
                    Attempts remaining: <span id="remainingAttempts"><?php echo esc_html($sentence_limit);?></span>/<?php echo esc_html($sentence_limit);?>
                </div>
            </div>
            <div id="sidebar1">
                <b>Context:</b> <?php echo esc_html($testname);?><br>
                <b>Your role:</b><?php echo esc_html($user_role);?><br>
                <b>My role:</b> <?php echo esc_html($ai_role);?><br>
                <b>Quick Instruction:</b><br>
                <i>+ You are <?php echo esc_html($user_role);?> and I am <?php echo esc_html($ai_role);?> inside the context '<?php echo esc_html($testname);?>'</i><br><br>
                <i>+ Your missions are:
                    <div id="target-1">
                        <input type="checkbox" id="target1-checkbox" class="target-checkbox" disabled>
                        <b>Target 1: <?php echo esc_html($target_1);?></b>
                    </div>
                    <div id="target-2">
                        <input type="checkbox" id="target2-checkbox" class="target-checkbox" disabled>
                        <b>Target 2: <?php echo esc_html($target_2);?></b>
                    </div>
                    <div id="target-3">
                        <input type="checkbox" id="target3-checkbox" class="target-checkbox" disabled>
                        <b>Target 3: <?php echo esc_html($target_3);?></b>
                    </div>
                </i>
                <b>Note: </b><i>You will have <?php echo esc_html($sentence_limit);?> times to answer and finish missions</i>

                <button id="submitButton" class="btn btn-primary">Submit All</button>


                <!-- giấu form send kết quả bài thi -->
                    <span id="message"></span>
                    <form id="saveConversationAI"  >
                                <div class="card">
                                    <div class="card-header">Form lưu kết quả</div>
                                    <div class="card-body" >
                            
                                    <div class = "form-group">
                                        <input type="text" id="dateform" name="dateform" placeholder="Ngày"  class="form-control form_data"  />
                                        <span id="date_error" class="text-danger" ></span>
                                    </div>
                                
                                    <div class = "form-group">
                                        <input type="text" id="idtest" name="idtest" placeholder="Id test"  class="form-control form_data" />
                                        <span id="idtest_error" class="text-danger" ></span>
                                    </div>
                    
                                    <div class = "form-group">
                                        <input type="text"  id="testname" name="testname" placeholder="Test Name"  class="form-control form_data" />
                                        <span id="testname_error" class="text-danger"></span>
                                    </div>
                                    <div class = "form-group">
                                        <textarea type="text"  id="conversation" name="conversation" placeholder="User Answer"  class="form-control form_data"></textarea>
                                        <span id="conversation_error" class="text-danger"></span>
                                    </div>

                                    <div class = "form-group"   >
                                        <input type="text"  id="testsavenumber" name="testsavenumber" placeholder="Result Number"  class="form-control form_data" />
                                        <span id="testsavenumber_error" class="text-danger"></span>  
                                    </div>
                        
                                    <div class="card-footer">
                                        <td><input type="submit" id="submit" name="submit"/></td> 
                                    </div>
                                
                                </div>
                        <div id="result_msg" ></div>
                    </form>
                <!-- kết thúc send form -->


            </div>
        </div>

    </div>
</div>
<script>
    // function save data qua ajax
        jQuery('#saveConversationAI').submit(function(event) {
            event.preventDefault(); // Prevent the default form submission
            
            var link = "<?php echo admin_url('admin-ajax.php'); ?>";
            
            var form = jQuery('#saveConversationAI').serialize();
            var formData = new FormData();
            formData.append('action', 'save_user_result_conversation_ai');
            formData.append('save_user_result_conversation_ai', form);
            
            jQuery.ajax({
                url: link,
                data: formData,
                processData: false,
                contentType: false,
                type: 'post',
                success: function(result) {
                    jQuery('#submit').attr('disabled', false);
                    if (result.success == true) {
                        jQuery('#saveConversationAI')[0].reset();
                    }
                    jQuery('#result_msg').html('<span class="' + result.success + '">' + result.data + '</span>');
                }
            });
            });
            
        document.addEventListener("DOMContentLoaded", function () {
            document.addEventListener("submitForm", function () {
                setTimeout(function () {
                    let form = document.getElementById("saveConversationAI");
                    form.submit(); 
                }, 2000); 
            });
        });


        //end new adding
                
        let dateElement;

        function formatDateTimeForSQL(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        // Initialize date when script loads
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            dateElement = formatDateTimeForSQL(now);
        });

</script>
<script src="/contents/themes/tutorstarter/conversation_ai_toolkit/script5.js"></script>
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
            <p> Bạn đang có: $token_practice token</p>
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
        if ($token_practice < $token_need) {
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
                    table: 'conversation_with_ai_list',
                    change_token: '$token_need',
                    payment_gate: 'token',
                    type_token: 'token_practice',
                    title: 'Buy test $testname with $id_test (Conversation With AI) with $token_need (Buy $time_allow time do this test)',
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
            get_footer();
    }

} else {
    get_header();
    echo "<p>Please log in to submit your answer.</p>";
    get_footer();
}
