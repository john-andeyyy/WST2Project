<?php
session_start();
$id = $_SESSION['id'];
if (!isset($_SESSION['id'])) {
    echo "Session ID not set.";
    exit;
}
$username = "";
// Load the XML file
$xml = simplexml_load_file('../xml/account.xml');

// Find the account with the id
$accountToDelete = null;
foreach ($xml->account as $account) {
    if ((string)$account['id'] == $id) {
        $accountToDelete = $account;
        $username = (string)$account->username;
        break;
    }
}

// If the account was found, delete it
if ($accountToDelete !== null) {
    unset($accountToDelete[0]);

    // Save the updated XML back to the file
    if ($xml->asXML('../xml/account.xml')) {
        echo "Account with ID $id deleted successfully!";
        activityLog("Account $username deleted successfully!");
    } else {
        echo "Failed to save the updated XML file.";
    }
} else {
    echo "Account with ID $id not found.";
}

function activityLog($message) {
    $currentDateTime = date("m-d-Y h:i:s A");
    $xml = new DOMDocument();
    $xml->load('../xml/ActivityLog.xml');

    $activity = $xml->documentElement;

    $numberOfLogs = $activity->getElementsByTagName('Log')->length;
    $newLogId = $numberOfLogs + 1;

    $log = $xml->createElement('Log');
    $log->setAttribute('id', $newLogId);

    $messageElement = $xml->createElement('message', $message);
    $dateElement = $xml->createElement('date', $currentDateTime);

    $log->appendChild($messageElement);
    $log->appendChild($dateElement);

    $activity->appendChild($log);
    $xml->save('../xml/ActivityLog.xml');
}
?>
