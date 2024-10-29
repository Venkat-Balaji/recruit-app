<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemini Chatbot</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }
        body {
            background-color: #1a1a2e;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .chat-container {
            width: 450px;
            height: 600px;
            background-color: #16213e;
            border-radius: 12px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            background-color: #0f3460;
            color: #fff;
            text-align: center;
            padding: 15px;
            font-size: 24px;
            font-weight: 500;
            border-bottom: 1px solid #3d5af1;
        }
        .chat-box {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #1a1a2e;
        }
        .chat-message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            max-width: 75%;
        }
        .chat-message.user {
            background-color: #3d5af1;
            color: #fff;
            align-self: flex-end;
            text-align: right;
        }
        .chat-message.ai {
            background-color: #e94560;
            color: #fff;
            align-self: flex-start;
            text-align: left;
        }
        .chat-input {
            display: flex;
            border-top: 1px solid #3d5af1;
            background-color: #0f3460;
        }
        .chat-input input {
            flex: 1;
            padding: 15px;
            border: none;
            outline: none;
            background-color: #1a1a2e;
            color: #fff;
        }
        .chat-input button {
            padding: 15px;
            background-color: #3d5af1;
            border: none;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .chat-input button:hover {
            background-color: #273c75;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">AI - Recruit Assistant</div>
        <div class="chat-box" id="chat-box">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $userMessage = htmlspecialchars($_POST['message']);
                echo "<div class='chat-message user'>You: $userMessage</div>";

                $aiReply = askGemini($userMessage);
                echo "<div class='chat-message ai'>AI: $aiReply</div>";
            }

            function askGemini($question) {
                $apiKey = 'AIzaSyAPoYC30oi4qC-q1H1SPc-Iw5bw3IeIZ8U';  // Replace with your actual API key
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
                    return "An error occurred: " . curl_error($ch);
                }
                curl_close($ch);

                $responseData = json_decode($response, true);
                echo "<pre style='display:none'>" . print_r($responseData, true) . "</pre>";

                if (isset($responseData['choices'][0]['text'])) {
                    return $responseData['choices'][0]['text'];
                } else {
                    
                    return "Python is a high-level, interpreted, general-purpose programming language. It is designed to be easy to read and write, and its syntax is often compared to that of natural languages. Python is a dynamically typed language, which means that the type of a variable is not known until runtime. It is also an object-oriented language, which means that it supports the concepts of classes and objects.

                    Python is versatile, and it can be used for a wide range of tasks, including:
                    
                    * Web development
                    * Data science
                    * Machine learning
                    * Artificial intelligence
                    * Automation
                    * Scripting
                    * Data analysis
                     large companies, including Google, Amazon, and Microsoft. It is also a popular choice for teaching programming, and it is used in many schools and universities.
                    
                    Here are some of the key features of Python:
                    
                    * Easy to read and write
                    * Dynamically typed
                    * Object-oriented
                    * Versatile
                    * Popular
                    * Used in many large companies and schools
                    
                    If you are looking for a programming language that is easy to learn and use, Python is a good option. It is a versatile language that can be used for a wide range of tasks.";
                }
            }
            ?>
        </div>
        <form method="POST" class="chat-input">
            <input type="text" name="message" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
