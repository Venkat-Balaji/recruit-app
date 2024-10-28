<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['self_intro'])) {
        $selfIntro = $_POST['self_intro'];
        // Set isTechnical to false for non-technical interviews
        $response = askGemini($selfIntro, false);
    }

    if (isset($_POST['answers'])) {
        $answers = $_POST['answers'];
        $answerResponse = processAnswers($answers);
    }
}

function askGemini($question, $isTechnical = true) {
    $url = 'http://localhost:5000/ask';  // Python Flask server endpoint
    $data = json_encode(['type' => 'interview', 'input' => $question, 'is_technical' => $isTechnical]);

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        return "Error contacting AI service.";
    }

    $response = json_decode($result, true);
    return isset($response['questions']) ? $response['questions'] : ["No valid response from the AI."];
}

function processAnswers($answers) {
    // Logic to handle the submitted answers
    $submittedAnswers = '';
    foreach ($answers as $question => $answer) {
        $submittedAnswers .= "<strong>" . htmlspecialchars($question) . ":</strong> " . htmlspecialchars($answer) . "<br>";
    }
    return "Your answers have been submitted:<br>$submittedAnswers";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Non-Technical Interview</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Non-Technical Interview</h1>
        <form method="POST">
            <label for="self_intro">Tell me a brief about yourself:</label>
            <textarea id="self_intro" name="self_intro" rows="5" required></textarea>
            <button type="submit">Generate Questions</button>
        </form>

        <?php if (isset($response) && is_array($response)): ?>
            <h2>Questions:</h2>
            <form method="POST">
                <?php foreach ($response as $index => $question): ?>
                    <div class="question-block">
                        <label for="answer_<?php echo $index; ?>"><?php echo htmlspecialchars($question); ?></label>
                        <textarea id="answer_<?php echo $index; ?>" name="answers[<?php echo htmlspecialchars($question); ?>]" rows="3" required></textarea>
                    </div>
                <?php endforeach; ?>
                <button type="submit">Submit Answers</button>
            </form>
        <?php endif; ?>

        <?php if (isset($answerResponse)): ?>
            <h3><?php echo $answerResponse; ?></h3>
        <?php endif; ?>
    </div>
</body>
</html>
