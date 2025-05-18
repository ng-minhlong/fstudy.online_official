<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

    // Get the data from the POST request
    $number = wp_kses_post($_POST['number']);
    $username = wp_kses_post($_POST['username']);
    $idtest = wp_kses_post($_POST['idtest']);
    $test_type = wp_kses_post($_POST['test_type']);
    $testname = wp_kses_post($_POST['testname']);

    $band_score = wp_kses_post($_POST['band_score']);
    $band_score_expand = wp_kses_post($_POST['band_score_expand']);
    $timeSpent = wp_kses_post($_POST['timeSpent']);
    
    // Prepare the data for updating
    $data = array(
        'idtest' => $idtest,
        'testname' => $testname,
        'timedotest' => $timedotest,
        'test_type' => $test_type,
        'band_score' => $band_score,
        'band_score_expand' => $band_score_expand,
        'dateform' => $dateform,
        'timeSpent' => $timeSpent,

    );
    

// Update the record in the database
$wpdb->update('save_user_result_ielts_writing', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
