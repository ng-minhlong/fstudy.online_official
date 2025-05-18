<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$id_gift = wp_kses_post($_POST['id_gift']);
$gift_send = wp_unslash($_POST['gift_send']);
$date_created = wp_kses_post($_POST['date_created']);
$date_expired = wp_kses_post($_POST['date_expired']);
$user_receive = wp_unslash($_POST['user_receive']);
$title_gift = wp_unslash($_POST['title_gift']);
$content_gift = wp_unslash($_POST['content_gift']);


// Prepare the data for updating
$data = array(
    'id_gift' => $id_gift,
    'gift_send' => $gift_send,
    'date_created' => $date_created,
    'date_expired' => $date_expired,
    'user_receive' => $user_receive,
    'title_gift' => $title_gift,
    'content_gift' => $content_gift,

);

    // Insert the data into the database
    $inserted = $wpdb->insert('gift_from_admin', $data);

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
