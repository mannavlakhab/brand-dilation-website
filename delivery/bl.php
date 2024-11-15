<?php
// place_order.php
$conn = mysqli_connect("localhost", "root", "", "shop");

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $customer_name = $_POST['customer_name'];
  $email = $_POST['email'];
  $phone_number = $_POST['phone_number'];
  $shipping_address = $_POST['shipping_address'];
  $total_price = $_POST['total_price'];
  $payment_method = $_POST['payment_method'];

  // Insert customer into database
  $customer_query = "INSERT INTO customers (name, email, phone_number) VALUES ('$customer_name', '$email', '$phone_number')";
  mysqli_query($conn, $customer_query);
  $customer_id = mysqli_insert_id($conn);

  // Insert order into database
  $order_query = "INSERT INTO orders (customer_id, order_status, shipping_address, total_price, payment_method) VALUES ('$customer_id', 'pending', '$shipping_address', '$total_price', '$payment_method')";
  mysqli_query($conn, $order_query);
  $order_id = mysqli_insert_id($conn);

  // Generate barcode
  $barcode_number = generate_barcode($order_id);
  $barcode_query = "INSERT INTO barcodes (barcode_number, order_id) VALUES ('$barcode_number', '$order_id')";
  mysqli_query($conn, $barcode_query);

  echo "Order placed successfully!";
}

function generate_barcode($order_id) {
  // Generate a unique barcode number
  $barcode_number = rand(100000, 999999);
  return $barcode_number;
}

// update_order_status.php
$conn = mysqli_connect("localhost", "root", "", "shop");

if (!$conn) {
  die("Connection failed: ". mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $order_id = $_POST['order_id'];
  $order_status = $_POST['order_status'];

  // Update order status in database
  $update_query = "UPDATE orders SET order_status = '$order_status' WHERE id = '$order_id'";
  mysqli_query($conn, $update_query);

  echo "Order status updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- Order form -->
<form action="place_order.php" method="post">
  <label for="customer_name">Customer Name:</label>
  <input type="text" id="customer_name" name="customer_name"><br><br>
  <label for="email">Email:</label>
  <input type="email" id="email" name="email"><br><br>
  <label for="phone_number">Phone Number:</label>
  <input type="tel" id="phone_number" name="phone_number"><br><br>
  <label for="shipping_address">Shipping Address:</label>
  <textarea id="shipping_address" name="shipping_address"></textarea><br><br>
  <label for="total_price">Total Price:</label>
  <input type="number" id="total_price" name="total_price"><br><br>
  <label for="payment_method">Payment Method:</label>
  <select id="payment_method" name="payment_method">
    <option value="credit_card">Credit Card</option>
    <option value="paypal">PayPal</option>
  </select><br><br>
  <input type="submit" value="Place Order">
</form>

<!-- Order tracking page -->
<table>
  <tr>
    <th>Order ID</th>
    <th>Order Status</th>
    <th>Shipping Address</th>
    <th>Total Price</th>
    <th>Tracking ID</th>
  </tr>
  <?php
  // Query to retrieve orders
  $orders = mysqli_query($conn, "SELECT * FROM orders");
  while ($order = mysqli_fetch_assoc($orders)) {
    echo "<tr>";
    echo "<td>" . $order['order_id'] . "</td>";
    echo "<td>" . $order['order_status'] . "</td>";
    echo "<td>" . $order['shipping_address'] . "</td>";
    echo "<td>" . $order['total_price'] . "</td>";
    echo "<td>" . $order['tracking_id'] . "</td>";
    echo "</tr>";
  }
  ?>
</table>
</body>
</html>