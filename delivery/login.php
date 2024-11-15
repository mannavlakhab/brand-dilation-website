<?php
session_start();
include('config.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM delivery_partners WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['delivery_partner_id'] = $user['id'];
        header("Location: index.php");
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <meta charset="UTF-8" />
    <meta name="robots" content="follow,index" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delivery Partner Login</title>
    <link rel="stylesheet" href="same.css">
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap"
      rel="stylesheet"
    />


  </head>

  <body>
    <div class="logo-wrapper">
      <img class="logo" alt="Logo" src="../ass/bd.png" />
    </div>

    <div class="content-body">
      <div class="form-wrapper">
        <form action="" method="POST">
        <h1 class="text-title">Welocome Delivery Partner</h1>
        <div class="text-register">are you new user? <a href="signup.php">YES! I AM</a></div>
        <div class="field-group">
          <input
            class="input"
            type="text"
            id="txt-email"
            name="username"
            placeholder="Username"
          />
          <input class="input" type="password" id="txt-password" name="password" placeholder="Password" />
        </div>
        <?php if (isset($error)) { echo "<p style='color:red; ' >$error</p>"; } ?>

        <div class="field-group-inline">
            
          <label for="chk-rememberme">
            <input class="checkbox" checked  type="checkbox" required id="chk-rememberme" name="rememberme" />
            By clicking here, I state that I have read and understood the <a href="../pages/toc.html">terms and conditions</a>.
          </label>
          <a href="forgot_password.php"> forget Password? </a>
        </div>

        <div class="field-group">
          <input class="btn-submit" type="submit" value="Login" />
        </div></form>

        <div class="separator-wrapper">
          <div class="separator">
            <span>BRAND DILATION</span>
          </div>
        </div>


    <!-- ADDITIONAL CODE -- start YOU MIGHT NOT NEEDED -->
    <footer>
      <a href="../"> Back to Home </a>
      <span class="author"> | ❤️ by Man Navlakha</a>,
        © 2020
      </span> </footer>
    <!-- ADDITIONAL CODE -- end YOU MIGHT NOT NEEDED -->
  </body>
</html>