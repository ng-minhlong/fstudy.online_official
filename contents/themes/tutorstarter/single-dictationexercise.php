

<?php
$post_id = get_the_ID();
$user_id = get_current_user_id();
// Get the custom number field value
//$custom_number = get_post_meta($post_id, '_dictationexercise_custom_number', true);
$custom_number = get_query_var('id_test');
$site_url = get_site_url();

echo '
<script>
        var ajaxurl = "' . admin_url('admin-ajax.php') . '";

var siteUrl = "' . $site_url .'";
var id_test = "' . $id_test . '";
var siteUrl = "' . $site_url . '";

</script>
';

if (is_user_logged_in()) {
    global $wpdb;

    // Get current user's username
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;

    echo '
    <script>
        
        var Currentusername = "' . $username . '";

    </script>
    ';

    // Get results for the current user and specific idtest (custom_number)
    $results_query = $wpdb->prepare("
        SELECT * FROM dictation_question 
        WHERE idtest = %s
        ",
        $current_username,
        $custom_number
            );
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

        $sql = "SELECT type_test, testname, transcript FROM dictation_question WHERE id_test = ?";


        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $custom_number); // 'i' is used for integer
        $stmt->execute();
        $result = $stmt->get_result();


        // Fetch result and store in variables
        if ($row = $result->fetch_assoc()) {
            $transcript = $row['transcript'];
            $type_test = $row['type_test'];
            $testname = $row['testname'];
        } else {
            $transcript = "No content available.";
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
    <style>
          .progress-item {
        display: flex;
        flex-direction: column;
        padding: 15px;
        margin-bottom: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.3s ease;
        border-left: 4px solid #3498db;
    }
    
    .progress-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .test-id {
        font-weight: bold;
        color: #2c3e50;
    }
    
    .test-date {
        color: #7f8c8d;
        font-size: 14px;
    }
    
    .progress-bar-container {
        height: 20px;
        background: #ecf0f1;
        border-radius: 10px;
        margin: 10px 0;
        overflow: hidden;
    }
    
    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3498db, #2ecc71);
        border-radius: 10px;
        transition: width 0.5s ease;
        position: relative;
    }
    
    .progress-percent {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-shadow: 0 0 2px rgba(0,0,0,0.5);
    }
    
    .progress-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .continue-btn, .delete-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .continue-btn {
        background: #2ecc71;
        color: white;
    }
    
    .continue-btn:hover {
        background: #27ae60;
    }
    
    .delete-btn {
        background: #e74c3c;
        color: white;
    }
    
    .delete-btn:hover {
        background: #c0392b;
    }
    
    .no-progress {
        text-align: center;
        color: #7f8c8d;
        padding: 30px;
        font-size: 18px;
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px;
        border-radius: 5px;
        color: white;
        z-index: 1000;
        opacity: 1;
        transition: opacity 0.5s ease;
    }
    
    .success {
        background: #2ecc71;
    }
    
    .error {
        background: #e74c3c;
    }
    .progressList_dashboard{
        
    /* max-height: 400px; */
    overflow-y: auto;
    padding: 10px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
</style>


           


           <div class="container-info-test">
           <h1><?php echo htmlspecialchars($testname); ?> <span class="check-icon"></span></h1>
           
        <div class="test-info">
            <p>Loại đề: <?php echo htmlspecialchars($type_test); ?></p>
            <p>203832 người đã luyện tập đề thi này</p>
            <p class="note">Chú ý: để được duy trì điểm scaled score (ví dụ trên điểm 990 TOEIC hoặc 9.0 cho IELTS), vui lòng chọn chế độ làm FULL TEST.</p>
        </div>
        <div class="progress-container">
            <div class="progress-header">
                <b>Tiến Độ Học Tập</b>
                <div class="progress-count" id="progressCount">Đang tải...</div>
            </div>
            
            <div id="progressList_dashboard"></div>
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
                    <form action="<?php echo $site_url?>/practice/dictation/<?php echo $custom_number?>/start/" method="get">
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
                    <a   class="btn-submit" href="<?php echo $site_url?>/practice/dictation/<?php echo $custom_number?>/start/" method="get">Bắt đầu bài thi</a>
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


</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadProgressList();
});


