<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $xmlData = $_POST['xmlData'];

    if ($xmlData) {
        file_put_contents('assessment.xml', $xmlData);
        echo "XML data saved successfully.";
    } else {
        http_response_code(400);
        echo "Invalid XML data.";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}
?>
