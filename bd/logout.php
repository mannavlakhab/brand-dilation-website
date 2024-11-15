<?php
session_start();
include 'config.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $loginTime = $_SESSION['login_time'];
    $logoutTime = date("Y-m-d H:i:s");
    
    // Update logout time
    $conn->query("UPDATE admin_user_activity SET logout_time = '$logoutTime' WHERE user_id = '$user_id' AND login_time = '$loginTime'");
    
    session_unset();
    session_destroy();
}

header("Location: login.php");
exit();
?>
