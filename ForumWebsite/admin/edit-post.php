<?php
session_start();

require_once '../backend/db_connect.php'; // Adjust the path as necessary

$postID = $_GET['PostID'] ?? ''; // Get the PostID from the URL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update post in the database
    $title = $_POST['title'];
    $content = $_POST['content'];
    $sql = "UPDATE posts SET Title = ?, Content = ? WHERE PostID = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ssi", $title, $content, $postID);
        $stmt->execute();
        $stmt->close();
        header("Location: manage-posts.php"); // Redirect after updating
        exit;
    }
} else {
    // Fetch the existing post from the database
    $sql = "SELECT Title, Content FROM posts WHERE PostID = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $postID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($post = $result->fetch_assoc()) {
            $title = $post['Title'];
            $content = $post['Content'];
        } else {
            echo "No post found with that ID.";
        }
        $stmt->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<header>
    <h1>Edit Post</h1>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-posts.php">Manage Posts</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <form action="edit-post.php?PostID=<?= htmlspecialchars($postID) ?>" method="post">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
        </div>
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" rows="10" required><?= htmlspecialchars($content) ?></textarea>
        </div>
        <button type="submit">Update Post</button>
    </form>
</main>

<footer>
    <p>Admin Panel Footer</p>
</footer>
</body>
</html>
