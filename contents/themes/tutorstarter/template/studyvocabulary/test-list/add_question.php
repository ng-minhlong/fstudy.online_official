<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input data
    $id_test = wp_kses_post($_POST['id_test']);
    $testname = wp_kses_post($_POST['testname']);
    $test_type = wp_kses_post($_POST['test_type']);
    $question_choose = wp_kses_post($_POST['question_choose']);

    // Check and process question_choose for specific patterns
    $processed_question_choose = '';
    $lines = explode("\n", $question_choose);

    foreach ($lines as $line) {
        $line = trim($line);
        if (preg_match('/^vocabulary:\s*(\d+)\s*-\s*(\d+)$/i', $line, $matches)) {
            $start = (int)$matches[1];
            $end = (int)$matches[2];
            for ($i = $start; $i <= $end; $i++) {
                $processed_question_choose .= 'vocabulary' . $i . ', ';
            }
        }  else {
            $processed_question_choose .= $line . ', ';
        }
    }

    // Remove trailing comma and space
    $processed_question_choose = rtrim($processed_question_choose, ', ');

    // Prepare data for insertion
    $data = array(
        'id_test' => $id_test,
        'testname' => $testname,
        'test_type' => $test_type,
        'question_choose' => $processed_question_choose,
      
    );

    // Insert the data into the database
    $inserted = $wpdb->insert('list_test_vocabulary_book', $data);

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
