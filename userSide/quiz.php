<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .quiz-container {
            margin-top: 50px;
            padding: 0 20px;
        }

        .quiz-form {
            margin-top: 30px;
        }

        .quiz-form .question {
            margin-bottom: 15px;
        }

        .quiz-form .question h5 {
            margin-bottom: 10px;
            font-weight: bold;
        }

        .quiz-form .choices {
            list-style-type: none;
            padding: 0;
            margin-left: 20px;
        }

        .quiz-form .choices li {
            margin: 5px 0;
            padding: 10px;
            border: 1px solid #ccc;
            cursor: move;
            background-color: #f9f9f9;
        }

        .droppable-area {
            min-height: 100px;
            border: 2px dashed #aaa;
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
        }

        .droppable-area.correct {
            background-color: #c8e6c9;
            /* Light green background color */
        }

        .ui-state-highlight {
            background-color: #f0f8ff;
            /* Light blue background color */
        }

        .correct-indicator {
            color: green;
        }

        .wrong-indicator {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container quiz-container">
        <h2 id="quizTitle">Quiz</h2>
        <div class="quiz-form" id="quizForm">
            <form id="quiz">
                <!-- Questions will be dynamically inserted here -->
            </form>
            <button id="submitQuiz" class="btn btn-primary">Submit Quiz</button>
        </div>
    </div>
    <center>
        <button id="goBackButton">Go Back</button>
    </center>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const lessonId = urlParams.get('id');
            const userId = '<?php echo $_SESSION["id"]; ?>'; // PHP to embed the user ID
            const quizTakenKey = `quizTaken_${userId}_${lessonId}`;

            function shuffleArray(array) {
                for (let i = array.length - 1; i > 0; i++) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]];
                }
                return array;
            }

            function displayQuiz(data) {
                const xmlData = $(data);
                const quizContent = xmlData.find(`content[id="${lessonId}"]`);
                if (quizContent.length === 0) {
                    $("#quizForm").html('<p class="text-danger">No quiz found for this lesson.</p>');
                    return;
                }
                const questions = quizContent.find("question");
                questions.each(function(index) {
                    const question = $(this).text();
                    const choices = $(quizContent[index]).find("choice");
                    const questionId = `question${index + 1}`;
                    let choicesHtml = '';

                    choices.each(function(i) {
                        choicesHtml += `
                            <li data-value="${$(this).text()}">${$(this).text()}</li>
                        `;
                    });

                    const questionHtml = `
                        <div class="question">
                            <h5>${index + 1}. ${question}</h5>
                            <ul class="choices" id="sortable-${questionId}">
                                ${choicesHtml}
                            </ul>
                            <div class="droppable-area correct" id="droppable-${questionId}" data-answer="${$(quizContent[index]).find("answer").text()}" data-question-id="${questionId}">
                                <span class="drop-text">Drop Correct Answer Here</span>
                            </div>
                        </div>
                    `;
                    $("#quiz").append(questionHtml);

                    // Make choices draggable and droppable
                    $(`#sortable-${questionId} li`).draggable({
                        revert: "invalid",
                        cursor: "move"
                    });

                    $(`#droppable-${questionId}`).droppable({
                        accept: `#sortable-${questionId} li`,
                        drop: function(event, ui) {
                            $(this).addClass("ui-state-highlight").find(".drop-text").hide();
                            $(this).data("value", ui.draggable.data("value"));
                        }
                    });
                });
            }

            function getScore() {
                let score = 0;
                let totalQuestions = $(".question").length;

                $(".question").each(function() {
                    const questionId = $(this).find('.droppable-area').attr('id').replace('droppable-', '');
                    const correctAnswer = $(this).find('.droppable-area').data('answer');
                    const userAnswer = $(this).find('.droppable-area').data('value');
                    const questionElement = $(this).find('h5');

                    if (userAnswer === correctAnswer) {
                        score += 1;
                        questionElement.prepend(`<span class="correct-indicator">✓ </span>`);
                    } else {
                        questionElement.prepend(`<span class="wrong-indicator">✗ </span>`);
                    }

                });

                return {
                    score,
                    totalQuestions
                };
            }

            $.get("../lessons/quiz.xml", function(data) {
                displayQuiz(data);

                $("#submitQuiz").click(function() {
                    const result = getScore();
                    alert(`You scored ${result.score} out of ${result.totalQuestions}`);



                    Set_user_score(lessonId, "quiz", result.score, "assessmentscore")



                    // Mark the quiz as taken in local storage
                    localStorage.setItem(quizTakenKey, 'true');
                    // Redirect back to the lesson page
                    window.location.href = `viewlesson.php?id=${lessonId}`;
                });
            });

            $("#goBackButton").click(function() {
                history.back(); // Go back to the previous page
            });
        });





        function Set_user_score(lessonNumber, action, quiz, assessment) {

            // yung id ng user is naka session
            var formData = {
                assessment: assessment, // value
                quiz: quiz, // quiz value
                lesson: lessonNumber, // if lesson 1 etc
                action: action // quiz or assessment
            };

            $.ajax({
                type: 'POST',
                url: '../userbackend/updateStatus.php',
                data: formData,
                success: function(response) {
                    $('#message').text(response);
                }
            });
        }
    </script>
</body>

</html>