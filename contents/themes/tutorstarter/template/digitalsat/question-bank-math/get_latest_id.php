<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

$latest = $wpdb->get_var("SELECT MAX(CAST(SUBSTRING(id_question, 5) AS UNSIGNED)) FROM digital_sat_question_bank_math WHERE id_question LIKE 'math%'");
$new_id = $latest ? $latest + 1 : 1;
echo '' . $new_id;
?>