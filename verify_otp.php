<?php
session_start(); // Start the session at the beginning
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client; // Include Twilio SDK
require 'vendor/autoload.php'; // This should be the only include for PHPMailer

include 'db_connect.php';

$user_id = $_GET['user_id'];

// Check the verification status at the beginning
$stmt = $conn->prepare("SELECT email_verified, sms_verified FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email_verified, $sms_verified);
$stmt->fetch();
$stmt->close();

// Redirect if already verified for both OTPs
if ($email_verified == 1 && $sms_verified == 1) {
    echo "<p>You are already a verified member of Brand Dilation.</p>";
    $_SESSION['user_id'] = $user_id; // Set session
    $_SESSION['logged_in'] = true;
    header("Location: index.php"); // Redirect to a logged-in page (index.php)
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verify_otp'])) {
        $otp = $_POST['otp'];
        $otp_type = $_POST['otp_type']; // Determine if it's email or SMS

        // Select the right column for OTP verification
        $otp_column = $otp_type === 'email' ? 'email_otp' : 'sms_otp';
        $otp_expiry_column = $otp_type === 'email' ? 'email_otp_expiry' : 'sms_otp_expiry';
        $verified_column = $otp_type === 'email' ? 'email_verified' : 'sms_verified';

        $stmt = $conn->prepare("SELECT $otp_column, $otp_expiry_column FROM users WHERE user_id = ? AND $otp_column = ?");
        $stmt->bind_param("is", $user_id, $otp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_otp, $otp_expiry);
            $stmt->fetch();

            // Check if OTP is still valid (not expired)
            if (new DateTime() < new DateTime($otp_expiry)) {
                echo "OTP verified successfully!";

                // Update user status to verified for the specific OTP type
                $update_stmt = $conn->prepare("UPDATE users SET $otp_column = NULL, $otp_expiry_column = NULL, $verified_column = 1 WHERE user_id = ?");
                $update_stmt->bind_param("i", $user_id);
                $update_stmt->execute();
                $update_stmt->close();

                // Check if both email and SMS are verified
                $check_verification_stmt = $conn->prepare("SELECT email_verified, sms_verified FROM users WHERE user_id = ?");
                $check_verification_stmt->bind_param("i", $user_id);
                $check_verification_stmt->execute();
                $check_verification_stmt->bind_result($email_verified, $sms_verified);
                $check_verification_stmt->fetch();
                $check_verification_stmt->close();

                if ($email_verified == 1 && $sms_verified == 1) {
                    // Both OTPs verified, set `is_verified` to true
                    $update_is_verified_stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE user_id = ?");
                    $update_is_verified_stmt->bind_param("i", $user_id);
                    $update_is_verified_stmt->execute();
                    $update_is_verified_stmt->close();

                    // Set session for auto login
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['logged_in'] = true;

                    // Both OTPs verified, redirect to dashboard or index
                    header("Location: index.php");
                    exit();
                } else {
                    // Waiting for the other OTP verification
                    echo "Waiting for the other OTP verification.";
                }
            } else {
                echo 'OTP has expired.';
            }
        } else {
            echo "Invalid OTP. Please try again.";
        }

        $stmt->close(); // Close statement after OTP verification
    // Resend OTP logic remains the same 
        }elseif (isset($_POST['resend_otp'])) {
    // Resend OTP logic remains the same
    $otp = mt_rand(100000, 999999);
    $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    $otp_type = $_POST['otp_type']; 
    $otp_column = $otp_type === 'email' ? 'email_otp' : 'sms_otp';
    $otp_expiry_column = $otp_type === 'email' ? 'email_otp_expiry' : 'sms_otp_expiry';

    $update_stmt = $conn->prepare("UPDATE users SET $otp_column = ?, $otp_expiry_column = ? WHERE user_id = ?");
    $update_stmt->bind_param("ssi", $otp, $otp_expiry, $user_id);

    if ($update_stmt->execute()) {
        // Resend email or SMS based on user choice
        $stmt = $conn->prepare("SELECT email, first_name, phone_number FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($email, $first_name, $phone_number);
        $stmt->fetch();
        $stmt->close();

        if ($otp_type === 'email') {
            // Send OTP via email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'moviesfull808@gmail.com';
                $mail->Password = 'bmwgucgzfluojgea';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('moviesfull808@gmail.com', 'Brand Dilation');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Resend OTP for Your Account Registration';
                $mail->Body = "

<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset='UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <meta http-equiv='X-UA-Compatible' content='ie=edge' />
    <title>OTP for Your Account Registration (resended)</title>

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
              Verify Youe OTP for verfication (resended)
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
            $otp
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
                echo "A new OTP has been sent to your email.";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            // Send OTP via SMS
            $twilio_sid = 'ACf2e6f2830ff88f68f75ea2476c30bc89';
            $twilio_token = '0459762b4b960b2f50faec34a363328a';
            $twilio_number = '+19289853374';

            $client = new Client($twilio_sid, $twilio_token);
            try {
                $client->messages->create(
                    $phone_number,
                    [
                        'from' => $twilio_number,
                        'body' => "Your OTP is: $otp.
                        do not share with any one."
                    ]
                );
                echo "A new OTP has been sent to your phone.";
            } catch (Exception $e) {
                echo "Error sending SMS: " . $e->getMessage();
            }
        }
    } else {
        echo "Error: " . $update_stmt->error;
    }

    $update_stmt->close();
}
}
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" type="text/css" href="./s_l.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify OTP</title>
</head>
<body>
<div class="container_login">
    <div class="header_login">
        <a href="../index.php"><img src="../assets/img/64-bd-t.png" alt="logo" width="70px" height="70px"><span></span></a>
        <h1>Verify OTP <em id="brand">Brand</em> <em id="dilation">Dilation</em></h1>
    </div>
    <form action="verify_otp.php?user_id=<?php echo $user_id; ?>" method="post">
        <div class="form-group">
            <input placeholder="OTP HERE" type="text" id="otp" name="otp" required class="inputField" autocomplete="off">
            <select name="otp_type" required>
                <option value="email">Email</option>
                <option value="sms">SMS</option>
            </select>
        </div>
        <button type="submit" name="verify_otp" class="submitButton">Verify OTP</button>
    </form>
    <div class="footer">
        <form action="verify_otp.php?user_id=<?php echo $user_id; ?>" method="post">
            <input type="hidden" name="otp_type" value="email">
            <button type="submit" name="resend_otp" class="submitButton">Resend OTP (Email)</button>
        </form>
        <form action="verify_otp.php?user_id=<?php echo $user_id; ?>" method="post">
            <input type="hidden" name="otp_type" value="sms">
            <button type="submit" name="resend_otp" class="submitButton">Resend OTP (SMS)</button>
        </form>
    </div>
</div>

</body>
</html>
