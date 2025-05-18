<?php

function get_practice_code_tests_all($request) {
    global $wpdb;

    $params = $request->get_json_params();
    $difficulty = isset($params['difficulty']) ? sanitize_text_field($params['difficulty']) : '';
    $page = isset($params['page']) ? max(1, intval($params['page'])) : 1;
    $limit = 30;
    $offset = ($page - 1) * $limit;

    $where = "WHERE 1=1";
    $prepare_params = [];

    if (!empty($difficulty)) {
        $where .= " AND difficulty = %s";
        $prepare_params[] = $difficulty;
    }

    $count_sql = "SELECT COUNT(*) FROM code_practice_problems $where";
    $count = $wpdb->get_var($wpdb->prepare($count_sql, ...$prepare_params));

    $query_sql = "
        SELECT id, title, content, test_cases, difficulty, acceptance_rate 
        FROM code_practice_problems 
        $where 
        ORDER BY id ASC

        LIMIT %d OFFSET %d
    ";

    $prepare_params[] = $limit;
    $prepare_params[] = $offset;
    $results = $wpdb->get_results($wpdb->prepare($query_sql, ...$prepare_params), ARRAY_A);

    return rest_ensure_response([
        'data' => $results,
        'total' => intval($count),
        'page' => $page,
        'limit' => $limit
    ]);
}

?>