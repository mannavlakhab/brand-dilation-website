<?php
require '../vendor/autoload.php';
header('Content-Type: text/html; charset=UTF-8');
use Dompdf\Dompdf;

// Start the output buffering
ob_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice for Order <?php echo $order_id; ?></title>

    <style>
  @font-face {
  font-family: SourceSansPro;
  src: url(SourceSansPro-Regular.ttf);
}

.clearfix:after {
  content: "";
  display: table;
  clear: both;
}

a {
  color: #3c0e40;
  text-decoration: none;
}

body {
  position: relative;
  /* width: 21cm;   */
  height: 29.7cm; 
  margin: 0 auto; 
  color: #555555;
  background: #FFFFFF; 
  font-family: Arial, sans-serif; 
  font-size: 14px; 
  font-family: SourceSansPro;
}

header {
  padding: 10px 0;
  margin-bottom: 20px;
  border-bottom: 1px solid #AAAAAA;
}

#logo {
  float: left;
  margin-top: 8px;
}

#logo img {
  height: 70px;
}

#company {
  float: right;
  text-align: right;
}


#details {
  margin-bottom: 50px;
}

#client {
  padding-left: 6px;
  border-left: 6px solid #3c0e40;
  float: left;
}

#client .to {
  color: #777777;
}

h2.name {
  font-size: 1.4em;
  font-weight: normal;
  margin: 0;
}

#invoice {
  float: right;
  text-align: right;
}

#invoice h1 {
  color: #3c0e40;
  font-size: 2.4em;
  line-height: 1em;
  font-weight: normal;
  margin: 0  0 10px 0;
}

#invoice .date {
  font-size: 1.1em;
  color: #777777;
}

table {
  width: 100%;
  border-collapse: collapse;
  border-spacing: 0;
  margin-bottom: 20px;
}

table th,
table td {
  padding: 20px;
  background: #EEEEEE;
  text-align: center;
  border-bottom: 1px solid #FFFFFF;
}

table th {
  white-space: nowrap;        
  font-weight: normal;
}

table td {
  text-align: right;
}

table td h3{
  color: #db35a9;
  font-size: 1.2em;
  font-weight: normal;
  margin: 0 0 0.2em 0;
}

table .no {
  color: #FFFFFF;
  font-size: 1.6em;
  background: #4b6b00;
}

table .desc {
  text-align: left;
}

table .qty {
}

table .total {
  background: #4b6b00;
  color: #FFFFFF;
}

table td.qty,
table td.total {
  font-size: 1.2em;
}

table tbody tr:last-child td {
  border: none;
}

table tfoot td {
  padding: 10px 20px;
  background: #FFFFFF;
  border-bottom: none;
  font-size: 1.2em;
  white-space: nowrap; 
  border-top: 1px solid #AAAAAA; 
}

table tfoot tr:first-child td {
  border-top: none; 
}

table tfoot tr:last-child td {
  color: #4b6b00;
  font-size: 1.4em;
  border-top: 1px solid #4b6b00; 

}

table tfoot tr td:first-child {
  border: none;
}

#thanks{
  font-size: 2em;
  margin-bottom: 50px;
}

#notices{
  padding-left: 6px;
  border-left: 6px solid #3c0e40;  
}

#notices .notice {
  font-size: 1.2em;
}

footer {
  color: #777777;
  width: 100%;
  height: 30px;
  position: absolute;
  bottom: 0;
  border-top: 1px solid #AAAAAA;
  padding: 8px 0;
  text-align: center;
}


  </style>
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
            $order = $result->fetch_assoc();
        ?>
   <header class="clearfix">
      <div id="logo" bis_skin_checked="1">
      <img src="<?php echo 'data:image/png;base64,' . base64_encode(file_get_contents('../bd/ass/logo.png')); ?>" alt="BD">
      </div>
      <div id="company" bis_skin_checked="1">
        <h2 class="name">Brand Dilation</h2>
        <div bis_skin_checked="1">+19 289 853 374</div>
        <div bis_skin_checked="1"><a href="mailto:company@example.com">invoice@branddilation.com</a></div>
      </div>
      
    </header>
    <main>
      <div id="details" class="clearfix" bis_skin_checked="1">
        <div id="client" bis_skin_checked="1">
          <div class="to" bis_skin_checked="1">INVOICE TO:</div>
          <p> <?php echo $order['first_name'] . " " . $order['last_name']; ?> </p>
          <p> <?php echo $order['email']; ?> </p>
          <p><?php echo $order['phone_number']; ?> </p>
        </div>
        <div id="invoice" bis_skin_checked="1">
          <h1>INVOICE <?php echo $order['order_id']; ?></h1>
          <div class="date" bis_skin_checked="1">Date of Invoice: <?php echo $order['order_date']; ?></div>
          <div class="date" bis_skin_checked="1">Payment Method: <?php echo $order['payment_method']; ?></div>
        </div>
      </div>
      <table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="no">#</th>
            <th class="desc">DESCRIPTION</th>
            <th class="qty">QUANTITY</th>
          </tr>
        </thead>
        <tbody>
        <?php
        // Initialize a variable to store the final order details
        $final_order = null;

        // Loop through all products associated with the order
        do {
            // Store the last fetched row to use after the loop
            $final_order = $order;
            ?>
          <tr>
            <td class="no">01</td>
            <td class="desc"><h3><?php echo $order['brand'] . " " . $order['model']; ?></h3><?php echo $order['variation_name'] . " " . $order['variation_value'] . " " . $order['price_modifier']; ?>/- </td>
             <td class="qty"><?php echo $order['product_quantity']; ?></td>
          </tr>
          <?php
        } while ($order = $result->fetch_assoc());
        ?>
        </tbody>
        <tfoot>
         
          <tr>
            <td colspan=""></td>
            <td colspan="">GRAND TOTAL</td>
            <td><?php echo number_format($final_order['total_price'], 2); ?></td>
          </tr>
        </tfoot>
      </table>
      <div id="thanks" bis_skin_checked="1">Thank you!</div>
      <div id="notices" bis_skin_checked="1">
        <div bis_skin_checked="1">Note:</div>
        <div class="notice" bis_skin_checked="1">Thank Your for purchesing with Us. Your Payment of <?php echo $final_order['total_price']; ?> is <?php echo $final_order['payment_status']; ?></div>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
<?php
        } else {
            echo "<h3>Order not found.</h3>";
        }
        ?>
</body>
</html>

<?php
// Capture the generated HTML
$html = ob_get_clean();

// Initialize DOMPDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Save the generated PDF to a file
$pdf_file_path = '../invoices/invoice_' . $order_id . '.pdf';
file_put_contents($pdf_file_path, $dompdf->output());

// Output the generated PDF to Browser
// $dompdf->stream("invoice_$order_id.pdf", ["Attachment" => false]);

// Optionally, return the path of the PDF file for email attachment
return $pdf_file_path;
?>