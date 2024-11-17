# KIT2 Project Setup Guide

This guide outlines the steps to set up the **KIT2** project, including database configuration, file setup, and accessing the project locally.

---

## Step 1: Database Setup in AMPPS

1. Open **phpMyAdmin** in AMPPS by navigating to: http://localhost/phpmyadmin
2. Log in with the following default credentials (if not changed):
- Username: `root`
- Password: `mysql`

3. Create the database:
- Go to the **Databases** tab.
- Enter the database name `kit2` and click **Create**.
4. Create the required tables:
- Switch to the `kit2` database.
- Use the **SQL** tab to run the following commands:
```sql
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
  ```

---

## Step 2: File Setup in AMPPS

1. **Create a Project Directory**:
- Navigate to the `AMPPS/www/` directory.
- Create a folder named `kit2`.

2. **Core PHP Files**:
- Create the following files in the `kit2` folder:
  - `functions.php`: Handles database connections and reusable functions.
  - `header.php`: Sets up navigation and session handling.
  - `signup.php`: Implements user registration.

3. **Sample `functions.php` Code**:
```php
<?php
$host = 'localhost';
$data = 'kit2';
$user = 'root';
$pass = 'mysql';
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
?>

   
