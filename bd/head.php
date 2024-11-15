
<?php
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];
$role = $_SESSION['role'];

// Fetch user information including profile picture
$sql = "SELECT username, profile_icon FROM admin_users WHERE id = '$id'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $profile_icon = $row['profile_icon'];
} else {
    // Handle case where user data or profile picture is not found
    $username = "Admin"; // Default username or handle error accordingly
    $profile_icon = ""; // Default profile icon or handle error accordingly
}

// Fetch user activities
$activitySql = "SELECT * FROM admin_user_activity WHERE id = '$id'";
$activityResult = $conn->query($activitySql);


//order statuse
//order statuse

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>


<html>
 <head>
 <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
  Dashboard
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
      <script src="../bd/assets/chart/chart.js"></script>
  <style>
   body {
            font-family: 'Roboto', sans-serif;
        }
        .sidebar {
            transition: transform 0.3s ease;
        }
        .sidebar-hidden {
            transform: translateX(-100%);
        }
        .search-input {
            display: none;
            transition: width 0.3s ease;
        }
        .search-input.active {
            display: block;
            width: 200px;
        }
  </style>
 </head>
 <body class="bg-zinc-200">
    <div class="flex justify-between items-center mb-6">
       
    
    <div class="flex items-center">
            <button class="m-5 text-gray-500 focus:outline-none lg:hidden" id="menu-button">
            <i class="fas fa-bars">
            </i>
            </button>
                <h2 class="mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-2xl dark:text-black">
                Brand Dilation Dashboard
                </h2>
         </div>


     <div class="flex items-center space-x-4">
   

                <button class="text-gray-500 focus:outline-none relative" id="notificationButton">
                <i class="fas fa-bell"></i>
                <!-- <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1" id="notificationCount">3</span> Optional count badge -->
                                </button>
                    <div class="hidden absolute top-0 right-0 ms-12 mt-36 mr-12 w-auto bg-white rounded shadow-lg z-50" id="notificationPopover">
                        <h3 class="text-gray-700 font-bold p-2 border-b">Notifications</h3>
                        <ul class="p-2 max-h-80 overflow-y-auto" id="notificationList"></ul>
                        <p class="p-2">This chat is only for our staff</p>
                    </div>
                                    <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        const notificationButton = document.getElementById('notificationButton');
                                        const notificationPopover = document.getElementById('notificationPopover');
                                        const notificationList = document.getElementById('notificationList');

                                        // Function to fetch notifications
                                        async function fetchNotifications() {
                                        try {
                                            const response = await fetch('fetch_notifications.php');
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
                                            if (!notificationButton.contains(event.target) && !notificationPopover.contains(event.target)) {
                                                notificationPopover.classList.add('hidden');
                                            }
                                        });
                                    });
                                    </script>




                                <button class="text-gray-500 focus:outline-none">
                                <a href="logout.php" id="logout">
                                <i class="fa fa-sign-out-alt">
                                </i>
                                </a>
                            </button>
                            <button class="text-gray-500 focus:outline-none">
                                <a href="profile.php">
                                    <i class="fas fa-user-circle">
                                        </i>
                                    </a>
                                </button>
     </div>
    </div>

  <script>
   const menuButton = document.getElementById('menu-button');
        const closeButton = document.getElementById('close-button');
        const sidebar = document.getElementById('sidebar');
        const searchButton = document.getElementById('search-button');
        const searchInput = document.getElementById('search-input');

        menuButton.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-hidden');
            document.body.classList.toggle('no-scroll', !sidebar.classList.contains('sidebar-hidden'));
        });

        closeButton.addEventListener('click', () => {
            sidebar.classList.add('sidebar-hidden');
            document.body.classList.remove('no-scroll');
        });

        searchButton.addEventListener('click', () => {
            searchInput.classList.toggle('active');
            searchInput.focus();
        });
  </script>   <p class="mt-20">
                <?php include 'footer.php'; ?>
            </p>

 </body>
</html>