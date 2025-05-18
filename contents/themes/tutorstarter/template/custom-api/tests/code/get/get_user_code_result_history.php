
<?php

function get_code_result_history(WP_REST_Request $request) {
    global $wpdb;

    $body = json_decode($request->get_body(), true);
    $sessionID = sanitize_text_field($body['sessionID']);
    $id_problems = sanitize_text_field($body['id_problems']);

    $table = 'save_code_problems_history';

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT username, language, user_code, result, date 
             FROM $table 
             WHERE sessionID = %s AND id_problems = %s 
             ORDER BY date DESC LIMIT 1",
            $sessionID, $id_problems
        ),
        ARRAY_A
    );

    if ($row) {
        return new WP_REST_Response([
            'status' => 'valid',
            'data' => $row
        ], 200);
    } else {
        return new WP_REST_Response([
            'status' => 'invalid'
        ], 200);
    }
}
