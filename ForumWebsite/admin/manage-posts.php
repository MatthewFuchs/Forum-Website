<?php
session_start();

require_once '../backend/db_connect.php'; // Adjust the path as necessary

// Function to fetch all posts
function fetchPosts($mysqli) {
    $posts = [];
    $sql = "SELECT PostID, Title, Created_At FROM posts ORDER BY Created_At DESC";
    if ($result = $mysqli->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    }
    return $posts;
}

// Handling deletion of a post
if (isset($_POST['delete']) && isset($_POST['PostID'])) {
    $PostID = $_POST['PostID'];
    $sql = "DELETE FROM posts WHERE PostID = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $PostID);
        $stmt->execute();
        $stmt->close();
    }
    // Redirect to refresh the page and avoid resubmission
    header("Location: manage-posts.php");
    exit;
}

$posts = fetchPosts($mysqli);
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Posts</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<header>
    <h1>Manage Posts</h1>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-users.php">Manage Users</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
            <tr>
                <td><?= htmlspecialchars($post['Title']) ?></td>
                <td><?= htmlspecialchars($post['Created_At']) ?></td>
                <td>
                    <a href="edit-post.php?PostID=<?= $post['PostID'] ?>">Edit</a>
                    <form method="POST" action="manage-posts.php" style="display:inline;">
                        <input type="hidden" name="PostID" value="<?= $post['PostID'] ?>">
                        <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you want to delete this post?');">
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<footer>
    <p>Admin Panel Footer</p>
</footer>
</body>
</html>
