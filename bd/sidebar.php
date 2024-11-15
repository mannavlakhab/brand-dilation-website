<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard sidebar</title>
    <link rel="stylesheet" href="path_to_your_css_file.css"> <!-- Add your CSS file path -->
</head>

<body>

    <?php
// SQL query to fetch profile_icon and username for the logged-in admin
$sql = "SELECT profile_icon, username FROM admin_users WHERE role = 'super_admin'"; // Adjust role condition as needed

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $profile_icon = $row["profile_icon"];
        $username = $row["username"];
    }
?>



    <div class="sidebar">
        <nav class="nav">
            <ul class="nav__list">
<?php if ($role == 'sales') { ?>

                <li class="nav__item">
                    <a href="../index.php" class="nav__link">
                        <span class="nav__name">Visit Website</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#Dashboard" class="nav__link">
                        <span class="nav__name">Dashboard</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#products" class="nav__link">
                        <span class="nav__name">Products</span>
                    </a>
                    <ul>
                        <li><a href="add_product.php" class="nav__link">Add</a></li>
                        <li><a href="view_product.php" class="nav__link">View</a></li>
                        <li><a href="insert_product.php" class="nav__link">Add info</a></li>
                    </ul>
                </li>
                <li class="nav__item">
                    <a href="orders.php" class="nav__link">
                        <span class="nav__name">Orders</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#categories" class="nav__link">
                        <span class="nav__name">Categories</span>
                    </a>
                    <ul>
                        <li><a href="add_category.php" class="nav__link">Add</a></li>
                        <li><a href="view_categories.php" class="nav__link">View</a></li>
                    </ul>
                </li>
                <li class="nav__item">
                    <a href="rent_form.php" class="nav__link">
                        <span class="nav__name">Rent Form</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="logout.php" class="nav__link">
                        <span class="nav__name">Logout</span>
                    </a>
                </li>



                <?php } else 
                // ($role == 'super_admin') 
                { ?>

                <li class="nav__item">
                    <a href="../index.php" class="nav__link">
                        <span class="nav__name">Visit Website</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="./announcement.php" class="nav__link">
                        <span class="nav__name">Announcement</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#Dashboard" class="nav__link">
                        <span class="nav__name">Super Admin</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#products" class="nav__link">
                        <span class="nav__name">Products</span>
                    </a>
                    <ul>
                        <li><a href="add_product.php" class="nav__link">Add</a></li>
                        <li><a href="view_product.php" class="nav__link">View</a></li>
                        <li><a href="insert_product.php" class="nav__link">Add info</a></li>
                    </ul>
                </li>
                <li class="nav__item">
                    <a href="orders.php" class="nav__link">
                        <span class="nav__name">Orders</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="view_refunds.php" class="nav__link">
                        <span class="nav__name">Refund Requestes</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="view_exchanage.php" class="nav__link">
                        <span class="nav__name">exchanage Requestes</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="./users.php" class="nav__link">
                        <span class="nav__name">Users</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="slider.php" class="nav__link">
                        <span class="nav__name">Slider</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#categories" class="nav__link">
                        <span class="nav__name">Categories</span>
                    </a>
                    <ul>
                        <li><a href="add_category.php" class="nav__link">Add</a></li>
                        <li><a href="view_categories.php" class="nav__link">View</a></li>
                    </ul>
                </li>
                <li class="nav__item">
                    <a href="rent_form.php" class="nav__link">
                        <span class="nav__name">Rent Form</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="#coupons" class="nav__link">
                        <span class="nav__name">Coupons</span>
                    </a>
                    <ul>
                        <li><a href="add_coupon.php" class="nav__link">Add</a></li>
                        <li><a href="view_coupon.php" class="nav__link">View</a></li>
                    </ul>
                </li>
                <li class="nav__item">
                    <a href="variations.php" class="nav__link">
                        <span class="nav__name">Variations</span>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="logout.php" class="nav__link">
                        <span class="nav__name">Logout</span>
                    </a>
                </li>

                <?php }} ?>
            
            </ul>
        </nav>
    </div>
</body>

</html>