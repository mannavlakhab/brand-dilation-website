<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Check if category ID is provided in the URL
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Delete category from the database
    $sql = "DELETE FROM Categories WHERE category_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $category_id);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Category deleted successfully!";
    } else {
        $error = "Error deleting category: " . mysqli_error($conn);
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

// Fetch categories data from the database after deletion
$sql = "SELECT * FROM Categories";
$categ = mysqli_query($conn, $sql);

// Initialize an empty array to store categories
$categories = [];

if (mysqli_num_rows($categ) > 0) {
    // Fetch categories and store them in the $categories array
    while ($row = mysqli_fetch_assoc($categ)) {
        $categories[] = $row;
    }
}

// Close the database connection

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Categories</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'hsidebar.php'; ?>
        <div class="flex-1 p-4 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
            <h2 class="text-2xl font-bold mb-4">View Categories</h2>
            <?php if (isset($message)) : ?>
                <p class="text-green-500"><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if (isset($error)) : ?>
                <p class="text-red-500"><?php echo $error; ?></p>
            <?php endif; ?>
            <a href="add_category.php" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded mb-4">Add Category</a>
            <table class="mt-4 w-full">
                <thead>
                    <tr>
                        <th class="text-left">ID</th>
                        <th class="text-left">Name</th>
                        <th class="text-left">Image</th>
                        <th class="text-left">Description</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category) : ?>
                        <tr>
                            <td><?php echo $category['category_id']; ?></td>
                            <td><?php echo $category['name']; ?></td>
                            <td><img src="../<?php echo $category['img']; ?>" alt="<?php echo $category['name']; ?>" class="w-24 h-24 object-cover"></td>
                            <td><?php echo $category['description']; ?></td>
                            <td>
                                <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a> | 
                                <a href="?id=<?php echo $category['category_id']; ?>" onclick="return confirm('Are you sure you want to delete this category?')" class="text-red-500 hover:text-red-700">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>