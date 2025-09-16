<?php
require_once 'config/database.php';

// Get user profile by ID
function getUserProfile($userId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT id, username, full_name, email, phone, profile_image, role, created_at, updated_at 
            FROM users WHERE id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Get user profile error: " . $e->getMessage());
        return null;
    }
}

// Update user profile
function updateUserProfile($userId, $data) {
    global $pdo;
    
    try {
        $sql = "UPDATE users SET ";
        $params = [];
        $updates = [];
        
        // Allow updating with empty values (users can clear fields)
        if (isset($data['full_name'])) {
            $updates[] = "full_name = ?";
            $params[] = $data['full_name'];
        }
        
        if (isset($data['email'])) {
            $updates[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['phone'])) {
            $updates[] = "phone = ?";
            $params[] = $data['phone'];
        }
        
        if (isset($data['profile_image'])) {
            $updates[] = "profile_image = ?";
            $params[] = $data['profile_image'];
        }
        
        // Username should not be empty
        if (isset($data['username']) && !empty($data['username'])) {
            $updates[] = "username = ?";
            $params[] = $data['username'];
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'No data to update'];
        }
        
        $sql .= implode(', ', $updates) . " WHERE id = ?";
        $params[] = $userId;
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        if ($result) {
            // Update session username if it was changed
            if (isset($data['username']) && !empty($data['username'])) {
                $_SESSION['username'] = $data['username'];
            }
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update profile'];
        
    } catch(PDOException $e) {
        if ($e->getCode() == '23000') { // Duplicate entry
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        error_log("Update profile error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Update user password
function updateUserPassword($userId, $currentPassword, $newPassword) {
    global $pdo;
    
    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Update password
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $result = $stmt->execute([$newHash, $userId]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Password updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update password'];
        
    } catch(PDOException $e) {
        error_log("Update password error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}

// Handle profile image upload
function uploadProfileImage($file, $userId) {
    $uploadDir = 'uploads/profiles/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $fileType = $file['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed.'];
    }
    
    // Check file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Delete old profile image if exists
    $currentUser = getUserProfile($userId);
    if ($currentUser && $currentUser['profile_image'] && file_exists($currentUser['profile_image'])) {
        unlink($currentUser['profile_image']);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filepath' => $filepath, 'message' => 'Profile image uploaded successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to upload image'];
}

// Delete profile image
function deleteProfileImage($userId) {
    global $pdo;
    
    try {
        $user = getUserProfile($userId);
        if ($user && $user['profile_image'] && file_exists($user['profile_image'])) {
            unlink($user['profile_image']);
        }
        
        $stmt = $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
        $result = $stmt->execute([$userId]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Profile image deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete profile image'];
        
    } catch(PDOException $e) {
        error_log("Delete profile image error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred'];
    }
}
?>
