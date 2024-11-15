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

    // Fetch announcements from the database
$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = $conn->query($sql);

$announcements = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}    
?>
<!DOCTYPE html>
<html lang="en">
<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Dashboard</title>
      <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
      <link rel="stylesheet" href="dash.css">
      
      <script src="../bd/assets/chart/chart.js"></script>
     
  </head>
  <body>
  <div class="navarea">
      <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-left">
            <img src="./ass/bd.png" alt="Company Logo" class="company-logo">
        </div>
        <div style="margin-left: 51%;" class="navbar-right">
        <ul>
        <li><a href="#" id="notificationIcon"><img src="./assets/img/notification.png" alt="Notification"></a></li>
        <li><a href="profile.php"><img src="<?php echo $profile_icon; ?>" alt="Profile Picture"></a></li>
        <p style="margin: 1%; font-size: 1rem; text-align: center; text-transform: capitalize; font-weight: 500; color: #bebebe; font-family: Montserrat;">
          <a href="profile.php">Welcome, <?php echo $username; ?></a>
        </p>
        <li><a href="logout.php" id="logout"><img src="./assets/img/logout.png" alt="logout"></a></li>

      </ul>
    </div>
  </div>  

</div>
  
  <div class="container_admin">

  


  <div class="sidebar">  <aside class="sidebar-container"><?php include 'sidebar.php'; ?></aside></div>



  <div class="bash">
  

  <!-- Modal Structure -->
  <div id="announcementModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Announcements</h2>
      <div id="announcementsList">
        <?php foreach ($announcements as $announcement): ?>
          <div class="announcement">
            <h3><?php echo $announcement['title']; ?></h3>
            <p><?php echo $announcement['message']; ?></p>
            <p><small><?php echo $announcement['created_at']; ?></small></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <script>
    // Modal handling script
    var modal = document.getElementById("announcementModal");
    var btn = document.getElementById("notificationIcon");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
      modal.style.display = "block";
    }

    span.onclick = function() {
      modal.style.display = "none";
    }

    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
  <style>/* Modal styles */
  .announcement{
    margin:2%;
    background-color:#ff6666;
    color:#000;
    padding: 1%;
    border-radius:10px;
    border: 1px solid #ccc;
  }
.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgb(0,0,0);
  background-color: rgba(0,0,0,0.4);
  padding-top: 60px;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  border-radius:10px;
  border: 1px solid #ccc;
}

.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
</style>

<div class="row-dash">  
<!-- Start cards -->
  <h1 id="Dashboard" >BD-Dashboard</h1>

  <!-- report -->
  <div class="row-dashp">
  <div class="new_con_mr">
    <div class="row_mrg_3">
      <div class="mr_1">
        <div class="card-dashp">
          <h3>Today's Sales</h3>
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
        </div>
      </div>
      <div class="mr_2">
        <div class="card-dashp">
          <h3>This Week's Sales</h3>
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
        </div>
      </div>
      <div class="mr_3">
        <div class="card-dashp">
          <h3>This Month's Sales</h3>
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
        </div>
      </div>
    </div>
  </div>
</div>
<!-- end report -->



<!-- Products -->
<div class="column-dash">
      <div class="card-dash">
        <h3>Products</h3>
        <p>Here only</p>
        <p>  <?php
              // Example PHP code to fetch and display product count
              $sql = "SELECT COUNT(*) as product_count FROM Products";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              echo "<p>" . $row['product_count'] . "</p>";
              ?></p>
      </div>
    </div>
<!-- Orders -->
    <div class="column-dash">
      <div class="card-dash">
        <h3>Orders</h3>
        <p>Here only</p>
        <p>  <?php
              // Example PHP code to fetch and display product count
              $sql = "SELECT COUNT(*) as product_count FROM orders";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              echo "<p>" . $row['product_count'] . "</p>";
              ?></p>
      </div>
    </div>
