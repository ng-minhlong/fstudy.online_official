<?php
/*
Template Name: Security Template
*/
$site_url = get_site_url();

?>

<div class="security-center-container">
    <h3 class="security-center-title">Trung tâm bảo mật - Security Center</h3>
    
    <div class="security-tabs">
        <button class="tab-button active" data-tab="activity-log">Activity Log</button>
        <button class="tab-button" data-tab="violations">Violations</button>
    </div>
    
    <div id="activity-log" class="tab-content active">
        <div class="table-container">
            <table id="activity-table" class="security-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>IP Address</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
    
    <div id="violations" class="tab-content">
        <!-- Tab 2 content -->
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Load activity logs
    function loadActivityLogs() {
        $.ajax({
            url: `<?php echo $site_url; ?>/api/secure/v1/user_log`,
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce('wp_rest'); ?>');
            },
            success: function(response) {
                if(response.success) {
                    renderActivityLogs(response.data);
                }
            },
            error: function(error) {
                console.error('Error loading logs:', error);
            }
        });
    }

    // Render logs to table
    function renderActivityLogs(logs) {
        const tbody = $('#activity-table tbody');
        tbody.empty();
        
        logs.forEach(log => {
            const row = `
                <tr>
                    <td>${log.action}</td>
                    <td>${log.ip_address}</td>
                    <td>${new Date(log.time).toLocaleString()}</td>
                    <td>${log.city || 'Unknown'}, ${log.country || 'Unknown'}</td>
                    <td>
                        <button class="btn-logout-ip" data-ip="${log.ip_address}">
                            Logout this IP
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }

    // Tab switching
    $('.tab-button').click(function() {
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        
        $('.tab-content').removeClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    // Initial load
    loadActivityLogs();
});
</script>

<style>
.security-center-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.security-tabs {
    display: flex;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}

.tab-button {
    padding: 10px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-bottom: 3px solid transparent;
}

.tab-button.active {
    border-bottom: 3px solid #0073aa;
    font-weight: bold;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.security-table {
    width: 100%;
    border-collapse: collapse;
}

.security-table th, .security-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.security-table th {
    background-color: #f5f5f5;
    font-weight: 600;
}

.btn-logout-ip {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-logout-ip:hover {
    background-color: #c82333;
}
</style>

