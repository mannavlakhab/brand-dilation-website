<?php include('../db_connect.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.0.15/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/4.0.15/js/froala_editor.pkgd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: Bricolage Grotesque, sans-serif;
            background-color: #e8e8e8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 20px;
            border: solid 1px #8d8d8d;
        }

        h1 {
            text-align: center;
            color: #3c0e40;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            color: #555;
        }

        input[type="text"], input[type="file"], textarea {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            padding: 14px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 0px 0px 14px 14px;
            cursor: pointer;
            font-size: 16px;
            z-index: 10;
            margin-top:-41px;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 15px;
            }

            input[type="text"], input[type="file"], textarea {
                font-size: 14px;
            }

            input[type="submit"] {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Create New Blog Post</h1>
    <form action="save.php" method="POST" enctype="multipart/form-data">
        <label for="main_photo">Main Photo:</label>
        <input type="file" id="main_photo" name="main_photo" accept="image/*" required>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="category">Category:</label>
        <input type="text" id="category" name="category" required>

        <label for="topic">Topic:</label>
        <input type="text" id="topic" name="topic" required>

        <label for="tags">Tags (comma-separated):</label>
        <input type="text" id="tags" name="tags">

        <label for="short_description">Short Description:</label>
        <textarea id="short_description" name="short_description" rows="4" required></textarea>
        
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required>
        
        <label for="content">Content:</label>
        <textarea id="content" name="content" required></textarea>

        <input type="submit" value="Publish Post">
    </form>
</div>

<script>
    new FroalaEditor('#content', {
        // Configure image upload settings
        imageUploadURL: 'upload_image.php',  // URL to handle image uploads
        imageUploadParams: { id: 'content' }, // Parameters to send with the request
        imageAllowedTypes: ['jpeg', 'jpg', 'png', 'gif'], // Allowed image types
        height: 300 // Set the height of the editor
    });
</script>

</body>
</html>
