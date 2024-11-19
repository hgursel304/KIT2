<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$profilePicture = getProfilePicture($user);

// Handle Profile Picture Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $targetDir = "img/profiles/";
    $imageName = uniqid() . "-" . basename($_FILES['profile_picture']['name']);
    $targetFile = $targetDir . $imageName;

    // Allow only jpeg and png files
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (in_array($fileType, ['jpeg', 'jpg', 'png'])) {
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
            // Update the database with the new profile picture
            $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE username = ?");
            $stmt->execute([$imageName, $user]);
            $profilePicture = $targetFile;
        } else {
            echo "<p>Failed to upload the profile picture.</p>";
        }
    } else {
        echo "<p>Invalid file type. Only JPEG and PNG are allowed.</p>";
    }
}

// Fetch user's posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user = ? ORDER BY created_at DESC");
$stmt->execute([$user]);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Profile</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Left column -->
        <div class="left-column">
            <img src="<?php echo $profilePicture; ?>" alt="Your Profile Picture" class="profile-picture">
            <p><strong><?php echo htmlspecialchars($user); ?></strong></p>
            <nav>
                <a href="index.php" class="nav-link">Home</a>
                <a href="friends.php" class="nav-link">Friends</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
            <hr>
            <h2>Change Profile Picture</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/jpeg, image/png">
                <button type="submit">Update Picture</button>
            </form>
        </div>

        <!-- Right column -->
        <div class="right-column">
            <h1>Your Posts</h1>
            <?php if ($posts): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <?php if ($post['text']): ?>
                            <p><?php echo htmlspecialchars($post['text']); ?></p>
                        <?php endif; ?>
                        <?php if ($post['image']): ?>
                            <img src="img/posts/<?php echo $post['image']; ?>" alt="Post Image" class="post-image">
                        <?php endif; ?>
                        <small>Posted on <?php echo $post['created_at']; ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no posts yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
