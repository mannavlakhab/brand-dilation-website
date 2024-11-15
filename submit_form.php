<?php
// Database connection parameters

// Include the database connection file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';
$servername = "localhost";
$username = "root";
$password = "";
$database = "shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind SQL statement
    $stmt = $conn->prepare("INSERT INTO RentForm (name, companyName, phoneNumber, email, systemType, processor, RAM, SSD, quantity, duration) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiiii", $name, $companyName, $phoneNumber, $email, $systemType, $processor, $RAM, $SSD, $quantity, $duration);

    // Set parameters
    $name = $_POST["name"];
    $companyName = $_POST["companyName"];
    $phoneNumber = $_POST["phoneNumber"];
    $email = $_POST["email"];
    $systemType = $_POST["systemType"];
    $processor = $_POST["processor"];
    $RAM = $_POST["RAM"];
    $SSD = $_POST["SSD"];
    $quantity = $_POST["quantity"];
    $duration = $_POST["duration"];

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "Form submitted successfully!";
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
             $mail->Subject = 'Business Inquiry Submission';
             $mail->Body    = "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8' />
<meta name='viewport' content='width=device-width, initial-scale=1.0' />
<meta http-equiv='X-UA-Compatible' content='ie=edge' />
<title>Business Inquiry</title>
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
     Your inquiry has been submitted
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
    <p
      style='
        margin: 0;
        margin-top: 60px;
        font-size: 25px;
        font-weight: 600;
        color: #39a132;
      '
    >
   Business Inquiry Submited Successfully
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

             $mail->send();
         } catch (Exception $e) {
             $error_message = "Password changed, but email notification could not be sent. Mailer Error: {$mail->ErrorInfo}";
         }
    } else {
        echo "Error submitting form: " . $conn->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="shop.php">k</a>
</body>
</html>