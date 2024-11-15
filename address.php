<?php
session_start();
require_once 'db_connect.php'; // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if "Remember Me" cookie is set
    if (isset($_COOKIE['user_id'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      // Optionally, you can re-validate the user with the database here
  } else {
    // Store the current page in session to redirect after login
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit();
  }}

$user_id = $_SESSION['user_id'];

// Initialize variables for form data and error handling
$address_id = $address_line_1 = $address_line_2 = $city = $state = $postal_code = $country = '';
$error = '';

// Handle address addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_address'])) {
    $address_line_1 = $_POST['address_line_1'];
    $address_line_2 = $_POST['address_line_2'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];

    // Insert new address into the database
    $stmt = $conn->prepare("INSERT INTO addresses (user_id, address_line_1, address_line_2, city, state, postal_code, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $address_line_1, $address_line_2, $city, $state, $postal_code, $country);

    if ($stmt->execute()) {
        header("Location: address.php"); // Refresh to show the updated list
        exit();
    } else {
        $error = "Error adding address: " . $stmt->error;
    }
    $stmt->close();
}

// Handle address editing
if (isset($_GET['edit'])) {
    $address_id = $_GET['edit'];

    // Fetch address details for editing
    $stmt = $conn->prepare("SELECT * FROM addresses WHERE address_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $address_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $address = $result->fetch_assoc();
        $address_line_1 = $address['address_line_1'];
        $address_line_2 = $address['address_line_2'];
        $city = $address['city'];
        $state = $address['state'];
        $postal_code = $address['postal_code'];
        $country = $address['country'];
    }
    $stmt->close();
}

// Handle address update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_address'])) {
    $address_id = $_POST['address_id'];
    $address_line_1 = $_POST['address_line_1'];
    $address_line_2 = $_POST['address_line_2'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];

    // Update the address in the database
    $stmt = $conn->prepare("UPDATE addresses SET address_line_1 = ?, address_line_2 = ?, city = ?, state = ?, postal_code = ?, country = ? WHERE address_id = ? AND user_id = ?");
    $stmt->bind_param("ssssssii", $address_line_1, $address_line_2, $city, $state, $postal_code, $country, $address_id, $user_id);

    if ($stmt->execute()) {
    } else {
        $error = "Error updating address: " . $stmt->error;
    }
    $stmt->close();
}

// Handle address deletion
if (isset($_GET['delete'])) {
    $address_id = $_GET['delete'];

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM addresses WHERE address_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $address_id, $user_id);

    if ($stmt->execute()) {
        header("Location: address.php");
        exit();
    } else {
        $error = "Error deleting address: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all addresses for the logged-in user
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$addresses_result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Addresses</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <div class="address-management-container">
        <h1>Manage Addresses</h1>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Add Address Form -->
        <h2><?php echo $address_id ? 'Edit Address' : 'Add New Address'; ?></h2>
        <form method="post" action="">
            <input type="hidden" name="address_id" value="<?php echo $address_id; ?>">
            <label for="address_line_1">Address Line 1:</label>
            <input type="text" id="address_line_1" name="address_line_1" value="<?php echo htmlspecialchars($address_line_1); ?>" required>

            <label for="address_line_2">Address Line 2:</label>
            <input type="text" id="address_line_2" name="address_line_2" value="<?php echo htmlspecialchars($address_line_2); ?>">

            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required>

            <label for="state">State:</label>
            <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($state); ?>" required>

            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($postal_code); ?>" required>

            <label for="country">Country:</label>
            <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($country); ?>" required>

            <?php if ($address_id): ?>
                <button type="submit" name="update_address">Update Address</button>
            <?php else: ?>
                <button type="submit" name="add_address">Add Address</button>
            <?php endif; ?>
        </form>

        <!-- View Addresses -->
        <h2>Your Addresses</h2>
        <table>
            <thead>
                <tr>
                    <th>Address Line 1</th>
                    <th>Address Line 2</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Postal Code</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($address = $addresses_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($address['address_line_1']); ?></td>
                        <td><?php echo htmlspecialchars($address['address_line_2']); ?></td>
                        <td><?php echo htmlspecialchars($address['city']); ?></td>
                        <td><?php echo htmlspecialchars($address['state']); ?></td>
                        <td><?php echo htmlspecialchars($address['postal_code']); ?></td>
                        <td><?php echo htmlspecialchars($address['country']); ?></td>
                        <td>
                            <a href="?edit=<?php echo $address['address_id']; ?>">Edit</a>
                            <a href="?delete=<?php echo $address['address_id']; ?>" onclick="return confirm('Are you sure you want to delete this address?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
