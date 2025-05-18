<?php
 $post_id = get_the_ID();
 $user_id = get_current_user_id();// Get the custom number field value
$custom_number = get_query_var('id_test');
//$custom_number = get_post_meta($post_id, '_ieltsreadingtest_custom_number', true);
$site_url = get_site_url();

  // Database credentials
  $servername = DB_HOST;
  $username = DB_USER;
  $password = DB_PASSWORD;
  $dbname = DB_NAME;
//$commentcount = get_comments_number( $post->ID );

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Truy vấn `question_choose` từ bảng `ielts_reading_test_list` theo `id_test`
$sql_test = "SELECT  * FROM topik_listening_test_list WHERE id_test = ?";
$stmt_test = $conn->prepare($sql_test);

if ($stmt_test === false) {
    die('Lỗi MySQL prepare: ' . $conn->error);
}


$stmt_test->bind_param("i", $custom_number);
$stmt_test->execute();
$result_test = $stmt_test->get_result();
// Fetch the result

if ($result_test->num_rows === 0) {
    // Nếu không tìm thấy id_test, chuyển hướng đến trang 404
    wp_redirect(home_url('/404'));
    exit;
}


if ($result_test->num_rows > 0) {
    $row = $result_test->fetch_assoc();
    $test_type = $row['test_type'];
    $testname = $row['testname'];
}




add_filter('document_title_parts', function ($title) use ($testname) {
    $title['title'] = $testname; // Use the $testname variable from the outer scope
    return $title;
});


get_header(); // Gọi phần đầu trang (header.php)



// Close the database connection
$conn->close();

