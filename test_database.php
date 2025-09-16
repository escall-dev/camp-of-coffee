<?php
echo "<h2>Database Connection Test</h2>";

// Test database connection
try {
    require_once 'config/database.php';
    echo "✅ Database connection successful<br>";
    
    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Users table accessible - " . $result['count'] . " users found<br>";
    
    // Check for profile columns
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'full_name'");
    if ($stmt->fetch()) {
        echo "✅ full_name column exists<br>";
    } else {
        echo "❌ full_name column missing<br>";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
    if ($stmt->fetch()) {
        echo "✅ email column exists<br>";
    } else {
        echo "❌ email column missing<br>";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'phone'");
    if ($stmt->fetch()) {
        echo "✅ phone column exists<br>";
    } else {
        echo "❌ phone column missing<br>";
    }
    
    // Test admin user
    $stmt = $pdo->prepare("SELECT id, username, full_name, email, phone FROM users WHERE username = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Admin user found:<br>";
        echo "<pre>";
        print_r($admin);
        echo "</pre>";
    } else {
        echo "❌ Admin user not found<br>";
    }
    
} catch(Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><a href='profile.php'>Go to Profile Page</a></p>";
echo "<p><a href='debug_profile.php'>Run Full Profile Debug</a></p>";
echo "<p><a href='update_database_profile.php'>Update Database Schema</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; padding: 20px; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
a { color: #8B4513; text-decoration: none; padding: 5px 10px; background: #f0f0f0; border-radius: 3px; margin: 5px; display: inline-block; }
a:hover { background: #8B4513; color: white; }
</style>
