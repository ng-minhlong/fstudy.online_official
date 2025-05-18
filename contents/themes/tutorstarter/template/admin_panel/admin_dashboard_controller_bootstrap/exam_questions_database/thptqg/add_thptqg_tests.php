<?php
/*
 * Template Name: THPTQG
 */


 $wp_load_paths = [
    $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php',
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
$id_test_filter = isset($_GET['id_test_filter']) ? $_GET['id_test_filter'] : '';

// Pagination logic
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset

$total_sql = "SELECT COUNT(*) FROM thptqg_question";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM thptqg_question";
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
    <title>THPTQG Questions Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="/contents/themes/tutorstarter/ielts-reading-tookit/script_database_1.js"></script>

    
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

                <h1>THPTQG Questions Database</h1>

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
        <th>Subject</th>
        <th>Year</th>
        <th>Test Name</th>
        <th>Link test file</th>
        <th>Time</th>
        <th>Number Question</th>
        <th>Test Code (JSON)</th>
        <th>Token Need</th>
        <th>Role Access</th>
        <th>Permissive Management</th>
        <th>Time Allow</th>
        <th>Actions</th>
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               
                
                $question_content_sanitized = htmlspecialchars(json_encode($row['testcode'], JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                $question_content_words = explode(' ', $row['testcode']);
                $question_content_display = count($question_content_words) > 20 ? implode(' ', array_slice($question_content_words, 0, 20)) . '...' : $row['testcode'];
                $question_content_view_more = count($question_content_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Question Content\", $question_content_sanitized)'>View More</button>" : '';
                 

                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>
                            <a href='http://localhost/test/thptqg/{$row['id_test']}' target='_blank'> {$row['id_test']}</a> 
                        </td>
                        <td>{$row['subject']}</td>
                        <td>{$row['year']}</td>
                        <td>{$row['testname']}</td>
                        <td>{$row['link_file']}</td>

                        <td>{$row['time']}</td>
                        <td>{$row['number_question']}</td>
                        <td>{$question_content_display} $question_content_view_more</td>
                        <td>{$row['token_need']}</td>
                        <td>{$row['role_access']}</td>
                        <td>{$row['permissive_management']}</td>
                        <td>{$row['time_allow']}</td>
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
                    
                    ID Test: <input type="text" id="edit_id_test" name="id_test" class="form-control" required readonly><br>

                    Subject:<select id="edit_subject" name="subject" class="form-control" required>
                            <option value="">Select Subject</option>
                            <option value="Toán học">Toán học</option>
                            <option value="Vật lý">Vật lý</option>
                            <option value="Hóa học">Hóa học</option>

                            <option value="Sinh học">Sinh học</option>
                            <option value="Lịch sử">Lịch sử</option>
                            <option value="Ngữ văn">Ngữ văn</option>


                            <option value="Tiếng anh">Tiếng anh</option>
                            <option value="Tiếng hàn">Tiếng hàn</option>
                            <option value="Tiếng nhật">Tiếng nhật</option>
                            <option value="Tiếng trung">Tiếng trung</option>

                            <option value="Tin học">Tin học</option>
                            <option value="GDKT_PL">GDKT_PL</option>
                            <option value="Địa lý">Địa lý</option>

                        </select><br>




                    year: <input type="text" id="edit_year" name="year" class="form-control" required><br>
                    Test name: <textarea id="edit_testname" name="testname" class="form-control" required></textarea><br>
                    Link file: <textarea id="edit_link_file" name="link_file" class="form-control" required></textarea><br>

                    Time: <input type = "number" id="edit_time" name="time" class="form-control"><br>
                    Number Question: <textarea id="edit_number_question" name="number_question" class="form-control"></textarea><br>
                    Test Code json: <textarea  id="edit_testcode" name="testcode" class="form-control" required></textarea> <br>
                    Token Need: <input type = "number" id="edit_token_need" name="token_need" class="form-control" required><br>
                    Role Access: <textarea  id="edit_role_access" name="role_access" class="form-control" required></textarea> <br>
                    Time Allow: <input type = "number"  id="edit_time_allow" name="time_allow" class="form-control" required> <br>
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
                    ID Test: <input type="text" id="add_id_test" name="id_test" class="form-control" required readonly><br>
                    <button type="button" id="generate_id_btn" class="btn btn-primary">Generate ID</button><br>


                    Subject:<select id="add_subject" name="subject" class="form-control" required>
                            <option value="">Select Subject</option>
                            <option value="Toán học">Toán học</option>
                            <option value="Vật lý">Vật lý</option>
                            <option value="Hóa học">Hóa học</option>

                            <option value="Sinh học">Sinh học</option>
                            <option value="Lịch sử">Lịch sử</option>
                            <option value="Ngữ văn">Ngữ văn</option>


                            <option value="Tiếng anh">Tiếng anh</option>
                            <option value="Tiếng hàn">Tiếng hàn</option>
                            <option value="Tiếng nhật">Tiếng nhật</option>
                            <option value="Tiếng trung">Tiếng trung</option>

                            <option value="Tin học">Tin học</option>
                            <option value="GDKT_PL">GDKT_PL</option>
                            <option value="Địa lý">Địa lý</option>

                        </select><br>                    
                    Year: <input type="text" id="add_year" name="year" class="form-control" required><br>
                    Test Name: <textarea id="add_testname" name="testname" class="form-control" required></textarea><br>
                    Link file: <textarea id="add_link_file" name="link_file" class="form-control" required></textarea><br>

                    Time: <textarea id="add_time" name="time" class="form-control"></textarea><br>
                    Number Question: <textarea id="add_number_question" name="number_question" class="form-control"></textarea><br>
                    Test Code: <textarea  id="add_testcode" name="testcode" class="form-control" required></textarea> <br>

                    Token Need: <input type = "number" id="add_token_need" name="token_need" class="form-control" required><br>
                    Role Access: <textarea  id="add_role_access" name="role_access" class="form-control" required></textarea> <br>
                    Time Allow: <input  type = "number" id="add_time_allow" name="time_allow" class="form-control" required> <br>

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
    document.getElementById("generate_id_btn").addEventListener("click", function() {
        let now = new Date();
        let timestamp = `${now.getSeconds()}${now.getMinutes()}${now.getHours()}${now.getDate()}${now.getMonth() + 1}`;
        let randomStr1 = Math.random().toString(36).substring(2, 4).toUpperCase(); // Random 2 ký tự
        let randomStr2 = Math.random().toString(36).substring(2, 6).toUpperCase(); // Random 4 ký tự
        let encoded = (timestamp + randomStr1 + randomStr2).toString(36).toUpperCase(); // Chuyển đổi base 36
        document.getElementById("add_id_test").value = encoded;
    });
// Open the edit modal and populate it with data
function openEditModal(number) {
    $.ajax({
        url: 'http://localhost/contents/themes/tutorstarter/template/thptqg/database-template/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_test').val(data.id_test);
            $('#edit_subject').val(data.subject);
            $('#edit_year').val(data.year);
            $('#edit_testname').val(data.testname);
            $('#edit_link_file').val(data.link_file);
            $('#edit_time').val(data.time);
            $('#edit_number_question').val(data.number_question);
            $('#edit_testcode').val(data.testcode);
            $('#edit_token_need').val(data.token_need);
            $('#edit_role_access').val(data.role_access);
            $('#edit_time_allow').val(data.time_allow);
            $('#editModal').modal('show');
        }
    });
}

// Save the edited data
function saveEdit() {
    $.ajax({
        url: 'http://localhost/contents/themes/tutorstarter/template/thptqg/database-template/update_question.php',
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
        url: 'http://localhost/contents/themes/tutorstarter/template/thptqg/database-template/add_question.php',
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
            url: 'http://localhost/contents/themes/tutorstarter/template/thptqg/database-template/delete_question.php',
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