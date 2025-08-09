<?php
if (!defined('ABSPATH')) exit;

/**
 * Helper: set cookie with fallback domain/path
 */
function atp_set_cookie($name, $value, $expire) {
    $path = defined('COOKIEPATH') ? COOKIEPATH : '/';
    $domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : null;
    if ($domain) {
        setcookie($name, $value, $expire, $path, $domain, is_ssl(), true);
    } else {
        setcookie($name, $value, $expire, $path, '', is_ssl(), true);
    }
}

/**
 * Helper: update affiliate_stats counters atomically-ish
 */
function atp_stats_increment_clicks($referrer_id, $count = 1) {
    global $wpdb;
    $table = 'affiliate_stats';
    $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO {$table} (referrer_id, clicks_count) VALUES (%d, %d)
             ON DUPLICATE KEY UPDATE clicks_count = clicks_count + %d, updated_at = CURRENT_TIMESTAMP",
            $referrer_id, $count, $count
        )
    );
}

function atp_stats_add_commission($referrer_id, $amount) {
    global $wpdb;
    $table =  'affiliate_stats';
    $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO {$table} (referrer_id, commissions_total) VALUES (%d, %f)
             ON DUPLICATE KEY UPDATE commissions_total = commissions_total + %f, updated_at = CURRENT_TIMESTAMP",
            $referrer_id, $amount, $amount
        )
    );
}
