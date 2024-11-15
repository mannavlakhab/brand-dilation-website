<?php
include('../db_connect.php');

// Get the post ID from the URL
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($post_id > 0) {
    // Update the likes count
    $sql = "UPDATE blog_posts SET likes = likes + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    // Redirect back to the blog posts page
    header("Location: index.php");
    exit();
} else {
    // Handle error
    echo "Invalid post ID.";
}
?>
