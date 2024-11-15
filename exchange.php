<?php
session_start();
require_once 'db_connect.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if order_id and user_id are provided
if (!isset($_GET['order_id']) || !isset($_SESSION['user_id'])) {
    die("Order ID or User ID is not provided.");
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// SQL query to fetch order details
$query = "
SELECT 
    O.order_status, 
    O.order_id, 
    O.order_date, 
    O.delivery_date, 
    C.email,
    C.first_name,
    O.total_price, 
    O.shipping_cost, 
    O.payment_method, 
    O.payment_details, 
    O.payment_status,
    P.model AS product_name, 
    V.variation_value, 
    OI.product_quantity,
    OI.product_attributes,
    P.image_main
FROM 
    Orders O 
JOIN 
    Customers C ON O.customer_id = C.customer_id
JOIN 
    Order_Items OI ON O.order_id = OI.order_id 
JOIN 
    Products P ON OI.product_id = P.product_id 
JOIN 
    ProductVariations V ON OI.variation_id = V.variation_id
WHERE 
    O.order_id = ? 
    AND C.user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();

    // Check if payment is completed and order is eligible for an exchange
    if ($order['payment_status'] != "Paid") {
        die("Exchange cannot be processed because the order has not been paid.");
    }

    if ($order['order_status'] != 'delivered') {
        die(' <div class="ab-o-oa" aria-hidden="true">
                    <div class="ZAnhre">
                    <img class="wF0Mmb" src="../assets/not_eligible.svg" width="300px" height="300px" alt=""></div>
                    <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">Not eligible</div>
                    <div class="ab-o-oa-qc-r">Order is not eligible for an exchange product.</div></div>
                </div>
                <style>
                    
.ab-o-oa{
    display: flex;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    align-items: center;
    width: 100%;
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
    font-family: Montserrat, sans-serif;

}
.ab-o-oa-r{
    display: contents;
}
.ab-o-oa-qc-V{
    font-weight :800;

}
.ab-o-oa-qc-r{
    font-weight :normal;

}




                </style>');
    }

    $order_date = new DateTime($order['order_date']);
    $valid_until = $order_date->modify('+7 days');
    $now = new DateTime();

    if ($now > $valid_until) {
        die("Date and time expired. You cannot request an exchange after 7 days.");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handling custom reason for exchange
        $exchange_reason = isset($_POST['custom_reason']) && !empty($_POST['custom_reason']) ? $_POST['custom_reason'] : $_POST['exchange_reason'];
    
        // Setting exchange amount
        $exchange_amount = $order['total_price'];
    
        // Fetch product_name and product_attributes from the $order array
        $product_name = $order['product_name'];
        $product_attributes = $order['product_attributes'];
    
        // Generate a unique tracking ID for the exchange
        $exchange_track = 'BD-EXCH' . strtoupper(uniqid());
    
        // Handle file upload
        $target_dir = "uploads/exchanges/";
        $file_name = basename($_FILES["supporting_files"]["name"]);
        $target_file = $target_dir . $file_name;
    
        if (move_uploaded_file($_FILES["supporting_files"]["tmp_name"], $target_file)) {
            // SQL query to insert exchange request into the database
            $exchange_query = "INSERT INTO exchanage_requests (order_id, customer_id, exchanage_reason, exchanage_amount, product_name, product_attributes, supporting_files, exch_track) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $exchange_stmt = $conn->prepare($exchange_query);
            $exchange_stmt->bind_param("iisdssss", $order_id, $user_id, $exchange_reason, $exchange_amount, $product_name, $product_attributes, $target_file, $exchange_track);
    
            if ($exchange_stmt->execute()) {
                // Generate OTP and send email
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['order_id'] = $order_id;
    
                // Sending OTP via email
                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->AddAddress($order['email']);
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->Host = 'smtp.gmail.com';
                $mail->Username = 'moviesfull808@gmail.com';
                $mail->Password = 'bmwgucgzfluojgea';
                $mail->setFrom('moviesfull808@gmail.com', 'Brand Dilation');
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Exchange OTP Confirmation';
                $mail->Body = "<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta http-equiv='X-UA-Compatible' content='ie=edge' />
    <title>OTP for Your Account Registration</title>

    <link
      href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap'
      rel='stylesheet'
    />
  </head>
  <body
    style='
      margin: 0;
      font-family: Poppins;
      background: #ffffff;
      font-size: 14px;
    '
  >
    <div
      style='
        max-width: 680px;
        margin: 0 auto;
        padding: 45px 30px 60px;
        background: #f4f7ff;
        background-image: url(https://cloud1.email2go.io/faf73b21f308431fb3cf1c58d228eca2/10f7446a5d697c9ff6021de6cbcfd2c2eb145d42963bbb71596357f48f698d41.jpg);
        background-repeat: no-repeat;
        background-size: 800px 452px;
        background-position: top center;
        font-size: 14px;
        color: #434343;
      '
    >
      <header>
        <table style='width: 100%;'>
          <tbody>
            <tr style='height: 0;'>
              <td>
                <img
                  alt=''
                  src='https://cloud1.email2go.io/faf73b21f308431fb3cf1c58d228eca2/a8cf5a81117f1a1e6de3d2b97a7363c0120f2e49b0de4e5c2000ed70c7e50311.png'
                  height='30px'
                />
              </td>
              <td style='text-align: right;'>
                <span
                  style='font-size: 16px; line-height: 30px; color: #ffffff;'
                  ></span
                >
              </td>
            </tr>
          </tbody>
        </table>
      </header>

      <main>
        <div
          style='
            margin: 0;
            margin-top: 70px;
            padding: 92px 30px 115px;
            background: #ffffff;
            border-radius: 30px;
            text-align: center;
          '
        >
          <div style='width: 100%; max-width: 489px; margin: 0 auto;'>
            <h1
              style='
                margin: 0;
                font-size: 24px;
                font-weight: 500;
                color: #1f1f1f;
              '
            >
              Verify Your OTP for Exchange confirmation
            </h1>
            <p
              style='
                margin: 0;
                margin-top: 17px;
                font-size: 16px;
                font-weight: 500;
              '
            >
              Hey $first_name,
            </p>
            <p
              style='
                margin: 0;
                margin-top: 17px;
                font-weight: 500;
                letter-spacing: 0.56px;
              '
            >
             Thank you for choosing Brand Dilation.
To verify your exchange, please enter the following OTP:
            </p>
            <p
              style='
                margin: 0;
                margin-top: 60px;
                font-size: 25px;
                font-weight: 600;
                letter-spacing: 25px;
                color: #ba3d4f;
              '
            >
            $otp
            </p>
               <p
              style='
                margin: 0;
                margin-top: 17px;
                font-weight: 500;
                letter-spacing: 0.56px;
              '
            >
            This code is valid for <span style='font-weight: 600; color: #1f1f1f;'>10 minutes</span>.
For security reasons, please do not share this OTP with anyone.
            </p>
          </div>
        </div>

        <p
          style='
            max-width: 400px;
            margin: 0 auto;
            margin-top: 90px;
            text-align: center;
            font-weight: 500;
            color: #8c8c8c;
          '
        >
          Need help? 

          <a
            href='http://192.168.59.24/pages/help.html'
            style='color: #3c0e40; text-decoration: none;'
            >Contact us</a
          >
          or visit our
          <a
            href='http://192.168.59.24/pages/help.html'
            target='_blank'
            style='color: #3c0e40; text-decoration: none;'
            >Help Center</a
          >
        </p>
      </main>

      <footer
        style='
          width: 100%;
          max-width: 490px;
          margin: 20px auto 0;
          text-align: center;
          border-top: 1px solid #e6ebf1;
        '
      >
        <p
          style='
            margin: 0;
            margin-top: 40px;
            font-size: 16px;
            font-weight: 600;
            color: #434343;
          '
        >
         Brand Dilation
        </p>
        <p style='margin: 0; margin-top: 8px; color: #434343;'>

<!-- adderss -->

        </p>
        <div style='margin: 0; margin-top: 16px;'>
             <a href='' target='_blank' style='display: inline-block;'>
            <img
              width='36px'
              alt='Facebook'
              src='https://cloud1.email2go.io/faf73b21f308431fb3cf1c58d228eca2/8c61920aa90a066e2bfc76edc58c25a657c8ae2e05fe661c0c0c54df0c616439.png'
            />
          </a>
          <a
            href=''
            target='_blank'
            style='display: inline-block; margin-left: 8px;'
          >
            <img
              width='36px'
              alt='Instagram'
              src='https://cloud1.email2go.io/faf73b21f308431fb3cf1c58d228eca2/17dd79517d9c460c8ecce4fec7aa3f8fc0102bc0b270a72e483025d856ecadfb.png'
          /></a>
             <a
            href=''
            target='_blank'
            style='display: inline-block; margin-left: 8px;'
          >
            <img
              width='36px'
              alt='LinkedIn'
              src='https://cloud1.email2go.io/faf73b21f308431fb3cf1c58d228eca2/476ff11dac6413e039596bf97972a9090d00e90301a650d78deaf58d0e9ce0e2.png'
          /></a>
          <a
            href=''
            target='_blank'
            style='display: inline-block; margin-left: 8px;'
          >
            <img
              width='36px'
              alt='Twitter'
              src='https://cloud1.email2go.io/faf73b21f308431fb3cf1c58d228eca2/4692459cd351593c2e4ae1b76c27aadfc816f4d534fdb2f1d290146486d62d44.png'
            />
          </a>
          <a
            href=''
            target='_blank'
            style='display: inline-block; margin-left: 8px;'
          >
            <img
              width='36px'
              alt='Youtube'
              src='https://cloud1.email2go.io/faf73b21f308431fb3cf1c58d228eca2/fda3c7b366489e4e9c05461b1c255377841df34b932b86d718ef0fc6dd602982.png'
          /></a>
        </div>
        <p style='margin: 0; margin-top: 16px; color: #434343;'>
          Copyright © 2024 Brand Dilation. All rights reserved.
        </p>
      </footer>
    </div>
  </body>
</html>";
    
                if ($mail->Send()) {
                    // Redirect to OTP confirmation page
                    header("Location: ../confirm_exchange_otp.php");
                    exit();
                } else {
                    die("Failed to send OTP email: " . $mail->ErrorInfo);
                }
            } else {
                echo "Failed to submit exchange request.";
            }
        } else {
            echo "File upload failed.";
        }
    }
     else {
        // Display the form for exchange request
        echo '
        
<h2>Exchange Request Form</h2>
<p></p>
<div class="row">
  <div class="col-75">
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
        
        <div class="row">
          <div class="col-50">
            <h4>You are requesting for order id #' . htmlspecialchars($order['order_id']) . '.</h4>
            <label for="exchange_reason">Reason for exchange:</label>
            <select name="exchange_reason" id="exchange_reason" onchange="handleCustomReason(this)" required>
                <option value="" disabled selected>Select the reason</option>
                <option value="Product was mismatch/different product/color">Product was mismatch/different product/color</option>
                <option value="Buy this product by mistake">Buy this product by mistake</option>
                <option value="Wrong product shipped">Wrong product shipped</option>
                <option value="Item does not match the description">Item does not match the description</option>
                <option value="Defective">Defective</option>
                <option value="Fraud">Fraud</option>
                <option value="Change of mind">Change of mind</option>
                <option value="Incorrect product size">Incorrect product size</option>
                <option value="Not as described/expected">Not as described/expected</option>
                <option value="Product no longer required">Product no longer required</option>
                <option value="custom">Custom Reason</option>
            </select>
            
            <!-- Hidden input for custom reason -->
            <input type="text" name="custom_reason" id="custom_reason" style="display: none;" placeholder="Enter your custom reason" />
            <label for="supporting_files">Upload Supporting Files:</label>
            <input type="file" name="supporting_files" id="supporting_files" required>
            <h4>Product Deatils</h4>
            <label for="exchange_image">Attach image of the product:</label>
            <img width="100px" src="' . htmlspecialchars($order['image_main']) . '">
            <label for="exchange_amount">Product Name:</label>
            <input type="text" disabled value="' . htmlspecialchars($order['product_name']) . '" name="product_name" id="product_name">
            <label for="exchange_amount">Product Attribute:</label>
            <input type="text" disabled value="' . htmlspecialchars($order['product_attributes']) . '" name="product_attributes" id="product_attributes">

            <label for="exchange_amount">Exchange Amount:</label>
            <input type="number" disabled value="' . htmlspecialchars($order['total_price']) . '" name="exchange_amount" id="exchange_amount">   </div>

    
        </div>
        <label>
          <input type="checkbox" required  checked="checked" name="sameadr"> I agree your <a href="">T&C</a> for Privacy and Polices.
        </label>
        <input type="submit" value="Submit Exchange Request" class="btn">
      </form>
    </div>
  </div>
        </form>';
    }
} else {
    die("Order not found or does not belong to you.");
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exchange Request Form</title>

    <link rel="stylesheet" href="../assets/css/red_exc.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script>
function handleCustomAddress(select) {
    const customAddressInput = document.getElementById('custom_reason');
    if (select.value === 'custom') {
        customAddressInput.style.display = 'block';
    } else {
        customAddressInput.style.display = 'none';
    }
}
</script>
</head>
<body>
    
</body>
</html>
