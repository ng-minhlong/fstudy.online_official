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
    if (isset($input['results'])) {
       
        $results = $input['results'];


        $bandResults = calculateOverallBand($results);

        // Trả về dữ liệu đã nhận và kết quả phân tích
        echo json_encode([
            'status' => 'success',
          //  'results' => $results, /*For dev - not show in live */
            'bands' => [
                'fluency' => $bandResults['flu'],
                'lexicalResource' => $bandResults['lr'],
                'grammar' => $bandResults['gra'],
                'pronunciation' => $bandResults['pro'],
                'overallBand' => $bandResults['overallBand']
            ]           

        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}



function calculateOverallBand($results) {
    $totalFlu = 0;
    $totalLr = 0;
    $totalGra = 0;
    $totalPro = 0;
    $count = 0;

    // Calculate sum of each band across all parts
    foreach ($results as $part) {
        if (isset($part['final_analysis']['band'])) {
            $band = $part['final_analysis']['band'];
            $totalFlu += $band['flu'];
            $totalLr += $band['lr'];
            $totalGra += $band['gra'];
            $totalPro += $band['pro'];
            $count++;
        }
    }

    if ($count === 0) {
        return [
            'flu' => 0,
            'lr' => 0,
            'gra' => 0,
            'pro' => 0,
            'overallBand' => 0
        ];
    }

    // Calculate average for each skill
    $avgFlu = $totalFlu / $count;
    $avgLr = $totalLr / $count;
    $avgGra = $totalGra / $count;
    $avgPro = $totalPro / $count;

    // Round each average to nearest .0 or .5
    $roundedFlu = roundToNearestHalf($avgFlu);
    $roundedLr = roundToNearestHalf($avgLr);
    $roundedGra = roundToNearestHalf($avgGra);
    $roundedPro = roundToNearestHalf($avgPro);

    // Calculate overall band as average of the four rounded averages
    $overallBand = ($roundedFlu + $roundedLr + $roundedGra + $roundedPro) / 4;
    $roundedOverall = roundToNearestHalf($overallBand);

    return [
        'flu' => $roundedFlu,
        'lr' => $roundedLr,
        'gra' => $roundedGra,
        'pro' => $roundedPro,
        'overallBand' => $roundedOverall
    ];
}

function roundToNearestHalf($num) {
    return round($num * 2) / 2;
}