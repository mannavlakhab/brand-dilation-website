<?php
include('../../db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $title = $_POST['title'];
    $category = $_POST['category'];
    $topic = $_POST['topic'];
    $keywords = $_POST['keywords'];
    $short_description = $_POST['short_description'];
    $content = $_POST['content'];
    $author = $_POST['author'];

    // Handle file upload
    $main_photo = '';
    if (isset($_FILES['main_photo']) && $_FILES['main_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['main_photo']['tmp_name'];
        $fileName = $_FILES['main_photo']['name'];
        $fileSize = $_FILES['main_photo']['size'];
        $fileType = $_FILES['main_photo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Specify allowed file extensions
        $allowedExts = array('jpg', 'jpeg', 'png', 'gif');

        $uploadFileDir = 'uploads/';
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true); // Create the directory if it doesn't exist
        }

        $dest_path = $uploadFileDir . uniqid() . '.' . $fileExtension; // Unique file name

        if (in_array($fileExtension, $allowedExts)) {
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $main_photo = basename($dest_path); // Store the file name in the database
            } else {
                echo 'Error moving the uploaded file.';
                exit;
            }
        } else {
            echo 'Upload failed. Allowed file types: jpg, jpeg, png, gif.';
            exit;
        }
    } else {
        echo 'No file uploaded or there was an upload error.';
        exit;
    }

    // Save the post details to the database
    $sql = "INSERT INTO blog_posts (title, category, topic, keywords, short_description, content, author, main_photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssss', $title, $category, $topic, $keywords, $short_description, $content, $author, $main_photo);

    if ($stmt->execute()) {
        echo "New post created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
