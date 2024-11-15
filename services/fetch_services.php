<?php
session_start();
include '../db_connect.php'; // Include your database connection
$category_id = $_POST['category_id'];

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


// SQL query to fetch services
$sql = "SELECT * FROM services WHERE service_category_id = '$category_id'";
$services = $conn->query($sql);

// Display services
if ($services->num_rows > 0) {
    while($row = $services->fetch_assoc()) {
        ?>
        <div class="bg-white rounded-lg shadow-md p-4">
            <img src="<?php echo $row['img']; ?>" alt="<?php echo $row['service_name']; ?>" class="w-full rounded-lg">
            <h2 class="text-xl font-bold mt-2"><?php echo $row['service_name']; ?></h2>
            <p>Price: <?php echo $row['price']; ?></p>
        </div>
        <?php
    }
} else {
    echo "No services found.";
}
?>