<?php
session_start();
include 'db_connect.php'; // Include your database connection
// Check if user is logged in
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

// Assuming user_id is stored in session after login
$user_id = $_SESSION['user_id'];

// Function to check if a product exists
function productExists($conn, $product_id) {
    $query = "SELECT COUNT(*) FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Handle adding to whitelist
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $product_id = intval($_POST['product_id']);

    // Check if product exists
    if (!productExists($conn, $product_id)) {
        $message = "Product ID does not exist.";
    } else {
        // Check if product is already whitelisted by the user
        $check_query = "SELECT id FROM whitelist WHERE product_id = ? AND user_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param('ii', $product_id, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            // Add product to whitelist for this user
            $query = "INSERT INTO whitelist (product_id, user_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $product_id, $user_id);

            if ($stmt->execute()) {
                $update_query = "UPDATE products SET is_whitelisted = 1 WHERE product_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param('i', $product_id);
                $update_stmt->execute();
                $message = "Product added to your whitelist.";
            } else {
                $message = "Error adding product to whitelist.";
            }
            $stmt->close();
        } else {
            $message = "Product is already in your whitelist.";
        }
    }
}

// Handle removing from whitelist
if (isset($_POST['action']) && $_POST['action'] === 'remove') {
    $product_id = intval($_POST['product_id']);

    // Check if product exists
    if (!productExists($conn, $product_id)) {
        $message = "Product ID does not exist.";
    } else {
        // Remove product from user's whitelist
        $query = "DELETE FROM whitelist WHERE product_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $product_id, $user_id);

        if ($stmt->execute()) {
            $update_query = "UPDATE products SET is_whitelisted = 0 WHERE product_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('i', $product_id);
            $update_stmt->execute();
            $message = "Product removed from your whitelist.";
        } else {
            $message = "Error removing product from whitelist.";
        }
        $stmt->close();
    }
}

// Fetch whitelisted products for this user
$query = "SELECT * FROM products WHERE product_id IN (SELECT product_id FROM whitelist WHERE user_id = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Whitelist Management</title>
    <link rel="stylesheet" href="../assets/css/SHOP.css">
</head>
<body>
    <h1>Whitelist Management</h1>

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h2>Add/Remove Product from Whitelist</h2>
    <form method="post">
        <label for="product_id">Product ID:</label>
        <input type="number" id="product_id" name="product_id" required>
        <button type="submit" name="action" value="add">Add to Whitelist</button>
        <button type="submit" name="action" value="remove">Remove from Whitelist</button>
    </form>

    <h2>Your Whitelisted Products</h2>
    <div id="product-list">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="products-card">
                    <a href="../pd/?product_id=<?php echo htmlspecialchars($product['product_id']); ?>">
                        <div class="card-img"><img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($product['image_main']); ?>" alt="<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>"></div>
                        <div class="card-title"><?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?></div>
                        <div class="card-subtitle"><?php echo htmlspecialchars($product['short_des']); ?></div>
                        <hr class="card-divider">
                        <div class="card-footer">
                            <div class="card-price"><span>₹</span><?php echo htmlspecialchars($product['price']); ?></div>
                            <button class="card-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="m397.78 316h-205.13a15 15 0 0 1 -14.65-11.67l-34.54-150.48a15 15 0 0 1 14.62-18.36h274.27a15 15 0 0 1 14.65 18.36l-34.6 150.48a15 15 0 0 1 -14.62 11.67zm-193.19-30h181.25l27.67-120.48h-236.6z"></path>
                                    <path d="m222 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z"></path>
                                    <path d="m368.42 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z"></path>
                                    <path d="m158.08 165.49a15 15 0 0 1 -14.23-10.26l-25.71-77.23h-47.44a15 15 0 1 1 0-30h58.3a15 15 0 0 1 14.23 10.26l29.13 87.49a15 15 0 0 1 -14.23 19.74z"></path>
                                </svg>
                            </button>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No whitelisted products found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
