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

// Fetch the user's friends
$friendsStmt = $pdo->prepare("
    SELECT members.user, members.first_name, members.last_name, members.title, members.profile_picture 
    FROM friends 
    JOIN members ON friends.friend_id = members.user 
    WHERE friends.user_id = ?
");
$friendsStmt->execute([$user]);
$friends = $friendsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIT2 | Friends</title>
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
                <a href="index.php" class="nav-link">Home</a>
                <a href="my_profile.php" class="nav-link">My Profile</a>
                <a href="messages.php" class="nav-link">Messages</a>
                <a href="people.php" class="nav-link">People</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>

        <!-- Right column -->
        <div class="right-column">
            <h1 class="right-header">Friends</h1>
            <div class="members-list">
                <?php if (!empty($friends)): ?>
                    <?php foreach ($friends as $index => $friend): ?>
                        <div class="member-row <?php echo $index % 2 === 0 ? 'light-row' : 'dark-row'; ?>">
                            <a href="profile.php?user=<?php echo urlencode($friend['user']); ?>" class="profile-link">
                                <img src="img/profiles/<?php echo htmlspecialchars($friend['profile_picture']); ?>" alt="Member Profile Picture" class="member-profile-picture">
                            </a>
                            <div class="member-info">
                                <a href="profile.php?user=<?php echo urlencode($friend['user']); ?>" class="member-name-link">
                                    <?php echo htmlspecialchars($friend['first_name'] . ' ' . $friend['last_name']); ?>
                                </a>
                                <p class="member-title"><?php echo htmlspecialchars($friend['title']); ?></p>
                            </div>
                            <form method="post" class="friend-form">
                                <input type="hidden" name="friend_id" value="<?php echo htmlspecialchars($friend['user']); ?>">
                                <button type="submit" name="friend_action" value="remove" class="friend-button">Remove Friend</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You have no friends yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2024 KIT2 | Community at Work</p>
    </div>
    </div>    
</body>
</html>
