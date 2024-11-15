<?php
session_start();
include('../../db_connect.php');

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
</head>
<body>
    <h1>Admin Dashboard</h1>
    <nav>
        <ul>
            <li><a href="manage_posts.php">Manage Blog Posts</a></li>
            <li><a href="manage_users.php">Manage Users</a></li> <!-- Optional -->
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div>
        <h2>Welcome, Admin!</h2>
        <!-- You can add more content here, like site statistics -->
    </div>
</body>
</html>
