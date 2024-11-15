<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

$selected_status = isset($_GET['selected_status']) ? $_GET['selected_status'] : '';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT o.order_id, o.order_status, o.shipping_address, o.total_price, o.payment_status, o.payment_method, b.barcode_number
FROM orders o
INNER JOIN barcodes b ON o.order_id = b.order_id
WHERE o.order_status = '" . $conn->real_escape_string($selected_status) . "'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
echo "<h2>Orders with Barcodes</h2>";
echo "<table>
<tr>
<th>Order ID</th>
<th>order_status</th>
<th>shipping_address</th>
<th>total_price</th>
<th></th>
<th>Barcode Number</th>
</tr>";
// output data of each row
while($row = $result->fetch_assoc()) {
echo "<tr>
<td>" . $row["order_id"] . "</td>
<td>" . $row["order_status"] . "</td>
<td>" . $row["shipping_address"] . "</td>
<td>" . $row["total_price"] . "</td>
<td>" . $row["order_id"] . "</td>
<td></td>
<td>" . $row["barcode_number"] . "</td>
</tr>";
}
echo "</table>";
} else {
echo "No orders found with barcodes";
}

$conn->close();
?>