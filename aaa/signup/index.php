<?php
// Start the session
session_start();

// Database configuration
$servername = "localhost"; // Change this if your database server is different
$db_username = "root"; // Your database username
$db_password = ""; // Your database password
$dbname = "gt"; // Your database name

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit;
    }

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Username or email already exists. Please choose another.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Handle file upload
    $profile_picture = null;
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile-picture']['tmp_name'];
        $file_name = basename($_FILES['profile-picture']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Specify allowed file types
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Check file type
        if (in_array($file_ext, $allowed_extensions)) {
            $upload_dir = '../uploads/'; // Adjusted path

            // Check if the upload directory exists, if not, create it
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    echo "Failed to create upload directory.";
                    exit;
                }
            }

            $file_path = $upload_dir . uniqid() . '.' . $file_ext;

            // Move the uploaded file to the desired directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                $profile_picture = $file_path; // Store the file path
            } else {
                echo "Error uploading file.";
                exit;
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit;
        }
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, email, password, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $first_name, $last_name, $username, $email, $hashed_password, $profile_picture);

    // Execute the statement
    if ($stmt->execute()) {
        // Store user information in session
        $_SESSION['user_id'] = $stmt->insert_id; // Store the user ID
        $_SESSION['username'] = $username; // Store the username
        $_SESSION['email'] = $email; // Store the email

        // Redirect to home.php
        header("Location: ../home");
        exit;
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Signup &amp; Login Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp ;display=swap" rel="stylesheet"/>
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
            <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Signup</h2>
            <form class="space-y-6" method="POST" enctype="multipart/form-data">
            <div>
                    <label class="block text-sm font-medium text-gray-700" for="profile-picture">Profile Picture</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="profile-picture" name="profile-picture" type="file" required accept="image/*"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="first-name">First Name</label>
                        <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="first-name" name="first-name" required=""
                        pattern="^(?!.*\.\.)(?!.*\.$)(?!.*\._)(?!.*\.$)[a-zA-Z]{3,30}$"   type="text"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="last-name">Last Name</label>
                        <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="last-name" name="last-name" required=""
                        pattern="^(?!.*\.\.)(?!.*\.$)(?!.*\._)(?!.*\.$)[a-zA-Z]{1,30}$"  type="text"/>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="username">Username</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
       id="username" 
       name="username" 
       required 
       type="text" 
       pattern="^(?!.*\.\.)(?!.*\.$)(?!.*\._)(?!.*\.$)[a-zA-Z0-9._]{1,30}$" 
       title="Username must be 1-30 characters long and can only contain letters, numbers, periods, and underscores. It cannot start or end with a period or have consecutive periods." />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
       id="email" 
       name="email" 
       required 
       type="email" 
       pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
       title="Please enter a valid email address (e.g., user@example.com)" />
                </div>
                <div>
        <label class="block text-sm font-medium text-gray-700" for="password">Password</label>
        <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
               id="password" 
               name="password" 
               required 
               type="password" 
               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" 
               title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character." />
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700" for="confirm-password">Confirm Password</label>
        <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
               id="confirm-password" 
               name="confirm-password" 
               required 
               type="password" 
               pattern="^(?=.*[a-z])(?=.*[A-Z]) (?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" 
               title="Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number, and one special character." />
    </div>
                
                <div>
                    <button class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" type="submit">Sign Up</button>
                </div>
            </form>
            <p class="mt-6 text-center text-sm text-gray-600">Already have an account? <a class="font-medium text-indigo-600 hover:text-indigo-500" href="../login">Login</a></p>
        </div>
    </div>
</body>
</html>