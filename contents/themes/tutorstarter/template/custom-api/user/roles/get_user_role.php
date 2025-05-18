<?php
function get_user_role(WP_REST_Request $request) {
    global $wpdb;
    
    $parameters = $request->get_params();
    $user_id = isset($parameters['user_id']) ? intval($parameters['user_id']) : 0;
    
    if (empty($user_id)) {
        return new WP_REST_Response(array(
            'status' => 'error',
            'message' => 'User ID is required'
        ), 400);
    }
    
    $user_roles_table = 'user_role';
    $existing_user = $wpdb->get_row($wpdb->prepare(
        "SELECT roles FROM $user_roles_table WHERE user_id = %d",
        $user_id
    ));
    
    if ($existing_user) {
        return new WP_REST_Response(array(
            'status' => 'success',
            'roles' => json_decode($existing_user->roles, true)
        ), 200);
    }
    
    return new WP_REST_Response(array(
        'status' => 'not_found',
        'message' => 'User role not found'
    ), 404);
}