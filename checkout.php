<?php
session_start();
require_once 'db_connect.php'; // Connect to your database

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

// Check if the user is returning to the checkout page after starting an order
if (isset($_SESSION['order_in_progress']) && $_SESSION['order_in_progress'] === true) {
    // Redirect them to another page (e.g., Profile page)
    unset($_SESSION['cart']);
    unset($_SESSION['order_in_progress']);
    echo '<!DOCTYPE html>
<html>
<head>

<script src="../assets/js/internet-check.js" defer></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>processing.....................................</title>
</head>
<body>
<div class="ab-o-oa" aria-hidden="true">
<div class="ZAnhre">
                 <img class="wF0Mmb" src="../assets/processed.svg" width="300px" height="300px" ></div>
                <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">Your process is continued </div>
                 <div class="ab-o-oa-qc-r">For more info Go to <a href="../profile">"Profile Page"</a></div></div><br>
     <button class="btn-trick-new" onclick="history.go(1)">Go</button>
             </div>
             <style>
                 
.ab-o-oa{
 display: flex;
 flex-direction: column;
 align-content: center;
 justify-content: center;
 align-items: center;
 width: fit-;
 -webkit-user-select: none;
 -ms-user-select: none;
 user-select: none;
 font-family: Montserrat, sans-serif;

}
.ab-o-oa-r{
 display: contents;
}
.ab-o-oa-qc-V{
 font-weight :800;

}
.ab-o-oa-qc-r{
 font-weight :normal;

}
 
/* From Uiverse.io by e-coders */ 
.btn-trick-new {
 appearance: none;
 background-color: #FAFBFC;
 border: 1px solid rgba(27, 31, 35, 0.15);
 border-radius: 6px;
 box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
 box-sizing: border-box;
 color: #24292E;
 cursor: pointer;
 display: inline-block;
 font-family: "Montserrat", sans-serif;
 font-size: 14px;
 font-weight: 700;
 line-height: 20px;
 list-style: none;
 padding: 6px 16px;
 position: relative;
 transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
 user-select: none;
 -webkit-user-select: none;
 touch-action: manipulation;
 vertical-align: middle;
 white-space: nowrap;
 word-wrap: break-word;
}

.btn-trick-new:hover {
 background-color: #F3F4F6;
 text-decoration: none;
 transition-duration: 0.1s;
}

.btn-trick-new:disabled {
 background-color: #FAFBFC;
 border-color: rgba(27, 31, 35, 0.15);
 color: #959DA5;
 cursor: default;
}

.btn-trick-new:active {
 background-color: #EDEFF2;
 box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
 transition: none 0s;
}

.btn-trick-new:focus {
 outline: 1px transparent;
}

.btn-trick-new:before {
 display: none;
}

.btn-trick-new:-webkit-details-marker {
 display: none;
}
</style>

</body>
</html>';
    exit();
}

// Function to fetch user details from `users` table
function getUserDetails($user_id) {
  global $conn;
  $sql = "SELECT user_id, first_name, last_name, email, phone_number FROM users WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  $stmt->close();
  return $user;
}

// Function to fetch user addresses from `addresses` table
function getUserAddresses($user_id) {
  global $conn;
  $sql = "SELECT * FROM addresses WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $addresses = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
  return $addresses;
}

// After checking user session, retrieve user and addresses
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $user = getUserDetails($user_id);
  $addresses = getUserAddresses($user_id); // Fetch addresses
}

// Function to fetch product details from `Products` and `ProductVariations` tables
function getProductDetails($product_id, $variation_id) {
    global $conn;
    $sql = "SELECT P.brand, P.model, PV.variation_value, P.price, PV.price_modifier
            FROM Products P
            JOIN ProductVariations PV ON P.product_id = PV.product_id
            WHERE P.product_id = ? AND PV.variation_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $product_id, $variation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}

// Function to insert order into `orders` table
function insertOrder($customer_id, $order_status, $shipping_address, $total_price, $shipping_cost, $payment_method, $payment_status, $payment_details, $tracking_id) {
    global $conn;
    $order_date = date('Y-m-d H:i:s');
    $sql = "INSERT INTO orders (customer_id, order_status, shipping_address, total_price, shipping_cost, payment_method, payment_status, payment_details, tracking_id, order_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssdsssss", $customer_id, $order_status, $shipping_address, $total_price, $shipping_cost, $payment_method, $payment_status, $payment_details, $tracking_id, $order_date);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get the inserted order ID
    $stmt->close();
    return $order_id;
}

