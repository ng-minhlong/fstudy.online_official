<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize input data
    $document_id = wp_kses_post($_POST['document_id']);
    $document_name = wp_kses_post($_POST['document_name']);
    $category = wp_kses_post($_POST['category']);
    $tag = wp_kses_post($_POST['tag']);

    $content = wp_kses_post($_POST['content']);
    $file_link = wp_kses_post($_POST['file_link']);
    $file_type = wp_kses_post($_POST['file_type']);
    $prices = wp_kses_post($_POST['prices']);
   


    // Prepare data for insertion
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
    'created_at' => date('Y-m-d H:i:s'),

    );

    // Insert the data into the database
    $inserted = $wpdb->insert('document', $data);

    if ($inserted) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $wpdb->last_error;
    }

    // Redirect back to the main page
    wp_redirect('index.php');
    exit;
}
?>
