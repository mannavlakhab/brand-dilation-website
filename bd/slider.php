<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_slide'])) {
    // Handle adding new slide
    $caption = $_POST['caption'];
    $alt_text = $_POST['alt_text'];
    
    // File upload handling for desktop image
    $target_dir = "../uploads/";
    $desktop_target_file = $target_dir . basename($_FILES["desktop_image"]["name"]);
    $mobile_target_file = $target_dir . basename($_FILES["mobile_image"]["name"]);

    $desktopImageFileType = strtolower(pathinfo($desktop_target_file, PATHINFO_EXTENSION));
    $mobileImageFileType = strtolower(pathinfo($mobile_target_file, PATHINFO_EXTENSION));

    // Check if desktop image is valid
    $checkDesktop = getimagesize($_FILES["desktop_image"]["tmp_name"]);
    $checkMobile = getimagesize($_FILES["mobile_image"]["tmp_name"]);

    if ($checkDesktop !== false && $checkMobile !== false) {
        // Move uploaded files
        if (move_uploaded_file($_FILES["desktop_image"]["tmp_name"], $desktop_target_file) &&
            move_uploaded_file($_FILES["mobile_image"]["tmp_name"], $mobile_target_file)) {
            // Insert slide into database
            $sql = "INSERT INTO slideshow (image_path, mobile_image_path, caption, alt_text) 
                    VALUES ('$desktop_target_file', '$mobile_target_file', '$caption', '$alt_text')";
            if (mysqli_query($conn, $sql)) {
                echo "Slide added successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your files.";
        }
    } else {
        echo "One or both of the files are not valid images.";
    }
}

if (isset($_POST['remove_slide'])) {
  // Handle removing slide
  $remove_slide_id = $_POST['remove_slide_id'];

  // Check if slide ID is provided
  if (!empty($remove_slide_id)) {
      // Delete slide from database
      $sql = "DELETE FROM Slideshow WHERE slide_id = '$remove_slide_id'";
      if (mysqli_query($conn, $sql)) {
          echo "<div id='InfoBanner'>
<span class='reversed reversedRight'>
  <span>
    &#9888;
  </span>
</span>
<span class='reversed reversedLeft'>Sorry, error for removing file.!!
</span> 
</div>";
      } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($conn);
      }
  } else {
      echo "Please select a slide to remove.";
  }
}

// Function to fetch existing slides from the database
function getSlides() {
    global $conn;
    $slides = array();
    $sql = "SELECT * FROM slideshow";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $slides[] = $row;
        }
    }
    return $slides;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Slideshow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include 'hsidebar.php'; ?>
        <div class="flex-1 p-4 pt-6 pb-8 mb-4 bg-white rounded shadow-md">
            <h2 class="text-2xl font-bold mb-4">Add / Remove Slides</h2>
            <!-- Form for adding/removing slides -->
            <form method="POST" enctype="multipart/form-data">
                <!-- File input for uploading desktop image -->
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="desktop_image">
                            Upload Desktop Image:
                        </label>
                        <input type="file" id="desktop_image" name="desktop_image" accept="image/*" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                    <!-- File input for uploading mobile image -->
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="mobile_image">
                            Upload Mobile Image:
                        </label>
                        <input type="file" id="mobile_image" name="mobile_image" accept="image/*" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                </div>
                <!-- Input fields for caption and alt text -->
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="caption">
                            Caption:
                        </label>
                        <input type="text" id="caption" name="caption" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="alt_text">
                            Alt Text:
                        </label>
                        <input type="text" id="alt_text" name="alt_text" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    </div>
                </div>
                <!-- Button to add slide -->
                <button type="submit" name="add_slide" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add Slide
                </button>
            </form>
            <br><br>
            <form method="POST" enctype="multipart/form-data">
                <!-- Dropdown to select slide to remove -->
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="remove_slide_id">
                    Remove Slide:
                </label>
                <select name="remove_slide_id" id="remove_slide_id" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white">
                    <option value="" disabled="disabled" selected="selected">Removed Slide</option>
                    <?php
                    $slides = getSlides();
                    foreach ($slides as $slide) {
                        echo "<option value='" . $slide['slide_id'] . "'>" . $slide['caption'] . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="remove_slide" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Remove Slide
                </button>
            </form>
            <!-- Display existing slides -->
            <h2 class="text-2xl font-bold mb-4">Existing Slides</h2>
            <div class="flex flex-wrap -mx-3 mb-6">
                <?php foreach ($slides as $slide): ?>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <img src="../<?php echo $slide['image_path']; ?>" alt="<?php echo $slide['alt_text']; ?>" class="w-full h-64 object-cover">
                        <p class="text-gray-700 text-sm font-bold mb-2">Caption: <?php echo $slide['caption']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>