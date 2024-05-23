<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $xmlData = $_POST['xmlData'];
    file_put_contents('lesson.xml', $xmlData);
}
?>
