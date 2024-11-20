<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get the logged-in user
$loggedInUser = $_SESSION['user'];

// Get the user parameter for the profile page
$profileUser = isset($_GET['user']) ? $_GET['user'] : null;

if (!$profileUser) {
    header("Location: people.php");
    exit;
}

// Fetch the profile user's data
$profileStmt = $pdo->prepare("SELECT first_name, last_name, title, profile_picture, about_me FROM members WHERE user = ?");
$profileStmt->execute([$profileUser]);
$profileData = $profileStmt->fetch();

if (!$profileData) {
    echo "<p>User not found!</p>";
    exit;
}

// Fetch profile user's posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user = ? ORDER BY created_at DESC");
$stmt->execute([$profileUser]);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile of <?php echo htmlspecialchars($profileData['first_name'] . ' ' . $profileData['last_name']); ?></title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Left column -->
        <div class="left-column">
            <img src="img/profiles/<?php echo htmlspecialchars($profileData['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($profileData['first_name'] . ' ' . $profileData['last_name']); ?></h2>
            <?php if (!empty($profileData['title'])): ?>
                <p class="member-title"><?php echo htmlspecialchars($profileData['title']); ?></p>
            <?php endif; ?>
            <div style="width: 70px; height: 1px; background-color: #000; margin: 10px auto;"></div>
            <nav>
                <a href="index.php" class="nav-link">Home</a>
                <a href="people.php" class="nav-link">People</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
            <div style="width: 70px; height: 1px; background-color: #000; margin: 10px auto;"></div>
            <!-- Messaging Feature -->
            <a href="messages.php?recipient=<?php echo urlencode($profileUser); ?>" class="message-button">Message</a>
        </div>

        <!-- Right column -->
        <div class="right-column">
            <h1 class="right-header">About</h1>
                <p class="centered"><?php echo nl2br(htmlspecialchars($profileData['about_me'])); ?></p>
            <h1 class="right-header">Posts</h1>
            <?php if ($posts): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <?php if ($post['image']): ?>
                            <img src="img/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                        <?php endif; ?>
                        <?php if ($post['text']): ?>
                            <p><?php echo htmlspecialchars($post['text']); ?></p>
                        <?php endif; ?>
                        <small>Posted on <?php echo $post['created_at']; ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
