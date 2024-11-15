

<?php
session_start();
require_once '../db_connect.php';

// Handle Add User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $address_1 = $_POST['address_1'];
    $address_2 = $_POST['address_2'];
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;

    // Check if username already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "Username already taken.";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, first_name, last_name, phone_number, address_1, address_2, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $username, $email, $first_name, $last_name, $phone_number, $address_1, $address_2, $is_verified);

        $stmt->execute();
        $stmt->close();

        echo "User added successfully.";
    }
}

// Handle Edit User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $address_1 = $_POST['address_1'];
    $address_2 = $_POST['address_2'];
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
    $user_id = $_POST['user_id'];

    // Check if username is already taken by another user
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND user_id != ?");
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "Username already taken.";
    } else {
        // Update user
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, first_name=?, last_name=?, phone_number=?, address_1=?, address_2=?, is_verified=? WHEREuser_id=?");
        $stmt->bind_param("sssssssii", $username, $email, $first_name, $last_name, $phone_number, $address_1, $address_2, $is_verified, $user_id);

        $stmt->execute();
        $stmt->close();

        echo "User updated successfully.";
    }
}

// Handle Change Password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_POST['user_id'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHEREuser_id=?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        $stmt->execute();
        $stmt->close();

        echo "Password changed successfully.";
    } else {
        echo "Passwords do not match.";
    }
}

