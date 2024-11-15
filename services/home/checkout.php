<?php
session_start();
include 'db_connection.php'; // Include your database connection file

$total_price = 0;

// Calculate total price from the session cart
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'];
    }
}

$addresses = []; // Array to hold user's addresses
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch addresses if user is logged in
if ($user_id) {
    $address_query = "SELECT address_id, address_line_1, address_line_2, city, state, postal_code, country FROM addresses WHERE user_id = ?";
    $stmt = $conn->prepare($address_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all addresses for the user
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row; // Store addresses in the array
    }

    // Fetch user details from the users table
    $user_query = "SELECT first_name, last_name, email, phone_number FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch user details
        $user = $result->fetch_assoc();
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $email = $user['email'];
        $phone_number = $user['phone_number'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the customer data from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];

    // Insert customer data into the customers table
$customer_query = "INSERT INTO customers (first_name, last_name, email, phone_number, address" . (isset($_SESSION['user_id']) ? ", user_id" : "") . ") VALUES (?, ?, ?, ?, ?" . (isset($_SESSION['user_id']) ? ", ?)" : ")");
$stmt = $conn->prepare($customer_query);

if (isset($_SESSION['user_id'])) {
    // For logged-in users, bind 6 parameters
    $stmt->bind_param('sssssi', $first_name, $last_name, $email, $phone_number, $address, $user_id);
} else {
    // For guests, bind 5 parameters
    $stmt->bind_param('sssss', $first_name, $last_name, $email, $phone_number, $address);
}

    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
        exit;
    }
    
    // Get the customer_id of the inserted customer
    $customer_id = $conn->insert_id;

    // Generate a unique tracking ID
    $tracking_id = uniqid('BD-', true);
    
    // Insert the booking into the bookings table
    $booking_query = "INSERT INTO bookings (status, total_price, customer_id, tracking_id) VALUES ('pending', ?, ?, ?)";
    $stmt = $conn->prepare($booking_query);
    $stmt->bind_param('dis', $total_price, $customer_id, $tracking_id);
    
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
        exit;
    }
    
    // Get the booking_id of the inserted booking
    $booking_id = $conn->insert_id;

    // Insert each item from the cart into the order_items table
    foreach ($_SESSION['cart'] as $item) {
        $service_id = $item['service_id']; // Assuming service_id is in the cart
        $price = $item['price'];

        $order_items_query = "INSERT INTO order_items (booking_id, service_id, price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($order_items_query);
        $stmt->bind_param('iid', $booking_id, $service_id, $price);
        
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
            exit;
        }
    }

    // Redirect to the thank you page
    header("Location: thank_you.php?tracking_id=" . $tracking_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout</h1>
    <p>Total Price: $<?= number_format($total_price, 2) ?></p>

    <!-- Customer Information Form -->
    <form action="checkout.php" method="POST">
        <h3>Customer Information</h3>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name ?? '') ?>" required><br>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name ?? '') ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($phone_number ?? '') ?>" required><br>

        <label for="address">Address:</label>
        <select id="address" name="address" required>
            <option value="" disabled selected>Select an address</option>
            <?php foreach ($addresses as $addr): ?>
                <option value="<?= htmlspecialchars($addr['address_line_1'] . ', ' . ($addr['address_line_2'] ? $addr['address_line_2'] . ', ' : '') . $addr['city'] . ', ' . $addr['state'] . ' ' . $addr['postal_code'] . ', ' . $addr['country']) ?>">
                    <?= htmlspecialchars($addr['address_line_1'] . ', ' . ($addr['address_line_2'] ? $addr['address_line_2'] . ', ' : '') . $addr['city'] . ', ' . $addr['state'] . ' ' . $addr['postal_code'] . ', ' . $addr['country']) ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit">Place Order</button>
    </form>
</body>
</html>
