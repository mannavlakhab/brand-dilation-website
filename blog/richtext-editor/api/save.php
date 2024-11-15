<?php
header('Content-Type: application/json');
include('../db_connect.php'); // Make sure you have a DB connection file

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$content = $data['content'] ?? '';

if (empty($content)) {
    echo json_encode(['error' => 'Content cannot be empty.']);
    exit;
}

// Insert content into the database
$stmt = $conn->prepare("INSERT INTO content (content) VALUES (?)");
$stmt->bind_param("s", $content);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Content saved successfully!']);
} else {
    echo json_encode(['error' => 'Failed to save content.']);
}

$stmt->close();
$conn->close();
?>
