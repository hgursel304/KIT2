<?php
require_once 'functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);
    $confirmPass = sanitizeString($_POST['confirm_pass']);
    $firstName = sanitizeString($_POST['first_name']);
    $lastName = sanitizeString($_POST['last_name']);
    $title = sanitizeString($_POST['title']);
    $profilePicture = 'default.png'; // Default profile picture

    // Password complexity validation regex
    $passwordPattern = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%_&*]).{8,}$/';

    if ($user === '' || $pass === '' || $confirmPass === '' || $firstName === '' || $lastName === '') {
        $error = "All fields are required except title.";
    } elseif ($pass !== $confirmPass) {
        $error = "Passwords do not match.";
    } elseif (!preg_match($passwordPattern, $pass)) {
        $error = "Password must be at least 8 characters long and include at least one uppercase letter, one number, and one special character (!@#$%_&*).";
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
                $stmt = $pdo->prepare("INSERT INTO members (user, pass, first_name, last_name, title, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user, $hashedPass, $firstName, $lastName, $title, $profilePicture]);
                echo "<script>alert('Account created successfully! Please log in.'); window.location.href = 'login.php';</script>";
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIT2 Sign Up</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="signup-box">
            <h2>KIT2 User Sign Up</h2>
            <?php if ($error): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                </div>

                <div class="form-group">
                    <label for="user">Username:</label>
                    <input type="text" id="user" name="user" placeholder="Enter your username" required>
                </div>

                <div class="form-group">
                    <label for="pass">Password:</label>
                    <input type="password" id="pass" name="pass" placeholder="Enter your password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_pass">Confirm Password:</label>
                    <input type="password" id="confirm_pass" name="confirm_pass" placeholder="Re-enter your password" required>
                </div>

                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" placeholder="Optional: Enter your title">
                </div>

                <div class="form-group">
                    <label for="profile_picture">Profile Picture (JPEG/PNG):</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png">
                </div>

                <button type="submit">Sign Up</button>
            </form>
            <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
        </div>
    </div>
</body>
</html>
