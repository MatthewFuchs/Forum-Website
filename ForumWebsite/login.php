<?php
session_start(); // Start the session at the very beginning

require_once 'backend/db_connect.php'; // Adjust the path as necessary

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $mysqli->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Password as typed in by the user

    // SQL query to fetch user data along with role
    $sql = "SELECT UserID, Username, Password, role FROM users WHERE Username = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $username);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['Password'])) {
                    // Correct password, so start the user session
                    $_SESSION['userID'] = $user['UserID'];
                    $_SESSION['username'] = $user['Username'];
                    $_SESSION['role'] = $user['role']; // Store the user's role in the session

                    // Redirect to the admin dashboard if the user is an admin
                    if ($user['role'] === 'admin') {
                        header("Location: admin/dashboard.php");
                    } else {
                        // Redirect to home page or user dashboard for regular users
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    // Incorrect password
                    echo "<p>Incorrect username or password.</p>";
                }
            } else {
                // User not found
                echo "<p>Incorrect username or password.</p>";
            }
        } else {
            echo "Error: " . $mysqli->error;
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
    <title>Login</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<header>
    <div class="container">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="categories.php">Categories</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            </ul>
            <form class="search" action="search.php" method="get">
                <input type="search" name="query" placeholder="Search by post name or tag...">
                <button type="submit">Search</button>
            </form>
        </nav>
    </div>
</header>

<main class="main-container">
    <h2>Login</h2>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="log-in">Login</button>
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
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
