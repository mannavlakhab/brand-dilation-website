<?php
session_start();
require 'db_connection.php';

include '../config.php';
// Fetch undelivered orders with customer and address information
$query = "
    SELECT 
        o.order_id, 
        c.first_name, 
        c.last_name, 
        c.phone_number, 
        a.address_line_1,
        a.address_line_2,
        a.city,
        a.country,
        a.postal_code,
        a.state
    FROM 
        orders o
    JOIN 
        customers c ON o.customer_id = c.customer_id
    JOIN 
        addresses a ON a.address_id = c.address
    WHERE 
        o.order_status IN ('Processing', 'pending', 'exchanage under process')
    ORDER BY 
        a.country, a.state, a.postal_code
";
$result = $db->query($query);

if ($result) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Handle error
    echo "Error: " . $db->error;
}



// Fetch delivery partners
$partner_query = "SELECT id, firstname, lastname FROM admin_users WHERE role = 'delivery'";
$partner_result = $db->query($partner_query);
$delivery_partners = $partner_result->fetch_all(MYSQLI_ASSOC);
// Process form submission for task assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_assignments = $_POST['delivery_partner'];

    foreach ($delivery_assignments as $order_id => $partner_id) {
        if ($partner_id) { // Check if a partner was selected
            // Check if order_id exists in exchanage_requests table
            $checkStmt = $db->prepare("SELECT COUNT(*) FROM exchanage_requests WHERE order_id = ?");
            $checkStmt->bind_param("i", $order_id);
            $checkStmt->execute();
            $checkStmt->bind_result($exists);
            $checkStmt->fetch();
            $checkStmt->close();
    
            // Update orders table based on the existence of order_id in exchanage_requests
            if ($exists > 0) {
                $stmt = $db->prepare("UPDATE orders SET dp = ?, order_status = 'pickup and delivering' WHERE DATE(dp_date) = CURDATE() AND order_id = ?");
                $stmt1 = $db->prepare("UPDATE exchanage_requests SET status = 'SHIPPING & PICKUP' WHERE  order_id = ?");
                
                // Bind and execute for exchanage_requests
                $stmt1->bind_param("i", $order_id);
                $stmt1->execute();
                $stmt1->close();
            } else {
                $stmt = $db->prepare("UPDATE orders SET dp = ?, order_status = 'shipped' WHERE order_id = ?");
            }
            
            // Bind and execute for orders
            $stmt->bind_param("ii", $partner_id, $order_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Redirect back to the DSP dashboard
    header("Location: ?assignment=success");
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DSP Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function toggleSelection(className, checked) {
            const checkboxes = document.querySelectorAll('.' + className);
            checkboxes.forEach(checkbox => checkbox.checked = checked);
        }

        function setDeliveryPartner(className, partnerId) {
            const selects = document.querySelectorAll('.' + className + '-partner');
            selects.forEach(select => select.value = partnerId);
        }
    </script>
</head>
<body class="font-sans bg-gray-100">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold my-6 text-center text-gray-800">DSP Dashboard</h1>

    <?php if (isset($_GET['assignment']) && $_GET['assignment'] === 'success'): ?>
        <p class="text-green-600 text-center">Tasks have been successfully assigned to delivery partners.</p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="overflow-x-auto shadow-md rounded-lg">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-2 px-4 border">Order ID</th>
                        <th class="py-2 px-4 border">Customer Name</th>
                        <th class="py-2 px-4 border">Phone Number</th>
                        <th class="py-2 px-4 border">Address</th>
                        <th class="py-2 px-4 border">Pincode</th>
                        <th class="py-2 px-4 border">Assign Delivery Partner</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current_country = $current_state = $current_postal_code = null;
                    foreach ($orders as $order):
                        // Check if we need to display a new section header for country, state, or postal code
                        if ($current_country !== $order['country']) {
                            $current_country = $order['country'];
                            echo "<tr><td colspan='6' class='py-2 px-4 border'><input type='checkbox' onchange=\"toggleSelection('country-$current_country', this.checked)\"> <strong>Country: {$current_country}</strong> - Assign Partner: ";
                            echo "<select onchange=\"setDeliveryPartner('country-$current_country', this.value)\" class='ml-2 border rounded'><option value=''>Select Partner</option>";
                            foreach ($delivery_partners as $partner) {
                                echo "<option value='{$partner['id']}'>{$partner['firstname']} {$partner['lastname']}</option>";
                            }
                            echo "</select></td></tr>";
                            $current_state = null; // Reset state and postal code whenever country changes
                            $current_postal_code = null;
                        }
                        if ($current_state !== $order['state']) {
                            $current_state = $order['state'];
                            echo "<tr><td colspan='6' class='py-2 px-4 border'><input type='checkbox' onchange=\"toggleSelection('state-{$current_country}-{$current_state}', this.checked)\"> <strong>State: {$current_state}</strong> - Assign Partner: ";
                            echo "<select onchange=\"setDeliveryPartner('state-{$current_country}-{$current_state}', this.value)\" class='ml-2 border rounded'><option value=''>Select Partner</option>";
                            foreach ($delivery_partners as $partner) {
                                echo "<option value='{$partner['id']}'>{$partner['firstname']} {$partner['lastname']}</option>";
                            }
                            echo "</select></td></tr>";
                            $current_postal_code = null;
                        }
                        if ($current_postal_code !== $order['postal_code']) {
                            $current_postal_code = $order['postal_code'];
                            echo "<tr><td colspan='6' class='py-2 px-4 border'><input type='checkbox' onchange=\"toggleSelection('postal-{$current_country}-{$current_state}-{$current_postal_code}', this.checked)\"> <strong>Postal Code: {$current_postal_code}</strong> - Assign Partner: ";
                            echo "<select onchange=\"setDeliveryPartner('postal-{$current_country}-{$current_state}-{$current_postal_code}', this.value)\" class=' ml-2 border rounded'><option value=''>Select Partner</option>";
                            foreach ($delivery_partners as $partner) {
                                echo "<option value='{$partner['id']}'>{$partner['firstname']} {$partner['lastname']}</option>";
                            }
                            echo "</select></td></tr>";
                        }
                    ?>
                        <tr>
                            <td class="py-2 px-4 border">
                                <input type="checkbox" name="selected_orders[]" class="country-<?= $current_country ?> state-<?= $current_country ?>-<?= $current_state ?> postal-<?= $current_country ?>-<?= $current_state ?>-<?= $current_postal_code ?>" value="<?= $order['order_id'] ?>">
                                <?= htmlspecialchars($order['order_id']) ?>
                            </td>
                            <td class="py-2 px-4 border"><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                            <td class="py-2 px-4 border"><?= htmlspecialchars($order['phone_number']) ?></td>
                            <td class="py-2 px-4 border"><?= htmlspecialchars($order['address_line_1'] . ' ' . $order['address_line_2'] . ', ' . $order['city'] . ', ' . $order['state']) ?></td>
                            <td class="py-2 px-4 border"><?= htmlspecialchars($order['postal_code']) ?></td>
                            <td class="py-2 px-4 border">
                                <select name="delivery_partner[<?= $order['order_id'] ?>]" class="country-<?= $current_country ?>-partner state-<?= $current_country ?>-<?= $current_state ?>-partner postal-<?= $current_country ?>-<?= $current_state ?>-<?= $current_postal_code ?>-partner border rounded">
                                    <option value="">Select Delivery Partner</option>
                                    <?php foreach ($delivery_partners as $partner): ?>
                                        <option value="<?= $partner['id'] ?>"><?= htmlspecialchars($partner['firstname'] . ' ' . $partner['lastname']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <button  type="submit"
      class="relative mt-5 inline-block p-px font-semibold leading-6 text-white bg-gray-800 shadow-2xl cursor-pointer rounded-xl shadow-zinc-900 transition-transform duration-300 ease-in-out hover:scale-105 active:scale-95"
    >
      <span
        class="absolute inset-0 rounded-xl bg-gradient-to-r from-teal-400 via-blue-500 to-purple-500 p-[2px] opacity-0 transition-opacity duration-500 group-hover:opacity-100"
      ></span>

      <span class="relative z-10 block px-6 py-3 rounded-xl bg-gray-950">
        <div class="relative z-10 flex items-center space-x-2">
          <span class="transition-all duration-500 group-hover:translate-x-1"
            >Assign Task</span
          >
          <svg
            class="w-6 h-6 transition-transform duration-500 group-hover:translate-x-1"
            data-slot="icon"
            aria-hidden="true"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              clip-rule="evenodd"
              d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z"
              fill-rule="evenodd"
            ></path>
          </svg>
        </div>
      </span>
    </button>

   </form>
</div>

</body>
</html>