<?php
// Get form data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';

// Load the XML file
$xml = simplexml_load_file('../xml/account.xml');

// Check if username already exists
$userExists = $xml->xpath("//account[username='$username']");

// Check if email already exists
$emailExists = $xml->xpath("//account[email='$email']");

$response = array();

if (!empty($userExists)) {
    $response['status'] = 'error';
    $response['message'] = 'Username already exists. Please choose a different one.';
} elseif (!empty($emailExists)) {
    $response['status'] = 'error';
    $response['message'] = 'Email already exists. Please use a different one.';
} else {
    // Generate a unique ID for the new account
    $id = 1; // Initial ID
    foreach ($xml->account as $account) {
        $id = max($id, (int)$account['id']) + 1;
    }

    // Create a new <account> element
    $account = $xml->addChild('account');

    // Add ID attribute
    $account->addAttribute('id', $id);

    // Add child elements for username, password, name, email, and profilepic
    $account->addChild('username', $username);
    $account->addChild('password', $password);
    $account->addChild('name', $name);
    $account->addChild('email', $email);
    $account->addChild('profilepic', "../userSide/profile_pics/default.png");

    // Add <badges> element
    $badges = $account->addChild('badges');

    // Get the maximum lessons count across all accounts
    $lessonsCount = 0;
    foreach ($xml->account as $existingAccount) {
        $accountLessons = $existingAccount->xpath("*[starts-with(name(), 'lesson')]");
        if ($accountLessons !== false) {
            $accountLessonsCount = count($accountLessons);
            $lessonsCount = max($lessonsCount, $accountLessonsCount);
        }
    }

    // Add lessons to the new account
    for ($i = 1; $i <= $lessonsCount; $i++) {
        $newLesson = $account->addChild('lesson' . $i);
        $newLesson->addChild('assessment', 'Not Taken');
        $newLesson->addChild('quiz', 'Not Taken');
        $newLesson->addChild('dateCompleted', 'Not Taken');
    }

    // Log the creation of the new account
    activityLog("New Account with username of: " . $username);

    // Save the XML file
    $xml->asXML('../xml/account.xml');

    $response['status'] = 'success';
    $response['message'] = 'Account created successfully!';
}

echo json_encode($response);

function activityLog($message)
{
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
