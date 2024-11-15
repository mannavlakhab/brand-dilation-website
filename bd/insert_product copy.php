


<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure each key is set
    if (isset($_POST['product_id']) && isset($_POST['brand']) && isset($_POST['manufacturer']) &&
        isset($_POST['model_name']) && isset($_POST['series']) && isset($_POST['screen_resolution']) &&
        isset($_POST['screen_size']) && isset($_POST['batteries']) && isset($_POST['colour']) &&
        isset($_POST['processor_brand']) && isset($_POST['processor_type']) && isset($_POST['ram_size']) &&
        isset($_POST['memory_technology']) && isset($_POST['maximum_memory_supported']) &&
        isset($_POST['hard_disk_size']) && isset($_POST['hard_disk_description']) &&
        isset($_POST['audio_details']) && isset($_POST['graphics_coprocessor']) &&
        isset($_POST['graphics_chipset_brand']) && isset($_POST['graphics_card_description']) &&
        isset($_POST['graphics_ram_type']) && isset($_POST['graphics_card_ram_size']) &&
        isset($_POST['number_of_usb_3_0_ports']) && isset($_POST['power_source']) &&
        isset($_POST['operating_system']) && isset($_POST['country_of_origin']) &&
        isset($_POST['special_feature'])) {

        // Prepare and bind
        $stmt = $conn->prepare("
            INSERT INTO product_info (
                product_id, Brand, Manufacturer, Model_Name, Series, Screen_Resolution, Screen_Size, Batteries, Colour, 
                Processor_Brand, Processor_Type, RAM_Size, Memory_Technology, Maximum_Memory_Supported, Hard_Disk_Size, 
                Hard_Disk_Description, Audio_Details, Graphics_Coprocessor, Graphics_Chipset_Brand, Graphics_Card_Description, 
                Graphics_RAM_Type, Graphics_Card_RAM_Size, Number_of_USB_3_0_Ports, Power_Source, Operating_System, 
                Country_of_Origin, Special_Feature
            ) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind parameters (adjust types based on your database schema)
        $stmt->bind_param("isssssdssssdsddssssssdissss", 
            $_POST['product_id'],
            $_POST['brand'],
            $_POST['manufacturer'],
            $_POST['model_name'],
            $_POST['series'], 
            $_POST['screen_resolution'],
            $_POST['screen_size'], 
            $_POST['batteries'], 
            $_POST['colour'], 
            $_POST['processor_brand'], 
            $_POST['processor_type'], 
            $_POST['ram_size'], 
            $_POST['memory_technology'], 
            $_POST['maximum_memory_supported'], 
            $_POST['hard_disk_size'], 
            $_POST['hard_disk_description'], 
            $_POST['audio_details'], 
            $_POST['graphics_coprocessor'], 
            $_POST['graphics_chipset_brand'], 
            $_POST['graphics_card_description'], 
            $_POST['graphics_ram_type'], 
            $_POST['graphics_card_ram_size'], 
            $_POST['number_of_usb_3_0_ports'], 
            $_POST['power_source'], 
            $_POST['operating_system'], 
            $_POST['country_of_origin'], 
            $_POST['special_feature']
        );

        if ($stmt->execute()) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Missing form data.";
    }
}

// Fetch existing product_ids
$sql = "SELECT product_id FROM products";
$result = $conn->query($sql);

// Prepare options for the select element
$options = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='" . htmlspecialchars($row['product_id']) . "'>" . htmlspecialchars($row['product_id']) . "</option>";
    }
} else {
    $options = "<option value=''>No products available</option>";
}

$conn->close();
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Information Form</title>
</head>

<body>
    <h1>Product Information Form</h1>
    <form action="insert_product.php" method="post">
        <label for="product_id">Product ID:</label>
        <label for="product_id">Product ID:</label>
    <select id="product_id" name="product_id" required>
        <?php echo $options; ?>
    </select><br><br>

        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand"><br><br>

        <label for="manufacturer">Manufacturer:</label>
        <input type="text" id="manufacturer" name="manufacturer"><br><br>

        <label for="model_name">Model Name:</label>
        <input type="text" id="model_name" name="model_name"><br><br>

        <label for="series">Series:</label>
        <input type="text" id="series" name="series"><br><br>

        <label for="screen_resolution">Screen Resolution:</label>
        <input type="text" id="screen_resolution" name="screen_resolution"><br><br>

        <label for="screen_size">Screen Size:</label>
        <input type="number" step="0.01" id="screen_size" name="screen_size"><br><br>

        <label for="batteries">Batteries:</label>
        <input type="text" id="batteries" name="batteries"><br><br>

        <label for="colour">Colour:</label>
        <input type="text" id="colour" name="colour"><br><br>

        <label for="processor_brand">Processor Brand:</label>
        <input type="text" id="processor_brand" name="processor_brand"><br><br>

        <label for="processor_type">Processor Type:</label>
        <input type="text" id="processor_type" name="processor_type"><br><br>

        <label for="ram_size">RAM Size:</label>
        <input type="number" step="0.01" id="ram_size" name="ram_size"><br><br>

        <label for="memory_technology">Memory Technology:</label>
        <input type="text" id="memory_technology" name="memory_technology"><br><br>

        <label for="maximum_memory_supported">Maximum Memory Supported:</label>
        <input type="number" step="0.01" id="maximum_memory_supported" name="maximum_memory_supported"><br><br>

        <label for="hard_disk_size">Hard Disk Size:</label>
        <input type="number" step="0.01" id="hard_disk_size" name="hard_disk_size"><br><br>

        <label for="hard_disk_description">Hard Disk Description:</label>
        <input type="text" id="hard_disk_description" name="hard_disk_description"><br><br>

        <label for="audio_details">Audio Details:</label>
        <input type="text" id="audio_details" name="audio_details"><br><br>

        <label for="graphics_coprocessor">Graphics Coprocessor:</label>
        <input type="text" id="graphics_coprocessor" name="graphics_coprocessor"><br><br>

        <label for="graphics_chipset_brand">Graphics Chipset Brand:</label>
        <input type="text" id="graphics_chipset_brand" name="graphics_chipset_brand"><br><br>

        <label for="graphics_card_description">Graphics Card Description:</label>
        <input type="text" id="graphics_card_description" name="graphics_card_description"><br><br>

        <label for="graphics_ram_type">Graphics RAM Type:</label>
        <input type="text" id="graphics_ram_type" name="graphics_ram_type"><br><br>

        <label for="graphics_card_ram_size">Graphics Card RAM Size:</label>
        <input type="number" step="0.01" id="graphics_card_ram_size" name="graphics_card_ram_size"><br><br>

        <label for="number_of_usb_3_0_ports">Number of USB 3.0 Ports:</label>
        <input type="number" id="number_of_usb_3_0_ports" name="number_of_usb_3_0_ports"><br><br>

        <label for="power_source">Power Source:</label>
        <input type="text" id="power_source" name="power_source"><br><br>

        <label for="operating_system">Operating System:</label>
        <input type="text" id="operating_system" name="operating_system"><br><br>

        <label for="country_of_origin">Country of Origin:</label>
        <input type="text" id="country_of_origin" name="country_of_origin"><br><br>

        <label for="special_feature">Special Feature:</label>
        <input type="text" id="special_feature" name="special_feature"><br><br>

        <input type="submit" value="Submit">
    </form>
</body>

</html>