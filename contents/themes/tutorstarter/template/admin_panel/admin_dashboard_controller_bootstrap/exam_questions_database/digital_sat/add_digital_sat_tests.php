<?php
/*
 * Template Name: Test LIST DIGITAL SAT
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

$total_sql = "SELECT COUNT(*) FROM digital_sat_test_list";
if ($id_test_filter) {
    $total_sql .= " WHERE id_test LIKE '%$id_test_filter%'"; // Apply filter to total count
}
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit); // Calculate total pages

$sql = "SELECT * FROM digital_sat_test_list";
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
    <title>List các đề Digital SAT</title>
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

                <h1>List các đề Digital SAT Database</h1>


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
        <th>Number of Question</th>
        <th>Time (minutes)</th>
        <th>Test Type</th>
        <th>Question Choose</th>
        <th>Specific Module Fll Test</th>

        <th>Tag</th>
        <th>Book</th>
        <th>Token Need (per 1 test/time)</th>
        <th>Role Access</th>
        <th>Permissive Management</th>
        <th>Time Allow</th>
        <th>Action</th>

    </tr>

    <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Process "Sample" and "Important Add" columns
                $sample_words = explode(' ', $row['testname']);
                $sample_display = count($sample_words) > 20 ? implode(' ', array_slice($sample_words, 0, 20)) . '...' : $row['testname'];
                $sample_view_more = count($sample_words) > 20 ? "<button class='btn btn-link' onclick='showFullContent(\"Test Name\", \"{$row['testname']}\")'>View More</button>" : '';

                $important_words = explode(',', $row['question_choose']);
                if (count($important_words) > 3) {
                    $important_display = implode(',', array_slice($important_words, 0, 3)) . '...';
                    $important_view_more = "<button class='btn btn-link' onclick='showFullContent(\"Question Choose\", \"{$row['question_choose']}\")'>View More</button>";
                } else {
                    $important_display = $row['question_choose'];
                    $important_view_more = '';
                }

                $important_words_2 = explode(',', $row['full_test_specific_module']);
                if (count($important_words_2) > 3) {
                    $important_display_2 = implode(',', array_slice($important_words_2, 0, 3)) . '...';
                    $important_safe_2 = htmlspecialchars(json_encode($row['full_test_specific_module']), ENT_QUOTES);
                    $important_view_more_2 = "<button class='btn btn-link' onclick='showFullContent2(\"Specific Question Choose\", $important_safe_2)'>View More</button>";

                
                } else {
                    $important_display_2 = $row['full_test_specific_module'];
                    $important_view_more_2 = '';
                }


                echo "<tr id='row_{$row['number']}'>
                        <td>{$row['number']}</td>
                        <td>
                            
                                <a href='http://localhost/test/digitalsat/{$row['id_test']}' target='_blank'> {$row['id_test']}</a> 
                          
                        </td>
                        <td>{$row['testname']}</td>
                        <td>{$row['number_question']}</td>
                        <td>{$row['time']}</td>
                        <td>{$row['test_type']}</td>
                        <td>{$important_display} $important_view_more</td>   
                        <td>{$important_display_2} $important_view_more_2</td>   

       
                        <td>{$row['tag']}</td>
                        <td>{$row['book']}</td>
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
                    ID Test: <input type="text" id="edit_id_test" name="id_test" class="form-control" required readonly ><br>

            


                    Test Name: <input type="text" id="edit_testname" name="testname" class="form-control" required><br>
                    Number Question: <input type="text" id="edit_number_question" name="number_question" class="form-control" required><br>
                    Time: <input type="text" id="edit_time" name="time" class="form-control" required><br>
                    Test Type:<select id="edit_test_type" name="test_type" class="form-control" required>
                            <option value="">Select a Test Type</option>
                            <option value="Practice">Practice</option>
                            <option value="Full Test">Full Test</option>

                        </select><br>
                    
                    Question Choice: 
                    <p style = "color: red">Sử dụng lệnh verbal: __ - __ để thêm nhanh(Áp dụng với dãy liên tiếp) hoặc math: __ - __ Chấp nhận mỗi dòng 1 lệnh <br>Ví dụ:<br>verbal: 1 - 10 <br>math: 1 - 10 sẽ thêm verbal1, verbal2,... verbal10, math1,math2,...math10. </p>
                    <textarea id="edit_question_choose" name="question_choose" class="form-control" required></textarea><br>

                    <h4>Full Test Specific Module</h4>
                        <input type="hidden" id="edit_full_test_specific_module_input" name="full_test_specific_module">
                        
                        Module 1 - Reading And Writing<br>
                        <input type="number" id="edit_time_0" placeholder="Time for Reading and Writing Module 1"><br>
                        <input type="number" id="edit_start_0" placeholder="Start Module 1 (RW)"><br>
                        <input type="number" id="edit_end_0" placeholder="End Module 1 (RW)"><br>
                        
                        Module 2 - Reading And Writing<br>
                        <input type="number" id="edit_time_1" placeholder="Time for Reading and Writing Module 2"><br>
                        <input type="number" id="edit_start_1" placeholder="Start Module 2 (RW)"><br>
                        <input type="number" id="edit_end_1" placeholder="End Module 2 (RW)"><br>
                        
                        Module 1 - Math<br>
                        <input type="number" id="edit_time_2" placeholder="Time for Math Module 1"><br>
                        <input type="number" id="edit_start_2" placeholder="Start Module 1 (Math)"><br>
                        <input type="number" id="edit_end_2" placeholder="End Module 1 (Math)"><br>
                        
                        Module 2 - Math<br>
                        <input type="number" id="edit_time_3" placeholder="Time for Math Module 2"><br>
                        <input type="number" id="edit_start_3" placeholder="Start Module 2 (Math)"><br>
                        <input type="number" id="edit_end_3" placeholder="End Module 2 (Math)"><br>




                    Tag: <textarea id="edit_tag" name="tag" class="form-control"></textarea><br>
                   
                    Book:<select id="edit_book" name="book" class="form-control" required>
                            <option value="">Select a Book</option>
                            <option value="SAT Suite Question Bank">SAT Suite Question Bank</option>
                        </select><br>
                    Token Need: <input type = "number" id="edit_token_need" name="token_need" class="form-control" required><br>
                    Role Access: <textarea  id="edit_role_access" name="role_access" class="form-control" required></textarea> <br>
                    Time Allow: <input  type = "number"  id="edit_time_allow" name="time_allow" class="form-control" required> <br>

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
                    Number Question: <input type="text" id="add_number_question" name="number_question" class="form-control" required><br>
                    Time: <input type="text" id="add_time" name="time" class="form-control" required><br>

                    Test Type:<select id="add_test_type" name="test_type" class="form-control" required>
                            <option value="">Select a Test Type</option>
                            <option value="Practice">Practice</option>
                            <option value="Full Test">Full Test</option>

                        </select><br>                    
                    
                    Question Choice: 
                    <p style = "color: red">Sử dụng lệnh verbal: __ - __ để thêm nhanh(Áp dụng với dãy liên tiếp) hoặc math: __ - __ Chấp nhận mỗi dòng 1 lệnh <br>Ví dụ:<br>verbal: 1 - 10 <br>math: 1 - 10 sẽ thêm verbal1, verbal2,... verbal10, math1,math2,...math10. </p>

                    <textarea id="add_question_choose" name="question_choose" class="form-control" required></textarea><br>
                    <h4>Full Test Specific Module</h4>
                        <input type="hidden" id="add_full_test_specific_module_input" name="full_test_specific_module">
                        Module 1 - Reading And <br>
                        <input type="number" id="add_time_0" placeholder="Time for Reading and Writing Module 1"><br>
                        <input type="number" id="add_start_0" placeholder="Start Module 1 (RW)"><br>
                        <input type="number" id="add_end_0" placeholder="End Module 1 (RW)"><br>
                        
                        Module 2 - Reading And Writing<br>
                        <input type="number" id="add_time_1" placeholder="Time for Reading and Writing Module 2"><br>
                        <input type="number" id="add_start_1" placeholder="Start Module 2 (RW)"><br>
                        <input type="number" id="add_end_1" placeholder="End Module 2 (RW)"><br>
                        
                        Module 1 - Math<br>
                        <input type="number" id="add_time_2" placeholder="Time for Math Module 1"><br>
                        <input type="number" id="add_start_2" placeholder="Start Module 1 (Math)"><br>
                        <input type="number" id="add_end_2" placeholder="End Module 1 (Math)"><br>
                        
                        Module 2 - <br>
                        <input type="number" id="add_time_3" placeholder="Time for Math Module 2"><br>
                        <input type="number" id="add_start_3" placeholder="Start Module 2 (Math)"><br>
                        <input type="number" id="add_end_3" placeholder="End Module 2 (Math)"><br>





                    Tag: <textarea id="add_tag" name="tag" class="form-control"></textarea><br>
                    Book:<select id="add_book" name="book" class="form-control" required>
                            <option value="">Select a Book</option>
                            <option value="SAT Suite Question Bank">SAT Suite Question Bank</option>
                        </select><br>
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
    

    

function toggleFullTestSpecificModule() {
    var typeTest = document.getElementById('type_test').value;
    var moduleDiv = document.getElementById('full_test_specific_module');
    
    if (typeTest === 'full_test') {
        moduleDiv.style.display = 'block';
    } else {
        moduleDiv.style.display = 'none';
    }
}

document.getElementById('type_test').addEventListener('change', toggleFullTestSpecificModule);

toggleFullTestSpecificModule(); // Gọi khi trang tải để kiểm tra trạng thái ban đầu



function saveFullTestSpecificModule(isEdit = false) {
    const modules = [
        { name: "Module 1 Reading and Writing", key: "rw_module_1" },
        { name: "Module 2 Reading and Writing", key: "rw_module_2" },
        { name: "Module 1 Math", key: "math_module_1" },
        { name: "Module 2 Math", key: "math_module_2" }
    ];

    const moduleNameMapping = {
        "rw_module_1": "Section 1: Reading And Writing",
        "rw_module_2": "Section 2: Reading And Writing",
        "math_module_1": "Section 1: Math",
        "math_module_2": "Section 2: Math"
    };

    let fullTestSpecificModule = {};

    modules.forEach((module, index) => {
        let time = document.getElementById(`${isEdit ? "edit" : "add"}_time_${index}`)?.value;
        let startValue = document.getElementById(`${isEdit ? "edit" : "add"}_start_${index}`)?.value;
        let endValue = document.getElementById(`${isEdit ? "edit" : "add"}_end_${index}`)?.value;

        if (!startValue && !endValue) return;

        let start = parseInt(startValue);
        let end = parseInt(endValue);

        if (!time || isNaN(start) || isNaN(end) || start > end) {
            alert(`Vui lòng nhập đúng dữ liệu cho ${module.name}`);
            return;
        }

        let prefix = module.key.includes("rw_module_") ? "verbal" : "math";
        let questionParticular = [];
        for (let i = start; i <= end; i++) {
            questionParticular.push(`${prefix}${i}`);
        }

        let newKey = moduleNameMapping[module.key]; // Chuyển key sang dạng mong muốn
        fullTestSpecificModule[newKey] = { time, question_particular: questionParticular };
    });

    const jsonString = JSON.stringify(fullTestSpecificModule);
    let targetInput = document.getElementById(`${isEdit ? "edit" : "add"}_full_test_specific_module_input`);

    if (targetInput) {
        targetInput.value = jsonString;
    } else {
        console.error(`Không tìm thấy input hidden ${isEdit ? "edit" : "add"}_full_test_specific_module_input`);
    }

    console.log("Saved JSON:", jsonString);
}






// Open the edit modal and populate it with data
function openEditModal(number) {
    $.ajax({
        url: 'http://localhost/contents/themes/tutorstarter/template/digitalsat/test-list/get_question.php', // Fetch the question details
        type: 'POST',
        data: { number: number },
        success: function(response) {
            
            var data = JSON.parse(response);
            console.log(`Data: ${JSON.stringify(data, null, 2)}`);
            $('#edit_number').val(data.number);
            $('#edit_id_test').val(data.id_test);
            $('#edit_testname').val(data.testname);
            $('#edit_number_question').val(data.number_question);

            $('#edit_time').val(data.time);
            $('#edit_time_allow').val(data.time_allow);
            $('#edit_full_test_specific_module').val(data.full_test_specific_module);

            $('#edit_test_type').val(data.test_type);
            $('#edit_question_choose').val(data.question_choose);
            $('#edit_tag').val(data.tag);
            $('#edit_book').val(data.book);
            $('#edit_token_need').val(data.token_need);
            $('#edit_role_access').val(data.role_access);
            $('#editModal').modal('show');


            // Parse chuỗi JSON thành object trước
            const moduleData = JSON.parse(data.full_test_specific_module);

            // Lấy giá trị time (chú ý tên section có dấu cách, cần dùng ["..."] thay vì .)
            $('#edit_time_0').val(moduleData["Section 1: Reading And Writing"].time);
            $('#edit_start_0').val(moduleData["Section 1: Reading And Writing"].question_particular[0].replace(/\D/g, ''));
            $('#edit_end_0').val(moduleData["Section 1: Reading And Writing"].question_particular.slice(-1)[0].replace(/\D/g, ''));

            $('#edit_time_1').val(moduleData["Section 2: Reading And Writing"].time);
            $('#edit_start_1').val(moduleData["Section 2: Reading And Writing"].question_particular[0].replace(/\D/g, ''));
            $('#edit_end_1').val(moduleData["Section 2: Reading And Writing"].question_particular.slice(-1)[0].replace(/\D/g, ''));

            $('#edit_time_2').val(moduleData["Section 1: Math"].time);
            $('#edit_start_2').val(moduleData["Section 1: Math"].question_particular[0].replace(/\D/g, ''));
            $('#edit_end_2').val(moduleData["Section 1: Math"].question_particular.slice(-1)[0].replace(/\D/g, ''));

            $('#edit_time_3').val(moduleData["Section 2: Math"].time);
            $('#edit_start_3').val(moduleData["Section 2: Math"].question_particular[0].replace(/\D/g, ''));
            $('#edit_end_3').val(moduleData["Section 2: Math"].question_particular.slice(-1)[0].replace(/\D/g, ''));


        }
    });
}

// Save the edited data
function saveEdit() {
    saveFullTestSpecificModule(true); // Lưu JSON vào input trước khi submit
    $.ajax({
        url: 'http://localhost/contents/themes/tutorstarter/template/digitalsat/test-list/update_question.php',
        type: 'POST',
        data: $('#editForm').serialize(),
        success: function(response) {
            location.reload();
        }
    });
}
function openAddModal() {
    $('#addForm')[0].reset(); // Clear form data
    $('#add_time_0, #add_time_1, #add_time_2, #add_time_3').val('');  
    $('#add_start_0, #add_start_1, #add_start_2, #add_start_3').val('');
    $('#add_end_0, #add_end_1, #add_end_2, #add_end_3').val('');
    
    $('#addModal').modal('show'); // Show the modal
}


// Save the new question
function saveNew() {
    saveFullTestSpecificModule(false); // Lưu JSON vào input trước khi submit
    $.ajax({
        url: 'http://localhost/contents/themes/tutorstarter/template/digitalsat/test-list/add_question.php',
        type: 'POST',
        data: $('#addForm').serialize(),
        success: function(response) {
            location.reload(); 
        }
    });
}

// Delete a record
function deleteRecord(number) {
    if (confirm('Are you sure you want to delete this question?')) {
        $.ajax({
            url: 'http://localhost/contents/themes/tutorstarter/template/digitalsat/test-list/delete_question.php',
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

function showFullContent2(title, content) {
    // Set the title of the modal
    $('#viewMoreTitle').text(title);

    // Set the content with HTML allowed
    $('#viewMoreContent').html(content);

    // Show the modal
    $('#viewMoreModal').modal('show');
}

</script>



</html>