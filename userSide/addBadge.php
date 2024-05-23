<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['userId'];
    $badgePath = $_POST['badgePath'];

    $xmlFile = '../xml/account.xml'; // Update this path as needed
    $xml = simplexml_load_file($xmlFile);

    foreach ($xml->account as $account) {
        if ((string) $account['id'] === (string) $userId) {
            if (!isset($account->badge)) {
                $account->addChild('badge', $badgePath);
            }
            break;
        }
    }

    if ($xml->asXML($xmlFile)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save XML']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
