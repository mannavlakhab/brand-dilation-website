<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch products from the database
$sql_products = "SELECT * FROM couponcodes"; // Adjust the table name as needed
$result_products = mysqli_query($conn, $sql_products);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Coupons</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'hsidebar.php'; ?>
        <div class="flex-1 p-4 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
            <h2 class="text-2xl font-bold mb-4">Coupon Dashboard</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php while ($row = mysqli_fetch_assoc($result_products)) : ?>
        <div class="bg-white rounded shadow-md p-4">
            <h3 class="text-lg font-bold mb-2"><?php echo $row['CouponID']; ?> | <?php echo $row['Code']; ?></h3>
            <p class="text-gray-600"><?php echo $row['CouponType']; ?></p>
            <p class="text-gray-600">Stock: <?php echo $row['ExpiryDate']; ?></p>
            <p class="text-gray-600"><strong>Price:</strong> ₹<?php echo $row['DiscountPercentage']; ?></p>
            <p class="text-gray-600"><strong>Price:</strong><?php echo $row['IsActive']; ?></p>
        </div>
    <?php endwhile; ?>
</div>
        </div>
    </div>
</body>
</html>
