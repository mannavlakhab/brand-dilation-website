<?php
session_start();
require_once '../db_connect.php';

// Ensure the user is an admin
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action']) && isset($_GET['order_id'])) {
    $id = $_GET['id'];        // Refund request ID
    $action = $_GET['action'];
    $order_id = $_GET['order_id'];  // Order ID
    $status = '';

    switch ($action) {
        case 'approve':
            $status = 'Approved';
            break;
        case 'reject':
            $status = 'Rejected';
            break;
        case 'pending':
            $status = 'Pending';
            break;
        case 'onhold':
            $status = 'On Hold';
            break;
        case 'refund_credit':
            $status = 'Refund Credit';
            break;
        case 'complete':
            $status = 'Complete';
            break;
        default:
            $status = 'Pending';
            break;
    }

    // Update the refund request status
    $query = "UPDATE refund_requests SET refund_status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        // Update order status based on refund request status
        $update_order_query = "";
        switch ($status) {
            case 'Refund Credit':
            case 'Complete':
                $update_order_query = "UPDATE orders SET order_status = 'Refunded' WHERE order_id = ?";
                break;
            case 'Rejected':
                $update_order_query = "UPDATE orders SET order_status = 'Refund-rejected' WHERE order_id = ?";
                break;
            case 'Pending':
                $update_order_query = "UPDATE orders SET order_status = 'Refund-pending' WHERE order_id = ?";
                break;
            case 'On Hold':
                $update_order_query = "UPDATE orders SET order_status = 'Refund-onhold' WHERE order_id = ?";
                break;
            default:
                break;
        }

        if ($update_order_query) {
            $order_stmt = $conn->prepare($update_order_query);
            $order_stmt->bind_param("i", $order_id);

            if ($order_stmt->execute()) {
                header("Location: view_refunds.php?msg=Refund status updated and order marked accordingly.");
            } else {
                header("Location: view_refunds.php?msg=Refund updated but failed to update order status.");
            }
            $order_stmt->close();
        } else {
            header("Location: view_refunds.php?msg=Action completed successfully.");
        }
    } else {
        header("Location: view_refunds.php?msg=Failed to update refund status.");
    }

    $stmt->close();
} else {
    header("Location: view_refunds.php?msg=Invalid request.");
}
?>
