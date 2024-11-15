<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$id = $_SESSION['id'];
$sql = "SELECT username,firstname,lastname, email, phone, role, gender, profile_icon, date_of_birth, date_of_joining FROM admin_users WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $email = $row['email'];
    $phone = $row['phone'];
    $role = $row['role'];
    $gender = $row['gender'];
    $profile_icon = $row['profile_icon'];
    $date_of_birth = $row['date_of_birth'];
    $date_of_joining = $row['date_of_joining'];
} else {
    echo "User not found";
    exit();
}

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/profiles/';
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            // Update profile_icon in database
            $profile_icon = $uploadFile;
            $update_sql = "UPDATE admin_users SET profile_icon = '$profile_icon' WHERE id = $id";
            if ($conn->query($update_sql) === TRUE) {
                // Update successful
                $profile_icon = $uploadFile; // Update current session variable if needed
            } else {
                echo "Error updating profile picture: " . $conn->error;
            }
        } else {
            echo "Error uploading file.";
        }
    }

    // Update username, email, role (example)
    if (isset($_POST['new_username'])) {
        $new_username = $_POST['new_username'];
        $update_sql = "UPDATE admin_users SET username = '$new_username' WHERE id = $id";
        if ($conn->query($update_sql) === TRUE) {
            // Update successful
            $username = $new_username; // Update current session variable if needed
        } else {
            echo "Error updating username: " . $conn->error;
        }
    }

    if (isset($_POST['new_email'])) {
        $new_email = $_POST['new_email'];
        $update_sql = "UPDATE admin_users SET email = '$new_email' WHERE id = $id";
        if ($conn->query($update_sql) === TRUE) {
            // Update successful
            $email = $new_email; // Update current session variable if needed
        } else {
            echo "Error updating email: " . $conn->error;
        }
    }

    if (isset($_POST['new_role'])) {
        $new_role = $_POST['new_role'];
        $update_sql = "UPDATE admin_users SET role = '$new_role' WHERE id = $id";
        if ($conn->query($update_sql) === TRUE) {
            // Update successful
            $role = $new_role; // Update current session variable if needed
        } else {
            echo "Error updating role: " . $conn->error;
        }
    }
}

// Fetch user activities including login_time and logout_time
$sql_activities = "SELECT activity_date, activity_description, login_time, logout_time FROM admin_user_activity WHERE id = $id ORDER BY activity_date DESC";
$result_activities = $conn->query($sql_activities);

$activities = [];
if ($result_activities->num_rows > 0) {
    while ($row_activities = $result_activities->fetch_assoc()) {
        $activities[] = $row_activities;
    }
} else {
    // Handle no activities found (optional)
    // echo "No activities found for this user.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f2f2f2;
        }

        h2 {
            color: #333;
        }

        form {
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        input[type=text], input[type=email], select {
            width: calc(100% - 20px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        input[type=file] {
            width: 100%;
            margin-bottom: 10px;
        }

        button[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type=submit]:hover {
            background-color: #45a049;
        }

        img {
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .profile-info {
            margin-bottom: 20px;
        }

        .logout-link {
            margin-top: 20px;
            display: block;
            text-align: right;
        }
            .activity-section {
                margin-top: 20px;
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
            }

            .activity {
                margin-bottom: 10px;
            }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo $username; ?></h2>

    <form method="POST" action="" enctype="multipart/form-data">
        <h3>Update Profile:</h3>
        <div class="profile-info">
            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture">
            <img src="<?php echo $profile_icon; ?>" alt="Profile Picture" width="100" height="100">
        </div>
        <div class="profile-info">
            <label for="new_username">Username:</label>
            <input type="text" id="new_username" name="new_username" value="<?php echo $username; ?>" required>
        </div>
        <div class="profile-info">
            <label for="firstname">firstname:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" required>
        </div>
        <div class="profile-info">
            <label for="lastname">lastname:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>" required>
        </div>
        <div class="profile-info">
            <label for="new_email">Email:</label>
            <input type="email" id="new_email" name="new_email" value="<?php echo $email; ?>" required>
        </div>
        <div class="profile-info">
            <label for="new_role">Role:</label>
            <select id="new_role" name="new_role">
                <option value="sales" <?php if ($role == 'sales') echo 'selected'; ?>>Sales</option>
                <option value="inventory" <?php if ($role == 'inventory') echo 'selected'; ?>>Inventory</option>
                <option value="super_admin" <?php if ($role == 'super_admin') echo 'selected'; ?>>Main Admin</option>
            </select>
        </div>
        <button type="submit">Update Profile</button>
    </form>

    <div class="profile-info">
        <h3>Current Profile Information:</h3>
        <img src="<?php echo $profile_icon; ?>" alt="Profile Picture" width="100" height="100">
        <p><strong>Email:</strong> <?php echo $email; ?></p>
        <p><strong>Phone:</strong> <?php echo $phone; ?></p>
        <p><strong>Role:</strong> <?php echo $role; ?></p>
        <p><strong>Gender:</strong> <?php echo $gender; ?></p>
        <p><strong>Date of Birth:</strong> <?php echo $date_of_birth; ?></p>
        <p><strong>Date of Joining:</strong> <?php echo $date_of_joining; ?></p>
    </div>

  
    <div class="activity-section">
    <h3>Activity Log:</h3>
    <?php if (!empty($activities)) : ?>
        <?php foreach ($activities as $activity) : ?>
            <div class="activity">
                <p><strong>Date:</strong> <?php echo $activity['activity_date']; ?></p>
                <p><strong>Login Time:</strong> <?php echo $activity['login_time']; ?></p>
                <p><strong>Logout Time:</strong> <?php echo $activity['logout_time']; ?></p>
                <p><strong>Description:</strong> <?php echo $activity['activity_description']; ?></p>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No activities found.</p>
    <?php endif; ?>
</div>

    
    <a href="logout.php" class="logout-link">Logout</a>
</body>
</html>


