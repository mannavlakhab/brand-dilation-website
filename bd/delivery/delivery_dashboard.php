<?php
session_start();
require 'db_connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$session_id = $_SESSION['dp_session_id'] ?? 0;
$search_query = $_GET['search_query'] ?? '';


$session_id = 103;
// Base query
$query = "
    SELECT o.order_id, c.first_name, c.last_name, c.phone_number, 
           a.address_line_1, a.address_line_2, a.city, a.postal_code, a.state, a.country,
           o.order_status, u.username, u.profile_icon, o.tracking_id
    FROM orders o
    JOIN admin_users u ON o.dp = u.id
    JOIN customers c ON o.customer_id = c.customer_id
    JOIN addresses a ON c.address = a.address_id
    WHERE o.dp = ?";

// If there's a search query, add conditions to filter by `order_id` or `tracking_id`
if (!empty($search_query)) {
    $query .= " AND (o.order_id LIKE ? OR o.tracking_id LIKE ?)";
}

// Prepare and bind parameters
$stmt = $db->prepare($query);
if (!empty($search_query)) {
    $searchParam = "%$search_query%"; // Wildcard search
    $stmt->bind_param("iss", $session_id, $searchParam, $searchParam);
} else {
    $stmt->bind_param("i", $session_id);
}

$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        #menu { display: none; position: absolute; top: 4rem; left: 1rem; }
        #help-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); }
        #help-content { max-width: 500px; margin: 5% auto; background: white; border-radius: 8px; overflow-y: auto; max-height: 80%; }
        .delivered { opacity: 0.5; } /* Class to lower opacity for delivered orders */
    </style>
</head>
<body class="bg-gray-100">
    <div class="bg-gray-800 text-white p-4 flex items-center justify-between">
        <i id="menu-icon" class="fas fa-bars text-xl cursor-pointer"></i>
        <h1 class="text-lg font-semibold">inventory</h1>
        <div class="flex items-center space-x-4">
            <i class="fas fa-sync-alt text-xl cursor-pointer" onclick="location.reload();"></i>
            <i id="help-icon" class="fas fa-question-circle text-xl cursor-pointer"></i>
        </div>
    </div>


<script>
    // Function to toggle the menu visibility
document.getElementById('menu-icon').addEventListener('click', function () {
    const menu = document.getElementById('menu');
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block'; // Show the menu
    } else {
        menu.style.display = 'none'; // Hide the menu
    }
});

