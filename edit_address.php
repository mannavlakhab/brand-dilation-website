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


$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address_1 = $_POST['address_1'];
    $address_2 = $_POST['address_2'];

    $stmt = $conn->prepare("UPDATE users SET address_1 = ?, address_2 = ? WHERE id = ?");
    $stmt->bind_param("ssi", $address_1, $address_2, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: checkout.php");
    exit();
} else {
    $user_query = $conn->prepare("SELECT address_1, address_2 FROM users WHERE user_id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_query->bind_result($address_1, $address_2);
    $user_query->fetch();
    $user_query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
<script src="../assets/js/internet-check.js" defer></script>
    <meta charset="UTF-8">
    <title>Edit Address</title>
</head>
<body>
    <h2>Edit Address</h2>
    <form method="post" action="edit_address.php">
        <label for="address_1">Address Line 1:</label>
        <input type="text" id="address_1" name="address_1" value="<?php echo htmlspecialchars($address_1); ?>" required><br>
        <label for="address_2">Address Line 2:</label>
        <input type="text" id="address_2" name="address_2" value="<?php echo htmlspecialchars($address_2); ?>"><br>
        <button type="submit">Save Address</button>
    </form>
</body>
</html>
