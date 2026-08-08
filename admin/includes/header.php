<?php
require_once __DIR__ . '/../config.php';
session_start();
// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    if (basename($_SERVER['PHP_SELF']) != 'login.php') { // Avoid redirect loop
        header("Location: login.php");
        exit;
    }
}

// Basic navigation for logged-in users
$is_login_page = basename($_SERVER['PHP_SELF']) == 'login.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - BN Dry Fruits</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { display: flex; min-height: 100vh; flex-direction: column; }
        .admin-wrapper { display: flex; flex-grow: 1; }
        .sidebar { width: 250px; background-color: #343a40; color: #fff; padding-top: 1rem; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 0.75rem 1.5rem; }
        .sidebar a:hover, .sidebar a.active { color: #fff; background-color: #495057; }
        .content { flex-grow: 1; padding: 2rem; }
        .navbar-admin { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; }
    </style>
</head>
<body>

<?php if (!$is_login_page && isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
<nav class="navbar navbar-expand-lg navbar-light navbar-admin">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">BN Dry Fruits Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <span class="navbar-text me-3">
                        Welcome, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-danger" href="logout.php">Logout <i class="fas fa-sign-out-alt"></i></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="admin-wrapper">
    <?php if (!$is_login_page && isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
    <nav class="sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_BASE_URL; ?>dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['REQUEST_URI'], '/admin/offers/') !== false ? 'active' : ''; ?>" href="<?php echo ADMIN_BASE_URL; ?>offers/index.php">
                        <i class="fas fa-tags me-2"></i> Manage Offers
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/categories/') !== false ? 'active' : ''; ?>" href="<?php echo ADMIN_BASE_URL; ?>categories/index.php">
                        <i class="fas fa-sitemap me-2"></i> Manage Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], '/admin/products/') !== false ? 'active' : ''; ?>" href="<?php echo ADMIN_BASE_URL; ?>products/index.php">
                        <i class="fas fa-box-open me-2"></i> Manage Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="<?php echo ADMIN_BASE_URL; ?>settings.php">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </li>
                <!-- Add other management links here e.g., Orders -->
                 <li class="nav-item">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-store me-2"></i> View Storefront
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>
    <main role="main" class="content <?php echo $is_login_page ? 'w-100' : ''; ?>">
        <!-- Content of specific admin page will go here -->
<?php // Do not close body/html here, footer will do it ?>
