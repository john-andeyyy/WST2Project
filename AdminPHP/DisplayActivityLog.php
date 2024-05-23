<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Ensure the response is JSON

$xmlFile = '../xml/ActivityLog.xml';

if (!file_exists($xmlFile)) {
    echo json_encode(['error' => 'XML file not found.']);
    exit;
}

$xml = simplexml_load_file($xmlFile);
if ($xml === false) {
    echo json_encode(['error' => 'Failed to load XML file.']);
    exit;
}

$logs = [];
foreach ($xml->Log as $log) {
    $id = (string) $log['id'];
    $message = (string) $log->message;
    $date = (string) $log->date;
    $logs[] = [
        'id' => $id,
        'message' => $message,
        'date' => $date
    ];
}

$filter = $_POST['filter'] ?? '';
$startDate = $_POST['startDate'] ?? '';
$endDate = $_POST['endDate'] ?? '';

if ($filter && $filter !== 'all') {
    $filteredLogs = [];
    $now = new DateTime();

    foreach ($logs as $log) {
        $logDate = DateTime::createFromFormat('m-d-Y h:i:s A', $log['date']);

        if ($logDate === false) {
            continue;
        }

        switch ($filter) {
            case 'day':
                if ($logDate->format('Y-m-d') === $now->format('Y-m-d')) {
                    $filteredLogs[] = $log;
                }
                break;
            case 'week':
                $weekStart = (clone $now)->modify('this week');
                $weekEnd = (clone $weekStart)->modify('+6 days');
                if ($logDate >= $weekStart && $logDate <= $weekEnd) {
                    $filteredLogs[] = $log;
                }
                break;
            case 'month':
                if ($logDate->format('Y-m') === $now->format('Y-m')) {
                    $filteredLogs[] = $log;
                }
                break;
            case 'year':
                if ($logDate->format('Y') === $now->format('Y')) {
                    $filteredLogs[] = $log;
                }
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $start = DateTime::createFromFormat('Y-m-d', $startDate);
                    $end = DateTime::createFromFormat('Y-m-d', $endDate);
                    if ($start !== false && $end !== false && $logDate >= $start && $logDate <= $end) {
                        $filteredLogs[] = $log;
                    }
                }
                break;
        }
    }
    echo json_encode($filteredLogs);
} else {
    echo json_encode($logs);
}
