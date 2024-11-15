<?php
session_start();
require_once '../db_connect.php';

// Ensure the user is an admin
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['id']) && isset($_GET['action']) && isset($_GET['order_id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];
    $order_id = $_GET['order_id'];
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
        case 'SP':
            $status = 'SHIPPING & PICKUP';
            break;
        case 'complete':
            $status = 'Complete';
            break;
        default:
            $status = 'Pending';
            break;
    }

    $query = "UPDATE exchanage_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
        // Update order status based on exchanage request status
        $update_order_query = "";
        switch ($status) {
            case 'SHIPPING & PICKUP':
                $update_order_query = "UPDATE orders SET order_status = 'pickup and delivering' WHERE order_id = ?";
                break;
            case 'Complete':
                $update_order_query = "UPDATE orders SET order_status = 'exchanged' WHERE order_id = ?";
                break;
            case 'Rejected':
                $update_order_query = "UPDATE orders SET order_status = 'exchanage-rejected' WHERE order_id = ?";
                break;
            case 'Pending':
                $update_order_query = "UPDATE orders SET order_status = 'exchanage-pending' WHERE order_id = ?";
                break;
            case 'On Hold':
                $update_order_query = "UPDATE orders SET order_status = 'exchanage-onhold' WHERE order_id = ?";
                break;
            default:
                break;
        }

        if ($update_order_query) {
            $order_stmt = $conn->prepare($update_order_query);
            $order_stmt->bind_param("i", $order_id);

            if ($order_stmt->execute()) {
                header("Location: view_exchanage.php?msg=exchanage status updated and order marked accordingly.");
            } else {
                header("Location: view_exchanage.php?msg=exchanage updated but failed to update order status.");
            }
            $order_stmt->close();
        } else {
            header("Location: view_exchanage.php?msg=Action completed successfully.");
        }
    } else {
        header("Location: view_exchanage.php?msg=Failed to update exchanage status.");
    }

    $stmt->close();
} else {
    header("Location: view_exchanage.php?msg=Invalid request.");
}

?>