<?php


    function get_user_notation_by_username( $data ) {
        global $wpdb;

        // Lấy giá trị 'username' từ dữ liệu POST
        $username = isset($data['username']) ? sanitize_text_field($data['username']) : '';

        // Nếu không có username, trả về lỗi
        if ( empty($username) ) {
            return new WP_REST_Response( 'Username is required', 400 );
        }

        // Truy vấn cơ sở dữ liệu để lấy dữ liệu từ bảng 'notation' theo username
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM notation WHERE username = %s", 
                $username
            )
        );

        // Kiểm tra nếu không có dữ liệu nào được tìm thấy
        if ( empty($results) ) {
            return new WP_REST_Response( 'No notations found for this username', 404 );
        }

        // Trả về kết quả dưới dạng JSON
        return new WP_REST_Response( $results, 200 );
    }
?>