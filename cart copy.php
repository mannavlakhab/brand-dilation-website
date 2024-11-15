<?php
session_start();
// Connect to the database
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

// Fetch data from POST request
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Declare constants based on fetched data
if ($product_id > 0 && $variation_id > 0 && $quantity > 0) {
    define('PRODUCT_ID', $product_id);
    define('VARIATION_ID', $variation_id);
    define('QUANTITY', $quantity);
}

// Function to add item to cart
function addToCart() {
    global $conn;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    
    $cart_key = PRODUCT_ID . '_' . VARIATION_ID;

    // Assign constants to variables so they can be passed by reference
    $product_id = PRODUCT_ID;
    $quantity = QUANTITY;

    // Fetch the stock quantity from the database
    $stmt = $conn->prepare("SELECT stock_quantity FROM Products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stock_quantity = $product['stock_quantity'];
    
    // If the requested quantity exceeds stock, set it to the available stock
    if ($quantity > $stock_quantity) {
        $quantity_to_add = $stock_quantity;
    } else {
        $quantity_to_add = $quantity;
    }
    
    if (isset($_SESSION['cart'][$cart_key])) {
        $_SESSION['cart'][$cart_key] += $quantity_to_add;
    } else {
        $_SESSION['cart'][$cart_key] = $quantity_to_add;
    }

    $stmt->close();
}

// Function to remove item from cart
function removeFromCart($cart_key) {
    if (isset($_SESSION['cart'][$cart_key])) {
        unset($_SESSION['cart'][$cart_key]);
    }
}

// Function to validate coupon
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

// Function to display cart
function displayCart() {
    global $conn;
    if (!empty($_SESSION['cart'])) {
        $conn = mysqli_connect("localhost", "root", "", "shop");
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        echo '<table class="cart-table">';
        echo "<tr><th>Product</th><th>Quantity</th><th>Price</th><th>Action</th></tr>";
        
        $total_price = 0;

        foreach ($_SESSION['cart'] as $cart_key => $quantity) {
            list($product_id, $variation_id) = explode('_', $cart_key);
            $product_id = intval($product_id);
            $variation_id = intval($variation_id);
            
            $sql = "SELECT P.brand, P.stock_quantity, P.model, P.image_main, P.price, PV.variation_value, PV.price_modifier
            FROM Products P
            JOIN ProductVariations PV ON P.product_id = PV.product_id
            WHERE P.product_id = ? AND PV.variation_id = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $product_id, $variation_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if (!$result) {
                printf("Error: %s\n", mysqli_error($conn));
                exit();
            }
            
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                $product_name = $product['brand'] . ' ' . $product['model'] .' with '. $product['variation_value'];
                $image_main = $product['image_main'];
                $variation_details = $product['variation_value'];
                $product_price_n = $product['price'] + $product['price_modifier'];
                $stock_quantity = $product['stock_quantity'];
               
                $product_price = $product_price_n * $quantity;
                $total_price += $product_price;

                echo "<tr>";
                echo "<td><img src='{$image_main}' alt='{$product_name}' /><br>$product_name</td>";
                echo "<td class='cart-quantity'><p>$quantity</p></td>";
                echo "<td>₹$product_price</td>";
                echo "<td>
                <button onclick=\"window.location.href='cart.php?action=remove&cart_key=$cart_key'\" class='cart-remove-btn'>
                    <svg viewBox='0 0 448 512' class='svgIcon'><path d='M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z'></path></svg>
                </button>
            </td>";
                echo "</tr>";
            }
        }
        echo "</table>";

        // Display coupon input form
        echo '<form style="display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: center;
    align-items: stretch;" method="post" action="">
                <input placeholder="Coupon code" class="hello-input" type="text" name="coupon_code" id="coupon_code" required>
<button type="submit" name="apply_coupon" class="btn-trick-new">Apply coupon</button>
              </form>';
 echo '
<table>
  <tr></tr>
  <tr></tr>
  <tr></tr>
  <tr rowspan=3 style="float: inline-end;">';

// Store the original total price
$total_price_before_discount = $total_price; 

// Display total price before any discount
echo "<tr><td>Total Price: ₹" . number_format($total_price_before_discount, 2) . "</td></tr>";

$discount = 0;

if (isset($_POST['apply_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    $discount = validateCoupon($coupon_code);
    if ($discount > 0) {
        $total_price *= (1 - $discount / 100); // Apply discount
        echo "<td>Coupon applied! Discount: $discount% (Total before discount: ₹" . number_format($total_price_before_discount, 2) . ")</td>";
    } else {
        echo "<td>Invalid or expired coupon code.</td>";
    }
}

echo "<tr><td>Total Price after discount: ₹" . number_format($total_price, 2) . "</td></tr>
  </tr>
</table>";


        $stmt->close();
        mysqli_close($conn);

        // Display the checkout button only if the cart is not empty
        echo '<form method="post" action="checkout.php">
    <input type="text" name="total_price" value=' . $total_price. '>
    <center>
        <button type="submit" name="proceed_to_checkout" value="Proceed to Checkout" class="cart-checkout-btn">
            Proceed to Checkout
            <svg fill="currentColor" viewBox="0 0 24 24" class="icon">
                <path
                    clip-rule="evenodd"
                    d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm4.28 10.28a.75.75 0 000-1.06l-3-3a.75.75 0 10-1.06 1.06l1.72 1.72H8.25a.75.75 0 000 1.5h5.69l-1.72 1.72a.75.75 0 101.06 1.06l3-3z"
                    fill-rule="evenodd"
                ></path>
            </svg>
        </button>
    </center>
</form>';
    } else {
        echo "<button onclick=\"window.location.href='index.php'\" class='shopping'>
    <span><center><img style=' margin:2%; width: 660px;height: 100%;' src='../assets/img/cart.png'></center></span>
    </button>";
    }
}

if (isset($_POST['add_to_cart'])) {
    addToCart();
}

if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['cart_key'])) {
    $cart_key = $_GET['cart_key'];
    removeFromCart($cart_key);
}
?>


<!DOCTYPE html>
<html>
<head>
    
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart</title>
  <link rel="stylesheet" href="../assets/css/cart.css">
  <link rel="stylesheet" href="../assets/css/btn.css">

  
  <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <!--=============== file loader ===============-->
    <!--=============== header ===============-->
    <script src="../assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <script>
        $(function () {
            $('#header').load('../pages/header.php');

        });
    </script>
    <!--=============== footer ===============-->
    <script>
        $(function () {
            $('#footer').load('../pages/footer.php');

        });
    </script>
</head>
<body>
        
        <!--=============== HEADER ===============-->
    <span id="header"></span>
        
    
    
    
        <main  style="margin-top:13%">
<div class="cart-container">
    <h2>Your Cart</h2>
    <?php displayCart(); ?>
    <br>
</div>

    </main>
      <!--=============== HEADER ===============-->
      <div id="footer"></div>
</body>
</html>
