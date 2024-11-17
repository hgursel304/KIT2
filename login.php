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
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>KIT2 User Login</h2>
        <form method="post">
            <label for="user">Username:</label>
            <input type="text" id="user" name="user" required>
            
            <label for="pass">Password:</label>
            <input type="password" id="pass" name="pass" required>
            
            <button type="submit">Login</button>
        </form>
        
        <!-- Sign Up Button -->
        <p>Don't have an account?</p>
        <a href="signup.php" class="signup-button">Sign Up</a>
    </div>
</body>
</html>
