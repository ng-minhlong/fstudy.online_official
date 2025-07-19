<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

// Get the data from the POST request
    $number = wp_kses_post($_POST['number']);
    $id_test = wp_kses_post($_POST['id_test']);
    $testname = wp_kses_post($_POST['testname']);
    $testcode = wp_unslash($_POST['testcode']); // Không lọc thẻ HTML
    $correct_answer = wp_unslash($_POST['correct_answer']);
    $permissive_management = wp_kses_post($_POST['permissive_management']);
    $token_need = wp_unslash($_POST['token_need']);
    $role_access = wp_unslash($_POST['role_access']);
    $time_allow = wp_unslash($_POST['time_allow']);
    $number_question = wp_unslash($_POST['number_question']);

    $test_type = wp_kses_post($_POST['test_type']);
    $time = wp_kses_post($_POST['time']);



// Prepare the data for updating
$data = array(
        'id_test' => $id_test,
        'testname' => $testname,
        'testcode' => $testcode,
        'correct_answer' => $correct_answer,
        'permissive_management' => $permissive_management,
        'test_type' => $test_type,
        'time' => $time,
        'number_question' => $number_question,
        'token_need' => $token_need,
        'role_access' => $role_access,
        'time_allow' => $time_allow,
        'updated_at' => date('Y-m-d H:i:s'),
);

// Update the record in the database
$wpdb->update('topik_listening_test_list', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
