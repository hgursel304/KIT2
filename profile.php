<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $profilePicture = $user . '.' . $ext;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], "img/profiles/$profilePicture");
            $stmt = $pdo->prepare("UPDATE members SET profile_picture = ? WHERE user = ?");
            $stmt->execute([$profilePicture, $user]);
            echo "<script>alert('Profile picture updated!');</script>";
        } else {
            echo "<script>alert('Only JPEG and PNG files are allowed.');</script>";
        }
    }
}

$profilePicture = getProfilePicture($user);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Profile</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Your Profile</h2>
        <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-picture">
        <form method="post" enctype="multipart/form-data">
            <label for="profile_picture">Update Profile Picture (JPEG/PNG):</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png">
            <button type="submit">Update</button>
        </form>
    </div>
</body>
</html>
