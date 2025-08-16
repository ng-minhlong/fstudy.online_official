

<?php
//$post_id = get_the_ID();
$user_id = get_current_user_id();
// Get the custom number field value
$custom_number = get_query_var('id_test');
//$commentcount = get_comments_number( $post->ID );
global $wpdb;
$id_test = $custom_number;
$site_url = get_site_url();
echo '
<script>

var siteUrl = "' . $site_url .'";
var id_test = "' . $id_test . '";

</script>
';
// Prepare the SQL statement
$test_info = $wpdb->get_row($wpdb->prepare(
    "SELECT testname, time, test_type, token_need, role_access, permissive_management, question_choose, tag, number_question, book 
    FROM digital_sat_test_list 
    WHERE id_test = %s", 
    $id_test
));
// Set testname and default the time to 40 minutes
$testname = $test_info ? $test_info->testname : '';
$time = $test_info ? $test_info->time : '';
$number_question = $test_info ? $test_info->number_question : '';
$book = $test_info ? $test_info->book : '';
$test_type = $test_info ? $test_info->test_type : '';

$token_need = $test_info ? $test_info->token_need : '';
$role_access = $test_info ? $test_info->role_access : '';
$permissive_management = $test_info ? $test_info->permissive_management : '';


// Add filter for document title
add_filter('document_title_parts', function ($title) use ($testname) {
    $title['title'] = $testname; // Use the $testname variable from the outer scope
    return $title;
});

get_header(); // Gọi phần đầu trang (header.php)




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
        SELECT * FROM save_user_result_digital_sat 
        WHERE username = %s 
        AND idtest = %s
        ORDER BY dateform DESC",
        $current_username,
        $custom_number
    );
    $results = $wpdb->get_results($results_query); ?>




    <!--Style Comment-->      

