<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

$latest = $wpdb->get_var("SELECT MAX(CAST(SUBSTRING(id, 11) AS UNSIGNED)) FROM list_vocabulary WHERE id LIKE 'vocabulary%'");
$new_id = $latest ? $latest + 1 : 1;
echo '' . $new_id;
?>