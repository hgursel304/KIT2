<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$stmt = $pdo->query("SELECT user FROM members WHERE user != '$user'");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Home</title></head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($user); ?>!</h2>
<a href="profile.php">Your Profile</a> | 
<a href="friends.php">Friends</a> | 
<a href="logout.php">Log Out</a>
<h3>Other Users</h3>
<ul>
    <?php foreach ($users as $row): ?>
        <li><?php echo htmlspecialchars($row['user']); ?></li>
    <?php endforeach; ?>
</ul>
</body>
</html>
