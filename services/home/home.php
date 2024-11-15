<?php
include 'db_connection.php';

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


$service_category_id = $_GET['service_category_id'];

// Fetch services under the selected category
$query = "SELECT * FROM services WHERE service_category_id = $service_category_id";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body>
    <header class="bg-gray-100 py-4">
        <h1 class="text-3xl font-bold text-center">Services</h1>
    </header>
    <main class="max-w-7xl mx-auto p-4">
        <div class="space-y-4">
        <?php 
            while($row = mysqli_fetch_assoc($result)) { 
            ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex items-center justify-between p-4">
                    <div class="w-1/2 min-w-4 max-w-8">
                        <h3 class="text-lg font-bold text-gray-800"><?= $row['service_name'] ?></h3>
                        <p class="text-gray-600 text-sm">₹<?= $row['price'] ?></p>
                    </div>
                    <div class="min-w-4 max-w-2">
                    <img src="<?= $row['img'] ?>" alt="<?= $row['service_name'] ?>" style="width:212px; height:auto">
                        <button onclick="location.href = 'add_to_cart.php?service_id=<?= $row['id'] ?>';" class="bg-blue-500 w-full hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2 inline-block">Add to Cart</button>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </main>
</body>
</html>