<?php
require_once 'functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = sanitizeString($_POST['user']);
    $pass = password_hash(sanitizeString($_POST['pass']), PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM members WHERE user = ?");
    $stmt->execute([$user]);

    if ($stmt->rowCount() > 0) {
        $error = "Username is already taken.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO members (user, pass) VALUES (?, ?)");
        $stmt->execute([$user, $pass]);
        echo "Account created! <a href='login.php'>Log in</a>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Sign Up</title></head>
<body>
<h2>Sign Up</h2>
<p><?php echo $error; ?></p>
<form method="post">
    <label>Username:</label> <input type="text" name="user" required>
    <label>Password:</label> <input type="password" name="pass" required>
    <button type="submit">Sign Up</button>
</form>
</body>
</html>
