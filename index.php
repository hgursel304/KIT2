<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$profilePicture = getProfilePicture($user); // Fetch the logged-in user's profile picture

// Fetch other users excluding the logged-in user
$stmt = $pdo->query("SELECT user FROM members WHERE user != '$user'");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>KIT2</h1>
        <!-- Display user profile picture and welcome message -->
        <div class="welcome-section">
            <img src="<?php echo $profilePicture; ?>" alt="Your Profile Picture" class="profile-picture">
            <p class="welcome">Welcome, <strong><?php echo htmlspecialchars($user); ?></strong>!</p>
        </div>
        
        <!-- Navigation links -->
        <nav>
            <a href="profile.php" class="nav-link">Your Profile</a>
            <a href="friends.php" class="nav-link">Friends</a>
            <a href="logout.php" class="nav-link">Log Out</a>
        </nav>

        <!-- Display other users -->
        <h2>Other Users</h2>
        <ul class="user-list">
            <?php foreach ($users as $row): ?>
                <li><?php echo htmlspecialchars($row['user']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
