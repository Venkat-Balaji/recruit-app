<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Interview</title>
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            animation: fadeIn 0.5s ease-in-out;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }

        form div {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .question {
            margin-bottom: 20px;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        .thank-you {
            text-align: center;
            margin-top: 20px;
            color: #28a745;
            font-weight: bold;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            button {
                font-size: 0.9rem;
                padding: 8px 16px;
            }

            input[type="text"], textarea {
                font-size: 0.9rem;
            }
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
            "Can you generate a pattern of a diamond in $skill, made of numbers such that the numbers in each row increase towards the center of the diamond and decrease symmetrically, with the size of the diamond based on a user-provided odd integer n?",
            "Explain a recent project where you used $skill. What challenges did you face?"
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
            echo '<div class="thank-you">Thank you for your answers!</div>';
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
