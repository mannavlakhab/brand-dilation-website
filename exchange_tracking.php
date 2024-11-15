<?php
// exchange_tracking.php

require_once 'db_connect.php';



// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if "Remember Me" cookie is set
    if (isset($_COOKIE['user_id'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      // Optionally, you can re-validate the user with the database here
    } else {
      // If login is optional, do not redirect, just leave session unset
      // If you want to prompt the user to log in, you can show a message or an optional login button
      // Optionally, store the current page in session for redirection after optional login
      $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
      // You can choose to show a notification or a button to log in here
    }
}


$exchange = null;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['exch_track'])) {
    $exch_track = $_GET['exch_track'];

    // Fetch exchange details
    $tracking_query = "SELECT order_id, status, exchanage_date, exchanage_amount, supporting_files FROM exchanage_requests WHERE exch_track = ?";
    $tracking_stmt = $conn->prepare($tracking_query);
    $tracking_stmt->bind_param("s", $exch_track);
    $tracking_stmt->execute();
    $result = $tracking_stmt->get_result();

    if ($result->num_rows > 0) {
        $exchange = $result->fetch_assoc();
    } else {
        $error_message = "No exchange found with this tracking ID.";
    }
} else {
    $error_message = "Tracking ID is required.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>exchange Tracking</title>
    <link rel="stylesheet" href="../assets/css/btn.css">
 
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        body {
            font-family: Montserrat;
            background-color: #f3f3f3;
            padding: 20px;
            margin: 0;
        }

        h3 {
            text-align: center;
        }

        .exchange-tracking {
            width: 90%;
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .search-form {
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form input[type="text"] {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: calc(100% - 130px);
            box-sizing: border-box;
        }

        .search-form input[type="submit"] {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            width: 120px;
            box-sizing: border-box;
        }

        .search-form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .exchange-details {
            text-align: left;
            margin-top: 20px;
        }

        .exchange-details p {
            margin: 10px 0;
        }

        .exchange-amount {
            font-size: 1.5em;
            color: #4caf50;
            text-align: center;
            margin-top: 20px;
        }

        .supporting-files {
            margin-top: 20px;
        }

        .supporting-files a {
            color: #007bff;
            text-decoration: none;
        }

        .supporting-files a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            text-align: center;
        }

        .progress-bar {
            margin-top: 30px;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }

        .progress-bar .step {
            position: relative;
            text-align: center;
            width: 100%;
        }

        .progress-bar .step::before {
            content: '';
            position: absolute;
            top: -20%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #ddd;
            color: #fff;
            line-height: 24px;
            font-size: 16px;
            z-index: 1;
            transition: background-color 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .progress-bar .step.complete::before {
            background-color: #4caf50;
            content: '\e876'; /* Check icon */
            font-family: 'Material Icons';
        }

        .progress-bar .step.rejected::before {
            background-color: #f44336; /* Red color for rejected */
            content: '\e14c'; /* Cancel icon */
            font-family: 'Material Icons';
        }

        .progress-bar .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: -20%;
            left: 100%;
            transform: translateY(-50%);
            width: 100%;
            height: 2px;
            z-index: 0;
            transition: background-color 0.3s ease;
        }
        .progress-bar .step span {
            display: block;
            margin-top: 10px;
        }

        @media (max-width: 600px) {
            .search-form input[type="text"] {
                width: calc(100% - 90px);
            }

            .search-form input[type="submit"] {
                width: 80px;
            }

            .progress-bar {
                flex-direction: column;
            }

            .progress-bar .step {
                width: auto;
                margin-bottom: 20px;
            }

            .progress-bar .step:not(:last-child)::after {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="exchange-tracking">
    <br>
<button class="btn-trick-new" onclick="history.back()">Go Back</button>
<br><br>
        <div class="search-form">
            <form action="exchange_tracking.php" method="GET">
                <input type="text" name="exch_track" placeholder="Enter Tracking ID" required>
                <input type="submit" value="Search">
            </form>
        </div>

        <?php if ($error_message): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($error_message); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($exchange): ?>
            <h3>exchange Details</h3>

            <div class="exchange-details">
                <p>Order ID: <?php echo htmlspecialchars($exchange['order_id']); ?></p>
                <p>Status: <?php echo ucfirst($exchange['status']); ?></p>
                <p>exchange Process Date: <?php echo htmlspecialchars($exchange['exchanage_date']); ?></p>
            </div>
            <div class="supporting-files">
                <p><a href="<?php echo htmlspecialchars($exchange['supporting_files']); ?>">Download Supporting Files</a></p>
            </div>

            <div class="exchange-amount">
                <p>Total Estimated exchange: ₹<?php echo number_format($exchange['exchanage_amount']); ?></p>
            </div>


            <div class="progress-bar">
        <?php
        $steps = ['Request', 'Pending', 'Approved', 'SHIPPING & PICKUP', 'Complete'];
        $status_index = array_search(ucfirst($exchange['status']), $steps);

        foreach ($steps as $index => $step) {
            if (ucfirst($exchange['status']) === 'Rejected' && $index == $status_index) {
                $class = 'step rejected';
            } else {
                $class = ($index <= $status_index) ? 'step complete' : 'step';
            }
            echo "<div class=\"$class\"><span>$step</span></div>";
        }
        ?>
    </div>
        <?php endif; ?>
<br>
        <p>if you have quetion related to exchange & return than read our <a href="../pages/faq.html">Frequently Asked Questions</a> else Contect to our <a href="../pages/help.html">Help Desk</a>, also read our <a href="../pages/toc.html">Terms and Conditions</a>.</p>

    </div>
</body>
</html>
