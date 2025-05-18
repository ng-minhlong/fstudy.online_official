<?php
function delete_progress_api(WP_REST_Request $request) {
    global $wpdb;

    $params = $request->get_json_params();
    $username = sanitize_text_field($params['username'] ?? '');
    $id_test_to_delete = sanitize_text_field($params['id_test'] ?? '');

    if (empty($username) || empty($id_test_to_delete)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Thiếu thông tin bắt buộc'
        ], 400);
    }

    $table_name = 'save_progress_test';
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE username = %s", 
        $username
    ));

    if (!$existing) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Không tìm thấy progress của người dùng'
        ], 404);
    }

    $progress_data = json_decode($existing->progress, true) ?: [];
    $new_progress_data = [];
    $deleted = false;

    foreach ($progress_data as $entry) {
        if ($entry['id_test'] == $id_test_to_delete) {
            $deleted = true;
            continue;
        }
        $new_progress_data[] = $entry;
    }

    if (!$deleted) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Không tìm thấy record để xóa'
        ], 404);
    }

    $progress_number = $existing->progress_number - 1;

    $result = $wpdb->update(
        $table_name,
        [
            'progress' => json_encode($new_progress_data),
            'progress_number' => $progress_number
        ],
        ['username' => $username],
        ['%s', '%d'],
        ['%s']
    );

    if ($result !== false) {
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Xóa thành công',
            'progress_number' => $progress_number,
            'remaining_progress' => $new_progress_data
        ], 200);
    }

    return new WP_REST_Response([
        'success' => false,
        'message' => 'Lỗi khi xóa record'
    ], 500);
}