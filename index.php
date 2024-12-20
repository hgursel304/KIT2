<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$profileStmt = $pdo->prepare("SELECT first_name, last_name, title, profile_picture FROM members WHERE user = ?");
$profileStmt->execute([$user]);
$profileData = $profileStmt->fetch();

// Determine the post filter
$filter = isset($_GET['filter']) && $_GET['filter'] === 'all' ? 'all' : 'friends';

// Fetch posts based on filter
if ($filter === 'friends') {
    $stmt = $pdo->prepare(
        "SELECT posts.*, members.first_name, members.last_name 
        FROM posts 
        INNER JOIN members ON posts.user = members.user 
        WHERE posts.user IN (SELECT friend_id FROM friends WHERE user_id = ?) 
        ORDER BY posts.created_at DESC"
    );
    $stmt->execute([$user]);
} else {
    $stmt = $pdo->query(
        "SELECT posts.*, members.first_name, members.last_name 
        FROM posts 
        INNER JOIN members ON posts.user = members.user 
        ORDER BY posts.created_at DESC"
    );
}

$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIT2 | Home</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
<div class="container-full">
    <div class="container">
        <!-- Left column -->
        <div class="left-column">
            <img src="img/profiles/<?php echo htmlspecialchars($profileData['profile_picture']); ?>" alt="Your Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($profileData['first_name'] . ' ' . $profileData['last_name']); ?></h2>
            <?php if (!empty($profileData['title'])): ?>
                <p class="member-title"><?php echo htmlspecialchars($profileData['title']); ?></p>
            <?php endif; ?>
            <div style="width: 70px; height: 1px; background-color: #000; margin: 10px auto;"></div>
            <nav>
                <span class="nav active">Home</span>
                <a href="my_profile.php" class="nav-link">My Profile</a>
                <a href="messages.php" class="nav-link">Messages</a>
                <a href="friends.php" class="nav-link">Friends</a>
                <a href="people.php" class="nav-link">People</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>

        <!-- Right column -->
        <div class="right-column">
            <p class="logo">
                <span class="kit2">KIT2</span>
                <span class="divider"></span>
                <span class="slogan">Community at Work</span>
            </p>
            <!-- Filter Buttons -->
            <div class="post-filter">
                <a href="index.php?filter=friends" class="filter-button <?php echo $filter === 'friends' ? 'active' : ''; ?>">Friends</a>
                <a href="index.php?filter=all" class="filter-button <?php echo $filter === 'all' ? 'active' : ''; ?>">All People</a>
            </div>
            <!-- Posts Section -->
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <strong>
                        <a href="profile.php?user=<?php echo urlencode($post['user']); ?>" class="post-author">
                            <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?>
                        </a>
                    </strong>
                    <?php if ($post['image']): ?>
                        <img src="img/posts/<?php echo $post['image']; ?>" alt="Post Image" class="post-image">
                    <?php endif; ?>
                    <?php if ($post['text']): ?>
                        <p><?php echo htmlspecialchars($post['text']); ?></p>
                    <?php endif; ?>
                    <small>Posted on <?php echo $post['created_at']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 KIT2 | Community at Work</p>
    </div>
</div>
</body>
</html>
