<?php
// Ensure link and order_id are provided via GET
// if (!isset($_GET['link']) || !isset($_GET['order_id'])) {
//     echo "No payment link or order ID provided.";
//     exit();
// }
$payment_link = urldecode($_GET['link']);


$tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : '';
$payment_link = urldecode($_GET['link']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI App Payment</title>
    <meta http-equiv="refresh" content="5;url=<?php echo htmlspecialchars($payment_link); ?>">
    <script>
        function isMobileDevice() {
            const userAgent = navigator.userAgent || navigator.vendor || window.opera;
            return (/android|iPad|iPhone|iPod|windows phone|kindle|silk|blackberry|playbook|tablet/i.test(userAgent.toLowerCase()));
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (!isMobileDevice()) {
                document.getElementById('unsupported-device-message').style.display = 'block';
                document.getElementById('redirect-message').style.display = 'none';
            }
        });
    </script>
</head>
<body>
    <!-- <div id="unsupported-device-message" style="display: none;">
        <h3>UPI App Payment Not Supported on This Device</h3>
        <p>UPI payments can only be processed on mobile devices. Please switch to a mobile device to complete your payment.</p>
    </div> -->

    <div id="redirect-message">
        <h3>Redirecting to UPI App...</h3>
        <p>If not redirected, <a href="<?php echo htmlspecialchars($payment_link); ?>">click here</a>.</p>
        <form method="post" enctype="multipart/form-data">
        <label>
            <select name="payment_status" required>
                <option value="" selected disabled>Select your payment status</option>
                <option value="complete">Complete</option>
                <option value="processing">Processing</option>
                <option value="failed">Failed</option>
                <option value="pending">Pending</option>
            </select>
        </label>
        <br>
        <label>
            Upload Screenshot:
            <input type="file" name="payment_screenshot" required>
        </label>
        <br>
        <input type="submit" value="Submit Payment Status">
    </form>
    <p><b>Notes:</b> If payment not done it will consider as cash.</p>
    </div>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_status = $_POST['payment_status'];

    // Handle screenshot upload if provided
    $screenshot_file = ''; // Placeholder for storing the filename
    if ($_FILES['payment_screenshot']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'screenshots/'; // Directory to store screenshots
        $screenshot_file = $upload_dir . basename($_FILES['payment_screenshot']['name']);
        if (!move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $screenshot_file)) {
            echo "Failed to move uploaded file.";
            exit();
        }
    }

    // Update payment status and screenshot in the Orders table
    require_once 'db_connect.php'; // Ensure this file connects to your database
    $sql = "UPDATE Orders SET payment_status = ?, payment_screenshot = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $payment_status, $screenshot_file, $order_id);
    $stmt->execute();
    $stmt->close();

    header("Location: thank_you.php?order_id=" . $order_id . "&tracking_id=" . $tracking_id);
    exit();
}
?>
