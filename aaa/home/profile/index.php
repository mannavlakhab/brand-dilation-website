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
    header("Location: ../../login"); // Redirect to login if not logged in
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

<html>
<head> <meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"></link>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 10px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content button {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            width: 100%;
            text-align: left;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
<?php include '../head.php'; ?>
            <div class="bg-gradient-to-r from-teal-400 to-blue-500 h-32 rounded-t-lg"></div>
            <div class="relative -mt-16 px-6 pb-6">
                <div class="flex flex-col sm:flex-row items-center glass-effect">
                    <div class="w-24 h-24 bg-gray-300 rounded-full border-4 border-white overflow-hidden">
                        <img alt="Profile picture" class="w-full h-full object-cover" height="96" src="../../signup/<?php echo htmlspecialchars($profile_picture)?>" width="96"/>
                    </div>
                    <div class="ml-0 sm:ml-6 mt-4 sm:mt-0 text-center sm:text-left">
                        <h1 class="text-xl font-semibold mt-4">
                            Hi, I'm <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>
                        </h1>
                        <p class="text-gray-600">@<?php echo htmlspecialchars($username); ?></p>
                        <!-- <p class="text-blue-500">Wxyz, JX - <a href="#" class="text-blue-500">Contact Information</a></p> -->
                    </div>
                </div>
                <div class="mt-4 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <button onclick="location.href = '../../signup/edit.php';" class="bg-blue-500 text-white px-4 py-2 rounded">+ Edit Profile</button>
                    <button onclick="location.href = '../../login/logout.php';" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Logout</button>
                </div>
                <!-- <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <p class="text-gray-600">Lorem ipsum dolor sit amet consectetur adipiscing elit proin nisi nisl facilisis et fringilla.</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <p class="text-gray-600">Lorem ipsum dolor sit amet consectetur adipiscing elit proin nisi nisl facilisis et fringilla.</p>
                    </div>
                </div> -->
                <div class="bg-white rounded-lg shadow-lg mt-6 p-6">
                    <h2 class="text-xl font-bold mb-4">Personal Information</h2>
                    <p class="text-gray-600"><strong>Username:</strong> <?php echo htmlspecialchars($username ); ?></p>
                    <p class="text-gray-600"><strong>First Name:</strong> <?php echo htmlspecialchars($first_name); ?></p>
                    <p class="text-gray-600"><strong>Last Name:</strong> <?php echo htmlspecialchars($last_name); ?></p>
                    <p class="text-gray-600"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p class="text-gray-600"><strong>Profile Picture:</strong> <img alt="Profile picture" class="w-16 h-16 object-cover rounded-full" src="../../signup/<?php echo htmlspecialchars($profile_picture)?>" /></p>
                </div>
            </div>
        </div>
    </div>
    <?php include '../footer.php'; ?>
</body>
</html>