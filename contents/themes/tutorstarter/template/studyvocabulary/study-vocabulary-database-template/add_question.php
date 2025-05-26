<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data and sanitize it
    $id = wp_kses_post($_POST['id']);
    $new_word = wp_kses_post($_POST['new_word']);
    $language_new_word = wp_kses_post($_POST['language_new_word']);
    $vietnamese_meaning = wp_kses_post($_POST['vietnamese_meaning']);
    $english_explanation = wp_unslash(wp_kses_post($_POST['english_explanation']));
    $example = wp_unslash(wp_kses_post($_POST['example']));

    $image_link = wp_kses_post($_POST['image_link']);
    // Prepare the data for insertion
    $data = array(
        'id' => $id,
        'new_word' => $new_word,
        'language_new_word' => $language_new_word,
        'vietnamese_meaning' => $vietnamese_meaning,
        'english_explanation' => $english_explanation,
        'example' => $example,
        'image_link' => $image_link
    );

    // Insert the data into the database
    $inserted = $wpdb->insert('list_vocabulary', $data);

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
