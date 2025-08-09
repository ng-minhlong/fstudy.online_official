<?php
/* 
 * Template Name: Affiliate Dashboard
 */
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url() );
    exit;
}

global $wpdb;
$current_user = wp_get_current_user();
$table_refs = 'affiliate_refs';
$user_id = $current_user->ID;

// Check ref_code
$ref_data = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_refs WHERE user_id = %d", $user_id) );

// Xử lý tạo mới ref_code
if ( isset($_POST['create_ref']) && !$ref_data ) {
    $uuid = wp_generate_uuid4();
    $wpdb->insert(
        $table_refs,
        [
            'user_id' => $user_id,
            'ref_code' => $uuid,
            'commission_rate' => 10.00,
            'status' => 'active',
            'created_at' => current_time('mysql')
        ]
    );
    $ref_data = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_refs WHERE user_id = %d", $user_id) );
}

// Load dữ liệu dashboard
$table_commissions =  'affiliate_commissions';
$table_payouts = 'affiliate_payouts';

$total_commission = 0;
$pending_commission = 0;
$paid_commission = 0;

if ( $ref_data ) {
    $referrer_id = $ref_data->id;
    $stats = $wpdb->get_results( $wpdb->prepare("
        SELECT status, SUM(amount) as total 
        FROM $table_commissions 
        WHERE referrer_id = %d 
        GROUP BY status
    ", $referrer_id) );

    foreach ( $stats as $row ) {
        if ( $row->status === 'pending' ) $pending_commission = $row->total;
        if ( $row->status === 'paid' ) $paid_commission = $row->total;
        $total_commission += $row->total;
    }

    $payouts = $wpdb->get_results( $wpdb->prepare("
        SELECT * FROM $table_payouts 
        WHERE referrer_id = %d 
        ORDER BY created_at DESC
    ", $referrer_id) );
}
?>

<style>
:root {
    --primary: #2b6cb0;
    --success: #38a169;
    --warning: #d69e2e;
    --light: #f8f9fa;
    --border: #e2e8f0;
    --radius: 8px;
}
.affiliate-dashboard {
    max-width: 1000px;
    margin: 40px auto;
    font-family: Arial, sans-serif;
}
.affiliate-card {
    background: var(--light);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 15px 20px;
    margin-bottom: 20px;
}
.affiliate-tabs {
    display: flex;
    border-bottom: 1px solid var(--border);
    margin-bottom: 15px;
}
.affiliate-tab {
    padding: 10px 20px;
    cursor: pointer;
    border: 1px solid var(--border);
    border-bottom: none;
    background: var(--light);
    border-radius: var(--radius) var(--radius) 0 0;
    margin-right: 5px;
}
.affiliate-tab.active {
    background: white;
    font-weight: bold;
}
.affiliate-tab-content {
    border: 1px solid var(--border);
    border-radius: 0 var(--radius) var(--radius) var(--radius);
    padding: 20px;
    background: white;
}
.stat-grid {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}
.stat-box {
    flex: 1;
    min-width: 200px;
    background: white;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 15px;
    text-align: center;
}
.stat-value {
    font-size: 1.5em;
    font-weight: bold;
}
.table-aff {
    width: 100%;
    border-collapse: collapse;
}
.table-aff th, .table-aff td {
    border: 1px solid var(--border);
    padding: 8px;
    text-align: left;
}
.table-aff th {
    background: var(--light);
}
</style>

<div class="affiliate-dashboard">
    <h2>Affiliate Dashboard</h2>

    <?php if ( ! $ref_data ): ?>
        <div class="affiliate-card">
            <p>Bạn chưa có mã affiliate.</p>
            <form method="post">
                <button type="submit" name="create_ref" style="background:var(--primary);color:white;padding:8px 16px;border:none;border-radius:var(--radius);cursor:pointer">
                    Tạo mã affiliate mới
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="affiliate-card">
            <strong>Mã giới thiệu:</strong> 
            <code><?php echo esc_html($ref_data->ref_code); ?></code><br>
            <strong>Link giới thiệu:</strong> 
            <code><?php echo esc_url(home_url('/?ref=' . $ref_data->ref_code)); ?></code>
        </div>

        <div class="affiliate-tabs">
            <div class="affiliate-tab active" data-tab="overview">Doanh thu</div>
            <div class="affiliate-tab" data-tab="payment">Thông tin thanh toán</div>
        </div>

        <div id="tab-overview" class="affiliate-tab-content">
            <h4>Tổng quan</h4>
            <div class="stat-grid">
                <div class="stat-box">
                    <div>Tổng hoa hồng</div>
                    <div class="stat-value" style="color:var(--success)"><?php echo number_format($total_commission, 2); ?> đ</div>
                </div>
                <div class="stat-box">
                    <div>Pending</div>
                    <div class="stat-value" style="color:var(--warning)"><?php echo number_format($pending_commission, 2); ?> đ</div>
                </div>
                <div class="stat-box">
                    <div>Đã thanh toán</div>
                    <div class="stat-value" style="color:var(--primary)"><?php echo number_format($paid_commission, 2); ?> đ</div>
                </div>
            </div>
        </div>

        <div id="tab-payment" class="affiliate-tab-content" style="display:none">
            <h4>Lịch sử thanh toán</h4>
            <?php if ( !empty($payouts) ): ?>
                <table class="table-aff">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Phương thức</th>
                            <th>Số tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $payouts as $p ): ?>
                            <tr>
                                <td><?php echo esc_html($p->created_at); ?></td>
                                <td><?php echo esc_html($p->payout_method); ?></td>
                                <td><?php echo number_format($p->total_amount, 2); ?> đ</td>
                                <td><?php echo esc_html($p->status); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Chưa có thanh toán nào.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.affiliate-tab').forEach(tab => {
    tab.addEventListener('click', function(){
        document.querySelectorAll('.affiliate-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.affiliate-tab-content').forEach(c => c.style.display = 'none');
        if(this.dataset.tab === 'overview'){
            document.getElementById('tab-overview').style.display = 'block';
        } else {
            document.getElementById('tab-payment').style.display = 'block';
        }
    });
});
</script>
