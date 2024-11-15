<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
// Function to insert values into the services table
function insert_service($conn, $service_name, $service_category_id, $img, $price) {
    $sql = "SELECT * FROM services WHERE service_name = ? AND service_category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $service_name, $service_category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $message = "<p>Record already exists.</p>";
        return $message;

    } else {
        $sql = "INSERT INTO services (service_name, service_category_id, img, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisi", $service_name, $service_category_id, $img, $price);
        $stmt->execute();
        $stmt->close();
    }
}


// Function to display all services
function display_services($conn) {
    $sql = "SELECT * FROM services";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = $_POST["service_name"];
    $service_category_id = $_POST["service_category_id"];
    $img = $_POST["img"];
    $price = $_POST["price"];

    insert_service($conn, $service_name, $service_category_id, $img, $price);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Service</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen">
    <?php include 'hsidebar.php'; ?>
    <div class="container mx-auto p-4 mt-4 flex-1">
        <h1 class="text-3xl font-bold mb-4">Insert Service</h1>
        <p><?php if (isset($message)) { echo $message; } ?></p>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="mb-4">
                <label for="service_name" class="block text-gray-700 text-sm font-bold mb-2">Service Name:</label>
                <input type="text" id="service_name" name="service_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="service_category_id" class="block text-gray-700 text-sm font-bold mb-2">Service Category ID:</label>
                <select id="service_category_id" name="service_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php
                    $sql = "SELECT * FROM service_categories";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($category = $result->fetch_assoc()) {
                    ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="img" class="block text-gray-700 text-sm font-bold mb-2">Image:</label>
                <input type="text" id="img" name="img" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Price:</label>
                <input type="number" id="price" name="price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Insert Service</button>
        </form>
        <h2 class="text-2xl font-bold mb-4">All Services</h2>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class=" px-4 py-2">Service Name</th>
                    <th class="px-4 py-2">Service Category ID</th>
                    <th class="px-4 py-2">Image</th>
                    <th class="px-4 py-2">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $services = display_services($conn);
                while ($service = $services->fetch_assoc()) {
                ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo $service['service_name']; ?></td>
                    <td class="border px-4 py-2"><?php echo $service['service_category_id']; ?></td>
                    <td class="border px-4 py-2"><img class="rounded-2" src="<?php echo $service['img']; ?>" alt="" srcset="<?php echo $service['img']; ?>"></td>
                    <td class="border px-4 py-2"><?php echo $service['price']; ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>