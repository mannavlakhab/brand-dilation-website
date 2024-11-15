<?php
$user_agent = $_SERVER['HTTP_USER_AGENT'];


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard sidebar</title>
    <link rel="stylesheet" href="./assets/font.css">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>

<body>

    <div id="__next" bis_skin_checked="1">
        <div style="opacity: 0.5;"
            class="absolute z-0 top-0 inset-x-0 flex justify-center overflow-hidden pointer-events-none"
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

    <?php
   
$id = $_SESSION['id'];
$role = $_SESSION['role'];

// Fetch user information including profile picture
$sql = "SELECT username, profile_icon,role FROM admin_users WHERE id = '$id'";
$header = $conn->query($sql);

    if ($header->num_rows > 0) {
        // Output data of each row
        while ($row = $header->fetch_assoc()) {
            $profile_icon = $row["profile_icon"];
            $username = $row["username"];
            $role = $row["role"];
        }
    ?>


    <!-- Sidebar -->
    <div class="w-64 bg-zinc-50 shadow-md mr-4 min-h-full h-fit z-50 fixed lg:relative lg:transform-none lg:sidebar md:sidebar-hidden sm:sidebar-hidden"
        id="sidebar">
        <button class="p-2 m-2 right-0 text-gray-500 focus:outline-none lg:hidden" id="close-button">
            <span class="material-symbols-outlined">
                close
            </span>
             Close sidebar
        </button>
        <div class="p-4 flex justify-between items-center">
            <img src="./assets/logo.png" alt="">
            <h1 class="text-2xl font-bold">

            </h1>
        </div>
        <nav class="mt-6">
            <ul>

                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                    <a href="../index.php" class="nav__link">
                        <span class="material-symbols-outlined">
                            captive_portal
                        </span>
                        <span class="nav__name">Visit Website</span>
                    </a>
                </li>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                    <a href="index.php" class="nav__link">
                        <span class="material-symbols-outlined">
                            dashboard
                        </span>
                        <span class="nav__name">Dashboard</span>
                    </a>
                </li>

                <?php if ($role == 'sales') { ?>
                    <li class="px-4 py-2 text-gray-700">
                    <a href="#products" class="nav__link">
                        <span class="material-symbols-outlined">
                            inventory_2
                        </span>
                        <span class="nav__name">Products</span>
                    </a>
                    <ul>
                        <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="add_product.php"
                                class="nav__link"><span class="material-symbols-outlined">
                                    box_add
                                </span>Add</a></li>
                </li>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                    <a href="variations.php" class="nav__link"><span class="material-symbols-outlined">
                            style
                        </span>
                        <span class="nav__name">Variations</span>
                    </a>
                </li>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="view_product.php" class="nav__link"><span
                            class="material-symbols-outlined">
                            pageview
                        </span>View</a></li>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="insert_product.php"
                        class="nav__link"><span class="material-symbols-outlined">
                            box_edit
                        </span>Add info</a></li>
            </ul>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="view_orders.php" class="nav__link"><span class="material-symbols-outlined">
                        box
                    </span>
                    <span class="nav__name">Orders</span>
                </a>
            </li>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="rent_form.php" class="nav__link"><span class="material-symbols-outlined">
                        business_chip
                    </span>
                    <span class="nav__name">Rent Form</span>
                </a>
            </li>
            <li class="px -4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="#coupons" class="nav__link"><span class="material-symbols-outlined">
                        heap_snapshot_large
                    </span>
                    <span class="nav__name">Coupons</span>
                </a>
                <ul>
                    <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="add_coupon.php"
                            class="nav__link"><span class="material-symbols-outlined">
                                new_label
                            </span>Add</a></li>
                    <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="view_coupon.php"
                            class="nav__link"><span class="material-symbols-outlined">
                                shoppingmode
                            </span>View</a></li>
                </ul>   




<!-- super admin part -->



                <?php } elseif ($role == 'super_admin') { ?>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                    <a href="send_notification.php" class="nav__link"><span class="material-symbols-outlined">
                            add_alert
                        </span>
                        <span class="nav__name">Notifications</span>
                    </a>
                </li>
                <li class="px-4 py-2 text-gray-700">
                    <a href="#products" class="nav__link">
                        <span class="material-symbols-outlined">
                            inventory_2
                        </span>
                        <span class="nav__name">Products</span>
                    </a>
                    <ul>
                        <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="add_product.php"
                                class="nav__link"><span class="material-symbols-outlined">
                                    box_add
                                </span>Add</a></li>
                </li>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                    <a href="variations.php" class="nav__link"><span class="material-symbols-outlined">
                            style
                        </span>
                        <span class="nav__name">Variations</span>
                    </a>
                </li>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="view_product.php" class="nav__link"><span
                            class="material-symbols-outlined">
                            pageview
                        </span>View</a></li>
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="insert_product.php"
                        class="nav__link"><span class="material-symbols-outlined">
                            box_edit
                        </span>Add info</a></li>
            </ul>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="view_orders.php" class="nav__link"><span class="material-symbols-outlined">
                        box
                    </span>
                    <span class="nav__name">Orders</span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="view_booking.php" class="nav__link"><span class="material-symbols-outlined">
                        assignment
                    </span>
                    <span class="nav__name">Booking</span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="services_category.php" class="nav__link"><span class="material-symbols-outlined">
                        dvr
                    </span>
                    <span class="nav__name">S- Categories</span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="services_list.php" class="nav__link"><span class="material-symbols-outlined">
                        display_settings
                    </span>
                    <span class="nav__name">Services </span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="view_refunds.php" class="nav__link">
                    <span class="material-symbols-outlined">
                        request_quote
                    </span>
                    <span class="nav__name">Refund Requestes</span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <span class="material-symbols-outlined">
                    partner_exchange
                </span>
                <a href="view_exchanage.php" class="nav__link">
                    <span class="nav__name">exchanage Requestes</span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
            <span class="material-symbols-outlined">
admin_panel_settings
</span>
                <a href="./admin_user.php" class="nav__link">
                    <span class="nav__name">Admin Users</span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <span class="material-symbols-outlined">
                    group
                </span>
                <a href="./users.php" class="nav__link">
                    <span class="nav__name">Users</span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="slider.php" class="nav__link"><span class="material-symbols-outlined">
                        planner_banner_ad_pt
                    </span>
                    <span class="nav__name">Slider</span>
                </a>
            </li>
            <li class="px-4 py-2 text-gray-700 ">
                <a href="#categories" class="nav__link"><span class="material-symbols-outlined">
                        category
                    </span>
                    <span class="nav__name">Categories</span>
                </a>
                <ul>
                    <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="add_category.php"
                            class="nav__link"><span class="material-symbols-outlined">
                                add_circle
                            </span>Add</a></li>
                    <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="view_categories.php"
                            class="nav__link"><span class="material-symbols-outlined">
                                pageview
                            </span>View</a></li>
                </ul>
            </li>
            <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="rent_form.php" class="nav__link"><span class="material-symbols-outlined">
                        business_chip
                    </span>
                    <span class="nav__name">Rent Form</span>
                </a>
            </li>
            <li class="px -4 py-2 text-gray-700 hover:bg-gray-200">
                <a href="#coupons" class="nav__link"><span class="material-symbols-outlined">
                        heap_snapshot_large
                    </span>
                    <span class="nav__name">Coupons</span>
                </a>
                <ul>
                    <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="add_coupon.php"
                            class="nav__link"><span class="material-symbols-outlined">
                                new_label
                            </span>Add</a></li>
                    <li class="px-4 py-2 text-gray-700 hover:bg-gray-200"><a href="view_coupon.php"
                            class="nav__link"><span class="material-symbols-outlined">
                                shoppingmode
                            </span>View</a></li>
                        </ul>
                        <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                            <a href="delivery" class="nav__link"><span class="material-symbols-outlined">
        groups_2
        </span>
                                <span class="nav__name">Set Delivery</span>
                            </a>
                        </li> 
                        <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                                <a href="delivery/db.php" class="nav__link"><span class="material-symbols-outlined">
                        groups_2
                        </span>
                                    <span class="nav__name">start Delivery</span>
                                </a>
                            </li> 
                <?php } elseif ($role == 'outer') {?>
                 
                    <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                        <a href="delivery" class="nav__link"><span class="material-symbols-outlined">
groups_2
</span>
                            <span class="nav__name">Set Delivery</span>
                        </a>
                    </li> 
                 <?php     ?>
                    
                    
              <?php  } elseif ($role == 'delivery') { ?>         
                <li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
                        <a href="delivery/db.php" class="nav__link"><span class="material-symbols-outlined">
groups_2
</span>
                            <span class="nav__name">start Delivery</span>
                        </a>
                    </li> 

<?php }} ?>

<li class="px-4 py-2 text-gray-700 hover:bg-gray-200">
    <a href="logout.php" class="nav__link"><span class="material-symbols-outlined">
            logout
        </span>
        <span class="nav__name">Logout</span>
    </a>
</li>  

         
            </ul>

        </nav>

    </div>

</body>

</html>