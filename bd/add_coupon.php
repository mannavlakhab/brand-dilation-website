<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve input data
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $discount = mysqli_real_escape_string($conn, $_POST['discount']);
    $expiry = mysqli_real_escape_string($conn, $_POST['expiry']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);

    // Insert the coupon into the database
    $sql = "INSERT INTO CouponCodes (Code, DiscountPercentage, ExpiryDate, IsActive, CouponType)
            VALUES ('$code', '$discount', '$expiry', TRUE, '$type')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>New coupon added successfully</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

 
} else {
    echo "Invalid request method.";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Coupon Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'hsidebar.php'; ?>
        <div class="flex-1 p-4 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
            <h2 class="text-2xl font-bold mb-4">Add Coupon Code</h2>
            <form action="add_coupon.php" method="post">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="code">
                            Coupon Code
                        </label>
                        <input type="text" id="code" name="code" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="discount">
                            Discount Percentage
                        </label>
                        <input type="number" id="discount" name="discount" step="0.01" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="expiry">
                            Expiry Date
                        </label>
                        <input type="date" id="expiry" name="expiry" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
                            Coupon Type
                        </label>
                        <select id="type" name="type" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                            <option value="PercentageDiscount">Percentage Discount</option>
                            <option value="FreeGift">Free Gift</option>
                            <!-- Add more types as needed -->
                        </select>
                    </div>
                </div>
                <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Add Coupon</button>
            </form>
        </div>
    </div>
</body>
</html>
