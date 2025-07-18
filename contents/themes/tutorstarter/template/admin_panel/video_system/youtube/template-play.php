<?php
/*
 * Template Name: Video Player FROM STUDYAI
 * Template Post Type: video
 */
$video_id = get_query_var('custom_video_id');

if ($video_id) {
    // Nhúng thư viện Plyr
    wp_enqueue_style('plyr-css', 'https://cdn.plyr.io/3.7.8/plyr.css', array(), '3.7.8');
    wp_enqueue_script('plyr-js', 'https://cdn.plyr.io/3.7.8/plyr.js', array(), '3.7.8', true);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <style>
    .ytp-chrome-top .ytp-show-cards-title {
            display: none !important;
        }
        .ytp-popup .ytp-contextmenu {
            display: none !important;
        }
        .ytp-panel-menu {
            display: none !important;
        }
        .ytp-title-text{
            display: none !important;
        }
        .ytp-title-link{
            display: none !important;
        }
        .yt-uix-sessionlink{
            display: none !important;
        }
</style>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* Ẩn các nút không mong muốn */
        .plyr__controls button[data-plyr="share"],
        .plyr__controls button[data-plyr="captions"],
        .plyr__controls button[data-plyr="airplay"] {
            display: none !important;
        }
        
        /* Ẩn tiêu đề YouTube */
        .plyr__video-embed::before {
            display: none !important;
        }
        
        /* Ẩn tiêu đề khi tạm dừng */
        .plyr--paused .plyr__video-embed::before {
            display: none !important;
        }
        
        /* Chặn selection trong player */
        .plyr__video-wrapper {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        

    </style>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <div class="video-container" id="video-container">
        <div class="plyr__video-embed" id="player">
            <iframe
                id="yt-iframe"
                src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>?controls=0&showinfo=0&modestbranding=1&rel=0&iv_load_policy=3&fs=0&disablekb=1&playsinline=1&autohide=1&enablejsapi=1"
                frameborder="0"
                allow="autoplay; encrypted-media"
                allowfullscreen
                style="width:100%;height:100%;"
                sandbox="allow-same-origin allow-scripts allow-presentation"
            ></iframe>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Khởi tạo Plyr player với custom iframe (YouTube embed không controls, không logo)
        const player = new Plyr('#player', {
            controls: [
                'play-large',
                'play',
                'progress',
                'current-time',
                'mute',
                'volume',
                'pip',
                'fullscreen'
            ],
            settings: ['quality', 'speed'],
            hideControls: false,
            ratio: '16:9',
            // Không dùng provider youtube để Plyr không tự tạo lại iframe
        });

        // Chặn chuột phải trên iframe
        const iframe = document.getElementById('yt-iframe');
        if (iframe) {
            iframe.addEventListener('contextmenu', e => {
                e.preventDefault();
                return false;
            });
        }
    });
    document.addEventListener('DOMContentLoaded', () => {
    const iframe = document.getElementById('yt-iframe');

    const hideYTElements = () => {
        const iframeDoc = iframe?.contentDocument || iframe?.contentWindow?.document;
        if (!iframeDoc) return;

        try {
            const style = iframeDoc.createElement('style');
            style.textContent = `
                .ytp-chrome-top .ytp-show-cards-title,
                .ytp-popup .ytp-contextmenu,
                .ytp-panel-menu {
                    display: none !important;
                }
            `;
            iframeDoc.head.appendChild(style);
        } catch (err) {
            // likely CORS blocked
            console.warn("Can't access iframe contents due to cross-origin policy.");
        }
    };

    iframe.addEventListener('load', () => {
        hideYTElements();
    });
});

    </script>
    
    
    <?php wp_footer(); ?>
</body>
</html>
<?php
} else {
    echo '<p>Không tìm thấy video</p>';
}
?>