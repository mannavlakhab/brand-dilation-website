<?php
include('../db_connect.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT title, category, topic, short_description, content, author, created_at, main_photo FROM blog_posts WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    if ($post) {
        // Fetch associated tags
        $tagsSql = "SELECT t.name FROM tags t 
                    JOIN post_tags pt ON t.id = pt.tag_id 
                    WHERE pt.post_id = ?";
        $tagsStmt = $conn->prepare($tagsSql);
        $tagsStmt->bind_param("i", $id);
        $tagsStmt->execute();
        $tagsResult = $tagsStmt->get_result();
        $tags = [];
        while ($tag = $tagsResult->fetch_assoc()) {
            $tags[] = $tag['name'];
        }

        // Update the likes count (this line seems unnecessary since it's just adding 0 to likes)
        $sql = "UPDATE blog_posts SET likes = likes + 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } else {
        echo "<p>Post not found.</p>";
        exit;
    }
} else {
    echo "<p>Invalid post ID.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../assets/css/btn.css">
    <style>
       
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="breadcrumb">
            <a href="index.php">Home</a> <span>&gt;</span>
            <span><?php echo htmlspecialchars($post['title']); ?></span>
        </nav>
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <p><?php echo htmlspecialchars($post['short_description']); ?></p>
    </header>

    <main class="container">
        <article class="post">
            <button class="btn-trick-new" onclick="history.back()">Go Back</button>
            <a style="float: right;" href="like_post.php?id=<?php echo $id; ?>" style="float: right; padding:1%; " >
                <button class="likes btn-trick-new"><svg fill="#3c0e40" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50px" height="50px" viewBox="-122.88 -122.88 757.76 757.76" enable-background="new 0 0 512 512" xml:space="preserve" stroke="#3c0e40" stroke-width="0.00512" transform="rotate(0)">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier"> <g><g>
    <path d="M344,288c4.781,0,9.328,0.781,13.766,1.922C373.062,269.562,384,245.719,384,218.625C384,177.422,351.25,144,310.75,144 c-21.875,0-41.375,10.078-54.75,25.766C242.5,154.078,223,144,201.125,144C160.75,144,128,177.422,128,218.625 C128,312,256,368,256,368s14-6.203,32.641-17.688C288.406,348.203,288,346.156,288,344C288,313.125,313.125,288,344,288z"></path>
    <path d="M256,0C114.609,0,0,114.609,0,256s114.609,256,256,256s256-114.609,256-256S397.391,0,256,0z M256,472 c-119.297,0-216-96.703-216-216S136.703,40,256,40s216,96.703,216,216S375.297,472,256,472z"></path></g>
    <path d="M344,304c-22.094,0-40,17.906-40,40s17.906,40,40,40s40-17.906,40-40S366.094,304,344,304z M368,352h-16v16h-16v-16h-16 v-16h16v-16h16v16h16V352z"></path> </g> </g></svg></button>
            </a>
            <a href="javascript:;" style="background:#000;
            border-radius:20px; padding:1%; "  onclick="window.print();return false;" class="printer" title="Print" bis_skin_checked="1"><img src="https://www.croma.com/unboxed/wp-content/themes/unboxed/assets/images/unboxed_print_post.png" alt="Print" title="Print"></a>
            <br><br>
            <p><strong>Category:</strong> <a href="category/?category=<?php echo htmlspecialchars($post['category']); ?>"><?php echo htmlspecialchars($post['category']); ?></p></a>
            <p><strong>Topic:</strong> <a href="topic/?topic=<?php echo htmlspecialchars($post['topic']); ?>"><?php echo htmlspecialchars($post['topic']); ?></p>
            
            <!-- Displaying Tags -->
<p><strong>Tags:</strong></p>
<?php 
if (!empty($tags)) {
    foreach ($tags as $tag) {
        echo '<a href="keyword/?tag=' . htmlspecialchars($tag) . '"><p class="tags-page">' . htmlspecialchars($tag) . '</p></a>';
    }
} else {
    echo '<p>No tags available.</p>';
}
?>

            </p>
        
            <br><br>
            <p>by, <em><?php echo htmlspecialchars($post['author']); ?><br>on <?php echo date("F j, Y", strtotime($post['created_at'])); ?></em></p>
            <hr>
            <br>
            <p><?php echo htmlspecialchars($post['short_description']); ?></p>
            <!-- <img class="img-titw" src="uploads/<?php echo htmlspecialchars($post['main_photo']); ?>" alt="Post"> -->
            <div class="content">
                <?php echo $post['content']; // This should render HTML content including images ?>
            </div>
            <br>
            <b>
                <p style="float: right;">
                    <em><?php echo htmlspecialchars($post['author']); ?> on <?php echo date("F j, Y", strtotime($post['created_at'])); ?></em>
                </p>
            </b>
            <br>
            <hr>
            <a style="float: right;" href="like_post.php?id=<?php echo $id; ?>" style="float: right; padding:1%; " >
                <button class="likes btn-trick-new"><svg fill="#3c0e40" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50px" height="50px" viewBox="-122.88 -122.88 757.76 757.76" enable-background="new 0 0 512 512" xml:space="preserve" stroke="#3c0e40" stroke-width="0.00512" transform="rotate(0)">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier"> <g><g>
    <path d="M344,288c4.781,0,9.328,0.781,13.766,1.922C373.062,269.562,384,245.719,384,218.625C384,177.422,351.25,144,310.75,144 c-21.875,0-41.375,10.078-54.75,25.766C242.5,154.078,223,144,201.125,144C160.75,144,128,177.422,128,218.625 C128,312,256,368,256,368s14-6.203,32.641-17.688C288.406,348.203,288,346.156,288,344C288,313.125,313.125,288,344,288z"></path>
    <path d="M256,0C114.609,0,0,114.609,0,256s114.609,256,256,256s256-114.609,256-256S397.391,0,256,0z M256,472 c-119.297,0-216-96.703-216-216S136.703,40,256,40s216,96.703,216,216S375.297,472,256,472z"></path></g>
    <path d="M344,304c-22.094,0-40,17.906-40,40s17.906,40,40,40s40-17.906,40-40S366.094,304,344,304z M368,352h-16v16h-16v-16h-16 v-16h16v-16h16v16h16V352z"></path> </g> </g></svg></button>
            </a>
        </article>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Brand Dilation. All rights reserved.</p>
    </footer>
</body>
</html>
