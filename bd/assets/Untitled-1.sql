127.0.0.1/shop/product_info/		http://localhost/phpmyadmin/index.php?route=/table/sql&db=shop&table=product_info
Your SQL query has been executed successfully.

DESCRIBE product_info;



id	int(11)	NO	PRI	NULL	auto_increment	
product_id	int(11)	NO	UNI	NULL	

Brand	varchar(255)	YES		NULL		
Manufacturer	varchar(255)	YES		NULL		
Model_Name	varchar(255)	YES		NULL		
Series	varchar(255)	YES		NULL		
Screen_Resolution	varchar(255)	YES		NULL		

Screen_Size	decimal(10,2)	YES		NULL				
Batteries	varchar(255)	YES		NULL		
Colour	varchar(255)	YES		NULL				
Processor_Brand	varchar(255)	YES		NULL		
Processor_Type	varchar(255)	YES		NULL	
RAM_Size	decimal(10,2)	YES		NULL		
Memory_Technology	varchar(255)	YES		NULL		
Maximum_Memory_Supported	decimal(10,2)	YES		NULL	
Hard_Disk_Size	decimal(10,2)	YES		NULL		
Hard_Disk_Description	varchar(255)	YES		NULL		
Audio_Details	varchar(255)	YES		NULL		
Graphics_Coprocessor	varchar(255)	YES		NULL		
Graphics_Chipset_Brand	varchar(255)	YES		NULL		
Graphics_Card_Description	varchar(255)	YES		NULL		
Graphics_RAM_Type	varchar(255)	YES		NULL		
Graphics_Card_RAM_Size	decimal(10,2)	YES		NULL		
Number_of_USB_3_0_Ports	int(11)	YES		NULL		
Power_Source	varchar(255)	YES		NULL		
Operating_System	varchar(255)	YES		NULL		
Country_of_Origin	varchar(255)	YES		NULL		
Special_Feature	varchar(255)	YES		NULL		







127.0.0.1/shop/product_info/		http://localhost/phpmyadmin/index.php?route=/table/sql&db=shop&table=product_info
Your SQL query has been executed successfully.

DESCRIBE product_info;



id	int(11)	NO	PRI	NULL	auto_increment	
product_id	int(11)	NO	UNI	NULL		
Brand	varchar(255)	YES		NULL		
Manufacturer	varchar(255)	YES		NULL		
Model_Name	varchar(255)	YES		NULL		
Series	varchar(255)	YES		NULL		
Item_Weight	decimal(10,2)	YES		NULL		
Form_Factor	varchar(255)	YES		NULL		
Item_Height	decimal(10,2)	YES		NULL		
Item_Width	decimal(10,2)	YES		NULL		
Screen_Resolution	varchar(255)	YES		NULL		
Screen_Size	decimal(10,2)	YES		NULL		
Resolution	varchar(255)	YES		NULL		
Product_Dimensions	varchar(255)	YES		NULL		
Batteries	varchar(255)	YES		NULL		
Colour	varchar(255)	YES		NULL		
Item_Model_Number	varchar(255)	YES		NULL		
Processor_Brand	varchar(255)	YES		NULL		
Processor_Type	varchar(255)	YES		NULL		
Processor_Speed	decimal(10,2)	YES		NULL		
Processor_Count	int(11)	YES		NULL		
RAM_Size	decimal(10,2)	YES		NULL		
Memory_Technology	varchar(255)	YES		NULL		
Computer_Memory_Type	varchar(255)	YES		NULL		
Maximum_Memory_Supported	decimal(10,2)	YES		NULL		
Memory_Clock_Speed	decimal(10,2)	YES		NULL		
Hard_Disk_Size	decimal(10,2)	YES		NULL		
Hard_Disk_Description	varchar(255)	YES		NULL		
Hard_Drive_Interface	varchar(255)	YES		NULL		
Hard_Disk_Rotational_Speed	int(11)	YES		NULL		
Audio_Details	varchar(255)	YES		NULL		
Graphics_Coprocessor	varchar(255)	YES		NULL		
Graphics_Chipset_Brand	varchar(255)	YES		NULL		
Graphics_Card_Description	varchar(255)	YES		NULL		
Graphics_RAM_Type	varchar(255)	YES		NULL		
Graphics_Card_RAM_Size	decimal(10,2)	YES		NULL		
Graphics_Card_Interface	varchar(255)	YES		NULL		
Connectivity_Type	varchar(255)	YES		NULL		
Wireless_Type	varchar(255)	YES		NULL		
Number_of_USB_3_0_Ports	int(11)	YES		NULL		
Voltage	decimal(10,2)	YES		NULL		
Optical_Drive_Type	varchar(255)	YES		NULL		
Power_Source	varchar(255)	YES		NULL		
Hardware_Platform	varchar(255)	YES		NULL		
Operating_System	varchar(255)	YES		NULL		
Avg_Battery_Standby_Life	decimal(10,2)	YES		NULL		
Avg_Battery_Life	decimal(10,2)	YES		NULL		
Are_Batteries_Included	tinyint(1)	YES		NULL		
Lithium_Battery_Energy_Content	decimal(10,2)	YES		NULL		
Lithium_Battery_Weight	decimal(10,2)	YES		NULL		
Number_of_Lithium_Ion_Cells	int(11)	YES		NULL		
Number_of_Lithium_Metal_Cells	int(11)	YES		NULL		
Included_Components	varchar(255)	YES		NULL		
Country_of_Origin	varchar(255)	YES		NULL		
Special_Feature	varchar(255)	YES		NULL		

  // Prepare and bind
$stmt = $conn->prepare("
    INSERT INTO product_info (
        product_id, Brand, Manufacturer, Model_Name, Series, Item_Weight, Form_Factor, Item_Height, 
        Item_Width, Screen_Resolution, Screen_Size, Resolution, Product_Dimensions, Batteries, Colour, 
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
    $item_width, $screen_resolution, $screen_size, $resolution, $product_dimensions, $batteries, $colour, 
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