// Check if user is logged in and retrieve their details
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user = getUserDetails($user_id);
}

// Initialize total price
   // Get the total price from the form
   // Calculate total price from cart items
   if (!empty($_SESSION['cart'])) {
     foreach ($_SESSION['cart'] as $cart_key => $quantity) {
        if (strpos($cart_key, '_') !== false) {
            list($product_id, $variation_id) = explode('_', $cart_key);
        } else { continue; // or some other error handling
        }
       $product = getProductDetails($product_id, $variation_id);
       $total_price = isset($_POST['total_price']) ? floatval($_POST['total_price']) : 0;
      }}
      else{
      echo '<!DOCTYPE html>
<html>
<head>

<script src="../assets/js/internet-check.js" defer></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>processing.....................................</title>
</head>
<body>
<div class="ab-o-oa" aria-hidden="true">
<div class="ZAnhre">
                 <img class="wF0Mmb" src="../assets/processed.svg" width="300px" height="300px" ></div>
                <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">Your process is continued </div>
                 <div class="ab-o-oa-qc-r">For more info Go to <a href="../profile">"Profile Page"</a></div></div><br>
     <button class="btn-trick-new" onclick="history.go(1)">Go</button>
             </div>
             <style>
                 
.ab-o-oa{
 display: flex;
 flex-direction: column;
 align-content: center;
 justify-content: center;
 align-items: center;
 width: fit-;
 -webkit-user-select: none;
 -ms-user-select: none;
 user-select: none;
 font-family: Montserrat, sans-serif;

}
.ab-o-oa-r{
 display: contents;
}
.ab-o-oa-qc-V{
 font-weight :800;

}
.ab-o-oa-qc-r{
 font-weight :normal;

}
 
/* From Uiverse.io by e-coders */ 
.btn-trick-new {
 appearance: none;
 background-color: #FAFBFC;
 border: 1px solid rgba(27, 31, 35, 0.15);
 border-radius: 6px;
 box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
 box-sizing: border-box;
 color: #24292E;
 cursor: pointer;
 display: inline-block;
 font-family: "Montserrat", sans-serif;
 font-size: 14px;
 font-weight: 700;
 line-height: 20px;
 list-style: none;
 padding: 6px 16px;
 position: relative;
 transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
 user-select: none;
 -webkit-user-select: none;
 touch-action: manipulation;
 vertical-align: middle;
 white-space: nowrap;
 word-wrap: break-word;
}

.btn-trick-new:hover {
 background-color: #F3F4F6;
 text-decoration: none;
 transition-duration: 0.1s;
}

.btn-trick-new:disabled {
 background-color: #FAFBFC;
 border-color: rgba(27, 31, 35, 0.15);
 color: #959DA5;
 cursor: default;
}

.btn-trick-new:active {
 background-color: #EDEFF2;
 box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
 transition: none 0s;
}

.btn-trick-new:focus {
 outline: 1px transparent;
}

.btn-trick-new:before {
 display: none;
}

.btn-trick-new:-webkit-details-marker {
 display: none;
}
</style>

</body>
</html>';
            exit();
          }
          
          // Process checkout when the form is submitted
          if (isset($_POST['submit_order'])) {
            // Retrieve form data
            $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $shipping_address = $_POST['address'];
    
    // Check if a custom address was provided
    if (isset($_POST['custom_address']) && !empty($_POST['custom_address'])) {
      $shipping_address = $_POST['custom_address'];
    } else {
      $shipping_address = $_POST['address'];
    }
    
    // Insert customer data into `Customers` table
    $sql = "INSERT INTO Customers (first_name, last_name, email, phone_number, address, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone_number, $shipping_address, $user_id);
    $stmt->execute();
    $customer_id = $stmt->insert_id;
    $stmt->close();
  
    // Prepare order data
    $order_status = 'pending';
    $payment_status = 'unpaid';
    $tracking_id = uniqid('brand-track-');
    $payment_screenshot = '';
    $payment_method = '';
    
    // Insert order into `orders` table
    $order_id = insertOrder($customer_id, $order_status, $shipping_address, $total_price, 0, $payment_method, $payment_status, $payment_screenshot, $tracking_id);

    // Insert order items into `order_items` table
foreach ($_SESSION['cart'] as $cart_key => $quantity) {
    // Ensure cart_key has the correct format
    if (strpos($cart_key, '_') !== false) {
        list($product_id, $variation_id) = explode('_', $cart_key);

        // Fetch product details from the database
        $product = getProductDetails($product_id, $variation_id);

        // Ensure product details are retrieved successfully
        if ($product) {
            $product_name = $product['brand'] . ' ' . $product['model'];
            $variation_details = isset($product['variation_value']) ? $product['variation_value'] : ''; // Safeguard for variation_value
            $product_price = (isset($product['price']) ? $product['price'] : 0) + (isset($product['price_modifier']) ? $product['price_modifier'] : 0);
            $product_price *= $quantity; // Multiply after ensuring it's a number
            
            // Insert each order item
            $sql = "INSERT INTO order_items (order_id, product_id, product_attributes, product_quantity, order_item_price, variation_id)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisidi", $order_id, $product_id, $variation_details, $quantity, $product_price, $variation_id);
            $stmt->execute();
            $stmt->close();
            
            // Update the stock quantity in `Products` table
            $sql = "UPDATE Products SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $quantity, $product_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Handle case where product details are not found (optional logging or error handling)
            error_log("Product not found for product_id: $product_id, variation_id: $variation_id");
        }
    } else {
        // Optionally log invalid cart key format
        error_log("Invalid cart key format: $cart_key");
    }
}

      // Set session variable to indicate order process has started
      $_SESSION['order_in_progress'] = true;
      
      // Redirect to payment page
      header("Location: payment.php?order_id=" . $order_id . "&tracking_id=" . $tracking_id ."&".$total_price ."okfine");
      exit();
}
?>


