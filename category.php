<?php
require_once 'includes/header.php'; // Includes session_start(), db connection, and common header

// --- Validate Category ID ---
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    // If no ID, or ID is invalid, show an error or redirect.
    // For now, we'll show a simple error message within the page structure.
    $category_name = "Invalid Category";
    $products = [];
} else {
    $category_id = $_GET['id'];

    // --- Fetch Category Details ---
    $stmt_cat = mysqli_prepare($conn, "SELECT name FROM categories WHERE id = ?");
    mysqli_stmt_bind_param($stmt_cat, "i", $category_id);
    mysqli_stmt_execute($stmt_cat);
    $result_cat = mysqli_stmt_get_result($stmt_cat);

    if ($row_cat = mysqli_fetch_assoc($result_cat)) {
        $category_name = $row_cat['name'];
    } else {
        $category_name = "Category Not Found";
    }
    mysqli_free_result($result_cat);
    mysqli_stmt_close($stmt_cat);

    // --- Fetch Products in this Category ---
    $products = [];
    $stmt_prod = mysqli_prepare($conn, "SELECT id, name, price, original_price, image, rating, rating_count, stock_quantity FROM products WHERE category_id = ? ORDER BY name ASC");
    if($stmt_prod){
        mysqli_stmt_bind_param($stmt_prod, "i", $category_id);
        mysqli_stmt_execute($stmt_prod);
        $result_prod = mysqli_stmt_get_result($stmt_prod);
        while ($row_prod = mysqli_fetch_assoc($result_prod)) {
            $products[] = $row_prod;
        }
        mysqli_free_result($result_prod);
        mysqli_stmt_close($stmt_prod);
    }
}
?>

<div class="container-fluid py-5">
    <div class="section-title">
        <h2><?php echo htmlspecialchars($category_name); ?></h2>
        <p>Browse our selection of premium <?php echo strtolower(htmlspecialchars($category_name)); ?></p>
    </div>

    <?php if (!empty($products)): ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="product-card h-100">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="text-decoration-none">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <?php if (!empty($product['original_price']) && $product['original_price'] > $product['price']): ?>
                                    <div class="product-badge">SALE</div>
                                <?php endif; ?>
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
                                        <i class="fas fa-star<?php echo ($i > $rating) ? ' text-muted' : ''; ?>"></i>
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
                                a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center">
            <p class="lead">No products found in this category.</p>
            <a href="index.php" class="btn btn-primary">Back to Homepage</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
