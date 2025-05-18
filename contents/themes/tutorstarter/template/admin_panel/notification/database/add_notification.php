<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$title = wp_unslash($_POST['title']);
$id_notification = wp_unslash($_POST['id_notification']);
$content = wp_unslash($_POST['content']);
$level_notification = wp_unslash($_POST['level_notification']);
//$role_receive = wp_unslash($_POST['role_receive']);
$user_receive = wp_unslash($_POST['user_receive']);


$role_receive = isset($_POST['role_receive']) ? array_map('sanitize_text_field', $_POST['role_receive']) : array(); // Process roles
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

    // Insert the data into the database
    $inserted = $wpdb->insert('notification_admin', $data);

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
