<?php
session_start();

require_once '../backend/db_connect.php';

// Function to fetch user statistics
function getUserStatistics() {
    global $mysqli; // Ensure $mysqli is accessible inside the function
    $sql = "SELECT COUNT(*) AS total_users FROM users";
    $result = $mysqli->query($sql);
    return $result->fetch_assoc()['total_users'];
}

// Function to fetch recent post statistics
function getRecentPostStatistics() {
    global $mysqli;
    // Use the correct column name 'created_At'
    $sql = "SELECT COUNT(*) AS recent_posts FROM posts WHERE created_At >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
    $result = $mysqli->query($sql);
    if ($result) {
        return $result->fetch_assoc()['recent_posts'];
    } else {
        // It's good practice to handle potential SQL errors
        error_log('SQL error: ' . $mysqli->error);
        return 0; // Return 0 if there's an error
    }
}

// Total users and recent posts for widgets
$totalUsers = getUserStatistics();
$recentPosts = getRecentPostStatistics();

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
    <nav>
        <ul>
            <li><a href="manage-users.php">Manage Users</a></li>
            <li><a href="manage-posts.php">Manage Posts</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="widget">
        <h2>User Statistics</h2>
        <p>Total Users: <?= $totalUsers; ?></p>
    </section>
    <section class="widget">
        <h2>Recent Post Activity</h2>
        <p>Posts This Week: <?= $recentPosts; ?></p>
    </section>
</main>

<footer>
    <p>Admin Panel Footer</p>
</footer>
</body>
</html>
