<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Check if "Remember Me" cookie is set
    if (isset($_COOKIE['user_id'])) {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      // Optionally, you can re-validate the user with the database here
  } else {
    // Store the current page in session to redirect after login
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header('Location: ../login.php');
    exit();
  }}

$user_id = $_SESSION['user_id'];

// Fetch user information
$user_query = $conn->prepare("SELECT username, email, first_name, last_name, phone_number, address_1, address_2 FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_query->bind_result($username, $email, $first_name, $last_name, $phone_number, $address_1, $address_2);
$user_query->fetch();
$user_query->close();

// Fetch user orders
$order_query = $conn->prepare("
  SELECT 
    O.order_status, 
    O.order_id, 
    O.order_date, 
    O.delivery_date, 
    C.phone_number, 
    C.first_name, 
    C.last_name, 
    C.address, 
    C.email, 
    O.shipping_address, 
    O.total_price, 
    O.shipping_cost, 
    O.payment_method, 
    O.payment_details, 
    O.payment_status, 
    O.tracking_id, 
    P.model AS product_name, 
    V.variation_value, 
    OI.product_quantity,
    OI.product_attributes,
    P.product_id,
    P.image_main,
    RR.refund_reason,
    RR.refund_amount,
    RR.ref_track,
    RR.status AS refund_status,
    RR.supporting_files,
    ER.exchanage_reason,
    ER.exchanage_amount,
    ER.exch_track,
    ER.status AS exchanage_status,
    ER.supporting_files,
    R.id,
    R.rating,
    R.review_text,
    a.address_id,
    a.address_line_1,
    a.address_line_2,
    a.city,
    a.state,
    a.postal_code,
    a.country 
FROM 
    Orders O 
JOIN 
    Order_Items OI ON O.order_id = OI.order_id 
JOIN 
    Products P ON OI.product_id = P.product_id 
JOIN 
    ProductVariations V ON OI.variation_id = V.variation_id 
JOIN 
    Customers C ON O.customer_id = C.customer_id 
JOIN
    addresses a ON c.address = a.address_id
LEFT JOIN
    refund_requests RR ON O.order_id = RR.order_id
LEFT JOIN
    exchanage_requests ER ON O.order_id = ER.order_id
LEFT JOIN
    product_reviews R ON OI.product_id = R.product_id AND O.customer_id = R.user_id
WHERE 
    C.user_id = ? 
ORDER BY 
    O.order_id DESC

");
$order_query->bind_param("i", $user_id);
$order_query->execute();
$order_result = $order_query->get_result();
$order_query->close();
?>

<!DOCTYPE html>
<html>

<head>

    <script src="../assets/js/internet-check.js" defer></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile</title>
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/orders_my.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link rel="stylesheet" href="../assets/css/magic-mouse.css">
    
    <link rel="stylesheet" href="../assets/css/btn.css">
    <link rel="stylesheet" href="../assets/tailwind.min.css">
    <script src="../assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>
    <script>
    $(function() {
        $('#header').load('../pages/header.php');
        $('#footer').load('../pages/footer.php');
    });
    </script>
</head>

<body>
    <div class="content">
        <?php
    if (isset($_GET['page']) && $_GET['page'] == 'dashboard') {
        echo "
           <button class='btn-trick-new' onclick='history.back()'>&#8592; Go Back</button>
        <div class='dasg'>
         <h1 class='name'><p>Welcome,</p><wbr> $first_name $last_name<wbr></h1>


           <section class='grid sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-6xl'>


           <a  href='profile.php?page=orders'>
           <div class='relative p-5 bg-gradient-to-r from-purple-400 to-purple-900 rounded-md overflow-hidden'>
                            <div class='relative z-10 mb-4 text-white text-4xl leading-none font-semibold'>Orders</div>
                            <div class='relative z-10 text-purple-200 leading-none font-semibold'>Check Orders</div>
                            <svg class='absolute right-0 bottom-0 h-32 w-32 -mr-8 -mb-8 text-green-600 opacity-50' version='1.2' baseProfile='tiny' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'
	 x='0px' y='0px' viewBox='0 0 173 173' overflow='visible' xml:space='preserve'>
<g>
	<path fill='#FF6666' d='M139,54c-1,0-1.9-0.6-2.3-1.5c-0.5-1.3,0.1-2.7,1.3-3.2l29.9-12.4c1.3-0.5,2.7,0.1,3.3,1.3
		c0.5,1.3-0.1,2.7-1.3,3.2l-29.9,12.4C139.6,53.9,139.3,54,139,54z'/>
	<path fill='#FF6666' d='M110.2,161.7c-1,0-1.9-0.6-2.3-1.5c-0.5-1.3,0.1-2.7,1.3-3.2l57.2-24.2V39.1c0-1.4,1.1-2.5,2.5-2.5
		c1.4,0,2.5,1.1,2.5,2.5v95.2c0,1-0.6,1.9-1.5,2.3l-58.7,24.9C110.8,161.6,110.5,161.7,110.2,161.7z'/>
	<path fill='#FF6666' d='M86.1,171.9c-1.4,0-2.5-1.1-2.5-2.5v-96c0-1,0.6-1.9,1.5-2.3l32.5-13.5c1.3-0.5,2.7,0.1,3.3,1.3
		c0.5,1.3-0.1,2.7-1.3,3.2l-31,12.8v94.3C88.6,170.7,87.5,171.9,86.1,171.9z'/>
	<path fill='#FF6666' d='M86.1,171.9c-0.3,0-0.7-0.1-1-0.2l-82-35c-0.9-0.4-1.5-1.3-1.5-2.3V39.1c0-0.8,0.4-1.6,1.1-2.1
		c0.7-0.5,1.6-0.5,2.3-0.2l82,34.3c1.3,0.5,1.9,2,1.3,3.2c-0.5,1.3-2,1.8-3.3,1.3L6.6,42.9v89.9l80.5,34.4c1.3,0.5,1.8,2,1.3,3.2
		C88,171.3,87.1,171.9,86.1,171.9z'/>
	<path fill='#FF6666' d='M168.9,41.6c-0.3,0-0.7-0.1-1-0.2l-66.5-27.9c-1.3-0.5-1.9-2-1.3-3.2c0.5-1.3,2-1.8,3.3-1.3l66.5,27.9
		c1.3,0.5,1.9,2,1.3,3.2C170.8,41,169.9,41.6,168.9,41.6z'/>
	<path fill='#FF6666' d='M4.1,41.6c-1,0-1.9-0.5-2.3-1.5c-0.6-1.2,0-2.7,1.3-3.3L83.3,1.4c1.3-0.6,2.7,0,3.3,1.3
		c0.6,1.2,0,2.7-1.3,3.3L5.1,41.4C4.8,41.5,4.5,41.6,4.1,41.6z'/>
	<polygon fill='#FF6666' points='116.2,91.1 116.2,61.5 35.6,27.7 37.6,23.1 121.2,58.2 121.2,83.7 136.5,77.2 136.5,53.1 55.4,19 
		57.4,14.5 141.5,49.8 141.5,80.5 	'/>
	<path fill='#FF6666' d='M68.1,146.8c-1.4,0-2.5-1.1-2.5-2.5v-22.6c0-1.4,1.1-2.5,2.5-2.5c1.4,0,2.5,1.1,2.5,2.5v22.6
		C70.6,145.7,69.5,146.8,68.1,146.8z'/>
	<path fill='#FF6666' d='M53.2,140.7c-1.4,0-2.5-1.1-2.5-2.5v-22.6c0-1.4,1.1-2.5,2.5-2.5c1.4,0,2.5,1.1,2.5,2.5v22.6
		C55.7,139.6,54.6,140.7,53.2,140.7z'/>
</g>
</svg>
</div>
</a>

    <a  href='profile.php?page=bookings'>
           <div class='relative p-5 bg-gradient-to-r from-purple-400 to-purple-900 rounded-md overflow-hidden'>
                            <div class='relative z-10 mb-4 text-white text-4xl leading-none font-semibold'>Booking</div>
                            <div class='relative z-10 text-purple-200 leading-none font-semibold'>Check bookings</div>
</div>
</a>


                                    <a href='profile.php?page=information'>
                                    <div class='relative p-5 bg-gradient-to-r from-purple-900 to-purple-500  rounded-md overflow-hidden'>
                                    <div class='relative z-10 mb-4 text-white text-4xl leading-none font-semibold'>$username</div>
                                    <div class='relative z-10 text-purple-200 leading-none font-semibold'>Information</div>
                                        <svg version='1.1' id='Layer_1' stroke='currentColor' class='absolute right-0 bottom-0 h-32 w-32 -mr-8 -mb-8 text-blue-700 opacity-50'
                                    viewBox='0 0 173 173' style='enable-background:new 0 0 173 173;' xml:space='preserve'>
                                <style type='text/css'>
                                    .st0{fill:#FF6666;}
                                    .st1{opacity:0.11;fill:#FF6666;}
                                </style>
                                <g>
                                    <path class='st0' d='M127.7,0H45.3C31.1,0,19.6,9.7,19.6,21.7v129.5c0,12,11.5,21.7,25.7,21.7h82.5c14.2,0,25.7-9.7,25.7-21.7V21.7
                                        C153.4,9.7,141.9,0,127.7,0z M148.1,151.3c0,9.5-9.1,17.2-20.4,17.2H45.3c-11.2,0-20.4-7.7-20.4-17.2V21.7
                                        c0-9.5,9.1-17.2,20.4-17.2h82.5c11.2,0,20.4,7.7,20.4,17.2V151.3z'/>
                                    <path class='st0' d='M32.8,138.7c0-29.7,24-53.7,53.7-53.7s53.7,24,53.7,53.7H32.8z'/>
                                    <circle class='st0' cx='86.5' cy='52.3' r='23.2'/>
                                    <path class='st1' d='M120.7,173H52.3c-18.1,0-32.8-14.7-32.8-32.8V32.8C19.6,14.7,34.2,0,52.3,0h68.3c18.1,0,32.8,14.7,32.8,32.8
                                        v107.4C153.4,158.3,138.8,173,120.7,173z'/>
                                </g>
                                </svg>

                                    </div>
                                    </a>
                                    <a  href='profile.php?page=address'>
                            <div class='relative p-5 bg-gradient-to-r from-purple-400 to-purple-900 rounded-md overflow-hidden'>
                            <div class='relative z-10 mb-4 text-white text-4xl leading-none font-semibold'>Addess</div>
                            <div class='relative z-10 text-purple-200 leading-none font-semibold'>Check Or edit Add</div>
                        <svg class='absolute right-0 bottom-0 h-32 w-32 -mr-8 -mb-8 text-green-600 opacity-50' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='119px'
                            height='110.7px' viewBox='0 0 119 110.7'xml:space='preserve'>
                        <style type='text/css'>
                            .st0{fill:#FF6666;}
                        </style>
                        <defs>
                        </defs>
                        <g>
                            <path class='st0' d='M100.8,45.3c-0.6,0-1.1-0.2-1.6-0.7L61.3,4.9c-0.5-0.5-1.1-0.8-1.8-0.8s-1.3,0.3-1.8,0.8L19.7,44.6
                                c-0.8,0.8-2.2,0.9-3,0.1c-0.9-0.8-0.9-2.1-0.1-2.9L54.5,2.1C55.8,0.8,57.6,0,59.5,0c1.9,0,3.7,0.8,4.9,2.1l37.9,39.7
                                c0.8,0.8,0.7,2.1-0.1,2.9C101.8,45.1,101.3,45.3,100.8,45.3z'/>
                            <path class='st0' d='M92.2,84.4H26.8c-1.2,0-2.1-0.9-2.1-2.1V34.2c0-1.1,1-2.1,2.1-2.1c1.2,0,2.1,0.9,2.1,2.1v46.1H90V46.2
                                c0-1.1,1-2.1,2.1-2.1c1.2,0,2.1,0.9,2.1,2.1v36.2C94.3,83.5,93.4,84.4,92.2,84.4z'/>
                            <path class='st0' d='M69.8,71.3H49.2c-1.2,0-2.1-0.9-2.1-2.1c0-1.1,1-2.1,2.1-2.1h20.7c1.2,0,2.1,0.9,2.1,2.1
                                C71.9,70.4,71,71.3,69.8,71.3z'/>
                            <path class='st0' d='M59.5,95.4c-1.2,0-2.1-0.9-2.1-2.1v-11c0-1.1,1-2.1,2.1-2.1c1.2,0,2.1,0.9,2.1,2.1v11
                                C61.6,94.5,60.7,95.4,59.5,95.4z'/>
                            <path class='st0' d='M116.8,104.4H68c-1.2,0-2.1-0.9-2.1-2.1c0-1.1,1-2.1,2.1-2.1h48.8c1.2,0,2.1,0.9,2.1,2.1
                                C119,103.4,118,104.4,116.8,104.4z'/>
                            <path class='st0' d='M50.2,104.4H2.1c-1.2,0-2.1-0.9-2.1-2.1c0-1.1,1-2.1,2.1-2.1h48.1c1.2,0,2.1,0.9,2.1,2.1
                                C52.4,103.4,51.4,104.4,50.2,104.4z'/>
                            <path class='st0' d='M64.8,110.7c-0.6,0-1.2-0.3-1.6-0.8c-0.8-0.9-0.6-2.2,0.3-2.9c1.6-1.2,2.5-3.1,2.5-5c0-3.6-3-6.5-6.8-6.5
                                c-3.7,0-6.8,2.9-6.8,6.5c0,1.9,0.9,3.8,2.4,5c0.9,0.7,1,2,0.3,2.9c-0.8,0.9-2.1,1-3,0.3c-2.5-2-3.9-5-3.9-8.2c0-5.9,5-10.7,11-10.7
                                c6.1,0,11,4.8,11,10.7c0,3.2-1.5,6.2-4,8.2C65.8,110.5,65.3,110.7,64.8,110.7z'/>
                        </g>
                        </svg>
                            </div></a>
                            
                            <a  href='profile.php?page=settings'>
                          <div class='relative p-5 bg-gradient-to-r from-purple-900 to-purple-500 rounded-md overflow-hidden'>
                            <div class='relative z-10 mb-4 text-white text-4xl leading-none font-semibold'>Setting</div>
                            <div class='relative z-10 text-purple-200 leading-none font-semibold'>Change information</div>

                         <svg class='absolute right-0 bottom-0 h-32 w-32 -mr-8 -mb-8 text-green-600 opacity-50' version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='119px'
                            height='110.7px' viewBox='0 0 119 110.7'xml:space='preserve'>
<style type='text/css'>
	.st0{fill:#FF6666;}
</style>
<g>
	<path class='st0' d='M151.7,166.9H21.3c-9.9,0-18-7.8-18-17.4V23.4c0-9.6,8.1-17.4,18-17.4h130.4c9.9,0,18,7.8,18,17.4v126.1
		C169.7,159.1,161.6,166.9,151.7,166.9z M21.3,12.1c-6.4,0-11.7,5.1-11.7,11.3v126.1c0,6.2,5.2,11.3,11.7,11.3h130.4
		c6.4,0,11.7-5.1,11.7-11.3V23.4c0-6.2-5.2-11.3-11.7-11.3H21.3z M111.1,105.2c-10.4,0-18.8-8.2-18.8-18.2c0-0.2,0-0.5,0-0.7
		c0.1-1.7,1.6-2.9,3.3-2.8c1.7,0.1,3,1.5,2.9,3.2c0,0.1,0,0.2,0,0.3c0,6.7,5.6,12.1,12.6,12.1s12.6-5.4,12.6-12.1c0-0.1,0-0.2,0-0.3
		c-0.1-1.7,1.2-3.1,2.9-3.2c1.7-0.1,3.2,1.2,3.3,2.8c0,0.2,0,0.5,0,0.7C129.9,97,121.5,105.2,111.1,105.2z'/>
	<g>
		<path class='st0' d='M140.4,41H78.6c-1.7,0-3.1-1.4-3.1-3c0-1.7,1.4-3,3.1-3h61.7c1.7,0,3.1,1.4,3.1,3
			C143.5,39.6,142.1,41,140.4,41z'/>
		<path class='st0' d='M47.3,41H31.6c-1.7,0-3.1-1.4-3.1-3c0-1.7,1.4-3,3.1-3h15.7c1.7,0,3.1,1.4,3.1,3C50.4,39.6,49,41,47.3,41z'/>
		<path class='st0' d='M95.4,89.5H32.1c-1.7,0-3.1-1.4-3.1-3s1.4-3,3.1-3h63.3c1.7,0,3.1,1.4,3.1,3S97.1,89.5,95.4,89.5z'/>
		<path class='st0' d='M140.9,89.5h-14.1c-1.7,0-3.1-1.4-3.1-3s1.4-3,3.1-3h14.1c1.7,0,3.1,1.4,3.1,3S142.6,89.5,140.9,89.5z'/>
		<path class='st0' d='M141.4,138.1H78.6c-1.7,0-3.1-1.4-3.1-3c0-1.7,1.4-3,3.1-3h62.8c1.7,0,3.1,1.4,3.1,3
			C144.6,136.7,143.2,138.1,141.4,138.1z'/>
		<path class='st0' d='M47.3,138.1H32.6c-1.7,0-3.1-1.4-3.1-3c0-1.7,1.4-3,3.1-3h14.7c1.7,0,3.1,1.4,3.1,3
			C50.4,136.7,49,138.1,47.3,138.1z'/>
	</g>
	<path class='st0' d='M63,55.6c-1.7,0-3.1-1.4-3.1-3c0-1.7,1.4-3,3.1-3c6.9,0,12.6-5.4,12.6-12.1c0-6.7-5.6-12.1-12.6-12.1
		s-12.6,5.4-12.6,12.1c0,1.7-1.4,3-3.1,3s-3.1-1.4-3.1-3c0-10,8.4-18.2,18.8-18.2s18.8,8.2,18.8,18.2C81.8,47.5,73.3,55.6,63,55.6z'
		/>
	<path class='st0' d='M126.8,86.5'/>
	<path class='st0' d='M95.4,89.5c0,0-0.1,0-0.1,0c-1.7-0.1-3.1-1.5-3-3.1c0.3-9.9,8.6-17.6,18.8-17.6c1.7,0,3.1,1.4,3.1,3
		c0,1.7-1.4,3-3.1,3c-6.8,0-12.3,5.2-12.5,11.7C98.5,88.2,97.1,89.5,95.4,89.5z'/>
	<path class='st0' d='M63,153.8c-10.4,0-18.8-8.2-18.8-18.2c0-10,8.4-18.2,18.8-18.2s18.8,8.2,18.8,18.2c0,1.7-1.4,3-3.1,3
		s-3.1-1.4-3.1-3c0-6.7-5.6-12.1-12.6-12.1s-12.6,5.4-12.6,12.1c0,6.7,5.6,12.1,12.6,12.1c1.7,0,3.1,1.4,3.1,3
		C66.1,152.4,64.7,153.8,63,153.8z'/>
</g>
</svg>

                            </div></a>
                            
        </section> 
    <p class='notifications'></p>

    <div class='btn-l-c'> <div href='../logout.php' class='log'><a href='../logout.php'> Logout</a></div><div href='../logout.php' class='log'><a href='../change_password.php'>Change Password</a></div></div>

</div>
        ";
    }





    
    elseif (isset($_GET['page']) && $_GET['page'] == 'information') {
        // Dashboard content
        echo "
         <button class='btn-trick-new' onclick='history.back()'>&#8592; Go Back</button>
         <br>
         <br>
         <hr>
         <br>
         <h3>Informaton</h3>
         <br>
        <div class='show_info'>
            <table>
            <caption>Personal Information</caption>
        <tr>
            <th>Username: </th>
            <td>$username</td>
        </tr>
        <tr>
            <th>Email: </th>
            <td>$email</td>
        </tr>
        <tr>
            <th>First Name: </th>
            <td>$first_name</td>
        </tr>
        <tr>
            <th>Last Name: </th>
            <td>$last_name</td>
        </tr>
        <tr>
            <th>Phone Number: </th>
            <td>$phone_number</td>
        </tr>
        <tr>
            <td colspan='2'><a href='profile.php?page=settings'><button>Edit Information</button></a><td>
        </tr>
        <tr>
        <td colspan='2' rowspan='3'>
        <p>This information is stored in an encrypted format, ensuring the complete safety of your data.</p>
        </td>
        </tr>
    </table>
        </div>
        ";
    } 
    elseif (isset($_GET['page']) && $_GET['page'] == 'bookings') {
        // Dashboard content
        echo "
            <button class='btn-trick-new' onclick='history.back()'>&#8592; Go Back</button>
            <br><br>
            <hr><br>
            <h3>Bookings</h3>
            <br>
        ";
    
        // Fetch all bookings for the logged-in user
        $booking_query = "SELECT b.id, b.status, b.total_price, b.tracking_id, b.created_at, 
                                 c.first_name, c.last_name, c.email, c.phone_number, c.address 
                          FROM bookings b 
                          JOIN customers c ON b.customer_id = c.customer_id 
                          WHERE c.user_id = ? 
                          ORDER BY b.created_at DESC";
    
        if ($stmt = $conn->prepare($booking_query)) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            while ($order = $result->fetch_assoc()) {
                // Fetch associated services for each booking
                $services_query = "SELECT s.service_name, s.img, oi.price 
                                   FROM order_items oi 
                                   JOIN services s ON oi.service_id = s.id 
                                   WHERE oi.booking_id = ?";
    
                if ($service_stmt = $conn->prepare($services_query)) {
                    $service_stmt->bind_param('i', $order['id']);
                    $service_stmt->execute();
                    $service_result = $service_stmt->get_result();
                    
                    // Display each order
                    echo "<div class='order' id='order" . htmlspecialchars($order['id']) . "'>";
                    echo "<div class='order-header'>";
                    echo "<div>Booking Placed: <span>" . date('d-m-Y H:i:s', strtotime(htmlspecialchars($order['created_at']))) . "</span></div>"; 
                    echo "<div>Booking status: <span>" . htmlspecialchars($order['status']) . "</span></div>"; 
                    echo "<div>Payment status: <span>Pending</span></div>"; // Update this as per your payment status logic
                    echo "<div>Total: <span>₹" . htmlspecialchars($order['total_price']) . "</span></div>";
                    echo "<div>Booking #: <span>" . htmlspecialchars($order['tracking_id']) . "</span></div>"; 
                    echo "</div>";
                    echo "<div class='order-items'>";
    
                    // Display each service associated with the order
                    while ($service = $service_result->fetch_assoc()) {
                        echo "<div class='item'>";
                        echo "<img src='" . htmlspecialchars($service['img']) . "' alt='Service image'>"; 
                        echo "<div class='item-details'>";
                        echo "<div class='item-name'>" . htmlspecialchars($service['service_name']) . "<br>₹" . htmlspecialchars($service['price']) . "</div>"; 
                        echo "<hr>";
                        echo "<div class='c-name'>Name: " . htmlspecialchars($order['first_name']) . " " . htmlspecialchars($order['last_name']) . "</div>";
                        echo "<div class='c-name'>Phone Number: " . htmlspecialchars($order['phone_number']) . "</div>";
                        echo "<div class='c-name'>Email: " . htmlspecialchars($order['email']) . "</div>";
                        echo "<div class='c-name'>Address: <div class='c-address'>" . htmlspecialchars($order['address']) . "</div></div>";
                        echo "</div>"; // Closing item-details
                        echo "</div>"; // Closing item
                    }
    
                    echo "</div>"; // Closing order-items
                    echo "</div>"; // Closing order
                }
            }
    
            $stmt->close();
        } else {
            echo "<p>Error preparing statement: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
    elseif (isset($_GET['page']) && $_GET['page'] == 'orders') {
     
     
     
     
     
     
     
     
     
     
      // Orders content
echo "
 <button class='btn-trick-new' onclick='history.back()'>&#8592; Go Back</button><br>
 <br>
 <hr>
 <br>
<h3>My Orders</h3>";

while ($order = $order_result->fetch_assoc()) {
    $order_id = htmlspecialchars($order['order_id']);
    $tracking_id = htmlspecialchars($order['tracking_id']);
    $order_status = htmlspecialchars($order['order_status']);
    $payment_status = htmlspecialchars($order['payment_status']);
    $delivery_date = htmlspecialchars($order['delivery_date']);
    $order_date = htmlspecialchars($order['order_date']);
    $ref_track  = htmlspecialchars($order['ref_track']);
    
    echo "
    <div class='order' id='order" . $order_id . "'>
        <div class='order-header'>
            <div>Order Placed: <span>" . date('d-m-Y H:i:s', strtotime(htmlspecialchars($order['order_date']))) . "</span></div>
            <div>Order status: <span>" . $order_status . "</span></div>
            <div>Payment status: <span>" . $payment_status . "</span></div>
            <div>Total: <span>₹" . htmlspecialchars($order['total_price']) . "</span></div>
            <div>Order #: <span>" . $order_id . "</span></div>
        </div>
        <div class='order-items'>
            <div class='item'>
                <img src='../" . htmlspecialchars($order['image_main']) . "' alt='Product Image'>
                <div class='item-details'>
                    <div class='item-name'>" . htmlspecialchars($order['product_name']) . "<br>" . htmlspecialchars($order['product_attributes']) . "</div>
                    <div class='item-quantity'>Quantity: " . htmlspecialchars($order['product_quantity']) . "</div>
                    <div class='item-price'>₹" . htmlspecialchars($order['total_price']) . "</div>
                    <hr>
                    <div class='c-name'>Name : " . htmlspecialchars($order['first_name']) . " " . htmlspecialchars($order['last_name']) . "</div>
                    <div class='c-name'>Phone Number : " . htmlspecialchars($order['phone_number']) . "</div>
                    <div class='c-name'>Email : " . htmlspecialchars($order['email']) . "</div>
                    <div class='c-name'>Address : <div class='c-adress'>" . htmlspecialchars($order['address_line_1'].' ,'.$order['address_line_2'].' ,'.$order['city'].' ,'.$order['state'].' ,'.$order['postal_code'].' ,'.$order['country']) . "</div></div>
                </div>
            </div>
        </div>
        <div class='order-footer'>";

    // Payment status checks
    if ($payment_status == 'Paid') {
        echo "<button class='btn-trick-new' style='background-color: #24b663; color:#fff;'>Payment accepted</button>";
    } elseif ($payment_status == 'processing') {
        echo "Your payment is being processed<br><br>";
    } elseif ($payment_status == 'complete') {
        echo "Your payment is under verification<br><br>";
    } elseif ($payment_status == 'failed') {
        echo "Your payment failed<br><br>";
    } elseif ($payment_status == 'canceled') {
        echo "Your order is canceled<br><br>";
    } elseif ($payment_status == NULL) {
        echo "Your payment is under verification<br><br>";
    } else {
        echo "<button onclick=\"confirmRepayment('" . $order_id . "');\" class='btn'>Pay Now</button>";
    }

    // Order status checks
    if ($order_status == 'delivered') {
        if ($delivery_date > $order_date) {
            echo "
            <button onclick=\"location.href = 'refund.php?order_id=" . $order_id . "&user_id=" . $user_id . "';\" class='btn'>Refund Order</button>
            <button onclick=\"location.href = 'exchange.php?order_id=" . $order_id . "';\" class='btn'>Exchange Order</button>";
        }

        // Add "Write Review" button
        echo "
        <button onclick=\"location.href = 'pd/?order_id=" . $order_id . "&product_id=" . htmlspecialchars($order['product_id']) . "#add_reviews';\" class='btn'>Write Review</button>
        <button onclick=\"location.href = '../../bd/generate_invoice.php/?order_id=" . $order_id . "';\" class='btn'>View Bill</button>";

    } elseif ($order_status == 'canceled') {
        // No additional actions for canceled orders
    } elseif (in_array($order_status, ['refunded', 'exchanged'])) {
        // echo 'process Completed';
    } elseif ($order_status == 'awaiting-payment') {
        echo "
        <button onclick=\"location.href = '../payment.php?order_id=" . $order_id . "&tracking_id=" . $tracking_id . "';\" class='btn'>Pay Now</button>
        <button onclick=\"location.href = '../re-upload.php?order_id=" . $order_id . "&tracking_id=" . $tracking_id . "';\" class='btn'>Re-upload Payment proof</button>";
    } elseif ($order_status == 're-uploaded screenshot') {
        echo '<img class="wfv" src="../assets/img/wfv.gif" />';
    } elseif ($order_status == 'refund_process') {
        echo "<button onclick=\"location.href = 'refund_tracking.php?ref_track=" . htmlspecialchars($order['ref_track']) . "&user_id=" . $user_id . "';\" class='btn'>Track Refund</button>";
    } elseif ($order_status == 'Refund under process') {
        echo "<button onclick=\"location.href = 'refund_tracking.php?ref_track=" . htmlspecialchars($order['ref_track']) . "&user_id=" . $user_id . "';\" class='btn'>Track Refund</button>";
    } elseif ($order_status == 'exchange under process') {
        echo "<button onclick=\"location.href = 'exchange_tracking.php?exch_track=" . htmlspecialchars($order['exch_track']) . "&user_id=" . $user_id . "';\" class='btn'>Track Exchange</button>";
    } else {
        echo "
        <button onclick=\"location.href = 'track_order.php?tracking_id=" . $tracking_id . "';\" class='btn'>Track Package</button>
        <button onclick=\"confirmCancelOrder('" . $order_id . "');\" class='btn'>Cancel Order</button>";
    }

    echo "</div>"; // Close order-footer div
    echo "</div>"; // Close order div
}

if(mysqli_num_rows($order_result) === 0){
    
echo "  <div class='ab-o-oa' aria-hidden='true'>
                    <div class='ZAnhre'>
                    <img class='wF0Mmb' src='../assets/resent_order.svg' width='300px' height='300px' alt=''></div>
                    <div class='ab-o-oa-r'><div class='ab-o-oa-qc-V'>No Resent order Found</div>
                    <div class='ab-o-oa-qc-r'>Order now!, We have best offers.</div></div>
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
            else{
                
echo "
<div class='table-cell' colspan='7'>
    <a href='../myorders.php'>View all my past orders > > > ></a>
</div>";
echo "</div>"; // Close orders-table div
    }
}











    elseif (isset($_GET['page']) && $_GET['page'] == 'address') {
        // Fetch all addresses for the logged-in user
        $addresses_query = "SELECT address_id,address_line_1, address_line_2, city, state, postal_code, country 
                            FROM addresses 
                            WHERE user_id = ?";
    
    
// Handle address deletion
if (isset($_GET['delete'])) {
    $address_id = $_GET['delete'];

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM addresses WHERE address_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $address_id, $user_id);

    if ($stmt->execute()) {
        header("Location: ../profile.php?page=address");
        exit();
    } else {
        $error = "Error deleting address: " . $stmt->error;
    }

    $stmt->close();
}

// Handle address editing
if (isset($_GET['edit'])) {
    $address_id = $_GET['edit'];

    // Fetch address details for editing
    $stmt = $conn->prepare("SELECT * FROM addresses WHERE address_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $address_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $address = $result->fetch_assoc();
        $address_line_1 = $address['address_line_1'];
        $address_line_2 = $address['address_line_2'];
        $city = $address['city'];
        $state = $address['state'];
        $postal_code = $address['postal_code'];
        $country = $address['country'];
    }
    $stmt->close();
}

// Handle address update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_address'])) {
    $address_id = $_POST['address_id'];
    $address_line_1 = $_POST['address_line_1'];
    $address_line_2 = $_POST['address_line_2'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];

    // Update the address in the database
    $stmt = $conn->prepare("UPDATE addresses SET address_line_1 = ?, address_line_2 = ?, city = ?, state = ?, postal_code = ?, country = ? WHERE address_id = ? AND user_id = ?");
    $stmt->bind_param("ssssssii", $address_line_1, $address_line_2, $city, $state, $postal_code, $country, $address_id, $user_id);

    if ($stmt->execute()) {
    } else {
        $error = "Error updating address: " . $stmt->error;
    }
    $stmt->close();
}
        if ($stmt = $conn->prepare($addresses_query)) {
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Start table HTML
            echo "<button class='btn-trick-new' onclick='history.back()'>&#8592; Go Back</button>";
            echo "<br><br><hr><br>";
            echo "<h3>Your Addresses</h3>";
            echo "<br><button class='btn-trick-new' onclick=\"window.location.href='../profile.php?page=address&editbyman=1'\">Add a new address</button><br><br>";
            echo "<table>";
    
            // Display each address in a table
            echo "<table style='border: 1px solid black;
  border-radius: 10px; margi-bottom:2%;'>
            <thead style='border: 1px solid black;
  border-radius: 10px;'>
                <tr>
                    <th>Address Line 1</th>
                    <th>Address Line 2</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Postal Code</th>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>";
    
    while ($address = $result->fetch_assoc()) {
        echo "<tr style='border: 1px solid black;
  border-radius: 10px;'>";
        echo "<td>" . htmlspecialchars($address['address_line_1']) . "</td>";
        echo "<td>" . htmlspecialchars($address['address_line_2']) . "</td>";
        echo "<td>" . htmlspecialchars($address['city']) . "</td>";
        echo "<td>" . htmlspecialchars($address['state']) . "</td>";
        echo "<td>" . htmlspecialchars($address['postal_code']) . "</td>";
        echo "<td>" . htmlspecialchars($address['country']) . "</td>";
        echo "<td>
                <a href='?page=address&edit=" . htmlspecialchars($address['address_id']) . "&editbyman=1'>Edit</a> | 
                <a href='?page=address&delete=" . htmlspecialchars($address['address_id']) . "' onclick=\"return confirm('Are you sure you want to delete this address?');\">Delete</a>
              </td>";
        echo "</tr>";
        echo "<tr><td>";
        echo "</td></tr>";
    }
    
    echo "</tbody></table>";

      
// Handle address editing
if (isset($_GET['editbyman'])) {
    $tri_man = $_GET['editbyman'];


    // Initialize variables for form data and error handling
$address_id = $address_line_1 = $address_line_2 = $city = $state = $postal_code = $country = '';
$error = '';



// Handle address addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_address'])) {
    $address_line_1 = $_POST['address_line_1'];
    $address_line_2 = $_POST['address_line_2'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];

    // Insert new address into the database
    $stmt = $conn->prepare("INSERT INTO addresses (user_id, address_line_1, address_line_2, city, state, postal_code, country) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $address_line_1, $address_line_2, $city, $state, $postal_code, $country);

    if ($stmt->execute()) {
        header("Location: ../profile.php?page=address");
        exit();
    } else {
        $error = "Error adding address: " . $stmt->error;
    }
    $stmt->close();
}

if (isset($_GET['edit'])) {
    $address_id = $_GET['edit'];

    // Fetch address details for editing
    $stmt = $conn->prepare("SELECT * FROM addresses WHERE address_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $address_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $address = $result->fetch_assoc();
        $address_line_1 = $address['address_line_1'];
        $address_line_2 = $address['address_line_2'];
        $city = $address['city'];
        $state = $address['state'];
        $postal_code = $address['postal_code'];
        $country = $address['country'];
    }
   
}


// Handle address update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_address'])) {
    $address_id = $_POST['address_id'];
    $address_line_1 = $_POST['address_line_1'];
    $address_line_2 = $_POST['address_line_2'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];

    // Update the address in the database
    $stmt = $conn->prepare("UPDATE addresses SET address_line_1 = ?, address_line_2 = ?, city = ?, state = ?, postal_code = ?, country = ? WHERE address_id = ? AND user_id = ?");
    $stmt->bind_param("ssssssii", $address_line_1, $address_line_2, $city, $state, $postal_code, $country, $address_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: ../profile.php?page=address");
        exit();
    } else {
        $error = "Error updating address: " . $stmt->error;
    }
    
}

if ($tri_man == 1) {
    if (!empty($error)) {
        echo '
        <br>
        <br>
        <br>
        <br>
         <p style="color: red;">' . $error . '</p>';
    }
    echo '<br>
    
    <br><br>
    <br><!-- Add Address Form -->';
    echo '<h2>' . ($address_id ? 'Edit Address' : 'Add New Address ') . '</h2>';
    echo "<br><button class='btn-trick-new' onclick=\"window.location.href='../profile.php?page=address'\">&times; Close Form</button><br>";
    echo '<form method="post" action="">';
    echo '<input type="hidden" name="address_id" value="' . $address_id . '">';
    echo '<label for="address_line_1">Address Line 1:</label>';
    echo '<input type="text" id="address_line_1" name="address_line_1" value="' . htmlspecialchars($address_line_1) . '" required>';
    echo '<label for="address_line_2">Address Line 2:</label>';
    echo '<input type="text" id="address_line_2" name="address_line_2" value="' . htmlspecialchars($address_line_2) . '">';
    echo '<label for="city">City:</label>';
    echo '<input type="text" id="city" name="city" value="' . htmlspecialchars($city) . '" required>';
    echo '<label for="state">State:</label>';
    echo '<input type="text" id="state" name="state" value="' . htmlspecialchars($state) . '" required>';
    echo '<label for="postal_code">Postal Code:</label>';
    echo '<input type="text" id="postal_code" name="postal_code" value="' . htmlspecialchars($postal_code) . '" required>';
    echo '<label for="country">Country:</label>';
    echo '<input type="text" id="country" name="country" value="' . htmlspecialchars($country) . '" required>';
    if ($address_id) {
        echo '<button class="su" type="submit" name="update_address">Update Address</button>';
    } else {
        echo '<button class="su" type="submit" name="add_address">Add Address</button>';
    }
    echo '</form>';
}
}
    
            echo "<p>This information is stored in an encrypted format, ensuring the complete safety of your data.</p>";
    
            $stmt->close();
        } else {
            echo "<p>Error preparing statement: " . htmlspecialchars($conn->error) . "</p>";
        }
    }

    
    
    
    
    
    
    
    
    
    
    elseif (isset($_GET['page']) && $_GET['page'] == 'settings') {
        echo "<button class='btn-trick-new' onclick='history.back()'>&#8592; Go Back</button>
         <br>
         <br>
         <hr>";
        // Settings content
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
            $new_username = $_POST['username'];
            $new_first_name = $_POST['first_name'];
            $new_last_name = $_POST['last_name'];
            $new_phone_number = $_POST['phone_number'];
            // Check if the new username already exists for a different user
            $check_query = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND user_id != ?");
            $check_query->bind_param("si", $new_username, $user_id);
            $check_query->execute();
            $check_query->bind_result($count);
            $check_query->fetch();
            $check_query->close();
        
            if ($count > 0) {
                echo "Error: The username already exists. Please choose a different username.";
            } else {
                // Proceed with the update
                $update_query = $conn->prepare("UPDATE users SET username = ?, first_name = ?, last_name = ?, phone_number = ? WHERE user_id = ?");
                $update_query->bind_param("ssssi", $new_username, $new_first_name, $new_last_name, $new_phone_number, $user_id);
                $update_query->execute();
                $update_query->close();
        
                // Refresh user information
                header("Location: profile.php?page=dashboard");
                exit();
            }
        }
        

        echo "
        <hr>
         <br>
         <h3>Update Personal Information</h3>
         <br>
        <form method='post' action='profile.php?page=settings'>
            <label for='username'>Username:</label>
            <input type='text' id='username' name='username' value='".htmlspecialchars($username)."' required><br>
            <input readonly type='hidden' id='email' name='email' value='".htmlspecialchars($email)."' required disabled readonly>
            <label for='first_name'>First Name:</label>
            <input type='text' id='first_name' name='first_name' value='".htmlspecialchars($first_name)."' required><br>
            <label for='last_name'>Last Name:</label>
            <label id='address'></label>
            <input type='text' id='last_name' name='last_name' value='".htmlspecialchars($last_name)."' required><br>
            <label for='phone_number'>Phone Number:</label>
            <input type='text' id='phone_number' name='phone_number' value='".htmlspecialchars($phone_number)."' required><br>
            <button class=' su' type='submit' name='update_info'>Update Information</button>
        </form>
        <br>
         <a href='../change_password.php'><button class='btn-trick-new su' onclick='location.href = '';'>Change Password</button></a>
        
        ";
        // Display update form
    } else {
        // Default to dashboard if no valid page parameter is provided
        header("Location: profile.php?page=dashboard");
        exit();
    }
    ?>
    </div>



    <script>
    function confirmCancelOrder(orderId) {
        if (confirm("Are you sure you want to cancel this order?")) {
            window.location.href = 'cancel_order.php?order_id=' + orderId;
        }
    }

    function confirmRepayment(orderId) {
        if (confirm("Are you sure you want to make a payment for this order?")) {
            window.location.href = '../pay-now/?order_id=' + orderId;
        }
    }
    </script>
    <footer>
        <div id="footer"></div>
    </footer>

</body>

</html>