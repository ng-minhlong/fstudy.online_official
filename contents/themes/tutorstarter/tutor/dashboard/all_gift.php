<?php
/*
 * Template Name: Gift Receive Template
 * Template Post Type: gift receive page
 */

$user_id = get_current_user_id();

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

// Lấy giá trị custom_gift_id từ URL


    global $wpdb;

    // Thực hiện truy vấn để lấy id_video_orginal 
    $sql = "SELECT id_gift, gift_send, title_gift, content_gift, date_created, date_expired, user_receive FROM gift_from_admin";
$results = $wpdb->get_results($sql);

if ($results) {
    $siteurl = get_site_url();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>All Gifts</title>
        <style>
            #container {
                display: grid;
                gap: 16px;
                max-width: 800px;
                margin: 0 auto;
                padding: 16px;
            }
            .gift-card {
                padding: 16px;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .gift-title {
                font-size: 1.25rem;
                font-weight: bold;
                margin-bottom: 8px;
            }
            .gift-content {
                color: #666;
                margin-bottom: 12px;
            }
            .button-group {
                display: flex;
                justify-content: space-between;
            }
            .btn {
                background-color: #007bff;
                color: white;
                padding: 8px 12px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                text-decoration: none;
            }
            .btn:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <div id="container">
            <?php foreach ($results as $gift) : ?>
                <div class="gift-card">
                    <div class="gift-title">
                        <?= esc_html($gift->title_gift ?? 'Không có dữ liệu') ?>
                    </div>
                    <div class="gift-content">
                        <?= esc_html(mb_strimwidth($gift->content_gift ?? 'Không có dữ liệu', 0, 100, '...')) ?>
                    </div>
                    <div class="gift-content">
                        <i> Thời gian nhận: <?= esc_html($gift->date_created ?? 'Không có dữ liệu') ?> -  <?= esc_html($gift->date_expired ?? 'Không có dữ liệu') ?></i>
                    </div>

                 
                    <div class="button-group">
                        <a class="btn" href="<?= esc_url($siteurl . '/gift/' . $gift->id_gift) ?>">Nhận quà</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </body>
    </html>
    <?php
} else {
    echo 'Lỗi: Không tìm thấy phần quà nào';
}

?>