</script>





    <!-- Sidebar menu -->
    <div id="menu" class="w-80 bg-gray-900 rounded-lg p-4 text-white shadow-lg">
        <div class="flex items-center mb-4">
            <img alt="Profile picture" class="rounded-full w-10 h-10 mr-3" src="http://localhost/uploads/profiles/10-47-58-930_512.webp" width="40" height="40"/>
            <span class="text-lg font-semibold"> Man Navlakha</span>
        </div>
        <ul class="space-y-2">
            <li class="py-2 border-b border-gray-700">Current stop</li>
            <li class="py-2 border-b border-gray-700">Today's itinerary</li>
            <li class="py-2 border-b border-gray-700">Pick up</li>
            <li class="py-2 border-b border-gray-700">Home</li>
            <li class="py-2 border-b border-gray-700">Feedback</li>
            <li class="py-2 border-b border-gray-700">Settings</li>
            <li class="py-2">Breaks</li>
        </ul>
    </div>












    <!-- Help Modal -->
    <div id="help-modal">
        <div id="help-content" class="p-4">
            <div class="bg-gray-800 text-white text-center py-4">
                <h1 class="text-lg font-bold">HELP</h1>
            </div>
            <div class="divide-y divide-gray-300">
                <div>
                    <div class="flex justify-between items-center py-4 px-6 cursor-pointer" onclick="toggleExpand('content1')">
                        <span class="text-lg">Amazon Flex Program</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <div id="content1" class="hidden px-6 py-2">
                        <p>Details about Amazon Flex Program...</p>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center py-4 px-6 cursor-pointer" onclick="toggleExpand('content2')">
                        <span class="text-lg">Amazon Flex App</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <div id="content2" class="hidden px-6 py-2">
                        <p>Details about Amazon Flex App...</p>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center py-4 px-6 cursor-pointer" onclick="toggleExpand('content3')">
                        <span class="text-lg">Scheduling and Your Calendar</span>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </div>
                    <div id="content3" class="hidden px-6 py-2">
                        <p>Details about Scheduling and Your Calendar...</p>
                    </div>
                </div>
                <!-- Additional help sections can be added similarly -->
            </div>
        </div>
    </div>

    
    <script>
        function openHelpPopup() {
    document.getElementById('help-popup-modal').classList.remove('hidden');
}

        // Function to open the help modal
        document.getElementById('help-icon').addEventListener('click', function () {
            document.getElementById('help-modal').style.display = 'flex';
        });
        
        // Function to toggle visibility of help content
        function toggleExpand(contentId) {
            const content = document.getElementById(contentId);
            content.classList.toggle('hidden');
        }

        // Function to close the help modal
        document.getElementById('help-modal').addEventListener('click', function (e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
        </script>

        




    


    <!-- Tabs and Search bar -->
    <div class="bg-gray-700 text-white flex justify-around py-2">
        <a href="#" class="text-sm font-medium">List</a>
        <a href="#" class="text-sm font-medium">Map</a>
        <a href="#" class="text-sm font-medium">Summary</a>
    </div>
    
    <div class="bg-gray-200 p-4 flex items-center">
        <i class="fas fa-search text-xl text-gray-500"></i>
        <form method="GET" action="">
            <input type="text" name="search_query" placeholder="Search by tracking ID" class="ml-4 p-2 w-full rounded bg-white border border-gray-300">
        </form>
    </div>

    <!-- Orders List -->
    <div class="p-4">
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $index => $order): ?>
                <div class="flex items-start mb-4 cursor-pointer <?= $order['order_status'] === 'delivered' ? 'delivered' : '' ?>" onclick="openPopup(<?= $order['order_id'] ?>)">
                    <div class="flex-shrink-0">
                        <div class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center"><?= $index + 1 ?></div>
                    </div>
                    <div class="ml-4">
                        <p class="text-green-600 font-medium">Next stop:</p>
                        <p class="text-gray-900 font-semibold"><?= htmlspecialchars($order['address_line_1']) ?></p>
                        <?php if (!empty($order['address_line_2'])): ?>
                            <p class="text-gray-700"><?= htmlspecialchars($order['address_line_2']) ?></p>
                            <?php endif; ?>
                            <p class="text-gray-700"><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['state']) ?> <?= htmlspecialchars($order['postal_code']) ?></p>
                            <p class="text-gray-700"><?= htmlspecialchars($order['country']) ?></p>
                        <p class="text-gray-700">Deliver to <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?> (<?= htmlspecialchars($order['phone_number']) ?>)</p>
                        <p class="text-gray-700">Tracking id <?= htmlspecialchars($order['tracking_id'])?></p>
                    </div>
                </div>
                <hr class="border-gray-300">
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-700">No orders assigned to you.</p>
        <?php endif; ?>
    </div>

    <!-- Popup Modal for Marking Delivered -->
    <div id="popup-modal" class="hidden fixed inset-0 bg-gray-700 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-4"><button onclick="openHelpPopup()">Show Help</button>

            <h2 class="text-lg font-semibold mb-4">Mark Order as Delivered</h2>
            <p>Are you sure you want to mark this order as delivered?</p>

        
            <div class="mt-4 flex justify-end">
                <button class="bg-green-500 text-white px-4 py-2 rounded" id="confirm-delivery">Confirm Delivery</button>
                <button class="bg-red-500 text-white px-4 py-2 rounded ml-2" onclick="closePopup()">Cancel</button>
            </div>
        </div>
         <!-- Help Modal within Popup -->
         <div id="help-popup-modal" class="hidden fixed inset-0 bg-gray-700 bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white rounded-lg shadow-lg w-1/2">
                <div class="bg-gray-800 text-white flex justify-between items-center p-4">
                    <h1 class="text-lg font-bold">Parcel HELP</h1>
                    <button class="text-white text-2xl" onclick="closeHelpPopup()">&times;</button>
                </div>
                <div class="divide-y divide-gray-300">
                    <div>
                        <div class="flex justify-between items-center py-4 px-6 cursor-pointer" onclick="toggleExpand('helpContent1')">
                            <span class="text-lg">Amazon Flex Program</span>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        <div id="helpContent1" class="hidden px-6 py-2">
                            <p>Details about Amazon Flex Program...</p>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center py-4 px-6 cursor-pointer" onclick="toggleExpand('helpContent2')">
                            <span class="text-lg">Amazon Flex App</span>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        <div id="helpContent2" class="hidden px-6 py-2">
                            <p>Details about Amazon Flex App...</p>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center py-4 px-6 cursor-pointer" onclick="toggleExpand('helpContent3')">
                            <span class="text-lg">Scheduling and Your Calendar</span>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </div>
                        <div id="helpContent3" class="hidden px-6 py-2">
                            <p>Details about Scheduling and Your Calendar...</p>
                        </div>
                    </div>
                    <!-- Additional help sections can be added similarly -->
                </div>
                <div class="flex justify-end p-4">
                    <button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="closeHelpPopup()">Close</button>
                </div>
            </div>
        </div>
        
        <script>
            // Function to close the help popup modal
            function closeHelpPopup() {
                document.getElementById('help-popup-modal').classList.add('hidden');
            }
        
            // Function to toggle the expansion of help content sections
            function toggleExpand(contentId) {
                const content = document.getElementById(contentId);
                content.classList.toggle('hidden');
            }
        </script>
    </div>

        <script>
            // Function to open delivery popup
            function openPopup(orderId) {
    document.getElementById('popup-modal').classList.remove('hidden');
    document.getElementById('confirm-delivery').onclick = function () {
        // AJAX request to update the order status
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_order_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                const response = JSON.parse(xhr.responseText);
                alert(response.message); // Show the response message
                closePopup();
            } else {
                alert("Error: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            alert("Request failed.");
        };
        xhr.send("order_id=" + orderId);
    };
}

        
        // Function to close the delivery popup
        function closePopup() {
            document.getElementById('popup-modal').classList.add('hidden');
        }
        
        </script>
        
        
        
</body>
</html>
