<?php


// Kiểm tra user đã đăng nhập
if (!is_user_logged_in()) {
    echo '<p>Vui lòng đăng nhập để xem bảng từ đã lưu.</p>';
    get_footer();
    exit;
}

global $wpdb;
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$user_id = get_current_user_id();

$table_name ='notation';
$notations = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT number, username, user_id, table_note
         FROM $table_name 
         WHERE username = %s",
        $username
    )
);
// Get current time (hour, minute, second)
$hour = date("H"); // Giờ
$minute = date("i"); // Phút
$second = date("s"); // Giây

// Generate random two-digit number
$random_number = rand(10, 99);
// Handle user_id and id_test error, set to "00" if invalid
if (!$user_id) {
    $user_id = "00"; // Set user_id to "00" if invalid
}




$sessionID = $hour . $minute . $second . $user_id . $random_number;

$site_url = get_site_url();


echo "<script> 

        var username = '" .
        $username .
        "';
        var sessionID = '" .
        $sessionID .
        "';
       
        var siteUrl = '" .
        $site_url .
        "';
       
        console.log('session ID: ' + sessionID);
    </script>";


?>
<style>


/* CSS */
.button-10 {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 6px 14px;
  font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
  border-radius: 6px;
  border: none;

  color: #fff;
  background: linear-gradient(180deg, #4B91F7 0%, #367AF6 100%);
   background-origin: border-box;
  box-shadow: 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
}
.text-area-meaning{
   width: 100%;
  height: 150px;
  padding: 12px 20px;
  box-sizing: border-box;
  border: 2px solid #ccc;
  border-radius: 4px;
  background-color: #f8f8f8;
  font-size: 16px;
  resize: none;
}

.export-buttons {
        display: flex;
        gap: 10px; /* Khoảng cách giữa các nút */
        justify-content: center; /* Căn giữa các nút */
    }
.button-10:focus {
  box-shadow: inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2), 0px 0.5px 1.5px rgba(54, 122, 246, 0.25), 0px 0px 0px 3.5px rgba(58, 108, 217, 0.5);
  outline: 0;
}
.controls{
    margin-bottom: 20px; display: flex; gap: 10px;
}
.table{
    width: 100%;
}
</style>
<div class="notation-table-container">
    <h2>Bảng từ đã lưu của bạn</h2>

    <div class="export-buttons" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <button class="button-10" onclick="exportTableToCSV()">Xuất CSV</button>
        <button class="button-10" onclick="exportTableToDoc()">Xuất DOCX</button>
        <button class="button-10" onclick="window.print()">In Bảng</button>
    </div>
    <button class="button-10" onclick="openAddPopup()">Thêm Mới</button>

    <table class="table" id="notationTable" border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Number</th>
                <th>Thời gian lưu</th>
<!--                <th>Username</th> -->
                <th>Word Save</th>
                <th>Loại từ</th>
                <th>Nghĩa - Giải thích</th>
                
                <th>ID Test</th>

                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($notations)) : ?>
    <?php $rowNumber = 1; ?>
                    <?php foreach ($notations as $notation) :
                        $table_notes = json_decode($notation->table_note, true);
                        
                        if ($table_notes) :
                            // Lấy từ gốc (bỏ qua các key số)
                            if (isset($table_notes['word_save'])) :
                    ?>
                        <tr id="row-<?php echo $table_notes['id_note']; ?>">
                            <td><?php echo $rowNumber++; ?></td>
                            <td id="save-time-<?php echo $table_notes['id_note']; ?>"><?php echo esc_html($table_notes['save_time']); ?></td>

                            <td id="word-<?php echo $table_notes['id_note']; ?>"><?php echo esc_html($table_notes['word_save']); ?></td>
                            <td id="test-type-<?php echo $table_notes['id_note']; ?>"><?php echo esc_html($table_notes['test_type']); ?></td>
                            <td id="meaning-<?php echo $table_notes['id_note']; ?>"><?php echo esc_html($table_notes['meaning_or_explanation']); ?></td>

                            <td><?php echo esc_html($table_notes['id_test']); ?></td>
                            <!--<td><?php echo esc_html($table_notes['id_note']); ?></td> -->

                            <td>
                                <button class="button-10" onclick="openEditPopup('row-<?php echo $table_notes['id_note']; ?>')">Sửa</button>
                                <button class="button-10" onclick="deleteWord('row-<?php echo $note['id_note']; ?>')">Xóa</button>
                            </td>
                        </tr>

                    <?php 
                            endif;

                            // Duyệt các từ con (các key số)
                            foreach ($table_notes as $key => $note) :
                                if (!is_array($note) || !isset($note['word_save'])) continue;
                    ?>
                                 <tr id="row-<?php echo $note['id_note']; ?>">
                                        <td><?php echo $rowNumber++; ?></td>
                                        <td id="save-time-<?php echo $note['id_note']; ?>"><?php echo esc_html($note['save_time']); ?></td>

                                        <td id="word-<?php echo $note['id_note']; ?>"><?php echo esc_html($note['word_save']); ?></td>
                                        <td id="test-type-<?php echo $note['id_note']; ?>"><?php echo esc_html($note['test_type']); ?></td>

                                        <td id="meaning-<?php echo $note['id_note']; ?>"><?php echo esc_html($note['meaning_or_explanation']); ?></td>



                                        <td><?php echo esc_html($note['id_test']); ?></td>
                                        <!-- <td><?php echo esc_html($note['id_note']); ?></td> -->

                                        <td>
                                            <button class="button-10" onclick="openEditPopup('row-<?php echo $note['id_note']; ?>')">Sửa</button>
                                            <button class="button-10" onclick="deleteWord('row-<?php echo $note['id_note']; ?>')">Xóa</button>

                                        </td>
                                    </tr>
                    <?php 
                endforeach;
            endif;
        ?>
    <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td colspan="8">Không có từ nào được lưu.</td>
        </tr>
    <?php endif; ?>

