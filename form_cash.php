<?php
require_once 'db_connect.php'; // Ensure this file connects to your database
// Ensure order_id and tracking_id are provided via GET
if (!isset($_GET['order_id']) || !isset($_GET['tracking_id'])) {
    echo "No order ID or tracking ID provided.";
    exit();
}

$order_id = intval($_GET['order_id']);
$tracking_id = htmlspecialchars($_GET['tracking_id']);

// Fetch product details from the database
$sql = "SELECT p.brand, p.model, p.price, oi.product_quantity 
        FROM Order_Items oi 
        JOIN Products p ON oi.product_id = p.product_id 
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch total price from the orders table
$sql = "SELECT total_price, payment_method, payment_status FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure all expected POST variables are set
    if (isset($_POST['order_id']) && isset($_POST['payment_method']) && isset($_POST['payment_status'])) {
        $order_id = intval($_POST['order_id']);
        $payment_method = htmlspecialchars($_POST['payment_method']);
        $payment_status = htmlspecialchars($_POST['payment_status']);

        // Update order in the database
        $sql = "UPDATE orders SET payment_method = ?, payment_status = ?, delivery_date=DATE_ADD(CURDATE(), INTERVAL 7 DAY) WHERE order_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssi", $payment_method, $payment_status, $order_id);
            if ($stmt->execute()) {
                // Redirect to thank you page
                header("Location: thank_you.php?tracking_id=$tracking_id");
                exit();
            } else {
                echo "Error updating order: " . $conn->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Missing POST variables.";
    }
} else {
    echo "Invalid request method.";
}

?>