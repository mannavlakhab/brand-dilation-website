<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch products from the database
$sql_products = "SELECT * FROM products"; // Adjust the table name as needed
$result_products = mysqli_query($conn, $sql_products);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <style>
        .product-dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            width: calc(33.333% - 40px);
            box-sizing: border-box;
            background-color: #f9f9f9;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-card img {
            max-width: 100%;
            border-radius: 5px;
        }
        .product-card h3 {
            margin: 10px 0;
        }
        .product-card p {
            color: #555;
        }
        .product-card button {
            margin-top: 10px;
            padding: 10px 15px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .product-card button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
  <center> <h1>Product Dashboard</h1></center> 
    <div class="product-dashboard">
        <?php while ($row = mysqli_fetch_assoc($result_products)) : ?>
            <div class="product-card">
                <img src="../<?php echo $row['image_main']; ?>" alt="<?php echo $row['model']; ?>">
                <h3>N: <?php echo $row['brand']; ?><?php echo $row['model']; ?></h3>
                <p>D: <?php echo $row['description']; ?></p>
                <p>S D: <?php echo $row['short_des']; ?></p>
                <p>Stock: <?php echo $row['stock_quantity']; ?></p>
                <p><strong>Price:</strong> $<?php echo $row['price']; ?></p>
                <button onclick="window.location.href='edit_product.php?id=<?php echo $row['product_id']; ?>'">Edit</button>
                <button onclick="window.location.href='delete_product.php?id=<?php echo $row['product_id']; ?>'">Delete</button>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
