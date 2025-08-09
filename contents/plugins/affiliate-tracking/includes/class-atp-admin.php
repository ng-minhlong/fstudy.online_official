<?php
if (!defined('ABSPATH')) exit;

class ATP_Admin {
    public function __construct() {
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'maybe_ajax_actions']);
    }

    public function register_menu() {
        add_menu_page(
            __('Affiliate Tracking', 'affiliate-tracking-pro'),
            'Affiliate Tracking',
            'manage_options',
            'atp_dashboard',
            [$this, 'render_dashboard'],
            'dashicons-networking',
            60
        );
    }

    public function render_dashboard() {
        if (!current_user_can('manage_options')) {
            wp_die(__('No permission', 'affiliate-tracking-pro'));
        }

        global $wpdb;

        // Quick summary
        $clicks = $wpdb->get_var("SELECT COUNT(*) FROM affiliate_clicks");
        $commissions = $wpdb->get_var("SELECT COUNT(*) FROM affiliate_commissions");
        $commission_total = $wpdb->get_var("SELECT COALESCE(SUM(amount),0) FROM affiliate_commissions");

        // Top referrers by clicks (join affiliate_refs)
        $top_clicks = $wpdb->get_results("
            SELECT r.id, r.user_id, r.ref_code, s.clicks_count, s.commissions_total
            FROM affiliate_refs r
            LEFT JOIN affiliate_stats s ON s.referrer_id = r.id
            ORDER BY COALESCE(s.clicks_count,0) DESC
            LIMIT 20
        ");

        ?>
        <div class="wrap">
            <h1>Affiliate Tracking — Dashboard</h1>

            <h2>Quick summary</h2>
            <ul>
                <li>Total clicks: <strong><?php echo esc_html(number_format_i18n($clicks)); ?></strong></li>
                <li>Total commissions: <strong><?php echo esc_html(number_format_i18n($commissions)); ?></strong></li>
                <li>Commissions sum: <strong><?php echo esc_html(number_format_i18n($commission_total, 2)); ?></strong></li>
            </ul>

            <h2>Top referrers</h2>
            <table class="widefat striped">
                <thead>
                    <tr><th>ID</th><th>WP User</th><th>Ref code</th><th>Clicks</th><th>Commissions total</th></tr>
                </thead>
                <tbody>
                <?php if ($top_clicks): foreach ($top_clicks as $row): ?>
                    <tr>
                        <td><?php echo intval($row->id); ?></td>
                        <td><?php
                            $user = get_userdata($row->user_id);
                            echo $user ? esc_html($user->user_login . " (#{$row->user_id})") : esc_html("User #{$row->user_id}");
                        ?></td>
                        <td><?php echo esc_html($row->ref_code); ?></td>
                        <td><?php echo esc_html(number_format_i18n(intval($row->clicks_count))); ?></td>
                        <td><?php echo esc_html(number_format_i18n(floatval($row->commissions_total), 2)); ?></td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5">No referrers</td></tr>
                <?php endif; ?>
                </tbody>
            </table>

            <h2>Recent clicks (last 20)</h2>
            <?php
                $recent_clicks = $wpdb->get_results("SELECT * FROM affiliate_clicks ORDER BY clicked_at DESC LIMIT 20");
            ?>
            <table class="widefat">
                <thead><tr><th>ID</th><th>Referrer ID</th><th>IP</th><th>Landing</th><th>Referer</th><th>Clicked at</th></tr></thead>
                <tbody>
                <?php foreach ($recent_clicks as $c): ?>
                    <tr>
                        <td><?php echo intval($c->id); ?></td>
                        <td><?php echo intval($c->referrer_id); ?></td>
                        <td><?php echo esc_html($c->ip_address); ?></td>
                        <td><?php echo esc_html($c->landing_page); ?></td>
                        <td><?php echo esc_html($c->referer_url); ?></td>
                        <td><?php echo esc_html($c->clicked_at); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function maybe_ajax_actions() {
        // optional: add ajax endpoints later
    }
}
