<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
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

// Update the record in the database
$wpdb->update('api_key_route', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
