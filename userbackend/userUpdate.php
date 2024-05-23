    <?php
    session_start();
    $id = $_SESSION['id'];

    $xml = simplexml_load_file('../xml/account.xml');

    $name = $_REQUEST['name'];
    $username = $_REQUEST['username'];
    // $password = $_REQUEST['password'];
    $email = $_REQUEST['email'];


    if (!empty($_FILES['profilepic']['name']) && $_FILES['profilepic']['error'] === UPLOAD_ERR_OK) {
        $profilepic_tmp = $_FILES['profilepic']['tmp_name'];
        $profilepic_name = $_FILES['profilepic']['name'];

        // $uploadDir = '../User/profile_pics/profile';
        $uploadDir = '../userSide/profile_pics/img';

        // Check file extension
        $fileExtension = pathinfo($profilepic_name, PATHINFO_EXTENSION);
        $allowedExtensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }

        $profilepic = uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $profilepic;

        if (move_uploaded_file($profilepic_tmp, $uploadFile)) {
            $profilepic = $uploadFile;

            activityLog("Profile Picture updated successfully. id with: " . $id);
        } else {
            echo "Failed to upload file.";
            exit();
        }
    } else {
        // If no file was uploaded, set profilepic to null or any default value
        $profilepic = null; // or set to a default image path
    }

    // Check if username and email are unique
    $isUnique = true;
    foreach ($xml->account as $account) {
        if ($account->username == $username && $account['id'] != $id) {
            $isUnique = false;
            echo "Username is not unique.";
            exit();
        }
        if ($account->email == $email && $account['id'] != $id) {
            $isUnique = false;
            echo "Email is not unique.";
            exit();
        }
    }

    // If username and email are unique, update the account
    foreach ($xml->account as $account) {
        if ($account['id'] == $id) {
            $account->name = $name;
            $account->username = $username;

            // $username = (string)$account->username; // Remove this line

            // $account->password = $password;
            $account->email = $email;
            // Set profilepic only if a file was uploaded
            if ($profilepic !== null) {
                $account->profilepic = $profilepic;
            }

            // Assign $username after updating it
            $username = (string)$account->username;

            break;
        }
    }

    // Save changes
    $xml->asXML('../xml/account.xml');
    echo "Account updated successfully. id with: " . $id;

    activityLog("Account updated successfully. id with: " . $id . " Username: " . $username);

    exit();





    function activityLog($message)
    {
        $currentDateTime = date("m-d-Y h:i:s A");
        // $message .= " [" . $currentDateTime . "]";

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