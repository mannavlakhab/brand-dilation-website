<?php
session_start(); // Make sure the session is started


// Include the database connection file
require_once '../db_connect.php'; // Ensure this path is correct

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


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


// Validate and get product ID from URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
if ($product_id <= 0) {
    die("Invalid Product ID");
}

$result_info_pro = $conn->prepare("SELECT * FROM product_info WHERE product_id = ?");
if ($result_info_pro) {
    $result_info_pro->bind_param("i", $product_id);
    $result_info_pro->execute();
    $product_info_result = $result_info_pro->get_result();
    $pro_info = $product_info_result->fetch_assoc();
    $result_info_pro->close();
}

$product_id = $_GET['product_id'];  // Assuming the product ID is passed via URL

if (!isset($_SESSION['recently_viewed'])) {
    $_SESSION['recently_viewed'] = [];
}

// Avoid duplicates and maintain the limit of 5 recently viewed products
if (!in_array($product_id, $_SESSION['recently_viewed'])) {
    array_unshift($_SESSION['recently_viewed'], $product_id);
    if (count($_SESSION['recently_viewed']) > 5) {
        array_pop($_SESSION['recently_viewed']);
    }
}



// // Prepare options for the select element
// $options = "";
// if ($result_info_pro->num_rows > 0) {
//     while ($row_products = $result_info_pro->fetch_assoc()) {
//         // Check if the product ID exists in product_info
//         // $options .= "<div class='box'>" . htmlspecialchars($row_products['Brand']) . "</div>";
// }

// } else {
// $options = "<option value=''>No products description</option>";
// }



// Fetch product details
$product = null;
$product_query = $conn->prepare("SELECT * FROM Products WHERE product_id = ?");
if ($product_query) {
    $product_query->bind_param("i", $product_id);
    $product_query->execute();
    $product_result = $product_query->get_result();
    $product = $product_result->fetch_assoc();
    $product_query->close();
}

