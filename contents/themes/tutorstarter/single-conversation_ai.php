

<?php
$post_id = get_the_ID();
$user_id = get_current_user_id();
// Get the custom number field value
//$custom_number = get_post_meta($post_id, '_dictationexercise_custom_number', true);
$custom_number = get_query_var('id_test');
$site_url = get_site_url();
/*
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['doing_text'])) {
    $textarea_content = sanitize_textarea_field($_POST['doing_text']);
    update_user_meta($user_id, "ieltswritingtests_{$post_id}_textarea", $textarea_content);

    wp_safe_redirect(get_permalink($post_id) . 'result/');
    exit;
}*/




if (is_user_logged_in()) {
    global $wpdb;

    // Get current user's username
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;

    // Get results for the current user and specific idtest (custom_number)
    $results_query = $wpdb->prepare("
        SELECT * FROM conversation_with_ai_list 
        WHERE idtest = %s
        ",
        $current_username,
        $custom_number
            );
        // Database credentials (update with your own database details)
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

        $sql = "SELECT  testname FROM conversation_with_ai_list WHERE id_test = ?";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $custom_number); // 'i' is used for integer
        $stmt->execute();
        $result = $stmt->get_result();

// Check if a result was returned
if ($row = $result->fetch_assoc()) {
    $testname = $row['testname']; // Get the testname from the result
} else {
    $testname = "Default Title"; // Fallback if no result found
}

// Add filter for document title
add_filter('document_title_parts', function ($title) use ($testname) {
    $title['title'] = $testname; // Use the $testname variable from the outer scope
    return $title;
});

get_header(); // Gọi phần đầu trang (header.php)



        // Đóng kết nối
        $conn->close();


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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
    </style>

           


           <div class="container-info-test">
           <h1><?php echo htmlspecialchars($testname); ?> <span class="check-icon"></span></h1>
           
        <div class="test-info">
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
                            <th>Đề thi</th>
                            <th>Kết quả</th>
                            <th>Thời gian làm bài</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($results as $result) {
                        // Display each result as a new row in the same table
                        ?>
                        <tr>
                            <td><?php echo esc_html($result->dateform); ?></td>
                            <td><?php echo esc_html($result->testname); ?></td>
                            <td><?php echo esc_html($result->resulttest); ?></td>
                            <td><?php echo esc_html($result->timedotest); ?></td>
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
                <span class="option active-bar" id="practice">Luyện tập</span> 
                <span class="option" id="full-test">Làm full test</span>  
                <span class="option" id="discussion">Thảo luận</span>
    </p>
            
            <div id="tips-container">
                <div id="practice-content">
                    <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Pro tips:</h4> <hr>
                        <p>Hình thức luyện tập từng phần và chọn mức thời gian phù hợp sẽ giúp bạn tập trung vào giải đúng các câu hỏi thay vì phải chịu áp lực hoàn thành bài thi.</p>    
                    </div><br>
                    
                    <p class ="h2-test" >Giới hạn thời gian (Để trống để làm bài không giới hạn):</p>
                    <form action="<?php echo $site_url?>/practice/talk-with-edward/<?php echo $custom_number?>/start/" method="get" method="get">
                        <label style="font-size: 18px;"  for="timer"></label>

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
                        <button  class ="btn-submit" type="submit" value="Start test">Luyện tập</button>
                    </form>
                </div>

                <div id="full-test-content" style="display: none;">
                <div class="alert alert-success" role="alert">
                        <h4 class="alert-heading">Pro tips:</h4> <hr>
                        <p>Sẵn sàng để bắt đầu làm full test? Để đạt được kết quả tốt nhất, bạn cần dành ra 40 phút cho bài test này.</p>
                        </div><br>
                    <a   class="btn-submit" href="<?php echo $site_url?>/practice/talk-with-edward/<?php echo $custom_number?>/start/" method="get">Bắt đầu bài thi</a>
                </div>
            </div>
        </div>
    </div>



            
<?php if ( comments_open() || get_comments_number() ) :
    comments_template();
endif; ?>
            

<script>
 document.getElementById('practice').addEventListener('click', function() {
    // Show practice content
    document.getElementById('practice-content').style.display = 'block';
    document.getElementById('full-test-content').style.display = 'none';

    // Set active-bar state
    setActiveOption('practice');
});

document.getElementById('full-test').addEventListener('click', function() {
    // Show full test content
    document.getElementById('practice-content').style.display = 'none';
    document.getElementById('full-test-content').style.display = 'block';

    // Set active-bar state
    setActiveOption('full-test');
});

document.getElementById('full-test').addEventListener('click', function() {
    // Show full test content
    
});


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
setActiveOption('practice');

hidePreloader();
</script>



    <?php
        

    
} else {
    get_header();
    echo '<p>Vui lòng đăng nhập để làm test.</p>';
}



get_footer(); // Gọi phần cuối trang (footer.php)
?>