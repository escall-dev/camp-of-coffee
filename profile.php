<?php
// Include required files first (but not header yet)
require_once 'config/session.php';
requireLogin();
require_once 'includes/profile.php';

$userId = getCurrentUserId();
$user = getUserProfile($userId);

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log all POST data
    error_log("POST data received: " . json_encode($_POST));
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $data = [
                    'full_name' => trim($_POST['full_name'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'phone' => trim($_POST['phone'] ?? ''),
                    'username' => trim($_POST['username'] ?? '')
                ];
                
                // Debug: Log processed data
                error_log("Processed profile data: " . json_encode($data));
                
                $result = updateUserProfile($userId, $data);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'danger';
                
                // Debug information (remove in production)
                if (!$result['success']) {
                    error_log("Profile update failed for user $userId: " . $result['message']);
                    error_log("Data attempted: " . json_encode($data));
                }
                
                // Refresh user data
                $user = getUserProfile($userId);
                break;
                
            case 'update_password':
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if ($newPassword !== $confirmPassword) {
                    $message = 'New passwords do not match';
                    $messageType = 'danger';
                } elseif (strlen($newPassword) < 6) {
                    $message = 'Password must be at least 6 characters long';
                    $messageType = 'danger';
                } else {
                    $result = updateUserPassword($userId, $currentPassword, $newPassword);
                    $message = $result['message'];
                    $messageType = $result['success'] ? 'success' : 'danger';
                }
                break;
                
            case 'upload_image':
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $result = uploadProfileImage($_FILES['profile_image'], $userId);
                    
                    if ($result['success']) {
                        $updateResult = updateUserProfile($userId, ['profile_image' => $result['filepath']]);
                        $message = $result['message'];
                        $messageType = 'success';
                        $user = getUserProfile($userId); // Refresh user data
                    } else {
                        $message = $result['message'];
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Please select an image to upload';
                    $messageType = 'warning';
                }
                break;
                
            case 'delete_image':
                $result = deleteProfileImage($userId);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'danger';
                $user = getUserProfile($userId); // Refresh user data
                break;
        }
    }
}

// NOW include header after all processing is done
$pageTitle = 'My Profile';
require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0">
            <i class='bx bx-user-circle me-2'></i>My Profile
        </h2>
        <p class="text-muted">Manage your account settings and preferences</p>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>-fill me-2"></i>
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <!-- Profile Image Section -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class='bx bx-image me-2'></i>Profile Picture
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="profile-image-container mb-3">
                    <?php if ($user['profile_image'] && file_exists($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                             alt="Profile Picture" 
                             class="profile-image-large rounded-circle">
                    <?php else: ?>
                        <div class="profile-placeholder rounded-circle">
                            <i class='bx bx-user' style="font-size: 4rem;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="mb-3">
                    <input type="hidden" name="action" value="upload_image">
                    <div class="mb-3">
                        <input type="file" class="form-control" name="profile_image" 
                               accept="image/*" required>
                        <small class="text-muted">Max size: 5MB. Formats: JPG, PNG, GIF</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class='bx bx-upload me-1'></i>Upload Photo
                    </button>
                </form>
                
                <?php if ($user['profile_image']): ?>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_image">
                    <button type="submit" class="btn btn-danger btn-sm" 
                            onclick="return confirm('Are you sure you want to delete your profile picture?')">
                        <i class='bx bx-trash me-1'></i>Delete Photo
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Profile Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class='bx bx-user me-2'></i>Personal Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" value="<?php echo ucfirst($user['role'] ?? ''); ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <strong>Account Created:</strong> 
                            <?php echo date('M d, Y h:i A', strtotime($user['created_at'])); ?>
                        </small>
                        <?php if ($user['updated_at']): ?>
                        <br>
                        <small class="text-muted">
                            <strong>Last Updated:</strong> 
                            <?php echo date('M d, Y h:i A', strtotime($user['updated_at'])); ?>
                        </small>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class='bx bx-save me-2'></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class='bx bx-lock me-2'></i>Change Password
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_password">
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" 
                               name="current_password" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password" minlength="6" required>
                            <small class="text-muted">Minimum 6 characters</small>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" minlength="6" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning">
                        <i class='bx bx-key me-2'></i>Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.profile-image-large {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border: 3px solid var(--coffee-primary);
}

.profile-placeholder {
    width: 150px;
    height: 150px;
    background: var(--coffee-secondary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.profile-image-container {
    display: flex;
    justify-content: center;
}
</style>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('new_password').addEventListener('input', function() {
    const confirmPassword = document.getElementById('confirm_password');
    if (confirmPassword.value) {
        confirmPassword.dispatchEvent(new Event('input'));
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
