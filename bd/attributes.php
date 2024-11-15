<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}


// Fetch sliders from the database
$sql_slid = "SELECT * FROM attributes";
$result_slider = mysqli_query($conn, $sql_slid);


// Initialize variables
$attribute_name = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $attribute_name = test_input($_POST["attribute_name"]);

    // Insert attribute into Attributes table
    $sql = "INSERT INTO Attributes (attribute_name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $attribute_name);

    if (mysqli_stmt_execute($stmt)) {
        $message = "Attribute added successfully.";
    } else {
        $error = "Error adding attribute: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
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
    <title>Add Attribute</title>
</head>
<body>
    <h2>Add Attribute</h2>
    <?php if (isset($message)) : ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>
    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="attribute_name">Attribute Name:</label>
        <input type="text" name="attribute_name" id="attribute_name" required><br><br>
        <button type="submit">Add Attribute</button>
    </form>
<br>
<h2>Attribute List</h2>
    <?php while ($row = mysqli_fetch_assoc($result_slider)) : ?>
                <div class="attri-list" ><?php echo $row['attribute_name']; ?></div>
                <?php endwhile; ?>
    <style>
        .attri-list {
  padding: 5px 10px;
  margin: 5px 0;
  border: 1px solid #ddd;
  border-radius: 3px;
  display: inline-block;
  font-weight: bold;
  background-color: #f5f5f5; /* Light background */
  transition: all 0.2s ease-in-out; /* Smooth hover effect */
}

.attri-list:hover {
  background-color: #e0e4e8; /* Light hover background */
  cursor: pointer; /* Change cursor to indicate interactivity */
}

    </style>
</body>
</html>
