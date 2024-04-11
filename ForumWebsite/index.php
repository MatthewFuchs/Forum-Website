<?php
session_start();

// Check if user is logged in using the session variable
$isLoggedIn = isset($_SESSION['userID']);

require_once 'backend/db_connect.php';

// Fetch featured posts from the database
$query = "SELECT PostID, Title, Content FROM posts ORDER BY Created_At DESC LIMIT 6"; 
$result = $mysqli->query($query);

// Fetch latest discussions
$discussions = $mysqli->query("SELECT title, link FROM latest_discussions");

// Fetch upcoming events
$events = $mysqli->query("SELECT title, link, event_date FROM upcoming_events ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Forum</title>
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
    <section id="welcome">
        <h2>Welcome to Our Community!</h2>
        <p>This platform is a place for sharing knowledge, asking questions, and connecting with tech enthusiasts and professionals alike.</p>
    </section>
    
    <section id="featured-posts">
        <h2>Featured Posts</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='post'>";
                echo "<h3><a href='post.php?id=" . $row['PostID'] . "'>" . htmlspecialchars($row['Title']) . "</a></h3>";
                echo "<p>" . substr(htmlspecialchars($row['Content']), 0, 150) . "...</p>"; // Display a snippet
                echo "</div>";
            }
        } else {
            echo "<p>No featured posts found.</p>";
        }
        ?>
    </section>

    <section id="latest-discussions">
    <h2>Latest Discussions</h2>
    <ul>
        <?php while($discussion = $discussions->fetch_assoc()): ?>
            <li><a href="<?= htmlspecialchars($discussion['link']); ?>"><?= htmlspecialchars($discussion['title']); ?></a></li>
        <?php endwhile; ?>
    </ul>
    </section>

    <section id="upcoming-events">
    <h2>Upcoming Events</h2>
    <ul>
        <?php while($event = $events->fetch_assoc()): ?>
            <li><a href="<?= htmlspecialchars($event['link']); ?>"><?= htmlspecialchars($event['title']); ?></a> - <?= date('F j, Y', strtotime($event['event_date'])); ?></li>
        <?php endwhile; ?>
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

<?php
$mysqli->close();
?>

</body>
</html>
