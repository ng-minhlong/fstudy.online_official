<?php

    function get_user_logs(WP_REST_Request $request) {
        global $wpdb;
        $current_user = wp_get_current_user();
        
        $table_name =  'user_logs';
        $logs = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d ORDER BY time DESC", $current_user->ID)
        );

        return new WP_REST_Response(array(
            'success' => true,
            'data' => $logs
        ), 200);
    }
?>