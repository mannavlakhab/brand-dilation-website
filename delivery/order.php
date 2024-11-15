<?php
session_start();
include('config.php'); // Database connection

// Check if delivery partner is logged in
if (!isset($_SESSION['delivery_partner_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize variables
$order = null;
$status = '';

// Handle form submission for updating order status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = isset($_POST['order_id']) ? mysqli_real_escape_string($conn, $_POST['order_id']) : '';
    $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';

    // Update order status in the database
    $query = "UPDATE order_dil SET status = '$status' WHERE id = '$order_id'";
    if (mysqli_query($conn, $query)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }
}

// Fetch order details to populate the form (assuming $order_id is fetched from POST or GET)
$order_id = isset($_REQUEST['order_id']) ? $_REQUEST['order_id'] : '';
$query = "SELECT * FROM order_dil WHERE id = '$order_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $order = mysqli_fetch_assoc($result);
} else {
    echo "Order not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order Status</title>
</head>
<body>
    <h2>Update Order Status</h2>
    <form method="POST" action="order.php">
        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
        <label>Select Status:</label>
        <select name="status">
            <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
            <option value="Cancelled" <?php if ($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>
        <button type="submit">Update Status</button>
    </form>
</body>
</html>
