<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the POST request
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

    // Insert the data into the database
    $inserted = $wpdb->insert('order_and_prompt_api_list', $data);

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
