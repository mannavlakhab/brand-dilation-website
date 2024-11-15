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


// Check if form data is set
if (isset($_POST['service-type']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['details']) && isset($_POST['name']) && isset($_POST['phone'])) {
    // Get the form data
    $service_type = $_POST['service-type'];
    $date = $_POST['date'];    
    $time = $_POST['time'];
    $details = $_POST['details'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO bookings (service_type, preferred_date, preferred_time, additional_details, user_name, user_phone) VALUES ( ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $service_type, $date, $time, $details, $name, $phone);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Booking successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Form data not received.";
}
?>