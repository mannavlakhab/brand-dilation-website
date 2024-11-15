<?php
session_start();
include 'db_connect.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch products based on search query
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM Products WHERE brand LIKE '%$search%' OR model LIKE '%$search%' OR description LIKE '%$search%' ORDER BY product_id DESC LIMIT 10";
$result = $conn->query($sql);

$search = "I3";
$sql = "SELECT * FROM Products WHERE brand LIKE '%$search%' OR model LIKE '%$search%' OR description LIKE '%$search%' OR short_des LIKE '%$search%' ORDER BY product_id DESC";
$ai = $conn->query($sql);

$search = " office, laptop";
$sql = "SELECT * FROM Products WHERE keywords LIKE '%$search%' ORDER BY product_id DESC";
$words = $conn->query($sql);


// Fetch sliders from the database
$sql_slid = "SELECT * FROM slideshow";
$result_slider = mysqli_query($conn, $sql_slid);

// Fetch refurbished products from the database
$refub = "SELECT * FROM Products WHERE refurbished = 1 LIMIT 5";
$ref = mysqli_query($conn, $refub);

// Fetch refurbished products from the database
$rele = "SELECT * FROM Products WHERE brand = 'Lenovo'";
$len = mysqli_query($conn, $rele);


// Fetch refurbished products from the database
$re_asus = "SELECT * FROM Products WHERE brand = 'Asus'";
$asus = mysqli_query($conn, $re_asus);

// Fetch refurbished products from the database
$redell = "SELECT * FROM Products WHERE brand = 'Dell'";
$dell = mysqli_query($conn, $redell);

// Fetch refurbished products from the database
$rehp = "SELECT * FROM Products WHERE brand = 'HP'";
$hp = mysqli_query($conn, $rehp);

// Fetch refurbished products from the database
$recom = "SELECT * FROM Products WHERE category_id  = 2";
$com = mysqli_query($conn, $recom);


// Fetch services under the selected category
$query = "SELECT * FROM services ";
$servi = mysqli_query($conn, $query);

// Fetch services under the selected category
$query = "SELECT * FROM  service_categories";
$ser = mysqli_query($conn, $query);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Dilaton - welcome to refurbished market</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/blog.css">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">

    <!-- Include Material Icons CSS -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Kode+Mono:wght@400..700&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
    </style>
    <!--=============== file loader ===============-->
    <!--=============== header ===============-->
    <script src="../assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <script>
    $(function() {
        $('#header').load('../pages/header.php');

    });
    </script>
    <!--=============== footer ===============-->
    <script>
    $(function() {
        $('#footer').load('../pages/footer.php');

    });
    </script>
    <script>
    $(function() {
        $('#symbols_new_the_uid').load('sy.html');

    });
    </script>

    <script>
    $(function() {
        $('#2_colum_op').load('2col.html');

    });
    </script>
    <script>
    $(function() {
        $('#dd').load('dd.html');

    });
    </script>

    <!--=============== closing file loader ===============-->

</head>

<body>


    <header>

        <!--=============== HEADER ===============-->
        <div id="header"></div>
    </header>



    <main style="margin-top:3%">

        <!-- Slider -->
        <!-- Slider -->
        <section class="hero">
            <div class="slides-container">
                <?php while ($row = mysqli_fetch_assoc($result_slider)) : ?>
                <div class="slide">
                    <!-- Desktop image -->
                    <img src="<?php echo $row['image_path']; ?>" class="desktop-img">
                    <!-- Mobile image -->
                    <img src="<?php echo $row['mobile_image_path']; ?>" class="mobile-img">
                </div>
                <?php endwhile; ?>
            </div>

            <script>
            var slideIndex = 0;
            showSlides();

            function showSlides() {
                var i;
                var slides = document.getElementsByClassName("slide");
                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                slideIndex++;
                if (slideIndex > slides.length) {
                    slideIndex = 1;
                }
                slides[slideIndex - 1].style.display = "block";
                setTimeout(showSlides, 2000); // Change image every 2 seconds
            }
            </script>
        </section>
        <!-- end slider -->

        <!-- end slider -->

        <!-- rent card -->

        <section class="rent-se">
            <!-- <h2 id="title-new">Book a rent services</h2> -->
            <h3>Rent High-Performance Laptops and Computers at Affordable Rates! Starting at Just ₹1299!</h3>
            <p id="rent_id_p">Looking for a reliable and powerful laptop or computer? Rent the best high-performance
                devices at unbeatable prices.</p>
            <button onclick="location.href = '/rent-form-bd/rent-form.php';">Book Now </button><button
                onclick="location.href = '../pages/toc.html#rent';">Learn More</button>

        </section>


        <!-- end rent card -->


        <!-- shop page part -->

        <!-- 
<form method="GET" action="">
            <div class="div-search">
    <input type="text" class="search__input"  name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
    <button type="submit" class="search__button">
    <svg class="search__icon" aria-hidden="true" viewBox="0 0 24 24">
            <g>
                <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"></path>
            </g>
        </svg>
    </button>
