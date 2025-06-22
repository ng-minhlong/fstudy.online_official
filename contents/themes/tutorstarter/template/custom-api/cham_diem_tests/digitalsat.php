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
    $rw_total = $rw_correct = 0;
    $math_total = $math_correct = 0;
    $has_unidentified = false;

    foreach ($user_answer as $q) {
        $id = $q['id'];
        $type = $q['type'];
        $user_ans = trim($q['user_answer']);
        $is_math = str_starts_with($id, 'math');

        $table = $is_math ? 'digital_sat_question_bank_math' : 'digital_sat_question_bank_verbal';
        $row = $wpdb->get_row(
            $wpdb->prepare("SELECT correct_answer FROM $table WHERE id_question = %s", $id),
            ARRAY_A
        );
        if (!$row) continue;

        $correct_answer = $row['correct_answer'];
        $total++;
        $is_correct = false;

        if ($user_ans === '') {
            $skipped++;
        } else {
            if ($type === 'multiple-choice') {
                $correct_letter = match ($correct_answer) {
                    'answer_1' => 'A',
                    'answer_2' => 'B',
                    'answer_3' => 'C',
                    'answer_4' => 'D',
                    default => ''
                };
                $is_correct = strtoupper($user_ans) === $correct_letter;
            } elseif ($type === 'completion') {
                $is_correct = strtolower(trim($user_ans)) === strtolower(trim($correct_answer));
            }

            if ($is_correct) {
                $correct++;
                if ($is_math) $math_correct++; else $rw_correct++;
            } else {
                $incorrect++;
            }
        }

        if ($is_math) {
            $math_total++;
        } else {
            $rw_total++;
        }

        // Nếu không có domain (sau này bỏ khỏi câu hỏi luôn cũng được)
        if (empty($q['domainQuestion'])) {
            $has_unidentified = true;
        }
    }

    $rw_accuracy = $rw_total > 0 ? round($rw_correct / $rw_total * 100) : 0;
    $math_accuracy = $math_total > 0 ? round($math_correct / $math_total * 100) : 0;

    $result = [
        'total' => $total,
        'correct' => $correct,
        'incorrect' => $incorrect,
        'skipped' => $skipped,
        'accuracy_percent' => $total > 0 ? round($correct / $total * 100, 2) : 0,
        'accuracy' => [
            'Reading and Writing' => (string) $rw_accuracy,
            'Math' => (string) $math_accuracy
        ]
    ];

    // Tính điểm
    if ($type_test === 'Practice') {
        $final_result = [
            'result' => $result['accuracy_percent'] . '%'
        ];
    }
 else {
        if ($has_unidentified) {
            $rw_score = round($rw_accuracy * 800 / 100 / 10) * 10;
            $math_score = round($math_accuracy * 800 / 100 / 10) * 10;
        } else {
            $rw_score = round($rw_correct * 800 / 54 / 10) * 10;
            $math_score = round($math_correct * 800 / 44 / 10) * 10;
        }

        $total_score = round(($rw_score + $math_score) / 10) * 10;
        $final_result = [
            'result' => $total_score,
            'part_result' => [
                'Reading and Writing' => $rw_score,
                'Math' => $math_score
            ]
        ];
    }

    $result['final_result'] = $final_result;

    if (is_string($saveSpecificTime)) {
        $saveSpecificTime = json_decode($saveSpecificTime, true);
    }

    $wpdb->insert('save_user_result_digital_sat', [
        'username' => $username,
        'idtest' => $id_test,
        'testname' => $testname,
        'dateform' => current_time('mysql'),
        'timedotest' => $timedotest,
        'test_type' => $type_test,
        'resulttest' => json_encode($final_result, JSON_UNESCAPED_UNICODE),

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