</tbody>


    </table>
</div>
<!-- Edit Popup -->
<div id="editPopup" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1); z-index:1000;">
    <h3>Chỉnh sửa từ đã lưu</h3>
    
    <label>Save Time:</label>
    <input type="text" id="editSaveTime" readonly style="width: 100%; margin-bottom: 10px; background: #f3f3f3;">
    
    <label>ID Test:</label>
    <input type="text" id="editIdTest" readonly style="width: 100%; margin-bottom: 10px; background: #f3f3f3;">
    
    <label>ID Note:</label>
    <input type="text" id="editIdNote" readonly style="width: 100%; margin-bottom: 10px; background: #f3f3f3;">

    <label for="editWord">Word Save:</label>
    <input type="text" id="editWord" style="width: 100%; margin-bottom: 10px;">

    <label for="editMeaning">Meaning or Explanation:</label>
    <textarea id="editMeaning" class="text-area-meaning" style="width: 100%; margin-bottom: 10px;"></textarea>

    <div class="controls">
        <button class="button-10" onclick="saveEdit()">Lưu</button>
        <button class="button-10" onclick="closeEditPopup()">Đóng</button>
    </div>
</div>


<div id="addPopup" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1); z-index:1000;">
    <h3>Thêm Từ Mới</h3>
    <label for="addWord">Word Save:</label>
    <input type="text" id="addWord" style="width: 100%; margin-bottom: 10px;">
    <label for="addMeaning">Meaning or Explanation:</label>
    <textarea id="addMeaning" class="text-area-meaning" style="width: 100%; margin-bottom: 10px;"></textarea>
    <div class="controls">
        <button class="button-10" onclick="saveNewWord()">Lưu</button>
        <button class="button-10" onclick="closeAddPopup()">Đóng</button>
    </div>
</div>



<script>


// Lấy dữ liệu từ API sử dụng Fetch API
const fetchNotations = async (username) => {
    try {
        // URL API mà bạn đã tạo
        const apiUrl = `${siteUrl}/wp-json/api/v1/notations/`;

        // Gửi yêu cầu POST với username
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: username, // Gửi username trong body
            }),
        });

        // Kiểm tra nếu phản hồi từ API là hợp lệ
        if (!response.ok) {
            throw new Error('Failed to fetch notations');
        }

        // Lấy dữ liệu từ response
        const data = await response.json();

        // Kiểm tra dữ liệu trả về
        console.log(data);
        return data;
    } catch (error) {
        // Xử lý lỗi
        console.error('Error:', error);
        return null;
    }
};

// Ví dụ sử dụng hàm fetchNotations
fetchNotations(username).then(data => {
    if (data) {
        // Xử lý dữ liệu nhận được
        console.log('Fetched notations:', data);
    } else {
        console.log('No data found');
    }
});



