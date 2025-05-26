<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['messages']) || !is_array($data['messages']) || !isset($data['id_test'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request format']);
    exit;
}

// Kết nối database và lấy thông tin từ conversation_with_ai_list
global $wpdb;
$table_name = 'conversation_with_ai_list';

$id_test = $data['id_test'];
$lang = $data['lang'];

$ai_info = $wpdb->get_row($wpdb->prepare(
    "SELECT ai_role, user_role, testname, target_1, target_2, target_3 FROM $table_name WHERE id_test = %d",
    $id_test
));

if (!$ai_info) {
    http_response_code(404);
    echo json_encode(['error' => 'Test configuration not found']);
    exit;
}

$model = isset($data['model']) ? sanitize_text_field($data['model']) : 'gemma2-9b-it';

// Get the last user message to avoid duplicates
$filteredMessages = [];
$lastUserMessage = null;

foreach ($data['messages'] as $message) {
    // Skip system messages from the client (we'll add our own)
    if ($message['role'] === 'system') continue;
    
    // Skip duplicate user messages
    if ($message['role'] === 'user') {
        if ($lastUserMessage === $message['content']) continue;
        $lastUserMessage = $message['content'];
    }
    
    $filteredMessages[] = [
        'role' => sanitize_text_field($message['role']),
        'content' => sanitize_text_field($message['content'])
    ];
}
function mapLangToName($code) {
    $langMap = [
        'en' => 'English',
        'fr' => 'French',
        'vi' => 'Vietnamese',
        'de' => 'German',
        'ko' => 'Korean',
        'ja' => 'Japanese'
    ];
    return $langMap[$code] ?? 'English'; // fallback nếu không có
}

$langName = mapLangToName($lang);

$system_message_content = sprintf(
    "You are %s. I am %s in the context of %s. Keep answers short (max 50 words) and answer in language %s. Ask for details if needed. If the question is irrelevant, respond with {Not relevant}. The user has 3 targets: Target 1: %s; Target 2: %s; Target 3: %s. If a target is completed, return {Target_number}.",
    $ai_info->ai_role,
    $ai_info->user_role,
    $ai_info->testname,
    $lang,
    $ai_info->target_1,
    $ai_info->target_2,
    $ai_info->target_3
);


// Prepare messages with system message first
$messages = [
    [
        'role' => 'system',
        'content' => $system_message_content
    ]
];

// Add filtered messages
$messages = array_merge($messages, $filteredMessages);

// Call the Groq API
$api_key = "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";
$api_url = "https://api.groq.com/openai/v1/chat/completions";

$args = [
    'headers' => [
        'Authorization' => 'Bearer ' . $api_key,
        'Content-Type' => 'application/json',
    ],
    'body' => json_encode([
        'model' => $model,
        'messages' => $messages,
        'max_tokens' => 50,
        'temperature' => 0.6,
        'top_p' => 0.8,
    ]),
    'timeout' => 30,
];

$response = wp_remote_post($api_url, $args);

if (is_wp_error($response)) {
    http_response_code(500);
    echo json_encode(['error' => $response->get_error_message()]);
    exit;
}

$body = json_decode($response['body'], true);

if (!isset($body['choices'][0]['message']['content'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid response from AI service']);
    exit;
}

// Return the response
echo json_encode([
    'response' => $body['choices'][0]['message']['content'],
    'conversation' => array_merge($messages, [
        ['role' => 'assistant', 'content' => $body['choices'][0]['message']['content']]
    ])
]);
exit;