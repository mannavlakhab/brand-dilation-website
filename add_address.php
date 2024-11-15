<?php
session_start();
require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address_2 = $_POST['address_2'];

    $stmt = $conn->prepare("UPDATE users SET address_2 = ? WHERE id = ?");
    $stmt->bind_param("si", $address_2, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: checkout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Address</title>
</head>
<body>
    <h2>Add Address</h2>
    <form method="post" action="add_address.php">
        <label for="address_2">Address Line 2:</label>
        <input type="text" id="address_2" name="address_2" required><br>
        <button type="submit">Add Address</button>
    </form>
</body>
</html>
