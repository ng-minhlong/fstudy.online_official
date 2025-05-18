<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Kiểm tra nếu WordPress đã load đầy đủ
if (!defined('ABSPATH')) {
    exit;
}

// Chỉ xử lý nếu là phương thức POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ body JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Kiểm tra nếu các trường cần thiết tồn tại
    if (isset($input['conversationPairs'])) {
        $conversationPairs = $input['conversationPairs'];
       


        $grammarCheckResults = checkGrammarAndSpelling($conversationPairs);
        $pronunciationResults = checkPronunciation($conversationPairs);
        $speakRateFluencyResults = analyzeSpeakRateAndFluency($conversationPairs);
        $vocabularyCheckResults = checkVocabulary($conversationPairs);


        $finalResults = [];
        foreach ($conversationPairs as $pair) {
            $qNumber = $pair['questionNumber'];
            $userAnswer = $pair['userAnswer'] ?? '';
            $audioUrl = $pair['recording']['audioUrl'] ?? '';
            
            $finalResults[] = [
                'questionNumber' => $qNumber,
                'userAnswer' => $userAnswer,  // Include original user answer
                'audioUrl' => $audioUrl,      // Include audio URL
                'grammarCheck' => $grammarCheckResults[$qNumber] ?? [],
                'pronunciation' => $pronunciationResults[$qNumber] ?? [
                    'error' => 'Not analyzed',
                    'debug' => [
                        'userAnswer' => $userAnswer,
                        'audioUrl' => $audioUrl
                    ]
                ],
                'vocabularyCheck' => $vocabularyCheckResults[$qNumber] ?? [],
                'speakRateAndFluency' => $speakRateFluencyResults[$qNumber] ?? []
            ];
        }

        //$ai_route_stats = prepareAIRoute();
        //$ai_response = fetchAIResponse($question, $answer, $part);
        
        /* Tạo các hàm sau
        - Hàm chấm pronuniciation -> Hàm nhận xét tổng quát cách phát âm
        - Hàm chấm vốn từ vựng (vocabulary) -> Hàm nhận xét vốn từ vựng
        - Hàm chấm ngữ pháp => Sau khi ngữ pháp được chấm => Hàm dùng AI chấm xem sử dụng từ hợp lý chưa
        - Hàm xử lý speak rate và nhận xét (độ trôi chảy nữa)
        */


        // Trả về dữ liệu đã nhận và kết quả phân tích
        echo json_encode([ 
            'status' => 'success',
            'data' => $data, /*For dev - not show in live */

            'ai_route_stats' => $ai_route_stats, /*For dev - not show in live */
            //'idquestion' => $idquestion,
            //'ai_response' => $ai_response, /*For dev - not show in live */

            'analysis' => $finalResults

        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}



function prepareAIRoute() {
    $site_url = get_site_url();
    $api_url = $site_url . '/wp-json/api/v1/extract_current_and_prepare_ai_route';

    $api_response = file_get_contents($api_url);
    
    if ($api_response === false) {
        return ['error' => 'Failed to fetch AI route'];
    }
    
    $api_data = json_decode($api_response, true);
    
    if (!isset($api_data['now_end_point'], $api_data['api_info']['api_endpoint_url'])) {
        return ['error' => 'Invalid AI route response'];
    }
    
    return $api_data; // Trả về API route data
}


$ai_response_data = null; // Biến toàn cục để cache response

function fetchAIResponse($question, $answer, $part) {
    global $ai_response_data;
    
    if ($ai_response_data !== null) {
        return $ai_response_data;
    }

    $ai_route_stats = prepareAIRoute();
    if (isset($ai_route_stats['error'])) {
        return ['error' => 'Failed to prepare AI route'];
    }

    $api_endpoint_url = $ai_route_stats['api_info']['api_endpoint_url'] ?? '';
    $api_key = $ai_route_stats['api_info']['api_key'] ?? '';

    if (!$api_endpoint_url) {
        return ['error' => 'API endpoint URL is missing'];
    }

    $post_data = json_encode([
        'type_test' => 'ieltsWriting',
        'question' => $question,
        'part' => $part,
        'answer' => $answer
    ]);

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n" .
                         "Authorization: Bearer $api_key\r\n",
            'method'  => 'POST',
            'content' => $post_data
        ]
    ];

    $context  = stream_context_create($options);
    $ai_response = file_get_contents($api_endpoint_url, false, $context);

    $ai_response_data = $ai_response !== false ? json_decode($ai_response, true) : ['error' => 'Failed to fetch AI response'];
    return $ai_response_data;
}

