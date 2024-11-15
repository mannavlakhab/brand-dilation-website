
<?php
session_start();
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

// Array to store the order statuses and their counts
$orderStatusLabels = [];
$orderStatusCounts = [];

// Fetch order statuses and their counts from the database
$sql = "SELECT order_status, COUNT(*) as count FROM Orders GROUP BY order_status";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orderStatusLabels[] = $row['order_status'];
        $orderStatusCounts[] = (int)$row['count'];
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

//==========order statuse

// product sales 
// product sales 
// product sales 
  
// Fetch product sales data from the database
$sql = "SELECT 
p.brand,
p.model,
SUM(oi.product_quantity) AS total_quantity_sold,
SUM(oi.product_quantity * p.price) AS total_revenue
FROM 
Order_Items oi
JOIN 
Products p ON oi.product_id = p.product_id
JOIN 
Orders o ON oi.order_id = o.order_id
WHERE 
o.order_status = 'delivered' -- Consider only delivered orders
GROUP BY 
p.product_id
ORDER BY 
total_revenue "; // Order by total revenue in descending order

$result_sales = $conn->query($sql);

// Initialize arrays to store product names, quantity sold, and total revenue
$productNames = [];
$totalQuantitySold = [];
$totalRevenue = [];

// Fetch and store data in arrays
while ($row = $result_sales->fetch_assoc()) {
$productNames[] = $row['brand'] . ' ' . $row['model'];
$totalQuantitySold[] = $row['total_quantity_sold'];
$totalRevenue[] = $row['total_revenue'];
}

// Convert PHP arrays to JSON format
$productNamesJSON = json_encode($productNames);
$totalQuantitySoldJSON = json_encode($totalQuantitySold);
$totalRevenueJSON = json_encode($totalRevenue);

// ===========product sales 

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
 </head>
 <body class="bg-zinc-200">
 <div id="__next" bis_skin_checked="1">
        <div style="z-index:-2;" class="absolute z-0 top-0 inset-x-0 flex justify-center overflow-hidden pointer-events-none"
            bis_skin_checked="1">
            <div class="w-[108rem] flex-none flex justify-end" bis_skin_checked="1">
              
                <picture>
                    <source srcset="./assets/img/docs@30.8b9a76a2.avif" type="image/avif"><img
                        src="./assets/img/docs@tinypng.d9e4dcdc.png" alt=""
                        class="w-[90rem] flex-none max-w-none hidden dark:block" decoding="async">
                </picture>
            </div>
        </div>
      </div>
  <div class="flex">
  <?php include 'hsidebar.php'; ?>
   <!-- Main Content -->
   <div class="flex-1 p-6 ">
    <!-- Header -->
    <?php include 'head.php'; ?>





<div role="alert" class="w-max mb-5 p-2 bg-indigo-800 rounded-full items-center text-indigo-100 leading-none lg:rounded-full flex lg:inline-flex">
  <span class="flex rounded-full bg-indigo-500 uppercase px-2 py-1 text-xs font-bold mr-3">New</span>
  <a href=""><span class="font-semibold mr-2 text-left flex-auto"> <h2>Welcome, <?php echo $username; ?></h2></span></a>
  <svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" class="fill-current opacity-75 h-4 w-4"><path d="M12.95 10.707l.707-.707L8 4.343 6.586 5.757 10.828 10l-4.242 4.243L8 15.657l4.95-4.95z"></path></svg>
