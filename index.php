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
$stmt = $pdo->query("SELECT posts.*, members.first_name, members.last_name FROM posts INNER JOIN members ON posts.user = members.user ORDER BY posts.created_at DESC");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Left column -->
        <div class="left-column">
            <img src="img/profiles/<?php echo htmlspecialchars($profileData['profile_picture']); ?>" alt="Your Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($profileData['first_name'] . ' ' . $profileData['last_name']); ?></h2>
            <?php if (!empty($profileData['title'])): ?>
                <p><?php echo htmlspecialchars($profileData['title']); ?></p>
            <?php endif; ?>
            <nav>
                <a href="my_profile.php" class="nav-link">My Profile</a>
                <a href="messages.php" class="nav-link">Messages</a>
                <a href="people.php" class="nav-link">People</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
        </div>

        <!-- Right column -->
        <div class="right-column">
                <h1 class="centered">KIT2</h1>
            <form method="post" enctype="multipart/form-data" class="post-form">
                <textarea name="text" placeholder="What's on your mind?" maxlength="500"></textarea>
                <div class="post-controls">
                    <input type="file" name="image" accept="image/jpeg, image/png">
                     <button type="submit" class="post-button">Post</button>
                </div>

            </form>

            <!-- Posts Section -->
            <h2 class="centered">Posts</h2>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <p>
                        <strong>
                            <a href="profile.php?user=<?php echo urlencode($post['user']); ?>" class="post-author">
                                <?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?>
                            </a>
                        </strong>
                    </p>
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
</body>
</html>
