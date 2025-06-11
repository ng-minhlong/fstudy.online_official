<?php
    $post_id = get_the_ID();
    $user_id = get_current_user_id();// Get the custom number field value
    $custom_number = get_query_var('id_package');
    $site_url = get_site_url();
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

    $sql_test = "SELECT id_test, package_category, package_name, package_detail FROM list_vocabulary_package WHERE id_test = ?";

    $stmt_test = $conn->prepare($sql_test);

    if ($stmt_test === false) {
        die('Lỗi MySQL prepare: ' . $conn->error);
    }


    $stmt_test->bind_param("s", $custom_number);
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
        $package_category = $row['package_category'];
        $package_name = $row['package_name'];
        $package_detail = json_decode($row['package_detail'], true);
    }

    add_filter('document_title_parts', function ($title) use ($package_name) {
        $title['title'] = $package_name; // Use the $package_name variable from the outer scope
        return $title;
    });

    get_header(); // Gọi phần đầu trang (header.php)
?>

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
    
    .test-list {
        margin-top: 30px;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .test-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background: white;
        transition: all 0.3s ease;
    }
    
    .test-item:hover {
        background: #f9f9f9;
    }
    
    .test-item:last-child {
        border-bottom: none;
    }
    
    .test-info-left {
        flex: 1;
    }
    
    .test-name {
        font-weight: 600;
        font-size: 16px;
        color: #333;
        margin-bottom: 5px;
    }
    
    .test-meta {
        font-size: 14px;
        color: #666;
    }
    
    .free-badge {
        background: #4CAF50;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        margin-left: 10px;
    }
    
    .paid-badge {
        background: #FF9800;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 12px;
        margin-left: 10px;
    }
    
    .test-actions {
        display: flex;
        gap: 10px;
    }
    
    .test-btn {
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .test-btn.test {
        background: #2196F3;
        color: white;
        border: 1px solid #2196F3;
    }
    
    .test-btn.test:hover {
        background: #0b7dda;
    }
    
    .test-btn.flashcard {
        background: white;
        color: #2196F3;
        border: 1px solid #2196F3;
    }
    
    .test-btn.flashcard:hover {
        background: #f1f9ff;
    }
    
    .test-list-header {
        background: #f5f5f5;
        padding: 15px 20px;
        font-weight: 600;
        color: #333;
        border-bottom: 1px solid #ddd;
    }
</style>

<div class="container-info-test">
    <h2><?php echo htmlspecialchars($package_name); ?>  </h2>
    
    <div class="test-info">
        <p><strong>Thời gian làm bài:</strong> 40 phút | 4 phần thi | 40 câu hỏi |  bình luận</p>
        <p>203832 người đã luyện tập đề thi này</p>
        <p class="note">Chú ý: để được duy trì điểm scaled score (ví dụ trên điểm 990 TOEIC hoặc 9.0 cho IELTS), vui lòng chọn chế độ làm FULL TEST.</p>
    </div>

    <?php if (!empty($package_detail)): ?>
    <div class="test-list">
        <div class="test-list-header">
            Danh sách các bài test trong package
        </div>
        <?php 
        // Get test details for each test in package_detail
        $test_ids = implode("','", $package_detail);
        $sql = "SELECT id_test, testname, token_need FROM list_test_vocabulary_book WHERE id_test IN ('$test_ids')";
        $result = $conn->query($sql);
        $tests = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $tests[$row['id_test']] = $row;
            }
        }
        
        foreach ($package_detail as $test_id): 
            if (isset($tests[$test_id])):
                $test = $tests[$test_id];
                $is_free = empty($test['token_need']) || $test['token_need'] == 0;
        ?>
        <div class="test-item">
            <div class="test-info-left">
                <div class="test-name">
                    <?php echo htmlspecialchars($test['testname']); ?>
                    <span class="<?php echo $is_free ? 'free-badge' : 'paid-badge'; ?>">
                        <?php echo $is_free ? 'FREE' : 'PAID'; ?>
                    </span>
                </div>
                <div class="test-meta">
                    <?php echo $is_free ? 'Miễn phí' : 'Yêu cầu ' . $test['token_need'] . ' token'; ?>
                </div>
            </div>
            <div class="test-actions">
                <a href="<?php echo $site_url; ?>/practice/vocabulary/package/<?php echo $custom_number; ?>/<?php echo $test_id; ?>/test/" class="test-btn test">Làm Test</a>
                <a href="<?php echo $site_url; ?>/practice/vocabulary/package/<?php echo $custom_number; ?>/<?php echo $test_id; ?>/flashcard/" class="test-btn flashcard">Flashcard</a>
            </div>
        </div>
        <?php 
            endif;
        endforeach; 
        ?>
    </div>
    <?php endif; ?>
</div>
<script>
    hidePreloader();
</script>

<?php 
    $conn->close();
    
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;

    get_footer(); // Gọi phần cuối trang (footer.php)
?>