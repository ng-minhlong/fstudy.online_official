<?php
function get_all_progress_api(WP_REST_Request $request) {
    global $wpdb;

    $params = $request->get_json_params();
    $username = sanitize_text_field($params['username'] ?? '');

    if (empty($username)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Missing username parameter'
        ], 400);
    }

    $table_name = 'save_progress_test';
    
    // Check if user has any progress record
    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT progress, progress_number FROM $table_name WHERE username = %s",
        $username
    ));

    // No record found for this username
    if (!$row) {
        return new WP_REST_Response([
            'success' => true,
            'data' => [],
            'progress_number' => 0,
            'message' => 'No progress records found'
        ], 200);
    }

    // Decode progress data or return empty array if null/empty
    $progress_data = json_decode($row->progress, true) ?: [];

    return new WP_REST_Response([
        'success' => true,
        'data' => $progress_data,
        'progress_number' => $row->progress_number,
        'message' => 'Successfully retrieved progress'
    ], 200);
}