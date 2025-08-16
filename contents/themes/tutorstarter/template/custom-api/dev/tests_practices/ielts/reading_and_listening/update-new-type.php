<?php
    function update_new_type_ielts_reading_listening( WP_REST_Request $request ) {
        global $wpdb;

        $params = $request->get_json_params();
        if ( empty($params) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Empty request body'), 400 );
        }

        $idTest     = isset($params['idTest']) ? intval($params['idTest']) : 0;
        $partIndex  = isset($params['partIndex']) ? intval($params['partIndex']) : 0;
        $typeRequest= isset($params['typeRequest']) ? sanitize_text_field($params['typeRequest']) : '';
        $range      = isset($params['range']) ? $params['range'] : null; // {start: "1", end:"6"}
        $optionStr  = isset($params['option_choice']) ? $params['option_choice'] : ''; // ví dụ "A,B,C,D"

        if ( ! $idTest || ! in_array($partIndex, array(1,2,3), true) || $typeRequest !== 'ielts_reading' ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Invalid parameters'), 400 );
        }
        if ( ! is_array($range) || empty($range['start']) || empty($range['end']) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Missing range'), 400 );
        }
        if ( empty($optionStr) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Missing option_choice'), 400 );
        }

        // chuẩn hóa option_choice thành mảng
        $option_choice = array_map( 'trim', explode(',', $optionStr) );
        $option_choice = array_filter($option_choice, function($v){ return $v !== ''; });
        if ( empty($option_choice) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'option_choice invalid'), 400 );
        }

        // 1) lấy question_choose từ ielts_reading_test_list
        $table_tests =  'ielts_reading_test_list';
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT question_choose FROM {$table_tests} WHERE id_test = %d", $idTest
        ), ARRAY_A );
        if ( ! $row ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Test not found'), 404 );
        }

        $parts_raw = array_map('trim', explode(',', $row['question_choose']));
        if ( ! isset($parts_raw[$partIndex - 1]) ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'partIndex not found in question_choose'), 400 );
        }

        $id_part = intval($parts_raw[$partIndex - 1]);
        if ( ! $id_part ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Invalid id_part parsed'), 400 );
        }

        // table part
        $table_part = 'ielts_reading_part_' . $partIndex . '_question';

        // lấy group_question hiện tại
        $existing_row = $wpdb->get_row(
            $wpdb->prepare("SELECT group_question FROM {$table_part} WHERE id_part = %d LIMIT 1", $id_part),
            ARRAY_A
        );
        if ( ! $existing_row ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'No row found for id_part'), 404 );
        }

        $group_question = array();
        if ( ! empty($existing_row['group_question']) ) {
            $decoded = json_decode($existing_row['group_question'], true);
            if ( json_last_error() === JSON_ERROR_NONE && is_array($decoded) ) {
                $group_question = $decoded;
            }
        }

        // tìm group trùng range
        $range_str = $range['start'] . '-' . $range['end'];
        $found = false;
        foreach ( $group_question as &$g ) {
            if ( isset($g['group']) && trim($g['group']) === $range_str ) {
                $g['option_choice'] = array_values($option_choice); // thêm/ghi đè option_choice
                $found = true;
                break;
            }
        }
        unset($g);

        if ( ! $found ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'Group ' . $range_str . ' not found'), 404 );
        }

        // encode lại và update
        $group_json = json_encode($group_question, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $updated = $wpdb->update(
            $table_part,
            array( 'group_question' => $group_json ),
            array( 'id_part' => $id_part ),
            array( '%s' ),
            array( '%d' )
        );

        if ( $updated === false ) {
            return new WP_REST_Response( array('success' => false, 'message' => 'DB update error: '.$wpdb->last_error), 500 );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'message' => 'Group updated with option_choice',
            'group_question' => $group_question
        ), 200 );
    }
?>
