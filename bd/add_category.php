<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
// Initialize variables (set to empty initially)
$nameErr = $imgErr = $descriptionErr = "";
$name = $description = "";

// Form submitted (using POST method)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Check if name is empty
  if (empty($_POST["name"])) {
    $nameErr = "Category name is required";
  } else {
    $name = test_input($_POST["name"]);
  }

  // Check if image file is uploaded
  if (!empty($_FILES["img"]["name"])) {
    $target_dir = "../uploads/"; // Directory where uploaded images will be stored
    $target_file = $target_dir . basename($_FILES["img"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["img"]["tmp_name"]);
    if ($check !== false) {
      // Check file size (optional, you can set your own size limit)
      if ($_FILES["img"]["size"] > 500000) { // 500KB
        $imgErr = "Sorry, your file is too large.";
      } elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $imgErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
      } else {
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
          $img = $target_file;
        } else {
          $imgErr = "Sorry, there was an error uploading your file.";
        }
      }
    } else {
      $imgErr = "File is not an image.";
    }
  } else {
    $imgErr = "Category image is required";
  }

  // Description is optional, no validation needed here

  // Insert category if no errors
 // Insert category if no errors
if (empty($nameErr) && empty($imgErr)) {
    $sql = "INSERT INTO Categories (name, img, description) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $img, $description);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Category added successfully!";
    } else {
        $error = "Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

    
  }

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Category</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<!-- <div id="__next" bis_skin_checked="1">
        <div style="z-index:100;  opacity: 0.5; right:-10px" class="absolute z-0 top-0 inset-x-0 flex justify-center overflow-hidden pointer-events-none"
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
      -->
  <div class="flex h-screen">
    <?php include 'hsidebar.php'; ?>
    <div class="flex-1 p-4 pt-6 pb-8 h-fit	 mb-4 bg-white rounded shadow-md">
      <h2 class="text-2xl font-bold mb-4">Add Category</h2>
      <a href="view_categories.php" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">View / edit or delete Category</a><br><br><br>
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
        <div class="flex flex-wrap -mx-3 mb-6">
          <div class="w-full px-3">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
              Category Name:
            </label>
            <span class="error text-red-500">* <?php echo $nameErr; ?></span><br>
            <input type="text" name="name" id="name" value="<?php echo $name; ?>" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
          </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
          <div class="w-full px-3">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="img">
              Category Image:
            </label>
            <span class="error text-red-500">* <?php echo $imgErr; ?></span><br>
            <input type="file" name="img" id="img" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
          </div>
        </div>
        <div class="flex flex-wrap -mx-3 mb-6">
          <div class="w-full px-3">
            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="description">
              Description:
            </label><br>
            <textarea name="description" id="description" rows="5" cols="30" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white"><?php echo $description; ?></textarea>
          </div>
        </div>
        <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Add Category</button>
      </form>
    </div>
  </div>

  <?php
  // Display success or error message (if any)
  if (isset($message)) {
    echo "<p class='text-green-500'>$message</p>";
  }
  if (isset($error)) {
    echo "<p class='text-red-500'>$error</p>";
  }
  ?>
</body>
</html>