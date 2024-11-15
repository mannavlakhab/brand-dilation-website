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

// Fetch user data
$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session
$stmt = $conn->prepare("SELECT first_name, last_name, username, email, profile_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    
    // Handle file upload
    $profile_picture = $user['profile_picture']; // Default to current profile picture
    if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile-picture']['tmp_name'];
        $file_name = basename($_FILES['profile-picture']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Specify allowed file types
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Check file type
        if (in_array($file_ext, $allowed_extensions)) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_path = $upload_dir . uniqid() . '.' . $file_ext;

            // Move the uploaded file to the desired directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                $profile_picture = $file_path; // Update the profile picture path
            } else {
                echo "Error uploading file.";
                exit;
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit;
        }
    }

    // Update user information
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, profile_picture = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $first_name, $last_name, $profile_picture, $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
        header("Location: ../home/profile"); // Redirect to profile page
        exit;
    } else {
        echo "Error updating profile: " . $stmt->error;
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
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="p-8 rounded-lg w-full max-w-md">
            <h2 class="text- 2xl font-bold mb-6">Edit Profile</h2>
            <form method="POST" enctype="multipart/form-data">
            <div>
                    <label class="block text-sm font-medium text-gray-700" for="profile-picture">Profile Picture</label>
                    <input class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" id="profile-picture" name="profile-picture" type="file" accept="image/*"/>
                </div>
                <div class="mb-4">
                    <label for="first-name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" name="first-name" id="first-name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 p-2"/>
                </div>
                <div class="mb-4">
                    <label for="last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" name="last-name" id="last-name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500 focus:border-blue-500 p-2"/>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>