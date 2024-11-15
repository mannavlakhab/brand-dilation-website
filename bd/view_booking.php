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

// Fetch booking details along with customer info
$booking_query = "SELECT b.*, c.first_name, c.last_name, c.email, c.phone_number, s.service_name, s.img, oi.price
FROM bookings b
JOIN order_items oi ON b.id = oi.booking_id  -- Assuming there's a booking_id in order_items
JOIN customers c ON b.customer_id = c.customer_id
JOIN services s ON oi.service_id = s.id";

$categ = mysqli_query($conn, $booking_query);

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
    <title>View Bookings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'hsidebar.php'; ?>
        <div class="flex-1 p-4 min-h-max h-screen pt-6 pb-8 mb-4 bg-white rounded shadow-md">
            <h2 class="text-2xl font-bold mb-4">View Bookings</h2>
            <?php if (isset($message)) : ?>
                <p class="text-green-500"><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if (isset($error)) : ?>
                <p class="text-red-500"><?php echo $error; ?></p>
            <?php endif; ?>
            <!-- <a href="add_category.php" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded mb-4">Add Bo</a> -->
            <table class="w-full">
            <thead>
    <tr>
        <th class="text-left w-1/2">Created At</th>
        <th class="text-left w-1/1">ID</th>
        <th class="text-left w-1/6">Service Img</th>
        <th class="text-left w-1/6">Service Name</th>
        <th class="text-left w-1/6">Price</th>
        <th class="text-left w-1/6">Status</th>
        <th class="text-left w-1/6">Total Price</th>
        <th class="text-left w-1/6">Customer Name</th>
        <th class="text-left w-1/6">Email</th>
        <th class="text-left w-1/6">Phone Number</th>
        <th class="text-left w-1/2">Tracking ID</th>
        <th class="text-left w-1/6">Actions</th>
    </tr>
</thead>

                <tbody>
                    <?php foreach ($categories as $category) : ?>
                        <tr>
                            <td><?php echo $category['created_at']; ?></td>
                            <td class="mr-3"><?php echo $category['id']; ?></td>
                            <td><img src="<?php echo $category['img']; ?>"></td>
                            <td><?php echo $category['service_name']; ?></td>
                            <td><?php echo $category['price']; ?></td>
                            <td><?php echo $category['status']; ?></td>
                            <td><?php echo $category['total_price']; ?></td>
                            <td><?php echo $category['first_name'].' '.$category['first_name']; ?></td>
                            <td><?php echo $category['email']; ?></td>
                            <td><?php echo $category['phone_number']; ?></td>
                            <td><?php echo $category['tracking_id']; ?></td>
                            <td>
                                <a href="?id=<?php echo $category['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a> | 
                                <a href="?id=<?php echo $category['id']; ?>" onclick="return confirm('Are you sure you want to delete this category?')" class="text-red-500 hover:text-red-700">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>