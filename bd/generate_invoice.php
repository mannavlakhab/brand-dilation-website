<?php
require_once("../db_connect.php");
if (!isset($_GET['order_id'])) {
    die("Order ID is missing.");
}

$order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

// Generate the PDF invoice
require 'dinvoice.php'; // This will generate the PDF using DOMPDF

// Ensure that the PDF was created before proceeding
$pdf_file_path = '../invoices/invoice_' . $order_id . '.pdf';
if (!file_exists($pdf_file_path)) {
    die("Invoice PDF could not be generated.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice for Order <?php echo $order_id; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style></style>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<?php
$sql = "SELECT o.order_id, o.customer_id, o.order_status, o.shipping_address, o.total_price, 
        o.shipping_cost, o.payment_method, o.payment_status, o.payment_details, 
        o.tracking_id, o.order_date, c.first_name, c.last_name, c.email, c.phone_number, 
        c.address, v.variation_name, v.variation_value, v.price_modifier, p.product_id, 
        p.brand, p.model, p.price, oi.product_quantity, oi.variation_id
    FROM Orders o
    JOIN Customers c ON o.customer_id = c.customer_id
    LEFT JOIN Order_Items oi ON o.order_id = oi.order_id
    LEFT JOIN Products p ON oi.product_id = p.product_id
    LEFT JOIN productvariations v ON oi.variation_id = v.variation_id
    WHERE o.order_id = '$order_id'";

$result = $conn->query($sql);

if (!$result) {
    die("Error in query: " . $conn->error);
}

if ($result->num_rows > 0) {
    // Fetch the first row to display order info
    $order = $result->fetch_assoc();
?>

<!-- component -->
<section class="bg-gray-100 py-20">
    <div class="max-w-2xl mx-auto py-0 md:py-16">
        <article class="shadow-none md:shadow-md md:rounded-md overflow-hidden">
            <div class="md:rounded-b-md bg-white">
                <div class="p-9 border-b border-gray-200">
                    <div class="space-y-6">
                        <div class="flex justify-between items-top">
                            <div class="space-y-4">
                                <div>
                                    <img class="h-14 object-cover mb-4" src="../assets/logo.png">
                                    <p class="font-bold text-lg">Invoice</p>
                                </div>
                                <div id="__next" bis_skin_checked="1">
        <div style="opacity: 0.5;" class="absolute z-0 top-0 inset-x-0 flex justify-center overflow-hidden pointer-events-none"
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
     
                                <div>
                                    <p class="font-medium text-sm text-gray-400">Billed To</p>
                                    <p><?php echo $order['first_name'] . " " . $order['last_name']; ?></p>
                                    <p><?php echo $order['email']; ?></p>
                                    <p><?php echo $order['phone_number']; ?></p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div>
                                    <p class="font-medium text-sm text-gray-400">Invoice Number</p>
                                    <p><?php echo $order['order_id']; ?></p>
                                </div>
                                <div>
                                    <p class="font-medium text-sm text-gray-400">Invoice Date</p>
                                    <p><?php echo $order['order_date']; ?></p>
                                </div>
                                <div>
                                    <p class="font-medium text-sm text-gray-400">ABN</p>
                                    <p><?php echo $order['tracking_id']; ?></p>
                                </div>
                                <div>
                                    <a href="../../invoices/invoice_<?php echo $order['order_id']; ?>.pdf" target="_blank"
                                       class="inline-flex items-center text-sm font-medium text-blue-500 hover:opacity-75">
                                        Download PDF
                                        <svg class="ml-0.5 h-4 w-4 fill-current"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             aria-hidden="true">
                                            <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
                                            <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
                                        </svg>
                                    </a>
                                </div>
                                <div>
                                    <a href="send_invoice.php?order_id=<?php echo $order['order_id']; ?>"
                                       target="_blank"
                                       class="inline-flex items-center text-sm font-medium text-blue-500 hover:opacity-75">
                                        Send Invoice
                                        <svg class="ml-0.5 h-4 w-4 fill-current"
                                             xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                             aria-hidden="true">
                                            <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z"></path>
                                            <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-9 border-b border-gray-200">
                    <p class="font-medium text-sm text-gray-400">Note</p>
                    <p class="text-sm">Thank you for your order.</p>
                </div>

                <table class="w-full divide-y divide-gray-200 text-sm">
    <thead>
        <tr>
            <th scope="col" class="px-9 py-4 text-left font-semibold text-gray-400">Item</th>
            <th scope="col" class="py-3 text-left font-semibold text-gray-400"></th>
            <th scope="col" class="py-3 text-left font-semibold text-gray-400">Qty</th>
            <th scope="col" class="py-3 text-left font-semibold text-gray-400"></th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        <?php
        // Initialize a variable to store the final order details
        $final_order = null;

        // Loop through all products associated with the order
        do {
            // Store the last fetched row to use after the loop
            $final_order = $order;
            ?>
            <tr>
                <td class="px-9 py-5 whitespace-nowrap space-x-1 flex items-center">
                    <div>
                        <p><?php echo $order['brand'] . " " . $order['model']; ?></p>
                        <p class="text-sm text-gray-400">
                            <?php echo $order['variation_name'] . " " . $order['variation_value'] . " ₹" . $order['price_modifier']; ?>
                        </p>
                    </div>
                </td>
                <td class="whitespace-nowrap text-gray-600 truncate"></td>
                <td class="whitespace-nowrap text-gray-600 truncate">
                    <?php echo $order['product_quantity']; ?>
                </td>
            </tr>
        <?php
        } while ($order = $result->fetch_assoc());
        ?>
    </tbody>
</table>

<div class="p-9 border-b border-gray-200">
    <div class="space-y-3">
        <div class="flex justify-between">
            <div>
                <p class="text-gray-500 text-sm">Payment</p>
            </div>
            <p class="text-gray-500 text-sm"><?php echo $final_order['payment_status']; ?></p>
        </div>
        <div class="flex justify-between">
            <div>
                <p class="text-gray-500 text-sm">Using</p>
            </div>
            <p class="text-gray-500 text-sm"><?php echo $final_order['payment_method']; ?></p>
        </div>
        <div class="flex justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total</p>
            </div>
            <p class="text-gray-500 text-sm"><?php echo $final_order['total_price']; ?></p>
        </div>
    </div>
</div>

<div class="p-9 border-b border-gray-200">
    <div class="space-y-3">
        <div class="flex justify-between">
            <div>
                <p class="font-bold text-black text-lg">Total Amount</p>
            </div>
            <p class="font-bold text-black text-lg">₹<?php echo number_format($final_order['total_price'], 2); ?></p>
        </div>
    </div>
</div>

            </div>
        </article>
    </div>
</section>

<?php
} else {
    echo "<h3>Order not found.</h3>";
}
$conn->close();
?>

<div class="button-container">
    <button onclick="window.print();">Print Invoice</button>
</div>
</body>
</html>
