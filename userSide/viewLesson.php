<?php
session_start();
$id = $_SESSION['id'];


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Lesson</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #31363F;
            /* color: #fff; */
            font-family: Arial, sans-serif;
        }

        .lesson-container {
            margin-top: 50px;
            padding: 0 20px;
            /* color: #EEEEEE; */
        }

        .lesson-card {
            margin-bottom: 20px;
            /* background-color: #404854; */
            border-radius: 8px;
        }

        .link-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-custom {
            color: #FFF;
            background-color: #333844;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
            margin: 10px;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-custom:hover {
            background-color: #556062;
        }
    </style>
</head>

<body>
    <div class="container lesson-container">
        <h2 id="lessonTitle" style="color: white;">Lesson Title</h2>
        <div class="row" id="lessonData">
            <!-- Lesson cards will be inserted here -->
        </div>

    </div>

    <div class="link-container">
        <a id="takeQuiz" href="#" class="btn-custom">Take a Quiz</a>
        <a id="takeAssessment" href="#" class="btn-custom" style="display:none;">Take an Assessment</a>
        <a href="../userSide/lesson.html" class="btn-custom">View all Lessons</a>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const lessonId = urlParams.get('id');
            const userId = '<?php echo $_SESSION["id"]; ?>'; // PHP to embed the user ID
            const quizTakenKey = `quizTaken_${userId}_${lessonId}`;

            function displayLesson(data) {
                const xmlData = $(data);
                const lesson = xmlData.find(`lesson:has(id:contains(${lessonId}))`);

                if (lesson.length > 0) {
                    const lessonTitle = lesson.find("lessontitle").text();
                    $("#lessonTitle").text(lessonTitle);
                    $("#lessonData").empty();

                    lesson.find("content").each(function() {
                        const title = $(this).find("title").text();
                        const description = $(this).find("description").text();

                        const lessonCard = `
                            <div class="col-md-4 lesson-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">${title}</h5>
                                        <p class="card-text">${description}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        $("#lessonData").append(lessonCard);
                    });

                    $("#takeAssessment").attr("href", `assessment.php?id=${lessonId}&userId=${userId}`);
                    $("#takeQuiz").attr("href", `quiz.php?id=${lessonId}`);

                    // Check if the quiz is taken
                    const isQuizTaken = localStorage.getItem(quizTakenKey);
                    if (isQuizTaken === 'true') {
                        $("#takeAssessment").show();
                    } else {
                        $("#takeAssessment").hide();
                    }
                } else {
                    $("#lessonData").html('<p class="text-danger">Lesson not found.</p>');
                }
            }

            $.get("../lessons/lesson.xml", function(data) {
                displayLesson(data);
            });
        });
    </script>
</body>

</html>