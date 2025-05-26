    <?php
    function cham_diem_digitalsat(WP_REST_Request $request) {
        global $wpdb;

        $params = $request->get_json_params();
        $user_answer = $params['user_answer'];
        $type_test = $params['type_test'];
        $id_test = $params['id_test'];
        $username = $params['username'];
        $result_id = $params['result_id'];
        $saveSpecificTime = $params['saveSpecificTime'];
        $timedotest = $params['timedotest'];
        $testname = $params['testname'];


        $total = $correct = $incorrect = $skipped = 0;

        // Theo loại
        $stats_by_type = [
            'multiple-choice' => ['total' => 0, 'correct' => 0, 'incorrect' => 0, 'skipped' => 0],
            'completion' => ['total' => 0, 'correct' => 0, 'incorrect' => 0, 'skipped' => 0]
        ];

        foreach ($user_answer as $q) {
            $id = $q['id'];
            $type = $q['type'];
            $answer = trim($q['user_answer']);

            $table = str_starts_with($id, 'math') 
                ? 'digital_sat_question_bank_math' 
                : 'digital_sat_question_bank_verbal';

            $row = $wpdb->get_row(
                $wpdb->prepare("SELECT correct_answer FROM $table WHERE id_question = %s", $id),
                ARRAY_A
            );

            if (!$row) continue;

            $correct_answer_raw = $row['correct_answer'];
            $stats_by_type[$type]['total']++;
            $total++;

            if ($answer === '') {
                $skipped++;
                $stats_by_type[$type]['skipped']++;
            } else {
                if ($type === 'multiple-choice') {
                    // answer_1 → A, answer_2 → B,...
                    $correct_letter = match ($correct_answer_raw) {
                        'answer_1' => 'A',
                        'answer_2' => 'B',
                        'answer_3' => 'C',
                        'answer_4' => 'D',
                        default => ''
                    };

                    if (strtoupper($answer) === $correct_letter) {
                        $correct++;
                        $stats_by_type[$type]['correct']++;
                    } else {
                        $incorrect++;
                        $stats_by_type[$type]['incorrect']++;
                    }
                } else if ($type === 'completion') {
                    if (strtolower(trim($answer)) === strtolower(trim($correct_answer_raw))) {
                        $correct++;
                        $stats_by_type[$type]['correct']++;
                    } else {
                        $incorrect++;
                        $stats_by_type[$type]['incorrect']++;
                    }
                }
            }
        }

        // Tổng
        $result = [
            'total' => $total,
            'correct' => $correct,
            'incorrect' => $incorrect,
            'skipped' => $skipped,
            'accuracy_percent' => $total > 0 ? round($correct / $total * 100, 2) : 0,
        ];

        // Thống kê từng loại
        foreach ($stats_by_type as $type => $stat) {
            $stat['accuracy_percent'] = $stat['total'] > 0 ? round($stat['correct'] / $stat['total'] * 100, 2) : 0;
            $result['by_type'][$type] = $stat;
        }
        if (is_string($saveSpecificTime)) {
            $saveSpecificTime = json_decode($saveSpecificTime, true);
        }


        //$result['score'] = round(min($total_score, 10), 2);
        $wpdb->insert('save_user_result_digital_sat', [
            'username' => $username,
            'idtest' => $id_test,
            'testname' => $testname,
            'dateform' => current_time('mysql'),
            'timedotest' => $timedotest,
            'test_type' => $type_test,
            'resulttest' => $result['accuracy_percent'],
            'correct_percentage' => $result['accuracy_percent'],
            'total_question_number' => $result['total'],
            'correct_number' => (string) $result['correct'],
            'incorrect_number' => $result['incorrect'],
            'skip_number' => $result['skipped'],
            'useranswer' => json_encode($user_answer, JSON_UNESCAPED_UNICODE),
            'save_specific_time' => json_encode($saveSpecificTime, JSON_UNESCAPED_UNICODE),
            'testsavenumber' => $result_id
        ]);



        return new WP_REST_Response([
            'success' => true,
            'result' => $result
        ], 200);
    }