<style>
.comment-section {
    margin-top: 40px;
    border-top: 2px solid #eee;
    padding-top: 20px;
    font-family: Arial, sans-serif;
}
.comment-section h3 {
    margin-bottom: 15px;
    font-weight: bold;
}
.comment-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.comment-item {
    padding: 12px 15px;
    border-bottom: 1px solid #ddd;
}
.comment-author {
    font-weight: bold;
    margin-bottom: 5px;
}
.comment-content {
    margin-bottom: 5px;
}
.comment-date {
    font-size: 12px;
    color: #777;
}
.comment-form textarea {
    width: 100%;
    min-height: 80px;
    resize: vertical;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.comment-form button {
    margin-top: 8px;
    padding: 8px 15px;
    background: #0073aa;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.comment-form button:hover {
    background: #005f87;
}
</style>
<!--End Style Comment-->      






     <style>
           

    .column {
        float: left;
        width: 50%;
        padding: 10px;
    }

    .row:after {
        content: "";
        display: table;
        clear: both;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
    }

    .container-info-test {
        max-width: 100%;
        margin: auto;
        background: white;
        padding: 20px;
       
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
        background-color: #007bff;
        color: white;
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
    
    .h2-test {
        font-weight: bold;
        font-size: 25px;
    }
    
    .alert {
        margin-bottom: 0;
        position: relative;
        padding: .75rem 1.25rem;
        border: 1px solid transparent;
        border-radius: .35rem;
    }
    
    .alert-success {
        color: 1f5e39;
        background-color: #d8f0e2;
        border-color: #c8ead6;
    }
    
    /* Graph styles */
    .graph-container {
        width: 100%;
        margin: 20px 0;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .graph-title {
        font-size: 18px;
        margin-bottom: 15px;
        color: #333;
        font-weight: bold;
    }
    
    canvas {
        width: 100% !important;
        height: 400px !important;
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
    .horizontal-ad {
    margin: 20px 0;
    text-align: center;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 10px;
}

.horizontal-ad img {
    max-width: 100%;
    height: auto;
    display: block;
    margin: auto;
    border-radius: 6px;
    transition: transform 0.3s ease;
}

.horizontal-ad img:hover {
    transform: scale(1.02);
}
</style>



    <div class="container-info-test">
        <h1><?php echo esc_html($testname); ?><?php if ($results): ?> <span class="check-icon">✔️</span><?php endif; ?></h1>
        
        <div class="test-info">
            <p><strong>Thời gian làm bài:</strong> <?php echo esc_html($time); ?> phút | <?php echo esc_html($number_question); ?> câu</p>
            <p><strong>Resource:</strong> <?php echo esc_html($book); ?></p>
            <p><strong>Loại đề:</strong> <?php echo esc_html($test_type); ?></p>
            <p>203832 người đã luyện tập đề thi này</p>
            <p class="note">Chú ý: Đối với Loại đề Practice - Digital SAT, hệ thống sẽ hiển thị kết quả làm bài là số đáp án đúng trên tổng số câu (VD: 20/23) Để hiển thị kết quả như 1 bài test thật (Thang 1600), vui lòng chọn Loại đề Full Test. Tổng hợp đề Full Test tại đây</p>
        </div>
        <div class="progress-container">
            <div class="progress-header">
                <b>Tiến Độ Học Tập</b>
                <div class="progress-count" id="progressCount">Đang tải...</div>
            </div>
            
            <div id="progressList_dashboard"></div>
        </div>


        <?php if ($results): ?>
            <?php
            // Chuẩn bị dữ liệu cho biểu đồ
            $dates = [];
            $scores = [];
            $tooltipData = []; // Lưu thông tin bổ sung cho tooltip
            $max_score = 1600; // Điểm tối đa Digital SAT
            $min_score = 400; // Điểm tối thiểu
            $is_practice = ($test_type === 'Practice');
            
            foreach ($results as $result) {
                $dates[] = date('d/m', strtotime($result->dateform));
                $time_do_test = $result->timedotest;
                $raw_result = $result->resulttest;
                
                // Xử lý kết quả tùy theo loại đề
                if ($is_practice) {
                    // Practice: lấy % câu đúng
                    if (strpos($raw_result, '/') !== false) {
                        list($correct, $total) = explode('/', $raw_result);
                        $percentage = round(($correct / $total) * 100);
                        $scores[] = $percentage;
                        $tooltipData[] = [
                            'result' => $raw_result,
                            'time' => $time_do_test,
                            'percentage' => $percentage . '%'
                        ];
                    } else {
                        // Nếu không phải dạng X/Y, giữ nguyên giá trị
                        $scores[] = (int)$raw_result;
                        $tooltipData[] = [
                            'result' => $raw_result,
                            'time' => $time_do_test,
                            'percentage' => ''
                        ];
                    }
                } else {
                    // Full Test: lấy điểm số trực tiếp
                    $scores[] = (int)$raw_result;
                    $tooltipData[] = [
                        'result' => $raw_result,
                        'time' => $time_do_test,
                        'percentage' => ''
                    ];
                }
            }
            
            $average_score = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
            ?>
            
            <div class="graph-container">
                <div class="graph-title">Biểu đồ tiến bộ của bạn</div>
                <canvas id="resultsChart"></canvas>
            </div>
            
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('resultsChart').getContext('2d');
                    const dates = <?php echo json_encode($dates); ?>;
                    const scores = <?php echo json_encode($scores); ?>;
                    const tooltipData = <?php echo json_encode($tooltipData); ?>;
                    const averageScore = <?php echo $average_score; ?>;
                    const isPractice = <?php echo $is_practice ? 'true' : 'false'; ?>;
                    const maxScore = isPractice ? 100 : <?php echo $max_score; ?>;
                    const minScore = isPractice ? 0 : <?php echo $min_score; ?>;
                    const yAxisTitle = isPractice ? 'Tỉ lệ % câu đúng' : 'Điểm số (400-1600)';
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [
                                {
                                    label: isPractice ? 'Tỉ lệ % câu đúng' : 'Điểm số',
                                    data: scores,
                                    borderColor: '#4e73df',
                                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                                    tension: 0.3,
                                    borderWidth: 3,
                                    fill: true,
                                    pointBackgroundColor: '#4e73df',
                                    pointRadius: 5,
                                    pointHoverRadius: 7
                                },
                                {
                                    label: 'Trung bình',
                                    data: Array(dates.length).fill(averageScore),
                                    borderColor: '#e74a3b',
                                    backgroundColor: 'rgba(0, 0, 0, 0)',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    pointRadius: 0
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: isPractice,
                                    min: isPractice ? 0 : minScore - 50,
                                    max: isPractice ? 100 : maxScore + 50,
                                    title: {
                                        display: true,
                                        text: yAxisTitle
                                    },
                                    ticks: {
                                        stepSize: isPractice ? 10 : 100,
                                        callback: function(value) {
                                            return isPractice ? value + '%' : value;
                                        }
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const data = tooltipData[context.dataIndex];
                                            let label = context.dataset.label + ': ' + 
                                                (isPractice ? data.percentage : context.parsed.y);
                                            
                                            // Thêm thông tin phụ
                                            label += '\nKết quả: ' + data.result;
                                            label += '\nThời gian: ' + data.time;
                                            
                                            return label.split('\n'); // Hiển thị thành nhiều dòng
                                        }
                                    }
                                },
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 20
                                    }
                                }
                            }
                        }
                    });
                });
            </script>
            
            <div class="horizontal-ad">
                <img src="https://your-site.com/path-to-ad/digitalsat-horizontal.jpg" alt="Advertisement">
            </div>
            <div class="results">
                <p class="h2-test">Kết quả làm bài của bạn:</p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ngày làm</th>
                            <th>Đề thi</th>
                            <th>Kết quả</th>
                            <th>Thời gian làm bài</th>
                            <th>Chi tiết bài làm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                            <tr>
                                <td><?php echo esc_html($result->dateform); ?></td>
                                <td><?php echo esc_html($result->testname); ?></td>
                                <td><?php echo esc_html($result->resulttest); ?></td>
                                <td><?php echo esc_html($result->timedotest); ?></td>
                                <td>
                                    <a href="<?php echo $site_url; ?>/digitalsat/result/<?php echo esc_html($result->testsavenumber); ?>">
                                        Xem bài làm
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Bạn chưa làm test này.</p>
        <?php endif; ?>
    </div>
            
            


        <div class="practice-options">
    <p class="options-header">
        <span class="option active-bar" id="full-test">Làm full test</span>  
        <span class="option" id="practice">Luyện tập</span> 
        <span class="option" id="discussion">Thảo luận</span>
    </p>

    <div id="tips-container">
        <div id="full-test-content">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Pro tips:</h4> <hr>
                <p>Sẵn sàng để bắt đầu làm full test? Để đạt được kết quả tốt nhất, bạn cần dành ra 40 phút cho bài test này.</p>
            </div><br>
            <a id="start-test-btn"  class="btn-submit" href="<?php echo $site_url?>/test/digitalsat/<?php echo $custom_number?>/start/">Bắt đầu bài thi</a>
        </div>
        <div id="practice-content" style="display: none;">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Pro tips:</h4> <hr>
                <p>Hình thức luyện tập từng phần và chọn mức thời gian phù hợp sẽ giúp bạn tập trung vào giải đúng các câu hỏi thay vì phải chịu áp lực hoàn thành bài thi.</p>    
            </div><br>

            <p class="h2-test">Giới hạn thời gian (Để trống để làm bài không giới hạn):</p>
            <form id="practice-form" action="<?php echo $site_url?>/test/digitalsat/<?php echo $custom_number?>/start/" method="get">
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
        </div>
    </div>
