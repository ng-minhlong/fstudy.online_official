<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$username = wp_kses_post($_POST['username']);
$idtest = wp_kses_post($_POST['idtest']);
$dateform = wp_kses_post($_POST['dateform']);
$test_type = wp_kses_post($_POST['test_type']);
$correct_percentage = wp_kses_post($_POST['correct_percentage']);
$total_question_number = wp_kses_post($_POST['total_question_number']);

$correct_number = wp_kses_post($_POST['correct_number']);
$incorrect_number = wp_kses_post($_POST['incorrect_number']);
$skip_number = wp_kses_post($_POST['skip_number']);
$overallband = wp_kses_post($_POST['overallband']);
$testname = wp_kses_post($_POST['testname']);
$useranswer = wp_kses_post($_POST['useranswer']);
$timedotest = wp_kses_post($_POST['timedotest']);
$testsavenumber = wp_kses_post($_POST['testsavenumber']);
$permission_link = wp_kses_post($_POST['permission_link']);

// Prepare the data for updating
$data = array(
    'username' => $username,
    'idtest' => $idtest,
    'testname' => $testname,
    'timedotest' => $timedotest,
    'test_type' => $test_type,
    'correct_percentage' => $correct_percentage,
    'overallband' => $overallband,

);

    // Insert the data into the database
    $inserted = $wpdb->insert('save_user_result_ielts_reading', $data);

    if ($inserted) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $wpdb->last_error; // Fetch last error if any
    }

    // Redirect back to the main page
    wp_redirect('index.php');
    exit; // Always call exit after redirecting
}
?>
