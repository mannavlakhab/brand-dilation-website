<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delivery_assignments = $_POST['delivery_partner'];

    foreach ($delivery_assignments as $order_id => $partner_id) {
        // Update order with delivery partner ID
        $stmt = $db->prepare("UPDATE orders SET dp = ?, delivery_status = 'assigned' WHERE order_id = ?");
        $stmt->bind_param("ii", $partner_id, $order_id);
        $stmt->execute();
    }

    // Redirect back to the DSP dashboard
    header("Location: dsp_dashboard.php?assignment=success");
}
?>