<!-- Category -->
    <div class="column-dash">
      <div class="card-dash">
        <h3>Category</h3>
        <p>Here only</p>
        <p>  <?php
              // Example PHP code to fetch and display product count
              $sql = "SELECT COUNT(*) as product_count FROM categories";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              echo "<p>" . $row['product_count'] . "</p>";
              ?></p>
      </div>
    </div>
    <!-- Total Prices -->
    <div class="column-dash">
      <div class="card-dash">
        <h3>Total Prices</h3>
        <p>of Orders</p>
        <p> <?php
$sql = "SELECT SUM(total_price) AS nws FROM orders;";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
echo "<p>" . $row['nws'] . "</p>";
?></p>
      </div>
      
    </div>
<!-- Coupon -->
    <div class="column-dash">
      <div class="card-dash">
        <h3>Coupon</h3>
        <p>available only</p>
        <p>  <?php
              // Example PHP code to fetch and display product count
              $sql = "SELECT COUNT(*) as product_count FROM couponcodes";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              echo "<p>" . $row['product_count'] . "</p>";
              ?></p>
      </div>
    </div>
<!-- Variation -->
    <div class="column-dash">
      <div class="card-dash">
        <h3>Variation</h3>
        <p>of Products</p>
        <p>  <?php
              // Example PHP code to fetch and display product count
              $sql = "SELECT COUNT(*) as product_count FROM ProductVariations";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              echo "<p>" . $row['product_count'] . "</p>";
              ?></p>
      </div>
    </div>
<!-- slider -->
    <div class="column-dash">
      <div class="card-dash">
        <h3>slider</h3>
        <p>Here only</p>
        <p>  <?php
              // Example PHP code to fetch and display product count
              $sql = "SELECT COUNT(*) as product_count FROM slideshow";
              $result = $conn->query($sql);
              $row = $result->fetch_assoc();
              echo "<p>" . $row['product_count'] . "</p>";
              ?></p>
      </div>
    </div>

    
    <!-- ====end cards==== -->

    <!-- ====STARTS CHARTS==== -->

    <!-- 1 CAHRT -->

    <!-- Order Status Distribution -->
     
    <br><br>
<!-- HTML and JavaScript for displaying the chart -->
    <div  class="column-dashp">
        <h1 style="margin:3%" id="chart">Order Status Distribution</h1>
        <div class="card-dashp">
            <p>Here's a chart showing the distribution of order statuses.</p>
            <div id="orderStatusChartContainer">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>
    <!-- ==END Order Status Distribution -->
    
    <!-- 2 CHART -->

    <!-- Product Sales -->
  <div class="column-dashp">
    <h1 style="margin:3%" id="chart">Product Sales</h1>
    <div class="card-dashp">
    <p>Here's a chart showing the product sales for delivered orders.</p>
    <div id="orderStatusChartContainer">
    <canvas id="productSalesChart" ></canvas>
</div>
</div>
</div>
<!-- ==END Product Sales -->









<!-- ========= -->
  
<br>
<!-- table orders newly -->
<div class='row-dashp'>
  <div class='column-dashp'><br>
    <h1>Newly orders</h1>
    <br><br>
    <div class='card-dashp'>

      <p>Here's a chart showing the letest 10 new orders.</p> <br>
      <table>
        <tr>
          <th>Order ID: </th>
          <th>Order Status: </th>
          <th>Product Name: </th>
          <th>Quantity: </th>
          <th>Total Price: </th>
          <th>Shipping Cost: </th>
          <th>Customer Name: </th>
          <th>Phone Number: </th>
          <th>Email: </th>
        </tr>
    
      <?php
      
      // order table 
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
  // Loop through each order
  
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";

    echo "<td><p>" . $row['order_id'] . "</p></td>";
    echo "<td><p>" . $row['order_status'] . "</p></td>";
    echo "<td><p>" . $row['brand'] .$row['model'] . "</p></td>";
    echo "<td><p>" . $row['product_quantity'] . "</p></td>";
    echo "<td><p>" . $row['total_price'] . "</p></td>";
    echo "<td><p>" . $row['shipping_cost'] . "</p></td>";
    echo "<td><p>" . $row['first_name'] . $row['last_name'] . "</p></td>";
    echo "<td><p>" . $row['phone_number'] . "</p></td>";
    echo "<td><p>" . $row['email'] . "</p></td>";

    echo "</tr>";
  }
} else {
 
  echo "<h3>No Orders Found</h3>";
}


      ?>
        </table>
    </div>

    <!-- product Table -->
