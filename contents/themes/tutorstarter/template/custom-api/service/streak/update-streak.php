<?php
function update_streak(WP_REST_Request $request) {
    global $wpdb;

    $user_id = (int)$request->get_param('user_id');
    $username = sanitize_text_field($request->get_param('username'));

    if (!$user_id || empty($username)) {
        return new WP_Error('missing_data', 'Thiếu user_id hoặc username', ['status' => 400]);
    }

    $now = current_time('mysql');
    $today = date('Y-m-d');

    $existing = $wpdb->get_row($wpdb->prepare("SELECT * FROM streak WHERE user_id = %d", $user_id));

    if ($existing) {
        $lastUpdateDate = date('Y-m-d', strtotime($existing->streak_last_update));
        if ($lastUpdateDate == $today) {
            return ['message' => 'Đã nhận streak hôm nay', 'streak_count' => (int)$existing->streak_count];
        } else {
            $new_count = $existing->streak_count + 1;
            $wpdb->update(
                'streak',
                ['streak_count' => $new_count, 'streak_last_update' => $now],
                ['user_id' => $user_id]
            );
            return ['message' => 'Cập nhật streak thành công', 'streak_count' => $new_count];
        }
    } else {
        $wpdb->insert('streak', [
            'user_id' => $user_id,
            'username' => $username,
            'streak_count' => 1,
            'streak_last_update' => $now
        ]);
        return ['message' => 'Tạo streak mới', 'streak_count' => 1];
    }
}