if (!$product) {
    die("  <div class='ab-o-oa' aria-hidden='true'>
                    <div class='ZAnhre'>
                    <img class='wF0Mmb' src='../assets/find_v1.svg' width='300px' height='300px' alt=''></div>
                    <div class='ab-o-oa-r'><div class='ab-o-oa-qc-V'>No product Found</div>
                    <div class='ab-o-oa-qc-r'> matching your search criteria.</div></div>
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




                </style>");
}


// Fetch product images
$images = [];
$images_query = $conn->prepare("SELECT image_path FROM Product_Images WHERE product_id = ?");
if ($images_query) {
    $images_query->bind_param("i", $product_id);
    $images_query->execute();
    $images_result = $images_query->get_result();
    while ($row = $images_result->fetch_assoc()) {
        $images[] = $row['image_path'];
    }
    $images_query->close();
}

// Fetch product variations
$variations = [];
$variations_query = $conn->prepare(
    "SELECT variation_id, variation_name, variation_value, price_modifier, stock_quantity 
    FROM ProductVariations 
    WHERE product_id = ?"
);
if ($variations_query) {
    $variations_query->bind_param("i", $product_id);
    $variations_query->execute();
    $variations_result = $variations_query->get_result();
    while ($row = $variations_result->fetch_assoc()) {
        $variations[] = $row;
    }
    $variations_query->close();
}

// Fetch product reviews
$reviews = [];
$sumRatings = 0;
$totalReviews = 0;

$reviews_query = $conn->prepare(
    "SELECT r.rating, r.review_text, r.review_date, u.username, r.review_image_path 
    FROM product_reviews r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.product_id = ?"
);
if ($reviews_query) {
    $reviews_query->bind_param("i", $product_id);
    $reviews_query->execute();
    $reviews_result = $reviews_query->get_result();
    while ($row = $reviews_result->fetch_assoc()) {
        $reviews[] = $row;
        // Add up all the ratings
        $sumRatings += $row['rating'];
    }
    $totalReviews = count($reviews); // Calculate total number of reviews
    $reviews_query->close();
}

// Calculate the average rating
$averageRating = $totalReviews > 0 ? $sumRatings / $totalReviews : 0;
$roundedRating = round($averageRating);

// Check if the product is in the user's whitelist
$is_whitelisted = false;

if ($user_id && $product_id) {
    $whitelist_sql = "SELECT * FROM whitelist WHERE user_id = ? AND product_id = ?";
    $whitelist_query = $conn->prepare($whitelist_sql);
    $whitelist_query->bind_param("ii", $user_id, $product_id);
    
    if ($whitelist_query->execute()) {
        $whitelist_result = $whitelist_query->get_result();
        if ($whitelist_result->num_rows > 0) {
            $is_whitelisted = true;
        }
    } else {
        echo "SQL Error: " . $whitelist_query->error;
    }
    $whitelist_query->close();
}

// Close the connection after all operations
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <script src="../assets/js/internet-check.js" defer></script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?></title>
    <!-- Stylesheet -->
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./pd.css">
    <link rel="stylesheet" href="../assets/css/btn.css">
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
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

    <!--=============== closing file loader ===============-->
    <title><?php echo htmlspecialchars($product['title']); ?></title>
    <script>
    function updateVariationId(variationId) {
        document.getElementById('variation_id').value = variationId;
    }

    function validateForm() {
        const variationId = document.querySelector('input[name="variation"]:checked');
        if (!variationId) {
            alert("Please select a variation.");
            return false;
        }
        return true;
    }
    </script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet"
        id="bootstrap-css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>



    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <!---Boxicons CDN Setup for icons-->
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <header>
        <!-- <div style="margin-bottom=3%;" id="header"></div> -->
    </header>
    <div class="pd-wrap">
        <div class="container">
            <button class="btn-trick-new" onclick="history.back()">Go Back</button>
            <div class="heading-section">
                <h2>Product Details - <?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?></h2>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div id="slider" class="owl-carousel product-slider">
                        <div class="item">
                            <img style="
    padding: 2%;
	border-radius: 20px;
" src="../<?php echo htmlspecialchars($product['image_main']); ?>"
                                alt="<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>" />
                        </div>

                        <?php foreach ($images as $image_url): ?>
                        <div class="item">
                            <img style="
    padding: 2%;
	border-radius: 20px;
" src="../<?php echo htmlspecialchars($image_url); ?>"
                                alt="<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="thumb" class="owl-carousel product-thumb">
                        <div class="item">
                            <img style="
    width: 100%;
    max-width: 312px;
    height: auto;
    padding: 2%;
	border-radius: 10px;
" src="../<?php echo htmlspecialchars($product['image_main']); ?>"
                                alt="<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>" />
                        </div>

                        <?php foreach ($images as $image_url): ?>
                        <div class="item">
                            <img style="
    width: 100%;
    max-width: 312px;
    height: auto;
    padding: 2%;
	border-radius: 10px;
" src="../<?php echo htmlspecialchars($image_url); ?>"
                                alt="<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-dtl">
                        <div class="product-info">
                            <div class="product-name">
                                <?php echo htmlspecialchars($product['title']); ?></div>
                            <div class="reviews-counter">
                                <a href="#add_reviews_tabs">
                                <div class="rate">
                                    <!-- Display the average rating and review count -->
                                    <?php if ($totalReviews > 0): ?>
                                    <div class="product-rating">
                                        <span class="rating">
                                            <?php 
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $roundedRating) {
                        echo "<i class='star filled'></i>";
                    } else {
                        echo "<i class='star'></i>";
                    }
                }
                ?>
                                        </span>
                                        <span>(<?php echo number_format($averageRating, 1); ?> out of 5 stars)</span>
                                        <span><?php echo $totalReviews; ?> reviews</span>
                                    </div>
                                    <?php else: ?>
                                    <div class="product-rating">
                                        <span>No reviews yet</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                </a>
                                </div>

                                            <?php 
                                            if ($product['choiced'] == 0 ) {
                                                echo '';
                                                } else {
                                                    echo ' <div class="ch">
                                                    <span class="tooltiptext">This Choiced highlights highly rated, best-selling  products.</span>
                                            <img width="130px" src="../assets/ch.svg">

                                            </div> ';
                                        }
                                        ?>
                                <div class="product-price-discount"><span>
                                        ₹<?php echo number_format($product['price'], 2); ?></span><span
                                        class="line-through">₹<?php echo number_format($product['offer_prices'], 2); ?></span>
                                </div>
                                <?php 
                                            if ($product['refurbished'] == 0 ) {
                                                echo '';
                                                } else {
                                                    echo ' <div class="ch">
                                                    <span class="tooltiptext">This REFURBISHED products.</span>
                                            <img width="110px" src="../assets/refurbished.svg">

                                            </div> ';
                                        }
                                        ?>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($product['short_des'])); ?></p>
                            <div class="row">

                                <div class="col-md-6">
                                    <label for="size">Select Product Model</label>
                                    <select id="size" name="size" class="form-control"
                                        onchange="updateVariationId(this.value)">
                                        <option selected disabled>Select the model</option>
                                        <?php foreach ($variations as $variation): ?>
                                        <option value="<?php echo $variation['variation_id']; ?>">
                                            <?php echo htmlspecialchars($variation['variation_name']) . ": " . htmlspecialchars($variation['variation_value']) . " (+₹" . number_format($variation['price_modifier'], 2) . ")"; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="product-count">
                                        <label for="quantity">Quantity</label>
                                        <div class="display-flex">
                                        <?php 
        if (!$product['stock_quantity'] < 1) {
            echo '<input type="number" name="quantity" id="quantity" value="1" class="qty"
                                                min="1"
                                                max="'. htmlspecialchars($product['stock_quantity']) .'">';
        }
        ?>
                                            
                                            <?php 
        if ($product['stock_quantity'] < 1) {
            echo '<p class="stock" style="color:red;">Out of stock</p>';
        } else if ($product['stock_quantity'] <= 5) {
            echo '<p class="stock" style="color:orange;">Limited stock only '. htmlspecialchars($product['stock_quantity']) .' left</p>';
        } else {
            echo '<p class="stock" style="color:green;">In stock</p>';
        }
        ?>
                                        </div>
                                    </div>



                                    <div class="row">
                                        <div class="like_button">
                                            <!-- HTML Part -->
                                            <?php 