<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../assets/css/checkout.css">
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    
  </head>
<body>

<h2>Checkout</h2>
<div class="row">
  <div class="col-75">
    <div class="container">
    <form method="post" action="">
      
        <div class="row">
          <div class="col-50">
            <h3>Billing Address</h3>
            <label for="fname"><i class="fa fa-user"></i> Full Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required><br><br>
            <label for="email"><i class="fa fa-envelope"></i> Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly required><br><br>
            <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
            <select name="address" id="address" onchange="handleCustomAddress(this)">
                <?php foreach ($addresses as $address): ?>
                    <option value="<?php echo htmlspecialchars($address['address_id']); ?>">
                        <?php echo htmlspecialchars($address['address_line_1']) . ' ' . htmlspecialchars($address['address_line_2']) . ' (' . htmlspecialchars($address['postal_code']).')'; ?>
                    </option>
                <?php endforeach; ?>
                <option value="custom">Custom Address</option>
            </select>

            <!-- Hidden input for custom address -->
            <input type="text" name="custom_address" id="custom_address" style="display: none;" placeholder="Enter your custom address" />

            <script>
            function handleCustomAddress(select) {
                const customAddressInput = document.getElementById('custom_address');
                if (select.value === 'custom') {
                    customAddressInput.style.display = 'block';
                } else {
                    customAddressInput.style.display = 'none';
                }
            }
            </script>

            <label for="city"><i class="fa fa-institution"></i> Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required><br><br>   
          </div>
        </div>
        <label>
          <input type="checkbox" checked="checked" name="sameadr"> Shipping address same as billing
        </label>
    <input type="submit" name="submit_order" value="Continue to checkout" class="btn">

    </div>
  </div>

  <div class="col-25">
    <div class="container">
      <h4>Cart <span class="price" style="color:black"><i class="fa fa-shopping-cart"></i></span></h4>

      <p>
    <?php foreach ($_SESSION['cart'] as $cart_key => $quantity): ?>
        <?php 
            // Ensure $cart_key is formatted correctly
            if (strpos($cart_key, '_') !== false) {
                list($product_id, $variation_id) = explode('_', $cart_key);
                
                // Fetch product details from the database
                $product = getProductDetails($product_id, $variation_id);
                
                // Check if product details are valid
                if ($product) {
                    $product_name = htmlspecialchars($product['brand'] . ' ' . $product['model'] .' with ' . $product['variation_value']);
                    // Ensure price and price_modifier are valid numbers before calculations
                    $product_price = (($product['price'] ?? 0) + ($product['price_modifier'] ?? 0)) * $quantity;
                    echo '<p>' . $product_name . '<span class="price">₹' . number_format($product_price, 2) . '</span></p>';
                }
            }
            // If the cart_key is not valid, do nothing and continue to the next iteration
        ?>
    <?php endforeach; ?>
</p>

      <hr>
      <p>Total <span class="price" style="color:black"><b><?php echo '₹' . number_format($total_price, 2); ?></b></span></p>
    </div>
  </div>
</div>

<input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
</form>
</body>
</html>