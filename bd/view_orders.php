<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$searchQuery = ""; // Initialize empty search query

// Handle search query
if (isset($_GET['search'])) {
  // Check if connection is still open before using mysqli_real_escape_string
  if ($conn->ping()) {
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']); // Sanitize user input
  } else {
    // Handle reconnecting or error handling as needed
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    $searchQuery = mysqli_real_escape_string($conn, $_GET['search']); // Sanitize user input
  }
}

// Pagination setup
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$results_per_page = 15; // Adjust as needed
$offset = ($page - 1) * $results_per_page;

$sql = "SELECT SQL_CALC_FOUND_ROWS o.order_id, o.customer_id, o.order_status, o.shipping_address, o.total_price, o.shipping_cost, 
       o.payment_method, o.payment_details, o.payment_status, o.payment_details, o.tracking_id, o.order_date, 
       c.first_name, c.last_name, c.email, c.phone_number, c.address,
       p.product_id, p.brand, p.model, p.price, oi.product_quantity, oi.variation_id, pv.variation_value 
  FROM Orders o
  JOIN Customers c ON o.customer_id = c.customer_id
  LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
  LEFT JOIN Products p ON oi.product_id = p.product_id
  LEFT JOIN ProductVariations pv ON oi.variation_id = pv.variation_id";

// Build search condition based on user input
if (!empty($searchQuery)) {
  $sql .= " WHERE (o.order_id LIKE '%$searchQuery%' OR c.first_name LIKE '%$searchQuery%' OR c.last_name LIKE '%$searchQuery%' OR c.phone_number LIKE '%$searchQuery%' OR o.tracking_id LIKE '%$searchQuery%')";
}

$sql .= " ORDER BY o.order_id DESC";
$sql .= " LIMIT $offset, $results_per_page";

$result = $conn->query($sql);

