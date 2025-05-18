<?php
/*
 * Template Name: Content Question DATABASE
 */


 $wp_load_paths = [
    $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php', // Local (có thư mục wordpress)
];

foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        break;
    }
}

// Kiểm tra nếu chưa load được WordPress
if (!defined('DB_HOST')) {
    die("Error: Unable to load WordPress configuration.");
}
 $servername = DB_HOST;
 $username = DB_USER;
 $password = DB_PASSWORD;
 $dbname = DB_NAME;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter value from the form input
$id_question_filter = isset($_GET['id_question_filter']) ? $_GET['id_question_filter'] : '';

// Pagination logic
$limit = 20; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

$total_sql = "SELECT COUNT(*) FROM ielts_speaking_part_3_question";
if ($id_question_filter) {
    $total_sql .= " WHERE id_question LIKE '%$id_question_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM ielts_speaking_part_3_question";
if ($id_question_filter) {
    $sql .= " WHERE id_question LIKE '%$id_question_filter%'"; // Apply filter to the SQL query
}
$sql .= " LIMIT $limit OFFSET $offset"; // Add pagination limits
$result = $conn->query($sql);
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">


    <!-- Custom fonts for this template-->
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">

    <meta charset="UTF-8">
    <title>IELTS Speaking Part 3 Questions Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="/wordpress/contents/themes/tutorstarter/ielts-reading-tookit/script_database_1.js"></script>

    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
       
    </style>

</head>


<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

    <?php include('../../sidebar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

            <?php include('../../topbar.php'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

         
<h1>IELTS Speaking Part 3 Questions Database</h1>
<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="id_test_filter" class="form-label">Filter by ID Test:</label>
            <input type="text" name="id_test_filter" id="id_test_filter" class="form-control" value="<?php echo isset($_GET['id_test_filter']) ? $_GET['id_test_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="?" class="btn btn-secondary">Clear Filter</a>
    </form>

<!-- Display the data from the database -->
<table class="table table-bordered">
    <tr>
        <th>Number</th>
        <th>ID Test</th>
        <th>Topic</th>
        <th>STT</th>
        <th>Question Content</th>
        <th>Sample</th>
        <th>Important Add</th>
        <th>Speaking Part</th>
        <th>Actions</th>
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Process "Sample" and "Important Add" columns
                $sample_words = explode(' ', $row['sample']);
                $sample_display = count($sample_words) > 20 ? implode(' ', array_slice($sample_words, 0, 20)) . '...' : $row['sample'];
                $sample_view_more = count($sample_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Sample\", \"{$row['sample']}\")'>View More</button>" : '';

                $important_words = explode(' ', $row['important_add']);
                $important_display = count($important_words) > 20 ? implode(' ', array_slice($important_words, 0, 20)) . '...' : $row['important_add'];
                $important_view_more = count($important_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Important Add\", \"{$row['important_add']}\")'>View More</button>" : '';

                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>{$row['id_test']}</td>
                        <td>{$row['topic']}</td>
                        <td>{$row['stt']}</td>
                        <td>{$row['question_content']}</td>
                        <td>{$sample_display} $sample_view_more</td>
                        <td>{$important_display} $important_view_more</td>
                        <td>{$row['speaking_part']}</td>
                        <td>
                            <button class='btn btn-primary btn-sm' onclick='openEditModal({$row['number']})'>Edit</button>
                            <button class='btn btn-danger btn-sm' onclick='deleteRecord({$row['number']})'>Delete</button>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No data found</td></tr>";
        }
        ?>
</table>
  <!-- Pagination buttons -->
  <nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=1&id_question_filter=<?php echo $id_question_filter; ?>">First</a>
            </li>

            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&id_question_filter=<?php echo $id_question_filter; ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php 
        // Define the range of page buttons to display
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);

        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&id_question_filter=<?php echo $id_question_filter; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&id_question_filter=<?php echo $id_question_filter; ?>">Next</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $total_pages; ?>&id_question_filter=<?php echo $id_question_filter; ?>">Last</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<!-- Button to Add New Record -->
<button class="btn btn-success" onclick="openAddModal()">Add New Question</button>
<!-- View More Modal -->
<div class="modal" id="viewMoreModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMoreTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewMoreContent">
                <!-- Full content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                
                <h5 class="modal-title">Edit Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_number" name="number">
                    ID Test: <input type="text" id="edit_id_test" name="id_test" class="form-control" required><br>
                    Topic: <input type="text" id="edit_topic" name="topic" class="form-control" required><br>
                    STT: <input type="number" id="edit_stt" name="stt" class="form-control" required><br>
                    Question Content: <textarea id="edit_question_content" name="question_content" class="form-control" required></textarea><br>
                    Sample: <textarea id="edit_sample" name="sample" class="form-control"></textarea><br>
                    Important Add: <textarea id="edit_important_add" name="important_add" class="form-control"></textarea><br>
                    Speaking Part: <input type="number" id="edit_speaking_part" name="speaking_part" class="form-control" required><br>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="saveEdit()">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    ID Test: <input type="text" id="add_id_test" name="id_test" class="form-control" required><br>
                    Topic: <input type="text" id="add_topic" name="topic" class="form-control" required><br>
                    STT: <input type="number" id="add_stt" name="stt" class="form-control" required><br>
                    Question Content: <textarea id="add_question_content" name="question_content" class="form-control" required></textarea><br>
                    Sample: <textarea id="add_sample" name="sample" class="form-control"></textarea><br>
                    Important Add: <textarea id="add_important_add" name="important_add" class="form-control"></textarea><br>
                    Speaking Part: <input type="number" id="add_speaking_part" name="speaking_part" class="form-control" value="3" readonly required><br>
                    </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveNew()">Add Question</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



</div>

                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include('../../footer.php'); ?>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>


    <!-- Bootstrap core JavaScript-->
    <script src="../../vendor/jquery/jquery.min.js"></script>
    <script src="../../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../../js/sb-admin-2.min.js"></script>

</body>

<!-- jQuery and JavaScript for AJAX -->
<script>
// Open the edit modal and populate it with data
function openEditModal(number) {
    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/ielts/ieltsspeakingtests/speaking-part-3-database-template/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_test').val(data.id_test);
            $('#edit_topic').val(data.topic);
            $('#edit_stt').val(data.stt);
            $('#edit_question_content').val(data.question_content);
            $('#edit_sample').val(data.sample);
            $('#edit_important_add').val(data.important_add);
            $('#edit_speaking_part').val(data.speaking_part);
            $('#editModal').modal('show');
        }
    });
}

