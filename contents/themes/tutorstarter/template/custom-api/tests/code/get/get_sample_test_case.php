<?php

    function get_sample_test_cases(WP_REST_Request $request) {
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
                test_cases
            FROM $table_name 
            WHERE id = %d",
            $id_problem
        );

        $problem = $wpdb->get_row($query, ARRAY_A);

        if (!$problem) {
            return new WP_Error('not_found', 'Problem not found', array('status' => 404));
        }

        // Decode the test cases JSON
        $test_cases = json_decode($problem['test_cases'], true);
        
        // Apply the test case selection rules
        if (is_array($test_cases)) {
            $count = count($test_cases);
            
            if ($count > 3) {
                $selected_test_cases = array_slice($test_cases, 0, 3);
            } elseif ($count === 3) {
                $selected_test_cases = array_slice($test_cases, 0, 2);
            } elseif ($count === 1 || $count === 2) {
                $selected_test_cases = array_slice($test_cases, 0, 1);
            } else {
                $selected_test_cases = [];
            }
            
            // Update the problem data with selected test cases
            $problem['test_cases'] = json_encode($selected_test_cases);
        }

        // Return the response
        return new WP_REST_Response(array(
            'success' => true,
            'data' => $problem
        ), 200);
    }