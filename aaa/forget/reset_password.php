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

// Check if the token is set
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT email, expires FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);

    // Execute the statement
    $stmt->execute();
    $stmt->store_result();

    // Check if the token is valid
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $expires);
        $stmt->fetch();

        // Check if the token has expired
        if (time() > $expires) {
            echo "This password reset link has expired.";
            exit;
        }
    } else {
        echo "Invalid password reset token.";
        exit;
    }

    // Process the new password
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = $_POST['new-password'];
        $confirm_password = $_POST['confirm-password'];

        // Check if passwords match
        if ($new_password !== $confirm_password) {
            echo "Passwords do not match!";
            exit;
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the user's password in the users table
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        // Delete the token from the password_resets table
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
         // Redirect to home.php
         header("Location: ../login");
         exit;
        echo "Your password has been reset successfully. You can now <a href='login.php'>login</a>.";
        exit;
    }
} else {
    echo "No token provided.";
}

// Close the statement
$stmt->close();
// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
<div class="min-h-screen flex items-center justify-center">
        <div class="p-8 rounded-lg w-full max-w-md">
            <div class="flex justify-center mb-6">
                <img alt="Company logo with a placeholder image of 100x100 pixels" height="100" src="https://storage.googleapis.com/a1aa/image/K0ir9v3XssawOJrQB4HPfA2ftXASHfJB52C5NfdxXhNAw89OB.jpg" width="100"/>
            </div>
            <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Reset Password</h2>
            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" class="space-y-6" method="POST">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="new-password">New Password</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="new-password" name="new-password" required="" 
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" 
                     title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character."
                    type="password"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="confirm-password">Confirm Password</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="confirm-password" name="confirm-password" required="" 
                                   title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character."
                                   type="password"/>
                </div>
                <div>
                    <button class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" type="submit">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>