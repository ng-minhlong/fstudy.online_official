<?php
function get_progress_api(WP_REST_Request $request) {
    global $wpdb;

    $params = $request->get_json_params();
    
    $username = sanitize_text_field($params['username'] ?? '');
    $type_test = sanitize_text_field($params['type_test'] ?? '');
    $id_test = sanitize_text_field($params['id_test'] ?? '');
    
    if (empty($username) || empty($type_test) || empty($id_test)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Missing required parameters'
        ], 400);
    }
    
    $table_name ='save_progress_test';
    
    // Get all progress records for this user
    $progress_data = $wpdb->get_var($wpdb->prepare(
        "SELECT progress FROM $table_name WHERE username = %s",
        $username
    ));
    
    if (!$progress_data) {
        return new WP_REST_Response([
            'success' => true,
            'found' => false,
            'message' => 'No progress records found for this user'
        ], 200);
    }
    
    // Decode the JSON array
    $progress_array = json_decode($progress_data, true);
    
    // Search for matching record
    $found_item = null;
    foreach ($progress_array as $item) {
        if (isset($item['type_test']) && 
            $item['type_test'] === $type_test && 
            isset($item['id_test']) && 
            $item['id_test'] === $id_test) {
            $found_item = $item;
            break;
        }
    }
    
    if ($found_item) {
        return new WP_REST_Response([
            'success' => true,
            'found' => true,
            'data' => [
                'progress' => $found_item['progress'],
                'date' => $found_item['date'] ?? null
            ]
        ], 200);
    }
    
    return new WP_REST_Response([
        'success' => true,
        'found' => false,
        'message' => 'No matching progress found'
    ], 200);
}