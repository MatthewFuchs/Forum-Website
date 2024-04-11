<?php
session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit;
}

require_once 'backend/db_connect.php';

$userID = $_SESSION['userID'];
$message = "";

if ($stmt = $mysqli->prepare("SELECT UserName, Email, FirstName, LastName, Bio FROM users WHERE UserID = ?")) {
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    } else {
        $message = "User not found.";
    }
    $stmt->close();
} else {
    $message = "Error fetching user data: " . $mysqli->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = $mysqli->real_escape_string($_POST['userName'] ?? '');
    $userEmail = $mysqli->real_escape_string($_POST['userEmail'] ?? '');
    $firstName = isset($_POST['firstName']) ? $mysqli->real_escape_string($_POST['firstName']) : '';
    $lastName = isset($_POST['lastName']) ? $mysqli->real_escape_string($_POST['lastName']) : '';
    $userBio = isset($_POST['userBio']) ? $mysqli->real_escape_string($_POST['userBio']) : '';

    $updateSql = "UPDATE users SET UserName = ?, Email = ?, FirstName = ?, LastName = ?, Bio = ? WHERE UserID = ?";
    if ($updateStmt = $mysqli->prepare($updateSql)) {
        $updateStmt->bind_param("sssssi", $userName, $userEmail, $firstName, $lastName, $userBio, $userID);
        if ($updateStmt->execute()) {
            $message = "Profile updated successfully.";
        } else {
            $message = "Error updating profile: " . $mysqli->error;
        }
        $updateStmt->close();
    } else {
        $message = "Error preparing update statement: " . $mysqli->error;
    }
}

$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/profile.css">
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
    <h1>User Profile</h1>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form id="profile-form" action="profile.php" method="post" enctype="multipart/form-data">      
        <div class="form-group">
            <label for="userName">User Name</label>
            <input type="text" id="userName" name="userName" value="<?= htmlspecialchars($userData['UserName'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($userData['FirstName'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($userData['LastName'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="userEmail">Email</label>
            <input type="email" id="userEmail" name="userEmail" value="<?= htmlspecialchars($userData['Email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="userBio">Bio</label>
            <textarea id="userBio" name="userBio" rows="5"><?= htmlspecialchars($userData['Bio'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn">Update Profile</button>
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
