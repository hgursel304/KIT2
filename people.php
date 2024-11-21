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

// Check if a friendship action was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['friend_action'], $_POST['friend_id'])) {
    $friendId = sanitizeString($_POST['friend_id']);
    if ($_POST['friend_action'] === 'add') {
        // Add friend
        $stmt = $pdo->prepare("INSERT IGNORE INTO friends (user_id, friend_id) VALUES (?, ?)");
        $stmt->execute([$user, $friendId]);
    } elseif ($_POST['friend_action'] === 'remove') {
        // Remove friend
        $stmt = $pdo->prepare("DELETE FROM friends WHERE user_id = ? AND friend_id = ?");
        $stmt->execute([$user, $friendId]);
    }
    // Redirect to prevent resubmission
    header("Location: people.php");
    exit;
}

// Fetch the user's current friends
$friendsStmt = $pdo->prepare("SELECT friend_id FROM friends WHERE user_id = ?");
$friendsStmt->execute([$user]);
$friends = $friendsStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIT2 | People</title>
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
                <a href="friends.php" class="nav-link">Friends</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>

        <!-- Right column -->
        <div class="right-column">
            <h1 class="right-header">People</h1>
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
            <?php if ($member['user'] !== $user): // Don't show button for yourself ?>
                <form method="post" class="friend-form">
                    <input type="hidden" name="friend_id" value="<?php echo htmlspecialchars($member['user']); ?>">
                    <?php if (in_array($member['user'], $friends)): ?>
                        <button type="submit" name="friend_action" value="remove" class="friend-button">Friend</button>
                    <?php else: ?>
                        <button type="submit" name="friend_action" value="add" class="friend-button">Add to Friends</button>
                    <?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
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
