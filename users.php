<?php
// Include required files first (but not header yet)
require_once 'config/session.php';
requireLogin();
require_once 'includes/auth.php';

// Check if user is admin
requireAdmin();

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? 'cashier';
                $fullName = trim($_POST['full_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                
                if ($username && $password) {
                    // Create user with additional profile fields
                    try {
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("
                            INSERT INTO users (username, password_hash, role, full_name, email, phone) 
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        if ($stmt->execute([$username, $passwordHash, $role, $fullName, $email, $phone])) {
                            $message = 'User created successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to create user. Username may already exist.';
                            $messageType = 'danger';
                        }
                    } catch(PDOException $e) {
                        $message = 'Failed to create user. Username or email may already exist.';
                        $messageType = 'danger';
                    }
                } else {
                    $message = 'Please fill all required fields.';
                    $messageType = 'warning';
                }
                break;
                
            case 'update':
                $id = intval($_POST['id'] ?? 0);
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? 'cashier';
                $fullName = trim($_POST['full_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                
                if ($id && $username) {
                    try {
                        // Build update query dynamically
                        $updateFields = ['username = ?', 'role = ?', 'full_name = ?', 'email = ?', 'phone = ?'];
                        $params = [$username, $role, $fullName, $email, $phone];
                        
                        if ($password) {
                            $updateFields[] = 'password_hash = ?';
                            $params[] = password_hash($password, PASSWORD_DEFAULT);
                        }
                        
                        $params[] = $id;
                        
                        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
                        $stmt = $pdo->prepare($sql);
                        
                        if ($stmt->execute($params)) {
                            $message = 'User updated successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to update user.';
                            $messageType = 'danger';
                        }
                    } catch(PDOException $e) {
                        $message = 'Failed to update user. Username or email may already exist.';
                        $messageType = 'danger';
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id'] ?? 0);
                if ($id) {
                    if (deleteUser($id)) {
                        $message = 'User deleted successfully!';
                        $messageType = 'success';
                    } else {
                        $message = 'Failed to delete user. Cannot delete admin users.';
                        $messageType = 'danger';
                    }
                }
                break;
        }
    }
}

// Get all users with profile info
try {
    $stmt = $pdo->query("SELECT id, username, role, full_name, email, phone, profile_image, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    $users = [];
}

// NOW include header after all processing is done
$pageTitle = 'Users Management';
require_once 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="mb-0">
            <i class="bi bi-people me-2"></i>Users Management
        </h2>
        <p class="text-muted">Manage system users and their access levels</p>
    </div>
    <div class="col-auto me-5">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus me-2"></i>Add New User
        </button>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <i class="bi bi-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>-fill me-2"></i>
    <?php echo htmlspecialchars($message); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>User Info</th>
                        <th>Contact</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($user['profile_image'] && file_exists($user['profile_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                         alt="Profile" class="user-table-avatar me-2">
                                <?php else: ?>
                                    <div class="user-table-avatar-placeholder me-2">
                                        <i class='bx bx-user'></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                <?php if ($user['id'] == getCurrentUserId()): ?>
                                    <span class="badge bg-info ms-1">You</span>
                                <?php endif; ?>
                                <?php if ($user['full_name']): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($user['full_name']); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($user['email']): ?>
                                <small><i class='bx bx-envelope'></i> <?php echo htmlspecialchars($user['email']); ?></small><br>
                            <?php endif; ?>
                            <?php if ($user['phone']): ?>
                                <small><i class='bx bx-phone'></i> <?php echo htmlspecialchars($user['phone']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" 
                                    onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                    <?php echo $user['role'] === 'admin' && $user['id'] != getCurrentUserId() ? 'disabled' : ''; ?>>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" 
                                    onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')"
                                    <?php echo $user['role'] === 'admin' || $user['id'] == getCurrentUserId() ? 'disabled' : ''; ?>>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>Add New User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="cashier">Cashier</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="text-muted">Minimum 6 characters recommended</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_role" class="form-label">Role</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="cashier">Cashier</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="edit_full_name" name="full_name">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <small class="text-muted">Leave blank to keep current password</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                    <p class="fw-bold mb-0">User: <span id="delete_username"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.user-table-avatar {
    width: 35px;
    height: 35px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid var(--coffee-primary);
}

.user-table-avatar-placeholder {
    width: 35px;
    height: 35px;
    background: var(--coffee-secondary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
</style>

<script>
function editUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_full_name').value = user.full_name || '';
    document.getElementById('edit_email').value = user.email || '';
    document.getElementById('edit_phone').value = user.phone || '';
    document.getElementById('edit_password').value = '';
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function deleteUser(id, username) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_username').textContent = username;
    
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}
</script>

<?php require_once 'includes/footer.php'; ?>
