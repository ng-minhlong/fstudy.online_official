<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$title = wp_unslash($_POST['title']);
$content = wp_unslash($_POST['content']);
$id_notification = wp_unslash($_POST['id_notification']);
$level_notification = wp_unslash($_POST['level_notification']);
$user_receive = wp_unslash($_POST['user_receive']);
$role_receive = isset($_POST['role_receive']) ? array_map('sanitize_text_field', $_POST['role_receive']) : array(); // Process roles

// Convert roles to a string if needed (e.g., for database storage)
$role_receive_string = implode(',', $role_receive);

// Prepare the data for updating
$data = array(
    'title' => $title,
    'content' => $content,
    'id_notification' => $id_notification,
    'level_notification' => $level_notification,
    'role_receive' => $role_receive_string, // Add the sanitized role data
    'user_receive' => $user_receive,
);

// Update the record in the database
$wpdb->update('notification_admin', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
