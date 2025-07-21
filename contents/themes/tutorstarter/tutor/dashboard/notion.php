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
    <button class="button-10" onclick="window.location.href='<?php echo $site_url?>/practice/notion/flashcard'">Luyện flashcard</button>

    <table class="table" id="notationTable" border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>STT</th>
                <th>Thời gian lưu</th>
                <!--<th>Username</th> -->
                <th>Từ được lưu</th>
                <th>Loại đề</th>
                <th>Nghĩa - Giải thích</th>
                
                <th>ID Test</th>

                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="notation-body">
            <tr><td colspan="7">Đang tải dữ liệu...</td></tr>
        </tbody>


    </table>
</div>
<!-- Edit Popup -->
<div id="editPopup" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1); z-index:1000;">
    <h3>Chỉnh sửa từ đã lưu</h3>
    
    <label>Thời gian lưu:</label>
    <input type="text" id="editSaveTime" readonly style="width: 100%; margin-bottom: 10px; background: #f3f3f3;">
    
    <label>ID Test:</label>
    <input type="text" id="editIdTest" readonly style="width: 100%; margin-bottom: 10px; background: #f3f3f3;">
    
    <label>ID Note:</label>
    <input type="text" id="editIdNote" readonly style="width: 100%; margin-bottom: 10px; background: #f3f3f3;">

    <label for="editWord">Từ được lưu:</label>
    <input type="text" id="editWord" style="width: 100%; margin-bottom: 10px;">

    <label for="editMeaning">Nghĩa hoặc Giải thích:</label>
    <textarea id="editMeaning" class="text-area-meaning" style="width: 100%; margin-bottom: 10px;"></textarea>

    <div class="controls">
        <button class="button-10" onclick="saveEdit()">Lưu</button>
        <button class="button-10" onclick="closeEditPopup()">Đóng</button>
    </div>
</div>


<div id="addPopup" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; padding:20px; box-shadow:0 2px 10px rgba(0,0,0,0.1); z-index:1000; overflow-y:auto; max-height:80vh;">
    <h3>Thêm Từ Mới</h3>

    <label>
        <input type="radio" name="inputMode" value="pair" checked onchange="toggleInputMode(this.value)"> Nhập từng dòng
    </label>
    <label>
        <input type="radio" name="inputMode" value="textarea" onchange="toggleInputMode(this.value)"> Nhập bằng textarea
    </label>

    <!-- Option 1: Từng dòng -->
    <div id="pairInputs">
        <div class="pair-row">
            <input type="text" placeholder="Từ cần lưu" class="word">
            <input type="text" placeholder="Nghĩa từ" class="meaning">
        </div>
        <button onclick="addNewPair()">+ Thêm dòng</button>
    </div>

    <!-- Option 2: Textarea -->
    <div id="bulkTextarea" style="display:none;">
        <label for="delimiter">Ký tự phân cách từ & nghĩa:</label>
        <select id="delimiter" onchange="syncFromTextarea()">
            <option value=" - "> - </option>
            <option value=" ">dấu cách</option>
            <option value="\t">tab</option>
        </select>
        <textarea id="bulkInput" rows="10" style="width: 100%;" oninput="syncFromTextarea()"></textarea>
    </div>

    <div class="controls">
        <button class="button-10" onclick="saveAllWords()">Lưu toàn bộ</button>
        <button class="button-10" onclick="closeAddPopup()">Đóng</button>
    </div>
</div>



<script>


