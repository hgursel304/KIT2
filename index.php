<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$profilePicture = getProfilePicture($user);

// Handle Post Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = isset($_POST['text']) ? sanitizeString($_POST['text']) : '';
    $image = null;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "img/posts/";
        $imageName = uniqid() . "-" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;

        // Allow only jpeg and png files
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($fileType, ['jpeg', 'jpg', 'png'])) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image = $imageName;
            } else {
                echo "<p>Failed to upload the image.</p>";
            }
        } else {
            echo "<p>Invalid file type. Only JPEG and PNG are allowed.</p>";
        }
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO posts (user, text, image) VALUES (?, ?, ?)");
    $stmt->execute([$user, $text, $image]);
}

// Fetch posts to display
$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Left column -->
        <div class="left-column">
            <img src="<?php echo $profilePicture; ?>" alt="Your Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($user); ?></h2>
            <nav>
                <a href="profile.php" class="nav-link">Profile</a>
                <a href="friends.php" class="nav-link">Friends</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>

            <!-- Friends Section -->
            <h2>Your Friends</h2>
            <?php
            $friendsStmt = $pdo->prepare("SELECT friend FROM friends WHERE user = ?");
            $friendsStmt->execute([$user]);
            $friends = $friendsStmt->fetchAll(PDO::FETCH_COLUMN);

            if ($friends): ?>
                <ul class="friends-list">
                    <?php foreach ($friends as $friend): ?>
                        <li><?php echo htmlspecialchars($friend); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>You have no friends added yet.</p>
            <?php endif; ?>
        </div>

        <!-- Right column -->
        <div class="right-column">
            <h1>KIT2</h1>
            <form method="post" enctype="multipart/form-data" class="post-form">
                <textarea name="text" placeholder="What's on your mind?" maxlength="500"></textarea>
                <input type="file" name="image" accept="image/jpeg, image/png">
                <button type="submit">Post</button>
            </form>

            <!-- Posts Section -->
            <h2>Posts</h2>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <p><strong><?php echo htmlspecialchars($post['user']); ?></strong></p>
                    <?php if ($post['text']): ?>
                        <p><?php echo htmlspecialchars($post['text']); ?></p>
                    <?php endif; ?>
                    <?php if ($post['image']): ?>
                        <img src="img/posts/<?php echo $post['image']; ?>" alt="Post Image" class="post-image">
                    <?php endif; ?>
                    <small>Posted on <?php echo $post['created_at']; ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
