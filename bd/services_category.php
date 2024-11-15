<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Function to insert values into the service_categories table
function insert_service_category($conn, $category_name, $img) {
    $sql = "SELECT * FROM service_categories WHERE category_name = ? AND img = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $category_name, $img);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "Record already exists.";
    } else {
        $sql = "INSERT INTO service_categories (category_name, img) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $category_name, $img);
        $stmt->execute();
        $stmt->close();
    }
}

// Function to display all categories
function display_categories($conn) {
    $sql = "SELECT * FROM service_categories";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST["category_name"];
    $img = $_POST["img"];

    insert_service_category($conn, $category_name, $img);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Service Category</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen">
    <?php include 'hsidebar.php'; ?>
    <div class="container mx-auto p-4 mt-4 flex-1">
        <h1 class="text-3xl font-bold mb-4">Insert Service Category</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="mb-4">
                <label for="category_name" class="block text-gray-700 text-sm font-bold mb-2">Category Name:</label>
                <input type="text" id="category_name" name="category_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="img" class="block text-gray-700 text-sm font-bold mb-2">Image:</label>
                <input type="text" id="img" name="img" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Insert Service Category</button>
        </form>
        <h2 class="text-2xl font-bold mb-4">All Categories</h2>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">Category Name</th>
                    <th class="px-4 py-2">Image</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $categories = display_categories($conn);
                while ($category = $categories->fetch_assoc()) {
                ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $category['category_name']; ?></td>
                    <td class="border px-4 py-2"><img class="w-10 h-10"  src="<?php echo $category['img']; ?>" alt="" srcset=""></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html> 