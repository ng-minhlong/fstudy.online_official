<?php

    function get_plan_today(WP_REST_Request $request) {
        global $wpdb;

        $username = sanitize_text_field($request->get_param('username'));
        if (empty($username)) {
            return new WP_Error('invalid_username', 'Vui lòng cung cấp username.', ['status' => 400]);
        }

        // Lấy kế hoạch từ database
        $table_name =  'user_plan_and_target';
        $plan_data = $wpdb->get_var(
            $wpdb->prepare("SELECT plan FROM $table_name WHERE username = %s", $username)
        );

        if (empty($plan_data)) {
            return ['plan' => []];
        }

        $plans = json_decode($plan_data, true);
        $today = date('j/n/Y'); // Ví dụ format: 5/2/2025

        // Lọc kế hoạch theo ngày hiện tại
        $today_plans = array_filter($plans, function ($plan) use ($today) {
            return isset($plan['date']) && $plan['date'] === $today;
        });

        return ['plan_today' => array_values($today_plans)];
    }
?>