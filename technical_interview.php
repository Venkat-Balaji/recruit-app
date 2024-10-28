<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Interview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .question {
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Technical Interview</h2>
    <form method="POST" action="technical_interview.php">
        <div>
            <label for="skill">Enter your technical skill:</label>
            <input type="text" id="skill" name="skill" required>
        </div>
        <button type="submit" name="generate">Generate Questions</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
        $skill = htmlspecialchars($_POST['skill']);

        // Example questions based on the entered skill
        $questions = [
            "Explain a recent project where you used $skill. What challenges did you face?",
            "How would you optimize a program written in $skill to improve performance?"
        ];
        
        echo '<form method="POST" action="technical_interview.php">';
        foreach ($questions as $index => $question) {
            echo '<div class="question">';
            echo "<label>Question " . ($index + 1) . ": $question</label>";
            echo '<textarea name="answer' . $index . '" required></textarea>';
            echo '</div>';
        }
        echo '<button type="submit" name="submit_answers">Submit Answers</button>';
        echo '</form>';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_answers'])) {
        $answer1 = htmlspecialchars($_POST['answer0']);
        $answer2 = htmlspecialchars($_POST['answer1']);

        if (!empty($answer1) && !empty($answer2)) {
            echo '<h3>Thank you for your answers!</h3>';
            echo '<p><strong>Answer 1:</strong> ' . $answer1 . '</p>';
            echo '<p><strong>Answer 2:</strong> ' . $answer2 . '</p>';
        } else {
            echo '<p style="color: red;">Please answer both questions.</p>';
        }
    }
    ?>
</div>

</body>
</html>