<?php
session_start();
include('../../db_connect.php');

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit();
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $category = $conn->real_escape_string($_POST['category']);
    $topic = $conn->real_escape_string($_POST['topic']);
    $keywords = $conn->real_escape_string($_POST['keywords']);
    $content = $conn->real_escape_string($_POST['content']);
    $author = $conn->real_escape_string($_POST['author']);
    
    $sql = "UPDATE blog_posts SET title = ?, category = ?, topic = ?, keywords = ?, content = ?, author = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $title, $category, $topic, $keywords, $content, $author, $post_id);
    
    if ($stmt->execute()) {
        echo "Post updated successfully. <a href='manage_posts.php'>Back to Posts</a>";
    } else {
        echo "Error updating post: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    $sql = "SELECT * FROM blog_posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.0.15/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.0.15/js/froala_editor.pkgd.min.js"></script>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
</head>
<body>
    <h1>Edit Blog Post</h1>
    <form action="edit_post.php?id=<?php echo $post_id; ?>" method="POST">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required><br><br>

        <label for="category">Category:</label><br>
        <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($post['category']); ?>" required><br><br>

        <label for="topic">Topic:</label><br>
        <input type="text" id="topic" name="topic" value="<?php echo htmlspecialchars($post['topic']); ?>" required><br><br>

        <label for="keywords">Keywords (comma-separated):</label><br>
        <input type="text" id="keywords" name="keywords" value="<?php echo htmlspecialchars($post['keywords']); ?>" required><br><br>

        <label for="content">Content:</label><br>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

        <label for="author">Author:</label><br>
        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($post['author']); ?>" required><br><br>

        <input type="submit" value="Update Post">
    </form>

    <script>
        new FroalaEditor('#content');
    </script>
</body>
</html>
