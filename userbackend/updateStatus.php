<?php
session_start();
if (isset($_SESSION['id']) && isset($_POST['assessment']) && isset($_POST['quiz']) && isset($_POST['lesson']) && isset($_POST['action'])) {
    $accountId = $_SESSION['id'];
    $assessmentScore = $_POST['assessment'];
    $quizScore = $_POST['quiz'];
    $lessonNumber = $_POST['lesson'];
    $action = $_POST['action'];

    $xmlFilePath = '../xml/account.xml';

    $xml = simplexml_load_file($xmlFilePath);

    $account = $xml->xpath("//account[@id='$accountId']");
    if (count($account) > 0) {
        $username = (string) $account[0]->username;
        $lessonNode = "lesson$lessonNumber";
        if (isset($account[0]->$lessonNode)) {
            if ($action === 'quiz') {
                $account[0]->$lessonNode->quiz = $quizScore;

                if (!isset($account[0]->$lessonNode->dateCompleted)) {
                    $account[0]->$lessonNode->addChild('dateCompleted', 'Not Taken');
                }
                echo "Quiz update successful for $username";
                activityLog("The User: " . $username . " completed the quiz for Lesson $lessonNumber.");
            } elseif ($action === 'assessment') {
                $account[0]->$lessonNode->assessment = $assessmentScore;

                if (!isset($account[0]->$lessonNode->dateCompleted)) {
                    $account[0]->$lessonNode->addChild('dateCompleted', 'Not Taken');
                }

                $account[0]->$lessonNode->dateCompleted = date("Y-m-d");
                echo "Assessment update successful for $username";
                activityLog("The User: " . $username . " completed the assessment for Lesson $lessonNumber.");
            } else {
                echo "Invalid action";
            }

            $xml->asXML($xmlFilePath);
        } else {
            echo "Lesson not found";
        }
    } else {
        echo "Account not found";
    }
} else {
    echo "Incomplete data";
}

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
