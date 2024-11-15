<?php
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once '../db_connect.php'; // Make sure this path is correct

session_start(); // Start the session

// Prepare notifications array
$notifications = [];

// Get user ID if logged in
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Fetch user-specific notifications if logged in
if ($user_id) {
    $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'message' => $row['message'],
                'created_at' => $row['created_at'],
                'user_id' => $row['user_id'],
                'type' => 'user', // Indicate user-specific notification
            ];
        }
    }
    $stmt->close();
}

// Fetch general notifications (for all users)
$sql = "SELECT * FROM notifications WHERE user_id IS NULL OR user_id = '' ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'message' => $row['message'],
                'created_at' => $row['created_at'],
                'user_id' => $row['user_id'],
                'type' => 'general', // Indicate general notification
            ];
        }
    }
} else {
    // Handle query error
    $notifications[] = ['message' => 'Query error: ' . $conn->error];
}

// Sort notifications by created_at in descending order (most recent first)
usort($notifications, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

$conn->close();

// Return notifications as JSON
echo json_encode($notifications);
?>
