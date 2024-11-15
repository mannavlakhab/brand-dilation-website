<?php
session_start();
// Include the database connection file
require_once 'db_connect.php';

// Initialize variables for form data and discount handling
$first_name = $last_name = $email = $phone_number = $address = '';
$total_price_before_discount = 0;
$total_price_after_discount = 0;
$coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
$discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
$error = '';

// Fetch user details and addresses if logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user details from the Users table
    $stmt = $conn->prepare("SELECT first_name, last_name, email, phone_number FROM Users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $first_name = $user['first_name'];
        $last_name = $user['last_name'];
        $email = $user['email'];
        $phone_number = $user['phone_number'];
    }
    $stmt->close();

    // Fetch user addresses from the Addresses table
    $stmt = $conn->prepare("SELECT address_id, address_line_1, address_line_2 FROM addresses WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $addresses_result = $stmt->get_result();
    $addresses = [];

    while ($row = $addresses_result->fetch_assoc()) {
        $addresses[] = $row;
    }
    $stmt->close();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect customer information from the form
    $selected_address_id = isset($_POST['address_id']) ? $_POST['address_id'] : '';
    
    // Additional validation for email and phone number can be added here
    // Example: Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }
}
// Function to display cart items and calculate both total prices (before and after discount)
function displayCheckoutCart($discount) {
    global $conn;

    // Convert discount percentage to decimal
    $discount_decimal = $discount / 100;

    $total_price_before_discount = 0;
    $total_price_after_discount = 0;

    // Display product cart items
    if (!empty($_SESSION['cart']['products'])) {
        echo "<h2>Your Products</h2>";
        echo '<table class="cart-table">';
        echo "<tr><th>Product</th><th>Quantity</th><th>Original Price</th><th>Discounted Price</th></tr>";

        foreach ($_SESSION['cart']['products'] as $cart_key => $quantity) {
            list($product_id, $variation_id) = explode('_', $cart_key);
            $stmt = $conn->prepare("SELECT P.brand, P.model, P.image_main, P.price, PV.variation_value, PV.price_modifier
                FROM Products P
                JOIN ProductVariations PV ON P.product_id = PV.product_id
                WHERE P.product_id = ? AND PV.variation_id = ?");
            $stmt->bind_param("ii", $product_id, $variation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            $product_name = $product['brand'] . ' ' . $product['model'] . ' with ' . $product['variation_value'];
            $image_main = $product['image_main'];
            $original_price = ($product['price'] + $product['price_modifier']) * $quantity;

            // Calculate the discounted price for this product
            $discounted_price = $original_price * (1 - $discount_decimal);

            // Add to total prices
            $total_price_before_discount += $original_price;
            $total_price_after_discount += $discounted_price;

            echo "<tr>";
            echo "<td><img src='{$image_main}' alt='{$product_name}' /><br>$product_name</td>";
            echo "<td class='cart-quantity'><p>$quantity</p></td>";
            echo "<td>₹" . number_format($original_price, 2) . "</td>";
            echo "<td>₹" . number_format($discounted_price, 2) . "</td>";
            echo "</tr>";
        }
        echo '</table>';
    }

    // Display service cart items
    if (!empty($_SESSION['cart']['services'])) {
        echo "<h2>Your Services</h2>";
        echo '<table class="cart-table">';
        echo "<tr><th>Service</th><th>Original Price</th><th>Discounted Price</th></tr>";

        foreach ($_SESSION['cart']['services'] as $service) {
            $service_price = $service['price'];

            // Calculate the discounted price for this service
            $discounted_service_price = $service_price * (1 - $discount_decimal);

            // Add to total prices
            $total_price_before_discount += $service_price;
            $total_price_after_discount += $discounted_service_price;

            echo "<tr>";
            echo "<td>{$service['service_name']}</td>";
            echo "<td>₹" . number_format($service_price, 2) . "</td>";
            echo "<td>₹" . number_format($discounted_service_price, 2) . "</td>";
            echo "</tr>";
        }
        echo '</table>';
    }

    // Return both total prices (before and after discount)
    return [
        'total_before_discount' => $total_price_before_discount,
        'total_after_discount' => $total_price_after_discount
    ];
}

// Display the cart items on the checkout page
$totals = displayCheckoutCart($discount);
$total_price_before_discount = $totals['total_before_discount'];
$total_price_after_discount = $totals['total_after_discount'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="./assets/css/cart.css">
</head>
<body>
    <div class="checkout-container">
        <h1>Checkout</h1>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- Checkout Form -->
        <form method="post" action="">
            <h2>Customer Information</h2>

            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <label for="phone_number">Phone Number:</label>
            <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>

            <label for="address_id">Select Address:</label>
            <select id="address_id" name="address_id" required>
                <option value="">Select an address</option>
                <?php foreach ($addresses as $address): ?>
                    <option value="<?php echo $address['address_id']; ?>">
                        <?php echo htmlspecialchars($address['address_line_1'] . ' ' . $address['address_line_2']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="coupon_code">Coupon Code:</label>
            <input type="text" id="coupon_code" name="coupon_code" value="<?php echo htmlspecialchars($coupon_code); ?>">

            <button type="submit">Place Order</button>
        </form>

        <h2>Order Summary</h2>
        <p>Total Price Before Discount: ₹<?php echo number_format($total_price_before_discount, 2); ?></p>
        <p>Total Price After Discount: ₹<?php echo number_format($total_price_after_discount, 2); ?></p>
    </div>
</body>
</html>
