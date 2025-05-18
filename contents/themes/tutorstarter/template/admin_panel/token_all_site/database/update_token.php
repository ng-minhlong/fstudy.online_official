<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$token_name = wp_unslash($_POST['token_name']);
$token_key = wp_unslash($_POST['token_key']);
$token_image = wp_unslash($_POST['token_image']);


// Prepare the data for updating
$data = array(
    'token_name' => $token_name,
    'token_key' => $token_key,
    'token_image' => $token_image,
   
);

// Update the record in the database
$wpdb->update('create_token_all_site', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
