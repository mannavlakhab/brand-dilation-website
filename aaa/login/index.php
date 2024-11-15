<?php
// Start the session
session_start();

// Database configuration
$servername = "localhost"; // Change this if your database server is different
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "gt"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_username = $_POST['login-username'];
    $login_password = $_POST['login-password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $login_username);

    // Execute the statement
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($login_password, $hashed_password)) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $login_username;

            // Redirect to home.php
            header("Location: ../home");
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with that username!";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
        <div class="p-8 rounded-lg w-full max-w-md">
            <div class="flex justify-center mb-6">
                <img alt="Company logo with a placeholder image of 100x100 pixels" height="100" src="https://storage.googleapis.com/a1aa/image/K0ir9v3XssawOJrQB4HPfA2ftXASHfJB52C5NfdxXhNAw89OB.jpg" width="100"/>
            </div>
            <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Login</h2>
            <form class="space-y-6" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="login-username">Username</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="login-username" name="login-username" required="" type="text"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="login-password">Password</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="login-password" name="login-password" required="" type="password"/>
                </div>
                <div class="flex justify-end">
                    <a class="text-sm text-indigo-600 hover:text-indigo-500" href="../forget">Forgot Password?</a>
                </div>
                <div>
                    <button class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" type="submit">Login</button>
                </div>
            </form>
            <p class="mt-6 text-center text-sm text-gray-600">Don't have an account? <a class="font-medium text-indigo-600 hover:text-indigo-500" href="../signup">Sign Up</a></p>
        </div>
    </div>
</body>
</html>