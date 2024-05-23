<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #1a1a1a;
            color: #fff;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .menu {
            background-color: #333;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dropdown {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }

        .dropdown button {
            width: 100%;
            height: 50px;
            background-color: #555;
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #444;
            min-width: 100%;
            z-index: 1;
            text-align: left;
            border-radius: 5px;
            overflow: hidden;
        }

        .dropdown-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #2ebaae;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        button {
            width: 100%;
            height: 50px;
            padding: 0;
            background-color: #555;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin: 10px 0;
        }

        button:hover {
            background-color: #777;
        }

        .logout-button {
            margin-top: 20px;
        }

        .link-button {
            width: 100%;
            padding: 0;
            border: none;
            background: none;
            margin: 10px 0;
        }

        .link-button button {
            width: 100%;
            height: 50px;
            margin: 0;
        }

        h1 {
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="menu">
        <h1>Welcome User!</h1>
        <div class="dropdown">

            <a href='lesson.html'> <button>Lesson</button></a>
            <!-- <div class="dropdown-content"> -->
            <!-- <a href='lesson.html'>View Lesson</a> -->
            <!-- </div> -->
        </div>
        <a href="UserProfile.html" class="link-button"><button>Profile</button></a>
        <button class="logout-button" onclick="logout()">Logout</button>
    </div>

    <script>
        function logout() {
            if (confirm("Are you sure you want to logout?")) {
                var url = '../index.html'; // Make sure these paths are correct
                var url2 = '../LandingPage/index.html';
                window.location.href = url2;
            }
        }
    </script>
</body>

</html>