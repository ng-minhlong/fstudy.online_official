<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$username = wp_kses_post($_POST['username']);
$idtest = wp_kses_post($_POST['idtest']);
$test_type = wp_kses_post($_POST['test_type']);
$band_score = wp_kses_post($_POST['band_score']);
$band_score_expand = wp_kses_post($_POST['band_score_expand']);
$testname = wp_kses_post($_POST['testname']);
$timeSpent = wp_kses_post($_POST['timeSpent']);

// Prepare the data for updating
$data = array(
    'idtest' => $idtest,
    'testname' => $testname,
    'timedotest' => $timedotest,
    'test_type' => $test_type,
    'band_score' => $band_score,
    'band_score_expand' => $band_score_expand,
    'timeSpent' => $timeSpent,

);

    // Insert the data into the database
    $inserted = $wpdb->insert('save_user_result_ielts_writing', $data);

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
