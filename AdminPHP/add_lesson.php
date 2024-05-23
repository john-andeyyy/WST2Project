<?php

// Check if lesson_id is set and not empty
    $lessonTitle = $_REQUEST['lesson_id'];

    // Check if lesson_id contains valid characters, for example, only alphanumeric characters
    if (preg_match('/^[a-zA-Z0-9_]+$/', $lessonTitle)) {
        $xml = new DOMDocument();
        $xml->load('../xml/account.xml');

        // Get all <account> elements
        $accounts = $xml->getElementsByTagName('account');

        // Loop through each <account> element
        foreach ($accounts as $account) {
            // Create a new lesson element with the lesson title
            $newlesson = $xml->createElement($lessonTitle);

            // Create child elements for assessment, quiz, and dateCompleted
            $assessment = $xml->createElement('assessment', 'Not Taken');
            $quiz = $xml->createElement('quiz', 'Not Taken');
            $dateCompleted = $xml->createElement('dateCompleted', 'Not Taken');

            // Append child elements to the new lesson element
            $newlesson->appendChild($assessment);
            $newlesson->appendChild($quiz);
            $newlesson->appendChild($dateCompleted);

            // Append the new lesson element to the current account
            $account->appendChild($newlesson);
        }

        // Save the changes to the XML file
        $xml->save('../xml/account.xml');

        echo "Lesson added successfully.";
    } else {
        echo "Invalid characters in lesson_id.";
    }

