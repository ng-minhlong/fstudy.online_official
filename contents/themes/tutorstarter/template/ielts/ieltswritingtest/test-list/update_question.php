<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

    // Get the data from the POST request
    $number = wp_kses_post($_POST['number']);
    $id_test = wp_kses_post($_POST['id_test']);
    $testname = wp_kses_post($_POST['testname']);
    $time = wp_kses_post($_POST['time']);

    $test_type = wp_kses_post($_POST['test_type']);
    $question_choose = wp_kses_post($_POST['question_choose']);
    $tag = wp_kses_post($_POST['tag']);
    $book = wp_kses_post($_POST['book']);
    $token_need = wp_unslash($_POST['token_need']);
    $role_access = wp_unslash($_POST['role_access']);
    $time_allow = wp_unslash($_POST['time_allow']);
    // Check and process question_choose for specific patterns
    $processed_question_choose = '';
    $lines = explode("\n", $question_choose);

    foreach ($lines as $line) {
        $line = trim($line);
        if (preg_match('/^verbal:\s*(\d+)\s*-\s*(\d+)$/i', $line, $matches)) {
            $start = (int)$matches[1];
            $end = (int)$matches[2];
            for ($i = $start; $i <= $end; $i++) {
                $processed_question_choose .= 'verbal' . $i . ', ';
            }
        } elseif (preg_match('/^math:\s*(\d+)\s*-\s*(\d+)$/i', $line, $matches)) {
            $start = (int)$matches[1];
            $end = (int)$matches[2];
            for ($i = $start; $i <= $end; $i++) {
                $processed_question_choose .= 'math' . $i . ', ';
            }
        } else {
            $processed_question_choose .= $line . ', ';
        }
    }

    // Remove trailing comma and space
    $processed_question_choose = rtrim($processed_question_choose, ', ');
// Prepare the data for updating
$data = array(
    'id_test' => $id_test,
    'testname' => $testname,
    'time' => $time,
    'test_type' => $test_type,
    'question_choose' => $processed_question_choose,
    'tag' => $tag,
    'book' => $book,
    'token_need' => $token_need,
    'role_access' => $role_access,
    'time_allow' => $time_allow,
);

// Update the record in the database
$wpdb->update('ielts_writing_test_list', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
