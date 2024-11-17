<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT friend FROM friends WHERE user = ?");
$stmt->execute([$user]);
$friends = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $friend = sanitizeString($_POST['friend']);
    if (isset($_POST['add'])) {
        $stmt = $pdo->prepare("INSERT INTO friends (user, friend) VALUES (?, ?)");
        $stmt->execute([$user, $friend]);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Friends</title>
    <link rel="stylesheet" type="text/css" href="styles.css">

</head>
<body>
<h2>Your Friends</h2>
<ul>
    <?php foreach ($friends as $friend): ?>
        <li><?php echo htmlspecialchars($friend); ?></li>
    <?php endforeach; ?>
</ul>
<form method="post">
    <label>Add Friend:</label> 
    <input type="text" name="friend" required>
    <button type="submit" name="add">Add</button>
</form>
</body>
</html>
