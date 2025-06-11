<?php


    function get_user_token(WP_REST_Request $request) {
        global $wpdb;

        $username = $request->get_param('username');

        if (empty($username)) {
            return new WP_Error('no_username', 'Username is required', ['status' => 400]);
        }

        $table_name =  'user_token';
        $token = $wpdb->get_var($wpdb->prepare("SELECT token, token_practice FROM $table_name WHERE username = %s", $username));
        $token_practice = $wpdb->get_var($wpdb->prepare("SELECT token_practice FROM $table_name WHERE username = %s", $username));

        if (!$token) {
            return new WP_Error('no_token', 'Token not found for the provided username', ['status' => 404]);
        }
        if (!$token_practice) {
            return new WP_Error('no_token_practice', 'Token Practice not found for the provided username', ['status' => 404]);
        }

        return ['username' => $username, 'token' => $token, 'token_practice' => $token_practice];
    }

?>