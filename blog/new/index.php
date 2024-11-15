<?php include('../../db_connect.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.0.15/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.0.15/js/froala_editor.pkgd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <h1>Create New Blog Post</h1>
    <form action="save.php" method="POST" enctype="multipart/form-data">
        <label for="main_photo">Main Photo:</label><br>
        <input type="file" id="main_photo" name="main_photo" accept="image/*" required><br><br>

        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="category">Category:</label><br>
        <input type="text" id="category" name="category" required><br><br>

        <label for="topic">Topic:</label><br>
        <input type="text" id="topic" name="topic" required><br><br>

        <label for="keywords">Keywords (comma-separated):</label><br>
        <input type="text" id="keywords" name="keywords" required><br><br>

        <label for="short_description">Short Description:</label><br>
        <textarea id="short_description" name="short_description" rows="4" required></textarea><br><br>
        
        <label for="author">Author:</label><br>
        <input type="text" id="author" name="author" required><br><br>
        
        <label for="content">Content:</label><br>
        <textarea id="content" name="content" required></textarea><br><br>

        <input type="submit" value="Publish Post">
    </form>

    <script>
        new FroalaEditor('#content', {
            // Configure image upload settings
            imageUploadURL: 'upload_image.php',  // URL to handle image uploads
            imageUploadParams: { id: 'content' }, // Parameters to send with the request
            imageAllowedTypes: ['jpeg', 'jpg', 'png', 'gif'], // Allowed image types
        });
    </script>
</body>
</html>
