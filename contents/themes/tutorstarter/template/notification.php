<?php
/*
Template Name: Notifications Template
*/


add_filter('document_title_parts', function ($title) {
 
    $title['title'] = sprintf('Notification');

return $title;
});





/*
 

sửa tại C:\xampp\htdocs\contents\themes\tutorstarter\tutor\dashboard\notification.php 
 


*/





/* 

Cần thêm file tương tự notification.php ở plugin -> tutor -> templates -> dashboard 



*/




get_header();

// Kiểm tra nếu người dùng đã đăng nhập
if (!is_user_logged_in()) {
    echo '<p>Bạn cần đăng nhập để xem thông báo.</p>';
    get_footer();
    exit;
}

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
$user_comment_ids = array_map(function($comment) {
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
<!-- 
 

sửa tại C:\xampp\htdocs\wordpress\contents\themes\tutorstarter\tutor\dashboard\notification.php 
 


-->
<div class="notifications">
    <b>Thông báo của bạn</b>
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
<!-- 
 

sửa tại C:\xampp\htdocs\wordpress\contents\themes\tutorstarter\tutor\dashboard\notification.php 
 


-->
<?php
get_footer();