if (!$is_whitelisted): ?>
                                            <!-- If not whitelisted, show 'Add' button -->
                                            <form method="post" action="../whitelist_action.php"
                                                style="display: inline;">
                                                <input type="hidden" name="product_id"
                                                    value="<?php echo htmlspecialchars($product_id); ?>">
                                                <button type="submit" name="action" value="add"
                                                    class="action_has has_liked" aria-label="like">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px"
                                                        viewBox="0 0 24 24" width="24px" fill="#3e0c40">
                                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                                        <path
                                                            d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <?php else: ?>
                                            <!-- If whitelisted, show 'Remove' button -->
                                            <form method="post" action="../whitelist_action.php"
                                                style="display: inline;">
                                                <input type="hidden" name="product_id"
                                                    value="<?php echo htmlspecialchars($product_id); ?>">
                                                <button type="submit" name="action" value="remove"
                                                    class="action_has has_liked" aria-label="like">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="24px"
                                                        viewBox="0 0 24 24" width="24px" fill="#3e0c40">
                                                        <path d="M0 0h24v24H0V0z" fill="none" />
                                                        <path
                                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                                    </svg>
                                                </button>
                                            </form>
                                            <?php endif; ?>


                                        </div>
                                        <?php 
        if ($product['stock_quantity'] < 1) {
            echo '<button disabled type="submit" name="add_to_cart" class="round-black-btn">Out Of Stock</button>';
        } else {
            echo '<form action="../cart.php" onsubmit="return validateForm()" method="POST">
                                            <input type="hidden" name="product_id" value="'. htmlspecialchars($product_id).'">
                                            <input type="hidden" name="variation_id" id="variation_id" value="default">
                                            <input type="hidden" name="quantity" id="hidden_quantity" value="1">
                                            <button type="submit" name="add_to_cart" class="round-black-btn">Add to
                                                cart</button>
                                        </form>';
        }
        ?>
                                        
                                    </div>
                                    <script>
                                    function updateVariationId(variationId) {
                                        document.getElementById('variation_id').value = variationId;
                                    }

                                    function validateForm() {
                                        const variationId = document.getElementById('variation_id').value;
                                        const quantity = parseInt(document.getElementById('quantity').value);
                                        if (!variationId || variationId === "default") {
                                            alert("Please select a product model.");
                                            return false;
                                        }
                                        if (isNaN(quantity) || quantity < 1) {
                                            alert("Please enter a valid quantity.");
                                            return false;
                                        }
                                        document.getElementById('hidden_quantity').value =
                                        quantity; // Ensure hidden input is updated
                                        return true;
                                    }
                                    </script>


                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- <button type="submit" name="add_to_cart" class="CartBtn">