// Split the comma-separated string into an array




    global $wpdb;

    // Get current user's username
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;

    // Get results for the current user and specific idtest (custom_number)
    $results_query = $wpdb->prepare("
        SELECT * FROM save_user_result_topik_listening 
        WHERE username = %s 
        AND idtest = %d
        ORDER BY dateform DESC",
        $current_username,
        $custom_number
    );
    $results = $wpdb->get_results($results_query); ?>
    
        <style>
            * {
            box-sizing: border-box;
            }

            /* Create two equal columns that floats next to each other */
            .column {
            float: left;
            width: 50%;
            padding: 10px;
            }

            /* Clear floats after the columns */
            .row:after {
            content: "";
            display: table;
            clear: both;
            }

            /* Additional styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container-info-test {
            max-width: 100%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #333;
        }

        .check-icon {
            color: green;
        }

        .test-info, .results, .practice-options {
            margin-bottom: 20px;
        }

        .note {
            color: red;
            font-weight: bold;
        }
        html {
            scroll-behavior: smooth;
        }

      

        .options-header {
            display: flex;
            margin-bottom: 20px;
            font-size: 15px;
        }

        .option {
            cursor: pointer;
            color: #007bff;
            text-decoration: underline;
            padding: 5px 10px;
        }

        .option:hover {
            text-decoration: none;
        }

        .active-bar {
            background-color: #007bff; /* Highlight color */
            color: white; /* Text color for active state */
            border-radius: 5px;
        }

        .checkbox-group {
            margin-bottom: 20px;
        }
        .checkbox-group label {
            display: block;
            margin: 5px 0;
        }
        .btn-submit {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }
        .h2-test{
            font-weight: bold;
            font-size: 25px;
        }
        .alert{
            margin-bottom: 0;
            position: relative;
            padding: .75rem 1.25rem;
            border: 1px solid transparent;
            border-radius: .35rem;
        }
        .alert-success{
            color: 1f5e39;
            background-color: #d8f0e2;
            border-color: #c8ead6;
        }

                
        .popup-position {
            display:none;
            position: fixed;
            top: 0;
            left: 0;
            background-color: rgba(0, 0, 0, 0.5);
            width: 100%;
            height: 100%;
        }

        #wrapper{
            width: 960px;
            margin: 40px auto;
            text-align: left;
        }

        #popup-wrapper{
            width: 800px;
            margin: 70px auto;
            text-align: left;
        }
        #popup-container{
            width:800px;
            background-color: #fff;
            padding: 20px;
            border-radius: 4px;
        }
    </style>

        <div class="container-info-test">
           <h1><?php echo htmlspecialchars($testname); ?>  <span class="check-icon">✔️</span></h1>
           
        <div class="test-info">
            <p><strong>Thời gian làm bài:</strong> 40 phút | 4 phần thi | 40 câu hỏi |  bình luận</p>
            <p>203832 người đã luyện tập đề thi này</p>
            <p class="note">Chú ý: để được duy trì điểm scaled score (ví dụ trên điểm 990 TOEIC hoặc 9.0 cho IELTS), vui lòng chọn chế độ làm FULL TEST.</p>
        </div>


        <?php
       
        if ($results) {
            // Start the results table before the loop
            ?>
            <div class="results">
                <p class="h2-test">Kết quả làm bài của bạn:</p>
                <table class="table table-striped" style="border: 1px solid #ddd; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th>Ngày làm</th>
                            <th>Kết quả</th>
                            <th>Điểm thành phần</th>
                            <th>Chi tiết bài làm</th>

                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($results as $result) {
                        // Display each result as a new row in the same table
                        ?>
                        <tr>
                            <td><?php echo esc_html($result->dateform); ?></td>
                            <td><?php echo esc_html($result->overallband); ?></td>
                            <td><?php echo esc_html($result->correct_number) ;?>/ <?php echo esc_html($result->total_question_number); ?></td>
                            <td>
                                <a href="<?php echo $site_url?>/topik/l/result/<?php echo esc_html($result->testsavenumber); ?>">
                                    Xem bài làm
                                </a>

                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <?php
        } else {
            echo '<p>Bạn chưa làm test này.</p>';
        }
        ?>
        
            



        
        <div class="practice-options">
        <p class="options-header">
            <span class="option active-bar" id="full-test">Làm full test</span>  
            <span class="option" id="practice">Luyện tập</span> 
            <span class="option" id="discussion">Thảo luận</span>
            <span class="option" id="preview_test" >Tải bản PDF</span>

        </p>

    <div id="tips-container">
        <div id="full-test-content">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Pro tips:</h4> <hr>
                <p>Sẵn sàng để bắt đầu làm full test? Để đạt được kết quả tốt nhất, bạn cần dành ra 40 phút cho bài test này.</p>
            </div><br>
            <a class="btn-submit" href="<?php echo $site_url?>/test/topik/l/<?php echo $custom_number?>/start/">Bắt đầu bài thi</a>
        </div>

        <div id="practice-content" style="display: none;">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Pro tips:</h4> <hr>
                <p>Hình thức luyện tập từng phần và chọn mức thời gian phù hợp sẽ giúp bạn tập trung vào giải đúng các câu hỏi thay vì phải chịu áp lực hoàn thành bài thi.</p>    
            </div><br>

            <p class="h2-test">Giới hạn thời gian (Để trống để làm bài không giới hạn):</p>
            <form action="<?php echo $site_url?>/test/topik/l/<?php echo $custom_number?>/start/" method="get">
                <label style="font-size: 18px;" for="timer"></label>

                <select id="timer" name="option">
                    <option value="1000000">Unlimited time</option>
                    <option value="60">1 minutes</option>
                    <option value="1800">30 minutes</option>
                    <option value="2700">45 minutes</option>
                    <option value="3600">60 minutes</option>
                    <option value="4500">75 minutes</option>
                    <option value="5400">90 minutes</option>
                    <option value="6300">105 minutes</option>
                    <option value="7200">120 minutes</option>
                    <option value="9000">150 minutes</option>
                    <option value="10800">180 minutes</option>
                </select><br><br>      
                <button class="btn-submit" type="submit" value="Start test">Luyện tập</button>
            </form>

  
</form>



        </div>
    </div>
</div>
    <div id="popup-box3" class="popup-position" style="display:none;">
    <div id="popup-wrapper">
        <div id="popup-container" style="height: 500px; overflow-y: auto; position: relative;">
            <!-- Fixed Close box -->
            <div style="position: sticky; top: 0; background: white; padding: 10px; z-index: 1;">
                <p><a href="javascript:void(0)" onclick="toggle_visibility('popup-box3');" style ="float:right;">Đóng Popup</a></p>
            </div>

           
        </div>
    </div>
</div>



    <script>
        

    function toggle_visibility(id) {
        var e = document.getElementById(id);
        if (e.style.display == 'block') {
            e.style.display = 'none';
        } else {
            e.style.display = 'block';
        }
    }

document.getElementById('practice').addEventListener('click', function() {
    // Show practice content
    document.getElementById('practice-content').style.display = 'block';
    document.getElementById('full-test-content').style.display = 'none';

    // Set active state
    setActiveOption('practice');
});

document.getElementById('full-test').addEventListener('click', function() {
    // Show full test content
    document.getElementById('practice-content').style.display = 'none';
    document.getElementById('full-test-content').style.display = 'block';

    // Set active state
    setActiveOption('full-test');
});

// Event listener for the discussion tab to redirect to #comment
document.getElementById('discussion').addEventListener('click', function() {
    window.location.href = '#comment';  // Redirect to #comment
});

function setActiveOption(optionId) {
    const options = document.querySelectorAll('.option');
    options.forEach(option => {
        if (option.id === optionId) {
            option.classList.add('active-bar');
        } else {
            option.classList.remove('active-bar');
        }
    });
}

// Initial state: Show full test content and highlight the full test button
document.getElementById('full-test-content').style.display = 'block';
document.getElementById('practice-content').style.display = 'none';
setActiveOption('full-test');


// Get the elements
const discussionTab = document.getElementById('discussion');

// Event listener for the discussion tab to redirect to #comment
discussionTab.addEventListener('click', function() {
    window.location.href = '#comment';  // Redirect to #comment
});


function setActiveOption(optionId) {
    const options = document.querySelectorAll('.option');
    options.forEach(option => {
        if (option.id === optionId) {
            option.classList.add('active-bar');
        } else {
            option.classList.remove('active-bar');
        }
    });
}

function resetActiveOptions() {
    const options = document.querySelectorAll('.option');
    options.forEach(option => {
        option.classList.remove('active-bar');
    });
}

// Initial state: Show practice content and highlight the practice button
setActiveOption('full-test');



</script>



            
<?php if ( comments_open() || get_comments_number() ) :
    comments_template();
endif; ?>


    
    
    <?php
    
get_footer(); // Gọi phần cuối trang (footer.php)
?>
