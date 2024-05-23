<?php
header("Content-Type: application/json");

$xmlFile = 'lessons.xml';
$xml = simplexml_load_file($xmlFile);

echo json_encode($xml);
?>
