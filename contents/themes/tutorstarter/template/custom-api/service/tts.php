<?php

function text_to_speech($request) {
    $text = $request->get_param('text') ?? '';
    $lang = $request->get_param('lang') ?? 'en';

    if (!$text) {
        return new WP_REST_Response('Missing text', 400);
    }

    $url = 'https://translate.google.com/translate_tts?' . http_build_query([
        'ie' => 'UTF-8',
        'q' => $text,
        'tl' => $lang,
        'client' => 'tw-ob'
    ]);

    $opts = ['http' => ['header' => "User-Agent: Mozilla/5.0\r\n"]];
    $context = stream_context_create($opts);
    $audio = fopen($url, 'rb', false, $context);

    if (!$audio) {
        return new WP_REST_Response('Failed to fetch audio', 500);
    }

    header('Content-Type: audio/mpeg');
    fpassthru($audio);
    exit; // Quan trọng để không WordPress tự thêm dữ liệu sau audio
}
