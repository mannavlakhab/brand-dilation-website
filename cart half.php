<?php
session_start();
// Include database connection file
require_once 'db_connect.php';
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

// Product cart management
if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $variation_id = intval($_POST['variation_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    addProductToCart($product_id, $variation_id, $quantity);
}

// Service cart management
if (isset($_GET['service_id'])) {
    $service_id = intval($_GET['service_id']);
    addServiceToCart($service_id);
}

// Add product to the cart function
function addProductToCart($product_id, $variation_id, $quantity) {
    global $conn;

    if (!isset($_SESSION['cart']['products'])) {
        $_SESSION['cart']['products'] = [];
    }

    $cart_key = $product_id . '_' . $variation_id;

    // Fetch stock quantity from the database
    $stmt = $conn->prepare("SELECT stock_quantity FROM Products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stock_quantity = $product['stock_quantity'];

    // Check if requested quantity exceeds stock
    $quantity_to_add = ($quantity > $stock_quantity) ? $stock_quantity : $quantity;

    if (isset($_SESSION['cart']['products'][$cart_key])) {
        $_SESSION['cart']['products'][$cart_key] += $quantity_to_add;
    } else {
        $_SESSION['cart']['products'][$cart_key] = $quantity_to_add;
    }

    $stmt->close();
}

// Add service to the cart function
function addServiceToCart($service_id) {
    global $conn;

    if (!isset($_SESSION['cart']['services'])) {
        $_SESSION['cart']['services'] = [];
    }

    // Fetch the service from the database
    $query = "SELECT * FROM services WHERE id = $service_id";
    $result = mysqli_query($conn, $query);
    $service = mysqli_fetch_assoc($result);

    // Check if the service exists
    if ($service) {
        $_SESSION['cart']['services'][] = [
            'service_id' => $service['id'],
            'service_name' => $service['service_name'],
            'price' => $service['price']
        ];
    }
}

// Remove item from cart function
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['cart_key'])) {
    $cart_key = $_GET['cart_key'];
    removeFromCart($cart_key);
}

function removeFromCart($cart_key) {
    if (isset($_SESSION['cart']['products'][$cart_key])) {
        unset($_SESSION['cart']['products'][$cart_key]);
    } elseif (isset($_SESSION['cart']['services'][$cart_key])) {
        unset($_SESSION['cart']['services'][$cart_key]);
    }
}

// Validate coupon function
function validateCoupon($code) {
    global $conn;
    $stmt = $conn->prepare("SELECT DiscountPercentage FROM couponcodes WHERE Code = ? AND IsActive = 1 AND ExpiryDate >= CURDATE()");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();
        return $coupon['DiscountPercentage'];
    }
    return 0;
}

// Display cart items
function displayCart() {
    global $conn;
    $total_price = 0;

    // Display product cart items
    if (!empty($_SESSION['cart']['products'])) {
        echo "<h2>Your Products</h2>";
        echo '<table class="cart-table">';
        echo "<tr><th>Product</th><th>Quantity</th><th>Price</th><th>Action</th></tr>";
        
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

            $product_name = $product['brand'] . ' ' . $product['model'] .' with '. $product['variation_value'];
            $image_main = $product['image_main'];
            $product_price = ($product['price'] + $product['price_modifier']) * $quantity;
            $total_price += $product_price;

            echo "<tr>";
            echo "<td><img src='{$image_main}' alt='{$product_name}' /><br>$product_name</td>";
            echo "<td class='cart-quantity'><p>$quantity</p></td>";
            echo "<td>₹$product_price</td>";
            echo "<td><button onclick=\"window.location.href='cart.php?action=remove&cart_key=$cart_key'\">Remove</button></td>";
            echo "</tr>";
        }
        echo '</table>';
    }

    // Display service cart items
    if (!empty($_SESSION['cart']['services'])) {
        echo "<h2>Your Services</h2>";
        echo '<table class="cart-table">';
        echo "<tr><th>Service</th><th>Price</th><th>Action</th></tr>";

        foreach ($_SESSION['cart']['services'] as $key => $service) {
            $total_price += $service['price'];
            echo "<tr>";
            echo "<td>{$service['service_name']}</td>";
            echo "<td>₹{$service['price']}</td>";
            echo "<td><button onclick=\"window.location.href='cart.php?action=remove&cart_key=$key'\">Remove</button></td>";
            echo "</tr>";
        }
        echo '</table>';
    }

    return $total_price;
}

$total_price_before_discount = displayCart();

// Coupon application
$discount = 0;
if (isset($_POST['apply_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    $discount = validateCoupon($coupon_code);
    if ($discount > 0) {
        $total_price_before_discount *= (1 - $discount / 100);
    }
}

echo "<h3>Total Price Before Discount: ₹" . number_format($total_price_before_discount, 2) . "</h3>";
if ($discount > 0) {
    echo "<h3>Discount Applied: $discount%</h3>";
    echo "<h3>Total Price After Discount: ₹" . number_format($total_price_before_discount, 2) . "</h3>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="./assets/css/cart.css">
</head>
<body>
    <div class="cart-container">
        <h1>Your Shopping Cart</h1>
        <form method="post">
            <input type="text" name="coupon_code" placeholder="Coupon Code">
            <button type="submit" name="apply_coupon">Apply Coupon</button>
        </form>

        <form method="post" action="checkout.php">
    <input type="hidden" name="total_price" value="<?php echo $total_price_before_discount; ?>">
    <input type="hidden" name="coupon_code" value="<?php echo isset($coupon_code) ? $coupon_code : ''; ?>">
    <input type="hidden" name="discount" value="<?php echo $discount; ?>">
    <button type="submit" class="checkout-btn">Proceed to Checkout</button>
</form>

    </div>
</body>
</html>
