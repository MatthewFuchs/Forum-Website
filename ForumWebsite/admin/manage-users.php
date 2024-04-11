<?php
session_start();

require_once '../backend/db_connect.php';

// Function to fetch all users
function fetchUsers($mysqli) {
    $users = [];
    $sql = "SELECT UserID, Username, Email, role FROM users";
    if ($result = $mysqli->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

// Handling deletion of a user
if (isset($_POST['delete']) && isset($_POST['userID'])) {
    $userID = $_POST['userID'];
    $sql = "DELETE FROM users WHERE UserID = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $stmt->close();
    }
    // Redirect to refresh the page and avoid resubmission
    header("Location: manage-users.php");
    exit;
}

$users = fetchUsers($mysqli);
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<header>
    <h1>Manage Users</h1>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage-posts.php">Manage Posts</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['Username']) ?></td>
                <td><?= htmlspecialchars($user['Email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <form method="POST" action="manage-users.php">
                        <input type="hidden" name="userID" value="<?= $user['UserID'] ?>">
                        <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure?');">
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
