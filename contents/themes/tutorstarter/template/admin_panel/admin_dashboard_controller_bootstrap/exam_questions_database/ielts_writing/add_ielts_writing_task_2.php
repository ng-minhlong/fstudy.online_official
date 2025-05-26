<?php


require_once(__DIR__ . '/../../../config-custom.php');

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
$id_test_filter = isset($_GET['id_test_filter']) ? $_GET['id_test_filter'] : '';

// Pagination logic
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

$total_sql = "SELECT COUNT(*) FROM ielts_writing_task_2_question";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM ielts_writing_task_2_question";
if ($id_test_filter) {
    $sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to the SQL query
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
    <title>IELTS Writing Task 2 Questions Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    
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

                <h1>IELTS Writing Task 2 Questions Database</h1>
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
        <th>Task</th>
        <th>Question Type</th>
        <th>Question Content</th>
        <th>Topic</th>
        <th>Time for test (minute)</th>
        <th>Sample Band 7</th>
        <th>Important Add</th>
        <th>Actions</th>
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Process "Sample" and "Important Add" columns
                $sample_words = explode(' ', $row['sample_writing']);
                $sample_display = count($sample_words) > 20 ? implode(' ', array_slice($sample_words, 0, 20)) . '...' : $row['sample_writing'];
                $sample_view_more = count($sample_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Sample\", \"{$row['sample_writing']}\")'>View More</button>" : '';

                $important_words = explode(' ', $row['important_add']);
                $important_display = count($important_words) > 20 ? implode(' ', array_slice($important_words, 0, 20)) . '...' : $row['important_add'];
                $important_view_more = count($important_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Important Add\", \"{$row['important_add']}\")'>View More</button>" : '';

                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>


                        <td>{$row['id_test']}</td>
                        <td>{$row['task']}</td>
                        <td>{$row['question_type']}</td>
                        <td>{$row['question_content']}</td>
                        <td>{$row['topic']}</td>
                        <td>{$row['time']}</td>
                        <td>{$sample_display} $sample_view_more</td>
                        <td>{$important_display} $important_view_more</td>
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
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&id_test_filter=<?php echo $id_test_filter; ?>">Previous</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&id_test_filter=<?php echo $id_test_filter; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&id_test_filter=<?php echo $id_test_filter; ?>">Next</a></li>
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
                    Task: <input type="number" id="edit_task" name="task" class="form-control" required><br>
                    Question Type: 
                    <select id="edit_question_type" name="question_type" class="form-control" required>
                        <option value="">Select a Question Type</option>
                        <option value="Argumentative/Opinion/Agree or Disagree">Argumentative/Opinion/Agree or Disagree </option>
                        <option value="Advantages and Disadvantages">Advantages and Disadvantages</option>
                        <option value="Discussion">Discussion</option>
                        <option value="Causes and Effects/Causes and Solutions/Problems and Solutions">Causes and Effects/Causes and Solutions/Problems and Solutions</option>
                        <option value="Two-Part Question">Two-Part Question</option>
                    </select><br>
                    
                    Question Content: <textarea id="edit_question_content" name="question_content" class="form-control" required></textarea><br>
                    Sample Band 7: <textarea  id="edit_sample_writing" name="sample_writing" class="form-control"></textarea><br>
                    Time (seconds): <textarea  id="edit_time" name="time" class="form-control"></textarea><br>

                    Important Add: <textarea id="edit_important_add" name="important_add" class="form-control"></textarea><br>
                    Topic: <input type="text" id="edit_topic" name="topic" class="form-control" required><br>
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
                    Task: <input type="number" id="add_task" name="task" class="form-control" value="2" readonly required><br>
                    Question Type: 
                    <select id="add_question_type" name="question_type" class="form-control" required>
                        <option value="">Select a Question Type</option>
                        <option value="Argumentative/Opinion/Agree or Disagree">Argumentative/Opinion/Agree or Disagree </option>
                        <option value="Advantages and Disadvantages Essay">Advantages and Disadvantages Essay</option>
                        <option value="Discussion Essay">Discussion Essay</option>
                        <option value="Causes and Effects/Causes and Solutions/Problems and Solutions Essay">Causes and Effects/Causes and Solutions/Problems and Solutions Essay</option>
                        <option value="Two-Part Question Essay">Two-Part Question Essay</option>
                    </select><br>

                    Question Content: <textarea id="add_question_content" name="question_content" class="form-control" required></textarea><br>
                    Sample: <textarea id="add_sample_writing" name="sample_writing" class="form-control"></textarea><br>
                    Time (seconds): <input id="add_time" name="time" class="form-control" value="40" readonly required><br>

                    Important Add: <textarea id="add_important_add" name="important_add" class="form-control"></textarea><br>
                    Topic: <input type="text" id="add_topic" name="topic" class="form-control" required><br>
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


// Open the edit modal and populate it with data
function openEditModal(number) {
    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltswritingtest/writing-task-2-database-template/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_test').val(data.id_test);
            $('#edit_task').val(data.task);
            $('#edit_question_type').val(data.question_type);
            $('#edit_question_content').val(data.question_content);
            $('#edit_sample_writing').val(data.sample_writing);
            $('#edit_time').val(data.time);

            $('#edit_important_add').val(data.important_add);
            $('#edit_topic').val(data.topic);
            $('#editModal').modal('show');
        }
    });
}
function formatTextWithLineBreaks(text) {
    return text.replace(/\n/g, '<br>');
}
// Save the edited data
function saveEdit() {
    let questionContent = $('#edit_question_content').val();
    const sample = $('#edit_sample_writing').val();
    $('#edit_question_content').val(formatTextWithLineBreaks(questionContent));
    $('#edit_sample_writing').val(formatTextWithLineBreaks(sample));


    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltswritingtest/writing-task-2-database-template/update_question.php',
        type: 'POST',
        data: $('#editForm').serialize(),
        success: function(response) {
            location.reload(); // Reload the page to reflect changes
        }
    });
}

function openAddModal() {
    $('#addForm')[0].reset();

    // Gọi API để lấy ID mới nhất
    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltswritingtest/writing-task-2-database-template/get_latest_id.php',
        type: 'GET',
        success: function(response) {
            $('#add_id_test').val(response);
        }
    });

    $('#addModal').modal('show');
}

// Save the new question
function saveNew() {
    let questionContent = $('#add_question_content').val();
    const sample = $('#add_sample_writing').val();
    $('#add_question_content').val(formatTextWithLineBreaks(questionContent));
    $('#add_sample_writing').val(formatTextWithLineBreaks(sample));

    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltswritingtest/writing-task-2-database-template/add_question.php',
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
            url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltswritingtest/writing-task-2-database-template/delete_question.php',
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