// Save the edited data
function saveEdit() {
    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/ielts/ieltsspeakingtests/speaking-part-3-database-template/update_question.php',
        type: 'POST',
        data: $('#editForm').serialize(),
        success: function(response) {
            location.reload(); // Reload the page to reflect changes
        }
    });
}

function openAddModal() {
    $('#addForm')[0].reset(); // Clear form data
    $('#addModal').modal('show'); // Show the modal
}

// Save the new question
function saveNew() {
    $.ajax({
        url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/ielts/ieltsspeakingtests/speaking-part-3-database-template/add_question.php',
        type: 'POST',
        data: $('#addForm').serialize(),
        success: function(response) {
            location.reload(); // Reload to show the new record
        }
    });
}

// Delete a record
function deleteRecord(number) {
    if (confirm('Are you sure you want to delete this question?')) {
        $.ajax({
            url: 'http://localhost/wordpress/contents/themes/tutorstarter/template/ielts/ieltsspeakingtests/speaking-part-3-database-template/delete_question.php',
            type: 'POST',
            data: { number: number },
            success: function(response) {
                location.reload(); // Reload after deletion
            }
        });
    }
}
function showFullContent(title, content) {
    // Set the title of the modal
    $('#viewMoreTitle').text(title);

    // Set the content with HTML allowed
    $('#viewMoreContent').html(content);

    // Show the modal
    $('#viewMoreModal').modal('show');
}


</script>




</html>