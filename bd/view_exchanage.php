<?php
session_start();
require_once '../db_connect.php';

// Ensure the user is an admin
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all exchange requests, ordered by newest first
$query = "SELECT er.*, O.order_date, C.email FROM exchanage_requests er
          JOIN Orders O ON er.order_id = O.order_id
          JOIN Customers C ON er.customer_id = C.customer_id
          ORDER BY er.id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../bd/favicon.ico" type="image/x-icon">
    <title>Exchange dashboard</title>
</head>
<body>
    
</body>
</html>

<div class="flex">
    <?php include 'hsidebar.php'; ?>
    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold text-center mb-6">Exchange Requests</h1>
        <table class="min-w-full bg-white border border-gray-200 shadow-md rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-200 text-gray-800 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Request ID</th>
                    <th class="py-3 px-6 text-left">Order ID</th>
                    <th class="py-3 px-6 text-left">Customer Email</th>
                    <th class="py-3 px-6 text-left">Exchange Reason</th>
                    <th class="py-3 px-6 text-left">Exchange Amount</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Supporting Files</th>
                    <th class="py-3 px-6 text-left">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-800 text-sm font-light">
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <?php 
                    $supporting_file_link = '';
                    if (!empty($row['supporting_files'])) {
                        $supporting_file_link = '<a href="javascript:void(0);" onclick="showImage(\'../' . $row['supporting_files'] . '\')" class="text-lime-700 font-bold hover:underline">View Image</a>';
                    }

                    // Set row style based on OTP verification and order completion
                    $row_style = '';
                    if ($row['otp_verified'] == 0) {
                        $row_style = 'bg-red-300';
                    } elseif ($row['status'] == 'complete') {
                        $row_style = 'bg-green-500 text-white';
                    }
                    ?>
                    <tr class="<?php echo $row_style; ?>">
                        <td class="py-3 px-6 border-b border-gray-200"><?php echo $row['id']; ?></td>
                        <td class="py-3 px-6 border-b border-gray-200"><?php echo $row['order_id']; ?></td>
                        <td class="py-3 px-6 border-b border-gray-200"><?php echo $row['email']; ?></td>
                        <td class="py-3 px-6 border-b border-gray-200"><?php echo htmlspecialchars($row['exchanage_reason']); ?></td>
                        <td class="py-3 px-6 border-b border-gray-200"><?php echo $row['exchanage_amount']; ?></td>
                        <td class="py-3 px-6 border-b border-gray-200"><?php echo $row['status']; ?></td>
                        <td class="py-3 px-6 border-b border-gray-200"><?php echo $supporting_file_link; ?></td>
                        <td class="py-3 px-6 border-b border-gray-200">
                            <a href="admin_exchanage_action.php?id=<?php echo $row['id']; ?>&action=approve&order_id=<?php echo $row['order_id']; ?>" class="text-blue-900 hover:underline">Approve</a> <br>
                            <a href="admin_exchanage_action.php?id=<?php echo $row['id']; ?>&action=reject&order_id=<?php echo $row['order_id']; ?>" class="text-blue-900 hover:underline">Reject</a> <br>
                            <a href="admin_exchanage_action.php?id=<?php echo $row['id']; ?>&action=pending&order_id=<?php echo $row['order_id']; ?>" class="text-blue-900 hover:underline">Pending</a> <br>
                            <a href="admin_exchanage_action.php?id=<?php echo $row['id']; ?>&action= onhold&order_id=<?php echo $row['order_id']; ?>" class="text-blue-900 hover:underline">On Hold</a> <br>
                            <a href="admin_exchanage_action.php?id=<?php echo $row['id']; ?>&action=SP&order_id=<?php echo $row['order_id']; ?>" class="text-blue-900 hover:underline">Shipping & Pickup</a> <br>
                            <a href="admin_exchanage_action.php?id=<?php echo $row['id']; ?>&action=complete&order_id=<?php echo $row['order_id']; ?>" class="text-blue-900 hover:underline">Complete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $conn->close(); ?>

<!-- Modal for displaying image -->
<div id="imageModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden">
    <span id="closeModal" class="absolute top-4 right-4 text-white text-3xl cursor-pointer">&times;</span>
    <img id="modalImage" src="" class="max-w-full max-h-full rounded-lg">
</div>

<script>
function showImage(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.remove('hidden');
}

document.getElementById('closeModal').onclick = function() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Close the modal when clicking outside of the image
window.onclick = function(event) {
    var modal = document.getElementById('imageModal');
    if (event.target == modal) {
        modal.classList.add('hidden');
    }
}
</script> 