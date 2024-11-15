<?php
session_start();
include('config.php'); // Database connection

$reset_link = ''; // Initialize reset_link variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    // Fetch user by username
    $query = "SELECT * FROM delivery_partners WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Generate reset token and expiry
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime("+20 minutes"));

        // Update user with reset token and expiry
        $query = "UPDATE delivery_partners SET reset_token = '$token', token_expiry = '$expiry' WHERE id = '{$user['id']}'";
        if (mysqli_query($conn, $query)) {
            // Construct reset link
            $reset_link = "reset_password.php?token=$token";
           
        } else {
            echo "Failed to generate reset link.";
        }
    } else {
        echo "Username not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="same.css">
</head>
<body>
    <div class="logo-wrapper">
        <img class="logo" alt="Logo" src="../ass/bd.png" />
    </div>

    <div class="content-body">
        <div class="form-wrapper">
            <form action="" method="POST">
                <h1 class="text-title">Welcome Delivery Partner</h1>
                <div class="text-register">Are you a new user? <a href="signup.php">YES! I AM</a></div>
                <div class="text-register">                    <?php if ($reset_link) { ?>
                        <p>Your password link : <a  href="<?php echo $reset_link; ?>">Create New password</a></p>
                    <?php } ?>
</div>
                <div class="field-group">
                    <input placeholder="Username" class="input" type="text" id="txt-email" name="username" required>
                </div>

                <div class="field-group-inline">
                    <label for="chk-rememberme">
                        <input class="checkbox" type="checkbox" required id="chk-rememberme" name="rememberme" />
                        By clicking here, I state that I have read and understood the <a href="../pages/toc.html">terms and conditions</a>.
                    </label>
                    <a href="login.php"> Login Now </a>
                </div>

                <div class="field-group">
                    <input class="btn-submit" type="submit" value="Submit" />
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
