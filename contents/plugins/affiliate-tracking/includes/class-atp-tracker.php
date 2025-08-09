<?php
if (!defined('ABSPATH')) exit;

class ATP_Tracker {
    protected $cookie_name = 'affiliate_ref';
    protected $cookie_ttl = 30 * DAY_IN_SECONDS; // 30 days

    public function __construct() {
        add_action('init', [$this, 'maybe_capture_ref']);
        add_action('wp_login', [$this, 'on_user_login'], 10, 2);
        add_action('user_register', [$this, 'on_user_register'], 10, 1);
    }

    public function maybe_capture_ref() {
        if (empty($_GET['ref'])) {
            return;
        }

        $ref_code = sanitize_text_field($_GET['ref']);
        if (empty($ref_code)) return;

        global $wpdb;
        $refs_table = 'affiliate_refs';

        $ref = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$refs_table} WHERE ref_code = %s AND status = 'active' LIMIT 1", $ref_code)
        );

        if (!$ref) {
            error_log("ATP: invalid ref_code: {$ref_code}");
            return;
        }

        $referrer_id = intval($ref->id);
        $current_user_id = get_current_user_id();

        // Log click
        $this->log_click($referrer_id);

        if ($current_user_id) {
            // Update last_referrer_id in tracking table
            $this->update_user_tracking_last($current_user_id, $referrer_id);
        } else {
            // Not logged in: set cookie with referrer_id and ref_code (store ref id for speed)
            $cookie_value = wp_json_encode([
                'referrer_id' => $referrer_id,
                'ref_code' => $ref_code,
                'ts' => time()
            ]);
            atp_set_cookie($this->cookie_name, rawurlencode($cookie_value), time() + $this->cookie_ttl);
            // Also set $_COOKIE for immediate availability during same request
            $_COOKIE[$this->cookie_name] = rawurlencode($cookie_value);
        }
    }

    protected function log_click($referrer_id) {
        global $wpdb;
        $clicks_table = 'affiliate_clicks';

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $landing = home_url($_SERVER['REQUEST_URI']);
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        $wpdb->insert(
            $clicks_table,
            [
                'referrer_id' => $referrer_id,
                'ip_address' => $ip,
                'user_agent' => $ua,
                'landing_page' => $landing,
                'referer_url' => $referer,
            ],
            ['%d', '%s', '%s', '%s', '%s']
        );

        // update stats
        atp_stats_increment_clicks($referrer_id, 1);
    }

    protected function update_user_tracking_last($user_id, $referrer_id) {
        global $wpdb;
        $track_table =  'affiliate_user_tracking';
        $wpdb->query(
            $wpdb->prepare(
                "INSERT INTO {$track_table} (user_id, last_referrer_id) VALUES (%d, %d)
                 ON DUPLICATE KEY UPDATE last_referrer_id = VALUES(last_referrer_id), updated_at = CURRENT_TIMESTAMP",
                $user_id, $referrer_id
            )
        );
    }

    public function on_user_login($user_login, $user) {
        $this->apply_cookie_to_user($user->ID);
    }

    public function on_user_register($user_id) {
        $this->apply_cookie_to_user($user_id, true);
    }

    protected function apply_cookie_to_user($user_id, $is_register = false) {
        if (empty($_COOKIE[$this->cookie_name])) {
            return;
        }

        $cookie = rawurldecode($_COOKIE[$this->cookie_name]);
        $data = json_decode($cookie, true);
        if (empty($data) || empty($data['referrer_id'])) {
            return;
        }
        $referrer_id = intval($data['referrer_id']);

        global $wpdb;
        $track_table = 'affiliate_user_tracking';

        // If first_referrer_id is empty: set it (first click wins); always set last_referrer_id
        // Use a single query: read current first_referrer_id then update accordingly
        $row = $wpdb->get_row($wpdb->prepare("SELECT first_referrer_id FROM {$track_table} WHERE user_id = %d LIMIT 1", $user_id));

        if (!$row) {
            // create with both first and last
            $wpdb->insert(
                $track_table,
                [
                    'user_id' => $user_id,
                    'first_referrer_id' => $referrer_id,
                    'last_referrer_id' => $referrer_id,
                ],
                ['%d', '%d', '%d']
            );
        } else {
            if (empty($row->first_referrer_id)) {
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$track_table} SET first_referrer_id = %d, last_referrer_id = %d, updated_at = CURRENT_TIMESTAMP WHERE user_id = %d",
                        $referrer_id, $referrer_id, $user_id
                    )
                );
            } else {
                // keep first, update last
                $wpdb->query(
                    $wpdb->prepare(
                        "UPDATE {$track_table} SET last_referrer_id = %d, updated_at = CURRENT_TIMESTAMP WHERE user_id = %d",
                        $referrer_id, $user_id
                    )
                );
            }
        }

        // remove cookie after applying
        setcookie($this->cookie_name, '', time() - 3600, defined('COOKIEPATH') ? COOKIEPATH : '/');
        unset($_COOKIE[$this->cookie_name]);
    }
}
