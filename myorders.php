<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
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

$user_id = $_SESSION['user_id'];

// Fetch user information
$user_query = $conn->prepare("SELECT username, email, first_name, last_name, phone_number, address_1, address_2 FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_query->bind_result($username, $email, $first_name, $last_name, $phone_number, $address_1, $address_2);
$user_query->fetch();
$user_query->close();

// Fetch user orders
$order_query = $conn->prepare("
    SELECT 
    O.order_status, 
    O.order_id, 
    O.order_date, 
    O.delivery_date, 
    C.phone_number, 
    O.shipping_address, 
    O.total_price, 
    O.shipping_cost, 
    O.payment_method, 
    O.payment_details, 
    O.payment_status, 
    O.tracking_id, 
    P.model AS product_name, 
    V.variation_value, 
    OI.product_quantity,
    OI.product_attributes,
    P.image_main
FROM 
    Orders O 
JOIN 
    Order_Items OI ON O.order_id = OI.order_id 
JOIN 
    Products P ON OI.product_id = P.product_id 
JOIN 
    ProductVariations V ON OI.variation_id = V.variation_id 
JOIN 
    Customers C ON O.customer_id = C.customer_id 
WHERE 
    C.user_id = ? 
ORDER BY 
    O.order_id DESC
LIMIT 30
");
$order_query->bind_param("i", $user_id);
$order_query->execute();
$order_result = $order_query->get_result();
$order_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="../assets/css/btn.css">
    <link rel="stylesheet" href="../assets/css/orders_my.css">
</head>
<body>
    <main>
        
        <div class="orders-container">
<button class="btn-trick-new" onclick="history.back()">Go Back</button>

            <h1>My Orders</h1>
            <hr>
            <?php while ($order = $order_result->fetch_assoc()) : 
    $order_id = htmlspecialchars($order['order_id']);
    $tracking_id = htmlspecialchars($order['tracking_id']);
    $order_status = htmlspecialchars($order['order_status']);
?>
    <div class="order" id="order<?php echo $order_id; ?>">
        <div class="order-header">
            <div>Order Placed: <span><?php echo htmlspecialchars($order['order_date']); ?></span></div>
            <div>Order status: <span><?php echo $order_status; ?></span></div>
            <div>Total: <span>₹<?php echo htmlspecialchars($order['total_price']); ?></span></div>
            <div>Order #: <span><?php echo $order_id; ?></span></div>
        </div>
        <div class="order-items">
            <div class="item">
                <img src="../<?php echo htmlspecialchars($order['image_main']); ?>" alt="Product Image">
                <div class="item-details">
                    <div class="item-name">
                        <?php echo htmlspecialchars($order['product_name']); ?>
                        <br><?php echo htmlspecialchars($order['product_attributes']); ?>
                    </div>
                    <div class="item-quantity">Quantity: <?php echo htmlspecialchars($order['product_quantity']); ?></div>
                    <div class="item-price">₹<?php echo htmlspecialchars($order['total_price']); ?></div>
                </div>
            </div>
        </div>
        <div class="order-footer">
            <?php if ($order_status == 'delivered') : ?>
                <button onclick="location.href = 'refund.php?order_id=<?php echo $order_id; ?>&user_id=<?php echo $user_id; ?>';" class="btn">Refund Order</button>
                <button onclick="location.href = 'exchange.php?order_id=<?php echo $order_id; ?>';" class="btn">Exchange Order</button>
            <?php elseif ($order_status == 'canceled') : ?>
                <!-- Do not display any buttons -->
            <?php elseif (in_array($order_status, ['refunded', 'exchanged'])) : ?>
                <img class="wfv" src="../assets/img/wfv.gif" alt="Processed Order">
            <?php elseif ($order_status == 'awaiting-payment') : ?>
                <button onclick="location.href = '../payment.php?order_id=<?php echo $order_id; ?>&tracking_id=<?php echo $tracking_id; ?>';" class="btn">Pay Now</button>
                <button onclick="location.href = '../re-upload.php?order_id=<?php echo $order_id; ?>&tracking_id=<?php echo $tracking_id; ?>';" class="btn">Re-upload Now</button>
            <?php elseif ($order_status == 're-uploaded screenshot') : ?>
                <img class="wfv" src="../assets/img/wfv.gif" alt="Waiting for Verification">
            <?php else : ?>
                <button onclick="location.href = 'track_order.php?tracking_id=<?php echo $tracking_id; ?>';" class="btn">Track Package</button>
                <button onclick="location.href = 'cancel_order.php?order_id=<?php echo $order_id; ?>';" class="btn">Cancel Order</button>
            <?php endif; ?>
        </div>
    </div>
<?php endwhile; ?>

</div>

    </main>
    <script src="script.js"></script>
</body>
</html>
