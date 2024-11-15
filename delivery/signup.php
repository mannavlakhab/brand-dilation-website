<?php
session_start();
include('config.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Handle profile picture upload
    $profile_picture = NULL;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = $target_file;
        }
    }

    // Insert into database
    $query = "INSERT INTO delivery_partners (username, password, name, phone_number, email, profile_picture)
              VALUES ('$username', '$password', '$name', '$phone_number', '$email', '$profile_picture')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['delivery_partner_id'] = mysqli_insert_id($conn);
        header("Location: index.php");
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="robots" content="follow,index" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delivery Partner Registration</title>    <link rel="stylesheet" href="same.css">
    <link
      href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap"
      rel="stylesheet"
    />

    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
  </head>

  <body>
    <div class="logo-wrapper">
      <img class="logo" alt="Logo" src="../ass/bd.png" />
    </div>

    <div class="content-body">
      <div class="form-wrapper">
      <form method="POST" action="signup.php" enctype="multipart/form-data">
        <h1 class="text-title">Delivery Partner Registration</h1>
        <div class="text-register">do you have account on delivery partner? <a href="login.php">YES! I HAVE</a></div>
        <div class="field-group">
          <input
            class="input"
            type="text"
            name="username"
            placeholder="Username"
          />
          <input class="input" type="password"  name="password" placeholder="Password" />
          <input class="input" type="text"  name="name" placeholder="Name" />
          <input class="input" type="number"  name="phone_number" placeholder="Phone Number" />
          <input class="input" type="email"  name="email" placeholder="email" />
        <input class="input" type="file" name="profile_picture"><br><br>

        </div>
        <?php if (isset($error)) { echo "<p style='color:red; ' >$error</p>"; } ?>

        <div class="field-group-inline">
            
          <label for="chk-rememberme">
            <input class="checkbox" type="checkbox" id="chk-rememberme" required name="rememberme" />
            By clicking here, I state that I have read and understood the <a href="../pages/toc.html">terms and conditions</a>.
          </label>
          <a href="forgot_password.php"> forget Password? </a>

        </div>

        <div class="field-group">
          <input class="btn-submit" name="register" type="submit" value="Register" />
        </div></form>

        <div class="separator-wrapper">
          <div class="separator">
            <span>BRAND DILATION</span>
          </div>
        </div>


    <!-- ADDITIONAL CODE -- start YOU MIGHT NOT NEEDED -->
    <footer>
      <a href="../"> Back to Home </a>
      <span class="author">
        Crafted with ❤️ by Man Navlakha</a>,
        © 2020
      </span> </footer>
    <!-- ADDITIONAL CODE -- end YOU MIGHT NOT NEEDED -->
  </body>
</html>