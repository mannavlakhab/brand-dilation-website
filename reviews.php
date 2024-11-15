<?php
// Include the database connection file
require_once 'db_connect.php';

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your reviews.");
}

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

$user_id = $_SESSION['user_id'];

// Fetch user reviews
$reviews = [];
$reviews_query = $conn->prepare(
    "SELECT id, product_id, rating, review_text, review_date, review_image_path 
     FROM product_reviews 
     WHERE user_id = ?"
);
if ($reviews_query) {
    $reviews_query->bind_param("i", $user_id);
    $reviews_query->execute();
    $reviews_result = $reviews_query->get_result();
    while ($row = $reviews_result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $reviews_query->close();
}

// Handle review deletion
if (isset($_POST['delete_review'])) {
    $review_id = intval($_POST['review_id']);
    $delete_query = $conn->prepare("DELETE FROM product_reviews WHERE id = ? AND user_id = ?");
    if ($delete_query) {
        $delete_query->bind_param("ii", $review_id, $user_id);
        $delete_query->execute();
        $delete_query->close();
        header("Location: review_history.php"); // Refresh the page to reflect changes
        exit();
    } else {
        echo "Error deleting review: " . $conn->error;
    }
}

// Handle review update
if (isset($_POST['update_review'])) {
    $review_id = intval($_POST['review_id']);
    $rating = intval($_POST['rating']);
    $review_text = $_POST['review_text'];
    $review_image_path = $_POST['review_image_path'];
    
    $update_query = $conn->prepare(
        "UPDATE product_reviews 
         SET rating = ?, review_text = ?, review_image_path = ? 
         WHERE id = ? AND user_id = ?"
    );
    if ($update_query) {
        $update_query->bind_param("issii", $rating, $review_text, $review_image_path, $review_id, $user_id);
        $update_query->execute();
        $update_query->close();
        header("Location: review_history.php"); // Refresh the page to reflect changes
        exit();
    } else {
        echo "Error updating review: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review History</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
</head>
<body>
    <header>
        <h1>Your Review History</h1>
        <a href="logout.php">Logout</a> <!-- Replace with actual logout script -->
    </header>
    <section>
        <?php foreach ($reviews as $review): ?>
            <div class="review">
                <h2>Review for Product ID: <?php echo htmlspecialchars($review['product_id']); ?></h2>
                <p>Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                <p><?php echo htmlspecialchars($review['review_text']); ?></p>
                <?php if (isset($review['review_image_path']) && $review['review_image_path']): ?>
                    <img src="<?php echo htmlspecialchars($review['review_image_path']); ?>" alt="Review Image" style="max-width: 200px; max-height: 200px;">
                <?php endif; ?>
                <small>Reviewed on <?php echo htmlspecialchars($review['review_date']); ?></small>
                <!-- Edit Review Form -->
                <form action="review_history.php" method="post">
                    <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['id']); ?>">
                    <label for="rating">Rating:</label>
                    <select name="rating" id="rating" required>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if ($i == $review['rating']) echo 'selected'; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <label for="review_text">Your Review:</label>
                    <textarea name="review_text" id="review_text" required><?php echo htmlspecialchars($review['review_text']); ?></textarea>
                    <label for="review_image_path">Image URL:</label>
                    <input type="text" name="review_image_path" id="review_image_path" value="<?php echo htmlspecialchars($review['review_image_path']); ?>">
                    <button type="submit" name="update_review">Update Review</button>
                </form>
                <!-- Delete Review Form -->
                <form action="review_history.php" method="post" onsubmit="return confirm('Are you sure you want to delete this review?');">
                    <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['id']); ?>">
                    <button type="submit" name="delete_review">Delete Review</button>
                </form>
            </div>
        <?php endforeach; ?>
    </section>
</body>
</html>
