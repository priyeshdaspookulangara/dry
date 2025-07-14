<!-- Page specific content ends here -->
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
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="terms.php">Terms & Conditions</a></li>
                    </ul>
                </div>

                <div class="col-md-3 footer-section">
                    <h4 class="footer-title">Categories</h4>
                    <ul class="footer-links">
                        <?php
                        // Note: $conn should be available from the header include.
                        // To be safe, we could re-check or re-include db.php if needed,
                        // but for now, we assume it's open.
                        if ($conn) {
                            $footer_category_sql = "SELECT id, name, slug FROM categories ORDER BY name ASC LIMIT 4";
                            $footer_category_result = mysqli_query($conn, $footer_category_sql);
                            if ($footer_category_result && mysqli_num_rows($footer_category_result) > 0) {
                                while($row_footer = mysqli_fetch_assoc($footer_category_result)) {
                                    echo '<li><a href="category.php?id=' . $row_footer["id"] . '">' . htmlspecialchars($row_footer["name"]) . '</a></li>';
                                }
                                mysqli_free_result($footer_category_result);
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

    <!-- This script section should be moved to a page-specific JS file or kept here for global scripts -->
    <script>
    // This script block should ideally be in a separate file, e.g., assets/js/main.js
    document.addEventListener('DOMContentLoaded', function() {
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
                    alert("Failed to copy. Please copy manually: " + code);
                });
            });
        });

        // Newsletter form
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const emailInput = this.querySelector('.newsletter-input');
                if (emailInput.value.trim() === '' || !emailInput.checkValidity()) {
                    alert('Please enter a valid email address.');
                    emailInput.focus();
                    return;
                }
                alert('Thank you for subscribing to our newsletter!');
                this.reset();
            });
        }

        // Add to cart functionality (placeholder)
        document.querySelectorAll('.btn-add-cart').forEach(button => {
            button.addEventListener('click', function() {
                alert('Product added to cart! (Placeholder)');
            });
        });

        // Quick view functionality (placeholder)
        document.querySelectorAll('.btn-quick-view').forEach(button => {
            button.addEventListener('click', function() {
                alert('Quick view for product! (Placeholder)');
            });
        });
    });
    </script>
</body>
</html>
<?php
// Close the database connection that was opened in the header
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>
