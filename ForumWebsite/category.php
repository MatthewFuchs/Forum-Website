<?php
require_once 'backend/db_connect.php';
session_start();

// Check if user is logged in using the session variable
$isLoggedIn = isset($_SESSION['userID']);

$categoryID = isset($_GET['id']) ? intval($_GET['id']) : 0;
$categoryName = ""; // To store the category name

// Fetch category name (optional but nice for displaying as a title)
if ($catStmt = $mysqli->prepare("SELECT Name FROM categories WHERE CategoryID = ?")) {
    $catStmt->bind_param("i", $categoryID);
    $catStmt->execute();
    $catResult = $catStmt->get_result();
    if ($catRow = $catResult->fetch_assoc()) {
        $categoryName = $catRow['Name'];
    }
    $catStmt->close();
}

// Fetch posts for the category
$posts = [];
if ($postStmt = $mysqli->prepare("SELECT PostID, Title, Content FROM posts WHERE CategoryID = ? ORDER BY Created_At DESC")) {
    $postStmt->bind_param("i", $categoryID);
    $postStmt->execute();
    $postResult = $postStmt->get_result();
    while ($postRow = $postResult->fetch_assoc()) {
        $posts[] = $postRow;
    }
    $postStmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($categoryName) ?> Posts</title>
    <link rel="stylesheet" href="css/index.css">
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
    <h1><?= htmlspecialchars($categoryName) ?></h1>
    <?php foreach ($posts as $post): ?>
        <article>
            <h2><a href="post.php?id=<?= $post['PostID'] ?>"><?= htmlspecialchars($post['Title']) ?></a></h2>
            <p><?= substr(htmlspecialchars($post['Content']), 0, 150) ?>...</p>
        </article>
    <?php endforeach; ?>
    <?php if (empty($posts)): ?>
        <p>No posts found in this category.</p>
    <?php endif; ?>
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
