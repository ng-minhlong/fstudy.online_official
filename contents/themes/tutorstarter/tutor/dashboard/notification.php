<?php
/*
Template Name: Notifications Template
*/

echo '<title>Notification</title>';


// Lấy thông tin user hiện tại
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Truy vấn tất cả bình luận của user hiện tại
global $wpdb;
$user_comments = $wpdb->get_results("
    SELECT comment_ID 
    FROM {$wpdb->prefix}comments 
    WHERE user_id = $user_id
");

// Nếu user không có bình luận nào
if (empty($user_comments)) {
    echo '<p>Bạn không có thông báo nào.</p>';
    get_footer();
    exit;
}

// Lấy danh sách comment_ID của user hiện tại
$user_comment_ids = array_map(function ($comment) {
    return $comment->comment_ID;
}, $user_comments);

// Tìm các phản hồi (comment_parent khớp với comment_ID của user)
$notifications = $wpdb->get_results("
    SELECT c.comment_content AS reply_content, c.comment_date, c.comment_author, 
           p.post_title, p.ID AS post_id
    FROM {$wpdb->prefix}comments AS c
    LEFT JOIN {$wpdb->prefix}posts AS p ON c.comment_post_ID = p.ID
    WHERE c.comment_parent IN (" . implode(',', $user_comment_ids) . ")
    ORDER BY c.comment_date DESC
");
?>

<div class="tabs">
    <button class="tab-button active" data-tab="tab1">Comment from users</button>
    <button class="tab-button" data-tab="tab2">Notification from System</button>
    <button class="tab-button" data-tab="tab3">Tab 3</button>
</div>

<div class="tab-content">
    <div id="tab1" class="tab-pane active">
        <div class="notifications">
            <?php if (!empty($notifications)) : ?>
                <ul>
                    <?php foreach ($notifications as $notification) : ?>
                        <li>
                            <p>
                                <strong><?php echo esc_html($notification->comment_author); ?></strong> đã trả lời comment của bạn 
                                tại bài đăng: 
                                <a href="<?php echo get_permalink($notification->post_id); ?>">
                                    <?php echo esc_html($notification->post_title); ?>
                                </a>
                            </p>
                            <p><em>Phản hồi: <?php echo esc_html($notification->reply_content); ?></em></p>
                            <p><small><?php echo date('d/m/Y H:i', strtotime($notification->comment_date)); ?></small></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>Bạn không có thông báo nào.</p>
            <?php endif; ?>
        </div>
    </div>
    <div id="tab2" class="tab-pane">
        <p>Nội dung cho Tab 2. Bạn có thể thay đổi nội dung này.</p>
    </div>
    <div id="tab3" class="tab-pane">
        <p>Nội dung cho Tab 3. Bạn có thể thay đổi nội dung này.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');

        tabButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Loại bỏ class active khỏi tất cả các nút và tab
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                // Thêm class active cho nút và tab được chọn
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });
    });
</script>

<style>
    .tabs {
        margin-bottom: 20px;
    }

    .tab-button {
        padding: 10px 20px;
        border: 1px solid #ccc;
        background: #f9f9f9;
        cursor: pointer;
        margin-right: 5px;
    }

    .tab-button.active {
        background: #0073aa;
        color: #fff;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }
</style>

<?php
?>