// Lấy dữ liệu từ API sử dụng Fetch API
const fetchNotations = async (username) => {
    try {
        // URL API mà bạn đã tạo
        const apiUrl = `${siteUrl}/api/api/v1/notations/`;

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
    const tbody = document.getElementById("notation-body");
    tbody.innerHTML = "";

    if (!data || data.length === 0) {
        tbody.innerHTML = "<tr><td colspan='7'>Không có từ nào được lưu.</td></tr>";
        return;
    }

    data.forEach((note, index) => {
        const row = document.createElement("tr");
        row.id = `row-${note.number}`;

        row.innerHTML = `
            <td>${index + 1}</td>
            <td id="save-time-${note.number}">${note.created_at || ""}</td>
            <td id="word-${note.number}">${note.word_save}</td>
            <td id="test-type-${note.number}">${note.type}</td>
            <td id="meaning-${note.number}">${note.meaning_and_explanation}</td>
            <td>${note.idtest}</td>
            <td>
                <button class="button-10" onclick="openEditPopup('row-${note.number}')">Sửa</button>
                <button class="button-10" onclick="deleteWord('row-${note.number}')">Xóa</button>
            </td>
        `;

        tbody.appendChild(row);
    });
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

        const apiUrl = `${siteUrl}/api/v1/update-notation/`;

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

    let data = {
        username: "<?php echo esc_js($username); ?>",  // server inject username
        user_id: <?php echo esc_js($user_id); ?>,       // server inject user_id
        word_save: wordSave,
        meaning_or_explanation: meaningOrExplanation,
        idtest: "",  
        type: ""  
    };

    fetch(`${siteUrl}/api/v1/add-notation/`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            let table = document.getElementById("notationTable").getElementsByTagName('tbody')[0];
            let newRow = table.insertRow(0);
            newRow.innerHTML = `
                <td>#</td>
                <td>${new Date().toISOString().split('T')[0]}</td>
                <td>${data.username}</td>
                <td>${wordSave}</td>
                <td>${meaningOrExplanation}</td>
                <td>${data.idtest}</td>
                <td>${data.type}</td>
                <td></td>
                <td><button class="button-10" onclick="openEditPopup(${result.insert_id})">Sửa</button></td>
            `;
            closeAddPopup();
            alert("Thêm mới thành công!");
        } else {
            alert(result.message || "Lỗi khi thêm!");
        }
    });
}

function toggleInputMode(mode) {
    document.getElementById("pairInputs").style.display = mode === "pair" ? "block" : "none";
    document.getElementById("bulkTextarea").style.display = mode === "textarea" ? "block" : "none";

    if (mode === "textarea") {
        syncToTextarea();
    } else {
        syncFromTextarea();
    }
}

function addNewPair(word = "", meaning = "") {
    const div = document.createElement("div");
    div.classList.add("pair-row");
    div.innerHTML = `
        <input type="text" placeholder="Từ cần lưu" class="word" value="${word}">
        <input type="text" placeholder="Nghĩa từ" class="meaning" value="${meaning}">
        <button onclick="this.parentNode.remove()">X</button>
    `;
    document.getElementById("pairInputs").appendChild(div);
}

function syncToTextarea() {
    const rows = document.querySelectorAll("#pairInputs .pair-row");
    const delimiter = document.getElementById("delimiter").value;
    let text = "";
    rows.forEach(row => {
        const word = row.querySelector(".word").value.trim();
        const meaning = row.querySelector(".meaning").value.trim();
        if (word && meaning) {
            text += `${word}${delimiter}${meaning}\n`;
        }
    });
    document.getElementById("bulkInput").value = text.trim();
}

function syncFromTextarea() {
    const delimiter = document.getElementById("delimiter").value;
    const lines = document.getElementById("bulkInput").value.trim().split("\n");

    // Clear old
    document.getElementById("pairInputs").innerHTML = "";

    lines.forEach(line => {
        const parts = line.split(delimiter);
        if (parts.length >= 2) {
            const word = parts[0].trim();
            const meaning = parts.slice(1).join(delimiter).trim(); // giữ nghĩa dài hơn
            addNewPair(word, meaning);
        }
    });
}

function saveAllWords() {
    const rows = document.querySelectorAll("#pairInputs .pair-row");
    const dataToSave = [];

    rows.forEach(row => {
        const word = row.querySelector(".word").value.trim();
        const meaning = row.querySelector(".meaning").value.trim();
        if (word && meaning) {
            dataToSave.push({
                username: "<?php echo esc_js($username); ?>",
                user_id: <?php echo esc_js($user_id); ?>,
                word_save: word,
                meaning_or_explanation: meaning,
                idtest: "",
                type: ""
            });
        }
    });

    // Gửi từng từ hoặc gộp lại theo nhu cầu
    Promise.all(dataToSave.map(data =>
        fetch(`${siteUrl}/api/v1/add-notation/`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        }).then(res => res.json())
    )).then(results => {
        alert("Lưu thành công!");
        closeAddPopup();
    }).catch(() => {
        alert("Có lỗi xảy ra!");
    });
}

</script>

