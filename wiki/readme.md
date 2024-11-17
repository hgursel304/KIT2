Step 1: Database Setup in AMPPS
Open phpMyAdmin in AMPPS.

Create a new database, e.g., kit2.

Run the following SQL commands:

sql
Copy code
CREATE DATABASE kit2;
USE kit2;

CREATE TABLE members (
    user VARCHAR(16) NOT NULL,
    pass VARCHAR(255) NOT NULL,
    PRIMARY KEY (user)
);

CREATE TABLE messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    auth VARCHAR(16),
    recip VARCHAR(16),
    pm CHAR(1),
    time INT UNSIGNED,
    message TEXT,
    INDEX(auth(6)),
    INDEX(recip(6))
);

CREATE TABLE friends (
    user VARCHAR(16),
    friend VARCHAR(16),
    INDEX(user(6)),
    INDEX(friend(6))
);

CREATE TABLE profiles (
    user VARCHAR(16),
    text TEXT,
    INDEX(user(6))
);
Step 2: File Setup in AMPPS
Directory Structure:

Create a folder under AMPPS/www/ called kit2.
Place the project files (functions.php, header.php, signup.php, etc.) in this folder.
Core PHP Files:

functions.php
Handles database connections and reusable functions for KIT2.

php
Copy code
<?php
$host = 'localhost';
$data = 'kit2';
$user = 'root'; // AMPPS default user
$pass = 'mysql'; // AMPPS default password
$chrs = 'utf8mb4';
$attr = "mysql:host=$host;dbname=$data;charset=$chrs";

$opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($attr, $user, $pass, $opts);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function queryMysql($query) {
    global $pdo;
    return $pdo->query($query);
}

function sanitizeString($var) {
    return htmlentities(strip_tags($var), ENT_QUOTES, 'UTF-8');
}
?>
header.php
Sets up session handling and the basic structure for KIT2 pages.

php
Copy code
<?php
session_start();
$userstr = isset($_SESSION['user']) ? "Logged in as: " . $_SESSION['user'] : "Welcome, Guest";

echo <<<_HEADER
<!DOCTYPE html>
<html>
<head>
    <title>KIT2</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>KIT2</h1>
    <p>$userstr</p>
    <nav>
        <a href="index.php">Home</a> |
        <a href="signup.php">Sign Up</a> |
        <a href="login.php">Log In</a> |
        <a href="logout.php">Log Out</a>
    </nav>
</header>
_HEADER;
?>
signup.php
Implements user registration for KIT2.

php
Copy code
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
Step 3: Access Locally
Launch AMPPS and start Apache and MySQL.
Open a browser and navigate to http://localhost/kit2/.
