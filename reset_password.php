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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    
    // Check if the token is valid
    $sql = "SELECT user_id FROM password_resets WHERE token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['user_id'];
        
        // Update the user's password
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_password, $user_id);
        $stmt->execute();
        
        // Delete the token
        $sql = "DELETE FROM password_resets WHERE token = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        
        echo "Your password has been reset successfully.";
        header("Location: login.php");
        exit();
    } else {
        echo "Invalid or expired token.";
    }
} else {
    if (!isset($_GET['token'])) {
        echo "No token provided.";
        exit();
    }
    $token = $_GET['token'];
}
?>

<!DOCTYPE html>
<html>
<head>  <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="./s_l.css">

    <title>Reset Password</title>
</head>
<body>

    <div class="container_login">
            <div class="header_login">
                <a href="../index.php"><img src="../assets/img/64-bd-t.png" alt="logo" width="70px" height="70px"><span></span></a>
                <h1>New Password of <em id="brand">Brand</em> <em id="dilation">Dilation</em></h1>
            </div>
            <form method="post" action="reset_password.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <input placeholder="Enter Your New P@ssW0rd" type="password" id="new_password" name="new_password" required class="inputField" >
                
                </div>
                <button type="submit" class="submitButton">Set as new p@ssw0rd</button>
            </form>

            <div class="footer">
                <a href="../login.php">Login Now</a>
                <span>•</span>
                <a href="../signup.php">Signup</a>
            </div>

</body>
</html>
