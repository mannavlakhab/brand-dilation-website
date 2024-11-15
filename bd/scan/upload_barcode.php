<?php
header('Content-Type: application/json');

// Autoload Composer dependencies
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['barcodeImage'])) {
    $target_dir = "uploads/";

    // Create the directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_file = $target_dir . basename($_FILES["barcodeImage"]["name"]);

    // Check for upload errors
    if ($_FILES['barcodeImage']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'File upload error: ' . $_FILES['barcodeImage']['error']
        ]);
        exit;
    }

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["barcodeImage"]["tmp_name"], $target_file)) {
        echo json_encode([
            'success' => true,
            'message' => 'File uploaded successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to move uploaded file.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No file uploaded.'
    ]);
}
