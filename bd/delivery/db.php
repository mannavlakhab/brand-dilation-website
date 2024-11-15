<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$db = 'shop';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['id'])) {
    header("Location: ../login.php");
    exit();
}
// Fetch logged-in user's ID from the session
$dp = isset($_SESSION['id']) ? $_SESSION['id'] : null; // Make sure this is the correct session variable
$searchTrackingId = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : null;
$newmap = isset($_GET['map']) ? $_GET['map'] : (isset($_POST['map']) ? $_POST['map'] : null);
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : (isset($_POST['order_id']) ? $_POST['order_id'] : null);


$adminData = [];
$orders = [];

if ($dp) {
    // Initialize counts
    $stopsCount = 0;
    $locationsCount = 0;
    $parcelsCount = 0;

// Count successful deliveries
$stmtSuccessful = $conn->prepare("
    SELECT COUNT(*) 
    FROM orders 
    WHERE delivery_partner_id = ? AND order_status IN ('delivered', 'exchanged') AND DATE(delivery_date) = CURDATE()");
$stmtSuccessful->bind_param("s", $dp);
$stmtSuccessful->execute();
$stmtSuccessful->bind_result($successfulCount);
$stmtSuccessful->fetch();
$stmtSuccessful->close();

// Count problems
$stmtSuccessful = $conn->prepare("
    SELECT COUNT(*) 
    FROM orders 
    WHERE delivery_partner_id = ? AND order_status = 'failed' AND DATE(delivery_date) = CURDATE()");
$stmtSuccessful->bind_param("s", $dp);
$stmtSuccessful->execute();
$stmtSuccessful->bind_result($problemCount);
$stmtSuccessful->fetch();
$stmtSuccessful->close();


    // Count total stops
    $stmtStops = $conn->prepare("SELECT COUNT(*) FROM orders WHERE dp = ?"); // Corrected to use 'delivery_partner_id'
    $stmtStops->bind_param("s", $dp);
    $stmtStops->execute();
    $stmtStops->bind_result($stopsCount);
    $stmtStops->fetch();
    $stmtStops->close();

    // Count total locations
    $stmtLocations = $conn->prepare("
        SELECT COUNT(DISTINCT a.address_id) 
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        JOIN addresses a ON c.address = a.address_id
        WHERE o.dp = ?"); // Corrected to use 'delivery_partner_id'

    $stmtLocations->bind_param("s", $dp);
    $stmtLocations->execute();
    $stmtLocations->bind_result($locationsCount);
    $stmtLocations->fetch();
    $stmtLocations->close();

    // Count total parcels
    $stmtParcels = $conn->prepare("SELECT COUNT(*) FROM orders WHERE dp = ? AND  DATE(dp_date) = CURDATE()"); // Corrected to use 'delivery_partner_id'
    $stmtParcels->bind_param("s", $dp);
    $stmtParcels->execute();
    $stmtParcels->bind_result($parcelsCount);
    $stmtParcels->fetch();
    $stmtParcels->close();

    // Count pickup
    $stmtParcels = $conn->prepare("SELECT COUNT(*) FROM orders WHERE dp = ? AND  DATE(dp_date) = CURDATE() AND order_status='pickup and delivering'"); // Corrected to use 'delivery_partner_id'
    $stmtParcels->bind_param("s", $dp);
    $stmtParcels->execute();
    $stmtParcels->bind_result($pickup);
    $stmtParcels->fetch();
    $stmtParcels->close();
    
    // Query to get admin user details
    $stmtAdmin = $conn->prepare("
        SELECT username, profile_icon 
        FROM admin_users 
        WHERE id = ?");
    $stmtAdmin->bind_param("s", $dp);
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();
    $adminData = $resultAdmin->fetch_assoc();

    // Query to get order details for the given dp and tracking ID if provided
    if ($searchTrackingId) {
        $stmtOrders = $conn->prepare("
            SELECT o.order_id, c.first_name, c.last_name, c.phone_number, 
                   a.address_line_1, a.address_line_2, a.city, a.postal_code, a.state, a.country,
                   o.order_status, o.tracking_id, o.total_price, o.payment_method, o.payment_status, o.payment_details
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            JOIN addresses a ON c.address = a.address_id
            WHERE o.dp = ? AND o.tracking_id = ?");
        $stmtOrders->bind_param("ss", $dp, $searchTrackingId);
    } else {
        $stmtOrders = $conn->prepare("
            SELECT o.order_id, c.first_name, c.last_name, c.phone_number, 
                   a.address_line_1, a.address_line_2, a.city, a.postal_code, a.state, a.country,
                   o.order_status, o.tracking_id, o.total_price, o.payment_method, o.payment_status, o.payment_details
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            JOIN addresses a ON c.address = a.address_id
            WHERE o.dp = ?");
        $stmtOrders->bind_param("s", $dp);
    }

    $stmtOrders->execute();
    $resultOrders = $stmtOrders->get_result();
    $orders = $resultOrders->fetch_all(MYSQLI_ASSOC);
    
    $stmtAdmin->close();
    $stmtOrders->close();
}

$newdp=$dp;








$page = isset($_GET['page']) ? $_GET['page'] : 'list';
// Map page logic

$error_deli = '';
$error_rl = '';

if ($page === 'map' || $page === 'help') {
    // Get order ID from URL or form submission
    $exe = isset($_GET['exe']) ? $_GET['exe'] : null;  // or $_POST['exe'] if using POST method
    
    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : (isset($_POST['order_id']) ? $_POST['order_id'] : null);
    
    $orderDetails;
    $exe_details;
    

    // Handle form actions if POST request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $order_id) {
        $action = $_POST['action'];

        if ($action == 'cash_collected') {
            $collected_amount = isset($_POST['collected_amount']) ? floatval($_POST['collected_amount']) : 0;
            $total_price = floatval($_POST['total_price']);
            
            if ($collected_amount >= $total_price) {
                $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid', payment_details = 'COD' WHERE order_id = ?");
                $stmt->bind_param("s", $order_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        $newdp=$dp;

        if ($action == 'delivery_complete') {
            $delivery_date = date("Y-m-d H:i:s");
            $stmt = $conn->prepare("UPDATE orders SET order_status = 'Delivered', delivery_partner_id = ?, dp = NULL, delivery_date = ? WHERE order_id = ?");
            $stmt->bind_param("sss", $newdp, $delivery_date, $order_id);
            $stmt->execute();
            $stmt->close();
        }
        if ($action == 'pickup') {
            $stmt = $conn->prepare("UPDATE exchanage_requests SET exchanage_status = 'pickup' WHERE order_id = ?");
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            $stmt->close();

        }
        if ($action == 'exch') {
            // Update `orders` table
            $delivery_date = date("Y-m-d H:i:s");
            $stmt1 = $conn->prepare("UPDATE orders SET order_status = 'exchanged', delivery_partner_id = ?, dp = NULL, delivery_date = ? WHERE order_id = ?");
            $stmt1->bind_param("ssi", $newdp, $delivery_date, $order_id);
            $stmt1->execute();
            $stmt1->close();
        
            // Update `exchange_requests` table
            $stmt2 = $conn->prepare("UPDATE exchanage_requests SET status = 'complete' WHERE order_id = ?");
            $stmt2->bind_param("i", $order_id);
            $stmt2->execute();
            $stmt2->close();
        }


        $deliverd_to = $_POST['location'] ?? null;
        $return_parcel = $_POST['rl'] ?? null;
        $error_deli = '';
        $error_rl = '';
        
        // Check if action is to update the delivery location
        if ($action === 'deli_to' && $deliverd_to !== null) {
            $stmt = $conn->prepare("UPDATE orders SET deliverd_to = ? WHERE order_id = ?");
            $stmt->bind_param("si", $deliverd_to, $order_id);
        
            if ($stmt->execute()) {
                $error_deli = "Delivery location updated successfully.";
            } else {
                $error_deli = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        
        // Check if action is to update the return parcel information
        if ($action === 'deli_to' && $return_parcel !== null) {
            $stmt = $conn->prepare("UPDATE orders SET return_parcel = ?, order_status = 'failed', dp=NULL,delivery_partner_id = ? WHERE order_id = ?");
            $stmt->bind_param("ssi", $return_parcel, $newdp, $order_id);
        
            if ($stmt->execute()) {
                $error_rl = "Return parcel updated successfully.";
            } else {
                $error_rl = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        
       
    }
// Fetch order, customer, and payment details if order_id is provided

if ($exe) {
    $stmt = $conn->prepare("
        SELECT o.order_id, o.total_price, o.payment_method, o.payment_status, o.payment_details, o.tracking_id, 
               o.order_status, 
                 o.deliverd_to, 
               e.exch_track, 
               e.product_name, 
               e.exchanage_status, 
               e.product_attributes, 
               p.image_main, 
               p.product_id, 
               pm.image_path, 
               c.first_name, c.last_name, c.phone_number,
               a.address_line_1, a.address_line_2, a.city, a.postal_code, a.state, a.country
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        JOIN addresses a ON c.address = a.address_id
        JOIN exchanage_requests e ON o.order_id = e.order_id
        LEFT JOIN products p ON p.product_id = e.product_id 
        LEFT JOIN product_images pm ON p.product_id = pm.product_id 
        WHERE o.order_id = ?");
    
    $stmt->bind_param("s", $exe);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $exe_details = $result->fetch_assoc();
    } else {
        echo "Query Error: " . htmlspecialchars($stmt->error);
    }
    
    $stmt->close();
}


if ($order_id) {
    $stmt = $conn->prepare("
        SELECT o.order_id, o.total_price, o.payment_method, o.payment_status, o.payment_details, o.tracking_id, 
               o.order_status, 
               o.deliverd_to, 
               c.first_name, c.last_name, c.phone_number,
               a.address_line_1, a.address_line_2, a.city, a.postal_code, a.state, a.country
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        JOIN addresses a ON c.address = a.address_id
        WHERE o.order_id = ?");

    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("s", $order_id);
    if (!$stmt->execute()) {
        die("Execution failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $result = $stmt->get_result();
    $orderDetails = $result->fetch_assoc();
    $stmt->close();
}

$conn->close();
}



if ($page === 'add-product' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Retrieve the tracking ID from the form
    $tracking_id = $_POST['tracking_id'] ?? null;


    // Fetch the order ID associated with the tracking ID
    $stmt = $conn->prepare("SELECT order_id FROM orders WHERE tracking_id = ?");
    $stmt->bind_param('s', $tracking_id);
    $stmt->execute();
    $stmt->bind_result($order_id);
    $stmt->fetch();
    $stmt->close();

    // Verify if the order exists and check specific conditions
    if ($order_id) {
        $stmt = $conn->prepare(
            "SELECT delivery_partner_id, dp, deliverd_to, return_parcel 
             FROM orders 
             WHERE order_id = ?"
        );
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $stmt->bind_result($delivery_partner_id, $dp, $deliverd_to, $return_parcel);
        $stmt->fetch();
        $stmt->close();

        // If all relevant fields are NULL, assign a container ID; otherwise, set an error message
        if (is_null($delivery_partner_id) && is_null($dp) && is_null($deliverd_to) && is_null($return_parcel)) {
            $container_id = 'AD-V-0248-248-ORG'; // Example container ID
        } else {
            $error = "Order does not meet the required conditions.";
        }
    } else {
        $error = "Invalid Tracking ID.";
    }
}

// Check if update_order form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $logged_in_id = $newdp; // Assuming session holds the logged-in user ID
    $current_date = date('Y-m-d');
    
    // Update order details with delivery information
    $stmt = $conn->prepare(
        "UPDATE orders 
         SET dp = ?, dp_date = ?, delivery_date = ?, order_status = 'shipped' 
         WHERE order_id = ?"
    );
    $stmt->bind_param('issi', $logged_in_id, $current_date, $current_date, $order_id);

    if ($stmt->execute()) {
        $success = "Order updated successfully!";
    } else {
        $error = "Failed to update the order. Please try again.";
    }
    $stmt->close();
}



// Determine which page to display based on query parameter
?>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="../../assets/img/delivery.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
    function toggleMenu() {
        const menu = document.getElementById('menu');
        menu.classList.toggle('hidden');
    }

    function togglehelp() {
        const help = document.getElementById('help');
        help.classList.toggle('hidden');
    }
    </script>
</head>

<body class="bg-gray-100">
    <div class="bg-gray-800 text-white p-4 flex items-center justify-between">
        <i class="fas fa-bars text-xl cursor-pointer" onclick="toggleMenu()"></i>
        <h1 class="text-lg font-semibold">Inventory</h1>
        <div class="flex items-center space-x-4">
            <i class="fas fa-envelope text-xl" id="notificationButton"></i>
            <i class="fas fa-question-circle text-xl cursor-pointer"
            onclick="location.href = '?page=help'"></i>
        </div>
    </div>

    <div class="hidden right-0 bg-white rounded shadow-lg z-50" id="notificationPopover">
        <h3 class="text-gray-700 font-bold p-2 border-b">Notifications</h3>
        <ul class="p-2 max-h-48 overflow-y-auto" id="notificationList"></ul>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const notificationButton = document.getElementById('notificationButton');
        const notificationPopover = document.getElementById('notificationPopover');
        const notificationList = document.getElementById('notificationList');

        // Function to fetch notifications
        async function fetchNotifications() {
            try {
                const response = await fetch('../fetch_notifications.php');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const notifications = await response.json();

                // Populate the notification list
                if (Array.isArray(notifications)) {
                    notificationList.innerHTML = notifications.map(notification => `
                <li class="p-1 border-b ${notification.is_for_current_user ? 'font-bold' : ''}">
                    ${notification.message} <span class="text-gray-400">${notification.created_at}</span>
                </li>`).join('');
                } else {
                    notificationList.innerHTML = '<li class="p-1">No notifications found.</li>';
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
                notificationList.innerHTML = `<li class="p-1">Error loading notifications.</li>`;
            }
        }


        // Toggle popover on click
        notificationButton.addEventListener('click', () => {
            if (notificationPopover.classList.contains('hidden')) {
                fetchNotifications(); // Fetch notifications only if the popover is shown
                notificationPopover.classList.remove('hidden');
            } else {
                notificationPopover.classList.add('hidden');
            }
        });

        // Close the popover when clicking outside of it
        document.addEventListener('click', (event) => {
            if (!notificationButton.contains(event.target) && !notificationPopover.contains(event
                    .target)) {
                notificationPopover.classList.add('hidden');
            }
        });
    });
    </script>


    <div class="bg-gray-800 text-white py-2 flex justify-around">
        <a href="?page=list" class="block text-sm py-1 <?php echo $page == 'list' ? 'font-bold' : ''; ?>">List</a>
        <a href="?page=map" class="block text-sm py-1 <?php echo $page == 'map' ? 'font-bold' : ''; ?>">Map</a>
        <a href="?page=summary"
            class="block text-sm py-1 <?php echo $page == 'summary' ? 'font-bold' : ''; ?>">Summary</a>
    </div>

    <div class="bg-gray-700 text-white py-2 hidden" id="menu">
        <a href="../profile.php">
            <div class="p-4 flex items-center space-x-4">
                <img alt="Profile photo" class="rounded-full w-10 h-10" height="40"
                    src="../<?php echo htmlspecialchars($adminData['profile_icon'] ?? '../uploads/profiles/defult.gif'); ?>"
                    width="40" />
                <p class="text-sm font-semibold"><?php echo htmlspecialchars($adminData['username'] ?? 'Hello Dear'); ?>
                </p>
            </div>
        </a>
        <hr class="border-gray-600" />
        <div class="p-4">
            <a class="block text-sm py-1" href="?page=add-product">Add Product</a>
        </div>
        <hr class="border-gray-600" />
        <div class="p-4">
            <a class="block text-sm py-1" href="?page=settings">Settings</a>
            <hr class="border-gray-600 my-2" />
            <a class="block text-sm py-1" href="?page=aboutus">About Us</a>
        </div>
        <hr class="border-gray-600" />
        <div class="p-4">
            <a class="block text-sm py-1" href="../logout.php">Logout</a>
        </div>
        <p class="mt-4 text-center"> Made with<span class="text-red-500"> ♥ </span>by MAN NAVLAKHA</p>
    </div>










    <div class="">
        <?php switch ($page):
            case 'list': ?>
        <div class="bg-gray-200 p-4 flex items-center">
            <i class="fas fa-sync-alt text-xl text-gray-500"></i>
            <div class="relative ml-4 w-full">
                <!-- Search form for tracking ID -->
                <form method="get" action="">
                    <input type="hidden" name="dp" value="<?php echo htmlspecialchars($dp); ?>">
                    <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
                    <div class="flex items-center border border-gray-300 rounded-md">
        <input
            class="flex-grow p-2 border border-gray-300 pl-10 rounded bg-white focus:ring-0 focus:outline-none"
            placeholder="Search by tracking ID"
            type="text"
            value="<?php echo htmlspecialchars($searchTrackingId ?? ''); ?>"
            name="tracking_id"
            required
        />
        <button class="p-3 hover:bg-gray-100 focus:outline-none" type="submit">
        <i class="fas fa-search"></i>
        </button>
    </div>
                </form>
            </div>
        </div>
        <div class="p-4">


            <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $index => $order): ?>
            <?php if ($order['order_status'] == 'pickup and delivering'):?>
            <a href="./db.php?page=map&exe=<?php echo $order['order_id']; ?>" class="block">
                <?php else:?>
                <a href="./db.php?page=map&order_id=<?php echo $order['order_id']; ?>" class="block">
                    <?php endif; ?>
                    <div class="flex items-start mt-4 mb-4">
                        <div class="flex-shrink-0">
                            <div class="bg-gray-500 text-white rounded-full w-8 h-8 flex items-center justify-center">
                                <?php echo $index + 1; ?></div>
                        </div>
                        <div class="ml-4">

                            <p class="text-green-600 font-medium">Next stop
                                <?php if ($order['order_status'] == 'pickup and delivering') {echo 'For Pickup & delivery ';}?>
                            </p>
                            <p class="text-gray-900 font-bold">
                                <?php echo htmlspecialchars($order['first_name']. ' ' . $order['last_name']); ?></p>
                            <p class="text-gray-800 font-semibold">
                                <?php echo htmlspecialchars($order['address_line_1']); ?></p>
                            <p class="text-gray-700">
                                <?php echo htmlspecialchars($order['address_line_2'] . ', ' .$order['city'] . ', ' . $order['postal_code'] . ', ' . $order['state'] . ', ' . $order['country']); ?>
                            </p>
                            <p class="text-gray-700">Id:
                                <?php echo htmlspecialchars($order['tracking_id']); ?>
                            </p>
                             <!-- Payment Status -->
        <?php if ($order['payment_status'] == 'Paid'): ?>
        <p><strong>Payment Status:</strong> Paid</p>
        <?php else: ?>
        <p><strong>Payment Due:</strong> <?php echo htmlspecialchars($order['total_price']); ?></p>
        <?php endif; ?>
                            <!-- <a href="https://www.google.com/maps/dir//<?php echo htmlspecialchars($order['address_line_1'] . '+'.$order['address_line_2'] . '+'.$order['city'] . '+' . $order['postal_code'] . '+' . $order['state'] . '+' . $order['country']); ?>"> -->
                </a>


                <button
                    onclick="location.href = 'https://www.google.com/maps/dir//<?php echo htmlspecialchars($order['address_line_1'] . '+'.$order['address_line_2'] . '+'.$order['city'] . '+' . $order['postal_code'] . '+' . $order['state'] . '+' . $order['country']); ?>'"
                    class="bg-gradient-to-r from-emerald-500 to-emerald-900 border-emerald-700 shadow-lg text-gray-50 flex justify-center gap-2 mt-2 items-center isolation-auto  before:absolute before:w-full before:transition-all before:duration-700 before:hover:w-full before:-left-full before:hover:left-0 before:rounded-full before:bg-emerald-500 hover:text-gray-50 before:-z-10 before:aspect-square before:hover:scale-150 before:hover:duration-700 relative z-10 px-2 py-1 overflow-hidden border-2 rounded-full group">
                    Go to Map
                    <svg class="w-7 h-7 justify-end text-gray-50 group-hover:rotate-90 group-hover:bg-gray-50 text-gray-50 ease-linear duration-300 rounded-full border border-gray-100 group-hover:border-none p-2 rotate-45"
                        viewBox="0 0 20 16" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M7 18C7 18.5523 7.44772 19 8 19C8.55228 19 9 18.5523 9 18H7ZM8.70711 0.292893C8.31658 -0.0976311 7.68342 -0.0976311 7.29289 0.292893L0.928932 6.65685C0.538408 7.04738 0.538408 7.68054 0.928932 8.07107C1.31946 8.46159 1.95262 8.46159 2.34315 8.07107L8 2.41421L13.6569 8.07107C14.0474 8.46159 14.6805 8.46159 15.0711 8.07107C15.4616 7.68054 15.4616 7.04738 15.0711 6.65685L8.70711 0.292893ZM9 18L9 1H7L7 18H9Z"
                            class="fill-gray-100 group-hover:fill-gray-800"></path>
                    </svg>
                </button>


                <!-- <p class="text-gray-700">Deliver <?php echo htmlspecialchars($order['order_status']); ?></p> -->
        </div>
    </div>
    <hr class="border-gray-300">
    <!-- </a> -->
    <?php endforeach; ?>
    <p class="text-gray-700">After you’re assigned more stops, you’ll see them in your itinerary</p>
    <p class="mt-4 text-center"> Made with<span class="text-red-500"> ♥ </span>by MAN NAVLAKHA</p>
    <?php else: ?>
    <p class="text-gray-700">No orders found for the given delivery
        point<?php echo $searchTrackingId ? " and tracking ID: $searchTrackingId" : ""; ?>.</p>

    <p class="mt-4 text-center"> Made with<span class="text-red-500"> ♥ </span>by MAN NAVLAKHA</p>
    </div>
    <?php endif; ?>
    <?php break; 
            case 'map': ?>















    <?php
    // Determine which details to use
    $details = isset($orderDetails) ? $orderDetails : (isset($exe_details) ? $exe_details : null);
    if ($details): ?>



<div class="bg-gray-800 text-white p-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Order Details</h1>
        <div class="flex items-center space-x-4">
            <i class="fas fa-question-circle text-xl cursor-pointer" onclick="location.href = '?page=help&order_id=<?php echo $order_id?>'"></i>
        </div>
    </div>
    <script>
    function handleCashCollection() {
        const totalPrice = parseFloat(document.getElementById('total_price').value);
        const collectedAmount = parseFloat(document.getElementById('collected_amount').value);
        const cashCollectedBtn = document.getElementById('cash_collected_btn');
        const feedbackMessage = document.getElementById('feedback_message');

        if (collectedAmount < totalPrice) {
            feedbackMessage.textContent = "Please collect the full payment.";
            cashCollectedBtn.disabled = true;
        } else if (collectedAmount > totalPrice) {
            const returnAmount = collectedAmount - totalPrice;
            feedbackMessage.textContent = "Return " + returnAmount.toFixed(2) + " rupees.";
            cashCollectedBtn.disabled = false;
        } else {
            feedbackMessage.textContent = "";
            cashCollectedBtn.disabled = false;
        }
    }
    </script>


<?php if($details['deliverd_to']):?>

    <div class="p-6  max-w-md">
<P class="text-orange-400 text-sm">You are delivering to <strong><?php echo($details['deliverd_to'])?></strong></P>
    <div class="p-4 border-t border-gray-300">
        <div class="flex items-center mb-2">
            <i class="fas fa-user-circle text-gray-600 text-5xl"></i>
            <div class="ml-2">
                <p class="text-gray-700 font-semibold"><strong><?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?></strong></p>
                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($details['address_line_1'] . ', ' . $details['address_line_2'] . ', ' . $details['city'] . ', ' . $details['postal_code'] . ', ' . $details['state'] . ', ' . $details['country']); ?></p>
                <!-- <p class="text-gray-500 text-sm"><strong><?php echo ($details['order_status'] == 'pickup and delivering' ? $details['exch_track'] : $details['tracking_id']); ?></strong></p> -->
                <!-- <p class="text-gray-500 text-sm">No recipient needed</p> -->
            </div>
        </div>
    </div>
        <p><strong>Tracking ID:</strong>
            <?php echo ($details['order_status'] == 'pickup and delivering' ? $details['exch_track'] : $details['tracking_id']); ?>
        </p>

                            <?php if ($details['order_status'] == 'failed'): ?>
                            <p><strong>This order is failed.</strong></p>
                            <?php else: ?>



    
     
        <br>
        <!-- Payment Status -->
        <?php if ($details['payment_status'] == 'Paid'): ?>
        <p><strong>Payment Status:</strong> Paid</p>
        <?php else: ?>
        <p><strong>Payment Due:</strong> <?php echo htmlspecialchars($details['total_price']); ?></p>
        <?php endif; ?>


        <!-- Order Status and Actions -->
        <?php if ($details['order_status'] == 'pickup and delivering'): ?>
        <?php if ($details['exchanage_status'] == 'pickup'): ?>
        <form method="post" action="">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($details['order_id']); ?>">
            <button type="submit" name="action" value="exch" id="exchange-btn"
                class="cursor-pointer transition-all 
                                    bg-gray-700 text-white px-6 py-2 rounded-lg
                                    border-green-400
                                    border-b-[4px] hover:brightness-110 mt-5 hover:-translate-y-[1px] hover:border-b-[6px]
                                    active:border-b-[2px] active:brightness-90 active:translate-y-[2px] hover:shadow-xl hover:shadow-green-300 shadow-green-300 active:shadow-none">
                Complate Exchange
            </button>
        </form>
        <?php else: ?>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Check Item</h2>
        <p><strong>Product Name:</strong> <?php echo htmlspecialchars($details['product_name']); ?></p>
        <p><strong>Product Image:</strong></p>
        <img src="../../<?php echo htmlspecialchars($details['image_main']); ?>" alt="Main Product Image"
            class="w-30 h-auto mb-4">

        <form method="post" action="">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($details['order_id']); ?>">
            <button type="submit" name="action" value="pickup" id="pickup_collected_btn" class="cursor-pointer transition-all bg-gray-700 text-white px-6 py-2 rounded-lg
        border-green-400 border-b-[4px] hover:brightness-110 mt-5 hover:-translate-y-[1px] 
        hover:border-b-[6px] active:border-b-[2px] active:brightness-90 active:translate-y-[2px] 
        hover:shadow-xl hover:shadow-green-300 shadow-green-300 active:shadow-none">
                Done Pickup
            </button>
        </form>
        <?php endif; ?>
        <?php elseif ($details['order_status'] == 'delivered' || $details['order_status'] == 'exchanged'): ?>
        <p class="text-green-600 mt-4">Order is already marked as complete.</p>
        <form method="get" action="db.php">
            <button type="submit" class="bg-green-500 border-t border-gray-300 text-white px-4 py-2 rounded mt-4">Go for Next Order</button>
        </form>
        <?php elseif ($details['payment_status'] == 'Paid'): ?>
        <p class="text-green-600 mt-4">Order is already paid.</p>
        <form method="post" action="">
            <button type="submit" name="action" value="delivery_complete"
                class="cursor-pointer transition-all 
                            bg-blue-700 text-white px-6 py-2 rounded-lg
                            border-green-400
                            border-b-[4px] hover:brightness-110 hover:-translate-y-[1px] hover:border-b-[6px]
                            active:border-b-[2px] active:brightness-90 active:translate-y-[2px] hover:shadow-xl hover:shadow-green-300 shadow-green-300 active:shadow-none">
                Delivery Complete
            </button>
        </form>
        <?php else: ?>
           
        <form method="post" action="">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order_id); ?>">
            <input type="hidden" id="total_price" name="total_price"
                value="<?php echo htmlspecialchars($details['total_price']); ?>">

            <label for="collected_amount" class="block text-gray-700 mt-4">Enter Collected Amount:</label>
            <input type="number" step="0.01" id="collected_amount" name="collected_amount"
                class="mt-1 mb-2 p-2 border rounded w-full" oninput="handleCashCollection()">

            <p id="feedback_message" class="text-red-500"></p>

            <button type="submit" name="action" value="cash_collected" id="cash_collected_btn" disabled
                class="cursor-pointer transition-all 
                            bg-gray-700 text-white px-6 py-2 rounded-lg
                            border-green-400
                            border-b-[4px] hover:brightness-110 mt-5 hover:-translate-y-[1px] hover:border-b-[6px]
                            active:border-b-[2px] active:brightness-90 active:translate-y-[2px] hover:shadow-xl hover:shadow-green-300 shadow-green-300 active:shadow-none">
                Cash Collected
            </button>
        </form>
        <?php endif; ?>
        <?php endif; ?>
             <?php else: ?>
                <form method="POST" action="">
    <div class="p-4 border-b border-gray-300">
        <h2 class="text-lg font-semibold">Where are you leaving the package?</h2>
        <label class="ml-2 text-red-700"><?php echo $error_deli;?></label>
    </div>
    <div class="p-4">
        <div class="flex items-center mb-4">
            <input id="option1" type="radio" name="location" value="<?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?> or a household member" class="form-radio h-5 w-5 text-gray-600" required>
            <label for="option1" class="ml-2 text-gray-700"><?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?> or a household member</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option2" type="radio" name="location" value="Receptionist or doorman" class="form-radio h-5 w-5 text-gray-600">
            <label for="option2" class="ml-2 text-gray-700">Receptionist or doorman</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option3" type="radio" name="location" value="Front door/Front porch" class="form-radio h-5 w-5 text-gray-600">
            <label for="option3" class="ml-2 text-gray-700">Front door/Front porch</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option4" type="radio" name="location" value="Rear door/Rear porch" class="form-radio h-5 w-5 text-gray-600">
            <label for="option4" class="ml-2 text-gray-700">Rear door/Rear porch</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option5" type="radio" name="location" value="In a secure mailroom" class="form-radio h-5 w-5 text-gray-600">
            <label for="option5" class="ml-2 text-gray-700">In a secure mailroom</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option6" type="radio" name="location" value="Another safe location" class="form-radio h-5 w-5 text-gray-600">
            <label for="option6" class="ml-2 text-gray-700">Another safe location</label>
        </div>
    </div>
    <div class="p-4 bg-gray-200 border-t border-gray-300">
        <div class="flex items-center mb-2">
            <i class="fas fa-user-circle text-gray-600 text-2xl"></i>
            <div class="ml-2">
                <p class="text-gray-700 font-semibold"><?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?></p>
                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($details['address_line_1'] . ', ' . $details['address_line_2'] . ', ' . $details['city'] . ', ' . $details['postal_code'] . ', ' . $details['state'] . ', ' . $details['country']); ?></p>
                <p class="text-gray-500 text-sm"><?php echo ($details['order_status'] == 'pickup and delivering' ? $details['exch_track'] : $details['tracking_id']); ?></p>
                <p class="text-gray-500 text-sm">No recipient needed</p>
            </div>
        </div>
    </div>
    <div class="">
        <button type="submit" name="action" value="deli_to" class="w-full p-4 text-center text-white bg-blue-500">Submit</button>
    </div>
</form>   

                <?php endif; ?>
        <?php else: ?>

        <p>No order details available.</p>
        <?php endif; ?>
    </div>





















    <?php break; 
           case 'summary': ?>
    <div class="p-4">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold">Work remaining</h2>
            <img src="https://placehold.co/20x20" alt="Information icon" class="w-5 h-5">
        </div>
        <div class="mb-4">
            <p class="text-sm font-semibold"><?php echo $stopsCount; ?> Stops</p>
            <p class="text-xs text-gray-500">Includes 0 grouped stops</p>
            <div class="w-full bg-gray-200 h-1">
                <div class="bg-orange-500 h-1"
                    style="width: <?php echo ($stopsCount > 0) ? ($stopsCount / 10 * 100) : 0; ?>%;"></div>
            </div>
        </div>
        <div class="mb-4">
            <p class="text-sm font-semibold"><?php echo $locationsCount; ?> locations</p>
            <div class="w-full bg-gray-200 h-1">
                <div class="bg-orange-500 h-1"
                    style="width: <?php echo ($locationsCount > 0) ? ($locationsCount / 10 * 100) : 0; ?>%;"></div>
            </div>
        </div>
        <div class="mb-4">
            <p class="text-sm font-semibold"><?php echo $parcelsCount; ?> parcels</p>
            <div class="w-full bg-gray-200 h-1">
                <div class="bg-orange-500 h-1"
                    style="width: <?php echo ($parcelsCount > 0) ? ($parcelsCount / 10 * 100) : 0; ?>%;"></div>
            </div>
        </div>
        <hr class="my-4" />
        <div class="mb-4">
            <h3 class="text-sm font-semibold">Stops</h3>
            <div class="flex justify-between text-sm text-gray-600">
                <p>To Do</p>
                <p><?php echo $stopsCount; ?></p>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <p>Successful</p>
                <p><?php echo $successfulCount; ?></p> <!-- Display the successful count -->
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <p>Problems</p>
                <p><?php echo $problemCount; ?></p> <!-- You can update this based on your logic -->
            </div>
        </div>
        <hr class="my-4" />
        <div class="mb-4">
            <h3 class="text-sm font-semibold">Parcels</h3>
            <div class="flex justify-between text-sm text-gray-600">
                <p>To deliver or drop off</p>
                <p><?php echo $parcelsCount; ?></p>
            </div>
            <div class="flex justify-between text-sm text-gray-600">
                <p>To pick up</p>
                <p><?php echo $pickup; ?></p> <!-- You can update this based on your logic -->
            </div>
        </div>
        <p class="mt-4 text-center"> Made with<span class="text-red-500"> ♥ </span>by MAN NAVLAKHA</p>
    </div>
    
    



    <?php break; 
           case 'add-product': ?>
        
<!-- HTML Form -->
<form class="bg-white p-6 shadow-md" method="POST">
    <div class="flex items-center border border-gray-300 rounded-md">
        <input
            class="flex-grow p-2 border-none focus:ring-0 focus:outline-none"
            placeholder="Enter Tracking ID"
            type="text"
            name="tracking_id"
            required
        />
        <button class="p-2 hover:bg-gray-100 focus:outline-none" type="submit">
            <svg class="h-6 w-6 text-gray-600" viewBox="0 -0.5 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- SVG Content -->
            </svg>
        </button>
    </div>
</form>

<!-- Display Order Details -->
<?php if (isset($container_id)): ?>
    <div class="p-4 space-y-2">
        <div class="bg-white p-4 rounded shadow flex items-center justify-between">
            <i class="fas fa-box text-gray-500 mr-4"></i>
            <div class="flex flex-col">
                <span class="text-gray-500">Tracking ID</span>
                <span class="font-bold"><?= htmlspecialchars($tracking_id) ?></span>
            </div>
            <i class="fas fa-check text-gray-400"></i>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700" onclick="showPopup(<?= $order_id ?>)">
                Update Order
            </button>
        </div>
    </div>
<?php elseif (isset($error)): ?>
    <div class="text-red-500"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Success Message -->
<?php if (isset($success)): ?>
    <div class="text-green-500"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Popup -->
<div id="popup" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg text-center space-y-4">
        <p class="text-gray-700">Do you want to proceed?</p>
        <form method="POST">
            <input type="hidden" id="order_id_input" name="order_id" value="">
            <button type="submit" name="update_order" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add
            </button>
            <button type="button" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400" onclick="closePopup()">
                Wait
            </button>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
    function showPopup(orderId) {
        document.getElementById('order_id_input').value = orderId;
        document.getElementById('popup').classList.remove('hidden');
    }

    function closePopup() {
        document.getElementById('popup').classList.add('hidden');
    }
</script>

    <?php break; 
           case 'help': 
           
           if ($order_id) :?>
           
           <?php
    // Determine which details to use
    $details = isset($orderDetails) ? $orderDetails : (isset($exe_details) ? $exe_details : null);
    if ($details): ?>

    <div id="chn">
        <script>

        function toggleContent(id) {
            var content = document.getElementById(id);
            var icon = content.previousElementSibling.querySelector('i');
            if (content.style.display === "none") {
                content.style.display = "block";
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                content.style.display = "none";
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
        </script>
        <div class="bg-gray-800 text-white p-4 flex items-center justify-between">
            <h1 class="text-xl font-semibold">How can i help you with "<?php echo $details['tracking_id'] ?>"?</h1>
        </div>
        <div class="p-4 ">
            <?php if ($details['order_status'] == 'failed'): ?>
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold"><?php echo $error_rl; ?></h2>
                </div>
            <div class="border-b py-2">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg text-green-500 font-semibold"><a href="?page=list">Go to next order</a></h2>
                    
                </div>
            </div>
            <?php else: ?>
            <?php endif; ?>
            
            <?php
            $ph = $details['phone_number'];
            ?>
            <div class="border-b py-2">
                <div class="flex justify-between items-center"
                    onclick='if (confirm("Are you sure you want to call?")) { window.location.href="tel:+91 <?php echo $ph?>" }'>
                    <h2 class="text-lg font-semibold">Call
                        <?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?> ,
                        customer</h2>
                    <i class="fa fa-phone"></i>
                </div>
            </div>
            <div class="border-b py-2">
                <div class="flex justify-between items-center"
                    onclick='if (confirm("Are you sure you want to call?")) { window.location.href="tel:+91 8888 88888" }'>
                    <h2 class="text-lg font-semibold">Call Support</h2>
                    <i class="fa fa-phone"></i>
                </div>
            </div>
            <div class="border-b py-2">
                <div class="flex flex-col justify-between" onclick="toggleContent('content1')">
                    <h2 class="text-lg font-semibold">Return Items</h2>
                    <p class="mt-2 text-gray-600">Remove items that are damaged or that the cutomer doesn't want.</p>
                </div>
                <div id="content1" style="display: none;">
                <form method="POST" action="">
                   
    <div class="p-4">
        <div class="flex items-center mb-4">
            <input id="option1" type="radio" name="rl" value="rejected by <?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?> or a household member " class="form-radio h-5 w-5 text-gray-600" required>
            <label for="option1" class="ml-2 text-gray-700">rejected by <?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?> or a household member </label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option2" type="radio" name="rl" value="Wrong Parcel/missing item in parcel" class="form-radio h-5 w-5 text-gray-600">
            <label for="option2" class="ml-2 text-gray-700">Wrong Parcel/missing item in parcel</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option3" type="radio" name="rl" value="damged parcel" class="form-radio h-5 w-5 text-gray-600">
            <label for="option3" class="ml-2 text-gray-700">damged parcel</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option4" type="radio" name="rl" value="Payment not ready" class="form-radio h-5 w-5 text-gray-600">
            <label for="option4" class="ml-2 text-gray-700">Payment not ready</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option5" type="radio" name="rl" value="Customer not avalible in office or house" class="form-radio h-5 w-5 text-gray-600">
            <label for="option5" class="ml-2 text-gray-700">Customer not avalible in office or house</label>
        </div>
    </div>
    <div class="p-4 bg-gray-200 border-t border-gray-300">
        <div class="flex items-center mb-2">
            <i class="fas fa-user-circle text-gray-600 text-2xl"></i>
            <div class="ml-2">
                <p class="text-gray-700 font-semibold"><?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?></p>
                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($details['address_line_1'] . ', ' . $details['address_line_2'] . ', ' . $details['city'] . ', ' . $details['postal_code'] . ', ' . $details['state'] . ', ' . $details['country']); ?></p>
                <p class="text-gray-500 text-sm"><?php echo ($details['order_status'] == 'pickup and delivering' ? $details['exch_track'] : $details['tracking_id']); ?></p>
                <p class="text-gray-500 text-sm">No recipient needed</p>
            </div>
        </div>
    </div>
    <div class="">
        <button type="submit" name="action" value="deli_to" class="w-full p-4 text-center text-white bg-blue-500">Submit</button>
    </div>
</form>
                </div>
            </div>
            <div class="border-b py-2">
                <div class="flex flex-col justify-between" onclick="toggleContent('content5')">
                    <h2 class="text-lg font-semibold">Unable to deliver</h2>
                    <p class="mt-2 text-gray-600">Make this order as undeliverable. You'll return the items to the
                        station.</p>
                </div>
                <div id="content5" style="display: none;">   <form method="POST" action="">
                    <?php echo $error_rl; ?>
    <div class="p-4">
        <div class="flex items-center mb-4">
            <input id="option2" type="radio" name="rl" value="Wrong /Incorrect address" class="form-radio h-5 w-5 text-gray-600">
            <label for="option2" class="ml-2 text-gray-700">Wrong /Incorrect address</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option1" type="radio" name="rl" value="rejected by <?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?> or a household member " class="form-radio h-5 w-5 text-gray-600" required>
            <label for="option1" class="ml-2 text-gray-700">rejected by <?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?> or a household member </label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option3" type="radio" name="rl" value="Delivery restrictions" class="form-radio h-5 w-5 text-gray-600">
            <label for="option3" class="ml-2 text-gray-700">Delivery restrictions</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option4" type="radio" name="rl" value="Damaged package" class="form-radio h-5 w-5 text-gray-600">
            <label for="option4" class="ml-2 text-gray-700">Damaged package</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option5" type="radio" name="rl" value="Unreadable label" class="form-radio h-5 w-5 text-gray-600">
            <label for="option5" class="ml-2 text-gray-700">Unreadable label</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option6" type="radio" name="rl" value="Recipient unavailable" class="form-radio h-5 w-5 text-gray-600">
            <label for="option6" class="ml-2 text-gray-700">Recipient unavailable</label>
        </div>
        <div class="flex items-center mb-4">
            <input id="option6" type="radio" name="rl" value="Cash on delivery unpaid" class="form-radio h-5 w-5 text-gray-600">
            <label for="option6" class="ml-2 text-gray-700">Cash on delivery unpaid</label>
        </div>
    </div>
    <div class="p-4 bg-gray-200 border-t border-gray-300">
        <div class="flex items-center mb-2">
            <i class="fas fa-user-circle text-gray-600 text-2xl"></i>
            <div class="ml-2">
                <p class="text-gray-700 font-semibold"><?php echo htmlspecialchars($details['first_name'] . ' ' . $details['last_name']); ?></p>
                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($details['address_line_1'] . ', ' . $details['address_line_2'] . ', ' . $details['city'] . ', ' . $details['postal_code'] . ', ' . $details['state'] . ', ' . $details['country']); ?></p>
                <p class="text-gray-500 text-sm"><?php echo ($details['order_status'] == 'pickup and delivering' ? $details['exch_track'] : $details['tracking_id']); ?></p>
                <p class="text-gray-500 text-sm">No recipient needed</p>
            </div>
        </div>
    </div>
    <div class="">
        <button type="submit" name="action" value="deli_to" class="w-full p-4 text-center text-white bg-blue-500">Submit</button>
    </div>
</form>
                </div>
            </div>
        </div>
        <?php else: ?>
        <p>No order details available.</p>
        <?php endif; ?>
    <?php else:?>

     <div class="" id="help">
        <script>
        function toggleContent(id) {
            var content = document.getElementById(id);
            var icon = content.previousElementSibling.querySelector('i');
            if (content.style.display === "none") {
                content.style.display = "block";
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                content.style.display = "none";
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
        </script>
        <div class="bg-gray-800 text-white p-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">How can i help you?</h1>
        
    </div>
        
        <div class="p-4">
            <div class="border-b py-2">
                <div class="flex justify-between items-center" onclick="toggleContent('content1')">
                    <h2 class="text-lg font-semibold">What will I deliver?</h2>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div id="content1" style="display: none;">
                    <p class="mt-2 text-white-700">You'll deliver Amazon packages of different sizes and weights.
                        Millions of items are available to shop on Amazon.in, including electronics, household
                        essentials, groceries, and much more.</p>
                </div>
            </div>
            <div class="border-b py-2">
                <div class="flex justify-between items-center" onclick="toggleContent('content5')">
                    <h2 class="text-lg font-semibold">How often can I make deliveries?</h2>
                    <i class="fas fa-chevron-up"></i>
                </div>
                <div id="content5" style="display: none;">
                    <p class="mt-2 text-white-700">You can make deliveries when it's convenient for you, depending on
                        the availability of our blocks. You can choose any available delivery block for the same day or
                        set availability for the future.</p>
                </div>
            </div>
        </div>
        <?php endif;?>
        <p class="mt-4 text-center"> Made with<span class="text-red-500"> ♥ </span>by MAN NAVLAKHA</p>
    </div>
<?php break; 
     case 'settings': ?>


    <?php break; 
    case 'aboutus': ?>
    <div class="p-4">
        <div class="p-4 absolute top-1 left-4"> </div>
        <div class="p-3 mt-8"> <img class=" w-30 h-30" height="100" src="../../assets/img/delivery.png" width="100" />
        </div>
        <h2 class="text-2xl font-bold mt-4"> Dilation </h2>
        <p class="text-lg mt-2"> V-1.15.11 </p <br><br>
        <!-- Author Section -->
        <div class="bg-gray-100 border-2 p-4 rounded-lg mb-6 w-full max-w-xs">
            <p class="text-xs font-semibold text-gray-500 mb-1"> AUTHOR </p>
            <div class="flex items-center justify-between">
                <p class="text-lg font-medium"> Man Navlakha</p>
                <div class="flex space-x-2"> <i class="fas fa-globe text-gray-600"> </i> <i
                        class="fab fa-android text-gray-600"> </i> </div>
            </div>
        </div>
        <h3 class="text-lg font-semibold">
            License </h3>
        <p class="text-gray-600 text-sm mt-2">
            © 2016-2021 Man Navlakha
        </p>
        <p class="text-gray-600 text-sm mt-2">
            This program is free software: you can redistribute it and/or modify it under the terms of the GNU General
            Public License as published by the Free Software Foundation, either version 2 of the License, or (at your
            option) any later version.
        </p>
        <p class="mt-4 text-center"> Made with<span class="text-red-500"> ♥ </span>by MAN NAVLAKHA</p>
    </div> <?php break; default: ?><p class="text-gray-700">Invalid page requested.</p> <?php break; endswitch; ?></div>
</body>

</html>