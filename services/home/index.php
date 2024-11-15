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


// Fetch services under the selected category
$query = "SELECT * FROM  service_categories";
$ser = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f8f8;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .service-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .service-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .service-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .service-card h3 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 1rem;
            color: #333;
        }

        .service-card p {
            color: #666;
            line-height: 1.6;
        }

        .service-card .button {
            background-color: #007bff;
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            margin-top : 1rem;
        }

        .service-card .button:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <header class="bg-white py-6 shadow-md">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-center text-gray-800">Services</h1>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <img src="<?= $row['img'] ?>" alt="<?= $row['category_name'] ?>" class="w-full rounded-lg mb-4">
                <h3 class="text-xl font-bold text-center text-gray-800"><?= $row['category_name'] ?></h3>
                <div onclick="location.href = 'home.php?service_category_id=<?= $row['id'] ?>';" class="bg-blue-500 hover:bg-blue-700 text-center text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Explore</div>
            </div>
            <?php } ?>
        </div>
    </main>
</body>
</html>