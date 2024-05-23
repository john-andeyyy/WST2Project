<?php
session_start();

// Load XML file
$xml = simplexml_load_file('../xml/account.xml');

// Retrieve the ID of the current user from the session
$id = $_SESSION['id'];

// Initialize the response array
$userData = [];

// Search for the user with the specified ID
foreach ($xml->account as $account) {
    if ((int)$account['id'] === (int)$id) {
        // Collect basic user information
        $userData = [
            'id' => (int)$account['id'],
            'name' => (string)$account->name,
            'username' => (string)$account->username,
            'email' => (string)$account->email,
            'profilepic' => (string)$account->profilepic,
            'badges' => [],
            'thirdLatestDate' => 'No third date available' // Default message
        ];

        // Collect badges
        foreach ($account->badges->badge as $badge) {
            $userData['badges'][] = (string)$badge;
        }

        // Collect and sort dates
        $dates = [];
        foreach ($xml->xpath("//account[@id='$id']/*/dateCompleted") as $date) { // This selects dateCompleted from any child of account
            if ((string)$date !== 'Not Taken') { // Ensuring the date is valid
                $dates[] = strtotime((string)$date);
            }
        }

        // Sort dates in descending order
        rsort($dates);

        // Get the third latest date if possible
        if (isset($dates[2])) {
            $userData['thirdLatestDate'] = date('Y-m-d', $dates[2]);
        }

        break; // Exit the loop once the specific user is found
    }
}

// Return user data as JSON
echo json_encode($userData);
