<?php
$xml = simplexml_load_file('../xml/account.xml');
$lessonCount = 0;

foreach ($xml->account as $account) {
    foreach ($account->children() as $child) {
        if (preg_match('/^lesson(\d+)$/', $child->getName(), $matches)) {
            $lessonNumber = (int)$matches[1];
            if ($lessonNumber > $lessonCount) {
                $lessonCount = $lessonNumber;
            }
        }
    }
}

echo json_encode(['status' => 'success', 'lessonCount' => $lessonCount]);
