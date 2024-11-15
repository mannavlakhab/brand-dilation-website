<?php
session_start();
require_once 'db_connect.php';

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
// Function to sanitize user inputs
function sanitize_input($data) {
    return htmlspecialchars(strip_tags($data));
}

// Check if order_id is provided
if (isset($_GET['order_id'])) {
    $order_id = sanitize_input($_GET['order_id']);

    // Validate order_id to be a valid integer
    if (filter_var($order_id, FILTER_VALIDATE_INT)) {
        // Prepare SQL query to update order status
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'canceled', payment_status = 'canceled' WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);

        if ($stmt->execute()) {
            echo '
            <div class="ab-o-oa" aria-hidden="true">
                    <div class="ZAnhre">
                    <img class="wF0Mmb" src="../assets/cancel.svg" width="300px" height="300px" alt=""></div>
                    <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">Order has been cancelled successfully.</div>
                    <div class="ab-o-oa-qc-r">You will be redirected to the previous page in <br><span id="countdown">5</span> seconds.</div></div>
                </div>
                <style>
                    
.ab-o-oa{
    display: flex;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    align-items: center;
    width: 100%;
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
    font-family: Montserrat, sans-serif;

}
.ab-o-oa-r{
    display: contents;
}
.ab-o-oa-qc-V{
    font-weight :800;

}
.ab-o-oa-qc-r{
    font-weight :normal;

}</style>
            <script>
                var countdown = 5;
                var countdownElement = document.getElementById("countdown");
                var interval = setInterval(function() {
                    countdown--;
                    countdownElement.textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(interval);
                        window.location.href = "../profile?page=orders"; // Redirect to home page
                    }
                }, 1000);
            </script>
            ';
        } else {
            echo "Error cancelling order: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Invalid order ID.";
    }
} else {
    echo "Order ID not provided.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order cancelled</title>
</head>
<body>
    
</body>
</html>