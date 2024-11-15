<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch variations from the ProductVariations
$sql_slid = "SELECT * FROM ProductVariations";
$result_slider = mysqli_query($conn, $sql_slid);

// Initialize variables
$product_id = $variation_name = $variation_value = $price_modifier = $stock_quantity = "";
$error = "";

// Fetch products from the database for dropdown selection
$sql_products = "SELECT product_id, model FROM Products";
$result_products = mysqli_query($conn, $sql_products);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $product_id = test_input($_POST["product_id"]);
    $variation_name = test_input($_POST["variation_name"]);
    $variation_value = test_input($_POST["variation_value"]);
    $price_modifier = test_input($_POST["price_modifier"]);
    $stock_quantity = test_input($_POST["stock_quantity"]);

    // Insert variation into ProductVariations table
    $sql_variation = "INSERT INTO ProductVariations (product_id, variation_name, variation_value, price_modifier, stock_quantity) VALUES (?, ?, ?, ?, ?)";
    $stmt_variation = mysqli_prepare($conn, $sql_variation);
    mysqli_stmt_bind_param($stmt_variation, "issdi", $product_id, $variation_name, $variation_value, $price_modifier, $stock_quantity);

    if (mysqli_stmt_execute($stmt_variation)) {
        $message = "Variation added successfully.";
    } else {
        $error = "Error adding variation: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt_variation);
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Variation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'hsidebar.php'; ?>
        <div class="flex-1 p-4 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
            <h2 class="text-2xl font-bold mb-4">Add Variation</h2>
            <?php if (isset($message)) : ?>
                <p class="text-green-500"><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if (!empty($error)) : ?>
                <p class="text-red-500"><?php echo $error; ?></p>
            <?php endif; ?>
            <form method="post">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="product_id">
                            Product:
                        </label>
                        <select name="product_id" id="product_id" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                            <option value="">Select Product</option>
                            <?php while ($row = mysqli_fetch_assoc($result_products)) : ?>
                                <option value="<?php echo $row['product_id']; ?>"><?php echo $row['model']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="variation_name">
                            Variation Name:
                        </label>
                        <input type="text" value="Default" name="variation_name" id="variation_name" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="variation_value">
                            Variation Value:
                        </label>
                        <input type="text" value="I3 12GEN - 8GB RAM - 256GB SSD " name="variation_value" id="variation_value" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="price_modifier">
                            Price Modifier:
                        </label>
                        <input type="number" value="2500" name="price_modifier" id="price_modifier" step="0.01" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="stock_quantity">
                            Stock Quantity:
                        </label>
                        <input type="number" value="3" name="stock_quantity" id="stock_quantity" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                </div>
                <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Add Variation</button>
            </form>
          
</body>
</html>