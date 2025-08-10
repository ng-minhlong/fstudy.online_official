<?php
function post_comment(WP_REST_Request $request) {
    global $wpdb;

    $table_name =  'comments'; // Nếu table không có prefix thì đổi lại

    // Lấy dữ liệu từ request
    $post_id       = intval($request->get_param('post_id'));
    $post_type     = sanitize_text_field($request->get_param('post_type'));
    $user_id       = intval($request->get_param('user_id'));
    $author_name   = sanitize_text_field($request->get_param('author_name'));
    $author_email  = sanitize_email($request->get_param('author_email'));
    $content       = wp_kses_post($request->get_param('content'));
    $parent_id     = intval($request->get_param('parent_id'));
    $status        = sanitize_text_field($request->get_param('status'));

    // Validate cơ bản
    if (empty($post_id) || empty($post_type) || empty($author_name) || empty($content)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Thiếu dữ liệu bắt buộc.'
        ], 400);
    }

    // Nếu status không hợp lệ thì để mặc định "approved"
    $allowed_status = ['pending', 'approved', 'spam'];
    if (!in_array($status, $allowed_status)) {
        $status = 'approved';
    }

    // Insert vào DB
    $result = $wpdb->insert($table_name, [
        'post_id'       => $post_id,
        'post_type'     => $post_type,
        'user_id'       => $user_id ?: null,
        'author_name'   => $author_name,
        'author_email'  => $author_email ?: null,
        'content'       => $content,
        'parent_id'     => $parent_id ?: null,
        'created_at'    => current_time('mysql'),
        'updated_at'    => current_time('mysql'),
        'status'        => $status
    ], [
        '%d', '%s', '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s'
    ]);

    if ($result === false) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Lỗi khi lưu comment.',
            'error'   => $wpdb->last_error
        ], 500);
    }

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Comment đã được lưu.',
        'data'    => [
            'id' => $wpdb->insert_id
        ]
    ], 200);
}



function get_comments_list(WP_REST_Request $request) {
    global $wpdb;

    $table_name ='comments';

    $post_id   = intval($request->get_param('post_id'));
    $post_type = sanitize_text_field($request->get_param('post_type'));

    if (empty($post_id) || empty($post_type)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Thiếu post_id hoặc post_type.'
        ], 400);
    }

    $comments = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name
             WHERE post_id = %d
               AND post_type = %s
               AND status = 'approved'
             ORDER BY created_at ASC",
            $post_id,
            $post_type
        )
    );

    return new WP_REST_Response([
        'success' => true,
        'data'    => $comments
    ], 200);
}