</div>
        </form>

 -->





        <div class="new_to_con">
            <style>
            :root {
                --color-text: navy;
                --color-bg: papayawhip;
                --color-bg-accent: #ecdcc0;
                --size: clamp(10rem, 1rem + 40vmin, 30rem);
                --gap: calc(var(--size) / 14);
                --duration: 60s;
                --scroll-start: 0;
                --scroll-end: calc(-100% - var(--gap));
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    --color-text: papayawhip;
                    --color-bg: #e8e8e8;
                    --color-bg-accent: #2626a0;
                }
            }

            * {
                box-sizing: border-box;
            }

            .new_to_con {
                display: grid;
                align-content: center;
                overflow: hidden;
                gap: var(--gap);
                width: 100%;
                font-family: system-ui, sans-serif;
                font-size: 1rem;
                line-height: 1.5;
                color: var(--color-text);
                background-color: #e8e8e8;
            }

            .marquee {
                display: flex;
                overflow: hidden;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                gap: var(--gap);
                -webkit-mask-image: linear-gradient(var(--mask-direction, to right),
                        hsl(0 0% 0% / 0),
                        hsl(0 0% 0% / 1) 20%,
                        hsl(0 0% 0% / 1) 80%,
                        hsl(0 0% 0% / 0));
                mask-image: linear-gradient(var(--mask-direction, to right),
                        hsl(0 0% 0% / 0),
                        hsl(0 0% 0% / 1) 20%,
                        hsl(0 0% 0% / 1) 80%,
                        hsl(0 0% 0% / 0));
            }

            .marquee__group {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: space-around;
                gap: var(--gap);
                min-width: 100%;
                -webkit-animation: scroll-x var(--duration) linear infinite;
                animation: scroll-x var(--duration) linear infinite;
            }

            @media (prefers-reduced-motion: reduce) {
                .marquee__group {
                    -webkit-animation-play-state: paused;
                    animation-play-state: paused;
                }
            }

            .marquee--vertical {
                --mask-direction: to bottom;
            }

            .marquee--vertical,
            .marquee--vertical .marquee__group {
                flex-direction: column;
            }

            .marquee--vertical .marquee__group {
                -webkit-animation-name: scroll-y;
                animation-name: scroll-y;
            }

            .marquee--reverse .marquee__group {
                animation-direction: reverse;
                -webkit-animation-delay: -3s;
                animation-delay: -3s;
            }

            @-webkit-keyframes scroll-x {
                from {
                    transform: translateX(var(--scroll-start));
                }

                to {
                    transform: translateX(var(--scroll-end));
                }
            }

            @keyframes scroll-x {
                from {
                    transform: translateX(var(--scroll-start));
                }

                to {
                    transform: translateX(var(--scroll-end));
                }
            }

            @-webkit-keyframes scroll-y {
                from {
                    transform: translateY(var(--scroll-start));
                }

                to {
                    transform: translateY(var(--scroll-end));
                }
            }

            @keyframes scroll-y {
                from {
                    transform: translateY(var(--scroll-start));
                }

                to {
                    transform: translateY(var(--scroll-end));
                }
            }

            /* Element styles */
            .marquee svg {
                display: grid;
                place-items: center;
                width: var(--size);
                fill: #000;
                background: #FFF;
                aspect-ratio: 16/9;
                padding: calc(var(--size) / 10);
                border-radius: 0.5rem;
            }

            .marquee--vertical svg {
                aspect-ratio: 1;
                width: calc(var(--size) / 1.5);
                padding: calc(var(--size) / 6);
            }

            /* Parent wrapper */
            .wrapper {
                display: flex;
                flex-direction: column;
                gap: var(--gap);
                margin: auto;
                max-width: 100vw;
            }

            .wrapper--vertical {
                flex-direction: row;
                height: 100vh;
            }

            /* Toggle direction button */
            .toggle {
                --size: 3rem;
                position: relative;
                position: fixed;
                top: 1rem;
                left: 1rem;
                width: var(--size);
                height: var(--size);
                font: inherit;
                text-align: center;
                cursor: pointer;
                outline: none;
                border: none;
                border-radius: 50%;
                color: inherit;
                background-color: var(--color-bg-accent);
                z-index: 1;
            }

            .toggle:focus-visible {
                box-shadow: 0 0 0 2px var(--color-text);
            }

            .toggle span {
                position: absolute;
                display: inline-block;
                top: 50%;
                left: calc(100% + 0.4em);
                width: -webkit-fit-content;
                width: -moz-fit-content;
                width: fit-content;
                white-space: nowrap;
                transform: translateY(-50%);
                -webkit-animation: fade 400ms 4s ease-out forwards;
                animation: fade 400ms 4s ease-out forwards;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            .toggle svg {
                --size: 1.5rem;
                position: absolute;
                top: 50%;
                left: 50%;
                width: var(--size);
                height: var(--size);
                fill: currentcolor;
                transform: translate(-50%, -50%);
                transition: transform 300ms cubic-bezier(0.25, 1, 0.5, 1);
            }

            .toggle--vertical svg {
                transform: translate(-50%, -50%) rotate(-90deg);
            }

            @-webkit-keyframes fade {
                to {
                    opacity: 0;
                    visibility: hidden;
                }
            }

            @keyframes fade {
                to {
                    opacity: 0;
                    visibility: hidden;
                }
            }

            @font-face {
                font-family: 'bd title';
                /* src: url('../assets/font/bd title.woff2') format('woff2'); */
                src: url('../assets/font/pp.woff2') format('woff2');

            }

            .wrapper h1 {
                font-family: bd title !important;
                text-transform: capitalize;
                font-weight: 600;
                color: #3c0e40;
                text-align: center;
                line-height: 1.1;
                font-size: xx-large;

            }
            </style>


            <!-- partial:index.partial.html -->
            <!-- <button class="toggle" id="direction-toggle">
    <span>Toggle scroll axis</span>
    <svg aria-hidden="true" viewBox="0 0 512 512" width="100" title="arrows-alt-h">
        <path d="M377.941 169.941V216H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.568 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296h243.882v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.568 0-33.941l-86.059-86.059c-15.119-15.12-40.971-4.412-40.971 16.97z" />
    </svg>
    </button> -->
            <article class="wrapper">

                <h1>Top companies</h1>
                <div class="marquee">
                    <div class="marquee__group">
                        <a href="../shop.php?search=&brand%5B%5D=hp" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#hp" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=microsoft" target="_blank"
                            rel="noopener noreferrer"><svg>
                                <use xlink:href="#microsoft" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=lenovo" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#lenovo" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=msi" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#msi" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=dell" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#dell" />
                            </svg></a>
                        <a href="../shop.php?search=intel" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#intel" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=apple" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#apple" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=asus" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#asus" />
                            </svg></a>
                        <a href="../shop.php?search=amd" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#amd" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=acer" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#acer" />
                            </svg></a>
                    </div>

                    <div aria-hidden="true" class="marquee__group">
                        <a href="../shop.php?search=&brand%5B%5D=lenovo" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#lenovo" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=dell" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#dell" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=hp" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#hp" />
                            </svg></a>
                        <a href="../shop.php?search=intel" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#intel" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=apple" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#apple" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=asus" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#asus" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=microsoft" target="_blank"
                            rel="noopener noreferrer"><svg>
                                <use xlink:href="#microsoft" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=acer" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#acer" />
                            </svg></a>
                        <a href="../shop.php?search=amd" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#amd" />
                            </svg></a>
                    </div>
                </div>

                <div class="marquee marquee--reverse">
                    <div class="marquee__group">
                        <a href="../shop.php?search=&brand%5B%5D=lenovo" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#lenovo" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=dell" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#dell" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=hp" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#hp" />
                            </svg></a>
                        <a href="../shop.php?search=intel" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#intel" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=apple" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#apple" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=asus" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#asus" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=microsoft" target="_blank"
                            rel="noopener noreferrer"><svg>
                                <use xlink:href="#microsoft" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=acer" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#acer" />
                            </svg></a>
                        <a href="../shop.php?search=amd" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#amd" />
                            </svg></a>
                    </div>

                    <div aria-hidden="true" class="marquee__group">
                        <a href="../shop.php?search=&brand%5B%5D=lenovo" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#lenovo" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=dell" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#dell" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=hp" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#hp" />
                            </svg></a>
                        <a href="../shop.php?search=intel" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#intel" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=apple" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#apple" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=asus" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#asus" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=microsoft" target="_blank"
                            rel="noopener noreferrer"><svg>
                                <use xlink:href="#microsoft" />
                            </svg></a>
                        <a href="../shop.php?search=&brand%5B%5D=acer" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#acer" />
                            </svg></a>
                        <a href="../shop.php?search=amd" target="_blank" rel="noopener noreferrer"><svg>
                                <use xlink:href="#amd" />
                            </svg></a>
                    </div>
                </div>
            </article>


            <svg style="display: none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <symbol id="hp" viewBox="0 0 24 24">
                        <path
                            d="M8.4210347,0.00016035599 L5,15.127022 L7.13818677,15.127022 L10.5590611,0.00016035599 L8.4210347,0.00016035599 Z M17.4142797,8.87313837 L15.9176772,15.0979976 L18.0557037,15.0979976 L19.5523061,8.87313837 L17.4142797,8.87313837 Z M13.7794905,8.87313837 L10.3586161,24 L12.4966425,24 L15.9176772,8.87313837 L13.7794905,8.87313837 Z M10.131552,8.87313837 L8.63478923,15.0979976 L10.7728157,15.0979976 L12.2694181,8.87313837 L10.131552,8.87313837 Z" />
                    </symbol>
                    <symbol id="microsoft" viewBox="0 0 16 16">
                        <path d="M1 1h6.5v6.5H1V1zM8.5 1H15v6.5H8.5V1zM1 8.5h6.5V15H1V8.5zM8.5 8.5H15V15H8.5V8.5z" />
                    </symbol>

                    <symbol id="dell" viewBox="-19 5 80 24">
                        <path
                            d="M23.953 19.469v-7.036h1.625v5.604h2.854v1.427h-4.479zM10.906 15.083l3.974-3.057c0.375 0.302 0.745 0.609 1.12 0.917l-3.771 2.854 0.818 0.714 3.766-2.958c0.375 0.307 0.75 0.615 1.125 0.922-1.25 0.99-2.51 1.974-3.771 2.953l0.813 0.714 3.776-3.057-0.005-2.651h1.63v5.604h2.958v1.427h-4.484v-2.646c-1.328 1.016-2.651 2.036-3.974 3.052l-3.974-3.052c-0.193 0.969-0.87 1.813-1.75 2.255-0.38 0.198-0.797 0.323-1.219 0.37-0.245 0.031-0.49 0.021-0.734 0.021h-2.516v-7.031h2.901c0.755 0.010 1.49 0.271 2.083 0.745 0.604 0.479 1.036 1.161 1.234 1.906zM6.219 13.859v4.281h1.271c0.542-0.021 1.047-0.276 1.38-0.698 0.573-0.719 0.667-1.708 0.245-2.521-0.266-0.51-0.74-0.885-1.302-1.021-0.224-0.052-0.453-0.042-0.677-0.042zM15.76 0h0.448c2.516 0.031 4.984 0.661 7.208 1.828 2.172 1.146 4.052 2.766 5.5 4.745 1.958 2.667 3.036 5.88 3.083 9.188v0.479c-0.052 3.984-1.589 7.807-4.313 10.714-2.969 3.167-7.104 4.99-11.443 5.047h-0.484c-1.891-0.026-3.76-0.391-5.526-1.073-2.151-0.839-4.094-2.13-5.698-3.781-1.719-1.771-3.010-3.906-3.771-6.25-0.484-1.505-0.74-3.073-0.766-4.656v-0.479c0.052-3.036 0.974-6 2.656-8.526 1.573-2.375 3.734-4.292 6.281-5.563 2.12-1.063 4.453-1.635 6.823-1.672zM15.557 1.641c-3.13 0.089-6.141 1.203-8.573 3.167-1.995 1.604-3.521 3.708-4.427 6.099-1.318 3.505-1.224 7.385 0.255 10.823 1.026 2.354 2.656 4.385 4.729 5.896 1.885 1.375 4.073 2.266 6.38 2.594 1.943 0.281 3.943 0.167 5.839-0.344 2.594-0.703 4.938-2.115 6.766-4.083 1.828-1.964 3.068-4.406 3.578-7.042 0.401-2.068 0.344-4.198-0.161-6.24-1.193-4.792-4.771-8.635-9.464-10.172-1.589-0.516-3.255-0.755-4.922-0.698z" />
                    </symbol>
                    <symbol id="msi" viewBox="0 0 174 40">
                        <path
                            d="M189.4,81.7l11.2-30.8a101.5,101.5,0,0,0-16.7,1.6L173.3,81.7Zm-1.9-39.2-2.1,6a95.7,95.7,0,0,1,16.7-1.7l2.2-5.9a102.3,102.3,0,0,0-16.8,1.6M129.2,74.4a87.6,87.6,0,0,0,15.5,1.4h0c5.5,0,9.4-1.2,9.8-4.9.2-1.8-1.4-3-4.5-4.7s-6.4-2.9-9.4-4.9a7.5,7.5,0,0,1-3.3-8.6c1.2-3.9,4-6.2,8.2-8.4s8.8-3.4,18.4-3.4a91.9,91.9,0,0,1,17.5,1.6l-2,5.7a89.3,89.3,0,0,0-15.5-1.4c-5.5,0-9.4,1.2-9.9,4.9-.2,1.7,1.5,3,4.5,4.7s6.5,2.9,9.5,4.9a7.5,7.5,0,0,1,3.3,8.6c-1.2,3.9-4,6.2-8.2,8.3s-8.8,3.5-18.4,3.5h0A91.9,91.9,0,0,1,127.2,80Zm-24.3,7.3L114.3,56c3.1-8.6-8.3-5.5-10.9-3.5s-5.6,5.1-7,8.8L89,81.7H72.9L82.2,56c3.1-8.6-8.3-5.5-10.8-3.5s-5.7,5.1-7,8.8L57,81.7H40.8L55.7,40.9c5.1,0,7.9,1.1,10.2,3.4a7.3,7.3,0,0,1,1.7,3.1,55.4,55.4,0,0,1,7.5-3.9,31.9,31.9,0,0,1,12.6-2.6c5.1,0,7.9,1.1,10.2,3.4a6.3,6.3,0,0,1,1.7,3.1,55.4,55.4,0,0,1,7.5-3.9,32.3,32.3,0,0,1,12.6-2.6c5.2,0,8,1.1,10.3,3.4s2.3,6.5,1.4,8.8L121,81.7Z"
                            transform="translate(-40.8 -40.8)" />
                    </symbol>

                    <symbol id="lenovo" viewBox="0 4 24 24">
                        <path xmlns="http://www.w3.org/2000/svg"
                            d="M27.005 15.229c-0.63-0.005-1.13 0.526-1.089 1.151-0.021 0.63 0.479 1.151 1.104 1.156 0.63 0.005 1.125-0.526 1.089-1.156 0.021-0.625-0.479-1.146-1.104-1.151zM18.323 15.229c-0.625-0.005-1.13 0.526-1.083 1.151-0.021 0.63 0.474 1.151 1.104 1.156 0.625 0.005 1.125-0.526 1.083-1.156 0.021-0.625-0.474-1.146-1.104-1.151zM8.979 15.156c-0.339-0.010-0.661 0.141-0.87 0.411-0.203 0.286-0.286 0.635-0.229 0.979l1.969-0.813c-0.146-0.349-0.49-0.578-0.87-0.578zM0 10.667v10.667h32v-10.667zM6.677 18.438h-3.708v-5.333h1.146v4.297h2.563zM9.099 17.609c0.432 0.005 0.854-0.146 1.177-0.438l0.714 0.547c-0.51 0.505-1.193 0.786-1.911 0.786-1.224 0.12-2.297-0.823-2.333-2.052-0.036-1.234 0.979-2.234 2.214-2.188 0.609-0.031 1.203 0.214 1.62 0.667 0.271 0.328 0.443 0.724 0.495 1.146l-2.885 1.203c0.245 0.224 0.573 0.344 0.911 0.328zM15.609 18.438h-1.13v-2.339c-0.016-0.5-0.443-0.891-0.948-0.865-0.5-0.031-0.927 0.365-0.932 0.865v2.339h-1.125v-4.109h1.13v0.589c0.318-0.411 0.813-0.651 1.333-0.656 0.927-0.052 1.698 0.703 1.667 1.63zM18.255 18.505c-1.87-0.078-2.734-2.359-1.38-3.656 1.349-1.292 3.594-0.339 3.594 1.531-0.005 1.208-1.010 2.172-2.214 2.125zM21.984 18.432l-1.688-4.104h1.286l1.021 2.802 1.021-2.802h1.286l-1.693 4.104zM26.932 18.505c-1.865-0.078-2.729-2.359-1.38-3.656 1.354-1.292 3.594-0.339 3.594 1.531-0.005 1.208-1.005 2.172-2.214 2.125zM29.599 17.948h-0.188v0.49h-0.109v-0.49h-0.182v-0.104h0.479zM30.323 18.438h-0.109v-0.422l-0.182 0.286h-0.016l-0.182-0.286v0.422h-0.109v-0.594h0.12l0.177 0.281 0.177-0.281h0.12z" />
                    </symbol>

                    <symbol id="apple" viewBox="-20 5 80 24">
                        <path xmlns="http://www.w3.org/2000/svg"
                            d="M9.438 31.401c-0.63-0.422-1.193-0.938-1.656-1.536-0.516-0.615-0.984-1.266-1.422-1.938-1.021-1.495-1.818-3.125-2.375-4.849-0.667-2-0.99-3.917-0.99-5.792 0-2.094 0.453-3.922 1.339-5.458 0.651-1.198 1.625-2.203 2.797-2.906 1.135-0.708 2.453-1.094 3.786-1.12 0.469 0 0.974 0.068 1.51 0.198 0.385 0.109 0.854 0.281 1.427 0.495 0.729 0.281 1.13 0.453 1.266 0.495 0.427 0.156 0.786 0.224 1.068 0.224 0.214 0 0.516-0.068 0.859-0.172 0.193-0.068 0.557-0.188 1.078-0.411 0.516-0.188 0.922-0.349 1.245-0.469 0.495-0.146 0.974-0.281 1.401-0.349 0.521-0.078 1.036-0.104 1.531-0.063 0.948 0.063 1.813 0.266 2.589 0.557 1.359 0.547 2.458 1.401 3.276 2.615-0.349 0.214-0.667 0.458-0.969 0.734-0.651 0.573-1.198 1.25-1.641 2.005-0.573 1.026-0.865 2.188-0.859 3.359 0.021 1.443 0.391 2.714 1.12 3.813 0.521 0.802 1.208 1.484 2.047 2.047 0.417 0.281 0.776 0.474 1.12 0.604-0.161 0.5-0.333 0.984-0.536 1.464-0.464 1.078-1.016 2.109-1.667 3.083-0.578 0.839-1.031 1.464-1.375 1.88-0.536 0.635-1.052 1.12-1.573 1.458-0.573 0.38-1.25 0.583-1.938 0.583-0.469 0.021-0.932-0.042-1.38-0.167-0.385-0.13-0.766-0.271-1.141-0.432-0.391-0.177-0.792-0.333-1.203-0.453-0.51-0.135-1.031-0.198-1.552-0.198-0.536 0-1.057 0.068-1.547 0.193-0.417 0.12-0.818 0.26-1.214 0.432-0.557 0.234-0.927 0.391-1.141 0.458-0.427 0.125-0.87 0.203-1.318 0.229-0.693 0-1.339-0.198-1.979-0.599zM18.578 6.786c-0.906 0.453-1.771 0.646-2.63 0.583-0.135-0.865 0-1.75 0.359-2.719 0.318-0.828 0.745-1.573 1.333-2.24 0.609-0.693 1.344-1.266 2.172-1.677 0.88-0.453 1.719-0.698 2.521-0.734 0.104 0.906 0 1.797-0.333 2.76-0.307 0.854-0.76 1.641-1.333 2.344-0.583 0.693-1.302 1.266-2.115 1.682z" />
                    </symbol>

                    <symbol id="acer" viewBox="0 0 24 24">
                        <path xmlns="http://www.w3.org/2000/svg"
                            d="M23.943 9.364c-.085-.113-.17-.198-.595-.226-.113 0-.453-.029-1.048-.029-1.56 0-2.636.482-3.175 1.417.142-.935-.765-1.417-2.749-1.417-2.324 0-3.798.935-4.393 2.834-.226.709-.226 1.276-.056 1.73h-.567c-.425.027-.992.056-1.36.056-.85 0-1.39-.142-1.588-.425-.17-.255-.17-.737.057-1.446.368-1.162 1.247-1.672 2.664-1.672.737 0 1.445.085 1.445.085.085 0 .142-.113.142-.198l-.028-.085-.057-.397c-.028-.255-.227-.397-.567-.453-.311-.029-.567-.029-.907-.029h-.028c-1.842 0-3.146.624-3.854 1.814.255-1.219-.596-1.814-2.551-1.814-1.105 0-1.9.029-2.353.085-.368.057-.595.199-.68.454l-.17.51c-.028.085.029.142.142.142.085 0 .425-.057.992-.086a24.816 24.816 0 0 1 1.672-.085c1.077 0 1.559.284 1.389.822-.029.114-.114.199-.255.227-1.02.17-1.842.284-2.438.369-1.7.226-2.692.736-2.947 1.587-.369 1.162.538 1.728 2.72 1.728 1.078 0 2.013-.056 2.75-.198.425-.085.652-.17.737-.453l.396-1.304c-.028 1.304.85 1.955 2.721 1.955.794 0 1.559-.028 1.927-.085.369-.056.567-.141.652-.425l.085-.396c.397.623 1.276.935 2.608.935 1.417 0 2.239-.029 2.465-.114a.523.523 0 0 0 .369-.311l.028-.085.17-.539c.029-.085-.028-.142-.142-.142l-.906.057c-.596.029-1.077.057-1.418.057-.651 0-1.076-.057-1.332-.142-.368-.142-.538-.397-.51-.822l2.863-.368c1.275-.17 2.154-.567 2.579-1.19l-.992 3.315c-.028.057 0 .114.028.142.029.028.085.057.199.057h1.19c.198 0 .283-.114.312-.199l1.048-3.656c.142-.481.567-.708 1.36-.708.71 0 1.22 0 1.56.028h.028c.057 0 .17-.028.255-.17l.17-.51c0-.085 0-.17-.057-.227zM4.841 13.73c-.368.057-.907.085-1.587.085-1.219 0-1.729-.255-1.587-.737.113-.34.425-.567.935-.624l2.75-.368zm12.669-2.95c-.114.369-.652.624-1.616.766l-2.295.311.056-.198c.199-.624.454-1.02.794-1.247.34-.227.907-.34 1.7-.34 1.05.028 1.503.255 1.36.708z" />
                    </symbol>

                    <symbol id="asus" viewBox="0 0 24 24">
                        <path xmlns="http://www.w3.org/2000/svg"
                            d="M23.904 10.788V9.522h-4.656c-.972 0-1.41.6-1.482 1.182v.018-1.2h-1.368v1.266h1.362zm-6.144.456-1.368-.078v1.458c0 .456-.228.594-1.02.594H14.28c-.654 0-.93-.186-.93-.594v-1.596l-1.386-.102v1.812h-.03c-.078-.528-.276-1.14-1.596-1.23L6 11.22c0 .666.474 1.062 1.218 1.14l3.024.306c.24.018.414.09.414.288 0 .216-.18.24-.456.24H5.946V11.22l-1.386-.09v3.348h5.646c1.26 0 1.662-.654 1.722-1.2h.03c.156.864.912 1.2 2.19 1.2h1.41c1.494 0 2.202-.456 2.202-1.524zm4.398.258-4.338-.258c0 .666.438 1.11 1.182 1.17l3.09.24c.24.018.384.078.384.276 0 .186-.168.258-.516.258h-4.212v1.29h4.302c1.356 0 1.95-.474 1.95-1.554 0-.972-.534-1.338-1.842-1.422zm-10.194-1.98h1.386v1.266h-1.386zM3.798 11.07l-1.506-.15L0 14.478h1.686zm7.914-1.548h-4.23c-.984 0-1.416.612-1.518 1.2v-1.2H3.618c-.33 0-.486.102-.642.33l-.648.936h9.384z" />
                    </symbol>

                    <symbol id="intel" viewBox="0 0 24 24">
                        <path xmlns="http://www.w3.org/2000/svg"
                            d="M20.42 7.345v9.18h1.651v-9.18zM0 7.475v1.737h1.737V7.474zm9.78.352v6.053c0 .513.044.945.13 1.292.087.34.235.618.44.828.203.21.475.359.803.451.334.093.754.136 1.255.136h.216v-1.533c-.24 0-.445-.012-.593-.037a.672.672 0 0 1-.39-.173.693.693 0 0 1-.173-.377 4.002 4.002 0 0 1-.037-.606v-2.182h1.193v-1.416h-1.193V7.827zm-3.505 2.312c-.396 0-.76.08-1.082.241-.327.161-.6.384-.822.668l-.087.117v-.902H2.658v6.256h1.639v-3.214c.018-.588.16-1.02.433-1.299.29-.297.642-.445 1.044-.445.476 0 .841.149 1.082.433.235.284.359.686.359 1.2v3.324h1.663V12.97c.006-.89-.229-1.595-.686-2.09-.458-.495-1.1-.742-1.917-.742zm10.065.006a3.252 3.252 0 0 0-2.306.946c-.29.29-.525.637-.692 1.033a3.145 3.145 0 0 0-.254 1.273c0 .452.08.878.241 1.274.161.395.39.742.674 1.032.284.29.637.526 1.045.693.408.173.86.26 1.342.26 1.397 0 2.262-.637 2.782-1.23l-1.187-.904c-.248.297-.841.699-1.583.699-.464 0-.847-.105-1.138-.321a1.588 1.588 0 0 1-.593-.872l-.019-.056h4.915v-.587c0-.451-.08-.872-.235-1.267a3.393 3.393 0 0 0-.661-1.033 3.013 3.013 0 0 0-1.02-.692 3.345 3.345 0 0 0-1.311-.248zm-16.297.118v6.256h1.651v-6.256zm16.278 1.286c1.132 0 1.664.797 1.664 1.255l-3.32.006c0-.458.525-1.255 1.656-1.261zm7.073 3.814a.606.606 0 0 0-.606.606.606.606 0 0 0 .606.606.606.606 0 0 0 .606-.606.606.606 0 0 0-.606-.606zm-.008.105a.5.5 0 0 1 .002 0 .5.5 0 0 1 .5.501.5.5 0 0 1-.5.5.5.5 0 0 1-.5-.5.5.5 0 0 1 .498-.5zm-.233.155v.699h.13v-.285h.093l.173.285h.136l-.18-.297a.191.191 0 0 0 .118-.056c.03-.03.05-.074.05-.136 0-.068-.02-.117-.063-.154-.037-.038-.105-.056-.185-.056zm.13.099h.154c.019 0 .037.006.056.012a.064.064 0 0 1 .037.031c.013.013.012.031.012.056a.124.124 0 0 1-.012.055.164.164 0 0 1-.037.031c-.019.006-.037.013-.056.013h-.154z" />
                    </symbol>

                    <symbol id="amd" viewBox="0 0 24 24">
                        <path xmlns="http://www.w3.org/2000/svg"
                            d="m18.324 9.137 1.559 1.56h2.556v2.557L24 14.814V9.137zM2 9.52l-2 4.96h1.309l.37-.982H3.9l.408.982h1.338L3.432 9.52zm4.209 0v4.955h1.238v-3.092l1.338 1.562h.188l1.338-1.556v3.091h1.238V9.52H10.47l-1.592 1.845L7.287 9.52zm6.283 0v4.96h2.057c1.979 0 2.88-1.046 2.88-2.472 0-1.36-.937-2.488-2.747-2.488zm1.237.91h.792c1.17 0 1.63.711 1.63 1.57 0 .728-.372 1.572-1.616 1.572h-.806zm-10.985.273.791 1.932H2.008zm17.137.307-1.604 1.603v2.25h2.246l1.604-1.607h-2.246z" />
                    </symbol>
                </defs>
            </svg>



            <script>
            /*
 JS to toggle scroll axis styles
*/
            const control = document.getElementById("direction-toggle");
            const marquees = document.querySelectorAll(".marquee");
            const wrapper = document.querySelector(".wrapper");

            control.addEventListener("click", () => {
                control.classList.toggle("toggle--vertical");
                wrapper.classList.toggle("wrapper--vertical");
                [...marquees].forEach((marquee) =>
                    marquee.classList.toggle("marquee--vertical")
                );
            });
            </script>
        </div>
        <?php if (isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 0): ?>
        <div class="sticky-bar">
            Please verify your email
            <form action="resend_verification.php" method="post" style="display:inline;">
                <button type="submit">Resend Email</button>
            </form>
        </div>
        <?php endif; ?>


        <style>
        .Hproducts {
            white-space: nowrap;
            position: relative;
            padding-bottom: 10px;
            /* Space for scrollbar */
        }

        .Hproducts-container {
            display: flex;
            scroll-behavior: smooth;
            overflow-x: auto;
        }

        .Hproducts-container-wrapper {
            display: flex;
            align-items: center;
        }

        .Hproducts-container::-webkit-scrollbar {
            display: none;
            /* Hide scrollbar */
        }

        .Hproducts::-webkit-scrollbar {
            display: none;
        }

        .scroll-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            transition: background 0.3s;
            z-index: 90;
        }

        .scroll-btn svg {
            width: 30px;
            height: 30px;
            fill: #333;
        }

        .scroll-btn:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .left-btn {
            position: absolute;
            left: 0;
        }

        .right-btn {
            position: absolute;
            right: 0;
        }
        </style>

        <span class="title-shop">
            <h1>Letest Collection</h1>
        </span>

        <section class="Hproducts">
            <div class="Hproducts-container-wrapper">
                <button class="scroll-btn left-btn">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50"
                        style="enable-background:new 0 0 50 50;" xml:space="preserve">
                        <style type="text/css">
                        .st0 {
                            fill: #F5F5F5;
                        }

                        .st1 {
                            fill: #FFFFFF;
                            stroke: #252525;
                            stroke-width: 4;
                            stroke-linecap: round;
                            stroke-miterlimit: 10;
                        }

                        .st2 {
                            fill: none;
                            stroke: #252525;
                            stroke-width: 4;
                            stroke-linecap: round;
                            stroke-linejoin: round;
                            stroke-miterlimit: 10;
                        }
                        </style>
                        <g>
                            <path class="st0" d="M41.56,50H8.44C3.78,50,0,46.22,0,41.56L0,8.44C0,3.78,3.78,0,8.44,0l33.12,0C46.22,0,50,3.78,50,8.44v33.12
		C50,46.22,46.22,50,41.56,50z" />
                            <line class="st1" x1="41.67" y1="25" x2="10.14" y2="25" />
                            <polyline class="st2" points="21.46,14 8.57,25 21.46,36 	" />
                        </g>
                    </svg>
                </button>
                <div class="Hproducts-container">
                    <?php
        if ($result !== null && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) :
                // Check if the product is whitelisted for the current user
                $is_whitelisted = false;
                if ($user_id) {
                    $whitelist_sql = "SELECT * FROM whitelist WHERE user_id = '$user_id' AND product_id = '{$row['product_id']}'";
                    $whitelist_result = $conn->query($whitelist_sql);
                    if ($whitelist_result->num_rows > 0) {
                        $is_whitelisted = true;
                    }
                }
        ?>
                    <div class="products-card">

                        <div class="card-img">

                            <div class="r1_l_c">
                                <div class="like_button">
                                    <button
                                        onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')"
                                        class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>"
                                        aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
                                        <span class="material-icons" style="color: #3e0c40;">
                                            <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
                                        </span>
                                    </button>
                                </div>

                                <a href="../pd/?product_id=<?php echo $row['product_id']; ?>">

                                    <?php 
                                if ($row['choiced'] == 0 ) {
                                    echo '';
                                } else {
                                    echo ' <div class="ch">
                                    <img width="130px" src="../assets/ch.svg">
    
                                    </div> ';
                                }
                                ?>
                            </div>
                            <img style="width: 100%;height: auto;"
                                src="<?php echo htmlspecialchars($row['image_main']); ?>"
                                alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                        </div>

                        <div class="card-title">
                            <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                        </div>
                        <div class="card-subtitle">
                            <?php echo htmlspecialchars($row['short_des'] ?? $row['description']); ?></div>
                        <hr class="card-divider">
                        <div class="card-footer">
                            <div class="card-price"><span>₹</span><?php echo htmlspecialchars($row['price']); ?>
                            </div>
                            <button class="card-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path
                                        d="m397.78 316h-205.13a15 15 0 0 1 -14.65-11.67l-34.54-150.48a15 15 0 0 1 14.62-18.36h274.27a15 15 0 0 1 14.65 18.36l-34.6 150.48a15 15 0 0 1 -14.62 11.67zm-193.19-30h181.25l27.67-120.48h-236.6z">
                                    </path>
                                    <path
                                        d="m222 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                                    </path>
                                    <path
                                        d="m368.42 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                                    </path>
                                    <path
                                        d="m158.08 165.49a15 15 0 0 1 -14.23-10.26l-25.71-77.23h-47.44a15 15 0 1 1 0-30h58.3a15 15 0 0 1 14.23 10.26l29.13 87.49a15 15 0 0 1 -14.23 19.74z">
                                    </path>
                                </svg>
                            </button>

                        </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                    <?php } else { ?>
                    <div class="ab-o-oa" aria-hidden="true">
                        <div class="ZAnhre">
                            <img class="wF0Mmb" src="../assets/l_p_n_found.svg" width="300px" height="300px" alt="">
                        </div>
                        <div class="ab-o-oa-r">
                            <div class="ab-o-oa-qc-V">No product Found</div>
                            <div class="ab-o-oa-qc-r"> matching your search criteria.</div>
                        </div>
                    </div>
                    <style>
                    .ab-o-oa {
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

                    .ab-o-oa-r {
                        display: contents;
                    }

                    .ab-o-oa-qc-V {
                        font-weight: 800;

                    }

                    .ab-o-oa-qc-r {
                        font-weight: normal;

                    }
                    </style>
                    <!-- <p>No products found matching your search criteria.</p> -->
                    <?php } ?>
                </div>


                <button class="scroll-btn right-btn">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50"
                        style="enable-background:new 0 0 50 50;" xml:space="preserve">
                        <style type="text/css">
                        .st0 {
                            fill: #F5F5F5;
                        }

                        .st1 {
                            fill: #FFFFFF;
                            stroke: #252525;
                            stroke-width: 4;
                            stroke-linecap: round;
                            stroke-miterlimit: 10;
                        }

                        .st2 {
                            fill: none;
                            stroke: #252525;
                            stroke-width: 4;
                            stroke-linecap: round;
                            stroke-linejoin: round;
                            stroke-miterlimit: 10;
                        }
                        </style>
                        <g>
                            <path class="st0" d="M41.56,50H8.44C3.78,50,0,46.22,0,41.56L0,8.44C0,3.78,3.78,0,8.44,0l33.12,0C46.22,0,50,3.78,50,8.44v33.12
		C50,46.22,46.22,50,41.56,50z" />
                            <line class="st1" x1="8.57" y1="25" x2="40.11" y2="25" />
                            <polyline class="st2" points="28.79,36 41.67,25 28.79,14 	" />
                        </g>
                    </svg>


                </button>
            </div>
        </section>












        <script>
        document.querySelector('.left-btn').addEventListener('click', function() {
            document.querySelector('.Hproducts-container').scrollBy({
                left: -300, // Adjust the scroll amount as needed
                behavior: 'smooth'
            });
        });

        document.querySelector('.right-btn').addEventListener('click', function() {
            document.querySelector('.Hproducts-container').scrollBy({
                left: 300, // Adjust the scroll amount as needed
                behavior: 'smooth'
            });
        });
        </script>

        <?php if (isset($_SESSION['recently_viewed']) && !empty($_SESSION['recently_viewed'])): ?>
        <?php 
    $recently_viewed = array_filter($_SESSION['recently_viewed']);  // Filter out any empty values
    if (!empty($recently_viewed)): 
    ?>
        <!-- Recently Viewed Products Section -->
        <span class="title-shop">
            <h2>Recently Viewed Products</h2>
        </span>

        <section class="products">
            <div class="k-pp-c">
                <?php
                $ids = implode(",", $recently_viewed);
                $query = "SELECT * FROM products WHERE product_id IN ($ids) ORDER BY FIELD(product_id, $ids)";
                
                $result = $conn->query($query);
                if ($result):
                    while ($row = $result->fetch_assoc()):
                ?>
                <div class="card-of-product">

                    <div class="r1_l_c">
                        <div class="like_button">
                            <button
                                onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')"
                                class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>"
                                aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
                                <span class="material-icons" style="color: #3e0c40;">
                                    <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
                                </span>
                            </button>
                        </div>

                        <a href="../pd/?product_id=<?php echo $row['product_id']; ?>">
                            <?php 
                                    if ($row['choiced'] == 0 ) {
                                        echo '';
                                    } else {
                                        echo ' <div class="choiced-product-card">
                                        <img src="../assets/ch.svg">
                                        
                                        </div> ';
                                    }
                                    ?>
                    </div>
                    <div class="img-of-product-card">
                        <img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($row['image_main']); ?>"
                            alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                    </div>

                    <div class="title-of-product-card">
                        <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                    </div>

                    <hr class="card-divider">
                    <div class="footer-of-product-card">
                        <div class="price-of-product-card"><span>₹</span><?php echo htmlspecialchars($row['price']); ?>
                        </div>

                    </div>
                    </a>
                </div>
                <?php
                    endwhile;
                else:
                    echo "Query error: " . $conn->error;
                endif;
                ?>
            </div>
        </section>
        <!-- End Recently Viewed Products Section -->
        <?php 
    endif; 
    ?>
        <?php endif; ?>



        <!-- refurbished -->
        <span class="title-shop">
            <h1>The Best in <strong>Refurbished</strong></h1>
        </span>

        <section class="products">
            <?php
            while ($row = mysqli_fetch_assoc($ref)) :
                // Check if the product is whitelisted for the current user
                $is_whitelisted = false;
                if ($user_id) {
                    $whitelist_sql = "SELECT * FROM whitelist WHERE user_id = '$user_id' AND product_id = '{$row['product_id']}'";
                    $whitelist_result = $conn->query($whitelist_sql);
                    if ($whitelist_result->num_rows > 0) {
                        $is_whitelisted = true;
                    }
                }
        ?>
            <div class="products-card">
                <div class="card-img">

                    <div class="r1_l_c">
                        <div class="like_button">
                            <button
                                onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')"
                                class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>"
                                aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
                                <span class="material-icons" style="color: #3e0c40;">
                                    <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
                                </span>
                            </button>
                        </div>

                        <script src="./assets/js/fav.js"></script>


                        <a href="../pd/?product_id=<?php echo $row['product_id']; ?>">

                            <?php 
                                    if ($row['choiced'] == 0 ) {
                                        echo '';
                                    } else {
                                        echo ' <div class="ch">
                                        <img width="130px" src="../assets/ch.svg">
        
                                        </div> ';
                                    }
                                    ?>
                    </div>
                    <img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($row['image_main']); ?>"
                        alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                </div>

                <div class="card-title">
                    <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                </div>
                <div class="card-subtitle"><?php echo htmlspecialchars($row['short_des'] ?? $row['description']); ?>
                </div>
                <hr class="card-divider">
                <div class="card-footer">
                    <div class="card-price"><span>₹</span><?php echo htmlspecialchars($row['price']); ?></div>
                    <button class="card-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path
                                d="m397.78 316h-205.13a15 15 0 0 1 -14.65-11.67l-34.54-150.48a15 15 0 0 1 14.62-18.36h274.27a15 15 0 0 1 14.65 18.36l-34.6 150.48a15 15 0 0 1 -14.62 11.67zm-193.19-30h181.25l27.67-120.48h-236.6z">
                            </path>
                            <path
                                d="m222 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m368.42 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m158.08 165.49a15 15 0 0 1 -14.23-10.26l-25.71-77.23h-47.44a15 15 0 1 1 0-30h58.3a15 15 0 0 1 14.23 10.26l29.13 87.49a15 15 0 0 1 -14.23 19.74z">
                            </path>
                        </svg>
                    </button>

                </div>
                </a>
            </div>
            <?php endwhile; ?>
        </section>

        <!-- end refurbished -->








        <!-- refurbished -->
        <span class="title-shop">
            <h1>Explore Service categories</h1>
            <hr>
        </span>

        <section class="products">
            <?php  while ($row = mysqli_fetch_assoc($ser)) : ?>
            <div class="products-card">
                <div class="card-img">
                    <a href="../services/home/home.php?service_category_id=<?= $row['id'] ?>">

                        <img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($row['img']); ?>"
                            alt="<?php echo htmlspecialchars($row['category_name']); ?>">
                </div>

                <div class="card-title"><?php echo htmlspecialchars($row['category_name']); ?> Service
                </div>
                <!-- <div class="card-subtitle"><?php echo htmlspecialchars($row['short_des'] ?? $row['description']); ?></div> -->
                <hr class="card-divider">
                <div style="bottom:0px" class="card-footer">
                    <!-- <div class="card-price"><span>₹</span><?php echo htmlspecialchars($row['price']); ?></div> -->
                    <button style="
                    --tw-text-opacity: 1;
    color: rgb(255 255 255 / var(--tw-text-opacity));font-weight: 700; width:100%;text-align: center;padding-top: 0.5rem;
    padding-bottom: 0.5rem;padding-left: 1rem;
    padding-right: 1rem;--tw-bg-opacity: 1;
    background-color: rgb(59 130 246 / var(--tw-bg-opacity));border-radius: 0.25rem;cursor: pointer;s">
                        Explore
                    </button>

                </div>
                </a>
            </div>
            <?php endwhile; ?>
        </section>

        <!-- end refurbished -->



        <span class="title-shop">
            <h1>Top <strong>Blog</strong></h1>
        </span>

        <?php 
  // Fetch all blog posts if no search query is provided
  $sql = "SELECT id, title, category, topic, keywords, short_description, content, author, main_photo, created_at, likes 
  FROM blog_posts
  ORDER BY likes DESC, created_at DESC LIMIT 03";
$result = $conn->query($sql);

?>

        <div class="cont">
            <?php if ($result->num_rows > 0): ?>
            <div class="grid-container">
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="card-blog">
                    <a href="../blog/page.php?id=<?php echo $row['id']; ?>">
                        <img src="../blog/uploads/<?php echo htmlspecialchars($row['main_photo']); ?>" alt="Post"
                            class="card-img">
                        <p style="cursor:pointer;"
                            onclick="window.location.href='/category/?category=<?php echo htmlspecialchars($row['category']); ?>'"
                            class="ct">
                            <?php echo htmlspecialchars($row['category']); ?>
                        </p>


                        <h2 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p class="short-description"><?php echo htmlspecialchars($row['short_description']); ?></p>
                        <div class="card-author">
                            <p>
                                <span class="author-name"><?php echo htmlspecialchars($row['author']); ?></span><br>
                                <span class="author-date"> on
                                    <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></span>
                            </p>

                    </a>
                    <p style="float: right; padding:1%; " class="likes btn-trick-new"><a
                            href="../blog/like_post.php?id=<?php echo $row['id']; ?>" class="like-button">
                            <svg fill="#3c0e40" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" width="50px" height="50px"
                                viewBox="-122.88 -122.88 757.76 757.76" enable-background="new 0 0 512 512"
                                xml:space="preserve" stroke="#3c0e40" stroke-width="0.00512" transform="rotate(0)">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <g>
                                        <g>
                                            <path
                                                d="M344,288c4.781,0,9.328,0.781,13.766,1.922C373.062,269.562,384,245.719,384,218.625C384,177.422,351.25,144,310.75,144 c-21.875,0-41.375,10.078-54.75,25.766C242.5,154.078,223,144,201.125,144C160.75,144,128,177.422,128,218.625 C128,312,256,368,256,368s14-6.203,32.641-17.688C288.406,348.203,288,346.156,288,344C288,313.125,313.125,288,344,288z">
                                            </path>
                                            <path
                                                d="M256,0C114.609,0,0,114.609,0,256s114.609,256,256,256s256-114.609,256-256S397.391,0,256,0z M256,472 c-119.297,0-216-96.703-216-216S136.703,40,256,40s216,96.703,216,216S375.297,472,256,472z">
                                            </path>
                                        </g>
                                        <path
                                            d="M344,304c-22.094,0-40,17.906-40,40s17.906,40,40,40s40-17.906,40-40S366.094,304,344,304z M368,352h-16v16h-16v-16h-16 v-16h16v-16h16v16h16V352z">
                                        </path>
                                    </g>
                                </g>
                            </svg>
                            <!-- [<?php echo htmlspecialchars($row['likes']); ?>] -->
                        </a> </p>
                </div>
            </div>

            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="ab-o-oa" aria-hidden="true">
            <div class="ZAnhre">
                <img class="wF0Mmb" src="../assets/empty_blog.svg" width="300px" height="300px" alt="">
            </div>
            <div class="ab-o-oa-r">
                <div class="ab-o-oa-qc-V">No Blog post found</div>
                <div class="ab-o-oa-qc-r"> matching your search criteria.</div>
            </div>
        </div>
        <style>
        .ab-o-oa {
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

        .ab-o-oa-r {
            display: contents;
        }

        .ab-o-oa-qc-V {
            font-weight: 800;

        }

        .ab-o-oa-qc-r {
            font-weight: normal;

        }
        </style>
        <?php endif; ?>

        </div>







        <!-- Asus -->

        <span class="title-shop">
            <h1>The Powerful in <strong>Asus</strong> Modal's</h1>
        </span>


        <section class="Hproducts">
            <div class="Hproducts-container-wrapper">
                <button class="scroll-btn left-btn">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50"
                        style="enable-background:new 0 0 50 50;" xml:space="preserve">
                        <style type="text/css">
                        .st0 {
                            fill: #F5F5F5;
                        }

                        .st1 {
                            fill: #FFFFFF;
                            stroke: #252525;
                            stroke-width: 4;
                            stroke-linecap: round;
                            stroke-miterlimit: 10;
                        }

                        .st2 {
                            fill: none;
                            stroke: #252525;
                            stroke-width: 4;
                            stroke-linecap: round;
                            stroke-linejoin: round;
                            stroke-miterlimit: 10;
                        }
                        </style>
                        <g>
                            <path class="st0" d="M41.56,50H8.44C3.78,50,0,46.22,0,41.56L0,8.44C0,3.78,3.78,0,8.44,0l33.12,0C46.22,0,50,3.78,50,8.44v33.12
		C50,46.22,46.22,50,41.56,50z" />
                            <line class="st1" x1="41.67" y1="25" x2="10.14" y2="25" />
                            <polyline class="st2" points="21.46,14 8.57,25 21.46,36 	" />
                        </g>
                    </svg>

                </button>
                <div class="Hproducts-container">
                    <?php
            while ($row = mysqli_fetch_assoc($asus)) :
                // Check if the product is whitelisted for the current user
                $is_whitelisted = false;
                if ($user_id) {
                    $whitelist_sql = "SELECT * FROM whitelist WHERE user_id = '$user_id' AND product_id = '{$row['product_id']}'";
                    $whitelist_result = $conn->query($whitelist_sql);
                    if ($whitelist_result->num_rows > 0) {
                        $is_whitelisted = true;
                    }
                }
        ?>
                    <div class="products-card">
                        <div class="card-img">

                            <div class="r1_l_c">
                                <div class="like_button">
                                    <button
                                        onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')"
                                        class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>"
                                        aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
                                        <span class="material-icons" style="color: #3e0c40;">
                                            <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
                                        </span>
                                    </button>
                                </div>

                                <script src="./assets/js/fav.js"></script>
                                <a href="../pd/?product_id=<?php echo $row['product_id']; ?>">

                                    <?php 
                                if ($row['choiced'] == 0 ) {
                                    echo '';
                                } else {
                                    echo ' <div class="ch">
                                    <img width="130px" src="../assets/ch.svg">
    
                                    </div> ';
                                }
                                ?>
                            </div>
                            <img style="width: 100%;height: auto;"
                                src="<?php echo htmlspecialchars($row['image_main']); ?>"
                                alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                        </div>

                        <div class="card-title">
                            <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                        </div>
                        <div class="card-subtitle">
                            <?php echo htmlspecialchars($row['short_des'] ?? $row['description']); ?></div>
                        <hr class="card-divider">
                        <div class="card-footer">
                            <div class="card-price"><span>₹</span><?php echo htmlspecialchars($row['price']); ?>
                            </div>
                            <button class="card-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path
                                        d="m397.78 316h-205.13a15 15 0 0 1 -14.65-11.67l-34.54-150.48a15 15 0 0 1 14.62-18.36h274.27a15 15 0 0 1 14.65 18.36l-34.6 150.48a15 15 0 0 1 -14.62 11.67zm-193.19-30h181.25l27.67-120.48h-236.6z">
                                    </path>
                                    <path
                                        d="m222 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                                    </path>
                                    <path
                                        d="m368.42 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                                    </path>
                                    <path
                                        d="m158.08 165.49a15 15 0 0 1 -14.23-10.26l-25.71-77.23h-47.44a15 15 0 1 1 0-30h58.3a15 15 0 0 1 14.23 10.26l29.13 87.49a15 15 0 0 1 -14.23 19.74z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        </a>
                    </div>
                    <?php endwhile; ?>
                </div>
                <button class="scroll-btn right-btn">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 50 50"
                        style="enable-background:new 0 0 50 50;" xml:space="preserve">
                        <style type="text/css">
                        .st0 {
                            fill: #F5F5F5;
                        }

                        .st1 {
                            fill: #FFFFFF;
                            stroke: #252525;
                            stroke-width: 4;
                            stroke-linecap: round;
                            stroke-miterlimit: 10;
                        }

                        .st2 {
                            fill: none;
                            stroke: #252525;
                            stroke-width: 4;
                            stroke-linecap: round;
                            stroke-linejoin: round;
                            stroke-miterlimit: 10;
                        }
                        </style>
                        <g>
                            <path class="st0" d="M41.56,50H8.44C3.78,50,0,46.22,0,41.56L0,8.44C0,3.78,3.78,0,8.44,0l33.12,0C46.22,0,50,3.78,50,8.44v33.12
		C50,46.22,46.22,50,41.56,50z" />
                            <line class="st1" x1="8.57" y1="25" x2="40.11" y2="25" />
                            <polyline class="st2" points="28.79,36 41.67,25 28.79,14 	" />
                        </g>
                    </svg>


                </button>
            </div>
        </section>


        <!-- end asus -->















        <!-- Why choose us -->
        <!-- <div class='container-wcu'> <div class="text-center mb-2-8 mb-lg-6">
        <h2 class="display-18 display-md-16 display-lg-14 font-weight-700">Why choose <strong class="text-primary font-weight-700">Us</strong></h2>
        <span>The trusted source for why choose us</span>
    </div>
  
    <div class="row align-items-center">
       
    <div class="col-sm-6 col-lg-4 mb-2-9 mb-sm-0">
            <div class="pr-md-3">
              
            <div class="text-center text-sm-right mb-2-9">
                    <div class="mb-4">
                        <img src="https://www.bootdey.com/image/80x80/FFB6C1/000000" alt="..." class="rounded-circle">
                    </div>
                    <h4 class="sub-info">Residential Cleaning</h4>
                    <p class="display-30 mb-0">Roin gravida nibh vel velit auctor aliquetenean sollicitudin, lorem qui bibendum auctor.</p>
                </div>
               
                <div class="text-center text-sm-right">
                    <div class="mb-4">
                        <img src="https://www.bootdey.com/image/80x80/87CEFA/000000" alt="..." class="rounded-circle">
                    </div>
                    <h4 class="sub-info">Commercial Cleaning</h4>
                    <p class="display-30 mb-0">Gravida roin nibh vel velit auctor aliquetenean sollicitudin, lorem qui bibendum auctor.</p>
                </div>

            </div>
        </div>

        <div class="col-lg-4 d-none d-lg-block">
            <div class="why-choose-center-image">
                <img src="../assets/img/024154df.png" alt="..." class="rounded-circle">
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="pl-md-3">
                <div class="text-center text-sm-left mb-2-9">
                    <div class="mb-4">
                        <img src="https://www.bootdey.com/image/80x80/8A2BE2/000000" alt="..." class="rounded-circle">
                    </div>
                    <h4 class="sub-info">Washing services</h4>
                    <p class="display-30 mb-0">Nibh roin gravida vel velit auctor aliquetenean sollicitudin, lorem qui bibendum auctor.</p>
                </div>

                <div class="text-center text-sm-left">
                    <div class="mb-4">
                        <img src="https://www.bootdey.com/image/80x80/20B2AA/000000" alt="..." class="rounded-circle">
                    </div>
                    <h4 class="sub-info">Carpet cleaning</h4>
                    <p class="display-30 mb-0">Vel proin gravida nibh velit auctor aliquetenean sollicitudin, lorem qui bibendum auctor.</p>
                </div>

            </div>
        </div>

    </div> -->

        <!-- end Why choose us -->





















        <!-- Lenovo products -->
        <span class="title-shop">
            <h1>Lenovo Collections</h1>
        </span>


        <section class="products">
            <?php
            while ($row = mysqli_fetch_assoc($len)) :
                // Check if the product is whitelisted for the current user
                $is_whitelisted = false;
                if ($user_id) {
                    $whitelist_sql = "SELECT * FROM whitelist WHERE user_id = '$user_id' AND product_id = '{$row['product_id']}'";
                    $whitelist_result = $conn->query($whitelist_sql);
                    if ($whitelist_result->num_rows > 0) {
                        $is_whitelisted = true;
                    }
                }
        ?>
            <div class="products-card">
                <div class="card-img">

                    <div class="r1_l_c">
                        <div class="like_button">
                            <button
                                onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')"
                                class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>"
                                aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
                                <span class="material-icons" style="color: #3e0c40;">
                                    <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
                                </span>
                            </button>
                        </div>

                        <script src="./assets/js/fav.js"></script>
                        <a href="../pd/?product_id=<?php echo $row['product_id']; ?>">


                            <?php 
                                    if ($row['choiced'] == 0 ) {
                                        echo '';
                                    } else {
                                        echo ' <div class="ch">
                                        <img width="130px" src="../assets/ch.svg">
        
                                        </div> ';
                                    }
                                    ?>
                    </div>
                    <img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($row['image_main']); ?>"
                        alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                </div>

                <div class="card-title">
                    <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                </div>
                <div class="card-subtitle"><?php echo htmlspecialchars($row['short_des'] ?? $row['description']); ?>
                </div>
                <hr class="card-divider">
                <div class="card-footer">
                    <div class="card-price"><span>₹</span><?php echo htmlspecialchars($row['price']); ?></div>
                    <button class="card-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path
                                d="m397.78 316h-205.13a15 15 0 0 1 -14.65-11.67l-34.54-150.48a15 15 0 0 1 14.62-18.36h274.27a15 15 0 0 1 14.65 18.36l-34.6 150.48a15 15 0 0 1 -14.62 11.67zm-193.19-30h181.25l27.67-120.48h-236.6z">
                            </path>
                            <path
                                d="m222 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m368.42 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m158.08 165.49a15 15 0 0 1 -14.23-10.26l-25.71-77.23h-47.44a15 15 0 1 1 0-30h58.3a15 15 0 0 1 14.23 10.26l29.13 87.49a15 15 0 0 1 -14.23 19.74z">
                            </path>
                        </svg>
                    </button>

                </div>
                </a>
            </div>
            <?php endwhile; ?>
        </section>
        <!-- end Lenovo products -->

        <!-- dell product -->

        <span class="title-shop">
            <h1>Exclusive Dell Collections</h1>
        </span>

        <section class="products">
            <?php
            while ($row = mysqli_fetch_assoc($dell)) :
                // Check if the product is whitelisted for the current user
                $is_whitelisted = false;
                if ($user_id) {
                    $whitelist_sql = "SELECT * FROM whitelist WHERE user_id = '$user_id' AND product_id = '{$row['product_id']}'";
                    $whitelist_result = $conn->query($whitelist_sql);
                    if ($whitelist_result->num_rows > 0) {
                        $is_whitelisted = true;
                    }
                }
        ?>
            <div class="products-card">
                <div class="card-img">

                    <div class="r1_l_c">
                        <div class="like_button">
                            <button
                                onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')"
                                class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>"
                                aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
                                <span class="material-icons" style="color: #3e0c40;">
                                    <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
                                </span>
                            </button>
                        </div>

                        <script src="./assets/js/fav.js"></script>
                        <a href="../pd/?product_id=<?php echo $row['product_id']; ?>">

                            <?php 
                                    if ($row['choiced'] == 0 ) {
                                        echo '';
                                    } else {
                                        echo ' <div class="ch">
                                        <img width="130px" src="../assets/ch.svg">
        
                                        </div> ';
                                    }
                                    ?>
                    </div>
                    <img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($row['image_main']); ?>"
                        alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                </div>

                <div class="card-title">
                    <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                </div>
                <div class="card-subtitle"><?php echo htmlspecialchars($row['short_des'] ?? $row['description']); ?>
                </div>
                <hr class="card-divider">
                <div class="card-footer">
                    <div class="card-price"><span>₹</span><?php echo htmlspecialchars($row['price']); ?></div>
                    <button class="card-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path
                                d="m397.78 316h-205.13a15 15 0 0 1 -14.65-11.67l-34.54-150.48a15 15 0 0 1 14.62-18.36h274.27a15 15 0 0 1 14.65 18.36l-34.6 150.48a15 15 0 0 1 -14.62 11.67zm-193.19-30h181.25l27.67-120.48h-236.6z">
                            </path>
                            <path
                                d="m222 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m368.42 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m158.08 165.49a15 15 0 0 1 -14.23-10.26l-25.71-77.23h-47.44a15 15 0 1 1 0-30h58.3a15 15 0 0 1 14.23 10.26l29.13 87.49a15 15 0 0 1 -14.23 19.74z">
                            </path>
                        </svg>
                    </button>

                </div>
                </a>
            </div>
            <?php endwhile; ?>
        </section>

        <!-- end dell product -->
        <!-- HP product -->

        <span class="title-shop">
            <h1>HP Collections</h1>
        </span>
        <section class="products">
            <?php if (mysqli_num_rows($hp) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($hp)) : 
            // Check if the product is whitelisted for the current user
            $is_whitelisted = false;
            if ($user_id) {
                $whitelist_sql = "SELECT * FROM whitelist WHERE user_id = '$user_id' AND product_id = '{$row['product_id']}'";
                $whitelist_result = $conn->query($whitelist_sql);
                if ($whitelist_result->num_rows > 0) {
                    $is_whitelisted = true;
                }
            }
        ?>
            <div class="products-card">
                <div class="card-img">

                    <div class="r1_l_c">
                        <div class="like_button">
                            <button
                                onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')"
                                class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>"
                                aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
                                <span class="material-icons" style="color: #3e0c40;">
                                    <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
                                </span>
                            </button>
                        </div>

                        <script src="./assets/js/fav.js"></script>
                        <a href="../pd/?product_id=<?php echo $row['product_id']; ?>">

                            <?php 
                                    if ($row['choiced'] == 0 ) {
                                        echo '';
                                    } else {
                                        echo ' <div class="ch">
                                        <img width="130px" src="../assets/ch.svg">
        
                                        </div> ';
                                    }
                                    ?>
                    </div>
                    <img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($row['image_main']); ?>"
                        alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                </div>

                <div class="card-title">
                    <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                </div>
                <div class="card-subtitle"><?php echo htmlspecialchars($row['short_des'] ?? $row['description']); ?>
                </div>
                <hr class="card-divider">
                <div class="card-footer">
                    <div class="card-price"><span>₹</span><?php echo htmlspecialchars($row['price']); ?></div>
                    <button class="card-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path
                                d="m397.78 316h-205.13a15 15 0 0 1 -14.65-11.67l-34.54-150.48a15 15 0 0 1 14.62-18.36h274.27a15 15 0 0 1 14.65 18.36l-34.6 150.48a15 15 0 0 1 -14.62 11.67zm-193.19-30h181.25l27.67-120.48h-236.6z">
                            </path>
                            <path
                                d="m222 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m368.42 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m158.08 165.49a15 15 0 0 1 -14.23-10.26l-25.71-77.23h-47.44a15 15 0 1 1 0-30h58.3a15 15 0 0 1 14.23 10.26l29.13 87.49a15 15 0 0 1 -14.23 19.74z">
                            </path>
                        </svg>
                    </button>

                </div>
                </a>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="ab-o-oa" aria-hidden="true">
                <div class="ZAnhre">
                    <img class="wF0Mmb" src="../assets/empty_hp.svg" width="300px" height="300px" alt="">
                </div>
                <div class="ab-o-oa-r">
                    <div class="ab-o-oa-qc-V">No product Found</div>
                    <div class="ab-o-oa-qc-r"> matching your search criteria "HP".</div>
                </div>
            </div>
            <style>
            .ab-o-oa {
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

            .ab-o-oa-r {
                display: contents;
            }

            .ab-o-oa-qc-V {
                font-weight: 800;

            }

            .ab-o-oa-qc-r {
                font-weight: normal;

            }
            </style>
            <?php endif; ?>
        </section>

        <!-- end HP product -->






        <!-- refurbished -->
        <span class="title-shop">
            <h1>The Best in <strong>Services</strong></h1>
        </span>

        <section class="products">
            <?php 
            
            while ($row = mysqli_fetch_assoc($servi)) :
        ?>
            <div class="products-card">
                <div class="card-img">


                    <a href="../services/home/add_to_cart.php?service_id=<?php echo $row['id']; ?>">

                        <img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($row['img']); ?>"
                            alt="<?php echo htmlspecialchars($row['service_name']); ?>">
                </div>

                <div class="card-title"><?php echo htmlspecialchars($row['service_name']); ?>
                </div>
                <!-- <div class="card-subtitle"><?php echo htmlspecialchars($row['short_des'] ?? $row['description']); ?></div> -->
                <hr class="card-divider">
                <div class="card-footer">
                    <div class="card-price"><span>₹</span><?php echo htmlspecialchars($row['price']); ?></div>
                    <button class="card-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path
                                d="m397.78 316h-205.13a15 15 0 0 1 -14.65-11.67l-34.54-150.48a15 15 0 0 1 14.62-18.36h274.27a15 15 0 0 1 14.65 18.36l-34.6 150.48a15 15 0 0 1 -14.62 11.67zm-193.19-30h181.25l27.67-120.48h-236.6z">
                            </path>
                            <path
                                d="m222 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m368.42 450a57.48 57.48 0 1 1 57.48-57.48 57.54 57.54 0 0 1 -57.48 57.48zm0-84.95a27.48 27.48 0 1 0 27.48 27.47 27.5 27.5 0 0 0 -27.48-27.47z">
                            </path>
                            <path
                                d="m158.08 165.49a15 15 0 0 1 -14.23-10.26l-25.71-77.23h-47.44a15 15 0 1 1 0-30h58.3a15 15 0 0 1 14.23 10.26l29.13 87.49a15 15 0 0 1 -14.23 19.74z">
                            </path>
                        </svg>
                    </button>

                </div>
                </a>
            </div>
            <?php endwhile; ?>
        </section>

        <!-- end refurbished -->









        <!-- Computer -->

        <span class="title-shop">
            <h1>Best Computers in Low rate</h1>
        </span>

        <section class="products">
            <div class="k-pp-c">
                <?php
            while ($row = mysqli_fetch_assoc($com)) :
                // Check if the product is whitelisted for the current user
                $is_whitelisted = false;
                if ($user_id) {
                    $whitelist_sql = "SELECT * FROM whitelist WHERE user_id = '$user_id' AND product_id = '{$row['product_id']}'";
                    $whitelist_result = $conn->query($whitelist_sql);
                    if ($whitelist_result->num_rows > 0) {
                        $is_whitelisted = true;
                    }
                }
        ?>
                <div class="card-of-product">

                    <div class="r1_l_c">
                        <div class="like_button">
                            <button
                                onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')"
                                class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>"
                                aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
                                <span class="material-icons" style="color: #3e0c40;">
                                    <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
                                </span>
                            </button>
                        </div>

                        <script src="./assets/js/fav.js"></script>
                        <a href="../pd/?product_id=<?php echo $row['product_id']; ?>">

                            <?php 
                                    if ($row['choiced'] == 0 ) {
                                        echo '';
                                    } else {
                                        echo ' <div class="choiced-product-card">
                                        <img src="../assets/ch.svg">
                                        
                                        </div> ';
                                    }
                                    ?>
                    </div>
                    <div class="img-of-product-card">
                        <img style="width: 100%;height: auto;" src="<?php echo htmlspecialchars($row['image_main']); ?>"
                            alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                    </div>

                    <div class="title-of-product-card">
                        <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                    </div>

                    <hr class="card-divider">
                    <div class="footer-of-product-card">
                        <div class="price-of-product-card"><span>₹</span><?php echo htmlspecialchars($row['price']); ?>
                        </div>

                    </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
        <!-- end computer -->






        <!--=============== HEADER ===============-->
        <div id="footer"></div>


        <?php
$conn->close();
?>


</body>

</html>