<?php
function report_broken_link(WP_REST_Request $request) {
    global $wpdb;

    // Lấy dữ liệu từ request
    $report_by_username = sanitize_text_field($request->get_param('report_by_username'));
    $report_by_user_id  = intval($request->get_param('report_by_user_id'));
    $course_id          = intval($request->get_param('course_id'));
    $source_type        = sanitize_text_field($request->get_param('source_type'));
    $source             = sanitize_text_field($request->get_param('source'));
    $error_log          = sanitize_text_field($request->get_param('error_log'));

    // Kiểm tra và liệt kê các field thiếu
    $missing = [];
    if (empty($report_by_username)) $missing[] = 'report_by_username';
    if (empty($report_by_user_id))  $missing[] = 'report_by_user_id';
    if (empty($course_id))          $missing[] = 'course_id';
    if (empty($source_type))        $missing[] = 'source_type';
    if (empty($source))             $missing[] = 'source';
    if (empty($error_log))          $missing[] = 'error_log';

    if (!empty($missing)) {
        return new WP_REST_Response(
            [
                'error'          => 'Missing required fields',
                'missing_fields' => $missing
            ],
            400
        );
    }

    // Tạo UUID cho report_id
    if (function_exists('wp_generate_uuid4')) {
        $report_id = wp_generate_uuid4();
    } else {
        $report_id = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    $date_created = current_time('mysql');
    $table_name   =  'report_lessons_link';

    $result = $wpdb->insert($table_name, [
        'report_id'          => $report_id,
        'date_created'       => $date_created,
        'report_by_username' => $report_by_username,
        'report_by_user_id'  => $report_by_user_id,
        'course_id'          => $course_id,
        'source_type'        => $source_type,
        'source'             => $source,
        'error_log'          => $error_log,
    ]);

    if ($result === false) {
        return new WP_REST_Response(['error' => 'Database insert failed'], 500);
    }

    return new WP_REST_Response(['success' => true, 'report_id' => $report_id], 200);
}
