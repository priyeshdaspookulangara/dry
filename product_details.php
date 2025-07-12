<?php
require_once 'includes/header.php'; // Includes session_start(), db connection, and common header

// --- Validate Product ID ---
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $product = null;
} else {
    $product_id = $_GET['id'];

    // --- Fetch Product Details with Category Name ---
    $sql = "SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
    } else {
        $product = null;
    }
}
?>

<div class="container-fluid py-5">
    <?php if ($product): ?>
        <div class="row">
            <!-- Product Image Gallery -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <!-- Thumbnails for a gallery could go here -->
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="category.php?id=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
                    </ol>
                </nav>

                <h1 class="display-5"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="d-flex align-items-center mb-3">
                    <div class="stars me-2">
                        <?php
                        $rating = floatval($product['rating']);
                        for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?php echo ($i > $rating) ? ' text-muted' : ''; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-text">(<?php echo htmlspecialchars($product['rating_count']); ?> reviews)</span>
                </div>

                <p class="lead price mb-3">
                    <span class="current-price fs-3 fw-bold text-primary">$<?php echo htmlspecialchars($product['price']); ?></span>
                    <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                        <span class="original-price text-muted text-decoration-line-through ms-2">$<?php echo htmlspecialchars($product['original_price']); ?></span>
                    <?php endif; ?>
                </p>

                <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

                <div class="card bg-light p-3 mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <?php if ($product['stock_quantity'] > 0): ?>
                                    <span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i> In Stock</span>
                                    <small class="text-muted d-block mt-1"><?php echo $product['stock_quantity']; ?> items available</small>
                                <?php else: ?>
                                    <span class="badge bg-danger fs-6"><i class="fas fa-times-circle me-1"></i> Out of Stock</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form action="cart_actions.php" method="POST" class="d-flex">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <label for="quantity" class="form-label me-2 pt-1">Qty:</label>
                                <input type="number" class="form-control me-3" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" style="width: 80px;" <?php if ($product['stock_quantity'] <= 0) echo 'disabled'; ?>>
                                <button type="submit" class="btn btn-primary flex-grow-1" <?php if ($product['stock_quantity'] <= 0) echo 'disabled'; ?>>
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="product-meta">
                    <p class="mb-1"><span class="fw-bold">SKU:</span> SKU-<?php echo htmlspecialchars($product['id']); ?></p>
                    <p class="mb-1"><span class="fw-bold">Category:</span> <a href="category.php?id=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></p>
                </div>
            </div>
        </div>

        <!-- You could add related products section here -->

    <?php else: ?>
        <div class="text-center py-5">
            <h1 class="display-4">Product Not Found</h1>
            <p class="lead">Sorry, the product you are looking for does not exist.</p>
            <a href="index.php" class="btn btn-primary mt-3">Return to Homepage</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
