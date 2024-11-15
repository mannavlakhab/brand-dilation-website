<?php
session_start();
require_once 'db_connect.php';


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

function maskEmail($email) {
  // Split email into the username and domain parts
  list($username, $domain) = explode("@", $email);
  
  // Get the first 3 characters of the username
  $usernameMasked = substr($username, 0, 3);
  
  // Add asterisks to mask the middle part
  $usernameMasked .= str_repeat("*", strlen($username) - 6); // Hide the middle part
  
  // Append the last 3 characters of the username (or the remainder if shorter)
  $usernameMasked .= substr($username, -3);

  // Return the masked email
  return $usernameMasked . "@" . $domain;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_otp = $_POST['otp'];

    if ($entered_otp == $_SESSION['otp']) {
        $order_id = $_SESSION['order_id'];

        // Update the order status to 'exchanage Requested'
        $update_query = "UPDATE orders SET order_status = 'exchanage under process' WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $order_id);
        $update_stmt->execute();

        // Update exchanage_requests table
        $update_exchanage_query = "UPDATE exchanage_requests SET otp_verified = 1 WHERE order_id = ?";
        $update_exchanage_stmt = $conn->prepare($update_exchanage_query);
        $update_exchanage_stmt->bind_param("i", $order_id);
        $update_exchanage_stmt->execute();

        // Clear OTP session data
        unset($_SESSION['otp']);
        unset($_SESSION['order_id']);

        // Display message and redirect
        echo '
        <p>exchanage request has been submitted for order ID: ' . htmlspecialchars($order_id) . '</p>
        <p>You will be redirected to the home page in <span id="countdown">5</span> seconds.</p>
        <script>
            var countdown = 5;
            var countdownElement = document.getElementById("countdown");
            var interval = setInterval(function() {
                countdown--;
                countdownElement.textContent = countdown;
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.location.href = "index.php"; // Redirect to home page
                }
            }, 1000);
        </script>';

    } else {
        echo "Invalid OTP. Please try again.";
        echo '<button class="btn-trick-new" onclick="history.back()">Go Back</button>';

    }
} else {
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>OTP verfication</title>
        <style>
        
        /* Reset default styles */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* Global styles */
body {
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f0f0f0;
}

.otp-form {
    background-color: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 400px;
    width: 100%;
}

h2 {
    margin-bottom: 20px;
    color: #333;
}

/* OTP input styles */
.otp-container, .email-otp-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.otp-input, .email-otp-input {
    width: 40px;
    height: 40px;
    text-align: center;
    font-size: 18px;
    margin: 0 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
    outline: none;
    transition: border-color 0.3s;
}

.otp-input:focus, .email-otp-input:focus {
    border-color: #007bff;
}

#verificationCode,
#emailverificationCode {
    width: 100%;
    margin-top: 15px;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    outline: none;
    transition: border-color 0.3s;
}

#verificationCode:focus,
#emailverificationCode:focus {
    border-color: #007bff;
}
.email-otp {
    margin-top: 25px;
}
/* Button styles */
button {
    margin-top: 15px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #0056b3;
}


</style>
    </head>
    <body>
    <body class="container-fluid bg-body-tertiary d-block">

    <div class="otp-form">
      
     
      <!-- Email OTP Form -->
      <form method="post" action="confirm_exchange_otp.php" class="email-otp">
          <h2>Email OTP</h2>
          <div class="email-otp-container">
              <!-- Six input fields for OTP digits -->
              <input type="text" class="email-otp-input" pattern="\d" maxlength="1">
              <input type="text" class="email-otp-input" pattern="\d" maxlength="1" disabled>
              <input type="text" class="email-otp-input" pattern="\d" maxlength="1" disabled>
              <input type="text" class="email-otp-input" pattern="\d" maxlength="1" disabled>
              <input type="text" class="email-otp-input" pattern="\d" maxlength="1" disabled>
              <input type="text" class="email-otp-input" pattern="\d" maxlength="1" disabled>
          </div>
          
          <!-- Field to display entered OTP -->
          <input type="hidden" id="emailverificationCode"  type="text" name="otp" required placeholder="Enter verification code" readonly>
          
          <!-- Button to verify OTP -->
          <button type="submit" id="verifyEmailOTP">VERIFY &amp; PROCEED</button>
      </form>
    </div>

        <script>document.addEventListener("DOMContentLoaded", function () {
  var otpInputs = document.querySelectorAll(".otp-input");
  var emailOtpInputs = document.querySelectorAll(".email-otp-input");

  function setupOtpInputListeners(inputs) {
    inputs.forEach(function (input, index) {
      input.addEventListener("paste", function (ev) {
        var clip = ev.clipboardData.getData("text").trim();
        if (!/^\d{6}$/.test(clip)) {
          ev.preventDefault();
          return;
        }

        var characters = clip.split("");
        inputs.forEach(function (otpInput, i) {
          otpInput.value = characters[i] || "";
        });

        enableNextBox(inputs[0], 0);
        inputs[5].removeAttribute("disabled");
        inputs[5].focus();
        updateOTPValue(inputs);
      });

      input.addEventListener("input", function () {
        var currentIndex = Array.from(inputs).indexOf(this);
        var inputValue = this.value.trim();

        if (!/^\d$/.test(inputValue)) {
          this.value = "";
          return;
        }

        if (inputValue && currentIndex < 5) {
          inputs[currentIndex + 1].removeAttribute("disabled");
          inputs[currentIndex + 1].focus();
        }

        if (currentIndex === 4 && inputValue) {
          inputs[5].removeAttribute("disabled");
          inputs[5].focus();
        }

        updateOTPValue(inputs);
      });

      input.addEventListener("keydown", function (ev) {
        var currentIndex = Array.from(inputs).indexOf(this);

        if (!this.value && ev.key === "Backspace" && currentIndex > 0) {
          inputs[currentIndex - 1].focus();
        }
      });
    });
  }

  function updateOTPValue(inputs) {
    var otpValue = "";
    inputs.forEach(function (input) {
      otpValue += input.value;
    });

    if (inputs === otpInputs) {
      document.getElementById("verificationCode").value = otpValue;
    } else if (inputs === emailOtpInputs) {
      document.getElementById("emailverificationCode").value = otpValue;
    }
  }

  // Setup listeners for OTP inputs
  setupOtpInputListeners(otpInputs);
  setupOtpInputListeners(emailOtpInputs);

  // Add event listener for verify button
  document.getElementById("verifyMobileOTP").addEventListener("click", function () {
    var otpValue = document.getElementById("verificationCode").value;
    alert("Submitted OTP: " + otpValue);
    // Add your submit logic here (e.g., AJAX request or form submission)
  });

  document.getElementById("verifyEmailOTP").addEventListener("click", function () {
    var otpValue = document.getElementById("emailverificationCode").value;
    alert("Submitted Email OTP: " + otpValue);
    // Add your submit logic here
  });

  // Initial focus on first OTP input field
  otpInputs[0].focus();
  emailOtpInputs[0].focus();
});
</script>
</body>
</html><?php
}
?>