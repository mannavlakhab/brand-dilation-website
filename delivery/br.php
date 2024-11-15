<?php
$barcode_number = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];

    // Generate a unique barcode number
    $barcode_number = uniqid('bd-');
    $barcode_number = substr($barcode_number, 0, 11); 

    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "shop";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the order_id already exists
    $check_sql = "SELECT * FROM barcodes WHERE order_id = $order_id";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        $message = "Error: A barcode for this order ID already exists.";
    } else {
        // Insert barcode data into the database
        $sql = "INSERT INTO barcodes (barcode_number, order_id) VALUES ('$barcode_number', $order_id)";
        if ($conn->query($sql) === TRUE) {
            $message = "New barcode generated successfully";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Barcode</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <h1>Generate Barcode</h1>
    <form action="br.php" method="POST">
        <label for="order_id">Order ID:</label>
        <input type="number" id="order_id" name="order_id" required><br><br>
        <input type="submit" value="Generate Barcode">
    </form>

    <?php if (!empty($barcode_number)) { ?>
        <div id="barcode-box">
            <p><?php echo $message; ?></p>
            <svg id="barcode"></svg>
        </div>
        <script>
            JsBarcode("#barcode", "<?php echo $barcode_number; ?>", {
                format: "CODE128",
                lineColor: "#000",
                width: 2,
                height: 50,
                displayValue: true
            });
        </script>
    <?php } else { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>

    <a href="http://localhost/delivery/showbr.php?selected_status=delivered,shipped,pending,canceled" target="_blank" rel="noopener noreferrer">delivered</a>
    <a href="http://localhost/delivery/showbr.php?selected_status=shipped" target="_blank" rel="noopener noreferrer">Shipped</a>
    <a href="http://localhost/delivery/showbr.php?selected_status=pending" target="_blank" rel="noopener noreferrer">pending</a>
    <a href="http://localhost/delivery/showbr.php?selected_status=canceled" target="_blank" rel="noopener noreferrer">canceled</a>
    <a href="http://localhost/delivery/showbr.php?selected_status=processing" target="_blank" rel="noopener noreferrer">processing</a>
</body>
</html>
