<?php
session_start();

// Check if user is logged in using the session variable
$isLoggedIn = isset($_SESSION['userID']);

require_once 'backend/db_connect.php';

$query = isset($_GET['query']) ? $_GET['query'] : '';

$posts = [];
if (!empty($query)) {
    // Prepare a SQL statement to fetch posts related to a specific tag or matching a post title
    $sql = "SELECT DISTINCT p.PostID, p.Title, p.Content, p.Created_At FROM posts p
    LEFT JOIN post_tags pt ON p.PostID = pt.PostID
    LEFT JOIN tags t ON pt.TagID = t.TagID
    WHERE p.Title LIKE CONCAT('%', ?, '%') OR t.Name LIKE CONCAT('%', ?, '%')
    ORDER BY p.Created_At DESC";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ss", $query, $query);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
        $stmt->close();
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
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
    <h1>Search Results for: <?= htmlspecialchars($query) ?></h1>
    <?php if (empty($posts)): ?>
        <p>No posts found matching your query.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h2><a href="post.php?id=<?= $post['PostID'] ?>"><?= htmlspecialchars($post['Title']) ?></a></h2>
                <p><?= nl2br(htmlspecialchars($post['Content'])) ?></p>
            </div>
        <?php endforeach; ?>
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
