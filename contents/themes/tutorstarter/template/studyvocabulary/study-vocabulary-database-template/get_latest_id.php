<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

$latest = $wpdb->get_var("SELECT MAX(CAST(SUBSTRING(id, 11) AS UNSIGNED)) FROM list_vocabulary WHERE id LIKE 'vocabulary%'");
$new_id = $latest ? $latest + 1 : 1;
echo '' . $new_id;
?>