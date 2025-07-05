<?php
require '../config/database.php';

try {
    $tempPdo = new PDO("mysql:host=localhost", "root", "");
    $tempPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `wap-test`");
    echo "Database 'wap-test' created successfully or already exists.<br>";
    
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Create users table with extended profile fields
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `fullname` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL UNIQUE,
            `password` varchar(255) NOT NULL,
            `phone` varchar(20) DEFAULT NULL,
            `course` varchar(255) DEFAULT NULL,
            `profile_picture` varchar(255) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";
    
    $pdo->exec($createTableSQL);
    echo "Users table created successfully or already exists.<br>";
    
    $createIndexSQL = "CREATE INDEX IF NOT EXISTS idx_email ON users(email)";
    $pdo->exec($createIndexSQL);
    echo "Email index created successfully.<br>";
    
    echo "<br><strong>Database setup completed successfully!</strong><br>";
    echo "You can now use the login and registration forms.<br>";
    echo "<a href='../php/login.php'>Go to Login</a> | <a href='../php/signup.php'>Go to Signup</a>";
    
} catch (PDOException $e) {
    echo "Database setup failed: " . $e->getMessage();
}
?>
