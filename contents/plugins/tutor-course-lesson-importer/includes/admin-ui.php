<?php
function tutor_excel_importer_menu() {
    add_menu_page(
        'Tutor Excel Importer',
        'Tutor Excel Importer',
        'manage_options',
        'tutor-excel-importer',
        'tutor_excel_importer_page'
    );
}
add_action('admin_menu', 'tutor_excel_importer_menu');

function tutor_excel_importer_page() {
    ?>
    <div class="wrap">
        <h1>Import Topics & Lessons from Excel</h1>
        <form method="post" enctype="multipart/form-data">
            <label for="course_id">Course ID:</label>
            <input type="number" name="course_id" required><br><br>

            <label for="excel_file">Excel File:</label>
            <input type="file" name="excel_file" accept=".xlsx" required><br><br>

            <input type="submit" name="submit_excel_import" class="button button-primary" value="Import">
        </form>
    </div>
    <?php
    if (isset($_POST['submit_excel_import'])) {
        $course_id = intval($_POST['course_id']);
        if (!empty($_FILES['excel_file']['tmp_name'])) {
            $file_path = $_FILES['excel_file']['tmp_name'];
            tutor_import_from_excel($course_id, $file_path);
        } else {
            echo '<div class="notice notice-error"><p>Please upload a valid Excel file.</p></div>';
        }
    }
}
