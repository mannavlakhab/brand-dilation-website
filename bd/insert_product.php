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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect the form data
    $product_id = $_POST['product_id'];
    $brand = $_POST['brand'];
    $manufacturer = $_POST['manufacturer'];
    $model_name = $_POST['model_name'];
    $series = $_POST['series'];
    $item_weight = $_POST['item_weight'];
    $form_factor = $_POST['form_factor'];
    $item_height = $_POST['item_height'];
    $item_width = $_POST['item_width'];
    $screen_resolution = $_POST['screen_resolution'];
    $screen_size = $_POST['screen_size'];
    $product_dimensions = $_POST['product_dimensions'];
    $batteries = $_POST['batteries'];
    $colour = $_POST['colour'];
    $item_model_number = $_POST['item_model_number'];
    $processor_brand = $_POST['processor_brand'];
    $processor_type = $_POST['processor_type'];
    $processor_speed = $_POST['processor_speed'];
    $processor_count = $_POST['processor_count'];
    $ram_size = $_POST['ram_size'];
    $memory_technology = $_POST['memory_technology'];
    $computer_memory_type = $_POST['computer_memory_type'];
    $maximum_memory_supported = $_POST['maximum_memory_supported'];
    $memory_clock_speed = $_POST['memory_clock_speed'];
    $hard_disk_size = $_POST['hard_disk_size'];
    $hard_disk_description = $_POST['hard_disk_description'];
    $hard_drive_interface = $_POST['hard_drive_interface'];
    $hard_disk_rotational_speed = $_POST['hard_disk_rotational_speed'];
    $audio_details = $_POST['audio_details'];
    $graphics_coprocessor = $_POST['graphics_coprocessor'];
    $graphics_chipset_brand = $_POST['graphics_chipset_brand'];
    $graphics_card_description = $_POST['graphics_card_description'];
    $graphics_ram_type = $_POST['graphics_ram_type'];
    $graphics_card_ram_size = $_POST['graphics_card_ram_size'];
    $graphics_card_interface = $_POST['graphics_card_interface'];
    $connectivity_type = $_POST['connectivity_type'];
    $wireless_type = $_POST['wireless_type'];
    $number_of_usb_3_0_ports = $_POST['number_of_usb_3_0_ports'];
    $voltage = $_POST['voltage'];
    $optical_drive_type = $_POST['optical_drive_type'];
    $power_source = $_POST['power_source'];
    $hardware_platform = $_POST['hardware_platform'];
    $operating_system = $_POST['operating_system'];
    $avg_battery_standby_life = $_POST['avg_battery_standby_life'];
    $avg_battery_life = $_POST['avg_battery_life'];
    $are_batteries_included = $_POST['are_batteries_included'];
    $lithium_battery_energy_content = $_POST['lithium_battery_energy_content'];
    $lithium_battery_weight = $_POST['lithium_battery_weight'];
    $number_of_lithium_ion_cells = $_POST['number_of_lithium_ion_cells'];
    $number_of_lithium_metal_cells = $_POST['number_of_lithium_metal_cells'];

     // Prepare and bind
