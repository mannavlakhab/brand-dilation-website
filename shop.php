<?php
session_start();
include 'db_connect.php';


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

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Initialize variables for search, filters, sorting, choiced products, and review filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$selected_category = isset($_GET['category']) ? $_GET['category'] : '';
$selected_brands = isset($_GET['brand']) ? $_GET['brand'] : [];
$price_min = isset($_GET['price_min']) ? (int)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? (int)$_GET['price_max'] : 120000;
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : ''; // Sorting option
$show_choiced = isset($_GET['choiced']) && $_GET['choiced'] == '1'; // Choiced filter
$review_filter = isset($_GET['review']) ? (int)$_GET['review'] : 0; // Review filter
$selected_occasions = isset($_GET['occasion']) ? $_GET['occasion'] : [];
$selected_processors = isset($_GET['processor']) ? $_GET['processor'] : [];
$selected_gpus = isset($_GET['gpu']) ? $_GET['gpu'] : [];

// Base query to fetch products with optional average review rating
$sql = "SELECT p.product_id,p.category_id , p.brand, p.model, p.description, p.short_des, p.offer_prices, p.title, p.price, p.image_main, p.choiced, 
               COALESCE(AVG(r.rating), 0) as avg_rating
        FROM Products p
        LEFT JOIN product_reviews r ON p.product_id = r.product_id
        WHERE 1=1";

// Prepare the search parameters array
$searchParams = [];

// Apply search filter if search input exists
if (!empty($search)) {
    $sql .= " AND (p.brand LIKE ? OR p.model LIKE ? OR p.description LIKE ?)";
    $searchParams[] = "%$search%";
    $searchParams[] = "%$search%";
    $searchParams[] = "%$search%";
}
// Apply category filter if selected
if (!empty($selected_category)) {
    $sql .= " AND p.category_id = ?";
    $searchParams[] = $selected_category;
}

// Apply brand filter if selected
if (!empty($selected_brands)) {
    $placeholders = implode(',', array_fill(0, count($selected_brands), '?'));
    $sql .= " AND p.brand IN ($placeholders)";
    $searchParams = array_merge($searchParams, $selected_brands);
}

// Apply occasional use filter (search in description or short_des)
if (!empty($selected_occasions)) {
    $occasionConditions = [];
    foreach ($selected_occasions as $occasion) {
        $occasionConditions[] = "(p.description LIKE ? OR p.short_des LIKE ?)";
        $searchParams[] = '%' . $occasion . '%';
        $searchParams[] = '%' . $occasion . '%';
    }
    $sql .= " AND (" . implode(' OR ', $occasionConditions) . ")";
}

// Apply processor brand filter (search in description or short_des)
if (!empty($selected_processors)) {
    $processorConditions = [];
    foreach ($selected_processors as $processor) {
        $processorConditions[] = "(p.description LIKE ? OR p.short_des LIKE ?)";
        $searchParams[] = '%' . $processor . '%';
        $searchParams[] = '%' . $processor . '%';
    }
    $sql .= " AND (" . implode(' OR ', $processorConditions) . ")";
}

// Apply graphic card brand filter (search in description or short_des)
if (!empty($selected_gpus)) {
    $gpuConditions = [];
    foreach ($selected_gpus as $gpu) {
        $gpuConditions[] = "(p.description LIKE ? OR p.short_des LIKE ?)";
        $searchParams[] = '%' . $gpu . '%';
        $searchParams[] = '%' . $gpu . '%';
    }
    $sql .= " AND (" . implode(' OR ', $gpuConditions) . ")";
}

// Apply price range filter if selected
if ($price_min >= 0 && $price_max > 0) {
    $sql .= " AND p.price BETWEEN ? AND ?";
    $searchParams[] = $price_min;
    $searchParams[] = $price_max;
}

// Apply choiced filter if selected
if ($show_choiced) {
    $sql .= " AND p.choiced = 1";
}

// Group by product ID to ensure proper average rating calculation
$sql .= " GROUP BY p.product_id, p.brand, p.model, p.description, p.price, p.image_main, p.choiced";

// Apply review filter if selected
if ($review_filter > 0) {
    $sql .= " HAVING avg_rating >= ?";
    $searchParams[] = $review_filter;
}

// Apply sorting option if selected
if ($sort_order == 'low_to_high') {
    $sql .= " ORDER BY p.price ASC";
} elseif ($sort_order == 'high_to_low') {
    $sql .= " ORDER BY p.price DESC";
}

