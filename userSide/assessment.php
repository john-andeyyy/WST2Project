<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Assessment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .assessment-container {
            margin-top: 50px;
            padding: 0 20px;
        }

        .assessment-form {
            margin-top: 30px;
        }

        .assessment-form .question {
            margin-bottom: 15px;
        }

        .assessment-form .choices {
            margin-bottom: 20px;
        }

        .error-message {
            color: red;
            display: none;
        }

        .correct::before {
            content: "✔️ ";
            color: green;
        }

        .incorrect::before {
            content: "❌ ";
            color: red;
        }
    </style>
</head>

<body>
    <div class="container assessment-container">
        <h2 id="assessmentTitle">Assessment</h2>
        <div class="assessment-form" id="assessmentForm">
            <form id="assessment">
                <!-- Questions will be dynamically inserted here -->
            </form>
            <button id="submitAssessment" class="btn btn-primary">Submit Assessment</button>
            <p class="error-message" id="errorMessage">Please answer all questions before submitting.</p>
        </div>
    </div>
    <center>
        <button id="goBackButton">Go Back</button>
    </center>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const lessonId = urlParams.get('id'); // id of lesson
            const userId = urlParams.get('userId'); // Extract the userId from the URL
            let selectedAssessments = [];
            let isSubmitted = false;

            function shuffle(array) {
                for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]];
                }
                return array;
            }

            function displayAssessment(data) {
                const xmlData = $(data);
                const assessmentContent = xmlData.find(`content[id="${lessonId}"]`);
                selectedAssessments = shuffle(assessmentContent.toArray()).slice(0, 10);

                if (selectedAssessments.length > 0) {
                    $("#assessment").empty();
                    $(selectedAssessments).each(function(index) {
                        const question = $(this).find("question").text();
                        const choices = $(this).find("choices choice");
                        const questionId = `question${index + 1}`;
                        let choicesHtml = '';

                        choices.each(function(i) {
                            choicesHtml += `
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="${questionId}" id="${questionId}_${i}" value="${$(this).text()}">
                                    <label class="form-check-label" for="${questionId}_${i}">${$(this).text()}</label>
                                </div>
                            `;
                        });

                        const questionHtml = `
                            <div class="question" id="questionContainer${index}">
                                <h5>${question}</h5>
                                <div class="choices">
                                    ${choicesHtml}
                                </div>
                            </div>
                        `;
                        $("#assessment").append(questionHtml);
                    });
                } else {
                    $("#assessmentForm").html('<p class="text-danger">No assessment found for this lesson.</p>');
                }
            }

            function getScore() {
                let score = 0;
                let totalQuestions = 0;

                $(selectedAssessments).each(function(index) {
                    const questionId = `question${index + 1}`;
                    const correctAnswer = $(this).find("answer").text();
                    const userAnswer = $(`input[name="${questionId}"]:checked`).val();

                    if (userAnswer === correctAnswer) {
                        score += 1;
                        $(`#questionContainer${index}`).addClass('correct');
                    } else {
                        $(`#questionContainer${index}`).addClass('incorrect');
                    }
                    totalQuestions += 1;
                });

                return {
                    score,
                    totalQuestions
                };
            }

            function validateForm() {
                let allAnswered = true;
                $(selectedAssessments).each(function(index) {
                    const questionId = `question${index + 1}`;
                    if (!$(`input[name="${questionId}"]:checked`).val()) {
                        allAnswered = false;
                        return false; // Exit the loop early
                    }
                });
                return allAnswered;
            }

            $.get("../lessons/assessment.xml", function(data) {
                displayAssessment(data);

                $("#submitAssessment").click(function() {
                    if (isSubmitted) {
                        return; // Prevent multiple submissions
                    }

                    if (!validateForm()) {
                        $("#errorMessage").show();
                    } else {
                        $("#errorMessage").hide();
                        const result = getScore();
                        alert(`You scored ${result.score} out of ${result.totalQuestions}`);



                        Set_user_score(lessonId, "assessment", "quiz sc", result.score)









                        if (result.score >= 10) {
                            $.post('addBadge.php', {
                                userId: userId,
                                badgePath: '../Badges/lesson1.png'
                            }, function(response) {
                                const res = JSON.parse(response);
                                if (res.success) {
                                    alert('Badge added successfully!');
                                    Set_user_score(lessonId, "assessment", "quiz sc", result.score)

                                } else {
                                    alert('Failed to add badge.');
                                }
                            });
                        }



                        isSubmitted = true; // Mark the form as submitted
                    }
                });
            });

            $("#goBackButton").click(function() {
                history.back();
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






                // add badge into accoubt xml
                $.ajax({
                    type: "POST",
                    url: "../AdminPHP/copy_badge.php",
                    data: {
                        lessonId: lessonId,
                        // accountNumber: accountNumber
                    },
                    success: function(response) {
                        // alert(response);
                    }
                });




            }





        });
    </script>
</body>

</html>