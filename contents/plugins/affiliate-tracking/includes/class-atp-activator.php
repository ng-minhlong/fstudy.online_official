<?php
if (!defined('ABSPATH')) exit;

class ATP_Activator {
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        

        // Tables from your schema + affiliate_stats
        $sql = [];

        $sql[] = "
        CREATE TABLE affiliate_user_tracking (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            first_referrer_id BIGINT UNSIGNED DEFAULT NULL,
            last_referrer_id BIGINT UNSIGNED DEFAULT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY user_id (user_id)
        ) $charset_collate;
        ";

        $sql[] = "
        CREATE TABLE affiliate_refs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            ref_code CHAR(36) NOT NULL UNIQUE,
            commission_rate DECIMAL(5,2) DEFAULT 10.00,
            status ENUM('active','inactive') DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            KEY user_id (user_id)
        ) $charset_collate;
        ";

        $sql[] = "
        CREATE TABLE affiliate_clicks (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            referrer_id BIGINT UNSIGNED NOT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            user_agent TEXT,
            landing_page TEXT,
            referer_url TEXT,
            clicked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            KEY referrer_id (referrer_id),
            KEY ip_address (ip_address)
        ) $charset_collate;
        ";

        $sql[] = "
        CREATE TABLE affiliate_commissions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `type` ENUM('ref_link','course_owner') NOT NULL,
            referrer_id BIGINT UNSIGNED NOT NULL,
            buyer_id BIGINT UNSIGNED NOT NULL,
            course_id BIGINT UNSIGNED NOT NULL,
            order_id BIGINT UNSIGNED DEFAULT NULL,
            amount DECIMAL(10,2) NOT NULL,
            rate DECIMAL(5,2) NOT NULL,
            status ENUM('pending','approved','paid','rejected') DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY referrer_id (referrer_id),
            KEY buyer_id (buyer_id),
            KEY course_id (course_id),
            KEY order_id (order_id)
        ) $charset_collate;
        ";

        $sql[] = "
        CREATE TABLE affiliate_payouts (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            referrer_id BIGINT UNSIGNED NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            payout_method VARCHAR(50) DEFAULT NULL,
            payout_details TEXT,
            status ENUM('processing','completed','failed') DEFAULT 'processing',
            paid_at DATETIME DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            KEY referrer_id (referrer_id)
        ) $charset_collate;
        ";

        // New aggregated stats table
        $sql[] = "
        CREATE TABLE affiliate_stats (
            referrer_id BIGINT UNSIGNED PRIMARY KEY,
            clicks_count BIGINT UNSIGNED DEFAULT 0,
            commissions_total DECIMAL(12,2) DEFAULT 0.00,
            last_payout_at DATETIME DEFAULT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;
        ";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($sql as $s) {
            dbDelta($s);
        }
    }

    public static function deactivate() {
        // nothing destructive on deactivate
    }
}
