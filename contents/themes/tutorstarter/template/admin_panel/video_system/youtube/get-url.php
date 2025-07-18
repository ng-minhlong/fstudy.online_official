<?php
function get_url_youtube(WP_REST_Request $request) {
    $youtube_url = $request->get_param('url');

    if (!$youtube_url) {
        return new WP_REST_Response(['success' => false, 'error' => 'Missing URL'], 400);
    }

    $escaped_url = escapeshellarg($youtube_url);
    $cmd = "yt-dlp -j {$escaped_url} 2>&1";
    $output = shell_exec($cmd);

    if (!$output) {
        return new WP_REST_Response(['success' => false, 'error' => 'yt-dlp failed'], 500);
    }

    $info = json_decode($output, true);
    if (!$info || empty($info['formats'])) {
        return new WP_REST_Response(['success' => false, 'error' => 'Invalid response from yt-dlp'], 500);
    }

    // Tìm format tốt nhất (MP4)
    $formats = array_reverse($info['formats']);
    $best = null;
    foreach ($formats as $f) {
        if ($f['ext'] === 'mp4' && isset($f['url'])) {
            $best = $f;
            break;
        }
    }

    if (!$best) {
        return new WP_REST_Response(['success' => false, 'error' => 'No MP4 link found'], 404);
    }

    return new WP_REST_Response([
        'success' => true,
        'title' => $info['title'],
        'thumbnail' => $info['thumbnail'],
        'url' => $best['url']
    ]);
}