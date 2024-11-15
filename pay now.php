<?php
session_start();
require_once 'phpqrcode/qrlib.php'; // Include the QR Code library
require_once 'db_connect.php'; // Ensure this file connects to your database
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if "Remember Me" cookie is set
    if (isset($_COOKIE['user_id'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      // Optionally, you can re-validate the user with the database here
    } else {
      // If login is optional, do not redirect, just leave session unset
      // If you want to prompt the user to log in, you can show a message or an optional login button
      // Optionally, store the current page in session for redirection after optional login
      $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
      // You can choose to show a notification or a button to log in here
    }
}

// Ensure order_id and tracking_id are provided via GET
if (!isset($_GET['order_id']) || !isset($_GET['tracking_id'])) {
    echo "No order ID or tracking ID provided.";
    exit();
}

$order_id = intval($_GET['order_id']);
$tracking_id = htmlspecialchars($_GET['tracking_id']);

// Fetch product details from the database
$sql = "SELECT p.brand, p.model, p.price, oi.product_quantity 
        FROM Order_Items oi 
        JOIN Products p ON oi.product_id = p.product_id 
        WHERE oi.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch total price from the orders table
$sql = "SELECT total_price, payment_method, payment_status FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

$total_price = $order['total_price']; // Use total price from orders table
$payment_link = "upi://pay?pa=9998332341@ybl&pn=Man Navlakha&mc=0000&mode=02&purpose=00&am={$total_price}";

// Define the directory and file path
$directory = 'qrcodes';
$file_path = "{$directory}/payment_qr.png";

// Create the directory if it doesn't exist
if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

// Generate the QR code and save it to the file path
QRcode::png($payment_link, $file_path, QR_ECLEVEL_L, 10);

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Option</title>
    <link rel="stylesheet" href="../assets/css/btn.css">
    <link rel="stylesheet" href="../assets/css/payment.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        .nav-pills .nav-link.active{

            background-color: #3c0e40;
        }
    </style>
</head>
<body>
               <!-- Payment Options Container -->
               <div class="container py-5">
                <!-- Payment Options Header -->
                <div class="row mb-4">
                    <div class="col-lg-8 mx-auto text-center">
                        <h1 class="display-6">Payment Options</h1>
                    </div>
                </div>
                <!-- Payment Options Body -->
                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <div class="card">
                            <!-- Payment Amount Display -->
                            <!-- <h1><?php echo htmlspecialchars($total_price); ?><span>₹</span></h1> -->
                            <!-- Payment Options Tab Header -->
                            <div class="card-header">
                                <div class="bg-white shadow-sm pt-4 pl-2 pr-2 pb-2">
                                    <!-- Payment Options Tab Navigation -->
                                    <ul role="tablist" class="nav bg-light nav-pills rounded nav-fill mb-3">
                                        <!-- Cash Payment Option -->
                                        <li  class="nav-item">
                                            <a  data-toggle="pill" href="#cash" class="nav-link active">
                                                <i class="fas fa-money-bill-wave-alt mr-2"></i> Cash </a>
                                        </li>
                                        <!-- QR Pay Payment Option -->
                                        <li class="nav-item">
                                            <a data-toggle="pill" href="#qr" class="nav-link">
                                                <i class="fas fa-qrcode mr-2"></i> QR Pay</a>
                                        </li>
                                        <!-- UPI APP Payment Option -->
                                        <li class="nav-item">
                                            <a data-toggle="pill" href="#upi-app" class="nav-link">
                                                <i class="fas fa-mobile-alt mr-2"></i> UPI APP</a>
                                        </li>
                                    </ul>
                                </div>
                        
        <!-- cash -->
