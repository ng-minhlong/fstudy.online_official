<?php
/**
 * Plugin Name: Custom Lesson Importer CSV
 * Description: Import các topics + lessons từ file Excel vào hệ thống Tutor.
 * Version: 1.0
 * Author: LongĐZ
 */

defined('ABSPATH') || exit;
require_once __DIR__ . '/vendor/autoload.php';

require_once plugin_dir_path(__FILE__) . 'includes/admin-ui.php';
require_once plugin_dir_path(__FILE__) . 'includes/import-logic.php';
