<?php
include('../db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $topic = $_POST['topic'];
    $short_description = $_POST['short_description'];
    $content = $_POST['content'];
    $author = $_POST['author'];
    $tags = isset($_POST['tags']) ? $_POST['tags'] : '';

    // Generate a custom ID (e.g., 2302410) where the first two digits are fixed (e.g., '23')
    $prefix = '23';
    $randomNumber = mt_rand(100000, 999999); // Generates a 6-digit random number
    $customId = $prefix . $randomNumber;

    // Check if the custom ID already exists in the database
    $idCheckSql = "SELECT COUNT(*) as count FROM blog_posts WHERE id = ?";
    $idCheckStmt = $conn->prepare($idCheckSql);
    $idCheckStmt->bind_param("s", $customId);
    $idCheckStmt->execute();
    $idCheckResult = $idCheckStmt->get_result();
    $idExists = $idCheckResult->fetch_assoc()['count'] > 0;

    // If ID already exists, generate a new one (simple loop for reattempting)
    while ($idExists) {
        $randomNumber = mt_rand(100000, 999999);
        $customId = $prefix . $randomNumber;
        $idCheckStmt->bind_param("s", $customId);
        $idCheckStmt->execute();
        $idCheckResult = $idCheckStmt->get_result();
        $idExists = $idCheckResult->fetch_assoc()['count'] > 0;
    }

    // Handle file upload
    if (isset($_FILES['main_photo']) && $_FILES['main_photo']['error'] == UPLOAD_ERR_OK) {
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

        $dest_path = $uploadFileDir . $fileName;

        if (in_array($fileExtension, $allowedExts)) {
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                echo 'File is successfully uploaded.';
            } else {
                echo 'Error moving the uploaded file.';
            }
        } else {
            echo 'Upload failed. Allowed file types: jpg, jpeg, png, gif.';
        }
    } else {
        echo 'No file uploaded or there was an upload error.';
    }

    // Save the post details to the database with the custom ID
    $sql = "INSERT INTO blog_posts (id, title, category, topic, short_description, content, author, main_photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $customId, $title, $category, $topic, $short_description, $content, $author, $fileName);
    if ($stmt->execute()) {
        $postId = $customId; // Use the custom ID for the post

        // Handle tags
        if (!empty($tags)) {
            $tagsArray = array_map('trim', explode(',', $tags));
            foreach ($tagsArray as $tag) {
                $tag = htmlspecialchars($tag);

                // Insert the tag if it does not exist
                $sql = "INSERT IGNORE INTO tags (name) VALUES (?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $tag);
                $stmt->execute();

                // Get the ID of the tag
                $tagIdQuery = $conn->prepare("SELECT id FROM tags WHERE name = ?");
                $tagIdQuery->bind_param("s", $tag);
                $tagIdQuery->execute();
                $tagIdResult = $tagIdQuery->get_result();
                $tagId = $tagIdResult->fetch_assoc()['id'];

                // Check if the tag-post association already exists before inserting
                $checkSql = "SELECT COUNT(*) as count FROM post_tags WHERE post_id = ? AND tag_id = ?";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bind_param("ii", $postId, $tagId);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
                $exists = $checkResult->fetch_assoc()['count'] > 0;

                if (!$exists) {
                    // Associate the tag with the post
                    $sql = "INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $postId, $tagId);
                    $stmt->execute();
                }
            }
        }

        echo "New post created successfully with ID: $postId.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
