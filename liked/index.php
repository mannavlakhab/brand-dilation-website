<?php
session_start();
include '../db_connect.php'; // Include your database connection

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

// Assuming user_id is stored in session after login
$user_id = $_SESSION['user_id'];

// Function to check if a product exists
function productExists($conn, $product_id) {
    $query = "SELECT COUNT(*) FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Handle removing from whitelist
if (isset($_POST['action']) && $_POST['action'] === 'remove') {
    $product_id = intval($_POST['product_id']);

    // Check if product exists
    if (!productExists($conn, $product_id)) {
        $message = "Product ID does not exist.";
    } else {
        // Remove product from user's whitelist
        $query = "DELETE FROM whitelist WHERE product_id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $product_id, $user_id);

        if ($stmt->execute()) {
            $update_query = "UPDATE products SET is_whitelisted = 0 WHERE product_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('i', $product_id);
            $update_stmt->execute();
            $message = "Product removed from your whitelist.";
        } else {
            $message = "Error removing product from whitelist.";
        }
        $stmt->close();
    }
}

// Fetch whitelisted products for this user
$query = " SELECT p.* 
    FROM products p
    JOIN whitelist w ON p.product_id = w.product_id
    WHERE w.user_id = ?
    ORDER BY w.date_added desc";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="../assets/js/internet-check.js" defer></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liked Collection</title>
    <link rel="stylesheet" href="../assets/css/SHOP.css">
    <link rel="stylesheet" href="../assets/css/btn.css">
    <style>
    /* Reset default margins and paddings */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    @font-face {
    font-family: 'bd title';
    /* src: url('../assets/font/bd title.woff2') format('woff2'); */
    src: url('../font/pp.woff2') format('woff2');
    
  }
@font-face {
    font-family: 'bd';
    /* src: url('../assets/font/bd title.woff2') format('woff2'); */
    src: url('../../font/BD.woff2') format('woff2');
}
    /* Basic page styles */
    body {
        font-family: bd, sans-serif;
        line-height: 1.6;
        background-color: #f4f4f4;
        padding: 20px;
    }
    
    /* Container for the main content */
    #product-list {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
    }
    
    
    h2{
        font-family: bd title, sans-serif;
    }
    
    /* Message display */
    p {
        font-size: 1em;
        color: #d9534f;
        margin-bottom: 20px;
    }

    .empty {
        display: block;
        height: auto;
        left: 78px;
        margin-left: auto;
        margin-right: auto;
        top: 0;
        width: 200px;
    }

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

    }



    


  .k-pp-c{
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    grid-gap: 20px;
  }
  .card-of-product {
    --font-color: #323232;
    --font-color-sub: #666;
    --bg-color: #fff;
    --main-color: #323232;
    --main-focus: #ff6666;
    width: 300px;
      height: auto;
    background: var(--bg-color);
    border: 1px solid #ccc;
    box-shadow: 2px 2px var(--main-color);
    border-radius: 5px;
    display: flex;
    justify-content: center;
    flex-direction: column;
    justify-content: flex-start;
    padding: 10px;
    gap: 5px;
    margin: 2%;
    transition: all ease-in-out .3s;
  }
  .card-of-product:hover {
   
    border: 2px solid var(--main-color);
  }
  
  .img-of-product-card {
    /* clear and add new css */
  transition: all 0.5s;
}
.img-of-product-card img{
border-radius: 3px
}