// Prepare and execute the query
$stmt = $conn->prepare($sql);
if ($stmt) {
    // Determine parameter types
    $types = str_repeat('s', count($searchParams));
    // Bind parameters dynamically
    $stmt->bind_param($types, ...$searchParams);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die('Query preparation failed: ' . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BD Shop</title>
    <link rel="stylesheet" href="../assets/css/SHOP1.css">
    <!-- jQuery -->
    <script src="../assets/js/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <script>
    $(function() {
        $('#header').load('../pages/header.php');
        $('#footer').load('../pages/footer.php');
    });
    </script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <header class="header" id="header"></header>
    <main>
        <style>
        @import url('https://fonts.googleapis.com/css2?family=Kode+Mono:wght@400..700&family=Montserrat:wght@100..900&display=swap');
        </style>



        <h1 style="
    text-align: center;
">Our Products</h1>
        <div class="product-list">
            <form method="GET" action="">
                <div class="search">
                    <input type="text" class="search__input" name="search" placeholder="Search products..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <!-- Include hidden filter inputs in the search form to preserve the selected filters -->
                    <?php if (!empty($selected_brands)): ?>
                    <?php foreach ($selected_brands as $brand): ?>
                    <input type="hidden" name="brand[]" value="<?php echo htmlspecialchars($brand); ?>">
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <input type="hidden" name="price_min" value="<?php echo htmlspecialchars($price_min); ?>">
                    <input type="hidden" name="price_max" value="<?php echo htmlspecialchars($price_max); ?>">
                    <input type="hidden" name="review" value="<?php echo htmlspecialchars($review_filter); ?>">

                    <button type="submit" class="search__button">
                        <svg viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" height="16" width="16"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="btu">


                <!-- Sorting and Choiced Filter Buttons -->
                <form method="GET" action="">
                    <!-- Pass all existing filters -->
                    <?php
                                                                foreach ($_GET as $key => $value) {
                                                                    if (is_array($value)) {
                                                                        foreach ($value as $v) {
                                                                            echo '<input type="hidden" name="' . htmlspecialchars($key) . '[]" value="' . htmlspecialchars($v) . '">';
                                                                        }
                                                                    } else {
                                                                        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                                                                    }
                                                                }
                                                                ?>

                    <!-- Sorting Buttons -->
                    <div class="sorting-buttons">
                        <button type="submit" name="sort" value="low_to_high"
                            <?php echo $sort_order == 'low_to_high' ? 'style="background-color: lightgray;"' : ''; ?>>Low
                            to High</button>
                        <button type="submit" name="sort" value="high_to_low"
                            <?php echo $sort_order == 'high_to_low' ? 'style="background-color: lightgray;"' : ''; ?>>High
                            to Low</button>

                        <!-- Filter Button -->
                    </div>
                </form>
                <button id="filterBtn"> Filter&darr; </button>
            </div>
            
        </div>
        <hr>
        <div class="shop-container">
            <!-- Product Listings -->

            <section class="all-products">

<!-- filter side bar options -->
            <aside id="filterAside">
    <h2>Filters your options
        <button id="closeBtn">&times; Close</button> <!-- Close button -->
    </h2>
    <form method="GET" action="">
        <!-- Include the search term as a hidden input in the filter form -->
        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
        <div class="filter-group">
    <h4>Categories</h4>
    <input type="radio" name="category" value="1" 
        <?php echo $selected_category == '1' ? 'checked' : ''; ?>> Laptop<br>
    <input type="radio" name="category" value="2" 
        <?php echo $selected_category == '2' ? 'checked' : ''; ?>> Computer<br>
</div>

        <div class="filter-group">
            <h4>Brand</h4>
            <input type="checkbox" class="inp-cbx" name="brand[]" value="lenovo"
                <?php echo in_array('lenovo', $selected_brands) ? 'checked' : ''; ?>> Lenovo<br>
            <input type="checkbox" class="inp-cbx" name="brand[]" value="Apple"
                <?php echo in_array('apple', $selected_brands) ? 'checked' : ''; ?>> Apple<br>
            <input type="checkbox" class="inp-cbx" name="brand[]" value="asus"
                <?php echo in_array('asus', $selected_brands) ? 'checked' : ''; ?>> Asus<br>
            <input type="checkbox" class="inp-cbx" name="brand[]" value="msi"
                <?php echo in_array('msi', $selected_brands) ? 'checked' : ''; ?>> MSI<br>
            <input type="checkbox" class="inp-cbx" name="brand[]" value="hp"
                <?php echo in_array('hp', $selected_brands) ? 'checked' : ''; ?>> HP<br>
            <input type="checkbox" class="inp-cbx" name="brand[]" value="dell"
                <?php echo in_array('dell', $selected_brands) ? 'checked' : ''; ?>> Dell<br>
        </div>

        <div class="filter-group">
    <h4>Occasional Use</h4>
    <input type="checkbox" name="occasion[]" value="office" 
        <?php echo in_array('office', $selected_occasions) ? 'checked' : ''; ?>> Office Work<br>
    <input type="checkbox" name="occasion[]" value="gaming" 
        <?php echo in_array('gaming', $selected_occasions) ? 'checked' : ''; ?>> Gaming<br>
    <input type="checkbox" name="occasion[]" value="editing" 
        <?php echo in_array('editing', $selected_occasions) ? 'checked' : ''; ?>> Video Editing<br>
    <input type="checkbox" name="occasion[]" value="browsing" 
        <?php echo in_array('browsing', $selected_occasions) ? 'checked' : ''; ?>> Casual Browsing<br>
</div>


        <div class="filter-group">
            <h4>Processor Brand</h4>
            <input type="checkbox" class="inp-cbx" name="processor[]" value="intel"
                <?php echo in_array('intel', $selected_processors) ? 'checked' : ''; ?>> Intel<br>
            <input type="checkbox" class="inp-cbx" name="processor[]" value="amd"
                <?php echo in_array('amd', $selected_processors) ? 'checked' : ''; ?>> AMD<br>
            <input type="checkbox" class="inp-cbx" name="processor[]" value="apple"
                <?php echo in_array('apple', $selected_processors) ? 'checked' : ''; ?>> Apple<br>
        </div>

        <div class="filter-group">
            <h4>Graphic Card Brand</h4>
            <input type="checkbox" class="inp-cbx" name="gpu[]" value="nvidia"
                <?php echo in_array('nvidia', $selected_gpus) ? 'checked' : ''; ?>> NVIDIA<br>
            <input type="checkbox" class="inp-cbx" name="gpu[]" value="amd"
                <?php echo in_array('amd', $selected_gpus) ? 'checked' : ''; ?>> AMD<br>
        </div>

        <!-- Existing Price Range and Choiced Products filters -->

        <div class="filter-group">
            <h4>Price Range</h4>
            <label for="price_min">Min Price</label>
            <input type="range" name="price_min" id="price_min" step="0.1" min="0" max="50000.00"
                value="<?php echo $price_min; ?>" oninput="this.nextElementSibling.value = this.value">
            <output><?php echo $price_min; ?></output><br>

            <label for="price_max">Max Price</label>
            <input type="range" name="price_max" id="price_max" min="10000" max="120000.00"
                value="<?php echo $price_max; ?>" oninput="this.nextElementSibling.value = this.value">
            <output><?php echo $price_max; ?></output>
        </div>

        <div class="filter-group">
            <h4>Choiced Products</h4>
            <label class="switch">
                <input type="checkbox" name="choiced" value="1"
                    <?php echo $show_choiced ? 'checked' : ''; ?>>
                <span class="slider"></span>
            </label>
        </div>

        <div class="filter-group">
            <h4>Customer Reviews</h4>
            <input type="radio" name="review" value="5"
                <?php echo $review_filter == 5 ? 'checked' : ''; ?>> 5 Stars & Up<br>
            <input type="radio" name="review" value="4"
                <?php echo $review_filter == 4 ? 'checked' : ''; ?>> 4 Stars & Up<br>
            <input type="radio" name="review" value="3"
                <?php echo $review_filter == 3 ? 'checked' : ''; ?>> 3 Stars & Up<br>
            <input type="radio" name="review" value="2"
                <?php echo $review_filter == 2 ? 'checked' : ''; ?>> 2 Stars & Up<br>
            <input type="radio" name="review" value="1"
                <?php echo $review_filter == 1 ? 'checked' : ''; ?>> 1 Star & Up<br>
        </div>
        <div style="margin-bottom:8%;" class="filter-group">
            <button class="apply_filter" type="submit">Apply Filters</button>
            <button id="clearFilter">Clear Filter</button>
        </div>
    </form>
</aside>


                <script src="../assets/js/shop,js"></script>

                <?php
                                                    if ($result !== null && $result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) :
                                                            $product_id = $row['product_id']; // Assign product_id for each product

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
                                                                $reviews_query->bind_param("i", $product_id); // Use the current product_id
                                                                $reviews_query->execute();
                                                                $reviews_result = $reviews_query->get_result();
                                                                while ($review_row = $reviews_result->fetch_assoc()) {
                                                                    $reviews[] = $review_row;
                                                                    $sumRatings += $review_row['rating']; // Add up all the ratings
                                                                }
                                                                $totalReviews = count($reviews); // Calculate total number of reviews
                                                                $reviews_query->close();
                                                            }

                                                            // Calculate the average rating
                                                            $averageRating = $totalReviews > 0 ? $sumRatings / $totalReviews : 0;
                                                            $roundedRating = round($averageRating);

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
                <style>

                </style>

                <!-- <a href="../pd/?product_id=<?php echo $row['product_id']; ?>"> -->
                    <div  class="container">
                        <div onclick="location.href = '../pd/?product_id=<?php echo $row['product_id']; ?>';" class="product-image">
                            <?php 
                                                                                        if ($row['choiced'] == 0 ) {
                                                                                            echo '';
                                                                                            } else {
                                                                                                echo ' <div class="ch">
                                                                                        <img width="130px" src="../assets/ch.svg">

                                                                                        </div> ';
                                                                                    }
                                                                                    ?>
                            <img src="<?php echo htmlspecialchars($row['image_main']); ?>"
                                alt="<?php echo htmlspecialchars($row['brand'] . ' ' . $row['model']); ?>">
                        </div>
                        <div  class="product-details">
                            <h2 onclick="location.href = '../pd/?product_id=<?php echo $row['product_id']; ?>';" class="product-title">
                                <?php echo htmlspecialchars($row['title'] . $row['brand'] . ' ' . $row['model']); ?>
                            </h2>
                            <div onclick="location.href = '../pd/?product_id=<?php echo $row['product_id']; ?>';" class="reviews-counter">
                                <div class="rate">

                                    <?php if ($totalReviews > 0): ?>
                                    <div class="product-rating">
                                        <span class="rating">
                                            <?php 
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $roundedRating) {
                                                        echo "<i style='font-size:18px;' class='material-icons star filled'>star</i>"; // Filled star
                                                    } else {
                                                        echo "<i style='font-size:18px;' class='material-icons star'>star_border</i>"; // Empty star
                                                    }
                                                }
                                                ?>
                                        </span>(<?php echo $totalReviews; ?>)<br>

                                        <span style='font-size: 14px;'>(<?php echo number_format($averageRating, 1); ?>
                                            out of 5 stars)</span><br>
                                    </div>
                                    <?php else: ?>
                                    <div class="product-rating">
                                        <span>No reviews yet</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div><br>
                            <!-- <div class="deal">Limited time deal</div> -->
                            <div onclick="location.href = '../pd/?product_id=<?php echo $row['product_id']; ?>';" class="product-price-discount">
                                <span><sup>₹</sup><?php echo number_format($row['price'], 2); ?></span>
                                <span class="line-through"><sup>₹</sup><?php echo number_format($row['offer_prices'], 2); ?></span>
                            </div>
                            <p onclick="location.href = '../pd/?product_id=<?php echo $row['product_id']; ?>';" class="subtitle-desc">
                                <?php echo htmlspecialchars($row['short_des']."  " . $row['description']); ?></p>
                            <!-- <p class="delivery">Service: Setup at delivery</p> -->
                            <div class="row">                </a>
                            <div class="like_button">
    <button onclick="toggleWhitelist(<?php echo htmlspecialchars($row['product_id']); ?>, '<?php echo !$is_whitelisted ? 'add' : 'remove'; ?>')" 
            class="action_has <?php echo $is_whitelisted ? 'has_liked' : ''; ?>" 
            aria-label="<?php echo !$is_whitelisted ? 'like' : 'remove'; ?>">
        <span class="material-icons" style="color: #3e0c40;">
            <?php echo !$is_whitelisted ? 'favorite_border' : 'favorite'; ?>
        </span>
    </button>
</div>

<script src="./assets/js/fav.js"></script>

                                <button onclick="location.href = '../pd/?product_id=<?php echo $row['product_id']; ?>';" class="button">View Product</button>
                            </div>
                        </div>
                    </div>

        </div>
        <?php endwhile; ?>
        <?php } else { ?>
        <div class="ab-o-oa" aria-hidden="true">
            <div class="ZAnhre"><img class="wF0Mmb" src="../assets/p_n_found.svg" width="300px" height="300px" alt="">
            </div>
            <div class="ab-o-oa-r">
                <div class="ab-o-oa-qc-V">No product Found</div>
                <div class="ab-o-oa-qc-r"> matching your search criteria.</div>
            </div>
        </div>
        <?php } ?>
        <h3>&#8592; That's all &#8594;</h3>
        </section>
        </div>
    </main>
    <footer id="footer"></footer>
</body>

</html>