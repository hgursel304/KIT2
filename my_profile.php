<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

// Fetch logged-in user's data
$profileStmt = $pdo->prepare("SELECT first_name, last_name, title, profile_picture, about_me FROM members WHERE user = ?");
$profileStmt->execute([$user]);
$profileData = $profileStmt->fetch();

if (!$profileData) {
    die("User not found.");
}

// Handle Profile Picture Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $targetDir = "img/profiles/";
    $imageName = uniqid() . "-" . basename($_FILES['profile_picture']['name']);
    $targetFile = $targetDir . $imageName;

    // Allow only jpeg and png files
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (in_array($fileType, ['jpeg', 'jpg', 'png'])) {
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
            $stmt = $pdo->prepare("UPDATE members SET profile_picture = ? WHERE user = ?");
            $stmt->execute([$imageName, $user]);
            $profileData['profile_picture'] = $imageName;
        } else {
            echo "<p>Failed to upload the profile picture.</p>";
        }
    } else {
        echo "<p>Invalid file type. Only JPEG and PNG are allowed.</p>";
    }
}

// Handle About Me Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['about_me'])) {
    $aboutMe = sanitizeString($_POST['about_me']);
    $stmt = $pdo->prepare("UPDATE members SET about_me = ? WHERE user = ?");
    $stmt->execute([$aboutMe, $user]);
    $profileData['about_me'] = $aboutMe; // Update local data for display
}

// Handle Post Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['text'])) {
    $text = sanitizeString($_POST['text']);
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

    $stmt = $pdo->prepare("INSERT INTO posts (user, text, image) VALUES (?, ?, ?)");
    $stmt->execute([$user, $text, $image]);
}

// Handle Post Deletion
if (isset($_GET['delete'])) {
    $postId = intval($_GET['delete']);
    $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user = ?");
    $deleteStmt->execute([$postId, $user]);
    header("Location: my_profile.php");
    exit;
}

// Fetch user's posts
$stmt = $pdo->prepare("SELECT * FROM posts WHERE user = ? ORDER BY created_at DESC");
$stmt->execute([$user]);
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
                <a href="people.php" class="nav-link">People</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
            <div style="width: 70px; height: 1px; background-color: #000; margin: 10px auto;"></div>
            <h3>About Me</h3>
            <form method="post">
                <textarea name="about_me" rows="5" placeholder="Write about yourself..." style="width: 92%;"><?php echo htmlspecialchars($profileData['about_me']); ?></textarea>
                <button type="submit">Update About Me</button>
            </form>
            <div style="width: 70px; height: 1px; background-color: #000; margin: 10px auto;"></div>
            <h3>Change Profile Picture</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/jpeg, image/png">
                <button type="submit">Update Picture</button>
            </form>
        </div>

        <!-- Right column -->
        <div class="right-column">
            <h1 class="right-header">Create a Post</h1>
            <form method="post" enctype="multipart/form-data" class="post-form">
                <textarea name="text" placeholder="What's on your mind?" maxlength="500"></textarea>
                <div class="post-controls">
                    <input type="file" name="image" accept="image/jpeg, image/png">
                    <button type="submit" class="post-button">Post</button>
                </div>
            </form>
            <h1 class="right-header">My Posts</h1>
            <?php if ($posts): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post">
                        <?php if ($post['image']): ?>
                            <img src="img/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" class="post-image">
                        <?php endif; ?>
                        <?php if ($post['text']): ?>
                            <p><?php echo htmlspecialchars($post['text']); ?></p>
                        <?php endif; ?>
                        <small>
                            Posted on <?php echo $post['created_at']; ?>
                            <a href="my_profile.php?delete=<?php echo $post['id']; ?>" style="color: red; margin-left: 10px;">Delete</a>
                        </small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
