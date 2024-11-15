<?php
// db_connection.php

// Database configuration
$host = 'localhost'; // Your database host
$user = 'root'; // Your database username
$pass = ''; // Your database password
$dbname = 'shop'; // Your database name

// Create a connection
$db = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
