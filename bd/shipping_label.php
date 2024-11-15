<?php
session_start();
require_once '../db_connect.php';
include 'config.php';
require_once '../phpqrcode/qrlib.php'; // Include the QR Code library

// Include the necessary barcode and QR code libraries
require '../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch order ID from URL
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // SQL query to fetch order and customer information
    $sql = "SELECT SQL_CALC_FOUND_ROWS o.order_id, o.customer_id, o.order_status, o.shipping_address, o.total_price, o.shipping_cost, 
                   o.payment_method, o.payment_details, o.payment_status, o.tracking_id, o.order_date, 
                   c.first_name, c.last_name, c.email, c.phone_number, c.address,
                   p.product_id, p.brand, p.model, p.price, oi.product_quantity, oi.variation_id, pv.variation_value ,
                   pv.variation_name ,
                   pv.price_modifier ,
    a.address_id,
    a.address_line_1,
    a.address_line_2,
    a.city,
    a.state,
    a.postal_code,
    a.country
            FROM Orders o
            JOIN Customers c ON o.customer_id = c.customer_id
            JOIN
    addresses a ON c.address = a.address_id
            LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
            LEFT JOIN Products p ON oi.product_id = p.product_id
            LEFT JOIN ProductVariations pv ON oi.variation_id = pv.variation_id
            WHERE o.order_id = $order_id";

    // Execute the query
    $result = $conn->query($sql);
// Check if the order was found
if ($result->num_rows > 0) {
    // Fetch the result
    $row = $result->fetch_assoc();
    
    // Display the shipping label
    echo '<div class="max-w-2xl mx-auto p-6 border border-gray-300 bg-gray-50 rounded-lg shadow-lg">';
    echo '<h1 class="text-3xl font-bold text-center mb-6">Shipping Invoice</h1>';
    
    // Fixed grid layout for customer address and return address
    echo '<div class="grid grid-cols-2 gap-4 mb-4">'; // Fixed to 2 columns for desktop only
    
    // Part 1: Customer Address and Return Address
    echo '<div class="customer-address p-4 border border-gray-200 bg-white rounded-lg col-span-1">';
    echo '<h4 class="text-xl font-semibold mb-2">Customer Address</h4>';
    echo '<p class="text-lg"><strong>' . $row['first_name'] . ' ' . $row['last_name'] . '</strong></p>';
    echo '<p>' . $row['address'] . ' ' . $row['address_line_1'] . ' ' . $row['address_line_2'] . ' ' . $row['city'] . ' ' . $row['state'] . ' ' . $row['postal_code'] . ' ' . $row['country'] . '</p>';
    echo '<p>' . $row['email'] . '</p>';
    echo '<p>' . $row['phone_number'] . '</p>';
    echo '</div>';
  
    echo '<div class="return-address p-4 border border-gray-200 bg-white rounded-lg col-span-1">';
    echo '<h4 class="text-xl font-semibold mb-2">If undelivered, return to:</h4>';
    echo '<p class="text-lg"><strong>Brand Dilation</strong></p>';
    echo '<p>Indias</p>';
    echo '</div>';
    
    echo '</div>'; // Close grid

/// COD Info and Barcode Section
echo '<div class="cod-info p-4 border border-gray-200 bg-white rounded-lg mb-4">'; // Main container
echo '<div class="grid grid-cols-2 gap-1">'; // Create a grid with 2 rows

// Part 1: COD Information
echo '<div class="cod-info-details">'; // Optional wrapper for styling
echo '<p class="text-lg"><strong>COD:</strong> ' . $row['total_price'] . '</p>';
echo '<p class="text-lg"><strong>Order ID:</strong> ' . $row['order_id'] . '</p>';
echo '<p class="text-lg"><strong>Tracking ID:</strong> ' . $row['tracking_id'] . '</p>';
echo '</div>'; // Close cod-info-details


    // Generate Barcode
$generator = new BarcodeGeneratorPNG();

// Generate a barcode with a larger size for better readability
$barcode = base64_encode($generator->getBarcode($row['tracking_id'], $generator::TYPE_CODE_128, 3, 50)); // Adjusted width (3) and height (50)

// Part 2: Barcode and QR Code
echo '<div class="barcode-qr flex justify-between items-center">'; // Flex container for side-by-side display
echo '<div class="barcode text-center">'; // Center the barcode text and image
$qr_code_file = 'uploadsqr/' . md5($row['tracking_id']) . '.png';

// Generate QR code if it doesn't already exist
if (!file_exists($qr_code_file)) {
    QRcode::png($row['tracking_id'], $qr_code_file);
}
// Display the barcode number above the barcode image
// echo '<p class="text-lg"><strong>Barcode Number:</strong> ' . htmlspecialchars($row['tracking_id']) . '</p>'; // Safe output of barcode number
echo '<p class="text-lg"><strong>Barcode:</strong></p>';
echo '<img src="data:image/png;base64,' . $barcode . '" alt="Barcode" class="w-full max-w-[300px] h-auto">'; // Responsive width with a max width
echo '</div>';
// QR Code
echo '<div class="qr-code text-center">';
echo '<p class="text-lg"><strong>QR Code:</strong></p>';
echo '<img src="'.$qr_code_file.'" alt="QR Code" class="w-full max-w-[300px] h-auto">';
echo '</div>';

echo '</div>'; // Close flex container
echo '</div>'; // Close grid
echo '</div>'; // Close main container

    // Product Details
    echo '<div class="product-details mb-4 p-4 border border-gray-200 bg-white rounded-lg">';
    echo '<h4 class="text-xl font-semibold mb-2">Product Details</h4>';
    echo '<table class="w-full text-left border-collapse">';
    echo '<thead>';
    echo '<tr>';
    echo '<th class="border p-2">Product Name</th>';
    echo '<th class="border p-2">Price</th>';
    echo '<th class="border p-2">Quantity</th>';
    echo '<th class="border p-2">Variation</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    echo '<tr>';
    echo '<td class="border p-2">' . $row['brand'] . ' ' . $row['model'] . '</td>';
    echo '<td class="border p-2">' . $row['price'] . '</td>';
    echo '<td class="border p-2">' . $row['product_quantity'] . '</td>';
    echo '<td class="border p-2">' . $row['variation_name'] . ' ' . $row['variation_value'] . ' ' . $row['price_modifier'] . '</td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    echo '</div>'; // Close main container
} else {
    echo "<p class='text-lg font-medium text-center'>No order found for the given Order ID.</p>";
}}
 else {
    echo "<p>Order ID not provided.</p>";
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Shipping</title>
</head>
<body>
    
</body>
</html>