// Count total rows for pagination
$total_results = $conn->query("SELECT FOUND_ROWS() as total")->fetch_assoc()['total'];

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-base">
    <div class="flex h-screen "><?php include 'hsidebar.php'; ?>
    <div class="flex-1  min-w-96 p-4 min-h-max h-screen pt-6 pb-8 mb-4 bg-white rounded shadow-md">
    
    <h2 class="text-2xl font-bold mb-4">View Orders</h2>
    
    
                                <form class="z-10 bg-gray" action="" method="GET">
                                    <div class="flex items-center justify-center p-5">

                                        <div class="flex">
                                            <div class="flex w-10 items-center justify-center rounded-tl-lg rounded-bl-lg border-r border-gray-200 bg-gray-100 p-5">
                                                <svg viewBox="0 0 20 20" aria-hidden="true" class="pointer-events-none absolute w-5 fill-gray-500 transition">
                                                                    <path
                                                        d="M16.72 17.78a.75.75 0 1 0 1.06-1.06l-1.06 1.06ZM9 14.5A5.5 5.5 0 0 1 3.5 9H2a7 7 0 0 0 7 7v-1.5ZM3.5 9A5.5 5.5 0 0 1 9 3.5V2a7 7 0 0 0-7 7h1.5ZM9 3.5A5.5 5.5 0 0 1 14.5 9H16a7 7 0 0 0-7-7v1.5Zm3.89 10.45 3.83 3.83 1.06-1.06-3.83-3.83-1.06 1.06ZM14.5 9a5.48 5.48 0 0 1-1.61 3.89l1.06 1.06A6.98 6.98 0 0 0 16 9h-1.5Zm-1.61 3.89A5.48 5.48 0 0 1 9 14.5V16a6.98 6.98 0 0 0 4.95-2.05l-1.06-1.06Z">
                                                                    </path>
                                                </svg>
                                            </div>
                                        <input type="text" class="w-full max-w-[560px] bg-gray-300 pl-2 text-base  outline-0"
                                                placeholder="Search by Order ID, Customer Name, Phone Number" type="text" id="search" name="search"
                                            value="<?php echo $searchQuery; ?>">
                                            <input type="submit" value="Search"
                                                class="bg-purple-500 p-2 rounded-tr-lg rounded-br-lg text-white font-semibold hover:bg-purple-800 transition-colors">
                                        </div>
                                    </div>

                                </form>
   
    <table class="table-auto border-spacing-x-0.5 ">
            <!-- <caption>Customers Orders using bd-shop</caption> -->
                    <thead class=" text-gray-700 border uppercase bg-gray-50 p-3 text-xs sm:rounded-lg	">
                        <tr>
                            <th scope="col" class="" >Order ID</th>
                            <th scope="col" class="" >Date</th>
                            <th scope="col" class="" >Order Status</th>
                            <th scope="col" class="" >Customer Name</th>
                            <!-- <th scope="col" class="" >Email</th> -->
                            <th scope="col" class="" >Phone</th>
                            <!-- <th scope="col" class=" text-balance" >Shipping Address</th> -->
                            <th scope="col" class="" >Payment Method</th>
                            <th scope="col" class=" text-balance" >Payment Details</th>
                            <th scope="col" class="" >Payment Status</th>
                            <th scope="col" class="" >Total Price</th>
                            <th scope="col" class="" >Shipping Cost</th>
                            <th scope="col" class="" >Product Name</th>
                            <th scope="col" class="" >Quantity</th>
                            <th scope="col" class="" >Action</th>
                        </tr>
                    </thead>
                     <tbody class="text-sm">


                        <!-- PHP code for fetching and displaying data -->
                        <?php
                    if ($result->num_rows > 0) {
                        // Loop through each order
                        while ($row = $result->fetch_assoc()) {
                        echo "<tr class='hover:bg-gray-50 border-b'  >";
                        echo "<td scope='row' class='hover:bg-gray-50' data-label='Order ID' >" . $row['order_id'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Date' >" . date('j M, Y', strtotime($row['order_date'])) . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Date' >" . $row['order_status'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Date' >" . $row['first_name'] . "<br>" . $row['last_name'] . "</td>";
                        // echo "<td class='hover:bg-gray-50' data-label='Email' >" . $row['email'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Phone' >" . $row['phone_number'] . "</td>";
                        // echo "<td class='hover:bg-gray-50' data-label='Date' >" . $row['shipping_address'] . "</td>";
                        echo "<td class='hover:bg-gray-50 letter-spacing: 5px;' data-label='Date' > " . $row['payment_method'] . "</td>";
                        echo "<td class='text-ellipsis overflow-hidden max-w-16 hover:bg-gray-50' data-label='Date' >" . $row['payment_details'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Date' >" . $row['payment_status'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Date' >" . $row['total_price'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Date' >" . $row['shipping_cost'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Date' >" . $row['brand'] . " " . $row['model'] . "<br>" . $row['variation_value'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Quantity' >" . $row['product_quantity'] . "</td>";
                        echo "<td class='hover:bg-gray-50' data-label='Action'  class='order-actions'>
                        <a class='text-purple-500 hover:text-purple-700' href='shipping_label.php?order_id=" . $row['order_id'] . "'>Lable</a>
                        <a class='text-blue-500 hover:text-blue-700' href='edit_order.php?order_id=" . $row['order_id'] . "'>Edit</a>
                        <br>
                        <a href='generate_invoice.php?order_id=" . $row['order_id'] . "'>Invoices</a>
                        </td>";
                        echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='17'><h3>No Orders Found</h3></td></tr>";
                    }
            
                    ?>

                    </tbody>
                </table>
                <?php if ($total_results > $results_per_page): ?>
                <nav aria-label="Page navigation example">
            <ul class="inline-flex -space-x-px text-sm">
            <?php
                    $total_pages = ceil($total_results / $results_per_page);
                    for ($i = 1; $i <= $total_pages; $i++) {
                        // echo "<a href='?page=$i&search=$searchQuery'>$i</a>";
                        echo "<li>
            <a href='?page=$i&search=$searchQuery' aria-current='page' class='flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white'>$i</a>
            </li>";
                    }
                    ?>
                  
                </ul>
              </nav>
                  <?php endif; ?>

  

                  
                      <div class="heell " style="margin-bottom:26px"></div>
    </div>
</body>
</html>