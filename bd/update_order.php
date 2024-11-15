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
                   o.payment_method, o.payment_status, o.payment_details, o.tracking_id, o.order_date, 
                   c.first_name, c.last_name, c.email, c.phone_number, c.address
            FROM Orders o
            JOIN Customers c ON o.customer_id = c.customer_id
            WHERE o.order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $shipping_address = $_POST['shipping_address'];
    $payment_status = $_POST['payment_status'];
    $tracking_id = $_POST['tracking_id'];

    // Handle empty tracking ID (treat it as NULL)
    $tracking_id = empty($tracking_id) ? NULL : $tracking_id;

    // Update order details
    $update_sql = "UPDATE Orders SET 
                   order_status = ?, 
                   shipping_address = ?, 
                   payment_status = ?, 
                   tracking_id = ?
                   WHERE order_id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $order_status, $shipping_address, $payment_status, $tracking_id, $order_id);
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
    <style>
        .container {
            width: 90%;
            margin: auto;
            padding: 10px;
        }
        .order {
            border: 1px solid #ccc;
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
            <form action="update_order.php" method="POST">
                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                <div class="order">
                    <h3>Order Information</h3>
                    <div class="form-group">
                        <label for="order_status">Order Status</label>
                        <select name="order_status" id="order_status" required>
                            <option value="pending" <?php if ($order['order_status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="processing" <?php if ($order['order_status'] == 'processing') echo 'selected'; ?>>Processing</option>
                            <option value="shipped" <?php if ($order['order_status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                            <option value="delivered" <?php if ($order['order_status'] == 'delivered') echo 'selected'; ?>>Delivered</option>
                            <option value="canceled" <?php if ($order['order_status'] == 'canceled') echo 'selected'; ?>>Canceled</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address</label>
                        <textarea name="shipping_address" id="shipping_address" required><?php echo $order['shipping_address']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="payment_status">Payment Status</label>
                        <input type="text" name="payment_status" id="payment_status" value="<?php echo $order['payment_status']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tracking_id">Tracking ID</label>
                        <input type="text" name="tracking_id" id="tracking_id" value="<?php echo $order['tracking_id']; ?>">
                    </div>
                    <button type="submit">Update Order</button>
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
