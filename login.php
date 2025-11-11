<?php
require_once 'config/session.php';
require_once 'includes/auth.php';

// Optional seeding/reset: /login.php?seed=admin (dev helper)
if (isset($_GET['seed']) && $_GET['seed'] === 'admin') {
	try {
		// Upsert admin with password admin123
		$hash = password_hash('admin123', PASSWORD_DEFAULT);
		global $pdo;
		$pdo->exec("CREATE TABLE IF NOT EXISTS users (
			id INT AUTO_INCREMENT PRIMARY KEY,
			username VARCHAR(50) UNIQUE NOT NULL,
			password_hash VARCHAR(255) NOT NULL,
			role ENUM('admin','cashier') DEFAULT 'cashier',
			full_name VARCHAR(100) NULL,
			email VARCHAR(100) NULL,
			phone VARCHAR(20) NULL,
			profile_image VARCHAR(255) NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		)");
		$stmt = $pdo->prepare("SELECT id FROM users WHERE username='admin'");
		$stmt->execute();
		if ($stmt->fetch()) {
			$stmt = $pdo->prepare("UPDATE users SET password_hash=?, role='admin' WHERE username='admin'");
			$stmt->execute([$hash]);
		} else {
			$stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES ('admin', ?, 'admin')");
			$stmt->execute([$hash]);
		}
		$seed_msg = 'Admin reset complete. Use admin / admin123';
	} catch (Throwable $e) {
		$seed_msg = 'Seed error: ' . $e->getMessage();
	}
}

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if (authenticateUser($username, $password)) {
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Camp Of Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --coffee-primary: #8B4513;
            --coffee-secondary: #6F4E37;
            --coffee-light: #D2691E;
            
            /* Light theme colors */
            --bg-overlay: rgba(255,255,255,0.95);
            --card-bg: #ffffff;
            --text-color: #212529;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --input-bg: #ffffff;
            --input-border: #ced4da;
        }
        
        [data-theme="dark"] {
            /* Dark theme colors */
            --bg-overlay: rgba(30,30,30,0.95);
            --card-bg: #1e1e1e;
            --text-color: #ffffff;
            --text-muted: #b0b0b0;
            --border-color: #444444;
            --input-bg: #2d2d2d;
            --input-border: #555555;
        }
        
        body {
            background: url('assets/images/coc-place2.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color);
            transition: color 0.3s ease;
        }
        .login-container {
            background: var(--bg-overlay);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            border: 1px solid var(--border-color);
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        .login-header {
            background: #6F4E37;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .coffee-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .form-control {
            background-color: var(--input-bg);
            color: var(--text-color);
            border: 1px solid var(--input-border);
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #8B4513;
            box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25);
            background-color: var(--input-bg);
            color: var(--text-color);
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
        }
        .btn-coffee {
            background: #8B4513;
            color: white;
            border: none;
            padding: 0.75rem;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-coffee:hover {
            background: #6F4E37;
            color: white;
            transform: translateY(-2px);
        }
        .logo-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
        
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--coffee-primary);
            color: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .theme-toggle:hover {
            background: var(--coffee-secondary);
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Theme Toggle Button -->
    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle Dark Mode">
        <i class="bi bi-moon-fill" id="theme-icon"></i>
    </button>
    
    <div class="login-container">
        <div class="login-header">
            <?php if (file_exists('assets/images/coc_logo.jpg')): ?>
                <img src="assets/images/coc_logo.jpg" alt="Camp Of Coffee" class="logo-img">
            <?php else: ?>
                <i class="bi bi-cup-hot-fill coffee-icon"></i>
            <?php endif; ?>
           
            <h3 class="mb-0 mt-2">Camp Of Coffee</h3>
        </div>
        <div class="p-4">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($seed_msg)): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <?php echo htmlspecialchars($seed_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-person-fill me-1"></i>Username
                    </label>
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="username" 
                           name="username" 
                           placeholder="Enter your username"
                           required 
                           autofocus>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock-fill me-1"></i>Password
                    </label>
                    <input type="password" 
                           class="form-control form-control-lg" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password"
                           required>
                </div>
                
                <button type="submit" class="btn btn-coffee w-100 btn-lg">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </form>
            
            <hr class="my-4">
            
            <div class="text-center text-muted">
                <small>
                    <i class="bi bi-info-circle me-1"></i>
                    Default admin: admin / admin123
                    <?php // Dev helper link ?>
                  <!--  <br><a href="?seed=admin" class="text-decoration-none">Reset admin (dev)</a> -->
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme toggle functionality
        let currentTheme = localStorage.getItem('theme') || 'light';
        const body = document.body;
        const themeIcon = document.getElementById('theme-icon');
        
        // Initialize theme
        function initializeTheme() {
            if (currentTheme === 'dark') {
                body.setAttribute('data-theme', 'dark');
                themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            } else {
                body.removeAttribute('data-theme');
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-fill');
            }
        }
        
        // Toggle theme
        function toggleTheme() {
            currentTheme = currentTheme === 'light' ? 'dark' : 'light';
            localStorage.setItem('theme', currentTheme);
            
            if (currentTheme === 'dark') {
                body.setAttribute('data-theme', 'dark');
                themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
            } else {
                body.removeAttribute('data-theme');
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-fill');
            }
        }
        
        // Initialize theme on page load
        initializeTheme();
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 300);
            });
        }, 5000);
    </script>
</body>
</html>
