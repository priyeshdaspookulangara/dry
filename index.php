<?php require_once 'db.php'; // Include the database connection ?>
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
                <a href="#" class="text-white text-decoration-none">Login</a>
                <span class="mx-3">|</span>
                <a href="#" class="text-white text-decoration-none">Register</a>
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
                <a href="#" class="action-item d-none d-sm-flex">
                    <i class="fas fa-sign-in-alt action-icon"></i>
                    <span class="action-text">Login</span>
                </a>
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
                            $category_sql = "SELECT id, name, slug FROM categories ORDER BY name ASC";
                            $category_result = mysqli_query($conn, $category_sql);
                            if (mysqli_num_rows($category_result) > 0) {
                                while($row = mysqli_fetch_assoc($category_result)) {
                                    // Note: Proper URL routing would use slugs, e.g., category.php?slug=dry-fruits
                                    echo '<li><a class="dropdown-item" href="category.php?id=' . $row["id"] . '">' . htmlspecialchars($row["name"]) . '</a></li>';
                                }
                            } else {
                                echo '<li><a class="dropdown-item" href="#">No categories found</a></li>';
                            }
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

            <!-- Hero Section -->
            <section class="hero-section">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="hero-content">
                                <h1 class="hero-title">Premium Quality Dry Fruits & Nuts</h1>
                                <p class="hero-subtitle">Discover the finest selection of natural, healthy, and delicious dry fruits and nuts sourced directly from the best farms.</p>
                                <a href="#" class="hero-cta">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Active Offers Bar -->
            <section class="offers-bar py-3">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h5 class="text-white mb-0"><i class="fas fa-fire me-2"></i>Active Offers</h5>
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex flex-wrap gap-3">
                                <div class="coupon-item bg-white rounded px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-bold text-primary">SAVE20</span>
                                    <span class="text-muted">-</span>
                                    <span class="text-success">20% OFF</span>
                                    <button class="btn btn-sm btn-outline-primary copy-coupon" data-code="SAVE20">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="coupon-item bg-white rounded px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-bold text-primary">FIRST10</span>
                                    <span class="text-muted">-</span>
                                    <span class="text-success">$10 OFF</span>
                                    <button class="btn btn-sm btn-outline-primary copy-coupon" data-code="FIRST10">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="coupon-item bg-white rounded px-3 py-2 d-flex align-items-center gap-2">
                                    <span class="fw-bold text-primary">FREESHIP</span>
                                    <span class="text-muted">-</span>
                                    <span class="text-success">FREE SHIPPING</span>
                                    <button class="btn btn-sm btn-outline-primary copy-coupon" data-code="FREESHIP">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Flash Sale Section -->
            <section class="flash-sale py-5">
                <div class="container-fluid">
                    <div class="section-title">
                        <h2><i class="fas fa-bolt text-warning me-2"></i>Flash Sale</h2>
                        <p>Limited time offers - Grab them before they're gone!</p>
                        <div class="countdown-timer">
                            <div class="time-unit">
                                <span class="time-value">12</span>
                                <span class="time-label">Hours</span>
                            </div>
                            <div class="time-unit">
                                <span class="time-value">34</span>
                                <span class="time-label">Minutes</span>
                            </div>
                            <div class="time-unit">
                                <span class="time-value">56</span>
                                <span class="time-label">Seconds</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php
                        $flash_sale_sql = "SELECT id, name, price, original_price, image, rating, rating_count FROM products WHERE is_flash_sale = 1 LIMIT 6"; // Limit to 6 flash sale items
                        $flash_sale_result = mysqli_query($conn, $flash_sale_sql);

                        if (mysqli_num_rows($flash_sale_result) > 0) {
                            while($product = mysqli_fetch_assoc($flash_sale_result)) {
                        ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="product-badge">SALE</div>
                                </div>
                                <div class="product-info">
                                    <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <div class="product-price">
                                        <span class="current-price">$<?php echo htmlspecialchars($product['price']); ?></span>
                                        <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                        <span class="original-price">$<?php echo htmlspecialchars($product['original_price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-rating">
                                        <div class="stars">
                                            <?php
                                            $rating = floatval($product['rating']);
                                            for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php if ($i > $rating) echo ' text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="rating-text">(<?php echo htmlspecialchars($product['rating_count']); ?>)</span>
                                    </div>
                                    <div class="product-actions">
                                        <button class="btn-add-cart">
                                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                        </button>
                                        <button class="btn-quick-view">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<p class="text-center">No flash sale products available at the moment.</p>';
                        }
                        ?>
                    </div>
                </div>
            </section>

            <!-- Featured Products Section -->
            <section class="featured-products py-5">
                <div class="container-fluid">
                    <div class="section-title">
                        <h2>Featured Products</h2>
                        <p>Handpicked premium products just for you</p>
                    </div>

                    <div class="row">
                        <?php
                        $featured_sql = "SELECT id, name, price, original_price, image, rating, rating_count FROM products WHERE is_featured = 1 LIMIT 4"; // Limit to 4 featured products
                        $featured_result = mysqli_query($conn, $featured_sql);

                        if (mysqli_num_rows($featured_result) > 0) {
                            while($product = mysqli_fetch_assoc($featured_result)) {
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="product-badge">FEATURED</div>
                                </div>
                                <div class="product-info">
                                    <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <div class="product-price">
                                        <span class="current-price">$<?php echo htmlspecialchars($product['price']); ?></span>
                                         <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                        <span class="original-price">$<?php echo htmlspecialchars($product['original_price']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-rating">
                                        <div class="stars">
                                            <?php
                                            $rating = floatval($product['rating']);
                                            for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php if ($i > $rating) echo ' text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="rating-text">(<?php echo htmlspecialchars($product['rating_count']); ?>)</span>
                                    </div>
                                    <div class="product-actions">
                                        <button class="btn-add-cart">
                                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                        </button>
                                        <button class="btn-quick-view">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<p class="text-center">No featured products available at the moment.</p>';
                        }
                        ?>
                    </div>

                    <div class="text-center mt-4">
                        <a href="#" class="hero-cta">View All Featured Products</a>
                    </div>
                </div>
            </section>

            <!-- Value Proposition Section -->
            <section class="value-prop">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
                            <div class="value-item">
                                <i class="fas fa-shipping-fast value-icon"></i>
                                <h4 class="value-title">Free Shipping</h4>
                                <p class="value-text">Free delivery on orders over $50. Fast and reliable shipping nationwide.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
                            <div class="value-item">
                                <i class="fas fa-medal value-icon"></i>
                                <h4 class="value-title">Premium Quality</h4>
                                <p class="value-text">Handpicked products from the finest farms. Quality guaranteed or money back.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
                            <div class="value-item">
                                <i class="fas fa-headset value-icon"></i>
                                <h4 class="value-title">24/7 Support</h4>
                                <p class="value-text">Round-the-clock customer support. We're here to help whenever you need us.</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4 mb-md-0">
                            <div class="value-item">
                                <i class="fas fa-shield-alt value-icon"></i>
                                <h4 class="value-title">Secure Payment</h4>
                                <p class="value-text">Your payment information is safe with our secure checkout process.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Categories Section -->
            <section class="categories py-5">
                <div class="container-fluid">
                    <div class="section-title">
                        <h2>Shop by Category</h2>
                        <p>Explore our wide range of premium products</p>
                    </div>

                    <div class="row">
                        <?php
                        // Re-fetch categories for this section or use the previous result if it's still in scope and not closed
                        // For simplicity, let's re-fetch. In a more complex app, you might store this in a variable.
                        $category_section_sql = "SELECT id, name, slug, description FROM categories ORDER BY name ASC LIMIT 4"; // Displaying 4 categories here
                        $category_section_result = mysqli_query($conn, $category_section_sql);
                        if (mysqli_num_rows($category_section_result) > 0) {
                            while($cat = mysqli_fetch_assoc($category_section_result)) {
                        ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-6 mb-4">
                            <a href="category.php?id=<?php echo $cat['id']; ?>" class="text-decoration-none">
                                <div class="category-card text-center p-3 p-md-4 h-100 border rounded">
                                    <!-- Using a generic icon for now, ideally categories could have associated icons -->
                                    <i class="fas fa-tags fa-3x text-primary mb-3"></i>
                                    <h5 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h5>
                                    <p class="text-muted small d-none d-md-block"><?php echo htmlspecialchars(substr($cat['description'], 0, 50)) . '...'; ?></p>
                                </div>
                            </a>
                        </div>
                        <?php
                            }
                        } else {
                            echo '<p class="text-center">No categories to display.</p>';
                        }
                        ?>
                    </div>
                </div>
            </section>

            <!-- Customer Testimonials Section -->
            <section class="testimonials py-5">
                <div class="container-fluid">
                    <div class="section-title">
                        <h2>What Our Customers Say</h2>
                        <p>Real reviews from satisfied customers</p>
                    </div>

                    <div class="row">
                        <!-- Static testimonials as per original HTML -->
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card bg-white p-4 rounded shadow-sm h-100">
                                <div class="stars mb-3">
                                    <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                                </div>
                                <p class="testimonial-text">"Excellent quality products! The almonds were fresh and perfectly roasted. Will definitely order again."</p>
                                <div class="testimonial-author">
                                    <strong>Sarah Johnson</strong>
                                    <small class="text-muted d-block">Verified Customer</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card bg-white p-4 rounded shadow-sm h-100">
                                <div class="stars mb-3">
                                    <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                                </div>
                                <p class="testimonial-text">"Fast shipping and great packaging. The mixed nuts were exactly what I was looking for. Highly recommended!"</p>
                                <div class="testimonial-author">
                                    <strong>Mike Chen</strong>
                                    <small class="text-muted d-block">Verified Customer</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="testimonial-card bg-white p-4 rounded shadow-sm h-100">
                                <div class="stars mb-3">
                                    <i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i><i class="fas fa-star text-warning"></i>
                                </div>
                                <p class="testimonial-text">"Best quality dry fruits I've ever purchased online. The dates were incredibly sweet and fresh."</p>
                                <div class="testimonial-author">
                                    <strong>Emily Davis</strong>
                                    <small class="text-muted d-block">Verified Customer</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="#" class="hero-cta">Read More Reviews</a>
                    </div>
                </div>
            </section>

            <!-- Newsletter Section -->
            <section class="newsletter">
                <div class="container-fluid">
                    <h3>Stay Updated with Our Latest Offers</h3>
                    <p>Subscribe to our newsletter and get exclusive deals, new product updates, and health tips delivered to your inbox.</p>
                    <form class="newsletter-form" id="newsletterForm">
                        <input type="email" class="newsletter-input" placeholder="Enter your email address" required>
                        <button type="submit" class="newsletter-btn">Subscribe Now</button>
                    </form>
                </div>
            </section>
        </div> <!-- End .container-fluid for main content -->
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 footer-section">
                    <h4 class="footer-title">BN Dry Fruits & Nuts</h4>
                    <p>Your trusted source for premium quality dry fruits, nuts, and spices. We bring you the finest products directly from farms to your kitchen.</p>
                    <div class="social-links">
                        <a href="#" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link linkedin"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-md-2 footer-section">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#">Shop Now</a></li>
                        <li><a href="#">Offers</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>

                <div class="col-md-3 footer-section">
                    <h4 class="footer-title">Categories</h4>
                    <ul class="footer-links">
                        <?php
                        // Re-fetch categories for footer or use a stored variable
                        $footer_category_sql = "SELECT id, name, slug FROM categories ORDER BY name ASC LIMIT 4";
                        $footer_category_result = mysqli_query($conn, $footer_category_sql);
                        if (mysqli_num_rows($footer_category_result) > 0) {
                            while($row = mysqli_fetch_assoc($footer_category_result)) {
                                echo '<li><a href="category.php?id=' . $row["id"] . '">' . htmlspecialchars($row["name"]) . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="col-md-3 footer-section">
                    <h4 class="footer-title">Contact Info</h4>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Main Street, City, State 12345</li>
                        <li><i class="fas fa-phone-alt me-2"></i> +1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope me-2"></i> info@bndryfruits.com</li>
                        <li><i class="fas fa-clock me-2"></i> Mon - Sat: 9:00 AM - 6:00 PM</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; <?php echo date("Y"); ?> BN Dry Fruits & Nuts. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="payment-methods">
                            <i class="fab fa-cc-visa payment-icon"></i>
                            <i class="fab fa-cc-mastercard payment-icon"></i>
                            <i class="fab fa-cc-paypal payment-icon"></i>
                            <i class="fab fa-cc-stripe payment-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
    // Copy coupon code functionality
    document.querySelectorAll('.copy-coupon').forEach(button => {
        button.addEventListener('click', function() {
            const code = this.dataset.code;
            navigator.clipboard.writeText(code).then(() => {
                this.innerHTML = '<i class="fas fa-check"></i> Copied';
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-copy"></i>';
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
                // Fallback for older browsers or if clipboard API fails
                alert("Failed to copy. Please copy manually: " + code);
            });
        });
    });

    // Newsletter form
    document.getElementById('newsletterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Basic validation
        const emailInput = this.querySelector('.newsletter-input');
        if (emailInput.value.trim() === '' || !emailInput.checkValidity()) {
            alert('Please enter a valid email address.');
            emailInput.focus();
            return;
        }
        alert('Thank you for subscribing to our newsletter!');
        this.reset();
    });

    // Add to cart functionality (placeholder)
    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', function() {
            alert('Product added to cart! (Placeholder)');
            // In a real app, you'd handle this with AJAX or form submission
        });
    });

    // Quick view functionality (placeholder)
    document.querySelectorAll('.btn-quick-view').forEach(button => {
        button.addEventListener('click', function() {
            alert('Quick view for product! (Placeholder)');
            // In a real app, this would likely open a modal with product details
        });
    });

    // Countdown Timer Logic (Simple static example, would need to be dynamic)
    // This is just to make it look like it's working.
    // For a real countdown, you'd set a target date and calculate remaining time.
    const countdownTimer = document.querySelector('.countdown-timer');
    if (countdownTimer) {
        const hoursVal = countdownTimer.querySelector('.time-unit:nth-child(1) .time-value');
        const minutesVal = countdownTimer.querySelector('.time-unit:nth-child(2) .time-value');
        const secondsVal = countdownTimer.querySelector('.time-unit:nth-child(3) .time-value');

        if(hoursVal && minutesVal && secondsVal) {
            let hours = parseInt(hoursVal.textContent);
            let minutes = parseInt(minutesVal.textContent);
            let seconds = parseInt(secondsVal.textContent);

            setInterval(() => {
                seconds--;
                if (seconds < 0) {
                    seconds = 59;
                    minutes--;
                }
                if (minutes < 0) {
                    minutes = 59;
                    hours--;
                }
                if (hours < 0) { // Timer ended
                    hours = 0; minutes = 0; seconds = 0;
                    // Optionally hide timer or show "Sale Ended"
                }

                hoursVal.textContent = String(hours).padStart(2, '0');
                minutesVal.textContent = String(minutes).padStart(2, '0');
                secondsVal.textContent = String(seconds).padStart(2, '0');

            }, 1000);
        }
    }
    </script>
</body>
</html>
<?php mysqli_close($conn); // Close the database connection ?>