function checkVocabulary($conversationPairs) {
    $results = [];
    foreach ($conversationPairs as $pair) {
        $questionNumber = $pair['questionNumber'];
        $sentence = $pair['userAnswer'] ?? '';

        $response = wp_remote_post('http://localhost/api/vocab-api/v1/check', [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode(['sentence' => $sentence])
        ]);

        if (is_wp_error($response)) {
            $results[$questionNumber] = ['error' => 'Vocabulary API error'];
        } else {
            $results[$questionNumber] = json_decode(wp_remote_retrieve_body($response), true);
        }
    }
    return $results;
}





function checkGrammarAndSpelling($conversationPairs) {
    $results = [];

    foreach ($conversationPairs as $pair) {
        $userAnswer = $pair['userAnswer'] ?? '';
        $questionNumber = $pair['questionNumber'];

        if (empty($userAnswer)) {
            $results[$questionNumber] = [
                'total_errors_count' => 0,
                'suggestions' => []
            ];
            continue;
        }

        $response = wp_remote_post('https://api.languagetool.org/v2/check', [
            'body' => [
                'text' => $userAnswer,
                'language' => 'en-US',
            ],
        ]);

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($data['matches'])) {
            $totalErrors = count($data['matches']);
            $suggestions = array_map(function ($match) use ($userAnswer) {
                $wrongWord = substr($userAnswer, $match['offset'], $match['length']);
                return [
                    'message' => $match['message'],
                    'replacements' => array_column($match['replacements'], 'value'),
                    'wrongWord' => $wrongWord,
                ];
            }, $data['matches']);
            $results[$questionNumber] = [
                'total_errors_count' => $totalErrors,
                'suggestions' => $suggestions
            ];
        } else {
            $results[$questionNumber] = [
                'total_errors_count' => 0,
                'suggestions' => []
            ];
        }
    }

    return $results;
}


function checkPronunciation($conversationPairs) {
    $results = [];
    
    foreach ($conversationPairs as $pair) {
        $userAnswer = $pair['userAnswer'] ?? '';
        $rawAudioUrl = $pair['recording']['audioUrl'] ?? '';
        $questionNumber = $pair['questionNumber'];
        
        // Clean the URL
        $audioUrl = str_replace('\/', '/', $rawAudioUrl);
        
        // Verify URL is accessible
        if (empty($audioUrl)) {
            $results[$questionNumber] = [
                'error' => true,
                'message' => 'Missing audio URL'
            ];
            continue;
        }
        
        $response = wp_remote_post('http://127.0.0.1:5000/analyze_sentence', [
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 30,
            'body' => json_encode([
                'text' => $userAnswer,
                'audio' => $audioUrl
            ])
        ]);

        if (is_wp_error($response)) {
            $results[$questionNumber] = [
                'error' => true,
                'message' => 'API connection failed',
                'details' => $response->get_error_message(),
                'debug' => [
                    'url_sent' => $audioUrl,
                    'original_url' => $rawAudioUrl
                ]
            ];
        } else {
            $results[$questionNumber] = json_decode(wp_remote_retrieve_body($response), true);
        }
    }
    
    return $results;
}



function analyzeSpeakRateAndFluency($conversationPairs) {
    $results = [];

    foreach ($conversationPairs as $pair) {
        $duration = $pair['recording']['duration'] ?? 0;
        $wordCount = $pair['recording']['wordCount'] ?? 0;
        $questionNumber = $pair['questionNumber'];

        if ($duration <= 0 || $wordCount <= 0) {
            $results[$questionNumber] = [
                'speakRate' => 0,
                'fluencyScore' => 0,
                'comment' => 'No valid recording'
            ];
            continue;
        }

        $speakRate = $wordCount / $duration; // words per second

        // Tự set fluencyScore theo speakRate
        if ($speakRate < 1.0) {
            $fluencyScore = 2; // quá chậm
            $comment = 'Too slow. Try to speak more fluently.';
        } elseif ($speakRate < 2.0) {
            $fluencyScore = 4; // tạm ổn
            $comment = 'Moderate fluency. Could be improved.';
        } else {
            $fluencyScore = 5; // tốt
            $comment = 'Good fluency.';
        }

        $results[$questionNumber] = [
            'speakRate' => round($speakRate, 2),
            'fluencyScore' => $fluencyScore,
            'comment' => $comment
        ];
    }

    return $results;
}
