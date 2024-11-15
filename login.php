
<?php
session_start();
include 'db_connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']) ? $_POST['remember_me'] : false;

    // Check user credentials
    $stmt = $conn->prepare("SELECT user_id, email, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $email, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // Set session
            $_SESSION['user_id'] = $user_id;

            // If "Remember Me" is checked, set a cookie
            if ($remember_me) {
                $cookie_name = "user_id";
                $cookie_value = $user_id;
                // Set cookie to expire in 30 days
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); 
            }

              // Setup PHPMailer
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

                 // Function to get the user's browser
function getUserBrowser() {
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  if (strpos($user_agent, 'Chrome') !== false) return 'Chrome';
  elseif (strpos($user_agent, 'Firefox') !== false) return 'Firefox';
  elseif (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident/7') !== false) return 'Internet Explorer';
  elseif (strpos($user_agent, 'Safari') !== false) return 'Safari';
  else return 'Unknown';
}

// Function to get the user's OS
function getUserOS() {
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  if (preg_match('/linux/i', $user_agent)) return 'Linux';
  elseif (preg_match('/macintosh|mac os x/i', $user_agent)) return 'Mac';
  elseif (preg_match('/windows|win32/i', $user_agent)) return 'Windows';
  else return 'Unknown';
}

// Function to get the user's location based on IP address
function getUserLocation() {
  // This is a placeholder. To get the actual location, you can use a service like IP Geolocation API.
  $ip = $_SERVER['REMOTE_ADDR'];
  // Optionally, you could use a service like ipinfo.io, geoplugin.net, etc.
  return 'Location not available';
}

function getLocationByIP($ip) {
  if ($ip == 'localhost') {
      return 'Localhost';
  }

  // API URL (replace with your preferred service)
  $url = "http://ipinfo.io/{$ip}/json";
  
  // Send a request to the API
  $response = file_get_contents($url);

  // Convert JSON response to an array
  $data = json_decode($response, true);

  // Extract location details
  if (isset($data['city']) && isset($data['region']) && isset($data['country'])) {
      $location = $data['city'] . ', ' . $data['region'] . ', ' . $data['country'];
  } else {
      $location = 'Location not available';
  }

  return $location;
}


// Get the user's IP address
function getUserIP() {
  $ip = '';

  // Check if it's a shared internet/forwarding IP
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
  }
  // Check if IP is passed from a proxy
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  // Fallback to the remote address
  else {
      $ip = $_SERVER['REMOTE_ADDR'];
  }

  // If the IP is localhost (IPv6 or IPv4), set it to something more appropriate for display
  if ($ip === '::1' || $ip === '127.0.0.1') {
      $ip = 'localhost';
  }

  return $ip;
}




                

    // Get user details
    // $location = getUserLocation();
    $os = getUserOS();
    $browser = getUserBrowser();
    $ip_address = getUserIP();
    // Usage
$ip = getUserIP();
$location = getLocationByIP($ip);
  
                  // Content
                  $mail->isHTML(true);
                  $mail->Subject = 'Login Alert';
                  $mail->Body    = "<!DOCTYPE html>
  <html lang='en'>
    <head>
      <meta charset='UTF-8' />
      <meta name='viewport' content='width=device-width, initial-scale=1.0' />
      <meta http-equiv='X-UA-Compatible' content='ie=edge' />
      <title>Log in</title>

  
      <link
        href='https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap'
        rel='stylesheet'
      />
    </head>
    <body
      style='
        margin: 0;
        font-family: Montserrat;
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
               Log in Alert
              </h1>
              <p
                style='
                  margin: 0;
                  margin-top: 17px;
                  font-size: 16px;
                  font-weight: 500;
                '
              >
                Hey $username,
              </p>
              <p
                style='
                  margin: 0;
                  margin-top: 17px;
                  font-weight: 500;
                  letter-spacing: 0.56px;
                '
              >
                Thank you for choosing Brand Dilation. Your id login. alert If this wasn't you, please <a href='http://192.168.59.24/pages/help.html'>contact support</a> immediately. after that just <a href='http://192.168.59.24/change_password.php'>chnage your password</a>.
              </p>
              <p
                style='
                  margin: 0;
                  margin-top: 60px;
                  font-size: 25px;
                  font-weight: 600;
                  color: #ba3d4f;
                '
              >
              Login Details
        <p>IP*: {$ip_address}</p>
        <p>Location*: {$location}</p>
        <p>OS: {$os}</p>
        <p>Browser: {$browser}</p>
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
                  echo 'Login successful! Email notification sent.';
              } catch (Exception $e) {
                  echo "Login successful! But the email notification could not be sent. Mailer Error: {$mail->ErrorInfo}";
              }
   // Redirect to the previous page (if exists) or a default page
   $redirectTo = isset($_SESSION['redirect_to']) ? $_SESSION['redirect_to'] : 'index.php';
   unset($_SESSION['redirect_to']); // Clear the session
  
   header("Location: $redirectTo");
   exit(); // Stop further execution
  } else {
              echo "Invalid password.";
          }
    } else {
        echo "No user found with that username.";
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
    <script src="https://kit.fontawesome.com/1379690e97.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container_login">
        <div class="header_login">
            <a href="../index.php"><img src="../assets/img/64-bd-t.png" alt="logo" width="70px" height="70px"><span></span></a>
            <h1>Log in to <em id="brand">Brand</em> <em id="dilation">Dilation</em></h1>
        </div>
        <form action="login.php" method="post">
  <div class="form-group">
    <input placeholder="username" type="text" name="username" id="username" required class="inputField" autocomplete="off">
  </div>
  <div class="form-group">
    <input placeholder="password" type="password" name="password" id="password" required class="inputField" autocomplete="off">
    <span class="toggle-password" toggle="#password-field">Show</span>
  </div>

  <div class="form-group">
        <label>
            <input type="checkbox" name="remember_me" value="1"> Keep me logged in on this System</label>
  </div>
  <button type="submit" class="submitButton">Log in</button>
</form>


        <div class="footer">
            <a href="../forget_password.php">Forgot password?</a>
            <span>•</span>
            <a href="../signup.php">Sign up now</a>
        </div>
    </div>
    
    <script>

const togglePassword = document.querySelector('.toggle-password');
const passwordField = document.querySelector('#password');

togglePassword.addEventListener('click', function() {
  // Toggle the password field type
  const type = passwordField.type === 'password' ? 'text' : 'password';
  passwordField.setAttribute('type', type);

  // Toggle the text content of the toggle element
  this.textContent = type === 'password' ? 'Show' : 'Hide';
});



</script>
</body>
</html>
