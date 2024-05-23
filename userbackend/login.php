<?php
// userbackend/login.php

session_start();

// XML file for users
$usersXML = simplexml_load_file('../xml/account.xml');

// XML file for admins
$adminsXML = simplexml_load_file('../xml/Admin.xml');

if (isset($_POST['emailuser'], $_POST['password'])) {
    $usernameOrEmail = $_POST['emailuser'];
    $password = $_POST['password'];

    // Check admin credentials
    // Check admin credentials
    foreach ($adminsXML->admin as $admin) {
        $adminUsername = (string) $admin->username;
        $adminEmail = (string) $admin->email;
        $adminPassword = (string) $admin->password;
        $adminId = (int) $admin['id']; // Retrieve the ID attribute

        if (($usernameOrEmail == $adminUsername || $usernameOrEmail == $adminEmail) && $password == $adminPassword) {
            echo "admin";
            $_SESSION['id'] = $adminId; 
            activityLog("Admin " . $adminUsername . " logged in.");

            exit;
        }
    }


    // Check user credentials
    $isValidUserLogin = false;

    foreach ($usersXML->account as $user) {
        $userUsername = (string) $user->username;
        $userEmail = (string) $user->email;
        $userPassword = (string) $user->password;

        if (($usernameOrEmail == $userUsername || $usernameOrEmail == $userEmail) && $password == $userPassword) {
            $isValidUserLogin = true;
            echo "user";
            $_SESSION['id'] = (int) $user['id'];
            
            activityLog("user " . $userUsername . " logged in.");


            exit; // Exit script after successful user login
        }
    }

    if (!$isValidUserLogin) {
        echo "Invalid username/email or password for user.";
    }
} else {
    echo "Please provide username/email and password.";
}



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