<br>

<div class='row-dashp'>
  <div id="products" class='column-dashp'><br>
    <h1>Products</h1>
    <br><br>
    <div class='card-dashp'>
      <table>
        <tr>
          <th colspan="8"><a href="/view_product.php" target="_blank" rel="noopener noreferrer">View All >></a></th>
        </tr>
        <tr>
          <th>Product ID: </th>
          <th>Product Name: </th>
          <th>Quantity: </th>
          <th>Price: </th>
          <th>Refurbished: </th>
          <th>Digital Product: </th>
          <th>img_path: </th>
        </tr>
        
  <?php
      
  $sql = "SELECT p.product_id ,
  p.brand,
  p.model,
  p.image_main,
  p.stock_quantity,
  p.price,
  p.refurbished,
  p.digital_product
FROM Products p
ORDER BY product_id 
LIMIT 10;";


$result = $conn->query($sql);


         
if ($result->num_rows > 0) {
// Loop through each order

while ($row = $result->fetch_assoc()) {
echo "<tr>";

echo "<td><p>" . $row['product_id'] . "</p></td>";
echo "<td><p>" . $row['brand'] .$row['model'] . "</p></td>";
echo "<td><p>" . $row['stock_quantity'] . "</p></td>";
echo "<td><p>" . $row['price'] . "</p></td>";
echo "<td><p>" . $row['refurbished'] . "</p></td>";
echo "<td><p>" . $row['digital_product'] . "</p></td>";
echo "<td><img class='img_product' src=../" . $row['image_main'] . "></td>";

echo "</tr>";
}
} else {

echo "<h3>No Product Found</h3>";
}
              ?>
      </table>
    </div>

    <!-- category table -->

    <br>

<div class='row-dashp'>
  <div id="categories" class='column-dashp'><br>
    <h1>Category</h1>
    <br><br>
    <div class='card-dashp'>
      <table>
        <tr>
          <th colspan="8">View All >></th>
        </tr>
        <tr>
          <th>Category: </th>
          <th></th>
          <th>Name: </th>
          <th></th>
          <th>img: </th>
          <th></th>
        </tr>
        
  <?php
      
  $sql = "SELECT 
  c.category_id,
  c.name,
  c.img
FROM categories c
ORDER BY category_id 
LIMIT 10;";


$result = $conn->query($sql);


         
if ($result->num_rows > 0) {
// Loop through each order

while ($row = $result->fetch_assoc()) {
echo "<tr>";
echo "<td><p>" . $row['category_id'] . "</p></td>";
echo "<td></td>";
echo "<td><p>" . $row['name'] . "</p></td>";
echo "<td></td>";
echo "<td><img class='img_product' src=../" . $row['img'] . "></td>";
echo "<td></td>";


echo "</tr>";
}
} else {

echo "<h3>No Product Found</h3>";
}
              ?>
      </table>
    </div>
    </div>
  </div>

<script>
  
  // order statuses
// order statuses

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
</div>
</div>
      
  <!-- Dashboard Container and other content -->
  <!-- <div class="dashboard-container"> -->
    <!-- Side bar nav -->
  
    <!-- Dashboard content -->
    <!-- (Your existing dashboard content here) -->


<!-- row 1 -->
 

   
</body>
</html>
