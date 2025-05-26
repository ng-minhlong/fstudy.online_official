<?php
/*
 * Template Name: Token System
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
// Get type filter value
$type_filter = isset($_GET['type_filter']) ? $_GET['type_filter'] : '';


// Pagination logic
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Calculate offset



$where_clauses = [];
if ($id_test_filter) {
    $where_clauses[] = "id_test LIKE '%$id_test_filter%'";
}
if ($type_filter) {
    $where_clauses[] = "type = '$type_filter'";
}

$total_sql = "SELECT COUNT(*) FROM admin_create";
if (!empty($where_clauses)) {
    $total_sql .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql = "SELECT * FROM admin_create";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql .= " LIMIT $limit OFFSET $offset";

$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages
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
    <title>Admin Create</title>
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

                <h1>Admin Create</h1>

<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="id_test_filter" class="form-label">Filter by ID Test:</label>
            <input type="text" name="id_test_filter" id="id_test_filter" class="form-control" value="<?php echo isset($_GET['id_test_filter']) ? $_GET['id_test_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="?" class="btn btn-secondary">Clear Filter</a>

        <div class="mb-3">
    <label for="type_filter" class="form-label">Filter by Type:</label>
    <select name="type_filter" id="type_filter" class="form-control">
        <option value="">All Types</option>
        <option value="role" <?php echo (isset($_GET['type_filter']) && $_GET['type_filter'] == 'role') ? 'selected' : ''; ?>>Role</option>
        <option value="token" <?php echo (isset($_GET['type_filter']) && $_GET['type_filter'] == 'token') ? 'selected' : ''; ?>>Token</option>
    </select>
</div>
</form>

<!-- Display the data from the database -->
<table class="table table-bordered">
    <tr>
        <th>Number</th>
        <th>Last Updated</th>
        <th>Type</th>
        <th>ID (Meta Key)</th>
        <th>Role</th>
        <th>Token</th>
        <th>Time Expired (Month)</th>


        <th>Price Role</th>
        <th>Price Token</th>
        <th>Note Role</th>
        <th>Note Token</th>

        <th>Actions</th>
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
             
               
                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>{$row['date_created']}</td>
                        <td>{$row['type']}</td>
                        <td>{$row['id']}</td>
                        <td>{$row['user_role']}</td>
                        <td>{$row['user_token']}</td>
                        <td>{$row['time_expired']}</td>

                        <td>{$row['price_role']}</td>
                        <td>{$row['price_token']}</td>
                        <td>{$row['note_role']}</td>
                        <td>{$row['note_token']}</td>

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
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&id_test_filter=<?php echo $id_test_filter; ?>&type_filter=<?php echo $type_filter; ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&id_test_filter=<?php echo $id_test_filter; ?>&type_filter=<?php echo $type_filter; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&id_test_filter=<?php echo $id_test_filter; ?>&type_filter=<?php echo $type_filter; ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<!-- Button to Add New Record -->
<button class="btn btn-success" onclick="openAddModal()">Add New Object</button>
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
                
                <h5 class="modal-title">Edit Object</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_number" name="number">
                    ID Meta Key: <input type="text" id="edit_id" name="id" class="form-control" required><br>
                    Type: <select id="edit_type" name="type" class="form-control" required>
                            <option value="">Select a Type</option>
                            <option value="role">role</option>
                            <option value="token">token</option>

                        </select><br>
                    Role: <input type="text" id="edit_user_role" name="user_role" class="form-control" required><br>
                    Token: <input type="text" id="edit_user_token" name="user_token" class="form-control" required><br>
                    Time expired (Month): <input type="text" id="edit_time_expired" name="time_expired" class="form-control" required><br>
                    
                    
                    Price Role (In token): <input type="text" id="edit_price_role" name="price_role" class="form-control" required><br>
                    Price Token (VNĐ): <input type="text" id="edit_price_token" name="price_token" class="form-control" required><br>
                    Note Role: <textarea type="text" id="edit_note_role" name="note_role" class="form-control" required></textarea> <br>
                    Note Token: <textarea type="text" id="edit_note_token" name="note_token" class="form-control" required></textarea>  <br>

                
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
                <h5 class="modal-title">Add New Object</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addForm">                    
                    ID Meta Key: <input type="text" id="add_id" name="id" class="form-control" required><br>
                    Type: <select id="add_type" name="type" class="form-control" required>
                            <option value="">Select a Type</option>
                            <option value="role">role</option>
                            <option value="token">token</option>

                        </select><br>
                    Role: <input type="text" id="add_user_role" name="user_role" class="form-control" required><br>
                    Token: <input type="text" id="add_user_token" name="user_token" class="form-control" required><br>
                    Time expired (Month): <input type="text" id="add_time_expired" name="time_expired" class="form-control" required><br>
                
                    Price Role (In token): <input type="text" id="add_price_role" name="price_role" class="form-control" required><br>
                    Price Token (VNĐ): <input type="text" id="add_price_token" name="price_token" class="form-control" required><br>
                    Note Role: <textarea type="text" id="add_note_role" name="note_role" class="form-control" required></textarea> <br>
                    Note Token: <textarea type="text" id="add_note_token" name="note_token" class="form-control" required></textarea> <br>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveNew()">Add Token</button>
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

function formatTextWithLineBreaks(text) {
    return text.replace(/\n/g, '<br>');
}
// Open the edit modal and populate it with data
function openEditModal(number) {
  
    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/admin_create/get.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id').val(data.id);
            $('#edit_type').val(data.type);
            $('#edit_user_role').val(data.user_role);

            $('#edit_user_token').val(data.user_token);
            $('#edit_time_expired').val(data.time_expired);


            $('#edit_price_role').val(data.price_role);
            $('#edit_price_token').val(data.price_token);
            $('#edit_note_token').val(data.note_token);
            $('#edit_note_role').val(data.note_role);
           
            $('#editModal').modal('show');
        }
    });
}
// Save the edited data
function saveEdit() {


    let noteRole = $('#edit_note_role').val();
    let noteToken = $('#edit_note_token').val();
    $('#edit_note_role').val(formatTextWithLineBreaks(noteRole));
    $('#edit_note_token').val(formatTextWithLineBreaks(noteToken));
   
    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/admin_create/update.php',
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
    let noteRole = $('#edit_note_role').val();
    let noteToken = $('#edit_note_token').val();
    $('#edit_note_role').val(formatTextWithLineBreaks(noteRole));
    $('#edit_note_token').val(formatTextWithLineBreaks(noteToken));
    
 

    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/admin_create/add.php',
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
            url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/admin_create/delete.php',
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