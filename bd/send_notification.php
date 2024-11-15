<?php
session_start();
require_once '../db_connect.php';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    
  <div class="flex">
  <?php include 'hsidebar.php'; ?>
  <!-- Main Content -->
  <div class="flex-1 p-6 ">
      
      <?php include 'head.php'; ?>
    <h1 class="text-2xl font-bold mb-4">Send Notification</h1>

    <form id="notificationForm" method="POST" action="">
        <div class="mb-4">
            <label for="message" class="block text-gray-700">Notification Message:</label>
            <textarea id="message" name="message" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
        </div>
        <div class="mb-4">
            <label for="user" class="block text-gray-700">Select User:</label>
            <select id="user" name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                <option disabled selected value="">All Users</option>
                <?php
                // Fetch users from the admin_users table
                $user_sql = "SELECT id, CONCAT(id,firstname, ' ', lastname) AS name FROM admin_users";
                $user_result = $conn->query($user_sql);

                if ($user_result->num_rows > 0) {
                    while ($user_row = $user_result->fetch_assoc()) {
                        echo "<option value='" . $user_row['id'] . "'>" .' '. htmlspecialchars($user_row['name']) . "</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="role" class="block text-gray-700">Select Role:</label>
            <select id="role" name="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                <option disabled selected value="">All Roles</option>
                <option value="Admin">Admin</option>
                <option value="User">User</option>
                <option value="Manager">Manager</option>
                <!-- Add other roles as needed -->
            </select>
        </div>
        <button type="submit" name="send_notification" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Send Notification</button>
    </form>

    <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p class="font-bold">Success!</p>
        <p>Your notification has been sent.</p>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p class="font-bold">Deleted!</p>
        <p>The notification has been deleted.</p>
    </div>
    <?php endif; ?>

    <h2 class="text-xl font-bold mt-8 mb-4">All Notifications</h2>

    <table class="min-w-full bg-white border border-gray-300">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b text-left">ID</th>
                <th class="py-2 px-4 border-b text-left">Message</th>
                <th class="py-2 px-4 border-b text-left">User ID</th>
                <th class="py-2 px-4 border-b text-left">Role</th>
                <th class="py-2 px-4 border-b text-left">Date</th>
                <th class="py-2 px-4 border-b text-left">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Handle form submission for sending notification
            if (isset($_POST['send_notification'])) {
                $message = $conn->real_escape_string($_POST['message']);
                $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;
                $role = !empty($_POST['role']) ? $conn->real_escape_string($_POST['role']) : null;

                // Insert notification into the database
                $sql = "INSERT INTO notifications (message, user_id, role) VALUES ('$message', '$user_id', '$role')";

                if ($conn->query($sql) === TRUE) {
                    // After inserting the notification successfully
                    // header("Location: send_notification.php?success=1");
                    // exit();
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }

            // Handle deletion of notifications
            if (isset($_POST['delete_notification'])) {
                $id = intval($_POST['id']);

                // Delete notification from the database
                $sql = "DELETE FROM notifications WHERE id = $id";

                if ($conn->query($sql) === TRUE) {
                    // Redirect back with success message
                    // header("Location: send_notification.php?deleted=1");
                    // exit();
                } else {
                    echo "Error deleting notification: " . $conn->error;
                }
            }

            // Fetch all notifications from the database
            $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='py-2 px-4 border-b'>" . $row['id'] . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . htmlspecialchars($row['message']) . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . ($row['user_id'] ? $row['user_id'] : 'All Users') . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . ($row['role'] ? htmlspecialchars($row['role']) : 'All Roles') . "</td>";
                    echo "<td class='py-2 px-4 border-b'>" . $row['created_at'] . "</td>";
                    echo "<td class='py-2 px-4 border-b'>";
                    echo "<form method='POST' action='' class='inline'>";
                    echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' name='delete_notification' class='text-red-500 hover:text-red-700'>Delete</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='py-2 px-4 border-b text-center'>No notifications found.</td></tr>";
            }

            ?>
        </tbody>
    </table>
        </div>
        </div>
</body>
</html>
