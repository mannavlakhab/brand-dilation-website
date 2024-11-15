<?php
$host = 'localhost';
$db = 'richtext_editor';
$user = 'root'; // Your DB username
$pass = ''; // Your DB password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
