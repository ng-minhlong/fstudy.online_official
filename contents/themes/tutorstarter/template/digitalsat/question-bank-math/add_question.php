<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data and sanitize it
    $id_question = wp_unslash($_POST['id_question']);
    $type_question = wp_unslash($_POST['type_question']);

    $question_content = wp_unslash($_POST['question_content']);
    // Tiếp tục với các bước lưu vào cơ sở dữ liệu
    $answer_1 = wp_unslash($_POST['answer_1']);
    $answer_2 = wp_unslash($_POST['answer_2']);
    $answer_3 = wp_unslash($_POST['answer_3']);
    $answer_4 = wp_unslash($_POST['answer_4']);
    $category = wp_unslash($_POST['category']);

    if ($_POST['type_question'] === 'completion') {
        $correct_answer = $_POST['custom_correct_answer'];
    } else {
        $correct_answer = $_POST['correct_answer'];
    }
    
    // Tiếp tục xử lý và lưu vào cơ sở dữ liệu
    $explanation = wp_unslash($_POST['explanation']);
    $image_link = wp_unslash($_POST['image_link']);
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

    

    $inserted = $wpdb->insert('digital_sat_question_bank_math', $data);

    if ($inserted) {
        wp_send_json_success('New record created successfully');
    } else {
        wp_send_json_error('Error: ' . $wpdb->last_error);
    }

    // Redirect back to the main page
    wp_redirect('index.php');
    exit; // Always call exit after redirecting
}
?>
