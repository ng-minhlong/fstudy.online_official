<?php
    function save_result_code(WP_REST_Request $request) {
        global $wpdb;

        // Lấy dữ liệu từ body
        $body = json_decode($request->get_body(), true);

        $username = sanitize_text_field($body['username']);
        $user_id = sanitize_text_field($body['user_id']);
        $date = current_time('mysql');
        $language = sanitize_text_field($body['lang']);
        $user_code = $body['code'];
        $result = json_encode($body['result'], JSON_UNESCAPED_UNICODE);
        $id_problems = sanitize_text_field($body['id']);
        $sessionID =  $body['sessionID'];

        $table = 'save_code_problems_history';

        $success = $wpdb->insert($table, [
            'username' => $username,
            'user_id' => $user_id,
            'date' => $date,
            'language' => $language,
            'user_code' => $user_code,
            'result' => $result,
            'id_problems' => $id_problems,
            'sessionID' => $sessionID
        ]);

        if ($success) {
            return new WP_REST_Response(['message' => 'Saved successfully'], 200);
        } else {
            return new WP_REST_Response(['message' => 'Error saving result'], 500);
        }
    }
