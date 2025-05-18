<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input data
    $id_part = isset($_POST['id_part']) ? wp_kses_post($_POST['id_part']) : '';
    $part = isset($_POST['part']) ? wp_kses_post($_POST['part']) : '';
    $duration = isset($_POST['duration']) ? wp_kses_post($_POST['duration']) : '';
    $number_question_of_this_part = isset($_POST['number_question_of_this_part']) ? wp_kses_post($_POST['number_question_of_this_part']) : '';
    $paragraph = isset($_POST['paragraph']) ? wp_unslash($_POST['paragraph']) : '';
    $group_question = isset($_POST['group_question']) ? wp_unslash($_POST['group_question']) : '';
    $note = isset($_POST['note']) ? wp_unslash($_POST['note']) : '';

    // Decode and validate the category JSON data
    $category = isset($_POST['category']) ? wp_unslash($_POST['category']) : '';

 

    // Prepare the data for insertion
    $data = [
        'id_part' => $id_part,
        'part' => $part,
        'duration' => $duration,
        'number_question_of_this_part' => $number_question_of_this_part,
        'paragraph' => $paragraph,
        'group_question' => $group_question,
        'category' => $category,
        'note' => $note,
    ];

    // Define the data format
    $format = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];

    // Insert the data into the database
    $table_name = 'ielts_reading_part_1_question'; // Ensure table name has the correct prefix
    $inserted = $wpdb->insert($table_name, $data, $format);

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
