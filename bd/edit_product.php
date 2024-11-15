<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$message = $error = "";

// Check if product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch the product details from the database
    $sql_product = "SELECT * FROM Products WHERE product_id = $product_id";
    $result_product = mysqli_query($conn, $sql_product);

    if ($result_product && mysqli_num_rows($result_product) > 0) {
        $product = mysqli_fetch_assoc($result_product);
    } else {
        $error = "Product not found!";
    }
} else {
    $error = "Product ID not provided!";
}

// Check if the form is submitted for updating the product details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $brand = $_POST["brand"];
    $model = $_POST["model"];
    $description = $_POST["description"];
    $short_des = $_POST["short_des"];
    $stock_quantity = $_POST["stock_quantity"];
    $price = $_POST["price"];
    $refurbished = isset($_POST["refurbished"]) ? 1 : 0;
    $digital_product = isset($_POST["digital_product"]) ? 1 : 0;
    $keywords = $_POST["keywords"];

    // Debug: Print POST data
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';

    // Update the product details in the database
    $sql_update = "UPDATE Products SET brand = ?, model = ?, description = ?, short_des = ?, stock_quantity = ?, price = ?, refurbished = ?, digital_product = ?, keywords = ? WHERE product_id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ssssddissi", $brand, $model, $description, $short_des, $stock_quantity, $price, $refurbished, $digital_product, $keywords, $product_id);

    if (mysqli_stmt_execute($stmt_update)) {
        $message = "Product updated successfully!";
        // Redirect to view_product.php
        header("Location: view_product.php?id=$product_id");
        exit(); // Ensure script execution stops after redirection
    } else {
        $error = "Error updating product: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
</head>
<body>
    <h2>Edit Product</h2>
    <?php if (!empty($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (!empty($message)) : ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if (isset($product)) : ?>
        <form method="post">
        <div id="charCount"></div>
            <label for="brand">Brand:</label>
            <input type="text" name="brand" id="brand" value="<?php echo $product['brand']; ?>" required><br><br>
            <label for="model">Model:</label>
            <input type="text" name="model" id="model" value="<?php echo $product['model']; ?>" required><br><br>
            <label for="description">Description:</label><br>
            <textarea name="description" id="description" rows="5" cols="30" required><?php echo $product['description']; ?></textarea><br><br>
            <label for="short_des">short_description:</label><div id="charCountDes"></div><br>
            <textarea name="short_des" maxlength="45" id="short_des" rows="2" cols="30" required><?php echo $product['short_des']; ?></textarea><br><br>
            <label for="stock_quantity">Stock Quantity:</label>
            <input type="number" name="stock_quantity" id="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required><br><br>
            <label for="price">Price:</label>
            <input type="number" name="price" id="price" step="0.01" value="<?php echo $product['price']; ?>" required><br><br>
            <label for="refurbished">Refurbished:</label>
            <input type="checkbox" name="refurbished" id="refurbished" <?php if ($product['refurbished']) echo 'checked'; ?>><br><br>
            <label for="digital_product">Digital Product:</label>
            <input type="checkbox" name="digital_product" id="digital_product" <?php if ($product['digital_product']) echo 'checked'; ?>><br><br>
            <label for="keywords">Keywords:</label>
<input type="text" name="keywords" id="keywords" placeholder="e.g., laptop, office, lightweight" value="<?php echo htmlspecialchars($product['keywords']); ?>"><br><br>
            <button type="submit">Update Product</button>
        </form>
    <?php endif; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const brandInput = document.getElementById('brand');
        const modelInput = document.getElementById('model');
        const charCountDisplay = document.getElementById('charCount');
        const maxLength = 28;

        function updateCharCount() {
            const brandLength = brandInput.value.length;
            const modelLength = modelInput.value.length;
            const totalLength = brandLength + modelLength;
            const remaining = maxLength - totalLength;

            if (remaining < 0) {
                const excess = Math.abs(remaining);
                if (brandLength > modelLength) {
                    brandInput.value = brandInput.value.slice(0, brandLength - excess);
                } else {
                    modelInput.value = modelInput.value.slice(0, modelLength - excess);
                }
            }

            charCountDisplay.textContent = `Remaining characters: ${remaining >= 0 ? remaining : 0}`;
        }

        brandInput.addEventListener('input', updateCharCount);
        modelInput.addEventListener('input', updateCharCount);

        // Initial count
        updateCharCount();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const shortDesTextarea = document.getElementById('short_des');
        const charCountDesDisplay = document.getElementById('charCountDes');
        const maxLengthDes = 45;

        function updateCharCountDes() {
            const currentLength = shortDesTextarea.value.length;
            const remaining = maxLengthDes - currentLength;

            if (currentLength > maxLengthDes) {
                shortDesTextarea.value = shortDesTextarea.value.slice(0, maxLengthDes);
            }

            charCountDesDisplay.textContent = `Remaining characters: ${remaining >= 0 ? remaining : 0}`;
        }

        shortDesTextarea.addEventListener('input', updateCharCountDes);

        // Initial count
        updateCharCountDes();
    });
    </script>
</body>
</html>
