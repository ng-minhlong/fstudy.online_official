<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input data
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

    // Prepare data for insertion
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

    // Insert the data into the database
    $inserted = $wpdb->insert('ielts_writing_test_list', $data);

    if ($inserted) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $wpdb->last_error;
    }

    // Redirect back to the main page
    wp_redirect('index.php');
    exit;
}
?>
