<?php
// This script fixes the admin password in an existing database
require_once 'config/database.php';

try {
    // Update the admin user's password hash
    $newPasswordHash = '$2y$10$wJiN/lbj0mz6nzP8YcQa9.UXh0vONSflYPDMI5W6A7FchYlY2AUCi'; // Hash for "admin123"
    
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'admin'");
    $result = $stmt->execute([$newPasswordHash]);
    
    if ($result) {
        echo "✅ Admin password has been fixed! You can now login with:\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        echo "❌ Failed to update admin password.\n";
    }
} catch(PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>
