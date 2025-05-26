<?php
// Include WordPress functions
require_once(__DIR__ . '/../../admin_panel/config-custom.php');

global $wpdb;

$latest = $wpdb->get_var("SELECT MAX(CAST(SUBSTRING(id_question, 7) AS UNSIGNED)) FROM digital_sat_question_bank_verbal WHERE id_question LIKE 'verbal%'");
$new_id = $latest ? $latest + 1 : 1;
echo '' . $new_id;
?>
