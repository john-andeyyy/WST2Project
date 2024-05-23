<?php
session_start();
$adminId = $_SESSION['id'];
$xml = simplexml_load_file('../xml/account.xml');

$id = $_REQUEST['id'];
$name = $_REQUEST['name'];
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$email = $_REQUEST['email'];

// Check if username and email are unique
$isUnique = true;
foreach ($xml->account as $account) {
    if ($account->username == $username && $account['id'] != $id) {
        $isUnique = false;
        echo "Username is not unique.";
        exit();
    }
    if ($account->email == $email && $account['id'] != $id) {
        $isUnique = false;
        echo "Email is not unique.";
        exit();
    }
}

// If username and email are unique, update the account
foreach ($xml->account as $account) {
    if ($account['id'] == $id) {
        $account->name = $name;
        $account->username = $username;
        $account->password = $password;
        $account->email = $email;

        // Move the activityLog function call outside the if block
        // Correct the string concatenation
        activityLog("Account updated successfully. id with:" . $id . " admin Username: " . $adminId);
        break; // Add break statement after activityLog function call
    }
}
// Save changes
$xml->asXML('../xml/account.xml');
echo "Account updated successfully.";
exit();

function activityLog($message)
{
    $currentDateTime = date("m-d-Y h:i:s A");
    // $message .= " [" . $currentDateTime . "]";

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
