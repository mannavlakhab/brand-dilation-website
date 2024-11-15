<?php

session_start();

// Unset session
session_unset();
session_destroy();

// Clear the "Remember Me" cookie
setcookie("user_id", "", time() - 3600, "/");

// Redirect to login page or home
header("Location: index.php");
exit();
?>