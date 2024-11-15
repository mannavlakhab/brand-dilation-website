<?php
require_once 'phpqrcode/qrlib.php'; // Include the QR Code library

// // Ensure link and order_id are provided via GET
// if (!isset($_GET['link']) || !isset($_GET['order_id'])) {
//     echo "No payment link or order ID provided.";
//     exit();
// }

$tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : '';
$payment_link = urldecode($_GET['link']);
$order_id = intval($_GET['order_id']);
$qr_code_file = 'qrcodes/' . md5($payment_link) . '.png';

// Generate QR code if it doesn't already exist
if (!file_exists($qr_code_file)) {
    QRcode::png($payment_link, $qr_code_file);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI QR Code Payment</title>
</head>
<body>

    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Outfit"
    />
    <style>
        body {
    align-items: center;
    background-color: hsl(212, 45%, 89%);
    display: flex;
    font-family: "Outfit", serif;
    justify-content: center;
    height: 100%;
    min-height: 100vh;
    width: 100%;
    overflow-y: scroll;

  }

  .card {
    background-color: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px hsl(220, 15%, 55%);
    padding: 4%;
    top: 25%;
    text-align: center;
    width: 375px;
  }

  #qr {
    border-radius: 15px;
    height: 375px;
    width: 375px;
  }

  #logo {
    height: 10%;
    width: 50%;
    margin-top: 20px;
  }

  .upi {
     display: flex;
     align-items: center;
     justify-content: center;
     height: 100%;
  }

  .upi > img {
    padding: 5px;
  }

  h1 {
    color: hsl(218, 44%, 22%);
    font-weight: 700;
  }

  p {
    color: hsl(220, 15%, 55%);
    font-size: 15px;
    font-weight: 400;
    padding: 15px;
  }
 
  .container {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .attribution {
    font-size: 11px;
    text-align: center;
  }

  .attribution a {
    color: hsl(228, 45%, 44%);
    text-decoration: none;
  }
  .btn {
  background-color: #04AA6D;
  color: white;
  padding: 12px;
  margin: 10px 0;
  border: none;
  width: 100%;
  border-radius: 10px;
  cursor: pointer;
  font-size: 17px;
}

.btn:hover {
  background-color: #45a049;
}
select {
  width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 10px;
}
    </style>
    <main>
   

      <section class="card">
         
        <img src="<?php echo htmlspecialchars($qr_code_file); ?>" id="qr" alt="image of qr code">

        <img src="../pages/brand/full.svg" id="logo" alt="logo here">

        <h3>Scan this QR Code to Pay</h3>

        <div class="upi">
          <img src="https://iili.io/HZsWZrl.png" style="height: 40px;">
          <img src="https://iili.io/HZsWQ14.png"  style="height: 20px;">
          <img src="https://iili.io/HZsWLBf.png"  style="height: 20px;">
          <img src="https://iili.io/HZsW6In.png"  style="height: 20px;">
        </div>
        <p>After Payment fill the form</p>
        <div class="container">
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
    <input type="submit" name="submit_order" value="Submit Payment Status" class="btn">

    </form>
    </div>
    <p><b>Notes:</b> If payment not done it will consider as cash.</p>

        <div class="attribution">
        Copyright © 2024-25 Brand Dilation • Powered by Man Navlakha    
        </div>
       
      </section>


      
    </main>
</body>
</html>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_status = $_POST['payment_status'];

    $screenshot_file = ''; // Placeholder for storing the filename
    if ($_FILES['payment_screenshot']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = './screenshots/'; // Ensure this is the correct path
        $unique_name = uniqid() . '_' . basename($_FILES['payment_screenshot']['name']);
        $screenshot_file = $upload_dir . $unique_name;

        $allowed_extensions = ['png', 'jpg', 'jpeg'];
        $file_extension = strtolower(pathinfo($screenshot_file, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            echo "Invalid file type. Only PNG, JPG, and JPEG files are allowed.";
            exit();
        }

        // Correcting the move_upload_file method to include the correct path
        if (!move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $screenshot_file)) {
            echo "Failed to upload screenshot.";
            exit();
        }

        // Only store the relative path for the screenshot file in the database
        $screenshot_file_relative = 'screenshots/' . $unique_name;

        // Update payment status and screenshot in the Orders table
        require_once 'db_connect.php'; // Ensure this file connects to your database
        $sql = "UPDATE Orders SET payment_status = ?, payment_screenshot = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $payment_status, $screenshot_file_relative, $order_id);
        $stmt->execute();
        $stmt->close();

        header("Location: thank_you.php?order_id=" . $order_id . "&tracking_id=" . $tracking_id);
        exit();
    } else {
        echo "File upload error: ";
        switch ($_FILES['payment_screenshot']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                echo "The uploaded file exceeds the allowed size.";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "No file was uploaded.";
                break;
            default:
                echo "Unknown error.";
                break;
        }
        exit();
    }
}
?>

