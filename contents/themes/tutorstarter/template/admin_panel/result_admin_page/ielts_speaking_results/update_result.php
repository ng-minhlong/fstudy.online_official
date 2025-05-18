<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

    // Get the data from the POST request
    $number = wp_kses_post($_POST['number']);
    $username = wp_kses_post($_POST['username']);

    $idtest = wp_kses_post($_POST['idtest']);
    $test_type = wp_kses_post($_POST['test_type']);
    $resulttest = wp_kses_post($_POST['resulttest']);
    $band_detail = wp_kses_post($_POST['band_detail']);
    $testname = wp_kses_post($_POST['testname']);
    
    // Prepare the data for updating
    $data = array(
        'idtest' => $idtest,
        'testname' => $testname,
        'test_type' => $test_type,
        'resulttest' => $resulttest,
        'band_detail' => $band_detail,
    
    );
    

// Update the record in the database
$wpdb->update('save_user_result_ielts_speaking', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