<div class="tab-content">
    <div id="cash" class="tab-pane fade show active pt-3">
        <div class="middle-box-2">
            <h3>Product Details</h3>
            <?php 
            /**
             * Loop through each product in the $products array and display its details.
             *
             * @param array $products Array of product information
             * @param float $total_price Total price of all products
             *
             * Example:
             * $products = [
             *     ['brand' => 'Apple', 'model' => 'iPhone', 'product_quantity' => 2],
             *     ['brand' => 'Samsung', 'model' => 'Galaxy', 'product_quantity' => 1]
             * ];
             * $total_price = 1000.00;
             */
            foreach ($products as $product): ?>
                <p>
                    Name: <?php echo htmlspecialchars($product['brand']); ?> <?php echo htmlspecialchars($product['model']); ?><br>
                    Product Quantity: <?php echo htmlspecialchars($product['product_quantity']); ?><br>
                    Total Price: <?php echo htmlspecialchars($total_price); ?>
                </p>
            <?php endforeach; ?>
        </div>
        <form method="post" action="form_cash.php?pd=qr&order_id=<?php echo htmlspecialchars($order_id); ?>&tracking_id=<?php echo htmlspecialchars($tracking_id); ?>" enctype="multipart/form-data">
    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
    <input type="hidden" name="payment_method" value="cash">
    <input type="hidden" name="payment_status" value="waiting">
    <button style="margin:2%;background-color: #1d8b00; border: 1px solid rgba(27, 31, 35, 0.15);color:#fff" type="submit" class="btn btn-trick-new">Confirm Cash Payment</button>
</form>

    </div>
    <!-- end cash -->


                            <div id="qr" class="tab-pane fade pt-3">
                                <main>
                                    <section class="qr_sec_new">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="qr_sec_new">
                                                    <h4>     <img src="../pages/brand/full.svg" id="logo" alt="logo here"></h4>
                                                    <img src="qrcodes/payment_qr.png" alt="Generated QR Code" id="qr11">
                                                    <h1>₹ <?php echo $total_price; ?></h1>
                                                    <div class="upi">
       <img src="https://iili.io/HZsWZrl.png" style="height: 40px;">
       <img src="https://iili.io/HZsWQ14.png"  style="height: 20px;">
       <img src="https://iili.io/HZsWLBf.png"  style="height: 20px;">
       <img src="https://iili.io/HZsW6In.png"  style="height: 20px;">
     </div>
                                                    <p>UPI ID: <strong>9998332341@ybl</strong></p>
                                                    <h1>Scan & Pay</h1>
                                                </div>
                                            </div>  
                                             <form method="post"  action="form.php?pd=qr&order_id=<?php echo htmlspecialchars($order_id); ?>&tracking_id=<?php echo htmlspecialchars($tracking_id); ?>" enctype="multipart/form-data">
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
        <input type="hidden" name="payment_details" value="<?php echo htmlspecialchars($payment_link); ?>">
    <input type="submit"  style="margin:2%;background-color: #1d8b00;
    border: 1px solid rgba(27, 31, 35, 0.15);color:#fff" name="submit_order" value="Submit QR Payment " class="btn btn-trick-new">

    </form>
                                        </div>
                                    </section>
                                </main>
                            
                            </div>
     <!-- upi info -->
     <div id="upi-app" class="tab-pane fade pt-3"
                                >    <script>
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
                                <div id="unsupported-device-message" style="display: none;">
        <h3>UPI App Payment Not Supported on This Device</h3>
        <p>UPI payments can only be processed on mobile devices. Please switch to a mobile device to complete your payment.</p>
    </div>

    <div id="redirect-message">
        <button class="pay" >Pay Now</button>
        <p>If not redirected, <a href="<?php echo htmlspecialchars($payment_link); ?>">click here</a>.</p>
      
    <p><b>Notes:</b> If payment not done it will consider as cash.</p>
    <form method="post"  action="form.php?pd=upi&order_id=<?php echo htmlspecialchars($order_id); ?>&tracking_id=<?php echo htmlspecialchars($tracking_id); ?>" enctype="multipart/form-data">
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
        <input style="margin:2%;background-color: #1d8b00;
    border: 1px solid rgba(27, 31, 35, 0.15);color:#fff" class="btn-trick-new" type="submit" value="Submit mobile Payment">
    </form>
    </div>
                            </div> <!-- End -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
