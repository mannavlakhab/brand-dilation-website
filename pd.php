<?php
// Include the database connection file
require_once 'db_connect.php'; // Ensure this path is correct
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
    die("Product not found");
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


// Close the connection after all operations
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/pd.css">
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <style>
@import url('https://fonts.googleapis.com/css2?family=Kode+Mono:wght@400..700&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');
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
    <title><?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?></title>
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
</head>
<body>
  
<header>
        
        <!--=============== HEADER ===============-->
        <div id="header"></div>
    
        <button class="goback" onclick="location.href = 'index.php';">
            <svg height="16" width="16" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 1024 1024">
                <path
                    d="M874.690416 495.52477c0 11.2973-9.168824 20.466124-20.466124 20.466124l-604.773963 0 188.083679 188.083679c7.992021 7.992021 7.992021 20.947078 0 28.939099-4.001127 3.990894-9.240455 5.996574-14.46955 5.996574-5.239328 0-10.478655-1.995447-14.479783-5.996574l-223.00912-223.00912c-3.837398-3.837398-5.996574-9.046027-5.996574-14.46955 0-5.433756 2.159176-10.632151 5.996574-14.46955l223.019353-223.029586c7.992021-7.992021 20.957311-7.992021 28.949332 0 7.992021 8.002254 7.992021 20.957311 0 28.949332l-188.073446 188.073446 604.753497 0C865.521592 475.058646 874.690416 484.217237 874.690416 495.52477z">
                </path>
            </svg>
            <span>Back</span>
        </button>
        <h1>Product Detail about - <?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?></h1>
    </header>
    <script>
        function showImage(imagePath) {
            document.getElementById('mainImage').src = imagePath;
        }
    </script>
    <section class="product-details">
        <div class="img_am">
            <div class="product-image">
                <img id="mainImage" src="<?php echo htmlspecialchars($product['image_main']); ?>" alt="Main Image">
            </div>
            <div class="additional-images">
                <img src="<?php echo htmlspecialchars($product['image_main']); ?>"
                     alt="<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>"
                     onclick="showImage('<?php echo htmlspecialchars($product['image_main']); ?>')">

                <?php foreach ($images as $image_url): ?>
                <img src="<?php echo htmlspecialchars($image_url); ?>"
                     alt="<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>"
                     onclick="showImage('<?php echo htmlspecialchars($image_url); ?>')">
                <?php endforeach; ?>
            </div>
        </div>
        <div class="product-info">
            <h2><?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?></h2>
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
            <p><?php echo nl2br(htmlspecialchars($product['short_des'])); ?></p>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <p><strong>Price:</strong> ₹<?php echo number_format($product['price'], 2); ?></p>
            <div class="radio-container flex-container">  <label for="variations">Choose a variation:</label>

<?php foreach ($variations as $variation): ?>
  <div class="radio-option">
    <input type="radio" name="variation" id="variation-<?php echo $variation['variation_id']; ?>"  value="<?php echo $variation['variation_id']; ?>"
           onclick="updateVariationId('<?php echo $variation['variation_id']; ?>')" selected required>
    <label for="variation-<?php echo $variation['variation_id']; ?>">  <?php echo htmlspecialchars($variation['variation_name']) . ": " . htmlspecialchars($variation['variation_value']) . " (+₹" . number_format($variation['price_modifier'], 2) . ")"; ?>
    </label>
  </div>
<?php endforeach; ?>
</div>
            <form action="cart.php" onsubmit="return validateForm()" method="POST">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <input type="hidden" name="variation_id" id="variation_id" value="defult">
                <button type="submit" name="add_to_cart" class="CartBtn">
                <span class="IconContainer">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 576 512" fill="#3c0e40" class="cart">
                        <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z">
                        </path>
                    </svg>
                </span>
                    <p class="text">Add to Cart</p>
                </button>
            </form>

            

        </div>
 



        <div class="reviews-container">

        <h2>Customers Reviews</h2>
        <button><a href="#add_reviews">Add Reviews</a></button>
    <!-- Review List -->
<div class="reviews">
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
                     alt="Review Image" 
                     class="review-image" 
                     data-image-src="../<?php echo htmlspecialchars($review['review_image_path']); ?>"
                     onclick="openModal(this)">
            <?php endif; ?><br>
            <small class="review-date">Reviewed on <?php echo htmlspecialchars($review['review_date']); ?></small>
        </div>
    <?php endforeach; ?>
</div>
<label id="add_reviews"    for="form"></label>

<!-- Modal for Image Preview -->
<div id="imageModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modalImage">
    <div id="caption"></div>
</div>

<!-- Review Form -->
<div class="review-form-container">
    <form action="submit_review.php" method="post" class="review-form" enctype="multipart/form-data">
        <h3>Add Your review</h3>
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <label for="rating">Rating:</label>
            <select name="rating" id="rating" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
            <label for="review_text">Your Review:</label>
            <textarea name="review_text" id="review_text" required></textarea>
            <label for="review_image">Upload Image:</label>
            <input type="file" name="review_image" id="review_image" accept="image/*">
            <button type="submit">Submit Review</button>
        </form>
    </div>
</div>
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
    </section>
    
    <!--=============== Footer ===============-->
    <div id="footer"></div>

</body>
</html>
