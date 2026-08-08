<?php
include 'includes/header.php';

$sql = "SELECT p.*, o.discount_percentage, o.start_date, o.end_date
        FROM products p
        INNER JOIN product_offers po ON p.id = po.product_id
        INNER JOIN offers o ON po.offer_id = o.id
        WHERE o.is_active = 1 AND o.start_date <= NOW() AND o.end_date >= NOW() AND p.stock_quantity > 0";
$result = mysqli_query($conn, $sql);
$offer_products = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<div class="container mt-5">
    <div class="section-title">
        <h2>Special Offers</h2>
    </div>
    <div class="row">
        <?php if (!empty($offer_products)): ?>
            <?php foreach ($offer_products as $product): ?>
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
                                <?php
                                $discounted_price = $product['price'] * (1 - $product['discount_percentage'] / 100);
                                ?>
                                <span class="price text-danger">$<?php echo number_format($discounted_price, 2); ?></span>
                                <s class="text-muted">$<?php echo number_format($product['price'], 2); ?></s>
                                <span class="badge bg-danger"><?php echo round($product['discount_percentage']); ?>% off</span>
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
                <p>No special offers found at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
