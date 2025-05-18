<?php
function get_test_case(WP_REST_Request $request) {
    global $wpdb;
    
    // Get the problem ID from the request
    $params = $request->get_json_params();
    $id_problem = isset($params['id_problem']) ? intval($params['id_problem']) : 0;

    if (!$id_problem) {
        return new WP_Error('invalid_id', 'Invalid problem ID', array('status' => 400));
    }

    // Prepare and execute the query
    $table_name = 'code_practice_problems';
    $query = $wpdb->prepare(
        "SELECT 
            id,
            title,
            content,
            python_code,
            analysis,
            annotated_code,
            test_cases,
            difficulty,
            acceptance_rate,
            similar_questions
        FROM $table_name 
        WHERE id = %d",
        $id_problem
    );

    $problem = $wpdb->get_row($query, ARRAY_A);

    if (!$problem) {
        return new WP_Error('not_found', 'Problem not found', array('status' => 404));
    }

  
    // Return the response
    return new WP_REST_Response(array(
        'success' => true,
        'data' => $problem
    ), 200);
}