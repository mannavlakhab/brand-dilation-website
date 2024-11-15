<?php
// Start the session
session_start();

// Database configuration
$servername = "localhost"; // Change this if your database server is different
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "gt"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login"); // Redirect to login if not logged in
    exit();
}

// Get the logged-in user's username
$current_username = $_SESSION['username'];

// Fetch user profile information from the database
$stmt = $conn->prepare("SELECT email, username, first_name, last_name, profile_picture FROM users WHERE username = ?");
$stmt->bind_param("s", $current_username);
$stmt->execute();
$stmt->bind_result($email, $username, $first_name, $last_name, $profile_picture);
$stmt->fetch();
$stmt->close();


// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>GT Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 ">
<?php include 'head.php'; ?>
            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-2xl font-semibold text-gray-900 ">Dashboard</h1>
                    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    
                    </div>
                    <!-- add here -->
                </div>
            </main>
            <?php include 'footer.php'; ?>
</body>
</html>