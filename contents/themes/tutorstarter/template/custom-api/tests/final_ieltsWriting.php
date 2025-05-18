<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if (!defined('ABSPATH')) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['results'])) {
        $results = $input['results'];

        $bandResults = calculateOverallBand($results);
        $partDetails = getPartDetails($results);

        echo json_encode([
            'status' => 'success',
            'bands' => [
                'taskAchievement' => $bandResults['ta'],
                'lexicalResource' => $bandResults['lr'],
                'coherenceAndCohesion' => $bandResults['cc'],
                'grammar' => $bandResults['gra'],
                'overallBand' => $bandResults['overallBand']
            ],
            'details' => $partDetails
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

function calculateOverallBand($results) {
    $sum = ['ta' => 0, 'lr' => 0, 'gra' => 0, 'cc' => 0];
    $weights = 0;

    foreach ($results as $part) {
        if (isset($part['data']['part']) && isset($part['final_analysis']['band'])) {
            $partNum = intval($part['data']['part']);
            $weight = ($partNum === 2) ? 2 : 1;
            $band = $part['final_analysis']['band'];

            $sum['ta'] += $band['ta'] * $weight;
            $sum['lr'] += $band['lr'] * $weight;
            $sum['gra'] += $band['gra'] * $weight;
            $sum['cc'] += $band['cc'] * $weight;
            $weights += $weight;
        }
    }

    if ($weights === 0) {
        return ['ta' => 0, 'lr' => 0, 'gra' => 0, 'cc' => 0, 'overallBand' => 0];
    }

    $ta = roundToNearestHalf($sum['ta'] / $weights);
    $lr = roundToNearestHalf($sum['lr'] / $weights);
    $gra = roundToNearestHalf($sum['gra'] / $weights);
    $cc = roundToNearestHalf($sum['cc'] / $weights);
    $overall = roundToNearestHalf(($ta + $lr + $gra + $cc) / 4);

    return [
        'ta' => $ta,
        'lr' => $lr,
        'gra' => $gra,
        'cc' => $cc,
        'overallBand' => $overall
    ];
}

function getPartDetails($results) {
    $partDetails = [];

    foreach ($results as $part) {
        if (isset($part['data']['part']) && isset($part['final_analysis']['band'])) {
            $p = intval($part['data']['part']);
            $band = $part['final_analysis']['band'];

            $ta = roundToNearestHalf($band['ta']);
            $lr = roundToNearestHalf($band['lr']);
            $cc = roundToNearestHalf($band['cc']);
            $gra = roundToNearestHalf($band['gra']);
            $overall = roundToNearestHalf(($ta + $lr + $cc + $gra) / 4);

            $partDetails[$p] = [
                'ta' => $ta,
                'lr' => $lr,
                'cc' => $cc,
                'gra' => $gra,
                'overallBand' => $overall
            ];
        }
    }

    return $partDetails;
}

function roundToNearestHalf($num) {
    return round($num * 2) / 2;
}
