<?php
session_start();
include '../db_connect.php';
// Fetch products based on search query
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "SELECT * FROM Products WHERE brand LIKE '%$search%' OR model LIKE '%$search%' OR description LIKE '%$search%'";
$result = $conn->query($sql);



?>
<!DOCTYPE html>
<html lang="en">

<head>
    
<script src="../assets/js/internet-check.js" defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>

    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <!--=============== fav ===============-->
    <link rel="shortcut icon" href="./assets/img/bd.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

    <!--=============== BOXICONS ===============-->


    <link rel="stylesheet" href="../pages/css/hstyle.css">
</head>

<body>
    <header class="header" id="header">
        <nav class="nav container-nav-new">

            <a href="../index.php" class="nav__logo"><img id="logo_style_bd" id="logo_nav_my"
                    src="../assets/img/1x/bd6.png" alt="Logo" srcset=""></a>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="../index.php" class="nav__link">
                            <i class='bx bxs-home-alt-2 nav__icon'></i>
                            <span class="nav__name">Home</span>
                        </a>
                    </li>

                    <li class="nav__item">
                        <a href="../services" class="nav__link">
                            <i class='bx bxs-wrench nav__icon'></i>
                            <span class="nav__name">Servies</span>
                        </a>
                    </li>

                    <!-- <li class="nav__item">
                    <form method="GET" action="../shop.php">
            <div class="div-search">
                <input type="text" class="search__input" name="search" placeholder="Search products..."
                    value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="search__button">
                    <svg class="search__icon" aria-hidden="true" viewBox="0 0 24 24">
                        <g>
                            <path
                                d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z">
                            </path>
                        </g>
                    </svg>
                </button>
            </div>
        </form>
                    </li> -->
                    <li class="nav__item">
                        <a href="../shop.php" class="nav__link">
                            <i class='bx bxs-package nav__icon'></i>
                            <span class="nav__name">Products</span>
                        </a>
                    </li>
                    <li class="nav__item">
                        <a href="../cart.php" class="nav__link">
                            <i class='bx bxs-cart nav__icon'></i>
                            <span class="nav__name">Cart</span>
                        </a>
                    </li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li class="nav__item">
                            <a href="../liked" class="nav__link">
                                <i class='bx bx-heart-circle nav__icon'></i> 
                                <span class="nav__name">Liked</span></a>
                        </li>
                        <div class="navbar-right">
                        <li class="nav__item">
                            <a href="../profile" class="nav__link">
                                <i class='bx bxs-user nav__icon'></i>
                                <span class="nav__name">Profile</span></a>
                        </li></div>
                        <?php else: ?>
                        <li class="nav__item">
                            <a href="../login.php" class="nav__link">
                                <i class='bx bxs-user nav__icon'></i>
                                <span class="nav__name">Log In</span>
                            </a>
                        </li>

                        <?php endif; ?>

                </ul>
            </div>


        </nav>
    </header>
</body>

</html>