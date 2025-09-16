<?php
// This script updates the existing database to add profile fields
require_once 'config/database.php';

try {
    echo "<h2>Updating Database for Profile Features</h2>";
    
    // Check if columns already exist
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $columnsToAdd = [
        'full_name' => "ADD COLUMN full_name VARCHAR(100) NULL",
        'email' => "ADD COLUMN email VARCHAR(100) NULL", 
        'phone' => "ADD COLUMN phone VARCHAR(20) NULL",
        'profile_image' => "ADD COLUMN profile_image VARCHAR(255) NULL",
        'updated_at' => "ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    foreach ($columnsToAdd as $column => $sql) {
        if (!in_array($column, $columns)) {
            $pdo->exec("ALTER TABLE users $sql");
            echo "✅ Added column: $column<br>";
        } else {
            echo "ℹ️ Column already exists: $column<br>";
        }
    }
    
    // Create uploads directory if it doesn't exist
    $uploadDir = 'uploads/profiles';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        echo "✅ Created uploads directory: $uploadDir<br>";
    }
    
    echo "<br><h3>✅ Database update completed successfully!</h3>";
    echo "<p>You can now use the profile management features.</p>";
    echo "<p><a href='dashboard.php' class='btn btn-primary'>Go to Dashboard</a></p>";
    
} catch(PDOException $e) {
    echo "<h3>❌ Database update failed:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