<span class="IconContainer">
	<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512" fill="#3c0e40" class="cart">
		<path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z">
		</path>
	</svg>
</span>
	<p class="cart_text">Add to Cart</p>
</button> -->



                    <div class="product-info-tabs">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description"
                                    role="tab" aria-controls="description" aria-selected="true">Description</a>
                            </li>
                            <li class="nav-item"> <span id="add_reviews_tabs"></span>
                                <a class="nav-link" id="review-tab" data-toggle="tab" href="#review" role="tab"
                                    aria-controls="review" aria-selected="false">Reviews
                                    (<?php echo $totalReviews; ?>)</a>
                            </li>
                        </ul>





                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel"
                                aria-labelledby="description-tab">

                                <div class="des">
                                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                                </div>

                                <div class="info-pro">
                                    <?php 
                                    echo "<table>";
                                    foreach ($result_info_pro as $row) {
                                        echo "<tr>";
                                        foreach ($row as $cell) {
                                            echo "<td><tr>$cell<br></tr></td>";
                                        }
                                        echo "</tr>";
                                    }
                                    echo "</table>";
                                    ?>

                                    <!-- <h3>Technical details of <?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?></h3>
                                    <p>*This details are might be wrong.</p>
                                    <br>
                                    <div class="divTable blueTable">
                                        <div class="divTableBody"> -->
                                                <!-- Option 1 -->
                                        <!-- <div class="divTableRow">
                                        <div class="divThead">Brand</div>
                                        <div class="divTableCell"><?php echo htmlspecialchars($pro_info['Brand'] ?? "Unknown brand"); ?></div>
                                        </div> -->
                                                <!-- Option 2 -->

                                        <!-- <?php
                                        if ($pro_info['Item_Weight']) {
                                            echo '
                                        <div class="divTableRow">
                                        <div class="divThead">Item_Weight</div>
                                        <div class="divTableCell">'. htmlspecialchars($pro_info['Item_Weight']) .'</div>
                                        </div>
                                        ';}
                                        else {
                                            echo '</div>';
                                        }
                                        ?> -->
                                                <!-- Option 3 -->

                                        <!-- <?php
                                        
                                        $fields = [
                                            'Brand' => $pro_info['Brand'],
                                            'Manufacturer' => $pro_info['Manufacturer'],
                                            'Model_Name' => $pro_info['Model_Name'],
                                            'Series' => $pro_info['Series'],
                                            'Item_Weight' => $pro_info['Item_Weight'],
                                            'Item_Weight' => $pro_info['Item_Weight'],
                                            'Item_Weight' => $pro_info['Item_Weight'],
                                            'Form_Factor' => $pro_info['Form_Factor'],
                                            'Screen_Resolution' => $pro_info['Screen_Resolution'],
                                            'Product_Dimensions' => $pro_info['Product_Dimensions'],
                                            'Batteries' => $pro_info['Batteries'],
                                            'Colour' => $pro_info['Colour'],
                                            'Item_Model_Number' => $pro_info['Item_Model_Number'],
                                            'Processor_Brand' => $pro_info['Processor_Brand'],
                                            'Processor_Type' => $pro_info['Processor_Type'],
                                            'Processor_Speed' => $pro_info['Processor_Speed'],
                                            'Processor_Count' => $pro_info['Processor_Count'],
                                            'RAM_Size' => $pro_info['RAM_Size'],
                                            'Memory_Technology' => $pro_info['Memory_Technology'],
                                            'Computer_Memory_Type' => $pro_info['Computer_Memory_Type'],
                                            'Maximum_Memory_Supported' => $pro_info['Maximum_Memory_Supported'],
                                            'Memory_Clock_Speed' => $pro_info['Memory_Clock_Speed'],
                                            'Hard_Disk_Size' => $pro_info['Hard_Disk_Size'],
                                            'Hard_Disk_Description' => $pro_info['Hard_Disk_Description'],
                                            'Hard_Drive_Interface' => $pro_info['Hard_Drive_Interface'],
                                            'Hard_Disk_Rotational_Speed' => $pro_info['Hard_Disk_Rotational_Speed'],
                                            'Audio_Details' => $pro_info['Audio_Details'],
                                            'Graphics_Coprocessor' => $pro_info['Graphics_Coprocessor'],
                                            'Graphics_Chipset_Brand' => $pro_info['Graphics_Chipset_Brand'],
                                            'Graphics_Card_Description' => $pro_info['Graphics_Card_Description'],
                                            'Graphics_RAM_Type' => $pro_info['Graphics_RAM_Type'],
                                            'Graphics_Card_RAM_Size' => $pro_info['Graphics_Card_RAM_Size'],
                                            'Graphics_Card_Interface' => $pro_info['Graphics_Card_Interface'],
                                            'Connectivity_Type' => $pro_info['Connectivity_Type'],
                                            'Wireless_Type' => $pro_info['Wireless_Type'],
                                            'Number_of_USB_3_0_Ports' => $pro_info['Number_of_USB_3_0_Ports'],
                                            'Voltage' => $pro_info['Voltage'],
                                            'Optical_Drive_Type' => $pro_info['Optical_Drive_Type'],
                                            'Power_Source' => $pro_info['Power_Source'],
                                            'Hardware_Platform' => $pro_info['Hardware_Platform'],
                                            'Operating_System' => $pro_info['Operating_System'],
                                            'Avg_Battery_Standby_Life' => $pro_info['Avg_Battery_Standby_Life'],
                                            'Avg_Battery_Life' => $pro_info['Avg_Battery_Life'],
                                            'Are_Batteries_Included' => $pro_info['Are_Batteries_Included'],
                                            'Lithium_Battery_Energy_Content' => $pro_info['Lithium_Battery_Energy_Content'],
                                            'Lithium_Battery_Weight' => $pro_info['Lithium_Battery_Weight'],
                                            'Number_of_Lithium_Ion_Cells' => $pro_info['Number_of_Lithium_Ion_Cells'],
                                            'Number_of_Lithium_Metal_Cells' => $pro_info['Number_of_Lithium_Metal_Cells'],
                                            'Included_Components' => $pro_info['Included_Components'],
                                            'Country_of_Origin' => $pro_info['Country_of_Origin'],
                                            'Special_Feature' => $pro_info['Special_Feature'],
                                        ];
                                        
                                        
                                        foreach ($fields as $field => $value) {
                                            if (!empty($value)) {
                                                echo '
                                                    <div class="divTableRow">
                                                        <div class="divThead">' . $field . '</div>
                                                        <div class="divTableCell">' . htmlspecialchars($value) . '</div>
                                                    </div>
                                                ';
                                            }
                                        }
                                        // echo '</div>'; // End the outer div

                                        ?> -->

                                        <!-- </div>
                                        </div> -->


                                
                                </div>
                            </div>

<style>
    div.blueTable {
  border: 0px solid #3C0E40;
  width: 100%;
  text-align: center;
  border-collapse: collapse;
  white-space: normal;
    word-wrap: break-word;
    background  : #e8e8e8;
}
.divTable.blueTable .divTableBody .divTableCell .divThead  {
  font-size: 13px;
  color: #000000;
  padding: 2%;
}
.divTableCell{
    background:#fff;
    color:#3c0e40;
    padding: 2%;
    text-align: left;
}
.divTableRow{
    border-block:1px solid;
}
.divTable{ display: table; }
.divTableRow { display: table-row; }
.divTableHeading { display: table-header-group;}
.divTableCell, .divTableHead { display: table-cell;}
.divTableHeading { display: table-header-group;}
.divTableFoot { display: table-footer-group;}
.divTableBody { display: table-row-group;}
</style>
                            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                                <div class="review-heading">REVIEWS</div>
                                <button class="round-black-btn"><a href="#add_reviews">Add Reviews</a></button>
                                <?php foreach ($reviews as $review): ?>
                                <div class="review">
                                    <div class="review-header">
                                        <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                        <span class="rating">
                                            <?php 
                    $rating = intval($review['rating']);
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $rating) {
                            echo "<i class='star filled'></i>";
                        } else {
                            echo "<i class='star'></i>";
                        }
                    }
                    ?>
                                        </span>
                                    </div>

                                    <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                    <?php if ($review['review_image_path']): ?>
                                    <img style="border-radius: 5px; cursor: pointer; width: auto; height: 150px;"
                                        src="../<?php echo htmlspecialchars($review['review_image_path']); ?>"
                                        alt="Review Image" class="review-image"
                                        data-image-src="../<?php echo htmlspecialchars($review['review_image_path']); ?>"
                                        onclick="openModal(this)">
                                    <?php endif; ?><br>
                                    <small class="review-date">Reviewed on
                                        <?php echo htmlspecialchars($review['review_date']); ?></small>
                                </div>
                                <?php endforeach; ?>
                                <span id="add_reviews"></span>

                                <!-- Modal for Image Preview -->
                                <div id="imageModal" class="modal">
                                    <span class="close" onclick="closeModal()">&times;</span>
                                    <img class="modal-content" id="modalImage">
                                    <div id="caption"></div>
                                </div>


                                <hr>

                                <form class="review-form" action="../submit_review.php" method="post"
                                    class="review-form" enctype="multipart/form-data">
                                    <h3>Add Your review</h3>
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <div class="form-group">
                                        <label>Your rating</label>
                                        <div class="reviews-counter">
                                            <div class="rate">
                                                <select class="form-control" name="rating" id="rating" required>
                                                    <option selected disabled>Select the Rating</option>
                                                    <hr>
                                                    <option disabled value="1">00</option>
                                                    <option value="1">01</option>
                                                    <option value="2">02</option>
                                                    <option value="3">03</option>
                                                    <option value="4">04</option>
                                                    <option value="5">05</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Your message</label>
                                        <textarea class="form-control" rows="10" name="review_text" id="review_text"
                                            required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Upload Image:</label>
                                        <input type="file" class="form-control" name="review_image" id="review_image"
                                            accept="image/*">
                                    </div>
                                    <button type="submit" class="round-black-btn">Submit Review</button>
                                </form>















                                <script>
                                // Function to open the modal and display the image
                                function openModal(imgElement) {
                                    var modal = document.getElementById("imageModal");
                                    var modalImg = document.getElementById("modalImage");
                                    var captionText = document.getElementById("caption");

                                    modal.style.display = "block";
                                    modalImg.src = imgElement.getAttribute("data-image-src");
                                    captionText.innerHTML = imgElement.alt;
                                }

                                // Function to close the modal
                                function closeModal() {
                                    var modal = document.getElementById("imageModal");
                                    modal.style.display = "none";
                                }
                                </script>
                                <script>
                                $(document).ready(function() {
                                    var slider = $("#slider");
                                    var thumb = $("#thumb");
                                    var slidesPerPage = 4; //globaly define number of elements per page
                                    var syncedSecondary = true;
                                    slider.owlCarousel({
                                        items: 1,
                                        slideSpeed: 2000,
                                        nav: false,
                                        autoplay: false,
                                        dots: false,
                                        loop: true,
                                        responsiveRefreshRate: 200
                                    }).on('changed.owl.carousel', syncPosition);
                                    thumb
                                        .on('initialized.owl.carousel', function() {
                                            thumb.find(".owl-item").eq(0).addClass("current");
                                        })
                                        .owlCarousel({
                                            items: slidesPerPage,
                                            dots: false,
                                            nav: true,
                                            item: 4,
                                            smartSpeed: 200,
                                            slideSpeed: 500,
                                            slideBy: slidesPerPage,
                                            navText: [
                                                '<svg width="18px" height="18px" viewBox="0 0 11 20"><path style="fill:none;stroke-width: 1px;stroke: #000;" d="M9.554,1.001l-8.607,8.607l8.607,8.606"/></svg>',
                                                '<svg width="25px" height="25px" viewBox="0 0 11 20" version="1.1"><path style="fill:none;stroke-width: 1px;stroke: #000;" d="M1.054,18.214l8.606,-8.606l-8.606,-8.607"/></svg>'
                                            ],
                                            responsiveRefreshRate: 100
                                        }).on('changed.owl.carousel', syncPosition2);

                                    function syncPosition(el) {
                                        var count = el.item.count - 1;
                                        var current = Math.round(el.item.index - (el.item.count / 2) - .5);
                                        if (current < 0) {
                                            current = count;
                                        }
                                        if (current > count) {
                                            current = 0;
                                        }
                                        thumb
                                            .find(".owl-item")
                                            .removeClass("current")
                                            .eq(current)
                                            .addClass("current");
                                        var onscreen = thumb.find('.owl-item.active').length - 1;
                                        var start = thumb.find('.owl-item.active').first().index();
                                        var end = thumb.find('.owl-item.active').last().index();
                                        if (current > end) {
                                            thumb.data('owl.carousel').to(current, 100, true);
                                        }
                                        if (current < start) {
                                            thumb.data('owl.carousel').to(current - onscreen, 100, true);
                                        }
                                    }

                                    function syncPosition2(el) {
                                        if (syncedSecondary) {
                                            var number = el.item.index;
                                            slider.data('owl.carousel').to(number, 100, true);
                                        }
                                    }
                                    thumb.on("click", ".owl-item", function(e) {
                                        e.preventDefault();
                                        var number = $(this).index();
                                        slider.data('owl.carousel').to(number, 300, true);
                                    });


                                    $(".qtyminus").on("click", function() {
                                        var now = $(".qty").val();
                                        if ($.isNumeric(now)) {
                                            if (parseInt(now) - 1 > 0) {
                                                now--;
                                            }
                                            $(".qty").val(now);
                                        }
                                    })
                                    $(".qtyplus").on("click", function() {
                                        var now = $(".qty").val();
                                        if ($.isNumeric(now)) {
                                            $(".qty").val(parseInt(now) + 1);
                                        }
                                    });
                                });
                                </script>
                                <script
                                    src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js">
                                </script>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
                                    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
                                    crossorigin="anonymous"></script>
                                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
                                    integrity="	sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
                                    crossorigin="anonymous"></script>
</body>

</html>