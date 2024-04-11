<?php
session_start();

// Check if user is logged in using the session variable
$isLoggedIn = isset($_SESSION['userID']);

require_once 'backend/db_connect.php';

// Ensure the ID is present and valid
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid Post ID";
    exit;
}

$postId = intval($_GET['id']);
$message = "";

// Fetch the post details
if ($postId > 0) {
    if ($stmt = $mysqli->prepare("SELECT Title, Content, Created_At FROM posts WHERE PostID = ?")) {
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $message = "Post not found.";
        } else {
            $post = $result->fetch_assoc();
        }
        $stmt->close();
    } else {
        $message = "Error fetching post: " . $mysqli->error;
    }
}

// Handle comment submission
if (isset($_POST['submitComment'], $_POST['commentContent']) && $isLoggedIn) {
    $commentContent = trim($_POST['commentContent']);
    $userID = $_SESSION['userID'];

    // Insert the comment into the database
    if ($stmt = $mysqli->prepare("INSERT INTO comments (PostID, UserID, Content) VALUES (?, ?, ?)")) {
        $stmt->bind_param("iis", $postId, $userID, $commentContent);
        $stmt->execute();
        $stmt->close();
        
        // Redirect to prevent form resubmission
        header("Location: post.php?id=" . $postId);
        exit;
    } else {
        $message = "Error: " . $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['Title'] ?? 'Post') ?></title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/post.css"> 
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

<main class="main-container">
    <h1><?= htmlspecialchars($post['Title']) ?></h1>
    <p><?= nl2br(htmlspecialchars($post['Content'])) ?></p>
    <p>Posted on: <?= date("F j, Y, g:i a", strtotime($post['Created_At'])) ?></p>

    <!-- Comments form -->
    <?php if ($isLoggedIn): ?>
        <form action="post.php?id=<?= $postId ?>" method="post">
            <textarea name="commentContent" required></textarea>
            <button type="submit" name="submitComment">Post Comment</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Log in</a> to post comments.</p>
    <?php endif; ?>

    <!-- Display comments -->
    <section>
        <h2>Comments</h2>
        <?php
        if ($stmt = $mysqli->prepare("SELECT c.Content, c.CreatedAt, u.UserName FROM comments c JOIN users u ON c.UserID = u.UserID WHERE PostID = ? ORDER BY c.CreatedAt DESC")) {
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            $comments = $stmt->get_result();

            if ($comments->num_rows > 0) {
                while ($comment = $comments->fetch_assoc()) {
                    echo "<div><strong>" . htmlspecialchars($comment['UserName']) . "</strong> (" . htmlspecialchars($comment['CreatedAt']) . ") says:<br>" . nl2br(htmlspecialchars($comment['Content'])) . "</div>";
                }
            } else {
                echo "<p>No comments yet.</p>";
            }
            $stmt->close();
        }
        ?>
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
