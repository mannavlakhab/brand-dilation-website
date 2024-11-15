<?php
// Database connection
$host = 'localhost';
$db = 'shop';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch categories from the database for dropdown selection
$sql_categories = "SELECT category_id, name FROM Categories";
$result_categories = mysqli_query($conn, $sql_categories);

// Insert product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $category_id = $_POST['category_id'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];
    $description = $_POST['description'];
    $stock_quantity = $_POST['stock_quantity'];
    $price = $_POST['price'];
    $refurbished = isset($_POST['refurbished']) ? 1 : 0;
    $digital_product = isset($_POST['digital_product']) ? 1 : 0;
    $short_des = $_POST['short_des'];
    $is_whitelisted = isset($_POST['is_whitelisted']) ? 1 : 0;
    $choiced = isset($_POST['choiced']) ? 1 : 0;
    $offer_prices = $_POST['offer_prices'];
    $title = $_POST['title'];

    // Handle main image upload
    $image_main = $_FILES['image_main'];
    $image_main_path = '../product_images/' . basename($image_main['name']);
    move_uploaded_file($image_main['tmp_name'], $image_main_path);

    // Insert product data into `products` table
    $sql_product = "INSERT INTO products (category_id, brand, model, description, image_main, stock_quantity, price, refurbished, digital_product, short_des, is_whitelisted, choiced, offer_prices, title)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_product);
    $stmt->bind_param("issssidiissiis", $category_id, $brand, $model, $description, $image_main_path, $stock_quantity, $price, $refurbished, $digital_product, $short_des, $is_whitelisted, $choiced, $offer_prices, $title);

    if ($stmt->execute()) {
        $product_id = $stmt->insert_id; // Get the ID of the inserted product

        // Handle multiple additional product images and insert them into the `product_images` table
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $key => $image_name) {
                $image_tmp_name = $_FILES['images']['tmp_name'][$key];
                $image_path = '../product_images/' . basename($image_name);

                // Move the uploaded image to the uploads directory
                if (move_uploaded_file($image_tmp_name, $image_path)) {
                    // Insert image data into `product_images` table
                    $sql_image = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                    $stmt_image = $conn->prepare($sql_image);
                    $stmt_image->bind_param("is", $product_id, $image_path);
                    $stmt_image->execute();
                }
            }
        }

        echo "Product and images inserted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white transition duration-500 ease-in-out">
  <input type="checkbox" id="dark-mode-toggle" class="mr-2" checked onclick="toggleDarkMode()">
  <label for="dark-mode-toggle">Dark Mode</label>

  <script>
    function toggleDarkMode() {
      document.body.classList.toggle('light-mode');
    }
  </script>

  <style>
    .light-mode {
      background-color: #f9f9f9;
      color: #333;
    }
    .light-mode input, .light-mode textarea, .light-mode select {
      background-color: #fff;
      color: #333;
      border: 1px solid #ddd;
    }
    .light-mode input:focus, .light-mode textarea:focus, .light-mode select:focus {
      background-color: #fff;
      color: #333;
      border: 1px solid #aaa;
    }
  </style>

<div id="__next" bis_skin_checked="1">
        <div style="z-index:-2; opacity:0.1;" class="absolute z-0 top-0 inset-x-0 flex justify-center overflow-hidden pointer-events-none"
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
  <!-- HTML Form -->
  <form method="post" enctype="multipart/form-data" class="max-w-md h-fit	mx-auto p-4 pt-6 pb-8 mb-4 bg-gray-800 dark:bg-gray-800 rounded shadow-md transition duration-500 ease-in-out">
    <h2 class="text-2xl font-bold mb-4 text-white transition duration-500 ease-in-out">Add Product</h2>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="category_id">
          Category
        </label>
      </div>
      <div class="w-3/4 px-3">
        <select name="category_id" id="category_id" class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
          <option selected disabled>Choose a Category</option>
          <?php while ($row = mysqli_fetch_assoc($result_categories)) : ?>
            <option value="<?php echo $row['category_id']; ?>"><?php echo $row['name']; ?></option>
          <?php endwhile; ?>
        </select>
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
  <div class="w-1/4 px-3 mb-6 md:mb-0">
    <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="title">
      Title
    </label>
  </div>
  <div class="w-3/4 px-3">
    <input type="text" name="title" class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
  </div>
</div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="brand">
          Brand
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="text" name="brand" class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="model">
          Model
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="text" name="model" class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="description">
          Description
        </label>
      </div>
      <div class="w-3/4 px-3">
        <textarea name="description" class="appearance-none block w-full bg-gray- 700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out"></textarea>
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="image_main">
          Main Image
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="file" name="image_main" id="image_main" required class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="images">
          Additional Images
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="file" name="images[]" id="images" multiple class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="stock_quantity">
          Stock Quantity
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="text" name="stock_quantity" class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="price">
          Price
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="text" name="price" class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="refurbished">
          Refurbished
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="checkbox" name="refurbished" value="1" class="mr-2">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="digital_product">
          Digital Product
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="checkbox" name="digital_product" value="1" class="mr-2">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="short_des">
          Short Description
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="text" name="short_des" class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="is_whitelisted">
          Is Whitelisted
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="checkbox" name="is_whitelisted" value="1" class="mr-2">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="choiced">
          Choiced
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="checkbox" name="choiced" value="1" class="mr-2">
      </div>
    </div>
    <div class="flex flex-wrap -mx-3 mb-6">
      <div class="w-1/4 px-3 mb-6 md:mb-0">
        <label class="block uppercase tracking-wide text-gray-300 text-xs font-bold mb-2 transition duration-500 ease-in-out" for="offer_prices">
          Offer Price
        </label>
      </div>
      <div class="w-3/4 px-3">
        <input type="text" name="offer_prices" class="appearance-none block w-full bg-gray-700 text-gray-300 border border-gray-700 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-gray-700 transition duration-500 ease-in-out">
      </div>
    </div>
    <input type="submit" value="Insert Product" class="bg-orange-500 dark:bg-orange-700 hover:bg-orange-700 dark:hover:bg-orange-500 text-white font-bold py-2 px-4 rounded transition duration-500 ease-in-out">
  </form>
</body>
</html>