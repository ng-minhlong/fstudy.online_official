<?php

function get_all_user_results_history(WP_REST_Request $request) {
    $current_username = sanitize_text_field($request->get_param('username'));
    if (empty($current_username)) {
        return new WP_Error('invalid_username', 'Vui lòng cung cấp username.', ['status' => 400]);
    }

    global $wpdb;

    $tables = [
        ['table' => 'save_user_result_digital_sat', 'result_column' => 'resulttest', 'type' => 'digitalsat', 'link' => 'digitalsat'],
        ['table' => 'save_user_result_ielts_reading', 'result_column' => 'overallband', 'type' => 'ieltsreadingtest', 'link' => 'ielts/r'],
        ['table' => 'save_user_result_ielts_speaking', 'result_column' => 'resulttest', 'type' => 'ieltsspeakingtests', 'link' => 'ielts/s'],
        ['table' => 'save_user_result_ielts_writing', 'result_column' => 'band_score', 'type' => 'ieltswritingtests', 'link' => 'ielts/w'],
        ['table' => 'save_user_result_ielts_listening', 'result_column' => 'overallband', 'type' => 'ieltslisteningtest', 'link' => 'ielts/l'],
        
        ['table' => 'save_user_result_topik_listening', 'result_column' => 'overallband', 'type' => 'topiklistening', 'link' => 'topik/l'],
        ['table' => 'save_user_result_topik_reading', 'result_column' => 'overallband', 'type' => 'topikreading', 'link' => 'topik/r'],
        ['table' => 'save_user_result_thptqg', 'result_column' => 'overallband', 'type' => 'thptqg', 'link' => 'thptqg'],
        ['table' => 'save_user_result_thptqg', 'result_column' => 'overallband', 'type' => 'thptqg', 'link' => 'thptqg']
    ];

    $all_results = [];

    foreach ($tables as $table_info) {
        $query = $wpdb->prepare(
            "SELECT dateform, testname, idtest, testsavenumber, {$table_info['result_column']} AS result 
             FROM {$table_info['table']} WHERE username = %s 
             ORDER BY dateform DESC", // Đã bỏ LIMIT 5
            $current_username
        );

        $results = $wpdb->get_results($query);

        foreach ($results as $row) {
            $all_results[] = [
                'date' => $row->dateform,
                'testname' => $row->testname,
                'idtest' => $row->idtest,
                'result' => $row->result,
                'testsavenumber' => $row->testsavenumber,
                'link' => $table_info['link'],
                'type' => $table_info['type']
            ];
        }
    }

    // Sắp xếp theo ngày làm bài
    usort($all_results, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
    
    return $all_results; // Trả về tất cả kết quả thay vì chỉ 5 kết quả
}
?>