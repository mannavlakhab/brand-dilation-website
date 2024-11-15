<?php
// whitelist_action.php

// Start the session to access user data
session_start();

// Include your database connection file
require 'db_connect.php'; // Ensure you have the correct path to your DB connection file

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User is not logged in.');
    }

    // Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if "Remember Me" cookie is set
    if (isset($_COOKIE['user_id'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      // Optionally, you can re-validate the user with the database here
  } else {
    // Store the current page in session to redirect after login
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
  }}

    // Get the user ID from session
    $user_id = $_SESSION['user_id'];

    // Validate and sanitize input data
    if (isset($_POST['product_id']) && isset($_POST['action'])) {
        $product_id = intval($_POST['product_id']); // Cast to integer for security
        $action = $_POST['action'];

        // Validate product_id
        if ($product_id <= 0) {
            throw new Exception('Invalid product ID.');
        }

        if ($action === 'add') {
            // Check if the product is already whitelisted
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM whitelist WHERE user_id = ? AND product_id = ?");
            $checkStmt->bind_param("ii", $user_id, $product_id);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) {
                throw new Exception('Product is already in the whitelist.');
            }

            // Prepare SQL statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO whitelist (user_id, product_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $product_id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Product added to whitelist.';
            } else {
                throw new Exception('Failed to add to whitelist: ' . $stmt->error);
            }
        } elseif ($action === 'remove') {
            // Prepare SQL statement to prevent SQL injection
            $stmt = $conn->prepare("DELETE FROM whitelist WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Product removed from whitelist.';
            } else {
                throw new Exception('Failed to remove from whitelist: ' . $stmt->error);
            }
        } else {
            throw new Exception('Invalid action.');
        }
    } else {
        throw new Exception('Invalid input data.');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Close the prepared statement and the database connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

// Return the JSON response
echo json_encode($response);
?>
