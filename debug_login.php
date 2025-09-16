<?php
echo "<h2>Camp Of Coffee - Login Debug Tool</h2>";
echo "<hr>";

// Test 1: Database Connection
echo "<h3>1. Testing Database Connection</h3>";
try {
    require_once 'config/database.php';
    echo "✅ Database connection successful<br>";
    echo "Database: " . DB_NAME . "<br>";
    echo "Host: " . DB_HOST . "<br>";
} catch(Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    die("Cannot proceed without database connection.");
}

// Test 2: Check if users table exists
echo "<h3>2. Checking Users Table</h3>";
try {
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    echo "✅ Users table exists with columns:<br>";
    foreach($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
    }
} catch(Exception $e) {
    echo "❌ Users table error: " . $e->getMessage() . "<br>";
}

// Test 3: Check if admin user exists
echo "<h3>3. Checking Admin User</h3>";
try {
    $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = 'admin'");
    $stmt->execute();
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✅ Admin user found:<br>";
        echo "- ID: " . $user['id'] . "<br>";
        echo "- Username: " . $user['username'] . "<br>";
        echo "- Role: " . $user['role'] . "<br>";
        echo "- Password Hash: " . substr($user['password_hash'], 0, 20) . "...<br>";
    } else {
        echo "❌ Admin user not found in database<br>";
    }
} catch(Exception $e) {
    echo "❌ Error checking admin user: " . $e->getMessage() . "<br>";
}

// Test 4: Test password verification
echo "<h3>4. Testing Password Verification</h3>";
if (isset($user)) {
    $testPassword = 'admin123';
    if (password_verify($testPassword, $user['password_hash'])) {
        echo "✅ Password verification works - 'admin123' matches the stored hash<br>";
    } else {
        echo "❌ Password verification failed - 'admin123' does not match the stored hash<br>";
        echo "Let's create a new hash for 'admin123':<br>";
        $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
        echo "New hash: " . $newHash . "<br>";
        
        // Update the database with correct hash
        try {
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
            $stmt->execute([$newHash]);
            echo "✅ Admin password hash has been updated in the database<br>";
        } catch(Exception $e) {
            echo "❌ Failed to update password hash: " . $e->getMessage() . "<br>";
        }
    }
}

// Test 5: Test authentication function
echo "<h3>5. Testing Authentication Function</h3>";
try {
    require_once 'includes/auth.php';
    
    // Test the actual authentication function
    if (authenticateUser('admin', 'admin123')) {
        echo "✅ Authentication function works correctly<br>";
        session_start();
        echo "Session variables set:<br>";
        echo "- user_id: " . ($_SESSION['user_id'] ?? 'not set') . "<br>";
        echo "- username: " . ($_SESSION['username'] ?? 'not set') . "<br>";
        echo "- role: " . ($_SESSION['role'] ?? 'not set') . "<br>";
    } else {
        echo "❌ Authentication function failed<br>";
    }
} catch(Exception $e) {
    echo "❌ Error testing authentication: " . $e->getMessage() . "<br>";
}

// Test 6: Check all users in database
echo "<h3>6. All Users in Database</h3>";
try {
    $stmt = $pdo->query("SELECT id, username, role, created_at FROM users");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "Found " . count($users) . " user(s):<br>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th></tr>";
        foreach($users as $u) {
            echo "<tr>";
            echo "<td>" . $u['id'] . "</td>";
            echo "<td>" . $u['username'] . "</td>";
            echo "<td>" . $u['role'] . "</td>";
            echo "<td>" . $u['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No users found in database<br>";
        echo "Creating admin user now...<br>";
        $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES ('admin', ?, 'admin')");
        if ($stmt->execute([$adminHash])) {
            echo "✅ Admin user created successfully<br>";
        } else {
            echo "❌ Failed to create admin user<br>";
        }
    }
} catch(Exception $e) {
    echo "❌ Error listing users: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "1. If all tests pass, try logging in again at <a href='login.php'>login.php</a><br>";
echo "2. If tests fail, check the error messages above<br>";
echo "3. Make sure your XAMPP MySQL service is running<br>";
echo "4. Check if the database 'camp_of_coffee' exists in phpMyAdmin<br>";
?>
