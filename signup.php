<?php
require_once 'functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);
    $profilePicture = 'default.png'; // Default profile picture

    if ($user === '' || $pass === '') {
        $error = "All fields are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE user = ?");
        $stmt->execute([$user]);

        if ($stmt->rowCount() > 0) {
            $error = "Username is already taken.";
        } else {
            // Handle file upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $profilePicture = $user . '.' . $ext;
                    move_uploaded_file($_FILES['profile_picture']['tmp_name'], "img/profiles/$profilePicture");
                } else {
                    $error = "Only JPEG and PNG files are allowed.";
                }
            }

            if (!$error) {
                $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO members (user, pass, profile_picture) VALUES (?, ?, ?)");
                $stmt->execute([$user, $hashedPass, $profilePicture]);
                echo "<script>alert('Account created successfully! Please log in.'); window.location.href = 'login.php';</script>";
                exit;
            }
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
        <form method="post" enctype="multipart/form-data">
            <label for="user">Username:</label>
            <input type="text" id="user" name="user" required>
            
            <label for="pass">Password:</label>
            <input type="password" id="pass" name="pass" required>
            
            <label for="profile_picture">Profile Picture (JPEG/PNG):</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png">

            <button type="submit">Sign Up</button>
        </form>
        
        <p>Already have an account?</p>
        <a href="login.php" class="signup-button">Log In</a>
    </div>
</body>
</html>
