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
    if (isset($input['question'], $input['answer'], $input['sample'], $input['part'], $input['idquestion'], $input['data'])) {
        $question = $input['question'];
        $answer = $input['answer'];
        $sample = $input['sample'];
        $part = $input['part'];
        $idquestion = $input['idquestion'];
        $essay_type = $input['type'];
        $data = $input['data'];


        // Gọi các hàm xử lý dữ liệu
        $grammarCheck = checkGrammarAndSpelling($answer);
        $ai_route_stats = prepareAIRoute();
        $ai_response = fetchAIResponse($question, $answer, $part);
        $finaloverall = finalOveralBand();
        $finalcomment = finalComment();
        $finalSuggestion = finalImprovementSuggestion();
        $finalImprovement_Suggest_words = finalSuggestionWordAndPhrase();
        $finalTopWeak = finalTopWeakPoint();
        $finalOverviewEssay = finaloverViewEssay($answer, $essay_type, $linkingWords);

        $wordFrequency = getWordFrequency($answer);
        $standardCheck = CheckStandard($answer);
        $linkingWordsCheck = checkLinkingWords($answer, $linkingWords);

        // Trả về dữ liệu đã nhận và kết quả phân tích
        echo json_encode([
            'status' => 'success',
            'data' => $data, /*For dev - not show in live */

            //'question' => $question, /*For dev - not show in live */
            //'answer' => $answer, 
            //'sample' => $sample, /*For dev - not show in live */
            //'part' => $part, /*For dev - not show in live */
            'ai_route_stats' => $ai_route_stats, /*For dev - not show in live */
            'idquestion' => $idquestion,
            //'ai_response' => $ai_response, /*For dev - not show in live */

            'final_analysis' => [
                'analysis' => [
                   // 'grammarCheck' => $grammarCheck,
                    //'wordFrequency' => $wordFrequency,
                    'standardCheck' => $standardCheck,
                    'linkingWordsCheck' => $linkingWordsCheck
                ],
                'overview_essay' => $finalOverviewEssay,
                'band' => $finaloverall,
                'detail_recommendation' => $finalcomment,
                'improvement_words' => $finalImprovement_Suggest_words,
                'suggestion' => $finalSuggestion,
                'top_weak_point' => $finalTopWeak
            ]

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


function finalOveralBand() {
    global $ai_response_data;

    if (!isset($ai_response_data['choices'][0]['message']['content'])) {
        return ['error' => 'Invalid AI response'];
    }

    $content = $ai_response_data['choices'][0]['message']['content'];

    preg_match('/"Task_Achievement":\s*([\d\.]+)/', $content, $ta);
    preg_match('/"Coherence_and_Cohesion":\s*([\d\.]+)/', $content, $cc);
    preg_match('/"Lexical_Resource":\s*([\d\.]+)/', $content, $lr);
    preg_match('/"Grammatical_Range_and_Accuracy":\s*([\d\.]+)/', $content, $gra);

    $ta_score  = isset($ta[1]) ? floatval($ta[1]) : null;
    $cc_score  = isset($cc[1]) ? floatval($cc[1]) : null;
    $lr_score  = isset($lr[1]) ? floatval($lr[1]) : null;
    $gra_score = isset($gra[1]) ? floatval($gra[1]) : null;

    if (is_null($ta_score) || is_null($cc_score) || is_null($lr_score) || is_null($gra_score)) {
        return ['error' => 'Missing score values'];
    }

    // Tính tổng và làm tròn .5 hoặc .0
    $average = round((($ta_score + $cc_score + $lr_score + $gra_score) / 4) * 2) / 2;

    return [
        'ta'  => $ta_score,
        'cc'  => $cc_score,
        'lr'  => $lr_score,
        'gra' => $gra_score,
        'overallband' => $average
    ];
}

function finalTopWeakPoint() {
    $scores = finalOveralBand();

    if (isset($scores['error'])) {
        return $scores; // Trả về lỗi nếu có
    }

    // Danh sách tiêu chí với điểm
    $criteria = [
        'Task Achievement'                => $scores['ta'],
        'Coherence and Cohesion'          => $scores['cc'],
        'Lexical Resource'                => $scores['lr'],
        'Grammatical Range and Accuracy'  => $scores['gra']
    ];

    // Tìm tiêu chí có điểm thấp nhất
    $min_score = min($criteria);
    $weakest_areas = array_keys($criteria, $min_score);

    // Ghép các phần yếu thành chuỗi
    $weakest_text = implode(', ', $weakest_areas);

    return [
        'weakest_score' => $min_score,
        'message' => "Bạn đang kém nhất ở phần: $weakest_text"
    ];
}


function finalComment() {
    global $ai_response_data;

    if (!isset($ai_response_data['choices'][0]['message']['content'])) {
        return ['error' => 'Invalid AI response'];
    }

    $content = $ai_response_data['choices'][0]['message']['content'];

    // Dùng regex để lấy các comment của từng tiêu chí
    preg_match('/"task_response_comment":\s*"([^"]+)"/', $content, $ta_comment);
    preg_match('/"coherence_and_cohesion_comment":\s*"([^"]+)"/', $content, $cc_comment);
    preg_match('/"lexical_resource_comment":\s*"([^"]+)"/', $content, $lr_comment);
    preg_match('/"grammatical_range_and_accuracy_comment":\s*"([^"]+)"/', $content, $gra_comment);

    // Kiểm tra nếu có thiếu comment nào thì báo lỗi
    if (!isset($ta_comment[1]) || !isset($cc_comment[1]) || !isset($lr_comment[1]) || !isset($gra_comment[1])) {
        return ['error' => 'Missing comment values'];
    }

    return [
        'ta_tr' => $ta_comment[1],
        'cc' => $cc_comment[1],
        'lr' => $lr_comment[1],
        'gra' => $gra_comment[1]
    ];
}
function finalImprovementSuggestion() {
    global $ai_response_data;

    if (!isset($ai_response_data['choices'][0]['message']['content'])) {
        return ['error' => 'Invalid AI response'];
    }

    $content = $ai_response_data['choices'][0]['message']['content'];

    preg_match('/"improvement_suggestions":\s*"([^"]+)"/', $content, $improvement);

    if (!isset($improvement[1])) {
        return ['error' => 'Missing improvement suggestions'];
    }

    return ['improvement_suggestions' => $improvement[1]];
}

function finalSuggestionWordAndPhrase() {
    global $ai_response_data;

    if (!isset($ai_response_data['choices'][0]['message']['content'])) {
        return ['error' => 'Invalid AI response'];
    }

    $content = $ai_response_data['choices'][0]['message']['content'];

    preg_match('/"suggested_words_or_phrases":\s*(\[[^\]]+\])/', $content, $suggestions);

    if (!isset($suggestions[1])) {
        return ['error' => 'Missing suggested words or phrases'];
    }

    return json_decode($suggestions[1], true);
}

function finaloverViewEssay($answer, $essay_type, $linkingWords) {
    $word_count = str_word_count($answer);
    $sentence_count = preg_match_all('/[.!?]/', $answer, $matches);
    $paragraph_count = substr_count($answer, "\n") + 1; // Đếm số dòng xuống

    // Kiểm tra Linking Words
    $linkingWordsData = checkLinkingWords($answer, $linkingWords);

    // Kiểm tra Lỗi Ngữ Pháp & Chính Tả
    $grammarAndSpellingData = checkGrammarAndSpelling($answer);

    // Tính tần suất từ
    $wordFrequency = getWordFrequency($answer);

    return [
        "word_count" => $word_count,
        "sentence_count" => $sentence_count,
        "paragraph_count" => $paragraph_count,
        "essay_type" => $essay_type,
        "linkingWordsCount" => $linkingWordsData['linkingWordsCount'],
        "uniqueLinkingWords" => $linkingWordsData['uniqueLinkingWords'],
        "foundLinkingWords" => $linkingWordsData['foundLinkingWords'],
        "total_errors_count" => $grammarAndSpellingData['total_errors_count'], // Thêm số lỗi tổng cộng
        "grammar_suggestions" => $grammarAndSpellingData['suggestions'], // Danh sách lỗi & gợi ý sửa
        "word_frequency" => $wordFrequency // Thêm tần suất từ
    ];
}


// Các hàm xử lý logic chính (chuyển từ code Node.js)
function getFirstSentence($text) {
    $sentences = explode('.', $text);
    return count($sentences) > 1 ? trim($sentences[0]) . '.' : trim($text);
}

function checkGrammarAndSpelling($text) {
    if (empty($text)) {
        throw new Exception('Text is required');
    }
    $response = wp_remote_post('https://api.languagetool.org/v2/check', [
        'body' => [
            'text' => $text,
            'language' => 'en-US',
        ],
    ]);
    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (isset($data['matches'])) {
        $totalErrors = count($data['matches']);
        $suggestions = array_map(function ($match) use ($text) {
            $wrongWord = substr($text, $match['offset'], $match['length']);
            return [
                'message' => $match['message'],
                'replacements' => array_column($match['replacements'], 'value'),
                'wrongWord' => $wrongWord,
            ];
        }, $data['matches']);
        return ['total_errors_count' => $totalErrors, 'suggestions' => $suggestions];
    }
    return ['total_errors_count' => 0, 'suggestions' => []];
}

function getWordFrequency($text) {
    $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $frequency = array_count_values(array_map('strtolower', $words));
    return $frequency;
}

function CheckStandard($answer) {
    $wordCount = str_word_count($answer);
    $sentenceCount = substr_count($answer, '.') + substr_count($answer, '!') + substr_count($answer, '?');
    $totalErrors = checkGrammarAndSpelling($answer)['total_errors_count'];
    $errorPercentage = ($totalErrors / $wordCount) * 100;

    $maxPointOverall = 8.5;
    $standardQualify = 'Đạt tiêu chuẩn';

    if ($wordCount <= 100 || $sentenceCount < 7) {
        $standardQualify = 'Không đạt tiêu chuẩn';
        $maxPointOverall = 2.0;
    } elseif ($errorPercentage > 15) {
        $standardQualify = 'Không đạt tiêu chuẩn';
        $maxPointOverall = 5.0;
    }

    return [
        'standardQualify' => $standardQualify,
        'maxPointOverall' => $maxPointOverall,
    ];
}

function WordRepetitionAnalyze($text) {
    $words = preg_split('/\s+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
    $frequency = array_count_values($words);
    $uniqueWords = count(array_filter($frequency, function ($count) {
        return $count > 5;
    }));
    return ['totalUniqueWords' => $uniqueWords];
}


function getSecondParagraph($text) {
    $paragraphs = preg_split("/\n+/", $text);
    return count($paragraphs) > 1 ? trim($paragraphs[1]) : trim($text);
}

$increase_words = ["rise","grown","grew","go up","uplift","rocketed","climb","went up","rose","rocket","upsurge","upsurge","soar","increase","increased"];
$decrease_words = ["plummet","plummeted","decrease","decreased","drop","dropped","go down","went down"];
$unchange_words = ["unchange","remain","similar"];
$good_verb_Words=["peak","rocket","soar","upsurge","upsurged","rocketed","uplift","uplifted","plummet","plummeted","double","doubled"];
$adjective_adverb_Words=["significantlly","rapidly","rapid","significant","fast","fastly","approximate","approximately","slow","slowly","sharply","sharp","suddenly","sudden","considerable","considerably","slightly","steadily","rough","roughly","remarkable","remarkably","moderate","moderately","stable","stably"];
$well_adjective_adverb_Words=["temporarily","dramatically","dramatical","exponentially","exponential","notably","notable","minimally","minimal","sizably","sizable","halve","abrupt","abruptly","gradually","gradual","consistently","consistent","shift","shiftly"];


function checkVocabularyRange($answer, $wordList) {
    $wordCount = 0;
    $uniqueWords = [];
    $essayWords = preg_split("/\s+/", strtolower($answer));

    foreach ($wordList as $word) {
        $count = array_count_values($essayWords)[$word] ?? 0;
        if ($count > 0) {
            $wordCount += $count;
            $uniqueWords[$word] = $count;
        }
    }

    return ['wordCount' => $wordCount, 'uniqueWords' => $uniqueWords];
}



$linkingWords = [
  "furthermore", "additionally", "in addition to", "also", "moreover", "and", 
  "as well as", "during", "while", "until", "before", "afterward", "in the end",
  "at the same time", "meanwhile", "subsequently", "simultaneously", "firstly",
  "secondly", "thirdly", "fourthly", "lastly", "for instance", "for example",
  "to cite an example", "to illustrate", "namely", "obviously", "particularly",
  "in particular", "especially", "specifically", "clearly", "as a result",
  "therefore", "thus", "consequently", "for this reason", "so", "hence"
];

function checkLinkingWords($answer) {
    global $linkingWords; // Gọi biến toàn cục

    $linkingWordsCount = 0;
    $uniqueLinkingWords = [];
    $foundLinkingWords = [];
    $essayWords = preg_split("/\s+/", strtolower($answer));

    foreach ($linkingWords as $word) {
        $count = array_count_values($essayWords)[$word] ?? 0;
        if ($count > 0) {
            $linkingWordsCount += $count;
            $uniqueLinkingWords[$word] = $count;
            $foundLinkingWords[] = ['word' => $word, 'count' => $count];
        }
    }

    return [
        'linkingWordsCount' => $linkingWordsCount,
        'uniqueLinkingWords' => count(array_keys($uniqueLinkingWords)),
        'foundLinkingWords' => $foundLinkingWords,
    ];
}

function findCommonNumbers($answer, $sample) {
    $answerNumbers = preg_match_all('/\b\d+\b/', $answer, $answerMatches) ? $answerMatches[0] : [];
    $sampleNumbers = preg_match_all('/\b\d+\b/', $sample, $sampleMatches) ? $sampleMatches[0] : [];
    $commonNumbers = array_values(array_intersect($answerNumbers, $sampleNumbers));
    return [
        'count' => count($commonNumbers),
        'numbers' => $commonNumbers
    ];
}

function wordCountPerParagraph($text) {
    $paragraphs = preg_split('/\n+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $details = [];
    foreach ($paragraphs as $index => $paragraph) {
        $wordCount = str_word_count($paragraph);
        $details[] = [
            'paragraph_number' => $index + 1,
            'word_count' => $wordCount,
        ];
    }
    return $details;
}

function checkOverall($answer) {
    $wordCount = str_word_count($answer);
    $paragraphs = preg_split('/\n+/', $answer, -1, PREG_SPLIT_NO_EMPTY);
    $sentenceCount = substr_count($answer, '.');
    $wordsPerParagraph = wordCountPerParagraph($answer);

    return [
        'word_count' => $wordCount,
        'paragraph_count' => count($paragraphs),
        'sentence_count' => $sentenceCount,
        'words_per_paragraph' => $wordsPerParagraph,
    ];
}

function determineQuestionType($part, $question) {
    $lowerQuestion = strtolower($question);
    if ($part == 1) {
        if (strpos($lowerQuestion, 'line') !== false) return 'Line graph';
        if (strpos($lowerQuestion, 'chart') !== false || strpos($lowerQuestion, 'bar chart') !== false) return 'Bar/Pie chart';
        if (strpos($lowerQuestion, 'map') !== false) return 'Map';
        if (strpos($lowerQuestion, 'pie') !== false) return 'Pie chart';
        if (strpos($lowerQuestion, 'table') !== false) return 'Table';
        if (strpos($lowerQuestion, 'process') !== false) return 'Process';
        if (strpos($lowerQuestion, 'diagram') !== false) return 'Diagram';
        return 'Mixed';
    } else if ($part == 2) {
        if (strpos($lowerQuestion, 'both views') !== false) return 'Type discuss both views - Part 2';
        if (strpos($lowerQuestion, 'agree or disagree') !== false) return 'Type Agree or Disagree Essay - Part 2';
        if (strpos($lowerQuestion, 'advantages') !== false) return 'Type Advantages and Disadvantages Essay - Part 2';
        return 'Unknown Type - Part 2';
    }
    return 'Unknown';
}


// Hàm phân tích cấu trúc đoạn văn
function find_structure_positions($answer) {
    $paragraphs = preg_split('/\n+/', trim($answer));
    $results = [
        'overall' => [
            'wordFound' => false,
            'wordPosition' => []
        ],
        'detail' => [
            'wordFound' => false,
            'wordPosition' => []
        ],
        'intro' => [
            'wordFound' => false,
            'wordPosition' => []
        ]
    ];

    // Tìm kiếm đoạn văn 'overall'
    $overallPhrases = ['overall', 'as you can see', 'summary'];
    foreach ($paragraphs as $index => $paragraph) {
        foreach ($overallPhrases as $phrase) {
            if (stripos($paragraph, $phrase) !== false) {
                $results['overall']['wordFound'] = true;
                $results['overall']['wordPosition'][] = $index + 1;
                break;
            }
        }
    }

    // Tìm đoạn văn dài nhất (>50 từ) cho 'detail'
    $longestIndex = null;
    foreach ($paragraphs as $index => $paragraph) {
        $wordCount = str_word_count($paragraph);
        if ($wordCount > 50 && ($longestIndex === null || $wordCount > str_word_count($paragraphs[$longestIndex]))) {
            $longestIndex = $index;
        }
    }
    if ($longestIndex !== null) {
        $results['detail'] = [
            'wordFound' => true,
            'wordPosition' => [$longestIndex + 1]
        ];
    }

    // Tìm đoạn văn ngắn nhất có chứa các cụm từ đặc trưng cho 'intro'
    $introPhrases = ['chart', 'demonstrate', 'given'];
    $shortestIndex = null;
    foreach ($paragraphs as $index => $paragraph) {
        foreach ($introPhrases as $phrase) {
            if (stripos($paragraph, $phrase) !== false) {
                if ($shortestIndex === null || strlen($paragraph) < strlen($paragraphs[$shortestIndex])) {
                    $shortestIndex = $index;
                }
                break;
            }
        }
    }
    if ($shortestIndex !== null) {
        $results['intro'] = [
            'wordFound' => true,
            'wordPosition' => [$shortestIndex + 1]
        ];
    }

    return $results;
}

// Hàm phân loại câu
function classify_sentence($sentence) {
    $coordinatingConjunctions = ['and', 'but', 'or', 'nor', 'for', 'yet', 'so'];
    $subordinatingConjunctions = ['after', 'although', 'as', 'because', 'before', 'even if', 'even though', 'if', 'once', 'since', 'so that', 'though', 'unless', 'until', 'when', 'whenever', 'where', 'wherever', 'whether', 'while', 'why'];

    $words = preg_split('/\s+/', $sentence);
    $hasCoordConj = count(array_intersect($words, $coordinatingConjunctions)) > 0;
    $hasSubordConj = count(array_intersect($words, $subordinatingConjunctions)) > 0;

    if (!$hasCoordConj && !$hasSubordConj) return 'Simple';
    if ($hasCoordConj && !$hasSubordConj) return 'Compound';
    if ($hasSubordConj) return 'Complex';
    return 'Simple';
}

// Hàm phân tích câu
function analyze_text($text, $maxLength = 50) {
    $sentences = preg_split('/(?<=[.!?])\s+/', trim($text));
    $analyzeSentence = [
        'Simple' => [],
        'Compound' => [],
        'Complex' => []
    ];

    foreach ($sentences as $index => $sentence) {
        $sentenceType = classify_sentence($sentence);
        $truncatedSentence = strlen($sentence) > $maxLength ? substr($sentence, 0, $maxLength) . '...' : $sentence;
        $analyzeSentence[$sentenceType][] = [
            'index' => $index + 1,
            'truncatedSentence' => $truncatedSentence
        ];
    }

    return $analyzeSentence;
}

$synonymGroups = [
        'synonym_group1' => ["parents", "guardians", "caretakers"],
        'synonym_group2' => ["children", "kids", "offspring", "youth"],
        'synonym_group3' => ["raising", "nurturing", "upbringing", "parenting"],
        'synonym_group4' => ["pressure", "stress", "burden"],
        'synonym_group5' => ["problems", "issues", "challenges"],
        'synonym_group6' => ["consumer", "buyer", "customer","client"],
        'synonym_group7' => ["job", "work", "career"],
        'synonym_group8' => ["salary","wage"],
        'synonym_group9' => ["cost","charge","expense"],
        'synonym_group10' => ["certificate","degree","diploma","qualification"],
        'synonym_group11' => ["commute","travel"],
        'synonym_group12' => ["hunt","poach"],
        'synonym_group13' => ["protect","preserve","conserve"],
        'synonym_group14' => ["environment","habitat"],
        'synonym_group15' => ["waste","rubbish","litter","sewage"],
        'synonym_group16' => ["exhaust","smoke","emission"],
        'synonym_group17' => ["pollute","contaminate"],
        'synonym_group18' => ["traffic jam","traffic congestion"],
        'synonym_group19' => ["rush hour","peak hour"],
        'synonym_group20' => ["accomplish","fullfill","achieve","perform","attain","execute"],
        'synonym_group21' => ["accurate","correct","error-free","veracious","meticulous","specific"],
        'synonym_group22' => ["active","energetic","agile","nimble","vigorous"],
        'synonym_group23' => ["adamant","firm","stubborn","obdurate","unshakeable","steadfast"],
        'synonym_group24' => ["amazing","extraodinary","fabulous","incredible","astonishing","fantastic"],
        'synonym_group25' => ["answer","respond","reaction","reply","solution"],
        'synonym_group26' => ["awful","dreadful","terrible","abdominal"],
        'synonym_group27' => ["balance","parity","fairness","impartiality"],
        'synonym_group28' => ["beautiful","stunning","exquisite","dazzling","ravishing","gorgeous"],
  
        ];




function extract_keywords($text, $topN = 5) {
    // Placeholder for extracting keywords; implement with PHP libraries if needed
    $words = explode(' ', strtolower($text));
    $wordCounts = array_count_values($words);
    arsort($wordCounts);

    return array_slice(array_keys($wordCounts), 0, $topN);
}

function count_keywords_in_text($text, $keywords) {
    $synonymGroups = [
        'synonym_group1' => ["parents", "guardians", "caretakers"],
        'synonym_group2' => ["children", "kids", "offspring", "youth"],
        'synonym_group3' => ["raising", "nurturing", "upbringing", "parenting"],
    ];

    $tokens = preg_split('/\s+/', strtolower($text));
    $counts = [];

    foreach ($keywords as $keyword) {
        $counts[$keyword] = ['count' => 0, 'matchedWords' => []];

        // Find synonym group
        $group = array_filter($synonymGroups, fn($synonyms) => in_array($keyword, $synonyms));
        $keywordSynonyms = $group ? array_values($group)[0] : [$keyword];

        // Count occurrences of the keyword and synonyms
        foreach ($keywordSynonyms as $syn) {
            $occurrences = array_filter($tokens, fn($word) => $word === $syn);
            $counts[$keyword]['count'] += count($occurrences);

            if (count($occurrences) > 0 && !in_array($syn, $counts[$keyword]['matchedWords'])) {
                $counts[$keyword]['matchedWords'][] = $syn;
            }
        }
    }

    return $counts;
}

function analyze_main_idea($testQuestion, $essay, $part) {
    if ($part != 2) return null;

    $testWords = array_map('strtolower', preg_split('/\s+/', $testQuestion));
    $essayWords = array_map('strtolower', preg_split('/\s+/', $essay));

    $commonTerms = array_intersect($testWords, $essayWords);

    if (count($commonTerms) > 5) {
        return "Essay addresses the main idea of the test question. Common terms: " . implode(', ', $commonTerms);
    } else {
        return "Essay does not adequately address the main idea of the test question. Common terms: " . implode(', ', $commonTerms);
    }
}