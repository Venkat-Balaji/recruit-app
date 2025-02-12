<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = $_POST['question'] ?? '';

    if (!empty($question)) {
        echo askGemini($question);
    } else {
        echo "Please provide a valid question.";
    }
    exit; // Stop further HTML rendering for AJAX requests
}

function askGemini($question) {
    $apiKey = 'AIzaSyAPoYC30oi4qC-q1H1SPc-Iw5bw3IeIZ8U'; // Replace with your Gemini API key
    $apiUrl = 'https://api.generativeai.googleapis.com/v1/models/gemini-pro:generateText';

    $data = json_encode([
        'prompt' => $question,
        'temperature' => 0.7,
        'max_tokens' => 100
    ]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return "cURL Error: " . curl_error($ch);
    }
    curl_close($ch);

    $responseData = json_decode($response, true);
    if (isset($responseData['choices'][0]['text'])) {
        return $responseData['choices'][0]['text'];
    } elseif (isset($responseData['error'])) {
        return "API Error: " . $responseData['error']['message'];
    } else {
        return "No valid response from Gemini AI.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Recruit Assistant</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #1a1a2e;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .chat-container {
            width: 50%;
            max-width: 500px;
            height: 80%;
            background-color: #0f3460;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .chat-header {
            padding: 15px;
            text-align: center;
            background-color: #16213e;
            font-size: 1.5em;
            font-weight: bold;
        }
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
        }
        .chat-messages p {
            margin: 5px 0;
            padding: 10px;
            background-color: #1a1a2e;
            border-radius: 5px;
            max-width: 80%;
            word-wrap: break-word;
        }
        .chat-messages .user {
            align-self: flex-end;
            background-color: #0f3460;
        }
        .chat-messages .ai {
            align-self: flex-start;
            background-color: #16213e;
        }
        .chat-input {
            display: flex;
            padding: 10px;
            background-color: #16213e;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
            font-size: 1em;
        }
        .chat-input button {
            padding: 10px 20px;
            border: none;
            background-color: #0f3460;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">AI Recruit Assistant</div>
        <div class="chat-messages" id="chatMessages"></div>
        <div class="chat-input">
            <input type="text" id="userInput" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            const userInput = document.getElementById("userInput");
            const chatMessages = document.getElementById("chatMessages");

            if (userInput.value.trim() !== "") {
                // Append user message
                const userMessage = document.createElement("p");
                userMessage.className = "user";
                userMessage.textContent = userInput.value;
                chatMessages.appendChild(userMessage);

                // Scroll to the bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Fetch AI response
                fetch("", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `question=${encodeURIComponent(userInput.value)}`
                })
                .then(response => response.text())
                .then(data => {
                    const aiMessage = document.createElement("p");
                    aiMessage.className = "ai";
                    aiMessage.textContent = data;
                    chatMessages.appendChild(aiMessage);

                    // Scroll to the bottom
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                })
                .catch(error => {
                    const errorMessage = document.createElement("p");
                    errorMessage.className = "ai";
                    errorMessage.textContent = "Error: Unable to fetch response.";
                    chatMessages.appendChild(errorMessage);
                });

                // Clear input field
                userInput.value = "";
            }
        }
    </script>
</body>
</html>
