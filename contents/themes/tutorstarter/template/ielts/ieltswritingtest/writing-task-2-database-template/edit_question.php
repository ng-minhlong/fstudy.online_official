<?php
// Include WordPress functions
require_once('C:\xampp\htdocs\wp-load.php'); // Adjust the path as necessary

global $wpdb;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $number = intval($_POST['number']);
    
    // Fetch the record from the database
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM ielts_writing_task_2_question WHERE number = %d", $number), ARRAY_A);

    if ($row) {
?>
        <form method="post" action="update_question.php">
            <input type="hidden" name="number" value="<?php echo esc_attr($row['number']); ?>">
            ID Test: <input type="text" name="id_test" value="<?php echo esc_attr($row['id_test']); ?>" required><br>
            Task: <input type="number" name="task" value="<?php echo esc_attr($row['task']); ?>" required><br>
            Question Type: <input type="number" name="question_type" value="<?php echo esc_attr($row['question_type']); ?>" required><br>
            Question Content: <textarea name="question_content" required><?php echo esc_textarea($row['question_content']); ?></textarea><br>
            Sample: <textarea name="sample"><?php echo esc_textarea($row['sample_writing']); ?></textarea><br>
            Time (minute): <input type="text" name="time" value="<?php echo esc_attr($row['time']); ?>" required><br>
            Important Add: <textarea name="important_add"><?php echo esc_textarea($row['important_add']); ?></textarea><br>
            Topic: <input type="text" name="topic" value="<?php echo esc_attr($row['topic']); ?>" required><br>
            <input type="submit" value="Update">
        </form>
<?php
    } else {
        echo "No record found.";
    }
}
?>
