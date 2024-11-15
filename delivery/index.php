<?php
session_start();
include('config.php'); // Database connection

if (!isset($_SESSION['delivery_partner_id'])) {
    header("Location: login.php");
    exit;
}
$delivery_partner_id = $_SESSION['delivery_partner_id'];
$query = "SELECT * FROM order_dil";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delivery Partner Dashboard</title>
</head>
<body>
    <h2>Welcome, Delivery Partner</h2>
    <a href="logout.php">Logout</a>
    <h3>Assigned Orders</h3>
    <table>
        <tr>
            <th>br_code</th>
            <th>fast_delivery </th>
            <th>Customer ID</th>
            <th>status </th>
            <th>Total Price</th>
            <th>Shipping Address</th>
            <th>Delivery Date</th>
            <th>customer_name </th>
            <th>phone_number </th>
            <th>Actions</th>
        </tr>
        <?php while ($order = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $order['br_code']; ?></td>
            <td><?php echo $order['fast_delivery']; ?></td>
            <td><?php echo $order['customer_id']; ?></td>
            <td><?php echo $order['status']; ?></td>
            <td><?php echo $order['total_price']; ?></td>
            <td><?php echo $order['shipping_address']; ?></td>
            <td><?php echo $order['delivery_date']; ?></td>
            <td><?php echo $order['customer_name']; ?></td>
            <td><?php echo $order['phone_number']; ?></td>
            <td>
                <a href="order.php?order_id=<?php echo $order['id']; ?>">Update Status</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    <a href="assign.php">ass</a>
</body>
</html>
