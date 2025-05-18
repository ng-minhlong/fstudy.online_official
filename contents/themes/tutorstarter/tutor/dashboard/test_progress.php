<?php
/*
 * Template Name: User Progress
 * Template Post Type: user progress
 */

 $servername = DB_HOST;
 $username = DB_USER;
 $password = DB_PASSWORD;
 $dbname = DB_NAME;
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

global $wp_query;
$siteurl = get_site_url();

global $wpdb;

$current_user = wp_get_current_user();
$current_username = $current_user->user_login;
$username = $current_username;
 
echo '
<script>
    var Currentusername = "' . $username . '";
    var siteUrl = "' . $siteurl . '";
</script>
';

get_header();
?>

<style>
    .progress-container {
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .progress-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .progress-header h1 {
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .progress-count {
        color: #7f8c8d;
        font-size: 16px;
    }
    
    .test-group {
        margin-bottom: 30px;
    }
    
    .group-title {
        font-size: 20px;
        color: #3498db;
        padding-bottom: 8px;
        border-bottom: 2px solid #3498db;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    
    .group-title i {
        margin-right: 10px;
    }
    
    .progress-item {
        display: flex;
        flex-direction: column;
        padding: 15px;
        margin-bottom: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: all 0.3s ease;
        border-left: 4px solid #3498db;
    }
    
    .progress-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    
    .test-id {
        font-weight: bold;
        color: #2c3e50;
    }
    
    .test-date {
        color: #7f8c8d;
        font-size: 14px;
    }
    
    .progress-bar-container {
        height: 20px;
        background: #ecf0f1;
        border-radius: 10px;
        margin: 10px 0;
        overflow: hidden;
    }
    
    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3498db, #2ecc71);
        border-radius: 10px;
        transition: width 0.5s ease;
        position: relative;
    }
    
    .progress-percent {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-shadow: 0 0 2px rgba(0,0,0,0.5);
    }
    
    .progress-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    
    .continue-btn, .delete-btn {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .continue-btn {
        background: #2ecc71;
        color: white;
    }
    
    .continue-btn:hover {
        background: #27ae60;
    }
    
    .delete-btn {
        background: #e74c3c;
        color: white;
    }
    
    .delete-btn:hover {
        background: #c0392b;
    }
    
    .no-progress {
        text-align: center;
        color: #7f8c8d;
        padding: 30px;
        font-size: 18px;
    }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px;
        border-radius: 5px;
        color: white;
        z-index: 1000;
        opacity: 1;
        transition: opacity 0.5s ease;
    }
    
    .success {
        background: #2ecc71;
    }
    
    .error {
        background: #e74c3c;
    }
    .progressList_dashboard{
        
    /* max-height: 400px; */
    overflow-y: auto;
    padding: 10px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="progress-container">
    <div class="progress-header">
        <b>Tiến Độ Học Tập</b>
        <div class="progress-count" id="progressCount">Đang tải...</div>
    </div>
    
    <div id="progressList_dashboard"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadProgressList();
});

function loadProgressList() {
    fetch(`${siteUrl}/api/v1/get-all-progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: Currentusername
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const progressList = document.getElementById('progressList_dashboard');
            const progressCount = document.getElementById('progressCount');
            
            progressCount.textContent = `Đã lưu ${result.progress_number}/20 kết quả`;
            
            if (result.data.length === 0) {
                progressList.innerHTML = '<div class="no-progress">Không có kết quả nào được lưu</div>';
                return;
            }

            // Nhóm progress theo type_test
            const groupedProgress = result.data.reduce((groups, item) => {
                const key = item.type_test || 'other';
                if (!groups[key]) {
                    groups[key] = [];
                }
                groups[key].push(item);
                return groups;
            }, {});

            // Sắp xếp các nhóm
            const sortedGroups = Object.entries(groupedProgress).sort((a, b) => {
                // Sắp xếp theo thứ tự ưu tiên nếu cần
                return a[0].localeCompare(b[0]);
            });

            progressList.innerHTML = '';
            
            // Tạo UI cho từng nhóm
            sortedGroups.forEach(([typeTest, items]) => {
                const groupContainer = document.createElement('div');
                groupContainer.className = 'test-group';
                
                // Thêm tiêu đề nhóm
                const groupTitle = document.createElement('div');
                groupTitle.className = 'group-title';
                
                // Icon khác nhau cho từng loại test
                let iconClass = 'fas fa-question-circle';
                if (typeTest === 'digitalsat') {
                    iconClass = 'fas fa-laptop-code';
                } else if (typeTest === 'dictation') {
                    iconClass = 'fas fa-microphone-alt';
                }
                else if (typeTest === 'shadowing') {
                    iconClass = 'fas fa-microphone-alt';
                }
                else if (typeTest === 'ielts_reading') {
                    iconClass = 'fas fa-microphone-alt';
                }
                
                groupTitle.innerHTML = `
                    <i class="${iconClass}"></i>
                    ${typeTest.toUpperCase()}
                `;
                
                groupContainer.appendChild(groupTitle);
                
                // Sắp xếp items trong nhóm theo ngày mới nhất
                items.sort((a, b) => new Date(b.date) - new Date(a.date));
                
                // Thêm từng item vào nhóm
                items.forEach(item => {
                    const progressItem = document.createElement('div');
                    progressItem.className = 'progress-item';
                    
                    progressItem.innerHTML = `
                        <div class="progress-info">
                            <span class="test-id">${item.testname} - ${item.id_test}</span>
                            <span class="test-date">${formatDate(item.date)}</span>
                        </div>
                        
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: ${item.percent_completed || 0}%">
                                <span class="progress-percent">${item.percent_completed || 0}%</span>
                            </div>
                        </div>
                        
                        <div class="progress-actions">
                            <button class="continue-btn" data-id="${item.id_test}" data-type="${item.type_test}">
                                <i class="fas fa-play"></i> Làm tiếp
                            </button>
                            <button class="delete-btn" data-id="${item.id_test}">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    `;
                    
                    groupContainer.appendChild(progressItem);
                });
                
                progressList.appendChild(groupContainer);
            });

            // Thêm sự kiện cho nút xóa
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idToDelete = this.getAttribute('data-id');
                    deleteProgressRecord(idToDelete);
                });
            });
            
            // Thêm sự kiện cho nút làm tiếp
            document.querySelectorAll('.continue-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type');
                    continueProgress(type, id);
                });
            });
        } else {
            throw new Error(result.message || 'Lỗi khi tải danh sách progress');
        }
    })
    .catch(error => {
        console.error('Error loading progress list:', error);
        const progressList = document.getElementById('progressList_dashboard');
        progressList.innerHTML = `
            <div class="notification error">
                Lỗi: ${error.message}
            </div>
        `;
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
}

function deleteProgressRecord(idToDelete) {
    if (!confirm('Bạn có chắc chắn muốn xóa kết quả này?')) return;

    fetch(`${siteUrl}/api/v1/delete-progress`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            username: Currentusername,
            id_test: idToDelete
        })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Xóa thành công!', 'success');
            loadProgressList();
        } else {
            throw new Error(result.message || 'Lỗi khi xóa progress');
        }
    })
    .catch(error => {
        console.error('Error deleting progress:', error);
        showNotification('Lỗi: ' + error.message, 'error');
    });
}

function continueProgress(type, id) {
    let url = '';
    if (type === 'digitalsat') {
        url = `${siteUrl}/test/digitalsat/${id}`;
    } else if (type === 'dictation') {
        url = `${siteUrl}/practice/dictation/${id}`;
    } else if (type === 'shadowing') {
        url = `${siteUrl}/practice/shadowing/${id}`;
    } else if (type === 'ielts_reading') {
        url = `${siteUrl}/test/ielts/r/${id}`;
    } else if (type === 'ielts_listening') {
        url = `${siteUrl}/test/ielts/l/${id}`;
    } else {
        showNotification('Loại bài kiểm tra không được hỗ trợ', 'error');
        return;
    }
    
    window.location.href = url;
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => document.body.removeChild(notification), 500);
    }, 3000);
}
</script>

<?php
//get_footer();
?>