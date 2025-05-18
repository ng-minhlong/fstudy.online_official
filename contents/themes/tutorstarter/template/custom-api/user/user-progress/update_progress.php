<?php
function update_progress_api(WP_REST_Request $request) {
    global $wpdb;

    $params = $request->get_json_params();

    $username = sanitize_text_field($params['username'] ?? '');
    $id_test = sanitize_text_field($params['id_test'] ?? '');
    $progress = intval($params['progress'] ?? 0);
    $progress_id = intval($params['progress_id'] ?? 0);

    $date = sanitize_text_field($params['date'] ?? current_time('mysql'));
    $percent_completed  = sanitize_text_field($params['percent_completed'] ?? '');
    $testname = sanitize_text_field($params['testname'] ?? '');
    $type_test = sanitize_text_field($params['type_test'] ?? '');

    // Xử lý detail_data - quan trọng
    $detail_data = $params['detail_data'] ?? [];
    if (is_string($detail_data)) {
        $detail_data = json_decode($detail_data, true);
    }
    $detail_data_serialized = wp_json_encode($detail_data);


    if (empty($username) || empty($id_test)|| empty($type_test)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Thiếu thông tin bắt buộc'
        ], 400);
    }

    $new_entry = [
        'id_test' => $id_test,
        'testname' => $testname,
        'progress' => $progress,
        'progress_id' => $progress_id,
        'type_test' => $type_test,
        'detail_data' => $detail_data_serialized,
        'percent_completed' => $percent_completed,
        'date' => $date
    ];

    $table_name =  'save_progress_test';
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE username = %s", 
        $username
    ));

    if ($existing) {
        $progress_data = json_decode($existing->progress, true) ?: [];
        $found = false;
        $progress_number = $existing->progress_number ?? count($progress_data);

        // Check if we're adding a new record (not updating existing)
        foreach ($progress_data as &$entry) {
            if ($entry['id_test'] == $id_test) {
                $entry = $new_entry;
                $found = true;
                break;
            }
        }

        if (!$found) {
            if ($progress_number >= 20) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Bạn đã đạt đến số lượng tối đa (20) progress có thể lưu. Vui lòng xóa bớt record cũ để được thêm mới.'
                ], 400);
            }
            $progress_data[] = $new_entry;
            $progress_number++;
        }

        $result = $wpdb->update(
            $table_name,
            [
                'progress' => json_encode($progress_data),
                'progress_number' => $progress_number
            ],
            ['username' => $username],
            ['%s', '%d'],
            ['%s']
        );
    } else {
        $progress_data = [$new_entry];
        $progress_number = 1;
        $result = $wpdb->insert(
            $table_name,
            [
                'username' => $username,
                'progress' => json_encode($progress_data),
                'progress_number' => $progress_number
            ],
            ['%s', '%s', '%d']
        );
    }

    if ($result !== false) {
        return new WP_REST_Response([
            'success' => true,
            'message' => 'Lưu thành công',
            'progress_number' => $progress_number
        ], 200);
    }

    return new WP_REST_Response([
        'success' => false,
        'message' => 'Lỗi khi lưu vào database'
    ], 500);
}