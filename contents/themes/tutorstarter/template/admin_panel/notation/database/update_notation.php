<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$username = wp_unslash($_POST['username']);
$word_save = wp_unslash($_POST['word_save']);
$meaning_or_explanation = wp_unslash($_POST['meaning_or_explanation']);
$is_source = wp_unslash($_POST['is_source']);
$test_type = wp_unslash($_POST['test_type']);
$id_test = wp_unslash($_POST['id_test']);



// Prepare the data for updating
$data = array(
    'username' => $username,
    'word_save' => $word_save,
    'meaning_or_explanation' => $meaning_or_explanation,
    'is_source' => $is_source, // Add the sanitized role data
    'test_type' => $test_type,
    'id_test' => $id_test,

);

// Update the record in the database
$wpdb->update('notation', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