// Handle Delete User
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Get customer ID(s) associated with the user
    $stmt = $conn->prepare("SELECT customer_id FROM customers WHERE user_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer_ids = [];
    while ($row = $result->fetch_assoc()) {
        $customer_ids[] = $row['customer_id'];
    }
    $stmt->close();

    // Delete order items associated with the orders
    foreach ($customer_ids as $customer_id) {
        $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id IN (SELECT order_id FROM orders WHERE customer_id = ?)");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $stmt->close();
    }

    // Delete orders associated with the customer IDs
    foreach ($customer_ids as $customer_id) {
        $stmt = $conn->prepare("DELETE FROM orders WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $stmt->close();
    }

    // Delete customer(s) associated with the user
    $stmt = $conn->prepare("DELETE FROM customers WHERE user_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    echo "User deleted successfully.";
}

$sql = "SELECT user_id, username, email, first_name, last_name, phone_number, address_1, address_2, is_verified FROM users";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'hsidebar.php'; ?>
        <div class="flex-1 p-4 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
            <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Users view & edit dashboard</h1>
            <form method="POST" action="" class="mb-6">
                <h2 class="text-2xl font-semibold mb-4">Add User</h2>
                <label class="block font-bold mb-1">Username:</label>
                <input type="text" name="username" required class="w-full p-2 border border-gray-300 rounded mb-4" />
                <label class="block font-bold mb-1">Email:</label>
                <input type="email" name="email" required class="w-full p-2 border border-gray-300 rounded mb-4" />
                <label class="block font-bold mb-1">First Name:</label>
                <input type="text" name="first_name" required class="w-full p-2 border border-gray-300 rounded mb-4" />
                <label class="block font-bold mb-1">Last Name:</label>
                <input type="text" name="last_name" required class="w-full p-2 border border-gray-300 rounded mb-4" />
                <label class="block font-bold mb-1">Phone Number:</label>
                <input type="text" name="phone_number" required class="w-full p-2 border border-gray-300 rounded mb-4" />
                <label class="block font-bold mb-1">Address 1:</label>
                <input type="text" name="address_1" required class="w-full p-2 border border-gray-300 rounded mb-4" />
                <label class="block font-bold mb-1">Address 2:</label>
                <input type="text" name="address_2" class="w-full p-2 border border-gray-300 rounded mb-4" />
                <label class="inline-flex items-center mb-4">
                    <input type="checkbox" name="is_verified" class="form-checkbox h-5 w-5 text-green-600" />
                    <span class="ml-2">Is Verified</span>
                </label>
                <input type="submit" name="add_user" value="Add User" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600" />
            </form>
            <table class="min-w-full border border-gray-300 mb-6">
                <thead>
                    <tr class="bg-green-500 text-white">
                        <th class="border border-gray-300 p-2">ID</th>
                        <th class="border border-gray-300 p-2">Username</th>
                        <th class="border border-gray-300 p-2">Email</th>
                        <th class="border border-gray-300 p-2">First Name</th>
                        <th class="border border-gray-300 p-2">Last Name</th>
                        <th class="border border-gray-300 p-2">Phone Number</th>
                        <th class="border border-gray-300 p-2">Address 1</th>
                        <th class="border border-gray-300 p-2">Address 2</th>
                        <th class="border border-gray-300 p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $rowClass = $row["is_verified"] == 0 ? 'bg-red-100' : '';
                            echo "<tr class='$rowClass'>
                                <td class='border border-gray-300 p-2'>" . $row["user_id"] . "</td>
                                <td class='border border-gray-300 p -2'>" . $row["username"] . "</td>
                                <td class='border border-gray-300 p-2'>" . $row["email"] . "</td>
                                <td class='border border-gray-300 p-2'>" . $row["first_name"] . "</td>
                                <td class='border border-gray-300 p-2'>" . $row["last_name"] . "</td>
                                <td class='border border-gray-300 p-2'>" . $row["phone_number"] . "</td>
                                <td class='border border-gray-300 p-2'>" . $row["address_1"] . "</td>
                                <td class='border border-gray-300 p-2'>" . $row["address_2"] . "</td>
                                <td class='border border-gray-300 p-2'>
                                    <a class='text-blue-600 hover:underline' href='javascript:void(0);' onclick='openEditModal(" . json_encode($row) . ")'>Edit</a>
                                    <a class='text-red-600 hover:underline' href='?delete_id=" . $row["user_id"] . "'>Delete</a>
                                    <a class='text-green-600 hover:underline' href='javascript:void(0);' onclick='openChangePasswordModal(" . $row["user_id"] . ")'>Change Password</a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='border border-gray-300 p-2 text-center'>No users found</td></tr>";
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div id="editUser Modal" class="modal hidden">
        <div class="modal-content bg-white rounded shadow-lg p-6">
            <span class="close">&times;</span>
            <form id="editUser Form" method="POST" action="">
                <h2 class="text-2xl font-semibold mb-4">Edit User</h2>
                <input type="hidden" name="user_id" id="edit_user_id">
                <label class="block font-bold mb-1">Username:</label>
                <input type="text" name="username" id="edit_username" required class="w-full p-2 border border-gray-300 rounded mb-4">
                <label class="block font-bold mb-1">Email:</label>
                <input type="email" name="email" id="edit_email" required class="w-full p-2 border border-gray-300 rounded mb-4">
                <label class="block font-bold mb-1">First Name:</label>
                <input type="text" name="first_name" id="edit_first_name" required class="w-full p-2 border border-gray-300 rounded mb-4">
                <label class="block font-bold mb-1">Last Name:</label>
                <input type="text" name="last_name" id="edit_last_name" required class="w-full p-2 border border-gray-300 rounded mb-4">
                <label class="block font-bold mb-1">Phone Number:</label>
                <input type="text" name="phone_number" id="edit_phone_number" required class="w-full p-2 border border-gray-300 rounded mb-4">
                <label class="block font-bold mb-1">Address 1:</label>
                <input type="text" name="address_1" id="edit_address_1" required class="w-full p-2 border border-gray-300 rounded mb-4">
                <label class="block font-bold mb-1">Address 2:</label>
                <input type="text" name="address_2" id="edit_address_2" class="w-full p-2 border border-gray-300 rounded mb-4">
                <label class="inline-flex items-center mb-4">
                    <input type="checkbox" name="is_verified" id="edit_is_verified" class="form-checkbox h-5 w-5 text-green-600">
                    <span class="ml-2">Is Verified</span>
                </label>
                <input type="submit" name="edit_user" value="Update User" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
            </form>
        </div>
    </div>

    <div id="changePasswordModal" class="modal hidden z-10000">
        <div class="modal-content bg-white rounded shadow-lg p-6">
            <span class="close change-password-close">&times;</span>
            <form id="changePasswordForm" method=" POST" action="">
                <h2 class="text-2xl font-semibold mb-4">Change Password</h2>
                <input type="hidden" name="user_id" id="password_user_id">
                <label class="block font-bold mb-1">New Password:</label>
                <input type="password" name="new_password" id="new_password" required class="w-full p-2 border border-gray-300 rounded mb-4">
                <label class="block font-bold mb-1">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required class="w-full p-2 border border-gray-300 rounded mb-4">
                <input type="submit" name="change_password" value="Change Password" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
            </form>
        </div>
    </div>

    <script>
        var modal = document.getElementById("editUser  Modal");
        var span = document.getElementsByClassName("close")[0];

        function openEditModal(user) {
            document.getElementById("edit_user_id").value = user.id;
            document.getElementById("edit_username").value = user.username;
            document.getElementById("edit_email").value = user.email;
            document.getElementById("edit_first_name").value = user.first_name;
            document.getElementById("edit_last_name").value = user.last_name;
            document.getElementById("edit_phone_number").value = user.phone_number;
            document.getElementById("edit_address_1").value = user.address_1;
            document.getElementById("edit_address_2").value = user.address_2;
            document.getElementById("edit_is_verified").checked = user.is_verified;

            modal.classList.remove("hidden");
        }

        span.onclick = function() {
            modal.classList.add("hidden");
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.classList.add("hidden");
            }
        }

        var changePasswordModal = document.getElementById("changePasswordModal");
        var changePasswordSpan = document.getElementsByClassName("change-password-close")[0];

        function openChangePasswordModal(user_id) {
            document.getElementById("password_user_id").value = user_id;
            changePasswordModal.classList.remove("hidden");
        }

        changePasswordSpan.onclick = function() {
            changePasswordModal.classList.add("hidden");
        }

        window.onclick = function(event) {
            if (event.target == changePasswordModal) {
                changePasswordModal.classList.add("hidden");
            }
        }
    </script>
</body>
</html>