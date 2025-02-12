function chatbot(input) {
  let output = "";
  input = input.toLowerCase();

  // General Greetings
  if (input.includes("hello") || input.includes("hi")) {
      output = "Hello, nice to meet you!";
  } else if (input.includes("how are you")) {
      output = "I'm doing fine, thank you for asking.";
  
  // Chatbot Introduction
  } else if (input.includes("what is your name")) {
      output = "My name is Jarvis, I'm a chatbot.";
  } else if (input.includes("what can you do")) {
      output = "I can chat with you, answer your questions, and help you with technical queries!";
  
  // Jokes
  } else if (input.includes("tell me a joke")) {
      output = "Why do programmers prefer dark mode? Because light attracts bugs!";
  
  // Python Related Questions
  } else if (input.includes("what is python")) {
      output = "Python is a high-level, interpreted programming language known for its simplicity and versatility.";
  } else if (input.includes("python libraries")) {
      output = "Some popular Python libraries are NumPy, Pandas, Matplotlib, TensorFlow, and Flask.";

  // JavaScript Related Questions
  } else if (input.includes("what is javascript")) {
      output = "JavaScript is a versatile, lightweight, and powerful programming language primarily used for web development.";
  } else if (input.includes("js frameworks")) {
      output = "Popular JavaScript frameworks include React, Angular, and Vue.js.";
  
  // Web Development
  } else if (input.includes("what is html")) {
      output = "HTML (HyperText Markup Language) is the standard language for creating web pages.";
  } else if (input.includes("what is css")) {
      output = "CSS (Cascading Style Sheets) is used to style and layout web pages.";
  } else if (input.includes("mern stack")) {
      output = "MERN stack is a web development framework comprising MongoDB, Express.js, React, and Node.js.";
  
  // Databases
  } else if (input.includes("what is sql")) {
      output = "SQL (Structured Query Language) is used to communicate with and manage databases.";
  } else if (input.includes("nosql")) {
      output = "NoSQL databases are designed for unstructured data and include MongoDB, Cassandra, and Redis.";

  // Artificial Intelligence and Machine Learning
  } else if (input.includes("what is ai")) {
      output = "AI (Artificial Intelligence) is the simulation of human intelligence in machines.";
  } else if (input.includes("what is machine learning")) {
      output = "Machine Learning is a subset of AI that enables machines to learn from data and improve over time.";
  } else if (input.includes("ai frameworks")) {
      output = "Popular AI frameworks include TensorFlow, PyTorch, and Keras.";

  // Miscellaneous Technical Questions
  } else if (input.includes("what is cloud computing")) {
      output = "Cloud computing is the delivery of computing services over the internet, such as storage, databases, and servers.";
  } else if (input.includes("what is devops")) {
      output = "DevOps is a set of practices that combines software development and IT operations to shorten the development lifecycle.";
  } else if (input.includes("what is version control")) {
      output = "Version control systems like Git help track and manage changes to code over time.";

  // Default Response
  } else {
      output = "Sorry, I don't understand that. Can you try asking something else?";
  }

  return output;
}


  // Display the user message on the chat
  function displayUserMessage(message) {
    let chat = document.getElementById("chat");
    let userMessage = document.createElement("div");
    userMessage.classList.add("message");
    userMessage.classList.add("user");
    let userAvatar = document.createElement("div");
    userAvatar.classList.add("avatar");
    let userText = document.createElement("div");
    userText.classList.add("text");
    userText.innerHTML = message;
    userMessage.appendChild(userAvatar);
    userMessage.appendChild(userText);
    chat.appendChild(userMessage);
    chat.scrollTop = chat.scrollHeight;
  }

  // Display the bot message on the chat
  function displayBotMessage(message) {
    let chat = document.getElementById("chat");
    let botMessage = document.createElement("div");
    botMessage.classList.add("message");
    botMessage.classList.add("bot");
    let botAvatar = document.createElement("div");
    botAvatar.classList.add("avatar");
    let botText = document.createElement("div");
    botText.classList.add("text");
    botText.innerHTML = message;
    botMessage.appendChild(botAvatar);
    botMessage.appendChild(botText);
    chat.appendChild(botMessage);
    chat.scrollTop = chat.scrollHeight;
  }

  // Send the user message and get the bot response
  function sendMessage() {
    let input = document.getElementById("input").value;
    if (input) {
      displayUserMessage(input);
      let output = chatbot(input);
      setTimeout(function() {
        displayBotMessage(output);
      }, 1000);
      document.getElementById("input").value = "";
    }
  }

  // Add a click event listener to the button
  document.getElementById("button").addEventListener("click", sendMessage);

  // Add a keypress event listener to the input
  document.getElementById("input").addEventListener("keypress", function(event) {
    if (event.keyCode == 13) {
      sendMessage();
    }
  });