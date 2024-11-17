<?php
require_once 'functions.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);

    $stmt = $pdo->prepare("SELECT pass FROM members WHERE user = ?");
    $stmt->execute([$user]);
    $result = $stmt->fetch();

    if ($result && password_verify($pass, $result['pass'])) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<h2>Login</h2>
<p><?php echo $error; ?></p>
<form method="post">
    <label>Username:</label> <input type="text" name="user" required>
    <label>Password:</label> <input type="password" name="pass" required>
    <button type="submit">Login</button>
</form>
</body>
</html>
