<?php require_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section" style="background-image: url('<?php
    $sql = "SELECT setting_value FROM settings WHERE setting_key = 'banner_image'";
    $result = mysqli_query($conn, $sql);
    $banner_image = mysqli_fetch_assoc($result)['setting_value'];
    echo !empty($banner_image) ? htmlspecialchars($banner_image) : 'assets/images/default-banner.jpg';
?>');">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">Premium Quality Dry Fruits & Nuts</h1>
                    <p class="hero-subtitle">Discover the finest selection of natural, healthy, and delicious dry fruits and nuts sourced directly from the best farms.</p>
                    <a href="index.php" class="hero-cta">Shop Now</a>
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
            // Note: $conn is available from includes/header.php
            $flash_sale_sql = "SELECT id, name, price, original_price, image, rating, rating_count, stock_quantity FROM products WHERE is_flash_sale = 1 LIMIT 6";
            $flash_sale_result = mysqli_query($conn, $flash_sale_sql);

            if ($flash_sale_result && mysqli_num_rows($flash_sale_result) > 0) {
                while($product = mysqli_fetch_assoc($flash_sale_result)) {
            ?>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                <div class="product-card h-100">
                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="product-badge">SALE</div>
                        </div>
                    </a>
                    <div class="product-info">
                        <h5 class="product-title">
                             <a href="product_details.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h5>
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
                            <form action="cart_actions.php" method="POST" class="d-inline">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-add-cart" <?php if ($product['stock_quantity'] <= 0) echo 'disabled'; ?>>
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            </form>
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-quick-view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
                mysqli_free_result($flash_sale_result);
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
            $featured_sql = "SELECT id, name, price, original_price, image, rating, rating_count, stock_quantity FROM products WHERE is_featured = 1 LIMIT 4";
            $featured_result = mysqli_query($conn, $featured_sql);

            if ($featured_result && mysqli_num_rows($featured_result) > 0) {
                while($product = mysqli_fetch_assoc($featured_result)) {
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="product-card h-100">
                     <a href="product_details.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="product-badge">FEATURED</div>
                        </div>
                    </a>
                    <div class="product-info">
                        <h5 class="product-title">
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h5>
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
                            <form action="cart_actions.php" method="POST" class="d-inline">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-add-cart" <?php if ($product['stock_quantity'] <= 0) echo 'disabled'; ?>>
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            </form>
                            <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-quick-view" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
                mysqli_free_result($featured_result);
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
            $category_section_sql = "SELECT id, name, slug, description FROM categories ORDER BY name ASC LIMIT 4";
            $category_section_result = mysqli_query($conn, $category_section_sql);
            if ($category_section_result && mysqli_num_rows($category_section_result) > 0) {
                while($cat = mysqli_fetch_assoc($category_section_result)) {
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 col-6 mb-4">
                <a href="category.php?id=<?php echo $cat['id']; ?>" class="text-decoration-none">
                    <div class="category-card text-center p-3 p-md-4 h-100 border rounded">
                        <i class="fas fa-tags fa-3x text-primary mb-3"></i>
                        <h5 class="category-name"><?php echo htmlspecialchars($cat['name']); ?></h5>
                        <p class="text-muted small d-none d-md-block"><?php echo htmlspecialchars(substr($cat['description'], 0, 50)) . '...'; ?></p>
                    </div>
                </a>
            </div>
            <?php
                }
                mysqli_free_result($category_section_result);
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

<script>
// This is for the countdown timer on the index page
// A more robust solution would be to place this in a separate JS file.
document.addEventListener('DOMContentLoaded', function() {
    const countdownTimer = document.querySelector('.countdown-timer');
    if (countdownTimer) {
        const hoursVal = countdownTimer.querySelector('.time-unit:nth-child(1) .time-value');
        const minutesVal = countdownTimer.querySelector('.time-unit:nth-child(2) .time-value');
        const secondsVal = countdownTimer.querySelector('.time-unit:nth-child(3) .time-value');

        if(hoursVal && minutesVal && secondsVal) {
            let totalSeconds = (parseInt(hoursVal.textContent) * 3600) +
                               (parseInt(minutesVal.textContent) * 60) +
                               parseInt(secondsVal.textContent);

            const timerInterval = setInterval(() => {
                if (totalSeconds <= 0) {
                    clearInterval(timerInterval);
                    // Optionally hide timer or show "Sale Ended"
                    return;
                }

                totalSeconds--;

                let hours = Math.floor(totalSeconds / 3600);
                let minutes = Math.floor((totalSeconds % 3600) / 60);
                let seconds = totalSeconds % 60;

                hoursVal.textContent = String(hours).padStart(2, '0');
                minutesVal.textContent = String(minutes).padStart(2, '0');
                secondsVal.textContent = String(seconds).padStart(2, '0');

            }, 1000);
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
