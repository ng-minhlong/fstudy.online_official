<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);

$id = wp_kses_post($_POST['id']);

$new_word = wp_kses_post($_POST['new_word']);


$language_new_word = wp_kses_post($_POST['language_new_word']);

$vietnamese_meaning = wp_kses_post($_POST['vietnamese_meaning']);

$english_explanation = wp_unslash(wp_kses_post($_POST['english_explanation']));
$example = wp_unslash(wp_kses_post($_POST['example']));

$image_link = wp_kses_post($_POST['image_link']);

// Prepare the data for updating
$data = array(
    'id' => $id,
    'new_word' => $new_word,
    'language_new_word' => $language_new_word,
    'vietnamese_meaning' => $vietnamese_meaning,
    'english_explanation' => $english_explanation,
    'example' => $example,

    'image_link' => $image_link,
);

// Update the record in the database
$wpdb->update('list_vocabulary', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));




?>
