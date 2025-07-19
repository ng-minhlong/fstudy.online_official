<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$id_test = wp_kses_post($_POST['id_test']);
$subject = wp_kses_post($_POST['subject']);
$year = wp_kses_post($_POST['year']);
$testname = wp_kses_post($_POST['testname']);
$answer = wp_unslash(wp_kses_post($_POST['answer']));
$time = wp_kses_post($_POST['time']);
$number_question = wp_kses_post($_POST['number_question']);
$testcode = wp_unslash($_POST['testcode']);

$token_need = wp_unslash($_POST['token_need']);
$role_access = wp_unslash($_POST['role_access']);
$time_allow = wp_unslash($_POST['time_allow']);

// Prepare the data for updating
$data = array(
    'id_test' => $id_test,
    'subject' => $subject,
    'year' => $year,
    'testname' => $testname,
    'answer' => $answer,
    'time' => $time,
    'number_question' => $number_question,
    'testcode' => $testcode,
    'token_need' => $token_need,
    'role_access' => $role_access,
    'time_allow' => $time_allow,
    'updated_at' => date('Y-m-d H:i:s'),
);

// Update the record in the database
$wpdb->update('thptqg_question', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
