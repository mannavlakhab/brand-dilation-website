    <?php
    include('../db_connect.php');

    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];

        if ($file['error'] === UPLOAD_ERR_OK) {
            // Generate a unique file name
            $uploadDir = 'uploads/';
            $fileName = uniqid() . '-man-' . basename($file['name']);
            $uploadFile = $uploadDir . $fileName;

            // Move the uploaded file to the destination directory
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                // Respond with the file URL
                echo json_encode(['link' => $uploadFile]);
            } else {
                echo json_encode(['error' => 'File upload failed.']);
            }
        } else {
            echo json_encode(['error' => 'File upload error.']);
        }
    } else {
        echo json_encode(['error' => 'No file uploaded.']);
    }
?>