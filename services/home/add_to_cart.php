<?php
session_start(); // Always start the session to access session variables

include 'db_connection.php'; // Make sure this points to your database connection file


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if "Remember Me" cookie is set
    if (isset($_COOKIE['user_id'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      // Optionally, you can re-validate the user with the database here
    } else {
      // If login is optional, do not redirect, just leave session unset
      // If you want to prompt the user to log in, you can show a message or an optional login button
      // Optionally, store the current page in session for redirection after optional login
      $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
      // You can choose to show a notification or a button to log in here
    }
}


// Check if service_id is set in the URL
if (isset($_GET['service_id'])) {
    $service_id = intval($_GET['service_id']); // Cast to integer for safety

    // Fetch the service from the database
    $query = "SELECT * FROM services WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $service_id); // Bind the service_id parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();

    // Check if the service exists
    if ($service) {
        // Initialize cart if it's not already set
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add the service to the cart
        $_SESSION['cart'][] = [
            'service_id' => $service['id'],
            'service_name' => $service['service_name'],
            'price' => $service['price']
        ];

        // Redirect to the cart page after adding the service
        // header('Location: cart.php');
    } else {
        echo "Service not found!";
    }
} else {
    echo "No service ID provided!";
}

// Handle removing an item from the cart
if (isset($_GET['remove'])) {
    $remove_service_id = intval($_GET['remove']); // Cast to integer for safety

    // Find the item in the cart and remove it
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['service_id'] == $remove_service_id) {
            unset($_SESSION['cart'][$key]);
            // Reindex the array after removing the item
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
}

// Handle clearing the entire cart
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']); // Clear the cart
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Cart</title>
</head>
<body>
    <h1>Your Cart</h1>
    
    <div class="cart-items">
        <?php if (!empty($_SESSION['cart'])) { ?>
            <ul>
                <?php foreach ($_SESSION['cart'] as $item) { ?>
                    <li>
                        <?= htmlspecialchars($item['service_name']) ?> - $<?= htmlspecialchars($item['price']) ?>
                        <a href="add_to_cart.php?remove=<?= htmlspecialchars($item['service_id']) ?>" style="color: red;">Remove</a>
                    </li>
                <?php } ?>
            </ul>
            <a href="checkout.php">Proceed to Checkout</a>
            <br><br>
            <a href="add_to_cart.php?clear=true" style="color: red;">Clear Cart</a>
        <?php } else { ?>
            <p>Your cart is empty!</p>
        <?php } ?>
    </div>
</body>
</html>
