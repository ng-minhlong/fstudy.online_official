<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input data and sanitize it
    $id_test = wp_kses_post($_POST['id_test']);
    $task = wp_kses_post($_POST['task']);
    $question_type = wp_kses_post($_POST['question_type']);
    $question_content = wp_kses_post($_POST['question_content']);
    $sample_writing = wp_kses_post($_POST['sample_writing']);
    $important_add = wp_kses_post($_POST['important_add']);
    $topic = wp_kses_post($_POST['topic']);
    $time = wp_kses_post($_POST['time']);
    // Prepare the data for insertion
    $data = array(
        'id_test' => $id_test,
        'task' => $task,
        'question_type' => $question_type,
        'question_content' => $question_content,
        'sample_writing' => $sample_writing,
        'important_add' => $important_add,
        'topic' => $topic,
        'time' => $time
    );

    // Insert the data into the database
    $inserted = $wpdb->insert('ielts_writing_task_2_question', $data);

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
