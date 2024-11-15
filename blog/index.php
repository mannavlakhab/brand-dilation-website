<?php
include('../db_connect.php');

// Get the search query
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Sanitize the query to prevent SQL injection
$search_query = "%" . $conn->real_escape_string($query) . "%";

// Prepare the SQL statement to search in multiple fields
if ($query) {
    $sql = "SELECT id, title, category, topic, keywords, short_description, content, author, created_at, main_photo, likes 
            FROM blog_posts 
            WHERE title LIKE ? 
               OR content LIKE ? 
               OR keywords LIKE ? 
               OR short_description LIKE ? 
               OR author LIKE ? 
            ORDER BY likes DESC, created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $search_query, $search_query, $search_query, $search_query, $search_query);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Fetch all blog posts if no search query is provided
    $sql = "SELECT id, title, category, topic, keywords, short_description, content, author, main_photo, created_at, likes 
            FROM blog_posts 
            ORDER BY likes DESC, created_at DESC";
    $result = $conn->query($sql);
}


// Fetch top 5 posts by category
$category_sql = "SELECT category, COUNT(*) as count 
                 FROM blog_posts 
                 GROUP BY category 
                 ORDER BY count DESC 
                 LIMIT 5";
$category_result = $conn->query($category_sql);



// Fetch top 5 posts by topic
$topic_sql = "SELECT topic, COUNT(*) as count 
              FROM blog_posts 
              GROUP BY topic 
              ORDER BY count DESC 
              LIMIT 5";
$topic_result = $conn->query($topic_sql);

// Fetch top 5 tags
// Fetch top 5 tags
$tags_sql = "SELECT t.name AS tag_name, COUNT(pt.post_id) as count
             FROM tags t
             JOIN post_tags pt ON t.id = pt.tag_id
             GROUP BY t.name
             ORDER BY count DESC
             LIMIT 5";

