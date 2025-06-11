<?php

function extract_current_and_prepare_ai_route() {
	require_once(ABSPATH . 'wp-load.php'); // Đúng vì ABSPATH là đường dẫn tuyệt đối
    global $wpdb;

    // Truy vấn bảng order_and_prompt_api_list
    $query = "SELECT list_name_endpoint_order, last_use_end_point 
              FROM order_and_prompt_api_list 
              WHERE number = 1";
    $result = $wpdb->get_row($query, ARRAY_A);

    $now_end_point = '';
    $next_end_point = '';

    if ($result) {
        $list_name_endpoint_order = json_decode($result['list_name_endpoint_order'], true);
        $last_use_end_point = $result['last_use_end_point'];

        if (is_array($list_name_endpoint_order)) {
            // Tìm ID của last_use_end_point
            $current_id = null;
            foreach ($list_name_endpoint_order as $item) {
                if ($item['name'] === $last_use_end_point) {
                    $current_id = $item['id'];
                    break;
                }
            }

            // Xác định now_end_point
            if ($current_id !== null) {
                $next_id = $current_id + 1;
                foreach ($list_name_endpoint_order as $item) {
                    if ($item['id'] === $next_id) {
                        $now_end_point = $item['name'];
                        break;
                    }
                }
            }

            // Nếu không tìm thấy next_id, lấy id đầu tiên
            if (!$now_end_point) {
                foreach ($list_name_endpoint_order as $item) {
                    if ($item['id'] === 1) {
                        $now_end_point = $item['name'];
                        break;
                    }
                }
            }

            // Xác định next_end_point
            if ($now_end_point) {
                foreach ($list_name_endpoint_order as $item) {
                    if ($item['name'] === $now_end_point) {
                        $current_id = $item['id'];
                        break;
                    }
                }

                if ($current_id !== null) {
                    $next_id = $current_id + 1;
                    foreach ($list_name_endpoint_order as $item) {
                        if ($item['id'] === $next_id) {
                            $next_end_point = $item['name'];
                            break;
                        }
                    }
                }

                if (!$next_end_point) {
                    foreach ($list_name_endpoint_order as $item) {
                        if ($item['id'] === 1) {
                            $next_end_point = $item['name'];
                            break;
                        }
                    }
                }
            }
        }
    }

    // Nếu có now_end_point, tìm trong api_key_route
    $api_info = [];
    if ($now_end_point) {
        $query = $wpdb->prepare(
            "SELECT name_end_point, api_endpoint_url, api_key, updated_time, type, all_time_use_number, today_time_use_number 
             FROM api_key_route 
             WHERE name_end_point = %s",
            $now_end_point
        );
        $api_info = $wpdb->get_row($query, ARRAY_A);
    }

    // Trả về JSON
    wp_send_json([
        'now_end_point' => $now_end_point,
        'next_end_point' => $next_end_point,
        'api_info' => $api_info ?: null,
    ]);
}
?>