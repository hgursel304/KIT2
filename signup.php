<?php
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = sanitizeString($_POST['user']);
    $pass = password_hash(sanitizeString($_POST['pass']), PASSWORD_DEFAULT);

    if ($user === '' || $pass === '') {
        $error = "All fields are required!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE user = ?");
        $stmt->execute([$user]);
        if ($stmt->rowCount() > 0) {
            $error = "Username is taken!";
        } else {
            $pdo->prepare("INSERT INTO members (user, pass) VALUES (?, ?)")->execute([$user, $pass]);
            echo "Account created. <a href='login.php'>Log in</a>";
            exit;
        }
    }
}
?>
<form method="post">
    <label>Username:</label>
    <input type="text" name="user" maxlength="16" required>
    <label>Password:</label>
    <input type="password" name="pass" required>
    <button type="submit">Sign Up</button>
</form>
