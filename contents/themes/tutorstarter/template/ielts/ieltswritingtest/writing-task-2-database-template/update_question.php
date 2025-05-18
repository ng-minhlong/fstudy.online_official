<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

// Get the data from the POST request
$number = intval($_POST['number']);
$id_test = wp_kses_post($_POST['id_test']);
$task = sanitize_text_field($_POST['task']);
$question_type = wp_kses_post($_POST['question_type']);

// Escape content to retain line breaks
$question_content = wp_kses_post($_POST['question_content']);
$sample_writing = wp_kses_post($_POST['sample_writing']);
$time = wp_kses_post($_POST['time']);
$important_add = wp_kses_post($_POST['important_add']);
$topic = wp_kses_post($_POST['topic']);

// Prepare the data for updating
$data = array(
    'id_test' => $id_test,
    'task' => $task,
    'question_type' => $question_type,
    'question_content' => $question_content,
    'sample_writing' => $sample_writing,
    'time' => $time,
    'important_add' => $important_add,
    'topic' => $topic,
);

// Update the record in the database
$wpdb->update('ielts_writing_task_2_question', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
