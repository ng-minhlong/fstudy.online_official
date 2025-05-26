<?php
/*
Template Name: User List and Role Editor
*/
require_once(__DIR__ . '/../../../config-custom.php');

if (!current_user_can('administrator')) {
    wp_die('You do not have permission to access this page.');
}
global $wp_roles;
$roles_list = [];

foreach ($wp_roles->roles as $role_slug => $role_data) {
    $roles_list[] = [
        'slug' => $role_slug,
        'name' => $role_data['name']
    ];
}

// Debug hoặc in mảng vai trò (chỉ để kiểm tra)
//echo '<pre>';
//print_r($roles_list);
//echo '</pre>';



?>


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
    <title>User Management</title>
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

                
<div class="wrap">
    <h1>User Management</h1>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr >
                <th>User ID</th>
                <th>Username</th>
                <th>Created Date</th>
                <th>User Role</th>
                <th>Edit Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $users = get_users();
            foreach ($users as $user) {
                $user_roles = implode(', ', $user->roles);
                if (in_array('administrator', $user->roles)) {
                    continue; // Bỏ qua user có vai trò Administrator
                }
                $user_roles = implode(', ', $user->roles);

                echo '<tr  id="row_' . esc_html($user->ID) . '">
                    <td>' . esc_html($user->user_login) . '</td>
                    <td>' . esc_html($user->ID) . '</td>
                    <td>' . esc_html($user->user_registered) . '</td>
                    <td class="user-role" data-user-id="' . esc_attr($user->ID) . '">' . esc_html($user_roles) . '</td>
                    <td>
                        <select class="role-selector" data-user-id="' . esc_attr($user->ID) . '">
                            <option value="">Select Role</option>';
                            
                global $wp_roles;
                foreach ($wp_roles->roles as $role_slug => $role_data) {
                    echo '<option value="' . esc_attr($role_slug) . '">' . esc_html($role_data['name']) . '</option>';
                }
            
                echo '</select>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="openEditModal(' . esc_attr($user->ID) . ')">Edit</button>
                    </td>
                </tr>';
            }
?>            
        </tbody>
    </table>
</div>



<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                
                <h5 class="modal-title">Edit Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    
                    ID Notification: <input type="text" id="edit_id_notification" name="id_notification" class="form-control" required><br>
                    Title: <textarea type="text" id="edit_title" name="title" class="form-control" required></textarea><br>
                    Content: <textarea type="text" id="edit_content" name="content" class="form-control" required></textarea><br>

                    Level Notification:<select id="edit_level_notification" name="level_notification" class="form-control" required>
                        <option value=""></option>
                        <option value="Normal">Normal</option>
                        <option value="Important">Important</option>
                    
                    </select><br>
                    <!-- Role Receiver Section -->
<div>
    <label for="role_receive">Role Receiver:</label>
    <div id="roleReceiveCheckboxes" class="form-check">
        <!-- Checkboxes sẽ được render bằng JavaScript -->
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="selectEveryone" onclick="toggleAllRoles()">
        <label class="form-check-label" for="selectEveryone">Select Everyone</label>
    </div>
</div>
<br>

                    User Receiver: <textarea id="edit_user_receive" name="user_receive" class="form-control" required></textarea><br>
                      
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
                    ID Notification: <input type="text" id="add_id_notification" name="id_notification" class="form-control" required><br>
                    Title: <textarea type="text" id="add_title" name="title" class="form-control" required></textarea><br>
                    Content: <textarea type="text" id="add_content" name="content" class="form-control" required></textarea><br>

                    Level Notification:<select id="add_level_notification" name="level_notification" class="form-control" required>
                        <option value=""></option>
                        <option value="Normal">Normal</option>
                        <option value="Important">Important</option>
                    
                    </select><br>
                    <!-- Role Receiver Section -->
<div>
    <label for="role_receive">Role Receiver:</label>
    <div id="roleReceiveCheckboxes" class="form-check">
        <!-- Checkboxes sẽ được render bằng JavaScript -->
    </div>
    <div class="form-check">
        <input type="checkbox" class="form-check-input" id="selectEveryone" onclick="toggleAllRoles()">
        <label class="form-check-label" for="selectEveryone">Select Everyone</label>
    </div>
</div>
<br>
                    User Receiver: <textarea id="add_user_receive" name="user_receive" class="form-control" required></textarea><br>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="saveNew()">Add Question</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
  
  // Lấy phần tử dòng tương ứng
  const rowElement = document.querySelector(`#row_${number}`);
 if (!rowElement) {
     console.error(`Row with ID #row_${number} not found.`);
     return;
 }

 // Lấy giá trị từ cột role_receive
 const roleReceiveElement = rowElement.querySelector('.role_receive');
 

 // Nếu có giá trị role_receive, tách chúng thành mảng
 const selectedRoles = roleReceiveElement.textContent.trim().split(",");
 renderRoleCheckboxes("#editModal", selectedRoles); // Gọi hàm để render checkbox cho modal

 $.ajax({
     url: '<?php echo get_site_url()?>/contents/themes/tutorstarter/template/admin_panel/notification/database/get_notification.php', // Fetch the question details
     type: 'POST',
     data: { number: number },
     success: function(response) {
         var data = JSON.parse(response);
         $('#edit_id_notification').val(data.id_notification);
         $('#edit_title').val(data.title);
         $('#edit_content').val(data.content);
         $('#edit_level_notification').val(data.level_notification);
         $('#edit_role_receive').val(data.role_receive);
         $('#edit_user_receive').val(data.user_receive);

         $('#editModal').modal('show');
     }
 });
}



    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.role-selector').forEach(function (selector) {
            selector.addEventListener('change', function () {
                const userId = this.getAttribute('data-user-id');
                const newRole = this.value;
                if (newRole) {
                    fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: new URLSearchParams({
                            action: "update_user_role",
                            user_id: userId,
                            role: newRole,
                            security: "<?php echo wp_create_nonce('update_user_role_nonce'); ?>"
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("User role updated successfully!");
                            location.reload();
                        } else {
                            alert("Error: " + data.message);
                        }
                    });
                }
            });
        });
    });
</script>
</html>
