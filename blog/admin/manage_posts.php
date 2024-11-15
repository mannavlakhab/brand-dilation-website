<?php
session_start();
include('../../db_connect.php');

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($action === 'delete' && $post_id > 0) {
        $sql = "DELETE FROM blog_posts WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $post_id);
        
        if ($stmt->execute()) {
            echo "Post deleted successfully.";
        } else {
            echo "Error deleting post: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

$sql = "SELECT id, title, created_at FROM blog_posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blog Posts</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
</head>
<body>
    <h1>Manage Blog Posts</h1>
    <a href="create_post.php">Add New Post</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                    echo "<td>" . date("F j, Y, g:i a", strtotime($row['created_at'])) . "</td>";
                    echo "<td>
                        <a href='edit_post.php?id=" . $row['id'] . "'>Edit</a> |
                        <a href='manage_posts.php?action=delete&id=" . $row['id'] . "' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No blog posts found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
