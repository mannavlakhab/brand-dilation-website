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

$conn->close(); // Close connection after fetching data

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/font.css">
    <link rel="prelaod" href="https://tailwindcss.com/_next/static/css/80b2c87c0b9a5af9.css">
    <link rel="stylesheet" href="./assets/font.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Orders View Page</title>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <!-- <link rel="stylesheet" href="dash.css">
  <link rel="stylesheet" href="path_to_your_css_file.css"> -->
    <style>
    table {
        border: 1px solid #ccc;
        border-collapse: collapse;
        margin: 0;
        padding: 0;
        width: 100%;
        table-layout: fixed;
        overflow: hidden;

    }

    table caption {
        font-size: 1.5em;
        margin: .5em 0 .75em;
    }

    table tr {
     
        border: 1px solid #ddd;
        padding: .35em;
    }

    table th,
    table td {
        padding: .625em;
        text-align: center;
    }

    table td {
        word-wrap: break-word;
    }

    table th {
        font-size: .85em;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    @media screen and (max-width: 600px) {
        table {
            border: 0;
            padding: 3%;
        }

        table caption {
            font-size: 1.3em;
        }

        table thead {
            border: none;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
        }

        table tr {
            border-bottom: 3px solid #ddd;
            display: block;
            margin-bottom: .625em;
        }

        table td {
            border-bottom: 1px solid #ddd;
            display: block;
            font-size: .8em;
            text-align: right;
            word-wrap: break-word;
        }

        table td::before {
            /*
          * aria-label has no advantage, it won't be read inside a table
          content: attr(aria-label);
          */
            content: attr(data-label);
            float: left;
            font-weight: bold;
            text-transform: uppercase;
        }

        table td:last-child {
            border-bottom: 0;
        }
    }



    table a:link,
    a:visited {
        appearance: none;
        background-color: #FAFBFC;
        border: 1px solid rgba(27, 31, 35, 0.15);
        border-radius: 6px;
        box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
        box-sizing: border-box;
        color: #24292E;
        cursor: pointer;
        display: inline-block;
        font-size: 14px;
        font-weight: 500;
        line-height: 20px;
        list-style: none;
        padding: 6px 16px;
        position: relative;
        transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        vertical-align: middle;
        text-decoration: none;
        white-space: nowrap;
        word-wrap: break-word;
    }

    table a:hover,
    a:active {
        background-color: #F3F4F6;
        text-decoration: none;
        transition-duration: 0.1s;
    }

    .pagination a:link,
    a:visited {
        appearance: none;
        background-color: #FAFBFC;
        border: 1px solid rgba(27, 31, 35, 0.15);
        border-radius: 6px;
        box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
        box-sizing: border-box;
        color: #24292E;
        cursor: pointer;
        display: inline-block;
        font-size: 14px;
        font-weight: 500;
        line-height: 20px;
        list-style: none;
        padding: 6px 16px;
        position: relative;
        transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        vertical-align: middle;
        text-decoration: none;
        white-space: nowrap;
        word-wrap: break-word;
    }

    .pagination a:hover,
    a:active {
        background-color: #F3F4F6;
        text-decoration: none;
        transition-duration: 0.1s;
    }



    /* general styling */
    body {
        font-family: "Open Sans", sans-serif;
        line-height: 1.25;
        font-size: 0.90rem;
        --tw-bg-opacity: 1;
        background-color: rgb(2 6 23 20/var(--tw-bg-opacity));
    }
    </style>

</head>

<body>
    <div id="__next" bis_skin_checked="1">
        <div style="z-index:-100;  opacity: 0.5; right:-10px" class="absolute z-0 top-0 inset-x-0 flex justify-center overflow-hidden pointer-events-none"
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
     
    <form class="z-10 bg-gray" action="" method="GET">
        <div class="flex items-center justify-center p-5">

            <div class="flex">
                <div
                    class="flex w-10 items-center justify-center rounded-tl-lg rounded-bl-lg border-r border-gray-200 bg-gray-100 p-5">
                    <svg viewBox="0 0 20 20" aria-hidden="true"
                        class="pointer-events-none absolute w-5 fill-gray-500 transition">
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
    </div>
    <table>
        <caption>Customers Orders using bd-shop</caption>
        <thead>
            <tr>
                <th scope="col">Order ID</th>
                <th scope="col">Date</th>
                <th scope="col">Order Status</th>
                <th scope="col">Customer Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Shipping Address</th>
                <th scope="col">Total Price</th>
                <th scope="col">Shipping Cost</th>
                <th scope="col">Payment Method</th>
                <th scope="col">Payment Details</th>
                <th scope="col">Payment Status</th>
                <th scope="col">Product Name</th>
                <th scope="col">Quantity</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>


            <!-- PHP code for fetching and displaying data -->
            <?php
           if ($result->num_rows > 0) {
             // Loop through each order
             while ($row = $result->fetch_assoc()) {
               echo "<tr>";
               echo "<td scope='row' data-label='Order ID' >" . $row['order_id'] . "</td>";
               echo "<td data-label='Date' >" . date('F j, Y', strtotime($row['order_date'])) . "</td>";
               echo "<td data-label='Date' >" . $row['order_status'] . "</td>";
               echo "<td data-label='Date' >" . $row['first_name'] . "<br>" . $row['last_name'] . "</td>";
               echo "<td data-label='Email' >" . $row['email'] . "</td>";
               echo "<td data-label='Phone' >" . $row['phone_number'] . "</td>";
               echo "<td data-label='Date' >" . $row['shipping_address'] . "</td>";
               echo "<td data-label='Date' >" . $row['total_price'] . "</td>";
               echo "<td data-label='Date' >" . $row['shipping_cost'] . "</td>";
               echo "<td data-label='Date' >" . $row['payment_method'] . "</td>";
               echo "<td data-label='Date' >" . $row['payment_details'] . "</td>";
               echo "<td data-label='Date' >" . $row['payment_status'] . "</td>";
               echo "<td data-label='Date' >" . $row['brand'] . " " . $row['model'] . "<br>" . $row['variation_value'] . "</td>";
               echo "<td data-label='Quantity' >" . $row['product_quantity'] . "</td>";
               echo "<td data-label='Action'  class='order-actions'><a href='edit_order.php?order_id=" . $row['order_id'] . "'>Edit</a><br><a href='generate_invoice.php?order_id=" . $row['order_id'] . "'>Invoices</a></td>";
               echo "</tr>";
             }
           } else {
             echo "<tr><td colspan='17'><h3>No Orders Found</h3></td></tr>";
           }
 
           ?>

        </tbody>
    </table>

    <!-- Pagination links -->
    <?php if ($total_results > $results_per_page): ?>
    <div class="pagination" bis_skin_checked="1" style="
    display: flex;
    margin-top: 10px;
    gap: 10px;
    justify-content: center;
    align-items: stretch;
">
        <?php
          $total_pages = ceil($total_results / $results_per_page);
          for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?page=$i&search=$searchQuery'>$i</a>";
          }
          ?>
    </div>
    <?php endif; ?>

    </div>
    </div>
    </div>

    <!-- JavaScript for sorting and editable functionality -->
    <script>
    // Add JavaScript for making table cells editable and handling pagination if needed
    // Example: Sorting functionality using JavaScript
    </script>


</body>

</html>