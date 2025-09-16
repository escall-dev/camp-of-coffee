<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once __DIR__ . '/activity.php';

// Authenticate user
function authenticateUser($username, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Log
            logActivity($user['id'], 'login', 'User logged in');
            
            return true;
        }
        
        return false;
    } catch(PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

// Create new user
function createUser($username, $password, $role = 'cashier') {
    global $pdo;
    
    try {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $password_hash, $role]);
    } catch(PDOException $e) {
        error_log("Create user error: " . $e->getMessage());
        return false;
    }
}

// Update user
function updateUser($id, $username, $password = null, $role = null) {
    global $pdo;
    
    try {
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password_hash = ?, role = ? WHERE id = ?");
            return $stmt->execute([$username, $password_hash, $role, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
            return $stmt->execute([$username, $role, $id]);
        }
    } catch(PDOException $e) {
        error_log("Update user error: " . $e->getMessage());
        return false;
    }
}

// Delete user
function deleteUser($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        return $stmt->execute([$id]);
    } catch(PDOException $e) {
        error_log("Delete user error: " . $e->getMessage());
        return false;
    }
}

// Get all users
function getAllUsers() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Get users error: " . $e->getMessage());
        return [];
    }
}

// Get user by ID
function getUserById($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Get user error: " . $e->getMessage());
        return null;
    }
}
?>
