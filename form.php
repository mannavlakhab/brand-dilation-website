<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Include database connection
    include('db_connect.php');
    
    // Retrieve tracking_id and pd (payment method) from the query parameter
    $tracking_id = htmlspecialchars($_GET['tracking_id']);
    $pd = htmlspecialchars($_GET['pd']);
    
    // Set the payment method based on the pd parameter
    if ($pd == 'qr') {
        $payment_method = 'QR';
    } elseif ($pd == 'upi') {
        $payment_method = 'UPI';
    } else {
        die('Invalid payment method');
    }

    // Retrieve payment status and validate it
    if (isset($_POST['payment_status']) && !empty($_POST['payment_status'])) {
        $payment_status = $_POST['payment_status'];
    } else {
        die('Payment status is required');
    }

    if (isset($_POST['payment_details']) && !empty($_POST['payment_details'])) {
        $payment_details = $_POST['payment_details'];
    } else {
        die('Payment details are required');
    }
    
    // Handle file upload
    if (isset($_FILES['payment_screenshot']) && $_FILES['payment_screenshot']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['payment_screenshot']['name'];
        $filetmp = $_FILES['payment_screenshot']['tmp_name'];
        $filesize = $_FILES['payment_screenshot']['size'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (!in_array($filetype, $allowed)) {
            die('File type not allowed');
        }
        
        if ($filesize > 2000000) { // 2MB limit
            die('File size is too large');
        }
        
        $new_filename = uniqid() . '.' . $filetype;
        $upload_dir = 'screenshots/';
        
        if (!move_uploaded_file($filetmp, $upload_dir . $new_filename)) {
            die('Failed to upload file');
        }
    } else {
        die('Screenshot is required');
    }
    
    // Update the Orders table
    $query = "UPDATE orders SET payment_status=?, payment_details=?, payment_screenshot=?, payment_method=?, delivery_date=DATE_ADD(CURDATE(), INTERVAL 5 DAY) WHERE tracking_id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $payment_status, $payment_details, $new_filename, $payment_method, $tracking_id);
    
    if ($stmt->execute()) {
        echo "Payment status updated successfully";
        header("Location: thank_you.php?order_id=" . $order_id . "&tracking_id=" . $tracking_id);
        exit();
    } else {
        echo "Failed to update payment status";
    }
    
    $stmt->close();
    $conn->close();
}
?>
