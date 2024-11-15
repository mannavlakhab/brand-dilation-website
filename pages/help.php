<?php
session_start();
include '../db_connect.php'; // Include your database connection

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


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the required POST fields are set
    if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['message'])) {
        echo "Form fields are missing.";
        exit;
    }

    // Sanitize and validate input
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));

    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please fill in all fields correctly.";
        exit;
    }

    // Prepare and execute SQL query
    $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";

    if (mysqli_query($conn, $sql)) {
        echo "Message sent successfully!";
    } else {
        echo "Failed to send message: " . mysqli_error($conn);
    }
}
?>
