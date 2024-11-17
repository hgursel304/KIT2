<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile = sanitizeString($_POST['profile']);
    $stmt = $pdo->prepare("INSERT INTO profiles (user, text) VALUES (?, ?) ON DUPLICATE KEY UPDATE text = ?");
    $stmt->execute([$user, $profile, $profile]);
}

$stmt = $pdo->prepare("SELECT text FROM profiles WHERE user = ?");
$stmt->execute([$user]);
$profile = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head><title>Profile</title></head>
<body>
<h2>Your Profile</h2>
<form method="post">
    <textarea name="profile"><?php echo htmlspecialchars($profile); ?></textarea>
    <button type="submit">Save</button>
</form>
</body>
</html>
