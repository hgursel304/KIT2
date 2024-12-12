<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host']);
    $dbUser = trim($_POST['db_user']);
    $dbPass = trim($_POST['db_pass']);
    $error = '';
    $success = '';

    try {
        // Connect to MySQL
        $pdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create database if it does not exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS kit2;");
        $pdo->exec("USE kit2;");

        // Create tables
        $sql = file_get_contents('kit2_schema.sql');
        $pdo->exec($sql);

        // Success message
        $success = "Database and tables created successfully! Please create the first user.";
        
        // Redirect to signup.php after setup
        header("Refresh: 3; URL=signup.php"); // Redirect after 3 seconds
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KIT2 | Setup</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1 class="logo">KIT2</h1>
            <form method="POST">
                <div class="form-group">
                    <label for="db_host">Database Host:</label>
                    <input type="text" id="db_host" name="db_host" placeholder="localhost" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Database User:</label>
                    <input type="text" id="db_user" name="db_user" placeholder="root" required>
                </div>
                <div class="form-group">
                    <label for="db_pass">Database Password:</label>
                    <input type="password" id="db_pass" name="db_pass" placeholder="mysql" required>
                </div>
                <button type="submit">Setup Database</button>
                <?php if ($error): ?>
                    <p class="error-message"><?php echo $error; ?></p>
                <?php elseif ($success): ?>
                    <p class="success-message"><?php echo $success; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
