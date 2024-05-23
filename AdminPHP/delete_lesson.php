<?php

// Load the XML file
$xml = new DOMDocument();
$xml->load('../xml/account.xml');

$lesson = $_REQUEST['lesson'];
// Get all <lesson2> elements
$lesson2Elements = $xml->getElementsByTagName($lesson);

// Iterate over the lesson2 elements in reverse order to avoid issues when removing elements
for ($i = $lesson2Elements->length - 1; $i >= 0; $i--) {
    $lesson2Element = $lesson2Elements->item($i);
    $lesson2Element->parentNode->removeChild($lesson2Element); // Remove the lesson2 element
}

// Save the changes to the XML file
$xml->save('../xml/account.xml');

echo "All lesson2 elements have been deleted successfully.";
