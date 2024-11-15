<?php
session_start();
include('config.php'); // Database connection

if (!isset($_SESSION['delivery_partner_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch all orders
$query = "SELECT * FROM orders";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Orders</title>
</head>
<body>
    <h2>Assign Orders</h2>
    <a href="logout.php">Logout</a>
    <h3>Orders</h3>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Order Status</th>
            <th>Shipping Address</th>
            <th>Total Price</th>
            <th>Payment Status</th>
            <th>Payment Method</th>
            <th>Delivery Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($order = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $order['order_id']; ?></td>
            <td><?php echo $order['order_status']; ?></td>
            <td><?php echo $order['shipping_address']; ?></td>
            <td><?php echo $order['total_price']; ?></td>
            <td><?php echo $order['payment_status']; ?></td>
            <td><?php echo $order['payment_method']; ?></td>
            <td><?php echo $order['delivery_date']; ?></td>
            <td>
                <form method="POST" action="assign.php">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <input type="hidden" name="order_status" value="<?php echo $order['order_status']; ?>">
                    <input type="hidden" name="shipping_address" value="<?php echo $order['shipping_address']; ?>">
                    <input type="hidden" name="total_price" value="<?php echo $order['total_price']; ?>">
                    <input type="hidden" name="payment_status" value="<?php echo $order['payment_status']; ?>">
                    <input type="hidden" name="payment_method" value="<?php echo $order['payment_method']; ?>">
                    <input type="hidden" name="delivery_date" value="<?php echo $order['delivery_date']; ?>">
                    <input type="submit" name="assign_order" value="Assign">
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

    <?php
    if (isset($_POST['assign_order'])) {
        $order_id = $_POST['order_id'];
        $order_status = $_POST['order_status'];
        $shipping_address = $_POST['shipping_address'];
        $total_price = $_POST['total_price'];
        $payment_status = $_POST['payment_status'];
        $payment_method = $_POST['payment_method'];
        $delivery_date = $_POST['delivery_date'];
        $delivery_partner_id = $_SESSION['delivery_partner_id'];

        // Fetch customer_id from orders table
        $query = "SELECT customer_id FROM orders WHERE order_id = '$order_id'";
        $result = mysqli_query($conn, $query);
        $order = mysqli_fetch_assoc($result);
        $customer_id = $order['customer_id'];

        // Generate a unique barcode number
        $barcode_number = uniqid('bd-');
        $barcode_number = substr($barcode_number, 0, 11);

        // Insert data into order_dil table
        $query = "INSERT INTO order_dil (customer_id, status, total_price, shipping_address, delivery_date, customer_name, phone_number, fast_delivery, br_code) 
                  VALUES ('$customer_id', '$order_status', '$total_price', '$shipping_address', '$delivery_date', '', '', 0, '$barcode_number')";

        if (mysqli_query($conn, $query)) {
            // Update orders table with the barcode number
            $query = "UPDATE orders SET barcode_number = '$barcode_number' WHERE order_id = '$order_id'";
            mysqli_query($conn, $query);

            echo "Order assigned successfully.";
        } else {
            echo "Failed to assign order.";
        }
    }
    ?>
</body>
</html>
