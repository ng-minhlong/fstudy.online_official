<?php
/*
 * Template Name: Video Player FROM STUDYAI
 * Template Post Type: video
 */
$site_url = get_site_url();

$video_id = get_query_var('custom_video_id');
?>

<?php if ($video_id): ?>
    <div id="video-container" style="width: 100%; height: 100vh;">
        <p>Đang tải video...</p>
    </div>

    <script>
        (async () => {
            const videoId = "<?php echo esc_js($video_id); ?>";
            try {
                const response = await fetch(`<?php echo  $site_url; ?>/api/v1/video-data/${videoId}`);
                if (!response.ok) throw new Error("API trả về lỗi");

                const data = await response.json();

                if (data.abyss_status === "Live" && data.abyss_slug) {
                    const iframe = document.createElement("iframe");
                    iframe.src = `https://short.icu/${data.abyss_slug}`;
                    iframe.style.width = "100%";
                    iframe.style.height = "100%";
                    iframe.style.border = "none";

                    const container = document.getElementById("video-container");
                    container.innerHTML = ""; // Xóa nội dung "Đang tải"
                    container.appendChild(iframe);
                } else {
                    document.getElementById("video-container").innerHTML = "<p>Video không khả dụng hoặc bị lỗi.</p>";
                    console.warn("Status:", data.abyss_status);
                }

            } catch (err) {
                console.error("Lỗi khi gọi API hoặc xử lý dữ liệu:", err);
                document.getElementById("video-container").innerHTML = "<p>Đã xảy ra lỗi khi tải video.</p>";
            }
        })();
    </script>
<?php else: ?>
    <p>Không có video ID hợp lệ.</p>
<?php endif; ?>

