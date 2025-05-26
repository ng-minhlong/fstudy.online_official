<?php
/*
 * Template Name: Route and Prompt
 */
require_once(__DIR__ . '/../../../config-custom.php');

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

$total_sql = "SELECT COUNT(*) FROM order_and_prompt_api_list";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM order_and_prompt_api_list";
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
    <title>API ROUTE AND PROMPT</title>
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

             
<h1>API ROUTE AND PROMPT</h1>

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
        <th>last_use_end_point</th>
        <th>list_name_endpoint_order</th>
        <th>prompt_ielts_writing</th>
        <th>prompt_ielts_speaking</th>
        <th>prompt_conversation_ai</th>

        <th>Actions</th>
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
             
               
                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>{$row['last_use_end_point']}</td>
                        <td>{$row['list_name_endpoint_order']}</td>
                        
                        <td>" . nl2br($row['prompt_ielts_writing']) . "</td>
                        <td>" . nl2br($row['prompt_ielts_speaking']) . "</td>
                        <td>" . nl2br($row['prompt_conversation_ai']) . "</td>



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
<button class="btn btn-success" onclick="openAddModal()">Add New API Route</button>
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
                
                <h5 class="modal-title">Edit API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit_number" name="number">
                    
                    last_use_end_point<input type="text" id="edit_last_use_end_point" name="last_use_end_point" class="form-control" required><br>
                    list_name_endpoint_order<textarea type="text" id="edit_list_name_endpoint_order" name="list_name_endpoint_order" class="form-control" required></textarea><br>
                    prompt_ielts_writing: <textarea id="edit_prompt_ielts_writing" name="prompt_ielts_writing" class="form-control" required></textarea><br>
                    prompt_ielts_speaking: <textarea type="text" id="edit_prompt_ielts_speaking" name="prompt_ielts_speaking" class="form-control" required></textarea> <br>
                    prompt_conversation_ai: <textarea type="text" id="edit_prompt_conversation_ai" name="prompt_conversation_ai" class="form-control" required></textarea> <br>
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
                <h5 class="modal-title">Add New API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addForm">
                    
                 
                    last_use_end_point<input type="text" id="add_last_use_end_point" name="last_use_end_point" class="form-control" required><br>
                    list_name_endpoint_order<textarea type="text" id="add_list_name_endpoint_order" name="list_name_endpoint_order" class="form-control" required></textarea><br>
                    prompt_ielts_writing: <textarea id="add_prompt_ielts_writing" name="prompt_ielts_writing" class="form-control" required></textarea><br>
                    prompt_ielts_speaking: <textarea type="text" id="add_prompt_ielts_speaking" name="prompt_ielts_speaking" class="form-control" required></textarea> <br>
                    prompt_conversation_ai: <textarea type="text" id="add_prompt_conversation_ai" name="prompt_conversation_ai" class="form-control" required></textarea> <br>

                           
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveNew()">Add Changes</button>
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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/api_store/database_order_and_prompt/get.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            
            $('#edit_list_name_endpoint_order').val(data.list_name_endpoint_order);
            $('#edit_last_use_end_point').val(data.last_use_end_point);
            $('#edit_prompt_ielts_writing').val(data.prompt_ielts_writing);
            $('#edit_prompt_ielts_speaking').val(data.prompt_ielts_speaking);
            $('#edit_prompt_conversation_ai').val(data.prompt_conversation_ai);


            $('#editModal').modal('show');
        }
    });
}

function saveEdit() {
    $.ajax({
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/api_store/database_order_and_prompt/update.php',
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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/api_store/database_order_and_prompt/add.php',
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
            url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/api_store/database_order_and_prompt/delete.php',
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