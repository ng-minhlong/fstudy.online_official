<?php 
function cham_diem_thptqg(WP_REST_Request $request) {
    global $wpdb;

    $params = $request->get_json_params();
    $id_test = $params['id_test'];
    $username = trim($params['username']);
    $subject = trim($params['subject']);
    $user_answer = $params['user_answer'];
    $testname = $params['testname'];
    $result_id = $params['result_id'];

    if (!$id_test) {
        return new WP_Error('invalid_id', 'Invalid problem ID', array('status' => 400));
    }

    $table_name = 'thptqg_question';
    $query = $wpdb->prepare(
        "SELECT answer FROM $table_name WHERE id_test = %d",
        $id_test
    );
    $correct_data = $wpdb->get_row($query, ARRAY_A);
    if (!$correct_data || empty($correct_data['answer'])) {
        return new WP_Error('no_data', 'No answer data found', array('status' => 404));
    }

    $correct_answers = json_decode($correct_data['answer'], true);

    $result = [
        'correct' => 0,
        'wrong' => 0,
        'skipped' => 0,
        'score' => 0,
        'detail' => [
            'part1' => 0,
            'part2' => 0,
            'part3' => 0,
        ]
    ];

    // === PART 1 ===
    if (isset($user_answer['part1'])) {
        foreach ($user_answer['part1'] as $key => $answer) {
            $qid = ucfirst(str_replace('q', 'Question ', $key));
            if (!isset($correct_answers[$qid])) continue;

            if ($answer === null || $answer === '') {
                $result['skipped']++;
            } elseif (strtoupper($answer) === strtoupper($correct_answers[$qid])) {
                $result['correct']++;
                $result['detail']['part1'] += 0.25;
            } else {
                $result['wrong']++;
            }
        }
    }

    // === PART 2 ===
    if (isset($user_answer['part2'])) {
        foreach ($user_answer['part2'] as $key => $choices) {
            $qid = ucfirst(str_replace('q', 'Question ', $key));
            if (!isset($correct_answers[$qid])) continue;

            $truths = explode('/', $correct_answers[$qid]);
            $user_choices = ['a', 'b', 'c', 'd'];
            $correct_count = 0;
            $has_choice = false;

            foreach ($user_choices as $i => $letter) {
                $user_val = strtolower(trim($choices[$letter] ?? ''));
                if ($user_val !== '' && $user_val !== null) {
                    $has_choice = true;
                    $expected = mb_strtolower($truths[$i] ?? '');
                    if (($expected === 'đ' && $user_val === 'true') || ($expected === 's' && $user_val === 'false')) {
                        $correct_count++;
                    }
                }
            }

            if (!$has_choice) {
                $result['skipped']++;
            } else {
                if ($correct_count === 1) {
                    $result['detail']['part2'] += 0.1;
                    $result['correct']++;
                } elseif ($correct_count === 2) {
                    $result['detail']['part2'] += 0.25;
                    $result['correct']++;
                } elseif ($correct_count === 3) {
                    $result['detail']['part2'] += 0.5;
                    $result['correct']++;
                } elseif ($correct_count === 4) {
                    $result['detail']['part2'] += 1.0;
                    $result['correct']++;
                } else {
                    $result['wrong']++;
                }
            }
        }
    }

    // === PART 3 ===
    if (isset($user_answer['part3'])) {
        foreach ($user_answer['part3'] as $key => $answer) {
            $qid = ucfirst(str_replace('q', 'Question ', $key));
            if (!isset($correct_answers[$qid])) continue;

            $correct_val = trim((string) $correct_answers[$qid]);
            $given = trim((string) $answer);

            if ($given === '') {
                $result['skipped']++;
            } elseif ($given === $correct_val) {
                $result['correct']++;
                $result['detail']['part3'] += ($subject === 'Toán học' || $subject === 'math') ? 0.5 : 0.25;
            } else {
                $result['wrong']++;
            }
        }
    }

    // Tổng điểm không vượt quá 10
    $total_score = $result['detail']['part1'] + $result['detail']['part2'] + $result['detail']['part3'];
    $result['score'] = round(min($total_score, 10), 2);
    $wpdb->insert('save_user_result_thptqg', [
        'username' => $username,
        'idtest' => $id_test,
        'testname' => $testname,
        'dateform' => current_time('mysql'),
        'subject' => $subject,
        'timedotest' => $result_id,
        'overallband' => $result['score'],
        'total_question_number' => count($correct_answers),
        'correct_number' => (string) $result['correct'],
        'incorrect_number' => $result['wrong'],
        'skip_number' => $result['skipped'],
        'useranswer' => json_encode($user_answer, JSON_UNESCAPED_UNICODE),
        'testsavenumber' => $result_id
    ]);


    return new WP_REST_Response([
        'success' => true,
        'result' => $result
    ], 200);
}
