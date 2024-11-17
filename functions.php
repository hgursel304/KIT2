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

function sanitizeString($var) {
    return htmlentities(strip_tags($var), ENT_QUOTES, 'UTF-8');
}
?>
