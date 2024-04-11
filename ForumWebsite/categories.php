<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['userID']);

require_once 'backend/db_connect.php';

// Fetch categories from the database
$sql = "SELECT CategoryID, Name, Description FROM categories ORDER BY Name";
$result = $mysqli->query($sql);

$categories = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch popular tags from the database
$tagQuery = "SELECT t.Name, COUNT(pt.TagID) AS TagCount FROM tags t
             JOIN post_tags pt ON t.TagID = pt.TagID
             GROUP BY t.TagID
             ORDER BY TagCount DESC
             LIMIT 10"; 

$tags = [];
if ($tagResult = $mysqli->query($tagQuery)) {
    while ($tagRow = $tagResult->fetch_assoc()) {
        $tags[] = $tagRow;
    }
}

// Fetch recent posts from the database
$recentPostsSql = "SELECT PostID, Title FROM posts ORDER BY Created_At DESC LIMIT 5";
$recentPostsResult = $mysqli->query($recentPostsSql);

$recentPosts = [];
if ($recentPostsResult && $recentPostsResult->num_rows > 0) {
    while ($post = $recentPostsResult->fetch_assoc()) {
        $recentPosts[] = $post;
    }
}

$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/categories.css">
</head>
<body>

<header>
    <div class="container">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Categories</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="create-post.php">New Post</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
            <form class="search" action="search.php" method="get">
                <input type="search" name="query" placeholder="Search by post name or tag...">
                <button type="submit">Search</button>
            </form>
        </nav>
    </div>
</header>


<main class="container">
    <h1>Forum Categories</h1>
    
    <section id="category-list">
    <h2>Browse by Category</h2>
    <?php foreach ($categories as $category): ?>
        <div class="category">
            <h3><a href="category.php?id=<?= $category['CategoryID'] ?>"><?= htmlspecialchars($category['Name']) ?></a></h3>
            <p><?= htmlspecialchars($category['Description']) ?></p>
        </div>
    <?php endforeach; ?>
    <?php if (empty($categories)): ?>
        <p>No categories found.</p>
    <?php endif; ?>
    </section>

    
    <aside id="popular-tags">
    <h2>Popular Tags</h2>
    <ul>
        <?php foreach ($tags as $tag): ?>
            <li><a href="search.php?tag=<?= urlencode($tag['Name']) ?>">#<?= htmlspecialchars($tag['Name']) ?></a></li>
        <?php endforeach; ?>
        <?php if (empty($tags)): ?>
            <p>No tags found.</p>
        <?php endif; ?>
    </ul>
    </aside>
    
    <section id="recent-posts">
        <h2>Recent Posts</h2>
        <ul>
            <?php foreach ($recentPosts as $post): ?>
                <li><a href="post.php?id=<?= $post['PostID'] ?>"><?= htmlspecialchars($post['Title']) ?></a></li>
            <?php endforeach; ?>
            <?php if (empty($recentPosts)): ?>
                <p>No recent posts available.</p>
            <?php endif; ?>
        </ul>
    </section>
</main>

<footer>
    <div class="container">
        <ul>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Privacy Policy</a></li>
        </ul>
    </div>
</footer>

</body>
</html>
