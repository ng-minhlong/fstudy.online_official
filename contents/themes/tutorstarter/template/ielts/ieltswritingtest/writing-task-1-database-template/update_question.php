<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

// Get the data from the POST request
$number = wp_kses_post($_POST['number']);
$id_test = wp_kses_post($_POST['id_test']);
$task = wp_kses_post($_POST['task']);
$question_type = wp_kses_post($_POST['question_type']);

// Escape content to retain line breaks
$question_content = wp_kses_post($_POST['question_content']);
$sample_writing = wp_kses_post($_POST['sample_writing']);
$important_add = wp_kses_post($_POST['important_add']);
$image_link = wp_kses_post($_POST['image_link']);
$time = wp_kses_post($_POST['time']);

// Prepare the data for updating
$data = array(
    'id_test' => $id_test,
    'task' => $task,
    'question_type' => $question_type,
    'question_content' => $question_content,
    'sample_writing' => $sample_writing,
    'important_add' => $important_add,
    'image_link' => $image_link,
    'time' => $time,

);

// Update the record in the database
$wpdb->update('ielts_writing_task_1_question', $data, array('number' => $number));

// Return a response
echo json_encode(array('status' => 'success'));
?>
