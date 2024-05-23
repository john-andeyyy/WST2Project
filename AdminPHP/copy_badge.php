<?php

session_start();
$accountNumber = $_SESSION['id'];
$lessonNumber = $_POST['lessonId'];

// Load XML files
$lessonsXML = simplexml_load_file('../lessons/lesson.xml');
$accountsXML = simplexml_load_file('../xml/account.xml');

// Find the badge URL in the specified lesson
$badgeURL = (string)$lessonsXML->lesson[$lessonNumber - 1]->badge;

// Find the specific account
$account = $accountsXML->xpath("//account[@id='$accountNumber']")[0];

// Check if the lesson is completed
$dateCompleted = (string)$lessonsXML->lesson[$lessonNumber - 1]->dateCompleted;
if ($dateCompleted === "Not Taken") {
    echo "Lesson $lessonNumber has not been taken yet.";
    exit;
}

// Check if <badges> element exists, if not create it
if (!isset($account->badges)) {
    $badges = $account->addChild('badges');
} else {
    $badges = $account->badges;
}

// Check if the badge already exists
$badgeExists = false;
foreach ($badges->badge as $existingBadge) {
    if ((string)$existingBadge === $badgeURL) {
        $badgeExists = true;
        break;
    }
}

if ($badgeExists) {
    echo "Badge already exists in account.";
} else {
    // Add badge URL to <badges> element
    $badge = $badges->addChild('badge', $badgeURL);
    // Save updated accounts XML
    $accountsXML->asXML('../xml/account.xml');
    echo "Badge from lesson $lessonNumber copied successfully to account with ID $accountNumber.";
}
