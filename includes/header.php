<?php
session_start(); // Start session on every page
require_once 'db.php'; // Include the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BN Dry Fruits & Nuts - Premium Quality Natural Products</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header Top -->
    <div class="header-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="contact-info">
                <i class="fas fa-phone-alt me-2"></i> +1 (555) 123-4567
                <span class="mx-3">|</span>
                <i class="fas fa-envelope me-2"></i> info@bndryfruits.com
            </div>
            <div class="top-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="account.php" class="text-white text-decoration-none">My Account</a>
                    <span class="mx-3">|</span>
                    <a href="logout.php" class="text-white text-decoration-none">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-white text-decoration-none">Login</a>
                    <span class="mx-3">|</span>
                    <a href="register.php" class="text-white text-decoration-none">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header py-3">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <!-- Logo Section -->
            <div class="logo-section">
                <a href="index.php">
                    <img src="admin/uploads/1751025979_spicesnnuts.jpg" alt="BN Dry Fruits & Nuts Logo" class="logo-img">
                </a>
                <a href="index.php" class="text-decoration-none">
                    <h1 class="brand-name mb-0">BN Dry Fruits & Nuts</h1>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="search-bar flex-grow-1 mx-md-4">
                <form action="#" method="GET" class="position-relative">
                    <input type="text" name="q" class="form-control search-input" placeholder="Search for products...">
                    <button type="submit" class="btn search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Header Actions -->
            <div class="header-actions d-flex align-items-center">
                <a href="#" class="action-item d-none d-sm-flex">
                    <i class="far fa-heart action-icon"></i>
                    <span class="action-text">Wishlist</span>
                </a>
                <a href="#" class="action-item">
                    <i class="fas fa-shopping-cart action-icon"></i>
                    <span class="action-text">Cart</span>
                    <span class="cart-badge">0</span> <!-- Static for now -->
                </a>
                 <?php if (isset($_SESSION['user_id'])): ?>
                     <a href="account.php" class="action-item d-none d-sm-flex">
                        <i class="fas fa-user-circle action-icon"></i>
                        <span class="action-text">My Account</span>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="action-item d-none d-sm-flex">
                        <i class="fas fa-sign-in-alt action-icon"></i>
                        <span class="action-text">Login</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Navigation / Mega Menu -->
    <nav class="navbar navbar-expand-lg navbar-light py-0">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="mega-menu-item nav-link active" href="index.php">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="mega-menu-item nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-list"></i> Categories
                        </a>
                        <ul class="dropdown-menu border-0 shadow-sm" aria-labelledby="categoriesDropdown">
                            <li><a class="dropdown-item" href="#">All Products</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php
                            // Note: $conn is available from the require_once 'db.php' at the top.
                            $category_sql_header = "SELECT id, name, slug FROM categories ORDER BY name ASC";
                            $category_result_header = mysqli_query($conn, $category_sql_header);
                            if ($category_result_header && mysqli_num_rows($category_result_header) > 0) {
                                while($row_header = mysqli_fetch_assoc($category_result_header)) {
                                    // Note: Proper URL routing would use slugs, e.g., category.php?slug=dry-fruits
                                    echo '<li><a class="dropdown-item" href="category.php?id=' . $row_header["id"] . '">' . htmlspecialchars($row_header["name"]) . '</a></li>';
                                }
                            } else {
                                echo '<li><a class="dropdown-item" href="#">No categories found</a></li>';
                            }
                            if($category_result_header) mysqli_free_result($category_result_header);
                            ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="mega-menu-item nav-link" href="#">
                            <i class="fas fa-tags"></i> Offers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="mega-menu-item nav-link" href="#">
                            <i class="fas fa-star"></i> Featured
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="mega-menu-item nav-link" href="#">
                            <i class="fas fa-info-circle"></i> About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="mega-menu-item nav-link" href="#">
                            <i class="fas fa-headset"></i> Contact Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="mega-menu-item nav-link" href="#">
                            <i class="fas fa-comments"></i> Testimonials
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="py-4">
        <div class="container-fluid">
            <!-- Page specific content starts here -->
