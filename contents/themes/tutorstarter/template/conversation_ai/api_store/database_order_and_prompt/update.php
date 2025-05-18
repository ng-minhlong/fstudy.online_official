<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$last_use_end_point = wp_kses_post($_POST['last_use_end_point']);
$list_name_endpoint_order = wp_unslash($_POST['list_name_endpoint_order']);
$prompt_ielts_writing = wp_unslash($_POST['prompt_ielts_writing']);
$prompt_ielts_speaking = wp_unslash($_POST['prompt_ielts_speaking']);
$prompt_conversation_ai = wp_unslash($_POST['prompt_conversation_ai']);

// Prepare the data for updating
$data = array(
    'last_use_end_point' => $last_use_end_point,
    'list_name_endpoint_order' => $list_name_endpoint_order,
    'prompt_ielts_writing' => $prompt_ielts_writing,
    'prompt_ielts_speaking' => $prompt_ielts_speaking,
    'prompt_conversation_ai' => $prompt_conversation_ai,
);

// Update the record in the database
$wpdb->update('order_and_prompt_api_list', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
