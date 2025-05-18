<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$save_time = wp_unslash($_POST['save_time']);
$username = wp_unslash($_POST['username']);
$word_save = wp_unslash($_POST['word_save']);
$meaning_or_explanation = wp_unslash($_POST['meaning_or_explanation']);
//$role_receive = wp_unslash($_POST['role_receive']);
$is_source = wp_unslash($_POST['is_source']);


$test_type = wp_unslash($_POST['test_type']);
$id_test = wp_unslash($_POST['id_test']);



// Prepare the data for updating
$data = array(
    'save_time' => $save_time,
    'username' => $username,
    'word_save' => $word_save,
    'meaning_or_explanation' => $meaning_or_explanation,
    'is_source' => $is_source, // Add the sanitized role data
    'test_type' => $test_type,
    'id_test' => $id_test,

);

    $inserted = $wpdb->insert('notation', $data);

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
