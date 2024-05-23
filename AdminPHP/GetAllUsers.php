<?php
header("Content-Type: application/xml");

$xmlFile = '../xml/account.xml';
if (file_exists($xmlFile)) {
    echo file_get_contents($xmlFile);
} else {
    echo "<error>File not found</error>";
}
