<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Insert the data into the database
    $inserted = $wpdb->insert('create_token_all_site', $data);

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
