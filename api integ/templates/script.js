// Replace with your Gemini API key
const GEMINI_API_KEY = 'AIzaSyAPoYC30oi4qC-q1H1SPc-Iw5bw3IeIZ8U';

document.getElementById("send-btn").addEventListener("click", () => {
    const userInput = document.getElementById("user-input").value.trim();
    if (userInput === "") return;

    // Display user message
    displayMessage(userInput, "user");

    // Call Gemini API to get the response
    fetchResponseFromGemini(userInput);
    document.getElementById("user-input").value = "";
});

function displayMessage(message, sender) {
    const chatBox = document.getElementById("chat-box");
    const messageElement = document.createElement("div");
    messageElement.classList.add("message", sender);
    messageElement.textContent = message;
    chatBox.appendChild(messageElement);
    chatBox.scrollTop = chatBox.scrollHeight; // Auto-scroll to the latest message
}

async function fetchResponseFromGemini(question) {
    try {
        const response = await fetch("https://api.palm-api.google.com/v1beta2/generateText", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${GEMINI_API_KEY}`,
            },
            body: JSON.stringify({
                prompt: question,
                model: "text-bison-001",
                temperature: 0.7,
                candidate_count: 1,
            }),
        });

        const data = await response.json();
        if (data && data.candidates && data.candidates[0].output) {
            displayMessage(data.candidates[0].output, "bot");
        } else {
            displayMessage("Sorry, I couldn't process your request.", "bot");
        }
    } catch (error) {
        displayMessage(`Error: ${error.message}`, "bot");
    }
}
