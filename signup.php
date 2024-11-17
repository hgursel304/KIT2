<?php
require_once 'functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);

    if ($user === '' || $pass === '') {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE user = ?");
        $stmt->execute([$user]);

        if ($stmt->rowCount() > 0) {
            $error = "Username is already taken.";
        } else {
            $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO members (user, pass) VALUES (?, ?)");
            $stmt->execute([$user, $hashedPass]);
            echo "<script>alert('Account created successfully! Please log in.'); window.location.href = 'login.php';</script>";
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>KIT2 User Sign Up</h2>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="user">Username:</label>
            <input type="text" id="user" name="user" required>
            
            <label for="pass">Password:</label>
            <input type="password" id="pass" name="pass" required>
            
            <button type="submit">Sign Up</button>
        </form>
        
        <p>Already have an account?</p>
        <a href="login.php" class="signup-button">Log In</a>
    </div>
</body>
</html>
