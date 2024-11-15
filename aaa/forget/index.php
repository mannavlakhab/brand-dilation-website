<?php
// Start the session
session_start();

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Make sure the path is correct

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
$error_m = ""; 
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_username = $_POST['login-username'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT email FROM users WHERE username = ?");
    $stmt->bind_param("s", $login_username);

    // Execute the statement
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();

        // Generate a unique token for the password reset link
        $token = bin2hex(random_bytes(50));
        $expires = date("U") + 3600; // Token expires in 1 hour

        // Store the token in the database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $email, $token, $expires);
        $stmt->execute();

        list($localPart, $domainPart) = explode('@', $email);

        // Obscure the last few characters of the local part
        $lengthToShow = 8; // Number of characters to show from the local part
        $obscuredLocalPart = substr($localPart, 0, $lengthToShow) . '***'; // Show first 8 characters and hide the rest
        
        // Construct the display email
        $displayEmail = $obscuredLocalPart . '@' . $domainPart;


        
        // Create the password reset link
        $reset_link = "http://192.168.43.178/aaa/forget/reset_password.php?token=" . $token;

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'moviesfull808@gmail.com'; // Your Gmail address
            $mail->Password = 'tscoeqiosoaalmvy'; // Your Gmail password or App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('man@man.unaux.com', 'Gravity Login Password Reset');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = 'Click the link below to reset your password:<br><a href="' . $reset_link . '">' . $reset_link . '</a>';

            $mail->send();
            $error_m = '<br><span class="text-green-600">🔑Password reset link🔗 has been sent to your email address📩 : ' . $displayEmail. '.</span><br>';
        } catch (Exception $e) {
            $error_m = '<br><span class="text-red-600">Failed to send email. Mailer Error: {$mail->ErrorInfo}</span><br>';
        }
    } else {
        $error_m = '<span class="text-red-600">No user found with that username!</span>';
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Forgot Password</title>
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
            <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Forgot Password</h2>
            <form class="space-y-6" method="POST">
                <?php echo $error_m ;?>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="login-username">Username</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="login-username" name="login-username" required="" type="text"/>
                </div>
                <div>
                    <button class="w-full flex justify-center py-3 px -4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" type="submit">Get Link</button>
                </div>
            </form>
            <p class="mt-6 text-center text-sm text-gray-600">Don't want to forget? <a class="font-medium text-indigo-600 hover:text-indigo-500" href="../Login">Login</a></p>
        </div>
    </div>
</body>
</html>