</div>
    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 max-w-fit md:grid-cols-3 gap-4 mb-6">
     <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-4 rounded-lg shadow-md">
      <h3 class="text-lg">
      Today's Sales
      </h3>
      <p class="text-2xl font-bold">
      <?php
          // Query for today's sales excluding cancelled and pending orders
          $today_sql = "SELECT SUM(total_price) AS total_sales FROM Orders WHERE DATE(order_date) = CURDATE() AND order_status NOT IN ('cancel', 'pending')";
          $today_result = $conn->query($today_sql);

          if ($today_result && $today_result->num_rows > 0) {
              $today_row = $today_result->fetch_assoc();
              $total_sales_today = formatIndianCurrency($today_row["total_sales"]);
              echo "<p>Total Sales: ₹" . $total_sales_today . "</p>";
          } else {
              echo "<p>No sales today.</p>";
          }
          ?>
      </p>
     </div>
     <div class="bg-gradient-to-r from-teal-400 to-blue-500 text-white p-4 rounded-lg shadow-md">
      <h3 class="text-lg">
       This Week's Sales
      </h3>
      <p class="text-2xl font-bold">
      <?php
          // Query for this week's sales excluding cancelled and pending orders
          $week_sql = "SELECT SUM(total_price) AS total_sales FROM Orders WHERE YEARWEEK(order_date) = YEARWEEK(NOW()) AND order_status NOT IN ('cancel', 'pending')";
          $week_result = $conn->query($week_sql);

          if ($week_result && $week_result->num_rows > 0) {
              $week_row = $week_result->fetch_assoc();
              $total_sales_week = formatIndianCurrency($week_row["total_sales"]);
              echo "<p>Total Sales: ₹" . $total_sales_week . "</p>";
          } else {
              echo "<p>No sales this week.</p>";
          }
          ?>
      </p>

      <!-- <p class="text-sm">
       Total Clients Profit
      </p> -->
     </div>
     <div class="bg-gradient-to-r from-green-400 to-green-600 text-white p-4 rounded-lg shadow-md">
      <h3 class="text-lg">
      This Month's Sales
      </h3>
      <p class="text-2xl font-bold">
      <?php
          // Query for this month's sales excluding cancelled and pending orders
          $month_sql = "SELECT SUM(total_price) AS total_sales FROM Orders WHERE YEAR(order_date) = YEAR(NOW()) AND MONTH(order_date) = MONTH(NOW()) AND order_status NOT IN ('cancel', 'pending')";
          $month_result = $conn->query($month_sql);

          if ($month_result && $month_result->num_rows > 0) {
              $month_row = $month_result->fetch_assoc();
              $total_sales_month = formatIndianCurrency($month_row["total_sales"]);
              echo "<p>Total Sales: ₹" . $total_sales_month . "</p>";
          } else {
              echo "<p>No sales this month.</p>";
          }

          // Function to format Indian currency notation
          function formatIndianCurrency($amount) {
            if ($amount === null) {
                return "0 ,waiting for order"; // Or any appropriate handling for null values
            }
        
            $formatted = "";
            if ($amount >= 10000000) {
                $formatted = number_format($amount / 10000000, 2) . "CR";
            } elseif ($amount >= 100000) {
                $formatted = number_format($amount / 100000, 2) . "L";
            } elseif ($amount >= 1000) {
                $formatted = number_format($amount / 1000, 2) . "K";
            } else {
                $formatted = number_format($amount, 2);
            }
            return $formatted;
        }
        
          ?>
      </p>
      <!-- <p class="text-sm">
       People Interested
      </p> -->
     </div>
    </div>