function openEditPopup(idNote) {
    console.log("Editing row with ID Note:", idNote); // Kiểm tra ID Note truyền vào

    // Loại bỏ "row-" để lấy ID số
    let cleanId = idNote.replace(/^row-/, '');
    console.log("Clean ID:", cleanId);

    let wordElement = document.getElementById("word-" + cleanId);
    let meaningElement = document.getElementById("meaning-" + cleanId);
    let saveTimeElement = document.getElementById("save-time-" + cleanId);

    if (!wordElement) {
        console.error("wordElement not found. Check IDs in HTML.");
        return;
    }
    if (!meaningElement) {
        console.error("meaningElement not found. Check IDs in HTML.");
        return;
    }
    if (!saveTimeElement) {
        console.error("saveTimeElement not found. Check IDs in HTML.");
        return;
    }

    let wordSave = wordElement.innerText;
    let meaningSave = meaningElement.innerText;
    let saveTime = saveTimeElement.innerText;

    document.getElementById("editIdNote").value = cleanId; // Chỉ dùng số
    document.getElementById("editWord").value = wordSave;
    document.getElementById("editMeaning").value = meaningSave;

    let saveTimeInput = document.getElementById("editSaveTime");
    if (saveTimeInput) {
        saveTimeInput.value = saveTime;
    }

    document.getElementById("editPopup").style.display = "block";
}





    function closeEditPopup() {
        document.getElementById("editPopup").style.display = "none";
    }

    function saveEdit() { 
        let idNote = document.getElementById("editIdNote").value;
        let wordSave = document.getElementById("editWord").value;
        let meaningOrExplanation = document.getElementById("editMeaning").value;

        console.log(`Đã lưu ID Note: ${idNote}, Word Save mới: ${wordSave}, Meaning mới: ${meaningOrExplanation}`);

        const apiUrl = `${siteUrl}/wp-json/api/v1/update-notation/`;

        fetch(apiUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id_note: idNote,
                word_save: wordSave,
                meaning_or_explanation: meaningOrExplanation,
                username: username
            }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Cập nhật thất bại!");
            }
            return response.json();
        })
        .then(() => {
            document.getElementById(`word-${idNote}`).innerText = wordSave;
            document.getElementById(`meaning-${idNote}`).innerText = meaningOrExplanation;
            closeEditPopup();
        })
        .catch(error => {
            console.error("Lỗi khi cập nhật:", error);
            alert(error.message);
        });
    }




    const date = new Date().toISOString().split('T')[0]

    // Xuất bảng ra CSV
    function exportTableToCSV() {
        let table = document.getElementById("notationTable");
        let rows = table.querySelectorAll("tr");
        let csvContent = "";

        rows.forEach(row => {
            let cells = row.querySelectorAll("td, th");
            let rowData = [];
            cells.forEach(cell => rowData.push(`"${cell.innerText}"`));
            csvContent += rowData.join(",") + "\n";
        });

        let blob = new Blob([csvContent], { type: "text/csv" });
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `notation_${date}.csv`;
        link.click();
    }

    // Xuất bảng ra DOCX
    function exportTableToDoc() {
        let table = document.getElementById("notationTable");
        let rows = table.querySelectorAll("tr");
        let docContent = "<table border='1' style='border-collapse:collapse;width:100%;'>";

        rows.forEach(row => {
            docContent += "<tr>";
            let cells = row.querySelectorAll("td, th");
            cells.forEach(cell => docContent += `<td>${cell.innerText}</td>`);
            docContent += "</tr>";
        });

        docContent += "</table>";

        let blob = new Blob(
            [`<html><head><meta charset='utf-8'></head><body>${docContent}</body></html>`],
            { type: "application/msword" }
        );
        let link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = `notation_${date}.doc`;
        link.click();
    }

function openAddPopup() {
    document.getElementById("addPopup").style.display = "block";
}

function closeAddPopup() {
    document.getElementById("addPopup").style.display = "none";
}

function saveNewWord() {
    let wordSave = document.getElementById("addWord").value;
    let meaningOrExplanation = document.getElementById("addMeaning").value;

    if (!wordSave || !meaningOrExplanation) {
        alert("Vui lòng nhập đủ thông tin!");
        return;
    }

    let data = new FormData();
    data.append('action', 'add_notation');
    data.append('word_save', wordSave);
    data.append('meaning_or_explanation', meaningOrExplanation);

    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            let table = document.getElementById("notationTable").getElementsByTagName('tbody')[0];
            let newRow = table.insertRow(0);
            newRow.innerHTML = `
                <td>#</td>
                <td>${new Date().toISOString().split('T')[0]}</td>
                <td><?php echo esc_html($username); ?></td>
                <td>${wordSave}</td>
                <td>${meaningOrExplanation}</td>
                <td></td>
                <td></td>
                <td></td>
                <td><button class="button-10" onclick="openEditPopup(${result.number})">Sửa</button></td>
            `;
            closeAddPopup();
            alert("Thêm mới thành công!");
        } else {
            alert("Lỗi khi thêm!");
        }
    });
}


</script>

