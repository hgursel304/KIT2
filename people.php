<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

// Fetch logged-in user's profile data
$profileStmt = $pdo->prepare("SELECT first_name, last_name, title, profile_picture FROM members WHERE user = ?");
$profileStmt->execute([$user]);
$profileData = $profileStmt->fetch();

// Fetch all members
$membersStmt = $pdo->query("SELECT user, first_name, last_name, title, profile_picture FROM members");
$members = $membersStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
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
                <a href="index.php" class="nav-link">Home</a>
                <a href="my_profile.php" class="nav-link">My Profile</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>

        <!-- Right column -->
        <div class="right-column">
            <h1 class="centered">People</h1>
            <div class="members-list">
                <?php foreach ($members as $index => $member): ?>
                    <div class="member-row <?php echo $index % 2 === 0 ? 'light-row' : 'dark-row'; ?>">
                        <a href="profile.php?user=<?php echo urlencode($member['user']); ?>" class="profile-link">
                            <img src="img/profiles/<?php echo htmlspecialchars($member['profile_picture']); ?>" alt="Member Profile Picture" class="member-profile-picture">
                        </a>
                        <div class="member-info">
                            <a href="profile.php?user=<?php echo urlencode($member['user']); ?>" class="member-name-link">
                                <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                            </a>
                            <p class="member-title"><?php echo htmlspecialchars($member['title']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
