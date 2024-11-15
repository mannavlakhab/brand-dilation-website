<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch form details
$sql = "SELECT * FROM RentForm";
$rent = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Rent Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'hsidebar.php'; ?>
        <div class="flex-1 p-4 pt-6 pb-8 mb-4 bg-white rounded shadow-md border border-gray-200">
            <h2 class="text-2xl font-bold mb-4">Welcome Admin</h2>
            <table class="w-full border rounded-lg divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Name</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Company Name</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Phone Number</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Email</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">System Type</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Processor</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">RAM</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">SSD</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Quantity</th>
                        <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php
                    if ($rent->num_rows > 0) {
                        while($row = $rent->fetch_assoc()) {
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
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
