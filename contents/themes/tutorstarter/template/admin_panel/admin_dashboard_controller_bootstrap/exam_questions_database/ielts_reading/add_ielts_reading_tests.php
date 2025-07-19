<?php
/*
 * Template Name: Test LIST Ielts Reading
 */


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

$total_sql = "SELECT COUNT(*) FROM ielts_reading_test_list";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM ielts_reading_test_list";
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
    <link href="../../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../../css/sb-admin-2.min.css" rel="stylesheet">

    <meta charset="UTF-8">
    <title>List các đề Ielts Reading Test</title>
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

     
<h1>List các đề Ielts Reading Test</h1>
<b>Check Valid json cho group_question tại: https://jsonlint.com/
</b>

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
        <th>Test Name</th>
        <th>Test Type</th>
        <th>Question Choose</th>
        <th>Tag</th>
        <th>Book</th>
        <th>Token Need</th>
        <th>Role Access</th>
        <th>Permissive Management</th>
        <th>Time Allow</th>
        <th>Created At</th>
        <th>Last update </th>

    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>
                            
                                <a href='http://localhost/fstudy/test/ielts/r/{$row['id_test']}' target='_blank'> {$row['id_test']}</a> 
                          
                        </td>
                        
                        <td>{$row['testname']}</td>
                        <td>{$row['test_type']}</td>
                        <td>";
        
                // Split the question_choose and generate links for each question
                $questions = explode(',', $row['question_choose']); // Tách các số trong question_choose
                foreach ($questions as $index => $question) {
                    $templatePartIndex = 'add_ielts_reading_part_' . ($index + 1) . '';

                    $templatePart = 'reading-part-' . ($index + 1) . '-database-template';
                    //echo "<a href='http://localhost/contents/themes/tutorstarter/template/ieltsreadingtest/$templatePart/database-question-content-sample.php?id_part_filter=$question' target='_blank'>$question</a>";
                    echo "<a href='http://localhost/fstudy/contents/themes/tutorstarter/template/admin_panel/admin_dashboard_controller_bootstrap/exam_questions_database/ielts_reading/$templatePartIndex.php?id_part_filter=$question' target='_blank'>$question</a>";

                    
                    
                    if ($index < count($questions) - 1) {
                        echo ", "; // Thêm dấu phẩy nếu chưa đến số cuối
                    }
                }
                http://localhost/contents/themes/tutorstarter/template/admin_panel/admin_dashboard_controller_bootstrap/exam_questions_database/ielts_reading/add_ielts_reading_part_1.php
                echo "</td>
                        <td>{$row['tag']}</td>
                        <td>{$row['book']}</td>

                        <td>{$row['token_need']}</td>
                        <td>{$row['role_access']}</td>
                        <td>{$row['permissive_management']}</td>
                        <td>{$row['time_allow']}</td>
                        <td>{$row['created_at']}</td>
                        <td>{$row['updated_at']}</td>
                        
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
                    


                    Test Name: <input type="text" id="edit_testname" name="testname" class="form-control" required><br>
                    Test Type:<select id="edit_test_type" name="test_type" class="form-control" required>
                            <option value="">Select a Test Type</option>
                            <option value="Practice">Practice</option>
                            <option value="Full Test">Full Test</option>

                        </select><br>
                    
                    Question Choice:<br>
                    (Mẫu:   reading part 1:_, reading part 2:_, reading part 3:_      ) 
                    <textarea id="edit_question_choose" name="question_choose" class="form-control" required></textarea><br>
                    Tag: <textarea id="edit_tag" name="tag" class="form-control"></textarea><br>
                   
                    Book:<select id="edit_book" name="book" class="form-control" required>
                            <option value="">Select a Book</option>
                            <option value="SAT Suite Question Bank">SAT Suite Question Bank</option>
                        </select><br>
                    
                    Token Need: <input type = "number" id="edit_token_need" name="token_need" class="form-control" required><br>
                    Role Access: <textarea  id="edit_role_access" name="role_access" class="form-control" required></textarea> <br>
                    Time Allow: <textarea  id="edit_time_allow" name="time_allow" class="form-control" required></textarea> <br>
                   
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
                    <input type="hidden" id="add_number" name="number">
                    ID Test: <input type="text" id="add_id_test" name="id_test" class="form-control" required readonly><br>
                    <button type="button" id="generate_id_btn" class="btn btn-primary">Generate ID</button><br>



                    Test Name: <input type="text" id="add_testname" name="testname" class="form-control" required><br>

                    Test Type:<select id="add_test_type" name="test_type" class="form-control" required>
                            <option value="">Select a Test Type</option>
                            <option value="Practice">Practice</option>
                            <option value="Full Test">Full Test</option>

                        </select><br>                    
                    
                    Question Choice:<br> 
                    (Mẫu:   reading part 1:_, reading part 2:_, reading part 3:_      ) 

                    <textarea id="add_question_choose" name="question_choose" class="form-control" required></textarea><br>
                    Tag: <textarea id="add_tag" name="tag" class="form-control"></textarea><br>
                    Book:<select id="add_book" name="book" class="form-control" required>
                            <option value="">Select a Book</option>
                            <option value="SAT Suite Question Bank">SAT Suite Question Bank</option>
                        </select><br>
                    Token Need: <input type = "number" id="add_token_need" name="token_need" class="form-control" required><br>
                    Role Access: <textarea  id="add_role_access" name="role_access" class="form-control" required></textarea> <br>
                    Time Allow: <textarea  id="add_time_allow" name="time_allow" class="form-control" required></textarea> <br>

                   
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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/test-list/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_test').val(data.id_test);
            $('#edit_testname').val(data.testname);
            $('#edit_test_type').val(data.test_type);
            $('#edit_question_choose').val(data.question_choose);
            $('#edit_tag').val(data.tag);
            $('#edit_token_need').val(data.token_need);
            $('#edit_role_access').val(data.role_access);
            $('#edit_time_allow').val(data.time_allow);
            $('#edit_book').val(data.book);
            $('#editModal').modal('show');
        }
    });
}

// Save the edited data
function saveEdit() {
    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/test-list/update_question.php',
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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/test-list/add_question.php',
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
            url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/ielts/ieltsreadingtest/test-list/delete_question.php',
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