<?php
session_start(); // Start the session at the beginning of the script

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

require_once 'backend/db_connect.php'; // Ensure this path is correct

$message = ""; // To hold success or error message

// Fetch categories from the database for the dropdown
$categoryResult = $mysqli->query("SELECT CategoryID, Name FROM categories");
$categories = [];
if ($categoryResult && $categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize form data
    $postTitle = $mysqli->real_escape_string($_POST['postTitle']);
    $postCategory = (int)$_POST['postCategory']; // Ensure this is treated as an integer
    $postContent = $mysqli->real_escape_string($_POST['postContent']);

    // Use the logged-in user's ID for the UserID field
    $userID = $_SESSION['userID'];

    // Prepare an insert statement
    $sql = "INSERT INTO posts (UserID, Title, Content, CategoryID) VALUES (?, ?, ?, ?)";

    if ($stmt = $mysqli->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("issi", $userID, $postTitle, $postContent, $postCategory);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            $message = "Post successfully created.";
            // Optionally, redirect to the post page or index.php after posting
            // header("Location: index.php");
            // exit; // Make sure not to execute further code
        } else {
            $message = "Error: Could not execute the query: " . $mysqli->error;
        }

        // Close statement
        $stmt->close();
    } else {
        $message = "Error: Could not prepare the query: " . $mysqli->error;
    }

    // Close connection
    $mysqli->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/create-post.css">
</head>
<body>

<header>
    <div class="container">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="create-post.php">New Post</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <form class="search" action="search.php" method="get">
                <input type="search" name="query" placeholder="Search by post name or tag...">
                <button type="submit">Search</button>
            </form>
        </nav>
    </div>
</header>

<main class="container">
    <h1>Create New Post</h1>
    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form id="create-post-form" action="create-post.php" method="post">
        <div class="form-group">
            <label for="postTitle">Post Title</label>
            <input type="text" id="postTitle" name="postTitle" required>
        </div>
        <div class="form-group">
            <label for="postCategory">Category</label>
            <select id="postCategory" name="postCategory">
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['CategoryID']); ?>">
                        <?php echo htmlspecialchars($category['Name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="postContent">Content</label>
            <textarea id="postContent" name="postContent" rows="10" required></textarea>
        </div>
        <button type="submit" class="submit-post-btn">Submit Post</button>
    </form>
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
