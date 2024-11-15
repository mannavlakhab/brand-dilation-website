<?php
session_start();
include 'db_connect.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if "Remember Me" cookie is set
    if (isset($_COOKIE['user_id'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      // Optionally, you can re-validate the user with the database here
    } else {
      // If login is optional, do not redirect, just leave session unset
      // If you want to prompt the user to log in, you can show a message or an optional login button
      // Optionally, store the current page in session for redirection after optional login
      $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
      // You can choose to show a notification or a button to log in here
    }
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        #chatbox {
            width: 400px;
            height: 300px;
            border: 1px solid #ccc;
            padding: 10px;
            overflow-y: auto;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        #userMessage {
            width: 300px;
        }
        .user-message, .agent-message {
            margin: 5px 0;
        }
        .user-message {
            color: blue;
        }
        .agent-message {
            color: green;
        }
        button {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <h3>Live Chat</h3>
    <div id="chatbox"></div>
    <input type="text" id="userMessage" placeholder="Type a message" onkeypress="handleKeyPress(event)">
    <input type="hidden" id="userId" value="<?php echo $user_id; ?>"> <!-- Method 1 -->
   
    <button onclick="sendMessage()">Send</button>

    <script>

        const conn = new WebSocket('ws://localhost:8080/chat');
        const chatbox = document.getElementById("chatbox");
        const userMessageInput = document.getElementById("userMessage");
        const user_id = document.getElementById("userId").value;


        conn.onopen = () => {
            console.log("Connection established!");
            fetchChatHistory();
        };

        conn.onmessage = (e) => {
            displayMessage(e.data, 'agent');
        };

        function handleKeyPress(event) {
            if (event.keyCode === 13) {
                sendMessage();
            }
        }

        function sendMessage() {
            const message = userMessageInput.value.trim();
            if (message !== "") {
                conn.send(`${user_id}:${message}`);
                displayMessage(message, 'user');
                userMessageInput.value = ''; // Clear input field
            }
        }

        function displayMessage(message, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = sender === 'user' ? 'user-message' : 'agent-message';
            messageDiv.textContent = `${sender === 'user' ? 'You' : 'Agent'}: ${message}`;
            chatbox.appendChild(messageDiv);
            chatbox.scrollTop = chatbox.scrollHeight; // Scroll to the bottom
        }

        function fetchChatHistory() {
            // Optional: Implement AJAX call to fetch chat history if needed
            // You can create a separate PHP script to return chat history as JSON
        }
    </script>
</body>
</html>