$tags_result = $conn->query($tags_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../assets/css/btn.css">
    <link rel="stylesheet" href="search.css">
    <link rel="shortcut icon" href="../assets/img/bd.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&display=swap" rel="stylesheet">
    <script src="../assets/js/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

    <script>
    $(function() {
        $('#header').load('../pages/header.php');

    });
    </script>
    <!--=============== footer ===============-->
    <script>
    $(function() {
        $('#footer').load('../pages/footer.php');

    });
    </script>
    <style>

    </style>
</head>
<body>
    <header>
        <!-- Breadcrumb Navigation -->
        <span class="t-c">Blog by,</span>
        <br><br>
        <a href="../blog"> <img src="../assets/img/64-bd-t.png" alt=""></a>
        
        <h1>Solution & Resources</h1>
        <p>The latest industry news, interviews, technologies, and resources.</p>
        <div class="search-bar">
           <form method="GET" class="search-bar">
<div id="poda">
  <div class="glow"></div>
  <div class="darkBorderBg"></div>
  <div class="darkBorderBg"></div>
  <div class="darkBorderBg"></div>

  <div class="white"></div>

  <div class="border"></div>

  <div id="main">
    <input placeholder="Search..." value="<?php echo htmlspecialchars($query); ?>" type="text" name="query" class="input" />
    <div id="input-mask"></div>
    <div id="pink-mask"></div>
    <div class="filterBorder"></div>
    <button type="submit" id="filter-icon">
      <svg
        preserveAspectRatio="none"
        height="27"
        width="27"
        viewBox="4.8 4.56 14.832 15.408"
        fill="none"
      >
        <path
          d="M8.16 6.65002H15.83C16.47 6.65002 16.99 7.17002 16.99 7.81002V9.09002C16.99 9.56002 16.7 10.14 16.41 10.43L13.91 12.64C13.56 12.93 13.33 13.51 13.33 13.98V16.48C13.33 16.83 13.1 17.29 12.81 17.47L12 17.98C11.24 18.45 10.2 17.92 10.2 16.99V13.91C10.2 13.5 9.97 12.98 9.73 12.69L7.52 10.36C7.23 10.08 7 9.55002 7 9.20002V7.87002C7 7.17002 7.52 6.65002 8.16 6.65002Z"
          stroke="#d6d6e6"
          stroke-width="1"
          stroke-miterlimit="10"
          stroke-linecap="round"
          stroke-linejoin="round"
        ></path>
      </svg>
</button>
    <div id="search-icon">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24"
        viewBox="0 0 24 24"
        stroke-width="2"
        stroke-linejoin="round"
        stroke-linecap="round"
        height="24"
        fill="none"
        class="feather feather-search"
      >
        <circle stroke="url(#search)" r="8" cy="11" cx="11"></circle>
        <line
          stroke="url(#searchl)"
          y2="16.65"
          y1="22"
          x2="16.65"
          x1="22"
        ></line>
        <defs>
          <linearGradient gradientTransform="rotate(50)" id="search">
            <stop stop-color="#f8e7f8" offset="0%"></stop>
            <stop stop-color="#b6a9b7" offset="50%"></stop>
          </linearGradient>
          <linearGradient id="searchl">
            <stop stop-color="#b6a9b7" offset="0%"></stop>
            <stop stop-color="#837484" offset="50%"></stop>
          </linearGradient>
        </defs>
      </svg>
    </div>
  </div>
</div>


                <!-- <input type="text" name="query" placeholder="Search" value="<?php echo htmlspecialchars($query); ?>">
                <button type="submit" class="search-icon">&#x1F50D;</button> -->
           </form>
        </div>
    </header>

    <!-- Display search results if a query is present, otherwise show all posts -->
    <div class="section-post">
        <?php if ($query): ?>
            <?php if ($result->num_rows > 0): ?>
              <div class="grid-container">
                <?php while($row = $result->fetch_assoc()): ?>
                  <div class="card-blog">
                      <a href="page.php?id=<?php echo $row['id']; ?>">
    <img src="uploads/<?php echo htmlspecialchars($row['main_photo']); ?>" alt="Post" class="card-img">
    <p style="cursor:pointer;" onclick="window.location.href='/category/?category=<?php echo htmlspecialchars($row['category']); ?>'" class="ct">
    <?php echo htmlspecialchars($row['category']); ?>
</p>


    <h2 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h2>
    <p class="short-description"><?php echo htmlspecialchars($row['short_description']); ?></p>
    <div class="card-author">
        <p>
            <span class="author-name"><?php echo htmlspecialchars($row['author']); ?></span><br>
            <span class="author-date"> on <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></span>
        </p>
   
      </a>
    <p style="float: right; padding:1%; " class="likes btn-trick-new"><a href="like_post.php?id=<?php echo $row['id']; ?>" class="like-button">
      <svg fill="#3c0e40" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50px" height="50px" viewBox="-122.88 -122.88 757.76 757.76" enable-background="new 0 0 512 512" xml:space="preserve" stroke="#3c0e40" stroke-width="0.00512" transform="rotate(0)">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier"> <g><g>
    <path d="M344,288c4.781,0,9.328,0.781,13.766,1.922C373.062,269.562,384,245.719,384,218.625C384,177.422,351.25,144,310.75,144 c-21.875,0-41.375,10.078-54.75,25.766C242.5,154.078,223,144,201.125,144C160.75,144,128,177.422,128,218.625 C128,312,256,368,256,368s14-6.203,32.641-17.688C288.406,348.203,288,346.156,288,344C288,313.125,313.125,288,344,288z"></path>
    <path d="M256,0C114.609,0,0,114.609,0,256s114.609,256,256,256s256-114.609,256-256S397.391,0,256,0z M256,472 c-119.297,0-216-96.703-216-216S136.703,40,256,40s216,96.703,216,216S375.297,472,256,472z"></path></g>
    <path d="M344,304c-22.094,0-40,17.906-40,40s17.906,40,40,40s40-17.906,40-40S366.094,304,344,304z M368,352h-16v16h-16v-16h-16 v-16h16v-16h16v16h16V352z"></path> </g> </g></svg>
  <!-- [<?php echo htmlspecialchars($row['likes']); ?>] -->
</a> </p>
</div>
</div>

                <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>  <div class="ab-o-oa" aria-hidden="true">
                    <div class="ZAnhre">
                    <img class="wF0Mmb" src="../assets/blog_empty.svg" width="300px" height="300px" alt=""></div>
                    <div class="ab-o-oa-r"><div class="ab-o-oa-qc-V">No blog Found</div>
                    <div class="ab-o-oa-qc-r"> matching your search criteria.</div></div>
                </div>
                <style>
                    
.ab-o-oa{
    display: flex;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    align-items: center;
    width: 100%;
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




                </style></p>
            <?php endif; ?>
        <?php else: ?>



            <?php if ($result->num_rows > 0): ?>
              <div class="grid-container">
                <?php while($row = $result->fetch_assoc()): ?>
                  <div class="card-blog">
                      <a href="page.php?id=<?php echo $row['id']; ?>">
    <img src="uploads/<?php echo htmlspecialchars($row['main_photo']); ?>" alt="Post" class="card-img">
    <p class="ct"><?php echo htmlspecialchars($row['category']); ?></p>
    <h2 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h2>
    <p class="short-description"><?php echo htmlspecialchars($row['short_description']); ?></p>
    <div class="card-author">
        <p>
            <span class="author-name"><?php echo htmlspecialchars($row['author']); ?></span><br>
            <span class="author-date"> on <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></span>
        </p>
    
      </a>
        <p style="float: right; padding:1%; " class="likes btn-trick-new"><a href="like_post.php?id=<?php echo $row['id']; ?>" class="like-button">
      <svg fill="#3c0e40" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50px" height="50px" viewBox="-122.88 -122.88 757.76 757.76" enable-background="new 0 0 512 512" xml:space="preserve" stroke="#3c0e40" stroke-width="0.00512" transform="rotate(0)">
        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
        <g id="SVGRepo_iconCarrier"> <g><g>
    <path d="M344,288c4.781,0,9.328,0.781,13.766,1.922C373.062,269.562,384,245.719,384,218.625C384,177.422,351.25,144,310.75,144 c-21.875,0-41.375,10.078-54.75,25.766C242.5,154.078,223,144,201.125,144C160.75,144,128,177.422,128,218.625 C128,312,256,368,256,368s14-6.203,32.641-17.688C288.406,348.203,288,346.156,288,344C288,313.125,313.125,288,344,288z"></path>
    <path d="M256,0C114.609,0,0,114.609,0,256s114.609,256,256,256s256-114.609,256-256S397.391,0,256,0z M256,472 c-119.297,0-216-96.703-216-216S136.703,40,256,40s216,96.703,216,216S375.297,472,256,472z"></path></g>
    <path d="M344,304c-22.094,0-40,17.906-40,40s17.906,40,40,40s40-17.906,40-40S366.094,304,344,304z M368,352h-16v16h-16v-16h-16 v-16h16v-16h16v16h16V352z"></path> </g> </g></svg>
  <!-- [<?php echo htmlspecialchars($row['likes']); ?>] -->
</a> </p>
</div>
</div>
                <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No blog posts available.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <style>
/* Grid container for posts */
.grid-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}


/* Responsive Styles */
@media (max-width: 768px) {
    .grid-container {
        grid-template-columns: repeat(1, 1fr);
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .grid-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (min-width: 1025px) and (max-width: 1440px) {
    .grid-container {
        grid-template-columns: repeat(3, 1fr);
    }
}
</style>
    <!-- Display top 5 posts by category, topic, and keywords -->
<style>
  /* Base styles */
.top_blog {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: 1fr;
  gap: 0px 4px;
  grid-auto-flow: column dense;
  grid-template-areas: "top_Category_blog top_Topic_blog top_Keywords_blog";
  margin: 2%;
  background: azure;
  padding: 2%;
  border: 1px solid;
  border-radius: 10px;
}

.top_Category_blog, .top_Topic_blog, .top_Keywords_blog {
  background: #fff;
  padding: 4%;
  border: 1px solid;
  margin: 2%;
  border-radius: 10px;
}

.post-list li a {
  color: #fff;
}

.list-top {
  background: blueviolet;
  color: #ffffff;
  padding: 2%;
  border-radius: 10px;
  text-align: center;
}

/* Responsive styles */
@media (max-width: 1200px) {
  .top_blog {
    grid-template-columns: repeat(2, 1fr);
    grid-template-areas:
      "top_Category_blog top_Topic_blog"
      "top_Keywords_blog top_Keywords_blog";
  }
}

@media (max-width: 768px) {
  .top_blog {
    grid-template-columns: 1fr;
    grid-template-areas:
      "top_Category_blog"
      "top_Topic_blog"
      "top_Keywords_blog";
  }
}

@media all and (-ms-high-contrast:none) {
  .top_blog {
    display: -ms-grid;
    -ms-grid-columns: repeat(3, 1fr);
    -ms-grid-rows: 1fr;
  }

  .top_Category_blog {
    -ms-grid-row: 1;
    -ms-grid-column: 1;
  }

  .top_Topic_blog {
    -ms-grid-row: 1;
    -ms-grid-column: 2;
  }

  .top_Keywords_blog {
    -ms-grid-row: 1;
    -ms-grid-column: 3;
  }
}

</style>
    <div class="top_blog">
  <div class="top_Category_blog">
  <section>
         <h2>Top Posts by Category</h2>
         <ul class="post-list">
    <?php while($row = $category_result->fetch_assoc()): ?>
        <li class="list-top" ><a href="category/?category=<?php echo urlencode($row['category']); ?>"><?php echo htmlspecialchars($row['category']); ?></a></li>
    <?php endwhile; ?>
</ul>

    </section>
  </div>
  <div class="top_Topic_blog">
  <section>
        <h2>Top Posts by Topic</h2>
        <ul class="post-list">
    <?php while($row = $topic_result->fetch_assoc()): ?>
        <li class="list-top" ><a href="topic/?topic=<?php echo urlencode($row['topic']); ?>"><?php echo htmlspecialchars($row['topic']); ?></a></li>
    <?php endwhile; ?>
</ul>

    </section>

  </div>
  <div class="top_Keywords_blog">
    
  <section>
        <h2>Top Posts by Keywords</h2>
        <ul class="post-list">
        <?php while($row = $tags_result->fetch_assoc()): ?>
                <li class="list-top" ><a href="keyword/?tag=<?php echo urlencode($row['tag_name']); ?>"><?php echo htmlspecialchars($row['tag_name']); ?></a></li>
            <?php endwhile; ?>
</ul>

    </section>
  </div>
</div>

<div id="footer"></div>

</body>
</html>