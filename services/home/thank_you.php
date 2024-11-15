<?php
session_start();
$tracking_id = $_GET['tracking_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the order (you can save order details in the database here)

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


    // Clear cart
    unset($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thank You</title>
</head>
<body>
    <h1>Thank You for Your Order!</h1>
    <p>Your order has been placed successfully.</p>
    copy this id(It was services tracking id) = <?php echo $tracking_id;?>
    <a href="track_booking.php?tracking_id=<?php echo $tracking_id;?>" target="_blank" rel="noopener noreferrer">Track Booking</a>
    <br><br>
    <a href="../index.php#shop">Return to Home</a>
</body>
</html>