</div>





        <!-- Comment Section-->
        <div class="comment-section" id="comment">
    <h3>Bình luận</h3>
    <ul class="comment-list" id="commentList">Đang tải bình luận...</ul>

    <div class="comment-form">
        <textarea id="commentContent" placeholder="Nhập bình luận của bạn..."></textarea>
        <button id="submitComment">Gửi bình luận</button>
    </div>
</div>

        <!-- Script Comment--> 
        <script>
document.addEventListener('DOMContentLoaded', function () {
    const commentList = document.getElementById('commentList');
    const commentContent = document.getElementById('commentContent');
    const submitBtn = document.getElementById('submitComment');

    const currentUser = "<?php echo is_user_logged_in() ? esc_js(wp_get_current_user()->display_name) : ''; ?>";
    const postId = "<?php echo $id_test; ?>"; // id_test từ code của bạn
    const postType = "digitalsat"; // loại post tùy bạn đặt

    // Load comments
    function loadComments() {
        fetch(`${siteUrl}/api/v1/comment?post_id=${postId}&post_type=${postType}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    commentList.innerHTML = '';
                    data.data.forEach(cmt => {
                        const li = document.createElement('li');
                        li.className = 'comment-item';
                        li.innerHTML = `
                            <div class="comment-author">${cmt.author_name}</div>
                            <div class="comment-content">${cmt.content}</div>
                            <div class="comment-date">${cmt.created_at}</div>
                        `;
                        commentList.appendChild(li);
                    });
                } else {
                    commentList.innerHTML = '<li>Chưa có bình luận nào.</li>';
                }
            })
            .catch(err => {
                console.error(err);
                commentList.innerHTML = '<li>Lỗi tải bình luận.</li>';
            });
    }

    // Submit comment
    submitBtn.addEventListener('click', function () {
        const content = commentContent.value.trim();
        if (!content) {
            alert('Vui lòng nhập nội dung bình luận.');
            return;
        }

        fetch(`${siteUrl}/api/v1/comment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                post_id: postId,
                post_type: postType,
                user_id: <?php echo get_current_user_id() ?: 'null'; ?>,
                author_name: currentUser || 'Khách',
                content: content,
                status: 'approved'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                commentContent.value = '';
                loadComments(); // reload list
            } else {
                alert(data.message || 'Lỗi gửi bình luận.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Có lỗi khi gửi bình luận.');
        });
    });

    loadComments();
});
</script>
<!--End Script Comment-->      

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
            type_test: 'digitalsat',
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