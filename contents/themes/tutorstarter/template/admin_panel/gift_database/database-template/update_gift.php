<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wordpress\wp-load.php'); // Adjust the path as necessary

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$id_gift = wp_kses_post($_POST['id_gift']);
$gift_send = wp_unslash($_POST['gift_send']);
$date_created = wp_kses_post($_POST['date_created']);
$date_expired = wp_kses_post($_POST['date_expired']);
$user_receive = wp_unslash($_POST['user_receive']);
$title_gift = wp_kses_post($_POST['title_gift']);
$content_gift = wp_kses_post($_POST['content_gift']);

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

// Update the record in the database
$wpdb->update('gift_from_admin', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
