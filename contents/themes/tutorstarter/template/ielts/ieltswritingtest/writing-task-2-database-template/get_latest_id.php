<?php
// Include WordPress functions
require_once(__DIR__ . '/../../../admin_panel/config-custom.php');

global $wpdb;
$latest = $wpdb->get_var("SELECT MAX(CAST(id_test AS UNSIGNED)) FROM ielts_writing_task_2_question");
$new_id = $latest ? $latest + 1 : 1;
echo $new_id;

?>