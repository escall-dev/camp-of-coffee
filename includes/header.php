<?php
require_once 'config/session.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Camp Of Coffee'; ?> - Sales & Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --coffee-primary: #8B4513;
            --coffee-secondary: #6F4E37;
            --coffee-light: #D2691E;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            
            /* Light theme colors */
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #212529;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --shadow: rgba(0,0,0,0.08);
            --shadow-hover: rgba(0,0,0,0.12);
            --table-hover: rgba(139, 69, 19, 0.05);
        }
        
        [data-theme="dark"] {
            /* Dark theme colors */
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #ffffff;
            --text-muted: #b0b0b0;
            --border-color: #333333;
            --shadow: rgba(255,255,255,0.05);
            --shadow-hover: rgba(255,255,255,0.1);
            --table-hover: rgba(139, 69, 19, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--coffee-secondary);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid white;
        }
        
        .logo-text {
            font-size: 18px;
            font-weight: bold;
            opacity: 1;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        
        .sidebar.collapsed .logo-container {
            justify-content: center;
        }
        
        /* Toggle Button */
        .toggle-btn {
            position: absolute;
            top: 20px;
            right: -15px;
            background: var(--coffee-primary);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .toggle-btn:hover {
            background: var(--coffee-light);
            transform: scale(1.1);
        }
        
        /* Navigation Menu */
        .nav-menu {
            padding: 20px 0;
        }
        
        .nav-item {
            margin: 5px 0;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: var(--coffee-primary);
            color: white;
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: white;
        }
        
        .nav-icon {
            font-size: 20px;
            min-width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .nav-text {
            margin-left: 15px;
            font-weight: 500;
            opacity: 1;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            margin-left: 0;
            overflow: hidden;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 15px 0;
        }
        
        /* User Profile */
        .user-profile {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding: 20px;
            z-index: 1050;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            gap: 15px;
            cursor: pointer;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .user-info:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--coffee-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            overflow: hidden;
        }
        
        .user-avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .user-details {
            flex: 1;
            opacity: 1;
            transition: all 0.3s ease;
        }
        
        .sidebar.collapsed .user-details {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-role {
            font-size: 12px;
            opacity: 0.8;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
            
            .mobile-toggle {
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1002;
                background: var(--coffee-primary);
                color: white;
                border: none;
                width: 40px;
                height: 40px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
            }
        }
        
        /* Card and other styles */
        .btn-primary {
            background-color: var(--coffee-primary);
            border-color: var(--coffee-primary);
        }
        
        .btn-primary:hover {
            background-color: var(--coffee-secondary);
            border-color: var(--coffee-secondary);
        }
        
        .card {
            border: none;
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px var(--shadow);
            transition: all 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 4px 8px var(--shadow-hover);
        }
        
        .stat-card {
            border-left: 4px solid var(--coffee-primary);
        }
        
        .table-hover tbody tr:hover {
            background-color: var(--table-hover);
        }
        
        .table {
            color: var(--text-color);
        }
        
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        .badge-success {
            background-color: #28a745;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-danger {
            background-color: #dc3545;
        }
        
        /* User Dropdown */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,.1);
        }
        
        .user-dropdown {
            position: relative;
        }
        
        .user-dropdown .dropdown-menu {
            position: absolute;
            bottom: 100%;
            left: 0;
            right: 0;
            margin-bottom: 10px;
            transform: translateY(-10px);
        }
        
        /* Fix dropdown positioning for sidebar */
        .sidebar .user-dropdown.dropup .dropdown-menu {
            margin-bottom: 10px;
            box-shadow: 0 -4px 6px rgba(0,0,0,.1);
            z-index: 1060;
            min-width: 200px;
        }
        
        .sidebar.collapsed .user-dropdown.dropup .dropdown-menu {
            min-width: 150px;
        }
        
        /* Modal and Form Elements Dark Mode Support */
        .modal-content {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }
        
        .modal-footer {
            border-top: 1px solid var(--border-color);
        }
        
        .form-control, .form-select {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--card-bg);
            color: var(--text-color);
            border-color: var(--coffee-primary);
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
        }
        
        .dropdown-menu {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
        }
        
        .dropdown-item {
            color: var(--text-color);
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: var(--table-hover);
            color: var(--text-color);
        }
        
        .alert {
            border: 1px solid var(--border-color);
        }
        
        .list-group-item {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        /* DataTables Dark Mode Support - Force Override */
        [data-theme="dark"] .dataTables_wrapper,
        [data-theme="dark"] .dataTables_wrapper * {
            color: var(--text-color) !important;
        }
        
        [data-theme="dark"] .card .table-responsive,
        [data-theme="dark"] .table-responsive {
            background-color: transparent !important;
        }
        
        [data-theme="dark"] .dataTables_length,
        [data-theme="dark"] .dataTables_filter,
        [data-theme="dark"] .dataTables_info,
        [data-theme="dark"] .dataTables_paginate {
            color: var(--text-color);
        }
        
        [data-theme="dark"] .dataTables_length select,
        [data-theme="dark"] .dataTables_filter input {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }
        
        [data-theme="dark"] .paginate_button {
            background-color: var(--card-bg) !important;
            color: var(--text-color) !important;
            border: 1px solid var(--border-color) !important;
        }
        
        [data-theme="dark"] .paginate_button:hover {
            background-color: var(--table-hover) !important;
            color: var(--text-color) !important;
        }
        
        [data-theme="dark"] .paginate_button.current {
            background-color: var(--coffee-primary) !important;
            color: white !important;
        }
        
        [data-theme="dark"] .dataTables_wrapper .dataTables_length,
        [data-theme="dark"] .dataTables_wrapper .dataTables_filter,
        [data-theme="dark"] .dataTables_wrapper .dataTables_info,
        [data-theme="dark"] .dataTables_wrapper .dataTables_paginate {
            color: var(--text-color);
        }
        
        [data-theme="dark"] .dataTables_wrapper .dataTables_length label,
        [data-theme="dark"] .dataTables_wrapper .dataTables_filter label {
            color: var(--text-color);
        }
        
        [data-theme="dark"] table.dataTable,
        [data-theme="dark"] table.dataTable thead,
        [data-theme="dark"] table.dataTable thead th,
        [data-theme="dark"] table.dataTable tbody,
        [data-theme="dark"] table.dataTable tbody tr,
        [data-theme="dark"] table.dataTable tbody td,
        [data-theme="dark"] .table,
        [data-theme="dark"] .table thead,
        [data-theme="dark"] .table thead th,
        [data-theme="dark"] .table tbody,
        [data-theme="dark"] .table tbody tr,
        [data-theme="dark"] .table tbody td {
            background-color: transparent !important;
            background: transparent !important;
            color: var(--text-color) !important;
            border-color: var(--border-color) !important;
        }
        
        [data-theme="dark"] table.dataTable thead th {
            border-bottom: 2px solid var(--border-color) !important;
            background-color: rgba(139, 69, 19, 0.1) !important;
        }
        
        [data-theme="dark"] table.dataTable tbody tr {
            background-color: transparent !important;
        }
        
        [data-theme="dark"] table.dataTable tbody tr:hover,
        [data-theme="dark"] table.dataTable tbody tr:hover td {
            background-color: var(--table-hover) !important;
        }
        
        [data-theme="dark"] table.dataTable tbody tr:nth-child(even) {
            background-color: rgba(255,255,255,0.02) !important;
        }
        
        [data-theme="dark"] table.dataTable tbody tr:nth-child(odd) {
            background-color: transparent !important;
        }
        
        [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-color) !important;
        }
        
        [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--table-hover) !important;
            color: var(--text-color) !important;
        }
        
        [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--coffee-primary) !important;
            border: 1px solid var(--coffee-primary) !important;
            color: white !important;
        }
        
        /* Make sure cards blend with dark background */
        [data-theme="dark"] .card {
            background-color: rgba(30, 30, 30, 0.8) !important;
            border: 1px solid var(--border-color) !important;
        }
        
        /* Fix pagination wrapper background */
        [data-theme="dark"] .dataTables_wrapper .dataTables_paginate {
            background-color: transparent !important;
        }
        
        /* Fix search and length controls background */
        [data-theme="dark"] .dataTables_wrapper .dataTables_length,
        [data-theme="dark"] .dataTables_wrapper .dataTables_filter {
            background-color: transparent !important;
        }
        
        /* Force Bootstrap table dark styling */
        [data-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > td,
        [data-theme="dark"] .table-striped > tbody > tr:nth-of-type(odd) > th {
            background-color: rgba(255,255,255,0.02) !important;
        }
        
        [data-theme="dark"] .table-striped > tbody > tr:nth-of-type(even) > td,
        [data-theme="dark"] .table-striped > tbody > tr:nth-of-type(even) > th {
            background-color: transparent !important;
        }
        
        [data-theme="dark"] .table-hover > tbody > tr:hover > td,
        [data-theme="dark"] .table-hover > tbody > tr:hover > th {
            background-color: var(--table-hover) !important;
        }
        
        /* Override any remaining white backgrounds */
        [data-theme="dark"] .table thead th,
        [data-theme="dark"] .table tbody td,
        [data-theme="dark"] .table tfoot td {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Special handling for regular Bootstrap tables (like My Activity) */
        [data-theme="dark"] .card .table,
        [data-theme="dark"] .table-responsive .table {
            background-color: transparent !important;
            color: var(--text-color) !important;
        }
        
        [data-theme="dark"] .card .table thead th {
            background-color: rgba(139, 69, 19, 0.1) !important;
            color: var(--text-color) !important;
            border-bottom: 2px solid var(--border-color) !important;
        }
        
        [data-theme="dark"] .card .table tbody tr,
        [data-theme="dark"] .card .table tbody td {
            background-color: transparent !important;
            color: var(--text-color) !important;
            border-color: var(--border-color) !important;
        }
        
        [data-theme="dark"] .card .table tbody tr:hover {
            background-color: var(--table-hover) !important;
        }
        
        @media (min-width: 769px) {
            .mobile-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Toggle Button -->
    <button class="mobile-toggle d-md-none" onclick="toggleSidebar()">
        <i class='bx bx-menu'></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn d-none d-md-block" onclick="toggleSidebar()">
            <i class='bx bx-menu' id="toggle-icon"></i>
        </button>
        
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo-container">
                <img src="assets/images/coc_logo.jpg" alt="Camp Of Coffee" class="logo-img">
                <span class="logo-text">Camp Of Coffee</span>
            </a>
        </div>
        
        <nav class="nav-menu">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class='bx bx-tachometer nav-icon'></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="products.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                    <i class='bx bx-package nav-icon'></i>
                    <span class="nav-text">Products</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="sales.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : ''; ?>">
                    <i class='bx bx-cart-alt nav-icon'></i>
                    <span class="nav-text">Sales</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="reports.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                    <i class='bx bx-bar-chart-alt-2 nav-icon'></i>
                    <span class="nav-text">Reports</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="my_activity.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'my_activity.php' ? 'active' : ''; ?>">
                    <i class='bx bx-time-five nav-icon'></i>
                    <span class="nav-text">My Activity</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link" onclick="toggleTheme(); return false;">
                    <i class='bx bx-moon nav-icon' id="theme-icon"></i>
                    <span class="nav-text">Dark Mode</span>
                </a>
            </div>
            <?php if (isAdmin()): ?>
            <div class="nav-item">
                <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    <i class='bx bx-user-circle nav-icon'></i>
                    <span class="nav-text">Users</span>
                </a>
            </div>
            <?php endif; ?>
        </nav>
        
        <div class="user-profile">
            <div class="user-dropdown dropup">
                <div class="user-info" data-bs-toggle="dropdown" data-bs-placement="top">
                    <div class="user-avatar">
                        <?php
                        // Get user profile data for avatar
                        require_once 'includes/profile.php';
                        $currentUser = getUserProfile(getCurrentUserId());
                        
                        if ($currentUser && $currentUser['profile_image'] && file_exists($currentUser['profile_image'])):
                        ?>
                            <img src="<?php echo htmlspecialchars($currentUser['profile_image']); ?>" 
                                 alt="Profile" class="user-avatar-img">
                        <?php else: ?>
                            <i class='bx bx-user'></i>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name">
                            <?php echo htmlspecialchars($currentUser['full_name'] ?: getCurrentUsername()); ?>
                        </div>
                        <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
                    </div>
                </div>
                <ul class="dropdown-menu">
                    <li><h6 class="dropdown-header">Account Settings</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="profile.php">
                            <i class='bx bx-user-circle me-2'></i>My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="my_activity.php">
                            <i class='bx bx-list-ul me-2'></i>My Activity
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="logout.php">
                            <i class='bx bx-log-out me-2'></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="main-content">
