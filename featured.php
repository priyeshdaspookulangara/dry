<?php
include 'includes/header.php';

$sql = "SELECT * FROM products WHERE is_featured = 1 AND stock_quantity > 0";
$result = mysqli_query($conn, $sql);
$featured_products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <div class="section-title">
        <h2>Featured Products</h2>
    </div>
    <div class="row">
        <?php if (!empty($featured_products)): ?>
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card product-card">
                        <a href="product_details.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                            </h5>
                            <p class="card-text">
                                <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php if (!empty($product['original_price'])): ?>
                                    <s class="text-muted">$<?php echo number_format($product['original_price'], 2); ?></s>
                                <?php endif; ?>
                            </p>
                            <form action="cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col">
                <p>No featured products found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
