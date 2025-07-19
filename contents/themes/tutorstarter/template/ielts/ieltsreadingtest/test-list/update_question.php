<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$id_test = wp_kses_post($_POST['id_test']);
$testname = wp_kses_post($_POST['testname']);
$test_type = wp_kses_post($_POST['test_type']);
$question_choose = wp_kses_post($_POST['question_choose']);
$tag = wp_kses_post($_POST['tag']);
$book = wp_kses_post($_POST['book']);
$token_need = wp_unslash($_POST['token_need']);
$role_access = wp_unslash($_POST['role_access']);
$time_allow = wp_unslash($_POST['time_allow']);




// Prepare the data for updating
$data = array(
    'id_test' => $id_test,
    'testname' => $testname,
    'test_type' => $test_type,
    'question_choose' => $question_choose,
    'tag' => $tag,
    'book' => $book,
    'token_need' => $token_need,
    'role_access' => $role_access,
    'time_allow' => $time_allow,
    'updated_at' => date('Y-m-d H:i:s'),
);

// Update the record in the database
$wpdb->update('ielts_reading_test_list', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
