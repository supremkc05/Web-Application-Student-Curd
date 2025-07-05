<?php
require __DIR__ . '/../config/database.php';

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
            `phone` varchar(50) DEFAULT NULL,
            `phone_secondary` varchar(50) DEFAULT NULL,
            `course` text DEFAULT NULL,
            `course_secondary` varchar(255) DEFAULT NULL,
            `course_tertiary` varchar(255) DEFAULT NULL,
            `profile_picture` varchar(500) DEFAULT NULL,
            `profile_picture_path` varchar(1000) DEFAULT NULL,
            `address` text DEFAULT NULL,
            `date_of_birth` date DEFAULT NULL,
            `gender` enum('Male','Female','Other') DEFAULT NULL,
            `status` enum('Active','Inactive','Suspended') DEFAULT 'Active',
            `email_verified` boolean DEFAULT FALSE,
            `last_login` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX `idx_email` (`email`),
            INDEX `idx_status` (`status`),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createTableSQL);
    echo "Users table created successfully or already exists.<br>";
      // Check existing columns and add missing ones
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $existingColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    
    // Define required columns with their definitions
    $requiredColumns = [
        'phone' => 'varchar(50) DEFAULT NULL',
        'phone_secondary' => 'varchar(50) DEFAULT NULL',
        'course' => 'text DEFAULT NULL',
        'course_secondary' => 'varchar(255) DEFAULT NULL',
        'course_tertiary' => 'varchar(255) DEFAULT NULL',
        'profile_picture' => 'varchar(500) DEFAULT NULL',
        'profile_picture_path' => 'varchar(1000) DEFAULT NULL',
        'address' => 'text DEFAULT NULL',
        'date_of_birth' => 'date DEFAULT NULL',
        'gender' => "enum('Male','Female','Other') DEFAULT NULL",
        'status' => "enum('Active','Inactive','Suspended') DEFAULT 'Active'",
        'email_verified' => 'boolean DEFAULT FALSE',
        'last_login' => 'timestamp NULL DEFAULT NULL'
    ];
    
    $columnsAdded = 0;
    foreach ($requiredColumns as $columnName => $columnDef) {
        if (!in_array($columnName, $existingColumns)) {
            try {
                $alterSQL = "ALTER TABLE `users` ADD COLUMN `{$columnName}` {$columnDef}";
                $pdo->exec($alterSQL);
                echo "✅ Added column: {$columnName}<br>";
                $columnsAdded++;
            } catch (PDOException $e) {
                echo "❌ Error adding column {$columnName}: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "ℹ️ Column {$columnName} already exists<br>";
        }
    }
    
    if ($columnsAdded > 0) {
        echo "<br>✅ Added {$columnsAdded} new columns successfully!<br>";
    } else {
        echo "<br>ℹ️ All columns already exist.<br>";
    }
    
    echo "Database columns updated successfully.<br>";
    
    $createIndexSQL = "CREATE INDEX IF NOT EXISTS idx_status ON users(status)";
    $pdo->exec($createIndexSQL);
    
    $createIndexSQL2 = "CREATE INDEX IF NOT EXISTS idx_created_at ON users(created_at)";
    $pdo->exec($createIndexSQL2);
    
    echo "Indexes created successfully.<br>";
    
    echo "<br><strong>Database setup completed successfully!</strong><br>";
    echo "You can now use the login and registration forms.<br>";
    echo "<a href='../php/login.php'>Go to Login</a> | <a href='../php/signup.php'>Go to Signup</a>";
    
} catch (PDOException $e) {
    echo "Database setup failed: " . $e->getMessage();
}
?>
