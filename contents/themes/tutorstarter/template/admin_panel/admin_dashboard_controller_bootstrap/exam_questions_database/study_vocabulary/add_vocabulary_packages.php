<?php
/*
 * Template Name: Package Vocabulary Test
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

$total_sql = "SELECT COUNT(*) FROM list_vocabulary_package";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM list_vocabulary_package";
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
    <title> Package Vocabulary Test</title>
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
<style>
        /* Ẩn popup mặc định */
        #popup-note {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Hiệu ứng mở popup */
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -55%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        /* Overlay làm mờ nền */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
        }

        /* Nút đóng */
        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
            background: none;
            border: none;
        }

        .close-btn:hover {
            color: red;
        }

        /* Nút mở popup */
        #open-popup {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        #open-popup:hover {
            background: #0056b3;
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

                <h1>List Package Test Vocabulary</h1>

                <div id="overlay"></div>
                <div id="popup-note"  style="white-space: pre;">
                    <button class="close-btn" onclick="closePopup()">✖</button>
                    Code python xuất package detail sang json_decode:


import json
data = """
2	19521055FFTVGH	New Words Digital Sat Pack 1	Full Test	vocabulary1, vocabulary2, vocabulary3... 	 
3	32531055HIT7O3	New Words Digital Sat Pack 2	Full Test	vocabulary21, vocabulary22, vocabulary23... 	 
4	41531055PS7FSM	New Words Digital Sat Pack 3	Full Test	vocabulary41, vocabulary42, vocabulary43... 	 
5	475310558830K8	New Words Digital Sat Pack 4	Full Test	vocabulary61, vocabulary62, vocabulary63... 	 	
"""

# Tách dòng và lấy ID Test từ cột thứ 2
ids = [line.split('\t')[1] for line in data.strip().split('\n') if line.strip()]

# Chuyển sang JSON
json_output = json.dumps(ids)

print(json_output)

            </div>


<!-- Filter form -->
<form method="GET" action="">
        <div class="mb-3">
            <label for="id_test_filter" class="form-label">Filter by ID Test:</label>
            <input type="text" name="id_test_filter" id="id_test_filter" class="form-control" value="<?php echo isset($_GET['id_test_filter']) ? $_GET['id_test_filter'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="?" class="btn btn-secondary">Clear Filter</a>
    </form>
    <button id="open-popup">Xem ghi chú các câu/ các loại</button>

<!-- Display the data from the database -->
<table class="table table-bordered">
    <tr>
        <th>Number</th>
        <th>Date Created </th>
        <th>ID Package</th>
        <th>Package Name</th>
        <th>Package Category</th>
        <th>Package Details</th>
        <th>Number Vocab</th>
        <th>Package Note</th>
        <th>Status</th>

        
    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Process "Sample" and "Important Add" columns
                $sample_words = explode(' ', $row['package_name']);
                $sample_display = count($sample_words) > 20 ? implode(' ', array_slice($sample_words, 0, 20)) . '...' : $row['package_name'];
                $sample_view_more = count($sample_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Test Name\", \"{$row['package_name']}\")'>View More</button>" : '';

                $important_words = explode(',', $row['package_detail']);
                if (count($important_words) > 3) {
                    $important_display = implode(',', array_slice($important_words, 0, 3)) . '...';
                    $important_view_more = "<button class='btn btn-link' onclick='showFullContent(\"Question Choose\", \"{$row['package_detail']}\")'>View More</button>";
                } else {
                    $important_display = $row['package_detail'];
                    $important_view_more = '';
                }

                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>{$row['date_created']}</td>

                        <td>
                            <a href='http://localhost/practice/vocabulary/package/{$row['id_test']}' target='_blank'> {$row['id_test']}</a> 
                        </td>
                        <td>{$row['package_name']}</td>
                        <td>{$row['package_category']}</td>
                        <td>{$important_display} $important_view_more</td>          
                        <td>{$row['number_of_vocab']}</td>
                        <td>{$row['package_note']}</td>
                        <td>{$row['status']}</td>
                        

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
                    ID Package: <input type="text" id="edit_id_test" name="id_test" class="form-control" required readonly><br>
                    Package Name: <input type="text" id="edit_package_name" name="package_name" class="form-control" required><br>
                    Package Type:<select id="edit_package_category" name="package_category" class="form-control" required>
                            <option value="">Select a Package Type</option>
                            <option value="Practice">Practice</option>
                            <option value="Full Test">Full Test</option>

                        </select><br>
                    Status:<select id="edit_status" name="status" class="form-control" required>
                            <option value="">Select a status</option>
                            <option value="Completed">Completed</option>
                            <option value="In Progress">In Progress</option>

                        </select><br>
                    Package Note: <input type="text" id="edit_package_note" name="package_note" class="form-control" required ><br>

                    
                    Question Choice: 
                    <textarea id="edit_package_detail" name="package_detail" class="form-control" required></textarea><br>
                  
                   
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
                    ID Package: <input type="text" id="add_id_test" name="id_test" class="form-control" required readonly><br>
                    <button type="button" id="generate_id_btn" class="btn btn-primary">Generate ID</button><br>

                    Package Name: <input type="text" id="add_package_name" name="package_name" class="form-control" required><br>

                    Package Type:<select id="add_package_category" name="package_category" class="form-control" required>
                            <option value="">Select a Package Type</option>
                            <option value="Practice">Practice</option>
                            <option value="Full Test">Full Test</option>

                        </select><br>            
                    Status:<select id="add_status" name="status" class="form-control" required>
                            <option value="">Select a status</option>
                            <option value="Completed">Completed</option>
                            <option value="In Progress">In Progress</option>

                        </select><br>
                    Package Note: <input type="text" id="add_package_note" name="package_note" class="form-control" required ><br>

                    
                    Question Choice: 

                    <textarea id="add_package_detail" name="package_detail" class="form-control" required></textarea><br>
                  

                   
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
     function openPopup() {
            document.getElementById("popup-note").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }

        function closePopup() {
            document.getElementById("popup-note").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }

        document.getElementById("open-popup").addEventListener("click", openPopup);
        document.getElementById("overlay").addEventListener("click", closePopup);

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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/studyvocabulary/package-list/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            var data = JSON.parse(response);
            $('#edit_number').val(data.number);
            $('#edit_id_test').val(data.id_test);
            $('#edit_package_name').val(data.package_name);
            $('#edit_package_note').val(data.package_note);


            $('#edit_package_category').val(data.package_category);
            $('#edit_package_detail').val(data.package_detail);

            $('#editModal').modal('show');
        }
    });
}

// Save the edited data
function saveEdit() {
    $.ajax({
        url: '<?php echo get_site_url()?>t/contents/themes/tutorstarter/template/studyvocabulary/package-list/update_question.php',
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
        url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/studyvocabulary/package-list/add_question.php',
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
            url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/studyvocabulary/package-list/delete_question.php',
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