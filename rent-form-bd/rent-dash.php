<?php
session_start();


// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch form details
$sql = "SELECT * FROM RentForm";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Rent Dashboard</title>
    <link rel="stylesheet" href="dashboard_styles.css">

</head>
<body>
    <h2>Welcome Admin</h2>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Company Name</th>
            <th>Phone Number</th>
            <th>Email</th>
            <th>System Type</th>
            <th>Processor</th>
            <th>RAM</th>
            <th>SSD</th>
            <th>Quantity</th>
            <th>Duration</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['name']."</td>";
                echo "<td>".$row['companyName']."</td>";
                echo "<td>".$row['phoneNumber']."</td>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".$row['systemType']."</td>";
                echo "<td>".$row['processor']."</td>";
                echo "<td>".$row['RAM']."</td>";
                echo "<td>".$row['SSD']."</td>";
                echo "<td>".$row['quantity']."</td>";
                echo "<td>".$row['duration']."</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>No records found</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
