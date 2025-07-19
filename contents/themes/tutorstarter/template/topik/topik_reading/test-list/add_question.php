<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input data
    $id_test = wp_kses_post($_POST['id_test']);
    $testname = wp_kses_post($_POST['testname']);
    $testcode = wp_unslash($_POST['testcode']); // Không lọc thẻ HTML
    $correct_answer = wp_unslash($_POST['correct_answer']);
    $test_type = wp_kses_post($_POST['test_type']);
    $time = wp_kses_post($_POST['time']);
    $number_question = wp_kses_post($_POST['number_question']);

    $permissive_management = wp_kses_post($_POST['permissive_management']);
    $token_need = wp_unslash($_POST['token_need']);
    $role_access = wp_unslash($_POST['role_access']);
    $time_allow = wp_unslash($_POST['time_allow']);


    // Prepare data for insertion
    $data = array(
        'id_test' => $id_test,
        'testname' => $testname,
        'testcode' => $testcode,
        'correct_answer' => $correct_answer,
        'permissive_management' => $permissive_management,
        'token_need' => $token_need,
        'test_type' => $test_type,
        'number_question' => $number_question,
        'time' => $time,

        'role_access' => $role_access,
        'time_allow' => $time_allow,
        'updated_at' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
    );

    // Insert the data into the database
    $inserted = $wpdb->insert('topik_reading_test_list', $data);

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
