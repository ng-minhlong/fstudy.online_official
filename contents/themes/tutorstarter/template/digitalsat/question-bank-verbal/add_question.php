<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data and sanitize it
    $id_question = wp_unslash($_POST['id_question']);
    $type_question = wp_unslash($_POST['type_question']);

    $question_content = wp_unslash($_POST['question_content']);
    $answer_1 = wp_unslash($_POST['answer_1']);
    $answer_2 = wp_unslash($_POST['answer_2']);
    $answer_3 = wp_unslash($_POST['answer_3']);
    $answer_4 = wp_unslash($_POST['answer_4']);
    $correct_answer = wp_unslash($_POST['correct_answer']);
    $explanation = wp_unslash($_POST['explanation']);
    $image_link = wp_unslash($_POST['image_link']);
    $category = wp_unslash($_POST['category']);

    // Prepare the data for insertion
    $data = array(
        'id_question' => $id_question,
        'type_question' => $type_question,
        'question_content' => $question_content,
        'answer_1' => $answer_1,
        'answer_2' => $answer_2,
        'answer_3' => $answer_3,
        'answer_4' => $answer_4,
        'correct_answer' => $correct_answer,
        'explanation' => $explanation,
        'image_link' => $image_link,
        'category' => $category,

    );

    // Insert the data into the database
    $inserted = $wpdb->insert('digital_sat_question_bank_verbal', $data);

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
