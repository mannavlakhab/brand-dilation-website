<?php
session_start();
require_once 'db_connection.php'; // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

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



// Fetch bookings for the logged-in user
$booking_query = "SELECT b.id, b.status, b.total_price, b.tracking_id, b.created_at 
                  FROM bookings b 
                  JOIN customers c ON b.customer_id = c.customer_id  -- Use the correct column name
                  ORDER BY b.created_at DESC";
$categ = mysqli_query($conn, $booking_query);

// Check if there are any bookings
if ($result->num_rows > 0) {
    echo '<h1>Your Bookings</h1>';
    echo '<table border="1">
            <tr>
                <th>Booking ID</th>
                <th>Status</th>
                <th>Total Price</th>
                <th>Tracking ID</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>';

    // Display each booking
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . htmlspecialchars($row['id']) . '</td>
                <td>' . htmlspecialchars($row['status']) . '</td>
                <td>' . htmlspecialchars($row['total_price']) . '</td>
                <td>' . htmlspecialchars($row['tracking_id']) . '</td>
                <td>' . htmlspecialchars($row['created_at']) . '</td>
                <td>
                    <a href="view_booking_details.php?id=' . htmlspecialchars($row['id']) . '">View Details</a>
                    <a href="cancel_booking.php?id=' . htmlspecialchars($row['id']) . '">Cancel Booking</a>
                </td>
              </tr>';
    }

    echo '</table>';
} else {
    echo '<h2>No bookings found.</h2>';
}

// Close the database connection
$stmt->close();
$conn->close();
?>

<!-- Optional: Include footer or other components -->
