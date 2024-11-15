<?php
session_start();
include 'db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Retrieve `order_id` and `tracking_id` from GET parameters
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : '';

// Function to get email and first_name by tracking_id
function getCustomerInfoByTrackingId($tracking_id, $conn) {
    $email = $first_name = null;

    // Query to find customer_id using tracking_id
    $stmt = $conn->prepare("SELECT customer_id FROM orders WHERE tracking_id = ?");
    $stmt->bind_param("s", $tracking_id);
    $stmt->execute();
    $stmt->bind_result($customer_id);
    
    if ($stmt->fetch()) {
        // Query to find email and first_name using customer_id
        $stmt->close();
        $stmt = $conn->prepare("SELECT email, first_name FROM customers WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $stmt->bind_result($email, $first_name);
        
        if ($stmt->fetch()) {
            // Success, return email and first_name
            return array('email' => $email, 'first_name' => $first_name);
        } else {
            // Customer not found
            return array('error' => 'Customer not found');
        }
    } else {
        // Tracking_id not found
        return array('error' => 'Tracking ID not found');
    }

    // Close the statement
    $stmt->close();
}

// Get customer info using the tracking_id
$customerInfo = getCustomerInfoByTrackingId($tracking_id, $conn);

// Check if there was an error
if (isset($customerInfo['error'])) {
    echo $customerInfo['error'];
} else {
    $email = $customerInfo['email'];
    $name = $customerInfo['first_name'];

    // Clear the cart session
    unset($_SESSION['cart']);
    
    // Send email notification
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'moviesfull808@gmail.com'; // SMTP username
        $mail->Password   = 'bmwgucgzfluojgea'; // SMTP password (use app-specific password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('moviesfull808@gmail.com', 'Brand Dilation');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Thank You! For Your Order';
        $mail->Body    = "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8' />
<meta name='viewport' content='width=device-width, initial-scale=1.0' />
<meta http-equiv='X-UA-Compatible' content='ie=edge' />
<title>Your order successfully placed</title>
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
     alt='logo'
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
margin-top: 60px;
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
Your order successfully placed
</h1>
<p
 style='
   margin: 0;
   margin-top: 17px;
   font-size: 16px;
   font-weight: 500;
 '
>
 Hey $name,
</p>
<p
 style='
   margin: 0;
   margin-top: 17px;
   font-weight: 500;
   letter-spacing: 0.56px;
 '
>
 Thank you for choosing Brand Dilation. If this wasn't you, please <a href='http://192.168.59.24/pages/help.html'>contact support</a> immediately.
</p>
<p onclick='location.href = 'track_order.php?tracking_id=$tracking_id';'
 style='
   margin: 0;
   margin-top: 60px;
   font-size: 25px;
   font-weight: 600;
   color: #39a132;
 '
>
 <img onclick='location.href = 'track_order.php?tracking_id=$tracking_id';'
     alt=''
     src='https://cloud1.email2go.io/faf73b21f308431fb3cf1c58d228eca2/3cafdfbb8d16ca5854178352771cdda5bb02c7702cb2b4b02a377615ddd52696.png'
     height='250px'
   />
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

<!-- address -->

</p>
<div style='margin: 0; margin-top: 16px;'>    <a href='' target='_blank' style='display: inline-block;'>
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

        $mail->send();
    } catch (Exception $e) {
        $error_message = "Order recived, but email notification could not be sent. Mailer Error: {$mail->ErrorInfo}";
        echo $error_message;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
<script src="../assets/js/internet-check.js" defer></script>
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="thank_you.css">
	<link href='https://fonts.googleapis.com/css?family=Lato:300,400|Montserrat:700' rel='stylesheet' type='text/css'>
	<style>
		@import url(//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css);
		@import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
      @font-face {
    font-family: 'bd title';
    /* src: url('../assets/font/bd title.woff2') format('woff2'); */
    src: url('../assets/font/pp.woff2') format('woff2');
    
  }
  @font-face {
    font-family: 'bd';
    /* src: url('../assets/font/bd title.woff2') format('woff2'); */
    src: url('../assets/font/BD.woff2') format('woff2');
    
  }
      h1{
    font-family: bd title, sans-serif !important;

}

p strong{
  padding: 1%;
    background: #e3e3e3;
    border-radius: 10px;
    font-family: bd, sans-serif !important;
}
	</style>
	<link rel="stylesheet" href="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/default_thank_you.css">
	<script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/jquery-1.9.1.min.js"></script>
	<script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/html5shiv.js"></script>
</head>
<body>


<header  class="site-header" id="header">
	
		<h1 class="site-header__title" data-lead-id="site-header-title">Thank You for Your Order!</h1>
	</header>


<div class="main-content">
		<i class="fa fa-check main-content__checkmark" id="checkmark"></i>
		<p class="main-content__body" data-lead-id="main-content-body">Your order has been placed successfully. </p>
        <p>Your tracking ID is <br><br><strong><?php echo $tracking_id; ?></strong>.</p>

<button onclick="location.href = 'track_order.php?tracking_id=<?php echo $tracking_id; ?>';" id="bottone1"><strong>Track Your Order</strong></button>
<button style="background: #ff6666;" onclick="location.href = 'index.php'" id="bottone1"><svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                <path
                    d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z">
                </path>
            </svg><strong>Home</strong></button>

		<style>
	
#bottone1>svg {
    margin-right: 5px;
    margin-left: 5px;
    font-size: 20px;
    transition: all 0.4s ease-in;
}
#bottone1:hover>svg {
    font-size: 1.2em;
    transform: translateX(-5px);
}

    #bottone1 {
 padding-left: 33px;
 padding-right: 33px;
 padding-bottom: 16px;
 padding-top: 16px;
 border-radius: 9px;
 background: #d5f365;
 border: none;
 font-family: inherit;
 text-align: center;
 margin-bottom: 5%;
 cursor: pointer;
 transition: 0.4s;
}

#bottone1:hover {
 box-shadow: 7px 5px 56px -14px #C3D900;
}

#bottone1:active {
 transform: scale(0.97);
 box-shadow: 7px 5px 56px -10px #C3D900;
}
    </style>
  <script>
  // Prevent the back button from navigating to the previous page
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.back(1);
    history.go(1);
};

</script>
</body>
</html>
