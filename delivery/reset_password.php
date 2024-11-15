<?php
session_start();
include('config.php'); // Database connection

// Initialize variables
$token = isset($_GET['token']) ? $_GET['token'] : '';
$show_form = false;
$error = '';
$success = '';

// Check if the token is provided and valid
if ($token) {
    // Fetch the user with the provided token
    $query = "SELECT reset_token, token_expiry FROM delivery_partners WHERE reset_token = '$token'";
    $result = mysqli_query($conn, $query);

    // Debug: Check if query executed successfully
    if (!$result) {
        die("Database query failed: " . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($result);

    // Debug: Check if user found
    if ($user) {
        $db_token = $user['reset_token'];
        $db_expiry = $user['token_expiry'];

        // Check if the token matches and is not expired
        if ($db_token === $token && strtotime($db_expiry) > time()) {
            $show_form = true;

            // Handle form submission for password reset
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
                $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

                if ($new_password == $confirm_password) {
                    // Hash the new password
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Update the user's password in the database
                    $query = "UPDATE delivery_partners SET password = '$hashed_password', reset_token = NULL, token_expiry = NULL WHERE reset_token = '$token'";
                    if (mysqli_query($conn, $query)) {
                        $success = "Your password has been updated successfully.";
                        $show_form = false;
                    } else {
                        $error = "Failed to update the password. Please try again.";
                    }
                } else {
                    $error = "Passwords do not match.";
                }
            }
        } else {
            $error = "Invalid or expired token.";
        }
    } else {
        $error = "Invalid or expired token.";
    }
} else {
    $error = "No token provided.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Your New Password</title>
    <link rel="stylesheet" href="same.css">
    <style>
     
     .message {
         margin-bottom: 10px;
         color: red;
     }
     .success {
         color: green;
     }
 </style>
</head>
<body>
    <div class="logo-wrapper">
        <img class="logo" alt="Logo" src="../ass/bd.png" />
    </div>

    <div class="content-body">
        <div class="form-wrapper">
          
                <h1 class="text-title">Welcome Delivery Partner</h1>
                <!-- <div class="text-register">Are you a new user? <a href="signup.php">YES! I AM</a></div> -->
                <div class="text-register">                 <?php if ($success) { echo "<p class='message success'>$success. <br> Let<a href='login.php'> Login Now </a>"; } ?>
                <?php if ($error) { echo "<p class='message'>$error</p>"; } ?>
</div>
                <div class="field-group">
                <?php if ($show_form) { ?>
        <form method="POST" action="">
            <input placeholder="Y0ur P@$$w0rd must be Str0ng" class="input" type="password" id="new_password" name="new_password" required>
            <input placeholder="Y0ur P@$$w0rd must be Str0ng" class="input" type="password" id="confirm_password" name="confirm_password" required>
        

        <?php } ?>
                </div>

                <div class="field-group-inline">
                    <label for="chk-rememberme">
                        <input class="checkbox" type="checkbox" checked  required id="chk-rememberme" name="rememberme" />
                        By clicking here, I state that I have read and understood the <a href="../pages/toc.html">terms and conditions</a>.
                    </label>
                    <a href="login.php"> Login Now </a>
                </div>

                <div class="field-group">
                    <input class="btn-submit" type="submit" value="Done" />
                </div>
            </form>

            <div class="separator-wrapper">
                <div class="separator">
                    <span>BRAND DILATION</span>
                </div>
            </div>

            <!-- ADDITIONAL CODE -- start YOU MIGHT NOT NEEDED -->
            <footer>
                <a href="../"> Back to Home </a>
                <span class="author"> | ❤️ by Man Navlakha, © 2020 </span>
            </footer>
            <!-- ADDITIONAL CODE -- end YOU MIGHT NOT NEEDED -->
        </div>
    </div>
</body>
</html>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="same.css">


</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?php if ($success) { echo "<p class='message success'>$success</p>"; } ?>
        <?php if ($error) { echo "<p class='message'>$error</p>"; } ?>
        <?php if ($show_form) { ?>
        <form method="POST" action="">
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php } ?>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>
