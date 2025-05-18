<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the POST request
$api_endpoint_url = wp_kses_post($_POST['api_endpoint_url']);
$api_key = wp_kses_post($_POST['api_key']);
$type = wp_kses_post($_POST['type']);
$name_end_point = wp_kses_post($_POST['name_end_point']);
$all_time_use_number = wp_kses_post($_POST['all_time_use_number']);
$today_time_use_number = wp_kses_post($_POST['today_time_use_number']);


// Prepare the data for updating
$data = array(
    'api_endpoint_url' => $api_endpoint_url,
    'api_key' => $api_key,
    'type' => $type,
    'name_end_point' => $name_end_point,
    'all_time_use_number' => $all_time_use_number,
    'today_time_use_number' => $today_time_use_number,
);

    // Insert the data into the database
    $inserted = $wpdb->insert('api_key_route', $data);

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