<?php if($role == 'outer'){
?>
 <!-- Sales Report -->
 <div class="bg-white max-w-min p-4 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                          Order Report
                        </h3>
                        <div class="flex space-x-4">
                        <p>Here's a chart showing the distribution of order statuses.</p>
                            
                        </div>
                        </div>
                        <div id="orderStatusChartContainer">
                                  <canvas id="orderStatusChart"></canvas>
                              </div>
                          </div>
                          <script>
                  var orderStatusLabels = <?php echo json_encode($orderStatusLabels); ?>;
                  var orderStatusCounts = <?php echo json_encode($orderStatusCounts); ?>;

                  var ctx = document.getElementById('orderStatusChart').getContext('2d');
                  var orderStatusChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                      labels: orderStatusLabels,
                      datasets: [{
                        label: 'Order Status',
                        data: orderStatusCounts,
                        backgroundColor: [
                          'rgba(255, 99, 132, 0.2)',
                          'rgba(54, 162, 235, 0.2)',
                          'rgba(255, 206, 86, 0.2)',
                          'rgba(75, 192, 192, 0.2)',
                          'rgba(153, 102, 255, 0.2)'
                        ],
                        borderColor: [
                          'rgba(255, 99, 132, 1)',
                          'rgba(54, 162, 235, 1)',
                          'rgba(255, 206, 86, 1)',
                          'rgba(75, 192, 192, 1)',
                          'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                      }]
                    },
                    options: {
                      scales: {
                        yAxes: [{
                          ticks: {
                            beginAtZero: true
                          }
                        }]
                      }
                    }
                  });
                  </script>
<?php
}
else {
  ?>

                  <div class="grid lg:grid-cols-3 w-[700px] gap-4 md:grid-cols-2 mb-10">
                    <div
                      class="group w-full rounded-lg bg-[#3c0e40] p-5 transition relative duration-300 cursor-pointer hover:translate-y-[3px] hover:shadow-[0_-8px_0px_0px_#2196f3]"
                    >
                      <p class="text-white text-2xl">Products</p>
                      <p class="text-white text-sm"><?php
                                // Example PHP code to fetch and display product count
                                $sql = "SELECT COUNT(*) as product_count FROM Products";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['product_count'];
                                ?></p>
                      <svg
                        xml:space="preserve"
                        style="enable-background:new 0 0 512 512"
                        viewBox="0 -960 960 960"
                        fill="#ffffff"
                        y="0"
                        x="0"
                        height="36"
                        width="36"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                        class="group-hover:opacity-100 absolute right-[10%] top-[50%] translate-y-[-50%] opacity-20 transition group-hover:scale-110 duration-300">
                        <path d="M200-80q-33 0-56.5-23.5T120-160v-451q-18-11-29-28.5T80-680v-120q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800v120q0 23-11 40.5T840-611v451q0 33-23.5 56.5T760-80H200Zm0-520v440h560v-440H200Zm-40-80h640v-120H160v120Zm200 280h240v-80H360v80Zm120 20Z"/></svg>
                    </div>


                    <div
                      class="group w-full rounded-lg bg-[rgb(41,49,79)] p-5 transition relative duration-300 cursor-pointer hover:translate-y-[3px] hover:shadow-[0_-8px_0px_0px_rgb(244,67,54)]"
                    >
                      <p class="text-white text-2xl">Orders</p>
                      <p class="text-white text-sm"><?php
                                $sql = "SELECT COUNT(*) as product_count FROM orders";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['product_count'] ;
                                ?></p>

                      <svg
                        class="group-hover:opacity-100 absolute right-[10%] top-[50%] translate-y-[-50%] opacity-20 transition group-hover:scale-110 duration-300"
                        xml:space="preserve"
                        style="enable-background:new 0 0 512 512"
                        viewBox="0 -960 960 960"
                        fill="#fff"
                        y="0"
                        x="0"
                        height="36"
                        width="36"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                      ><path d="M160-160v-516L82-846l72-34 94 202h464l94-202 72 34-78 170v516H160Zm240-280h160q17 0 28.5-11.5T600-480q0-17-11.5-28.5T560-520H400q-17 0-28.5 11.5T360-480q0 17 11.5 28.5T400-440ZM240-240h480v-358H240v358Zm0 0v-358 358Z"/></svg>
                    </div>

                    <div
                      class="group w-full rounded-lg bg-[#673ab7] p-5 transition relative duration-300 cursor-pointer hover:translate-y-[3px] hover:shadow-[0_-8px_0px_0px_#3cce40]"
                    >
                      <p class="text-white text-2xl">Category</p>
                      <p class="text-white text-sm"><?php
                                // Example PHP code to fetch and display product count
                                $sql = "SELECT COUNT(*) as product_count FROM categories";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['product_count'];
                                ?></p>
                      <svg
                        xml:space="preserve"
                        style="enable-background:new 0 0 512 512"
                        viewBox="0 -960 960 960"
                        fill="#ffffff"
                        y="0"
                        x="0"
                        height="36"
                        width="36"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                        class="group-hover:opacity-100 absolute right-[10%] top-[50%] translate-y-[-50%] opacity-20 transition group-hover:scale-110 duration-300">
                        <path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Zm580-60q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm-500-20h160v-160H200v160Zm202-420h156l-78-126-78 126Zm78 0ZM360-340Zm340 80Z"/></svg>
                      </div>

                    <div
                      class="group w-full rounded-lg bg-[#ff6666] p-5 transition relative duration-300 cursor-pointer hover:translate-y-[3px] hover:shadow-[0_-8px_0px_0px_#885545]"
                    >
                      <p class="text-white text-2xl">Users</p>
                      <p class="text-white text-sm"><?php
                                $sql = "SELECT COUNT(*) as product_count FROM users";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['product_count'];
                                ?></p>
                      <svg
                        xml:space="preserve"
                        style="enable-background:new 0 0 512 512"
                        viewBox="0 -960 960 960"
                        fill="#ffffff"
                        y="0"
                        x="0"
                        height="36"
                        width="36"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                        class="group-hover:opacity-100 absolute right-[10%] top-[50%] translate-y-[-50%] opacity-20 transition group-hover:scale-110 duration-300">
                        <path d="m260-520 220-360 220 360H260ZM700-80q-75 0-127.5-52.5T520-260q0-75 52.5-127.5T700-440q75 0 127.5 52.5T880-260q0 75-52.5 127.5T700-80Zm-580-20v-320h320v320H120Zm580-60q42 0 71-29t29-71q0-42-29-71t-71-29q-42 0-71 29t-29 71q0 42 29 71t71 29Zm-500-20h160v-160H200v160Zm202-420h156l-78-126-78 126Zm78 0ZM360-340Zm340 80Z"/></svg>
                      </div>

                    <div
                      class="group w-full rounded-lg bg-[#7e7e7e] p-5 transition relative duration-300 cursor-pointer hover:translate-y-[3px] hover:shadow-[0_-8px_0px_0px_#2196f3]"
                    >
                      <p class="text-white text-2xl">Coupon</p>
                      <p class="text-white text-sm"><?php
                                $sql = "SELECT COUNT(*) as product_count FROM couponcodes";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                echo $row['product_count'];
                                ?></p>
                      <svg
                        xml:space="preserve"
                        style="enable-background:new 0 0 512 512"
                        viewBox="0 -960 960 960"
                        fill="#ffffff"
                        y="0"
                        x="0"
                        height="36"
                        width="36"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                        class="group-hover:opacity-100 absolute right-[10%] top-[50%] translate-y-[-50%] opacity-20 transition group-hover:scale-110 duration-300">
                        <path d="M40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm720 0v-120q0-44-24.5-84.5T666-434q51 6 96 20.5t84 35.5q36 20 55 44.5t19 53.5v120H760ZM360-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm400-160q0 66-47 113t-113 47q-11 0-28-2.5t-28-5.5q27-32 41.5-71t14.5-81q0-42-14.5-81T544-792q14-5 28-6.5t28-1.5q66 0 113 47t47 113ZM120-240h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T440-640q0-33-23.5-56.5T360-720q-33 0-56.5 23.5T280-640q0 33 23.5 56.5T360-560Zm0 320Zm0-400Z"/></svg>
                      </div>


                  </div>

                      <!-- Reports -->
                      <div class="grid grid-cols-1 max-w-fit md:grid-cols-2 gap-4 mb-6">
                      <!-- Sales Report -->
                      <div class="bg-white p-4 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                          Order Report
                        </h3>
                        <div class="flex space-x-4">
                        <p>Here's a chart showing the distribution of order statuses.</p>
                            
                        </div>
                        </div>
                        <div id="orderStatusChartContainer">
                                  <canvas id="orderStatusChart"></canvas>
                              </div>
                          </div>
                      <!-- Bandwidth Reports -->
                      <div class="bg-white p-4 rounded-lg shadow-md">
                        <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">
                        Product Sales
                        </h3>
                        <div class="flex space-x-4">
                        Here's a chart showing the product sales for delivered orders.
                        </div>
                        </div>
                        <div id="orderStatusChartContainer">
                      <canvas id="productSalesChart" ></canvas>
                  </div>
                  </div>
                      </div>


                      <script>
                  var orderStatusLabels = <?php echo json_encode($orderStatusLabels); ?>;
                  var orderStatusCounts = <?php echo json_encode($orderStatusCounts); ?>;

                  var ctx = document.getElementById('orderStatusChart').getContext('2d');
                  var orderStatusChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                      labels: orderStatusLabels,
                      datasets: [{
                        label: 'Order Status',
                        data: orderStatusCounts,
                        backgroundColor: [
                          'rgba(255, 99, 132, 0.2)',
                          'rgba(54, 162, 235, 0.2)',
                          'rgba(255, 206, 86, 0.2)',
                          'rgba(75, 192, 192, 0.2)',
                          'rgba(153, 102, 255, 0.2)'
                        ],
                        borderColor: [
                          'rgba(255, 99, 132, 1)',
                          'rgba(54, 162, 235, 1)',
                          'rgba(255, 206, 86, 1)',
                          'rgba(75, 192, 192, 1)',
                          'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                      }]
                    },
                    options: {
                      scales: {
                        yAxes: [{
                          ticks: {
                            beginAtZero: true
                          }
                        }]
                      }
                    }
                  });
                  </script>
                  <script>
                  // Parse JSON data
                  var productNames = <?php echo $productNamesJSON; ?>;
                  var totalQuantitySold = <?php echo $totalQuantitySoldJSON; ?>;
                  var totalRevenue = <?php echo $totalRevenueJSON; ?>;

                  // Get canvas element
                  var ctx = document.getElementById('productSalesChart').getContext('2d');

                  // Create chart
                  var productSalesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                      labels: productNames, // Product names on X-axis
                      datasets: [{
                        label: 'Total Quantity Sold',
                        data: totalQuantitySold, // Quantity sold on Y-axis
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                      }, {
                        label: 'Total Revenue',
                        data: totalRevenue, // Revenue on Y-axis
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                      }]
                    },
                    options: {
                      scales: {
                        yAxes: [{
                          ticks: {
                            beginAtZero: true
                          }
                        }]
                      }
                    }
                  });
                  </script>



