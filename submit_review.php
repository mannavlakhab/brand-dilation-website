<?php
session_start();
require 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if "Remember Me" cookie is set
    if (isset($_COOKIE['user_id'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      // Optionally, you can re-validate the user with the database here
  } else {
    // Store the current page in session to redirect after login
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
  }}


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $product_id = $_POST['product_id'];
        $user_id = $_SESSION['user_id']; // Assuming user is logged in
        $rating = $_POST['rating'];
        $review_text = $_POST['review_text'];



        // Handle file upload
        $review_image_path = null;
        if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['review_image']['tmp_name'];
            $fileName = $_FILES['review_image']['name'];
            $fileSize = $_FILES['review_image']['size'];
            $fileType = $_FILES['review_image']['type'];
            $fileNameCmps = explode('.', $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Check file extension and size
            $allowedExts = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($fileExtension, $allowedExts) && $fileSize < 5000000) {
                // Move file to the desired directory
                $uploadFileDir = 'uploads/review_images/';
                $dest_path = $uploadFileDir . md5(time() . $fileName) . '.' . $fileExtension;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $review_image_path = $dest_path;
                } else {
                    echo 'Error moving the uploaded file.';
                }
            } else {
                echo 'Invalid file type or size.';
            }
        }

        // Prepare and execute SQL statement
        $sql = "INSERT INTO product_reviews (product_id, user_id, rating, review_text, review_image_path) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iiiss", $product_id, $user_id, $rating, $review_text, $review_image_path);
            if ($stmt->execute()) {
                echo "Review submitted successfully.";
                header("Location: ../pd/?product_id=$product_id");
            } else {
                echo "Error submitting review: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }

    }
        $conn->close();
        ?>
