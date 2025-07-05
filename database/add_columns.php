<?php
require __DIR__ . '/../config/database.php';

echo "<h2>Adding Missing Database Columns</h2>";

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Check existing columns
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $existingColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
    
    echo "<p>Existing columns: " . implode(', ', $existingColumns) . "</p>";
    
    // Define columns to add
    $columnsToAdd = [
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
    
    $added = 0;
    foreach ($columnsToAdd as $columnName => $columnDef) {
        if (!in_array($columnName, $existingColumns)) {
            try {
                $sql = "ALTER TABLE users ADD COLUMN `{$columnName}` {$columnDef}";
                $pdo->exec($sql);
                echo "<p style='color: green;'>✅ Added column: {$columnName}</p>";
                $added++;
            } catch (PDOException $e) {
                echo "<p style='color: red;'>❌ Error adding {$columnName}: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: blue;'>ℹ️ Column {$columnName} already exists</p>";
        }
    }
    
    if ($added > 0) {
        echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>Success!</strong> Added {$added} new columns to the database.";
        echo "</div>";
    } else {
        echo "<div style='background: #cce7ff; color: #004085; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>Info:</strong> All columns already exist in the database.";
        echo "</div>";
    }
    
    // Show final table structure
    echo "<h3>Final Table Structure:</h3>";
    $stmt = $pdo->prepare("DESCRIBE users");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #007bff; color: white;'>";
    echo "<th style='padding: 8px;'>Field</th><th style='padding: 8px;'>Type</th><th style='padding: 8px;'>Null</th><th style='padding: 8px;'>Key</th><th style='padding: 8px;'>Default</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td style='padding: 5px;'>" . $column['Field'] . "</td>";
        echo "<td style='padding: 5px;'>" . $column['Type'] . "</td>";
        echo "<td style='padding: 5px;'>" . $column['Null'] . "</td>";
        echo "<td style='padding: 5px;'>" . $column['Key'] . "</td>";
        echo "<td style='padding: 5px;'>" . ($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='../php/profile-update.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Profile Update</a>";
    echo " ";
    echo "<a href='../php/dashboard.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;'>";
    echo "<strong>Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>