<!-- table orders newly -->
<div class="bg-white shadow-xl mb-20 p-4 rounded-lg shadow-md ">
     <h3 class="text-lg font-semibold mb-4">
     Top 10 letest Orders
     </h3>
     <div class="overflow-x-auto">
     <table class="min-w-full bg-white">
       <thead>
        <tr>
         <th class="py-2 px-4 border-b">
         Order ID
         </th>
         <th class="py-2 px-4 border-b">
         Order Status
         </th>
         <th class="py-2 px-4 border-b">
         Product Name
         </th>
         <th class="py-2 px-4 border-b">
         Quantity
         </th>
         <th class="py-2 px-4 border-b">
         Total Price
         </th>
         <th class="py-2 px-4 border-b">
         Shipping Cost
         </th>
         <th class="py-2 px-4 border-b">
         Customer Name
         </th>
         <th class="py-2 px-4 border-b">
         Phone Number
         </th>
         <th class="py-2 px-4 border-b">
         Email
         </th>
        </tr>
       </thead>
       <tbody>
      <?php
      $sql = "SELECT o.order_id,
       o.order_status,
       p.brand,
       p.model,
       o.total_price,
       o.shipping_cost,
       oi.product_quantity,
       c.first_name,c.last_name,c.phone_number,
       c.email
FROM Orders o
INNER JOIN Order_Items oi ON o.order_id = oi.order_id
INNER JOIN Products p ON oi.product_id = p.product_id
INNER JOIN customers c ON o.customer_id  = c.customer_id  ORDER BY order_id DESC
LIMIT 10;";
$result = $conn->query($sql);          
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['order_id'] . "</p></td>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['order_status'] . "</p></td>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['brand'] .$row['model'] . "</p></td>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['product_quantity'] . "</p></td>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['total_price'] . "</p></td>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['shipping_cost'] . "</p></td>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['first_name'] . $row['last_name'] . "</p></td>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['phone_number'] . "</p></td>";
    echo "<td class='py-2 px-4 border-b' ><p>" . $row['email'] . "</p></td>";
    echo "</tr>";
  }
} else {
  echo "<h3>No Orders Found</h3>";
}
?>
       </tbody>
</table>
</div>
<br>
<?php 
}?>

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
  </script>

 </body>
</html>