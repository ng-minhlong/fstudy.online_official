<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;

// Validate and sanitize POST inputs
$number = isset($_POST['number']) ? wp_kses_post($_POST['number']) : '';
$id_part = isset($_POST['id_part']) ? wp_kses_post($_POST['id_part']) : '';
$part = isset($_POST['part']) ? wp_kses_post($_POST['part']) : '';
$duration = isset($_POST['duration']) ? wp_kses_post($_POST['duration']) : '';
$audio_link = isset($_POST['audio_link']) ? wp_unslash($_POST['audio_link']) : '';
$group_question = isset($_POST['group_question']) ? wp_unslash($_POST['group_question']) : '';
$note = isset($_POST['note']) ? wp_kses_post($_POST['note']) : '';

// Decode and validate the category JSON

$category = isset($_POST['category']) ? wp_unslash($_POST['category']) : '';


// Prepare the data for updating
$data = [
    'id_part' => $id_part,
    'part' => $part,
    'duration' => $duration,
    'audio_link' => $audio_link,
    'group_question' => $group_question,
    'category' => $category,
    'note' => $note,
];

// Update the record in the database
$table_name = 'ielts_listening_part_2_question'; // Use table prefix for compatibility
$where = ['number' => $number];
$updated = $wpdb->update($table_name, $data, $where);

// Check for errors or success
if ($updated !== false) {
    echo json_encode(['status' => 'success', 'message' => 'Record updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => $wpdb->last_error]);
}
?>
