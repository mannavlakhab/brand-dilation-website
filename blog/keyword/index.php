<?php
include('../../db_connect.php');

if (isset($_GET['tag'])) {
    $tag = $_GET['tag'];

    // Fetch posts that contain the selected tag
    $stmt = $conn->prepare("
        SELECT bp.id,bp.title, bp.short_description, bp.author, bp.created_at
        FROM blog_posts bp
        JOIN post_tags pt ON bp.id = pt.post_id
        JOIN tags t ON pt.tag_id = t.id
        WHERE t.name = ?
    ");
    $stmt->bind_param("s", $tag);
    $stmt->execute();
    $result = $stmt->get_result(); ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../style.css">
        <title>Category</title>
        <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
    
    </head>
    <body>
    <header>
    <button class="btn-trick-new" onclick="history.back()">Go Back</button>   <h1>Posts containing tag: <?php echo htmlspecialchars($tag) ?></h1>
    </header>
    
    </body>
    </html>
    <?php

    if ($result->num_rows > 0) {
        echo "<ul class='post-list'>";
        while ($row = $result->fetch_assoc()) {
            echo '<a href="../page.php?id='. htmlspecialchars($row['id']) .'">';
            echo '<li class="list-cate">';
            echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
            echo "<p>" . htmlspecialchars($row['short_description']) . "</p><br>";
            echo "<p>Author: " . htmlspecialchars($row['author']) . " | Date: " . htmlspecialchars($row['created_at']) . "</p>";
            echo "</li></a>";
        }
        echo "</ul>";
    } else {
        echo "<p>No posts found with this tag.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Tag not specified.</p>";
}

$conn->close();
?>
