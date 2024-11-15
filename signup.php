<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client; // Twilio SDK

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';
require 'vendor/autoload.php'; // Twilio autoload

include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];

    // Check if username or email already exists
    $check_query = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $check_query->bind_param("ss", $username, $email);
    $check_query->execute();
    $check_query->store_result();

    if ($check_query->num_rows > 0) {
        $_SESSION['error_message'] = "Username or Email already exists. Please choose a different one.";
        header("Location: signup.php");
        exit();
    }

    $check_query->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate a 6-digit OTP for email and SMS
    $email_otp = mt_rand(100000, 999999);
    $sms_otp = mt_rand(100000, 999999);
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes")); 

    // Insert user data with OTPs
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, first_name, last_name, phone_number,email_otp, email_otp_expiry, sms_otp, sms_otp_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $username, $email, $hashed_password, $first_name, $last_name, $phone_number, $email_otp, $otp_expiry, $sms_otp, $otp_expiry);

    if ($stmt->execute()) {
        // Get the user ID
        $user_id = $stmt->insert_id;

        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            // Set up mail server details
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'moviesfull808@gmail.com'; // SMTP username
            $mail->Password   = 'bmwgucgzfluojgea'; // SMTP password (use app-specific password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587; 

            // Recipients
            $mail->setFrom('moviesfull808@gmail.com', 'Brand Dilation');
            $mail->addAddress($email); 

            // Content
            $mail->isHTML(true); 
            $mail->Subject = 'OTP for Your Account Registration';
            $mail->Body    = "
            <!DOCTYPE html>
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
              Verify Your OTP for verfication
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
              Thank you for choosing Brand Dilation. Use the following OTP
              to complete the process of verification of your email address. OTP is
              valid for
              <span style='font-weight: 600; color: #1f1f1f;'>10 minutes</span>.
              Do not share this code with others, including Brand Dilation's
              employees.
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
            $email_otp
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
</html>
";

            $mail->send();

        } catch (Exception $e) {
            $_SESSION['error_message'] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            header("Location: signup.php");
            exit();
        }

        // Send SMS OTP
        try {
            $twilio_sid = 'ACf2e6f2830ff88f68f75ea2476c30bc89'; 
            $twilio_token = '0459762b4b960b2f50faec34a363328a'; 
            $twilio_number = '+19289853374'; // Replace with your Twilio number

            $client = new Client($twilio_sid, $twilio_token);
            $message = $client->messages->create(
                $phone_number, 
                [
                    'from' => $twilio_number,
                    'body' => "Your OTP for Brand Dilation is: $sms_otp. It is valid for 10 minutes. Please do not share with anyone.
                    Thanks,
                    Brand Dilation Team."
                ]
            );

            // Check if the SMS was sent successfully
            if ($message->sid) {
                $_SESSION['success_message'] = "Registration successful! An OTP has been sent to your email and phone.";
                header("Location: verify_otp.php?user_id=$user_id");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "SMS could not be sent. Error: {$e->getMessage()}";
            header("Location: signup.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Registration failed. Please try again.";
        header("Location: signup.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  
<script src="../assets/js/internet-check.js" defer></script>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="./s_l.css">
</head>

<body>
    <div class="container_login">
        <div class="header_login">
            <a href="../index.php"><img src="../assets/img/64-bd-t.png" alt="logo" width="70px" height="70px"><span></span></a>
            <h1>Sign up to <em id="brand">Brand</em> <em id="dilation">Dilation</em></h1>
        </div>
<br>
<?php
if (isset($_SESSION['error_message'])) {
    echo "<div style='color: red;'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}
?>
        <form action="signup.php" method="post">
            <div class="form-group">
                <input placeholder="username" type="text" name="username" id="username" required class="inputField">
            </div>
            <div class="form-group">
            <input placeholder="Email"  type="email" id="email" name="email" required class="inputField">
            </div>
            <div class="form-group">
            <input placeholder="Password" type="Password" id="password" name="password" required class="inputField">
            </div>
            <div class="form-group">
            <input placeholder="First Name" type="text" id="first_name" name="first_name" required class="inputField">
            </div>
            <div class="form-group">
                <input placeholder="Last Name" type="text" id="last_name" name="last_name" required class="inputField">
            </div>
            <div class="form-group">
                <input placeholder="Phone Number" type="text" id="phone_number" name="phone_number" required class="inputField">
            </div>
            <button type="submit" class="submitButton">Sign Up</button>
        </form>

        <div class="footer">
            <a href="../forget_password.php">Forgot password?</a>
            <span>•</span>
            <a href="../login.php">Log in now</a>
        </div>

    </div>
</body>

</html>