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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIT2 Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
            <div class="login-box">
                <h2>KIT2</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="user">Username:</label>
                        <input type="text" id="user" name="user" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group">
                        <label for="pass">Password:</label>
                        <input type="password" id="pass" name="pass" placeholder="Enter your password" required>
                    </div>

                    <button type="submit">Login</button>
                    <?php if ($error): ?>
                        <p class="error-message"><?php echo $error; ?></p>
                    <?php endif; ?>
                </form>
                <p class="signup-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
            </div>
    </div>
</body>
</html>
