<?php
include('../../db_connect.php');

if (isset($_GET['topic'])) {
    $topic = $_GET['topic'];

    // Fetch posts that belong to the selected topic
    $stmt = $conn->prepare("SELECT id,title, short_description, author, created_at FROM blog_posts WHERE topic = ?");
    $stmt->bind_param("s", $topic);
    $stmt->execute();
    $result = $stmt->get_result();?>
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
    <button class="btn-trick-new" onclick="history.back()">Go Back</button>    <h1>Posts in topic: <?php echo htmlspecialchars($topic) ?></h1>
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
            echo "<p>" . htmlspecialchars($row['short_description']) . "</p>";
            echo "<p>Author: " . htmlspecialchars($row['author']) . " | Date: " . htmlspecialchars($row['created_at']) . "</p>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No posts found in this topic.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Topic not specified.</p>";
}

$conn->close();
?>