
<?php
function get_streak(WP_REST_Request $request) {
    global $wpdb;

    $user_id = (int)$request->get_param('user_id');
    if (!$user_id) {
        return new WP_Error('missing_user_id', 'Thiếu user_id', ['status' => 400]);
    }

    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM streak WHERE user_id = %d", $user_id));

    if (!$result) {
        return [
            'streak_count' => 0,
            'streak_last_update' => null
        ];
    }

    return [
        'streak_count' => (int)$result->streak_count,
        'streak_last_update' => $result->streak_last_update
    ];
}