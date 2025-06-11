<?php

    function get_all_digital_sat_result(WP_REST_Request $request) {
        global $wpdb;

        // Lấy username từ request
        $username = sanitize_text_field($request->get_param('username'));

        if (empty($username)) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Username is required.'
            ), 400);
        }

        // Truy vấn dữ liệu
        $table_name =  'save_code_problems_history';
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name WHERE username = %s ORDER BY date DESC", $username),
            ARRAY_A
        );

        // Trả về dữ liệu
        return new WP_REST_Response(array(
            'success' => true,
            'data' => $results
        ), 200);
    }
?>
