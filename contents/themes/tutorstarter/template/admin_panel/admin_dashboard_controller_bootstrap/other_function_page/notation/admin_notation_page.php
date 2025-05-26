<?php
/*
 * Template Name: Notation System
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

$total_sql = "SELECT COUNT(*) FROM notation";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM notation";
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
    <title>Notation Admin System</title>
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

                <h1>Admin Notation</h1>

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
        <th>Time Created</th>
        <th>Username</th>
        <th>Word Save</th>
        <th>Meaning/Explanation</th>
        <th>Source</th>
        <th>Test type </th>
        <th>ID Test</th>
        <th>Actions</th>
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
             
               
                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>{$row['save_time']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['word_save']}</td>
                        <td>{$row['meaning_or_explanation']}</td>
                        <td>{$row['is_source']}</td>
                        <td>{$row['test_type']}</td>
                        <td>{$row['id_test']}</td>
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
<button class="btn btn-success" onclick="openAddModal()">Add New Notation</button>
<!-- View More Modal -->
<div class="modal" id="viewMoreModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewMoreTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewMoreContent">
                <!-- Full word_save will be loaded here -->
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
                
                <h5 class="modal-title">Edit Notation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_number" name="number">
                    
                    Time Created: <input type="date" id="edit_save_time" name="save_time" class="form-control" required disabled><br>
                    Username: <textarea type="text" id="edit_username" name="username" class="form-control" required></textarea><br>
                    Word Save: <textarea type="text" id="edit_word_save" name="word_save" class="form-control" required></textarea><br>

                    Meaning/Explanation:<textarea id="edit_meaning_or_explanation" name="meaning_or_explanation" class="form-control" required></textarea><br><br>
                    
                    is_source: 
                    <select id="edit_is_source" name="is_source" class="form-control" required>
                        <option value=""></option>
                        <option value="Web">Web</option>
                        <option value="User">User</option>
                    
                    </select>




                    Test Type: <textarea id="edit_test_type" name="test_type" class="form-control" required></textarea><br>
                    id_test: <input id="edit_id_test" name="id_test" class="form-control" required><br>

                      
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
                    Time created: <input type="date" id="add_save_time" name="save_time" class="form-control" required><br>
                    Username: <textarea type="text" id="add_username" name="username" class="form-control" required></textarea><br>
                    Content: <textarea type="text" id="add_word_save" name="word_save" class="form-control" required></textarea><br>

                    Meaning/Explanation:<textarea id="add_meaning_or_explanation" name="meaning_or_explanation" class="form-control" required></textarea><br><br>
                    
                    is_source: 
                    <select id="add_is_source" name="is_source" class="form-control" required>
                        <option value=""></option>
                        <option value="User">User</option>
                        <option value="Web">Web</option>
                    
                    </select>
                    Test Type: <textarea id="add_test_type" name="test_type" class="form-control" required></textarea><br>
                    ID Test: <input id="add_id_test" name="id_test" class="form-control" required><br>

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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/notation/database/get_notation.php', 
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_save_time').val(data.save_time);
            $('#edit_username').val(data.username);
            $('#edit_word_save').val(data.word_save);
            $('#edit_meaning_or_explanation').val(data.meaning_or_explanation);
            $('#edit_is_source').val(data.is_source);
            $('#edit_test_type').val(data.test_type);
            $('#edit_id_test').val(data.id_test);

            $('#editModal').modal('show');
        }
    });
}
// Save the edited data
function saveEdit() {
  
    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/notation/database/update_notation.php',
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

function saveNew() {
 

    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/notation/database/add_notation.php',
        type: 'POST',
        data: $('#addForm').serialize(),
        success: function(response) {
            location.reload(); // Reload to show the new record
        }
    });
}

// Delete a record
function deleteRecord(number) {
    if (confirm('Are you sure you want to delete this notation?')) {
        $.ajax({
            url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/notation/database/delete_notation.php',
            type: 'POST',
            data: { number: number },
            success: function(response) {
                location.reload(); // Reload after deletion
            }
        });
    }
}
function showFullContent(title, content) {
    // Set the username of the modal
    $('#viewMoreTitle').text(title);

    // Set the word_save with HTML allowed
    $('#viewMoreContent').html(content);

    // Show the modal
    $('#viewMoreModal').modal('show');
}


</script>



</html>