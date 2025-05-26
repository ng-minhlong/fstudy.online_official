<?php
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

$number = wp_kses_post($_POST['number']);

$id = isset($_POST['id']) ? wp_unslash($_POST['id']) : '';
$type = isset($_POST['type']) ? wp_unslash($_POST['type']) : '';

$user_role = isset($_POST['user_role']) ? wp_unslash($_POST['user_role']) : '';
$user_token = isset($_POST['user_token']) ? wp_unslash($_POST['user_token']) : '';

$time_expired = isset($_POST['time_expired']) ? wp_unslash($_POST['time_expired']) : '';


$price_role = isset($_POST['price_role']) ? wp_unslash($_POST['price_role']) : '';
$price_token = isset($_POST['price_token']) ? wp_unslash($_POST['price_token']) : '';
$note_role = isset($_POST['note_role']) ? wp_unslash($_POST['note_role']) : '';
$note_token = isset($_POST['note_token']) ? wp_unslash($_POST['note_token']) : '';
// Prepare the data for updating
$data = array(
    'user_role' => $user_role,
    'user_token' => $user_token,  
    'date_created' => current_time('mysql'),

    'id' => $id,
    'type' => $type,
    'time_expired' => $time_expired,

    'price_role' => $price_role,
    'price_token' => $price_token,
    'note_role' => $note_role,
    'note_token' => $note_token,
);

// Update the record in the database
$wpdb->update('admin_create', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
