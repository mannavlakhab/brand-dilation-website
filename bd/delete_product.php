<?php
session_start();
require_once '../db_connect.php';
include 'config.php';

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the ID is set in the URL parameter
if (isset($_GET['id'])) {
  $product_id = strip_tags(mysqli_real_escape_string($conn, $_GET['id']));

  // Check if the product ID is valid
  if (!is_numeric($product_id) || $product_id <= 0) {
    die('Invalid Product ID');
  }

  // Delete associated order items from Order_Items table
  $sql_order_items = "DELETE FROM Order_Items WHERE product_id = ?";
  $stmt_order_items = mysqli_prepare($conn, $sql_order_items);

  if ($stmt_order_items) {
    mysqli_stmt_bind_param($stmt_order_items, "i", $product_id);

    if (mysqli_stmt_execute($stmt_order_items)) {
      // Delete associated product variations from ProductVariations table
      $sql_variations = "DELETE FROM productvariations WHERE product_id = ?";
      $stmt_variations = mysqli_prepare($conn, $sql_variations);

      if ($stmt_variations) {
        mysqli_stmt_bind_param($stmt_variations, "i", $product_id);

        if (mysqli_stmt_execute($stmt_variations)) {
          // Delete associated product images from Product_Images table
          $sql_images = "DELETE FROM Product_Images WHERE product_id = ?";
          $stmt_images = mysqli_prepare($conn, $sql_images);

          if ($stmt_images) {
            mysqli_stmt_bind_param($stmt_images, "i", $product_id);

            if (mysqli_stmt_execute($stmt_images)) {
              // Delete product from Products table
              $sql = "DELETE FROM Products WHERE product_id = ?";
              $stmt = mysqli_prepare($conn, $sql);

              if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $product_id);

                if (mysqli_stmt_execute($stmt)) {
                  // Close statements
                  mysqli_stmt_close($stmt);
                  mysqli_stmt_close($stmt_images);
                  mysqli_stmt_close($stmt_order_items);
                  mysqli_stmt_close($stmt_variations);

                  // Redirect to a success page or product list page with a message
                  header("Location: view_product.php?message=Product+deleted+successfully");
                  exit();
                } else {
                  $error = "Error executing product delete statement: " . mysqli_error($conn);
                }
              } else {
                $error = "Error preparing product delete statement: " . mysqli_error($conn);
              }
            } else {
              $error = "Error executing images delete statement: " . mysqli_error($conn);
            }
          } else {
            $error = "Error preparing images delete statement: " . mysqli_error($conn);
          }
        } else {
          $error = "Error executing variations delete statement: " . mysqli_error($conn);
        }
      } else {
        $error = "Error preparing variations delete statement: " . mysqli_error($conn);
      }
    } else {
      $error = "Error executing order items delete statement: " . mysqli_error($conn);
    }
  } else {
    $error = "Error preparing order items delete statement: " . mysqli_error($conn);
  }
} else {
  // Redirect back or display an error message if ID is missing
  header("Location: view_product.php?message=Product+ID+missing");
  exit();
}

// Close connection (optional, since exit() is used in previous sections)
mysqli_close($conn);

// Display error message if any
if (isset($error)) {
  echo $error;
}
?>
