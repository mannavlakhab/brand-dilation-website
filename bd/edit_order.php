<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}


$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$order = null;

if ($order_id) {
    // Fetch order details
    $sql = "SELECT o.order_id, o.customer_id, o.order_status, o.shipping_address, o.total_price, o.shipping_cost, 
       o.payment_method, o.payment_status,o.payment_screenshot, o.payment_details, o.order_date, 
       c.first_name, c.last_name, c.email, c.phone_number, c.address, 
       a.address_id, a.address_line_1, a.address_line_2, a.city , a.state ,a.postal_code, a.country 
FROM Orders o
JOIN Customers c ON o.customer_id = c.customer_id
JOIN addresses a ON c.address = a.address_id
WHERE o.order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_status = $_POST['order_status'];
    $shipping_address = $_POST['shipping_address'];
    $payment_status = $_POST['payment_status'];

    // Handle empty tracking ID (treat it as NULL)
    if (empty($tracking_id)) {
        $tracking_id = null;
    }

    // Update order details
    $update_sql = "UPDATE Orders SET 
                   order_status = ?, 
                   shipping_address = ?, 
                   payment_status = ?
                   WHERE order_id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $order_status, $shipping_address, $payment_status, $order_id);
    $update_stmt->execute();

    // Redirect to view page or display success message
    header("Location: orders.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="../assets/css/btn.css">

    <style>
        body{
            box-shadow:  1px 1px 20px #c7c7c7;
            border-radius:10px; 

        }
        .container {
            width: 90%;
            margin: auto;
            padding: 10px;
        }
        .order {
            border: 1px solid #ccc;
            border-radius:10px; 
            margin: 10px 0;
            padding: 10px;

        }
        .form-group {
            margin: 10px 0;
        }
        .form-group label {
            display: block;
            margin: 5px 0;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Order</h1>
        <?php if ($order): ?>
            <form action="edit_order.php?order_id=<?php echo $order['order_id']; ?>" method="POST">
                <div class="order">
                    <h3>Order Information</h3>
                    <div class="form-group">
                        <label for="order_status">Order Status</label>
                        <select name="order_status" id="order_status" required>
                            <option value="pending" <?php if ($order['order_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="processing" <?php if ($order['order_status'] == 'processing') echo 'selected'; ?>>Processing</option>
                            <option value="shipped" <?php if ($order['order_status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                            <option value="delivered" <?php if ($order['order_status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                            <option value="onhold" <?php if ($order['order_status'] == 'onhold') echo 'selected'; ?>>onhold</option>
                            <option value="refunded" <?php if ($order['order_status'] == 'refunded') echo 'selected'; ?>>refunded</option>
                            <option value="exchanged" <?php if ($order['order_status'] == 'exchanged') echo 'selected'; ?>>Exchanged</option>
                            <option value="canceled" <?php if ($order['order_status'] == 'canceled') echo 'selected'; ?>>Canceled</option>
                            <option value="awaiting-payment" <?php if ($order['order_status'] == 'awaiting-payment') echo 'selected'; ?>>Waiting for Payment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address</label>
                        <textarea name="shipping_address" id="shipping_address" required><?php echo $order['address_line_1'] .' ' .$order['address_line_2']  .' ' .$order['city']  .' ' .$order['postal_code']  .' ' .$order['state']  .' ' .$order['country']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="payment_status">Payment Status</label>
                        <select name="payment_status" id="payment_status" required>
                            <option value="<?php echo $order['payment_status']; ?>"><?php echo $order['payment_status']; ?></option>
                            <option value="Paid" > Paid</option>
                            <option value="Unpaid" > Unpaid</option>
        </select>
        <?php
// Check if payment method is not cash
if ($order['payment_method'] !== 'cash') {
    // Define the paths to the payment screenshot
    $imagePath = '../' . $order['payment_screenshot'];
    $imagePath1 = '../screenshots/' . $order['payment_screenshot'];

    // Check if the image exists in the first path
    if (file_exists($imagePath1)) {
        // Display the image from the 'screenshots' directory
        echo '<img src="' . htmlspecialchars($imagePath1) . '" alt="Payment proof" width="auto" height="385px">';
    } elseif (file_exists($imagePath)) {
        // Display the image from the default directory
        echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Payment proof" width="auto" height="385px">';
    } else {
        // If neither image exists, show a message
        echo '<p>Payment proof not available.</p>';
    }
} else {
    // If payment method is cash, you might want to display something else or leave it blank
    echo '<p>Payment method is cash, no payment proof required.</p>';
}

?>

                    </div><br>
                    <button class="btn-trick-new" type="submit">Update Order</button>
                </div>
            </form>
        <?php else: ?>
            <p>Order not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
