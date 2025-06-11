<?php 


function get_user_target(WP_REST_Request $request) {
    global $wpdb;

    $username = $request->get_param('username');

    if (empty($username)) {
        return new WP_Error('no_username', 'Username is required', ['status' => 400]);
    }

    $table_name =  'user_plan_and_target';
    $target = $wpdb->get_var($wpdb->prepare("SELECT target FROM $table_name WHERE username = %s", $username));

    if (!$target) {
        return new WP_Error('no_target', 'Target not found for the provided username', ['status' => 404]);
    }

    return ['username' => $username, 'target' => $target];
}
?>