$stmt = $conn->prepare("
    INSERT INTO product_info (
        product_id, Brand, Manufacturer, Model_Name, Series, Item_Weight, Form_Factor, Item_Height, 
        Item_Width, Screen_Resolution, Screen_Size, inbox, Product_Dimensions, Batteries, Colour, 
        Item_Model_Number, Processor_Brand, Processor_Type, Processor_Speed, Processor_Count, RAM_Size, 
        Memory_Technology, Computer_Memory_Type, Maximum_Memory_Supported, Memory_Clock_Speed, 
        Hard_Disk_Size, Hard_Disk_Description, Hard_Drive_Interface, Hard_Disk_Rotational_Speed, 
        Audio_Details, Graphics_Coprocessor, Graphics_Chipset_Brand, Graphics_Card_Description, 
        Graphics_RAM_Type, Graphics_Card_RAM_Size, Graphics_Card_Interface, Connectivity_Type, 
        Wireless_Type, Number_of_USB_3_0_Ports, Voltage, Optical_Drive_Type, Power_Source, 
        Hardware_Platform, Operating_System, Avg_Battery_Standby_Life, Avg_Battery_Life, 
        Are_Batteries_Included, Lithium_Battery_Energy_Content, Lithium_Battery_Weight, 
        Number_of_Lithium_Ion_Cells, Number_of_Lithium_Metal_Cells, Included_Components, 
        Country_of_Origin, Special_Feature
    ) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");


// Adjust the bind_param based on the actual data types of your columns
$stmt->bind_param("issssdsddsdsssssssdidssdddssisssssdsssidssssddiddiisss", 
    $product_id, $brand, $manufacturer, $model_name, $series, $item_weight, $form_factor, $item_height, 
    $item_width, $screen_resolution, $screen_size, $inbox, $product_dimensions, $batteries, $colour, 
    $item_model_number, $processor_brand, $processor_type, $processor_speed, $processor_count, $ram_size, 
    $memory_technology, $computer_memory_type, $maximum_memory_supported, $memory_clock_speed, 
    $hard_disk_size, $hard_disk_description, $hard_drive_interface, $hard_disk_rotational_speed, 
    $audio_details, $graphics_coprocessor, $graphics_chipset_brand, $graphics_card_description, 
    $graphics_ram_type, $graphics_card_ram_size, $graphics_card_interface, $connectivity_type, 
    $wireless_type, $number_of_usb_3_0_ports, $voltage, $optical_drive_type, $power_source, 
    $hardware_platform, $operating_system, $avg_battery_standby_life, $avg_battery_life, 
    $are_batteries_included, $lithium_battery_energy_content, $lithium_battery_weight, 
    $number_of_lithium_ion_cells, $number_of_lithium_metal_cells, $included_components, 
    $country_of_origin, $special_feature
);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Fetch existing product_ids from products table
$sql = "SELECT product_id FROM products";
$result = $conn->query($sql);

// Fetch existing product_ids from product_info table
$sql_info = "SELECT product_id FROM product_info";
$result_info = $conn->query($sql_info);

// Create an array to store existing product IDs from product_info
$existing_product_ids = array();
while ($row_info = $result_info->fetch_assoc()) {
    $existing_product_ids[] = $row_info['product_id'];
}

// Prepare options for the select element, excluding existing product IDs
$options = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if the product ID exists in product_info
        if (!in_array($row['product_id'], $existing_product_ids)) {
            $options .= "<option value='" . htmlspecialchars($row['product_id']) . "'>" . htmlspecialchars($row['product_id']) . "</option>";
        }
    }
} else {
    $options = "<option value=''>No products available</option>";
}



// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Information Entry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-md mx-auto p-4 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
        <h1 class="text-2xl font-bold mb-4">Enter Product Information</h1>
        <form method="POST" action="" class="flex flex-wrap -mx-3 mb-6">
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="product_id">
                    Select Product ID:
                </label>
                <select name="product_id" id="product_id" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    <?php echo $options; ?>
                </select>
            </div>

            <!-- Brand -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="brand">
                    Brand:
                </label>
                <input type="text" id="brand" name="brand" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Manufacturer -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="manufacturer">
                    Manufacturer:
                </label>
                <input type="text" id="manufacturer" name="manufacturer" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Model Name -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="model_name">
                    Model Name:
                </label>
                <input type="text" id="model_name" name="model_name" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Series -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="series">
                    Series:
                </label>
                <input type="text" id="series" name="series" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Item Weight -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="item_weight">
                    Item Weight (kg):
                </label>
                <input type="number" step="0.01" id="item_weight" name="item_weight" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Form Factor -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="form_factor">
                    Form Factor:
                </label>
                <input type="text" id="form_factor" name="form_factor" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div >

            <!-- Item Height -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="item_height">
                    Item Height (cm):
                </label>
                <input type="number" step="0.01" id="item_height" name="item_height" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Item Width -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="item_width">
                    Item Width (cm):
                </label>
                <input type="number" step="0.01" id="item_width" name="item_width" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Screen Resolution -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="screen_resolution">
                    Screen Resolution:
                </label>
                <input type="text" id="screen_resolution" name="screen_resolution" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Screen Size -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="screen_size">
                    Screen Size (inches):
                </label>
                <input type="number" step="0.1" id="screen_size" name="screen_size" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Inbox -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="inbox">
                    Inbox:
                </label>
                <input type="text" id="inbox" name="inbox" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Product Dimensions -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="product_dimensions">
                    Product Dimensions (L x W x H):
                </label>
                <input type="text" id="product_dimensions" name="product_dimensions" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Batteries -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="batteries">
                    Batteries:
                </label>
                <input type="text" id="batteries" name="batteries" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Colour -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="colour">
                    Colour:
                </label>
                <input type="text" id="colour" name="colour" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Item Model Number -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="item_model_number">
                    Item Model Number:
                </label>
                <input type="text" id="item_model_number" name="item_model_number" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Processor Brand -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="processor_brand">
                    Processor Brand:
                </label>
                <input type="text" id="processor_brand" name="processor_brand" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Processor Type -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="processor_type">
                    Processor Type:
                </label>
                <input type="text" id="processor_type" name="processor_type" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Processor Speed -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="processor_speed">
                    Processor Speed (GHz):
                </label>
                <input type="number" step="0.1" id="processor_speed" name="processor_speed" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Processor Count -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="processor_count">
                    Processor Count:
                </label>
                <input type="number" id="processor_count" name="processor_count" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- RAM Size -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="ram_size">
                    RAM Size (GB):
                </label>
                <input type="number" id="ram_size" name="ram_size" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Memory Technology -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="memory_technology">
                    Memory Technology:
                </label>
                <input type="text" id="memory_technology" name="memory_technology" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Computer Memory Type -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="computer_memory_type">
                    Computer Memory Type:
                </label>
                <input type="text" id="computer_memory_type" name="computer_memory_type" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Maximum Memory Supported -->
            <div class="w-full md :w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="maximum_memory_supported">
                    Maximum Memory Supported (GB):
                </label>
                <input type="number" id="maximum_memory_supported" name="maximum_memory_supported" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Memory Clock Speed -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="memory_clock_speed">
                    Memory Clock Speed (MHz):
                </label>
                <input type="number" id="memory_clock_speed" name="memory_clock_speed" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Hard Disk Size -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="hard_disk_size">
                    Hard Disk Size (GB):
                </label>
                <input type="number" id="hard_disk_size" name="hard_disk_size" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Hard Disk Description -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="hard_disk_description">
                    Hard Disk Description:
                </label>
                <input type="text" id="hard_disk_description" name="hard_disk_description" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Hard Drive Interface -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="hard_drive_interface">
                    Hard Drive Interface:
                </label>
                <input type="text" id="hard_drive_interface" name="hard_drive_interface" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Hard Disk Rotational Speed -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="hard_disk_rotational_speed">
                    Hard Disk Rotational Speed (RPM):
                </label>
                <input type="number" id="hard_disk_rotational_speed" name="hard_disk_rotational_speed" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Audio Details -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="audio_details">
                    Audio Details:
                </label>
                <input type="text" id="audio_details" name="audio_details" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Graphics Coprocessor -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="graphics_coprocessor">
                    Graphics Coprocessor:
                </label>
                <input type="text" id="graphics_coprocessor" name="graphics_coprocessor" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Graphics Chipset Brand -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="graphics_chipset_brand">
                    Graphics Chipset Brand:
                </label>
                <input type="text" id="graphics_chipset_brand" name="graphics_chipset_brand" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Graphics Card Description -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="graphics_card_description">
                    Graphics Card Description:
                </label>
                <input type="text" id="graphics_card_description" name="graphics_card_description" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Graphics RAM Type -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="graphics_ram_type">
                    Graphics RAM Type:
                </label>
                <input type="text" id="graphics_ram_type" name="graphics_ram_type" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Graphics Card RAM Size -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="graphics_card_ram_size">
                    Graphics Card RAM Size (GB):
                </label>
                <input type="number" id="graphics_card_ram_size" name="graphics_card_ram_size" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Graphics Card Interface -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="graphics_card_interface">
                    Graphics Card Interface:
                </label>
                <input type="text" id="graphics_card_interface" name="graphics_card_interface" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Connectivity Type -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="connectivity_type">
                    Connectivity Type:
                </label>
                <input type="text" id="connectivity_type" name="connectivity_type" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Wireless Type -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="wireless_type">
                    Wireless Type:
                </label>
                <input type="text" id="wireless_type" name="wireless_type" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Number of USB 3.0 Ports -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="number_of_usb_3_0_ports">
                    Number of USB 3.0 Ports:
                </label>
                <input type="number" id="number_of_usb_3_0 _ports" name="number_of_usb_3_0_ports" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Voltage -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="voltage">
                    Voltage (V):
                </label>
                <input type="number" id="voltage" name="voltage" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Optical Drive Type -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="optical_drive_type">
                    Optical Drive Type:
                </label>
                <input type="text" id="optical_drive_type" name="optical_drive_type" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Power Source -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="power_source">
                    Power Source:
                </label>
                <input type="text" id="power_source" name="power_source" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Hardware Platform -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="hardware_platform">
                    Hardware Platform:
                </label>
                <input type="text" id="hardware_platform" name="hardware_platform" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Operating System -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="operating_system">
                    Operating System:
                </label>
                <input type="text" id="operating_system" name="operating_system" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Average Battery Standby Life -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="avg_battery_standby_life">
                    Average Battery Standby Life (hours):
                </label>
                <input type="number" step="0.1" id="avg_battery_standby_life" name="avg_battery_standby_life" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Average Battery Life -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="avg_battery_life">
                    Average Battery Life (hours):
                </label>
                <input type="number" step="0.1" id="avg_battery_life" name="avg_battery_life" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Are Batteries Included -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="are_batteries_included">
                    Are Batteries Included:
                </label>
                <select id="are_batteries_included" name="are_batteries_included" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>

            <!-- Lithium Battery Energy Content -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="lithium_battery_energy_content">
                    Lithium Battery Energy Content (Watt-Hours):
                </label>
                <input type="number" step="0.1" id="lithium_battery_energy_content" name="lithium_battery_energy_content" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Lithium Battery Weight -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="lithium_battery_weight">
                    Lithium Battery Weight (grams):
                </label>
                <input type="number" step="0.1" id="lithium_battery_weight" name="lithium_battery_weight" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Number of Lithium-Ion Cells -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="number_of_lithium_ion_cells">
                    Number of Lithium-Ion Cells:
                </label>
                <input type="number" id="number_of_lithium_ion_cells" name="number_of_lithium_ion_cells" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Number of Lithium-Metal Cells -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="number_of_lithium_metal_cells">
                    Number of Lithium-Metal Cells:
                </label>
                <input type="number" id="number_of_lithium_metal_cells" name="number_of_lithium_metal_cells" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Included Components -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="included_components">
                    Included Components:
                </label>
                <input type="text" id="included_components" name="included_components" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Country of Origin -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="country_of_origin">
                    Country of Origin:
                </label>
                <input type="text" id="country_of_origin" name="country_of_origin" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Special Feature -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="special_feature">
                    Special Feature:
 </label>
                <input type="text" id="special_feature" name="special_feature" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
            </div>

            <!-- Submit Button -->
            <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                <input type="submit" value="Submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            </div>
        </form>
    </div>
</body>
</html>