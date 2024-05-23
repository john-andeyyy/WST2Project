<?php
session_start();
$id = $_SESSION['id'];

$xml = simplexml_load_file('../xml/account.xml');

// Check if the password field is filled
if (!empty($_REQUEST['OldPassword'])) {
    $OldPassword = $_REQUEST['OldPassword'];

    $newPassword = $_REQUEST['newPassword'];
    $confirmPassword = $_REQUEST['confirmPassword'];

    foreach ($xml->account as $account) {
        if ($account['id'] == $id) {
            if ($account->password == $OldPassword) {
                if ($newPassword == $confirmPassword) {
                    $account->password = $newPassword;
                    $xml->asXML('../xml/account.xml');

                    $username = (string)$account->username;
                    
                    echo "Password updated successfully. id with: " . $id ;

                    activityLog("Password updated successfully. Username with: " . $username);
                }else{
                    echo "The did not match to the new and the confirm password";
                    break;
                }
            } else {
                echo "Incorrect Password";
                break;
            }


        }
    }

    // Save changes
} else {
    echo "Password field is empty.";
}
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