.img-of-product-card .img {
  /* delete */
   transform: scale(1);
   position: relative;
   box-sizing: border-box;
   width: 80px;
   height: 80px;
   border-top-left-radius: 80px 50px;
   border-top-right-radius: 80px 50px;
   border: 2px solid black;
   background-color: #228b22;
   background-image: linear-gradient(to top,transparent 10px,rgba(0,0,0,0.3) 10px,rgba(0,0,0,0.3) 13px,transparent 13px);
 }
 
 .img-of-product-card .img::before {
  /* delete */
   content: '';
   position: absolute;
   width: 65px;
   height: 110px;
   margin-left: -32.5px;
   left: 50%;
   bottom: -4px;
   background-repeat: no-repeat;
   background-image: radial-gradient(ellipse at center,rgba(0,0,0,0.7) 30%,transparent 30%),linear-gradient(to top,transparent 17px,rgba(0,0,0,0.3) 17px,rgba(0,0,0,0.3) 20px,transparent 20px),linear-gradient(to right,black 2px,transparent 2px),linear-gradient(to left,black 2px,transparent 2px),linear-gradient(to top,black 2px,#228b22 2px);
   background-size: 60% 10%,100% 100%,100% 65%,100% 65%,100% 50%;
   background-position: center 3px,center bottom,center bottom,center bottom,center bottom;
   border-radius: 0 0 4px 4px;
   z-index: 2;
 }
 
 .img-of-product-card .img::after {
  /* delete */
   content: '';
   position: absolute;
   box-sizing: border-box;
   width: 28px;
   height: 28px;
   margin-left: -14px;
   left: 50%;
   top: -13px;
   background-repeat: no-repeat;
   background-image: linear-gradient(80deg,#ffc0cb 45%,transparent 45%),linear-gradient(-175deg,#ffc0cb 45%,transparent 45%),linear-gradient(80deg,rgba(0,0,0,0.2) 51%,rgba(0,0,0,0) 51%),linear-gradient(-175deg,rgba(0,0,0,0.2) 51%,rgba(0,0,0,0) 51%),radial-gradient(circle at center,#ffa6b6 45%,rgba(0,0,0,0.2) 45%,rgba(0,0,0,0.2) 52%,rgba(0,0,0,0) 52%),linear-gradient(45deg,rgba(0,0,0,0) 48%,rgba(0,0,0,0.2) 48%,rgba(0,0,0,0.2) 52%,rgba(0,0,0,0) 52%),linear-gradient(65deg,rgba(0,0,0,0) 48%,rgba(0,0,0,0.2) 48%,rgba(0,0,0,0.2) 52%,rgba(0,0,0,0) 52%),linear-gradient(22deg,rgba(0,0,0,0) 48%,rgba(0,0,0,0.2) 48%,rgba(0,0,0,0.2) 54%,rgba(0,0,0,0) 54%);
   background-size: 100% 100%,100% 100%,100% 100%,100% 100%,100% 100%,100% 75%,100% 95%,100% 60%;
   background-position: center center;
   border-top-left-radius: 120px;
   border-top-right-radius: 10px;
   border-bottom-left-radius: 10px;
   border-bottom-right-radius: 70px;
   border-top: 2px solid black;
   border-left: 2px solid black;
   transform: rotate(45deg);
   z-index: 1;
 }
 
 @font-face {
  font-family: 'BD';
  src: url('../font/BD.woff2') format('woff2');
  
}
 .footer-of-product-card {
   display: flex;
   justify-content: center;
   align-items: center;
 }
 
 .price-of-product-card {
   font-size: 20px;
   font-weight: 500;
   color: var(--font-color);
 }
 
 .price-of-product-card span {
   font-size: 15px;
   font-weight: 500;
   color: var(--font-color-sub);
 }

 .title-of-product-card{
   margin-top: 2%;
   text-transform: uppercase;
   font-family: Montserrat, sans-serif;
   line-height: 1.1;
   font-size: 17px;
   font-weight: 650;
   text-align: center;
   color: var(--font-color);
   display:-webkit-box;
     -webkit-box-orient: vertical;     /* Specifies the orientation */
     -webkit-line-clamp: 2;            /* Limits the text to 2 lines */
     overflow: hidden;                 /* Hides any overflowing content */
     text-overflow: ellipsis;          /* Adds "..." when the text is truncated */
 }

.choiced-product-card img{
 width: 130px;

}
.like_button-of-product-card{
 margin-bottom: 2%;
}
 .action_has_click {
   cursor: pointer;
   display: flex;
   align-items: center;
   justify-content: center;
   height: calc(11px * 2.75);
   width: calc(11px * 2.75);
   padding: 0.4rem 0.5rem;
   border-radius: 3.375rem;
   background: #ffff;
   border: 0.0625rem solid #ff6666;
 }
 
 .has_liked-product-card svg{
   overflow: visible;
   height: calc(12px * 1.75);
   width: calc(12px * 1.75);
   --ease: cubic-bezier(0.5, 0, 0.25, 1);
   --zoom-from: 1.75;
   --zoom-via: 0.75;
   --zoom-to: 1;
   --duration: 1s;
 }
 
 .has_liked-product-card:hover {
   transition: border-color var(--duration) var(--ease);
   border-color: #ff6666;
 }
 
 .has_liked-product-card:hover svg{
   fill: #3c0e40;
   color: #000000;
 }

/* New for computer art only for mobile view*/
@media (max-width: 768px) {
 
  .k-pp-c{
    grid-template-columns: 1fr 1fr;
    grid-gap: 9px;
  }

  .card-of-product {
    
    width: auto;
  }
  
  
   
   .price-of-product-card {
     font-size: 15px;
   }
   
   .price-of-product-card span {
     font-size: 10px;
   }
 
   .title-of-product-card{
     font-size: 12px;
     -webkit-line-clamp: 3;
     max-width: 120px;
   }
 
 .choiced-product-card img{
   width: 90px;
 }
   .action_has_click {
     height: calc(10px * 2.75);
     width: calc(10px * 2.75);
   }
   
   .has_liked-product-card svg{
     height: calc(10px * 1.75);
     width: calc(10px * 1.75);
   }
 
  
  /* Closing computer part  */
}


.ab-o-oa-qc-r{
  color: #454545;
}
    </style>

</head>

<body>
    <div class="new_bar">
        <button class="btn-trick-new" onclick="history.back()">Go Back</button>
    </div>
    <center>
        <h2>Your Liked Products</h2>
        <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </center>
    <div id="product-list">
        <?php if (count($products) > 0): ?>
        <?php foreach ($products as $product): ?>
        <div class="products-card">
            <a href="../pd/?product_id=<?php echo htmlspecialchars($product['product_id']); ?>">
                <div class="card-img">
                    <img style="width: 100%; height: auto;"
                        src="../<?php echo htmlspecialchars($product['image_main']); ?>"
                        alt="<?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>">
                </div>
                <div class="card-title"><?php echo htmlspecialchars($product['brand'] . ' ' . $product['model']); ?>
                </div>
                <div class="card-subtitle"><?php echo htmlspecialchars($product['short_des']); ?></div>
                <hr class="card-divider">
                <div class="card-footer">
                    <div class="card-price"><span>₹</span><?php echo htmlspecialchars($product['price']); ?></div>
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
            <button class="remove-btn btn-trick-new"
                data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>">Remove from like</button>
        </div>
        <?php endforeach; ?>
        <?php else: ?>

        <div class="ab-o-oa" aria-hidden="true">
            <div class="ZAnhre"><img class="wF0Mmb" src="../assets/like.svg" width="300px" height="300px" alt=""></div>
            <div class="ab-o-oa-r">
                <div class="ab-o-oa-qc-V">Likes Not Found</div>
                <div class="ab-o-oa-qc-r">Add your favroute product to like.</div>
            </div>
        </div>
        <!-- <p>No whitelisted products found.</p> -->
        <!-- <img class="empty" src="../assets/empty.svg" alt="" ><br>
             <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">Drop files here</div><div class="ab-o-oa-qc-r">or use the ‘New’ button.</div></div> -->
        <?php endif; ?>
    </div>

    

    <script>
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');

            // Create form and submit it
            const form = document.createElement('form');
            form.method = 'post';
            form.action = '';

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_id';
            input.value = productId;

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'remove';

            form.appendChild(input);
            form.appendChild(actionInput);

            document.body.appendChild(form);
            form.submit();
        });
    });
    </script>
</body>

</html>