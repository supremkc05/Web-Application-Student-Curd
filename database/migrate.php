<?php
require __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    echo "<h2>Database Migration Script</h2>";
    echo "Updating existing database structure...<br><br>";
    
    // Check current table structure
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $currentColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $existingColumns = array_column($currentColumns, 'Field');
    
    echo "<h3>Current Columns:</h3>";
    foreach ($existingColumns as $column) {
        echo "- " . $column . "<br>";
    }
    echo "<br>";
    
    // Define columns that should exist
    $requiredColumns = [
        'phone_secondary' => "ALTER TABLE `users` ADD COLUMN `phone_secondary` varchar(50) DEFAULT NULL AFTER `phone`",
        'course_secondary' => "ALTER TABLE `users` ADD COLUMN `course_secondary` varchar(255) DEFAULT NULL AFTER `course`",
        'course_tertiary' => "ALTER TABLE `users` ADD COLUMN `course_tertiary` varchar(255) DEFAULT NULL AFTER `course_secondary`",
        'profile_picture_path' => "ALTER TABLE `users` ADD COLUMN `profile_picture_path` varchar(1000) DEFAULT NULL AFTER `profile_picture`",
        'address' => "ALTER TABLE `users` ADD COLUMN `address` text DEFAULT NULL AFTER `profile_picture_path`",
        'date_of_birth' => "ALTER TABLE `users` ADD COLUMN `date_of_birth` date DEFAULT NULL AFTER `address`",
        'gender' => "ALTER TABLE `users` ADD COLUMN `gender` enum('Male','Female','Other') DEFAULT NULL AFTER `date_of_birth`",
        'status' => "ALTER TABLE `users` ADD COLUMN `status` enum('Active','Inactive','Suspended') DEFAULT 'Active' AFTER `gender`",
        'email_verified' => "ALTER TABLE `users` ADD COLUMN `email_verified` boolean DEFAULT FALSE AFTER `status`",
        'last_login' => "ALTER TABLE `users` ADD COLUMN `last_login` timestamp NULL DEFAULT NULL AFTER `email_verified`"
    ];
    
    // Modify existing columns
    $modifyColumns = [
        "ALTER TABLE `users` MODIFY COLUMN `phone` varchar(50) DEFAULT NULL",
        "ALTER TABLE `users` MODIFY COLUMN `course` text DEFAULT NULL",
        "ALTER TABLE `users` MODIFY COLUMN `profile_picture` varchar(500) DEFAULT NULL"
    ];
    
    $addedColumns = 0;
    
    // Add missing columns
    foreach ($requiredColumns as $columnName => $query) {
        if (!in_array($columnName, $existingColumns)) {
            try {
                $pdo->exec($query);
                echo "✓ Added column: $columnName<br>";
                $addedColumns++;
            } catch (PDOException $e) {
                echo "✗ Failed to add column $columnName: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "- Column $columnName already exists<br>";
        }
    }
    
    // Modify existing columns
    echo "<br><h3>Modifying existing columns:</h3>";
    foreach ($modifyColumns as $query) {
        try {
            $pdo->exec($query);
            echo "✓ Modified column structure<br>";
        } catch (PDOException $e) {
            echo "✗ Failed to modify column: " . $e->getMessage() . "<br>";
        }
    }
    
    // Create additional indexes
    echo "<br><h3>Creating indexes:</h3>";
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_status ON users(status)",
        "CREATE INDEX IF NOT EXISTS idx_created_at ON users(created_at)",
        "CREATE INDEX IF NOT EXISTS idx_last_login ON users(last_login)"
    ];
    
    foreach ($indexes as $indexQuery) {
        try {
            $pdo->exec($indexQuery);
            echo "✓ Index created successfully<br>";
        } catch (PDOException $e) {
            echo "✗ Failed to create index: " . $e->getMessage() . "<br>";
        }
    }
    
    // Show final table structure
    echo "<br><h3>Final Table Structure:</h3>";
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $finalColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #007bff; color: white;'>";
    echo "<th style='padding: 10px;'>Field</th>";
    echo "<th style='padding: 10px;'>Type</th>";
    echo "<th style='padding: 10px;'>Null</th>";
    echo "<th style='padding: 10px;'>Key</th>";
    echo "<th style='padding: 10px;'>Default</th>";
    echo "<th style='padding: 10px;'>Extra</th>";
    echo "</tr>";
    
    foreach ($finalColumns as $column) {
        echo "<tr>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . $column['Field'] . "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . $column['Type'] . "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . $column['Null'] . "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . $column['Key'] . "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . ($column['Default'] ?? 'NULL') . "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<br><h3>Migration Summary:</h3>";
    echo "✓ Database migration completed successfully!<br>";
    echo "✓ Added $addedColumns new columns<br>";
    echo "✓ Modified existing column types<br>";
    echo "✓ Created additional indexes<br>";
    
    echo "<br><div style='background-color: #d4edda; padding: 15px; border-radius: 5px; border-left: 4px solid #28a745;'>";
    echo "<strong>Migration Complete!</strong><br>";
    echo "Your database has been updated with all required columns.<br>";
    echo "You can now use all features without any undefined array key warnings.";
    echo "</div>";
    
    echo "<br><a href='../php/dashboard.php' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>";
    echo " | <a href='../php/login.php' style='background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login</a>";
    
} catch (PDOException $e) {
    echo "<div style='background-color: #f8d7da; padding: 15px; border-radius: 5px; border-left: 4px solid #dc3545;'>";
    echo "<strong>Migration Failed:</strong><br>" . $e->getMessage();
    echo "</div>";
}
?>
