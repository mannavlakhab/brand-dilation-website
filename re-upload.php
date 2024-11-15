<?php
session_start();
require_once 'db_connect.php';

// Check if user is logged in, redirect to login if not
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  // Check if "Remember Me" cookie is set
  if (isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    // Optionally, you can re-validate the user with the database here
} else {
  // Store the current page in session to redirect after login
  $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
  header('Location: login.php');
  exit();
}}

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$order = null;

if ($order_id) {
  $sql = "SELECT o.order_id, o.customer_id, o.order_status, o.shipping_address, o.total_price, o.shipping_cost, 
                o.payment_method, o.payment_status, o.payment_screenshot, o.payment_details, o.order_date, 
                c.first_name, c.last_name, c.email, c.phone_number, c.address
          FROM Orders o
          JOIN Customers c ON o.customer_id = c.customer_id
          WHERE o.order_id = ?";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $order_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $order = $result->fetch_assoc();

  if (!$order) {
    echo "Invalid order ID.";
    exit();
  }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Handle screenshot upload if provided
  $screenshot_file = ''; // Placeholder for storing the filename
  if ($_FILES['payment_screenshot']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = './screenshots/';
    $unique_name = uniqid() . '_' . basename($_FILES['payment_screenshot']['name']);
    $screenshot_file =  $upload_dir . $unique_name;

    $allowed_extensions = ['png', 'jpg', 'jpeg'];
    $file_extension = strtolower(pathinfo($screenshot_file, PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
      echo '
       <!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Screenshot Upload</title>
</head>
<body>
 <div class="ab-o-oa" aria-hidden="true">
                    <div class="ZAnhre">
                    <img class="wF0Mmb" src="../assets/invalid_type.svg" width="300px" height="300px" alt=""></div>
                    <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">Invalid file type</div>
                    <div class="ab-o-oa-qc-r"> Only PNG, JPG, and JPEG files are allowed. </div></div><br>
        <button class="btn-trick-new" onclick="history.back()">Go Back</button>
                </div>
                <style>
                    
.ab-o-oa{
    display: flex;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    align-items: center;
    width: fit-;
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
    
/* From Uiverse.io by e-coders */ 
.btn-trick-new {
    appearance: none;
    background-color: #FAFBFC;
    border: 1px solid rgba(27, 31, 35, 0.15);
    border-radius: 6px;
    box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
    box-sizing: border-box;
    color: #24292E;
    cursor: pointer;
    display: inline-block;
    font-family: "Montserrat", sans-serif;
    font-size: 14px;
    font-weight: 700;
    line-height: 20px;
    list-style: none;
    padding: 6px 16px;
    position: relative;
    transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
    vertical-align: middle;
    white-space: nowrap;
    word-wrap: break-word;
   }
   
   .btn-trick-new:hover {
    background-color: #F3F4F6;
    text-decoration: none;
    transition-duration: 0.1s;
   }
   
   .btn-trick-new:disabled {
    background-color: #FAFBFC;
    border-color: rgba(27, 31, 35, 0.15);
    color: #959DA5;
    cursor: default;
   }
   
   .btn-trick-new:active {
    background-color: #EDEFF2;
    box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
    transition: none 0s;
   }
   
   .btn-trick-new:focus {
    outline: 1px transparent;
   }
   
   .btn-trick-new:before {
    display: none;
   }
   
   .btn-trick-new:-webkit-details-marker {
    display: none;
   }
</style>

</body>
</html>


';
      exit();
    }

    if (!move_uploaded_file($_FILES['payment_screenshot']['tmp_name'], $screenshot_file)) {
      echo "Failed to upload screenshot.";
      exit();
    }
  

   $order_status = 're-uploaded screenshot';

   // Only save the relative path in the database
   $screenshot_file_relative = 'screenshots/' . $unique_name;

   $update_sql = "UPDATE Orders SET payment_screenshot = ?, order_status = ? WHERE order_id = ?";
   $update_stmt = $conn->prepare($update_sql);
   $update_stmt->bind_param("ssi", $screenshot_file_relative, $order_status, $order_id);
   $update_stmt->execute();

   header("Location: ../profile.php?page=orders&success=Screenshot uploaded successfully!");
   exit();
 } else {
   echo '<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Screenshot Upload</title>
</head>
<body>
 <div class="ab-o-oa" aria-hidden="true">
                    <div class="ZAnhre">
                    <img class="wF0Mmb" src="../assets/failed.svg" width="300px" height="300px" alt=""></div>
                    <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">file upload error</div>
                    <div class="ab-o-oa-qc-r"> something went wrong </div></div><br>
        <button class="btn-trick-new" onclick="history.back()">Retry</button>
                </div>
                <style>
                    
.ab-o-oa{
    display: flex;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    align-items: center;
    width: fit-;
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
    
/* From Uiverse.io by e-coders */ 
.btn-trick-new {
    appearance: none;
    background-color: #FAFBFC;
    border: 1px solid rgba(27, 31, 35, 0.15);
    border-radius: 6px;
    box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
    box-sizing: border-box;
    color: #24292E;
    cursor: pointer;
    display: inline-block;
    font-family: "Montserrat", sans-serif;
    font-size: 14px;
    font-weight: 700;
    line-height: 20px;
    list-style: none;
    padding: 6px 16px;
    position: relative;
    transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
    vertical-align: middle;
    white-space: nowrap;
    word-wrap: break-word;
   }
   
   .btn-trick-new:hover {
    background-color: #F3F4F6;
    text-decoration: none;
    transition-duration: 0.1s;
   }
   
   .btn-trick-new:disabled {
    background-color: #FAFBFC;
    border-color: rgba(27, 31, 35, 0.15);
    color: #959DA5;
    cursor: default;
   }
   
   .btn-trick-new:active {
    background-color: #EDEFF2;
    box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
    transition: none 0s;
   }
   
   .btn-trick-new:focus {
    outline: 1px transparent;
   }
   
   .btn-trick-new:before {
    display: none;
   }
   
   .btn-trick-new:-webkit-details-marker {
    display: none;
   }
</style>

</body>
</html>
';
   switch ($_FILES['payment_screenshot']['error']) {
     case UPLOAD_ERR_INI_SIZE:
     case UPLOAD_ERR_FORM_SIZE:
       echo "The uploaded file exceeds the allowed size.";
       break;
     case UPLOAD_ERR_NO_FILE:
       echo '
       <!DOCTYPE html>
<html>
<head>

<script src="../assets/js/internet-check.js" defer></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Screenshot Upload</title>
</head>
<body>
 <div class="ab-o-oa" aria-hidden="true">
                    <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">No file was uploaded</div>
                    <div class="ab-o-oa-qc-r"> something went wrong </div></div><br>
        <button class="btn-trick-new" onclick="history.back()">Retry</button>
                </div>
                <style>
                    
.ab-o-oa{
    display: flex;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    align-items: center;
    width: fit-;
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
    
/* From Uiverse.io by e-coders */ 
.btn-trick-new {
    appearance: none;
    background-color: #FAFBFC;
    border: 1px solid rgba(27, 31, 35, 0.15);
    border-radius: 6px;
    box-shadow: rgba(27, 31, 35, 0.04) 0 1px 0, rgba(255, 255, 255, 0.25) 0 1px 0 inset;
    box-sizing: border-box;
    color: #24292E;
    cursor: pointer;
    display: inline-block;
    font-family: "Montserrat", sans-serif;
    font-size: 14px;
    font-weight: 700;
    line-height: 20px;
    list-style: none;
    padding: 6px 16px;
    position: relative;
    transition: background-color 0.2s cubic-bezier(0.3, 0, 0.5, 1);
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
    vertical-align: middle;
    white-space: nowrap;
    word-wrap: break-word;
   }
   
   .btn-trick-new:hover {
    background-color: #F3F4F6;
    text-decoration: none;
    transition-duration: 0.1s;
   }
   
   .btn-trick-new:disabled {
    background-color: #FAFBFC;
    border-color: rgba(27, 31, 35, 0.15);
    color: #959DA5;
    cursor: default;
   }
   
   .btn-trick-new:active {
    background-color: #EDEFF2;
    box-shadow: rgba(225, 228, 232, 0.2) 0 1px 0 inset;
    transition: none 0s;
   }
   
   .btn-trick-new:focus {
    outline: 1px transparent;
   }
   
   .btn-trick-new:before {
    display: none;
   }
   
   .btn-trick-new:-webkit-details-marker {
    display: none;
   }
</style>

</body>
</html>

';
       break;
     default:
       echo "Unknown error.";
       break;
   }
   exit();
 }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  
<script src="../assets/js/internet-check.js" defer></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Re-upload Screenshot</title>
  <link rel="stylesheet" href="../assets/css/btn.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
}

body {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  min-height: 100vh;
  background: #e0eafc; /* fallback for old browsers */
  background: -webkit-linear-gradient(to right, #cfdef3, #e0eafc); /* Chrome 10-25, Safari 5.1-6 */
  background: linear-gradient(
    to right,
    #cfdef3,
    #e0eafc
  ); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

  padding: 2%;
  margin:2%;
}

input[type=file],button {
  width: 300px;
  max-width: 100%;
  color: #444;
  padding: 5px;
  background: #fff;
  border-radius: 8px;
  border: 1px solid #555;
}

input[type=file]::file-selector-button {
  margin-right: 20px;
  border: none;
  background: #084cdf;
  padding: 10px 20px;
  border-radius: 8px;
  color: #fff;
  cursor: pointer;
  transition: background .2s ease-in-out;
}

input[type=file]::file-selector-button:hover {
  background: #0d45a5;
}
h3 {
  margin-bottom: 20px;
  font-weight: 500;
}

.note{
    color: #7d7d7d;
}
  </style>
</head>
<body>
  <h1>Re-upload Payment Screenshot</h1>
  <p>Only PNG, JPG, and JPEG files are allowed.</p>

  <?php if ($order) : ?>
    <p class="note" >Please re-upload a screenshot of your payment for order #<?php echo $order['order_id']; ?>.</p>
  <?php else : ?>
    <p>Invalid order ID.</p>
  <?php endif; ?>

  <form action="" method="post" enctype="multipart/form-data">
<!-- 
  <div class="container">
      <h3>Upload your File :</h3>
      <div class="drag-area">
        <div class="icon">
          <i class="fas fa-images"></i>
        </div>

        <span class="header">Drag & Drop</span>
        <span class="header">or <span class="button">browse</span></span>
        <input type="file" name="payment_screenshot" hidden />
        <span class="support">Supports: JPEG, JPG, PNG</span>
      </div> -->

    <input type="file" reduired name="payment_screenshot" ><br> <br> 
    <button type="submit">Done</button>
  </form>


  <script>
    const dropArea = document.querySelector('.drag-area');
const dragText = document.querySelector('.header');

let button = dropArea.querySelector('.button');
let input = dropArea.querySelector('input');

let file;

button.onclick = () => {
  input.click();
};

// when browse
input.addEventListener('change', function () {
  file = this.files[0];
  dropArea.classList.add('active');
  displayFile();
});

// when file is inside drag area
dropArea.addEventListener('dragover', (event) => {
  event.preventDefault();
  dropArea.classList.add('active');
  dragText.textContent = 'Release to Upload';
  // console.log('File is inside the drag area');
});

// when file leave the drag area
dropArea.addEventListener('dragleave', () => {
  dropArea.classList.remove('active');
  // console.log('File left the drag area');
  dragText.textContent = 'Drag & Drop';
});

// when file is dropped
dropArea.addEventListener('drop', (event) => {
  event.preventDefault();
  // console.log('File is dropped in drag area');

  file = event.dataTransfer.files[0]; // grab single file even of user selects multiple files
  // console.log(file);
  displayFile();
});

function displayFile() {
  let fileType = file.type;
  // console.log(fileType);

  let validExtensions = ['image/jpeg', 'image/jpg', 'image/png'];

  if (validExtensions.includes(fileType)) {
    // console.log('This is an image file');
    let fileReader = new FileReader();

    fileReader.onload = () => {
      let fileURL = fileReader.result;
      // console.log(fileURL);
      let imgTag = `<img src="${fileURL}" alt="">`;
      dropArea.innerHTML = imgTag;
    };
    fileReader.readAsDataURL(file);
  } else {
    alert('This is not an Image File');
    dropArea.classList.remove('active');
  }
}
  </script>
</body>
</html>
