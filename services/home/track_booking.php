<?php
include 'db_connection.php'; // Include your database connection file

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


// Check if tracking_id is set in the URL; if not, set it to null
$tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : null;
$booking = null;
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tracking_id = $_POST['tracking_id'];
    
    // Query to fetch booking information based on tracking_id
    $booking_query = "SELECT b.id, b.status, b.total_price, c.first_name, c.last_name, c.email, c.phone_number
                      FROM bookings b
                      JOIN customers c ON b.customer_id = c.customer_id
                      WHERE b.tracking_id = ?";
    
    $stmt = $conn->prepare($booking_query);
    $stmt->bind_param('s', $tracking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if booking exists
    if ($result->num_rows > 0) {
        $booking = $result->fetch_assoc();

        // Query to fetch ordered services for the booking
        $services_query = "SELECT s.service_name, oi.price 
                           FROM order_items oi 
                           JOIN services s ON oi.service_id = s.id
                           WHERE oi.booking_id = ?";
        $stmt = $conn->prepare($services_query);
        $stmt->bind_param('i', $booking['id']);
        $stmt->execute();
        $services_result = $stmt->get_result();
        $services = $services_result->fetch_all(MYSQLI_ASSOC);
    } else {
        $error = "Invalid tracking ID. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Booking</title>
</head>
<body>
    <h1>Track Your Booking</h1>

    <form action="track_booking.php" method="POST">
        <label for="tracking_id">Enter Tracking ID:</label>
        <input type="text" placeholder="Enter your tracking ID" value="<?php echo htmlspecialchars($tracking_id); ?>" id="tracking_id" name="tracking_id" required>
        <button type="submit">Track</button>
    </form>

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($booking): ?>
        <h3>Booking Details</h3>
        <p><strong>Status:</strong> <?= htmlspecialchars($booking['status']) ?></p>
        <p><strong>Total Price:</strong> $<?= htmlspecialchars($booking['total_price']) ?></p>
        <p><strong>Customer Name:</strong> <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>
        <p><strong>Phone Number:</strong> <?= htmlspecialchars($booking['phone_number']) ?></p>

        <h4>Ordered Services</h4>
        <ul>
            <?php if (!empty($services)) : ?>
                <?php foreach ($services as $service): ?>
                    <li><?= htmlspecialchars($service['service_name']) ?> - $<?= htmlspecialchars($service['price']) ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No services found for this booking.</p>
            <?php endif; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
<a href="../index.php#shop">Return to Home</a>