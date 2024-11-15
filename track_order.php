<?php
session_start();
require_once 'db_connect.php'; // Connect to your database

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : '';

$order_details = null;
$error = '';

if (isset($_POST['track_order'])) {
    $tracking_id = $_POST['tracking_id'];

    $tracking_query = "SELECT * FROM orders WHERE tracking_id = ?";
    $tracking_stmt = $conn->prepare($tracking_query);
    $tracking_stmt->bind_param("s", $tracking_id);
    $tracking_stmt->execute();
    $result = $tracking_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order_details = $result->fetch_assoc();
    } else {
        $error = "  <div class='ab-o-oa' aria-hidden='true'>
                    <div class='ZAnhre'>
                    <img class='wF0Mmb' src='../assets/track.svg' width='300px' height='300px' alt=''></div>
                    <div class='ab-o-oa-r'><div class='ab-o-oa-qc-V'>Tracking Id was wrong either invalid </div>
                    <div class='ab-o-oa-qc-r'>Please check & try agin</div></div>
                </div>
                <style>
                    
.ab-o-oa{
    display: flex;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    align-items: center;
    width: 100%;
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
    font-family: Montserrat, sans-serif;

}
.ab-o-oa-r{
    display: contents;
}
.ab-o-oa-qc-V{
    font-weight :800;

}
.ab-o-oa-qc-r{
    font-weight :normal;

}




                </style>";
    }

    $tracking_stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order</title>
    <link rel="stylesheet" href="../assets/css/track.css">
    <link rel="stylesheet" href="../assets/css/btn.css">
    <style>
        @font-face { font-family: Arial !important; font-display: swap !important; }
      
    </style>
         <!--=============== file loader ===============-->
    <!--=============== header ===============-->
    <script src="../assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <script>
        $(function () {
            $('#header').load('../pages/header.php');

        });
    </script>
    <!--=============== footer ===============-->
    <script>
        $(function () {
            $('#footer').load('../pages/footer.php');

        });
    </script>

    <!--=============== closing file loader ===============-->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<header class="header" id="header">
    <div id="header"></div>
    </header> 
<div class="container px-1 px-md-4 py-5 mx-auto">

    <div class="card">
        <br>
<button class="btn-trick-new" onclick="history.back()">Go Back</button>
<br><br>
        <form method="post" action="track_order.php">
            <div class="form-group">
                <label for="tracking_id">Tracking ID:</label>
                <input type="text" id="tracking_id" value="<?php echo htmlspecialchars($tracking_id); ?>" name="tracking_id" required
                style="width: max-content;">  <button class="btn-trick-new" type="submit" name="track_order">Track Order</button>
            </div>
          
        </form>

        <?php if ($error): ?>
            <div class="error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($order_details): ?>
            <div class="row d-flex justify-content-between px-3 top">
                <div class="d-flex">
                    <h5>ORDER <span class="text-primary font-weight-bold"><?php echo htmlspecialchars($order_details['order_id']); ?></span></h5>
                </div>
                <div class="d-flex flex-column text-sm-right">
                <p class="mb-0">Expected Arrival : <span><?php echo htmlspecialchars(date('F j, Y', strtotime($order_details['delivery_date']))); ?></span></p>

                    <p>Statuse <span class="font-weight-bold"><?php echo htmlspecialchars($order_details['order_status']); ?></span></p>
                </div>
            </div>

            <?php
            // Function to generate progress bar based on order status
            function generateProgressBar($order_status) {
                $steps = [
                    'pending' => 1,
                    'processing' => 2,
                    'shipped' => 3,
                    'delivered' => 4,
                    'canceled' => 4,
                    'awaiting-payment' => 1,
                    'refunded' => 4,
                    'failed' => 1,
                    'expired' => 1,
                    'disputed' => 1,
                    'manual-verification-required' => 1,
                    'onhold' => 1,
                    'in-transit' => 3,
                    'returned-to-sender' => 1,
                    'exchanged' => 4,
                    'delayed' => 1,
                    'lost' => 1,
                    'incorrect' => 1,
                    'damaged' => 1
                ];

                $activeSteps = isset($steps[$order_status]) ? $steps[$order_status] : 0;

                $progressBarClass = '';
                if ($order_status == 'canceled') {
                    $progressBarClass = 'canceled';
                } elseif ($order_status == 'delivered') {
                    $progressBarClass = 'delivered';
                }

                echo '<div class="row d-flex justify-content-center">';
                echo '<div class="col-12">';
                echo '<ul id="progressbar" class="text-center ' . $progressBarClass . '">';

                for ($i = 0; $i < 4; $i++) {
                    if ($i < $activeSteps) {
                        echo '<li class="active step0"></li>';
                    } else {
                        echo '<li class="step0"></li>';
                    }
                }

                echo '</ul>';
                echo '</div>';
                echo '</div>';
            }

            // Generate progress bar
            generateProgressBar($order_details['order_status']);
            ?>

            <div class="row justify-content-between top">
                <div class="row d-flex icon-content">
                    <img class="icon" src="https://i.imgur.com/9nnc9Et.png">
                    <div class="d-flex flex-column">
                        <p class="font-weight-bold">Order<br>Processed</p>
                    </div>
                </div>
                <div class="row d-flex icon-content">
                    <img class="icon" src="https://i.imgur.com/u1AzR7w.png">
                    <div class="d-flex flex-column">
                        <p class="font-weight-bold">Order<br>Shipped</p>
                    </div>
                </div>
                <div class="row d-flex icon-content">
                    <img class="icon" src="https://i.imgur.com/TkPm63y.png">
                    <div class="d-flex flex-column">
                        <p class="font-weight-bold">Order<br>En Route</p>
                    </div>
                </div>
                <div class="row d-flex icon-content">
                    <img class="icon" src="https://i.imgur.com/HdsziHP.png">
                    <div class="d-flex flex-column">
                        <p class="font-weight-bold">Order<br>Arrived</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- <div class="new_div_details">
        <hr>
            <h3>Order Details:~</h3>
            <p>order_id : <?php echo htmlspecialchars($order_id); ?>.</p>
            <p>order_status : <?php echo htmlspecialchars($order_status); ?>.</p>
            <p>shipping_address : <?php echo htmlspecialchars($shipping_address); ?>.</p>
            <p>total_price : <?php echo htmlspecialchars($total_price); ?>.</p>
            <p>payment_method : <?php echo htmlspecialchars($payment_method); ?>.</p>
            <p>payment_status : <?php echo htmlspecialchars($payment_status); ?>.</p>
            <p>order_date : <?php echo htmlspecialchars($order_date); ?>.</p>
            <p>delivery_date : <?php echo htmlspecialchars($delivery_date); ?>.</p>
            </div> -->
    </div>
</div>
    <!--=============== Footer ===============-->
    <div id="footer"></div>


</body>
</html>
