<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lessonId = $_POST['lessonId'];

    $xmlFile = '../lessons/lesson.xml';
    $uploadDir = '../Badges/';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (file_exists($xmlFile)) {
        if (isset($_FILES['badgeImage']) && $_FILES['badgeImage']['error'] == 0) {
            $badgeImage = $_FILES['badgeImage'];
            $imagePath = $uploadDir . basename($badgeImage['name']);

            if (move_uploaded_file($badgeImage['tmp_name'], $imagePath)) {
                $xml = new DOMDocument();
                $xml->load($xmlFile);

                $xpath = new DOMXPath($xml);
                $xpath->registerNamespace('xhtml', 'http://www.w3.org/1999/xhtml');

                $lesson = $xpath->query("//xhtml:lesson[@id='$lessonId']")->item(0);

                if ($lesson) {
                    // Check if a badge already exists
                    $existingBadge = $xpath->query("xhtml:badge", $lesson)->item(0);
                    if ($existingBadge) {
                        // If a badge exists, replace it with the new badge
                        $lesson->removeChild($existingBadge);
                    }

                    // Create new badge element
                    $badge = $xml->createElement('badge', $imagePath);

                    // Append the new badge to the lesson
                    $lesson->appendChild($badge);

                    // Save the updated XML file
                    $xml->save($xmlFile);

                    echo "Badge added successfully.";
                } else {
                    echo "Lesson not found. Lesson ID: " . htmlspecialchars($lessonId);
                }
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "No image uploaded or upload error.";
        }
    } else {
        echo "XML file not found.";
    }
} else {
    echo "Invalid request.";
}
