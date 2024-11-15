<?php
session_start();
require_once '../db_connect.php';

// Function to fetch all users
function getUsers($conn) {
    $sql = "SELECT * FROM admin_users";
    $result = $conn->query($sql);
    return $result;
}

// Function to delete user
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM admin_users WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// Update profile and password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $profile_icon = $_POST['profile_icon'];
    $date_of_birth = $_POST['date_of_birth'];
    $date_of_joining = $_POST['date_of_joining'];

    // Update user details
    $update_sql = "UPDATE admin_users SET username=?, email=?, phone=?, firstname=?, lastname=?, profile_icon=?, date_of_birth=?, date_of_joining=? WHERE id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssssi", $username, $email, $phone, $firstname, $lastname, $profile_icon, $date_of_birth, $date_of_joining, $id);
    $stmt->execute();

    // If password change is requested
    if (!empty($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $password_sql = "UPDATE admin_users SET password=? WHERE id=?";
        $stmt = $conn->prepare($password_sql);
        $stmt->bind_param("si", $new_password, $id);
        $stmt->execute();
    }

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Main Wrapper with Sidebar and Content -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <?php include 'hsidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 p-6">
            <!-- Header -->
            <?php include 'head.php'; ?>

            <!-- Admin Dashboard Content -->
            <h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>

            <!-- List All Users -->
            <h2 class="text-2xl font-semibold mb-4">All Users</h2>
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <thead>
                    <tr class="bg-gray-200 text-left text-gray-600 uppercase text-sm">
                        <th class="py-3 px-4 border-b">ID</th>
                        <th class="py-3 px-4 border-b">Username</th>
                        <th class="py-3 px-4 border-b">Email</th>
                        <th class="py-3 px-4 border-b">Phone</th>
                        <th class="py-3 px-4 border-b">First Name</th>
                        <th class="py-3 px-4 border-b">Last Name</th>
                        <th class="py-3 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users = getUsers($conn);
                    while ($user = $users->fetch_assoc()) {
                        echo "<tr class='text-gray-700'>
                                <td class='py-3 px-4 border-b'>{$user['id']}</td>
                                <td class='py-3 px-4 border-b'>{$user['username']}</td>
                                <td class='py-3 px-4 border-b'>{$user['email']}</td>
                                <td class='py-3 px-4 border-b'>{$user['phone']}</td>
                                <td class='py-3 px-4 border-b'>{$user['firstname']}</td>
                                <td class='py-3 px-4 border-b'>{$user['lastname']}</td>
                                <td class='py-3 px-4 border-b'>
                                    <a href='?edit_id={$user['id']}' class='text-blue-600 hover:underline'>Edit</a> | 
                                    <a href='?delete_id={$user['id']}' class='text-red-600 hover:underline' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                </td>
                            </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Edit User Profile Form -->
            <?php if (isset($_GET['edit_id'])):
                $edit_id = $_GET['edit_id'];
                $edit_sql = "SELECT * FROM admin_users WHERE id = ?";
                $stmt = $conn->prepare($edit_sql);
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
            ?>
            <h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>
            <form action="" method="post" class="bg-white p-6 rounded-lg shadow-sm">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                <div class="mb-4">
                    <label class="block text-gray-700">Username</label>
                    <input type="text" name="username" value="<?php echo $user['username']; ?>" required class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Phone</label>
                    <input type="text" name="phone" value="<?php echo $user['phone']; ?>" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">First Name</label>
                    <input type="text" name="firstname" value="<?php echo $user['firstname']; ?>" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Last Name</label>
                    <input type="text" name="lastname" value="<?php echo $user['lastname']; ?>" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Profile Icon URL</label>
                    <input type="text" name="profile_icon" value="<?php echo $user['profile_icon']; ?>" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="<?php echo $user['date_of_birth']; ?>" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Date of Joining</label>
                    <input type="date" name="date_of_joining" value="<?php echo $user['date_of_joining']; ?>" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">New Password</label>
                    <input type="password" name="new_password" class="w-full p-2 border border-gray-300 rounded">
                </div>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">Update Profile</button>
            </form>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
