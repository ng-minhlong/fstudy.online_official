<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;
$latest = $wpdb->get_var("SELECT MAX(CAST(id_test AS UNSIGNED)) FROM ielts_writing_task_2_question");
$new_id = $latest ? $latest + 1 : 1;
echo $new_id;

?>