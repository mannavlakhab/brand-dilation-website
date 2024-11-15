<?php
session_start();
require_once 'db_connection.php'; // Include your database connection file

// Check if the user is logged in
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

// Get the booking ID from the URL
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch booking details along with customer info
$booking_query = "SELECT b.*, c.first_name, c.last_name, c.email, c.phone_number 
                  FROM bookings b 
                  JOIN customers c ON b.customer_id = c.customer_id 
                  WHERE b.id = ? AND c.user_id = ?"; // Ensure we also check user_id for security

$stmt = $conn->prepare($booking_query);
$stmt->bind_param('ii', $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Check if the booking exists
if ($result->num_rows > 0) {
    $booking_details = $result->fetch_assoc();
    // Display booking details
    echo '<h1>Booking Details</h1>';
    echo '<p>Booking ID: ' . htmlspecialchars($booking_details['id']) . '</p>';
    echo '<p>Status: ' . htmlspecialchars($booking_details['status']) . '</p>';
    echo '<p>Total Price: ' . htmlspecialchars($booking_details['total_price']) . '</p>';
    echo '<p>Tracking ID: ' . htmlspecialchars($booking_details['tracking_id']) . '</p>';
    echo '<p>Customer Name: ' . htmlspecialchars($booking_details['first_name']) . ' ' . htmlspecialchars($booking_details['last_name']) . '</p>';
    echo '<p>Email: ' . htmlspecialchars($booking_details['email']) . '</p>';
    echo '<p>Phone Number: ' . htmlspecialchars($booking_details['phone_number']) . '</p>';
    echo '<p>Created At: ' . htmlspecialchars($booking_details['created_at']) . '</p>';
} else {
    echo '<h2>No booking found.</h2>';
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!-- Optional: Include footer or other components -->
