<?php

// Database credentials (replace with your actual details)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Now you can use $conn to perform database operations

?>
