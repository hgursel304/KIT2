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
