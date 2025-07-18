<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$document_id = wp_kses_post($_POST['document_id']);
$document_name = wp_kses_post($_POST['document_name']);
$category = wp_kses_post($_POST['category']);
$tag = wp_kses_post($_POST['tag']);

$content = wp_kses_post($_POST['content']);
$file_link = wp_kses_post($_POST['file_link']);
$file_type = wp_kses_post($_POST['file_type']);
$prices = wp_kses_post($_POST['prices']);


$data = array(
    'document_id' => $document_id,
    'document_name' => $document_name,
    'category' => $category,
    'content' => $content,
    'file_link' => $file_link,
    'file_type' => $file_type,
    'prices' => $prices,
    'tag' => $tag,

    'updated_at' => date('Y-m-d H:i:s'),

);

// Update the record in the database
$wpdb->update('document', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
