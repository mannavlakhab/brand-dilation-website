<?php
// update_order_status.php

include 'db_connection.php'; // Ensure this path is correct

header('Content-Type: application/json'); // Set the content type to JSON

$response = []; // Initialize the response array

if (isset($_POST['order_id'])) {
    $orderId = intval($_POST['order_id']);

    // Fetch the current payment status of the order
    $query = "SELECT payment_status FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        $response['status'] = 'error';
        $response['message'] = "SQL error: " . $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $paymentStatus = $order['payment_status'];

        // Check the payment status
        if ($paymentStatus === 'unpaid' || $paymentStatus === 'waiting') {
            $response['status'] = 'pending';
            $response['message'] = 'Payment is pending for Order ID ' . $orderId . '.';
        } else {
            // Update order status to delivered
            $updateQuery = "UPDATE orders SET order_status = 'delivered' WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            if ($updateStmt === false) {
                $response['status'] = 'error';
                $response['message'] = "SQL error: " . $conn->error;
                echo json_encode($response);
                exit;
            }

            $updateStmt->bind_param("i", $orderId);
            if ($updateStmt->execute()) {
                $response['status'] = 'success';
                $response['message'] = 'Order ID ' . $orderId . ' marked as delivered.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to update order status.';
            }

            $updateStmt->close();
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Order ID not found.';
    }

    $stmt->close();
} else {
    $response['status'] = 'error';
    $response['message'] = 'No Order ID provided.';
}

$conn->close();

// Output the JSON response
echo json_encode($response);
?>