function loadProgressList() {
    fetch(`${siteUrl}/api/v1/check-progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: Currentusername,
            type_test: 'dictation',
            id_test: id_test
        })
    })
    .then(response => response.json())
    .then(result => {
        const progressList = document.getElementById('progressList_dashboard');
        const progressCount = document.getElementById('progressCount');
        
        // Clear previous content
        progressList.innerHTML = '';
        
        if (result.success) {
            if (result.found) {
                // Display single progress item
                progressCount.textContent = `Tiến độ hiện tại: ${result.data.progress}%`;
                
                progressList.innerHTML = `
                    <div class="progress-item">
                        <div class="progress-info">
                            <span class="test-id">Bài làm đã lưu</span>
                            <span class="test-date">${formatDate(result.data.date)}</span>
                        </div>
                        
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: ${result.data.progress || 0}%">
                                <span class="progress-percent">${result.data.progress || 0}%</span>
                            </div>
                        </div>
                        
                        <div class="progress-actions">
                            <button class="continue-btn" data-id="${id_test}" data-type="dictation">
                                <i class="fas fa-play"></i> Làm tiếp
                            </button>
                            <button class="delete-btn" data-id="${id_test}">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                `;
                
                // Add event listeners
                document.querySelector('.delete-btn').addEventListener('click', function() {
                    const idToDelete = this.getAttribute('data-id');
                    deleteProgressRecord(idToDelete);
                });
                
                document.querySelector('.continue-btn').addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type');
                    continueProgress(type, id);
                });
            } else {
                // No progress found
                progressCount.textContent = 'Chưa có tiến độ được lưu';
            }
        } else {
            throw new Error(result.message || 'Lỗi khi tải thông tin progress');
        }
    })
    .catch(error => {
        console.error('Error loading progress:', error);
        const progressList = document.getElementById('progressList_dashboard');
        progressList.innerHTML = `
            <div class="notification error">
                Lỗi: ${error.message}
            </div>
        `;
    });
}

// Function to delete progress and refresh
function deleteProgressRecord(id) {
    if (confirm('Bạn có chắc muốn xóa tiến độ này?')) {
        fetch(`${siteUrl}/api/v1/delete-progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: Currentusername,
                id_test: id,
                type_test: 'dictation'
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Show success notification
                showNotification('Xóa thành công!', 'success');
                
                // Clear and reload progress list
                const progressList = document.getElementById('progressList_dashboard');
                progressList.innerHTML = '';
                loadProgressList();
            } else {
                throw new Error(result.message || 'Lỗi khi xóa progress');
            }
        })
        .catch(error => {
            console.error('Error deleting progress:', error);
            showNotification('Lỗi khi xóa tiến độ: ' + error.message, 'error');
        });
    }
}


// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return 'Chưa cập nhật';
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: '2-digit', 
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('vi-VN', options);
}

// Function to delete progress
function deleteProgressRecord(id) {
    if (confirm('Bạn có chắc muốn xóa tiến độ này?')) {
        fetch(`${siteUrl}/api/v1/delete-progress`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: Currentusername,
                id_test: id,
                type_test: 'dictation'
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification('Xóa thành công!', 'success');
                loadProgressList();
            } else {
                throw new Error(result.message || 'Lỗi khi xóa progress');
            }
        })
        .catch(error => {
            console.error('Error deleting progress:', error);
            alert('Lỗi khi xóa tiến độ: ' + error.message);
        });
    }
}


function continueProgress(type, id) {
    let url = '';
    if (type === 'digitalsat') {
        url = `${siteUrl}/test/digitalsat/${id}/continue`;
    } else if (type === 'dictation') {
        url = `${siteUrl}/practice/dictation/${id}/continue`;
    } else if (type === 'shadowing') {
        url = `${siteUrl}/practice/shadowing/${id}/continue`;
    } else {
        showNotification('Loại bài kiểm tra không được hỗ trợ', 'error');
        return;
    }
    
    window.location.href = url;
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => document.body.removeChild(notification), 500);
    }, 3000);
}
</script>

    <?php
        

    
} else {
    echo '<p>Vui lòng đăng nhập để làm test.</p>';
}



get_footer(); // Gọi phần cuối trang (footer.php)
?>