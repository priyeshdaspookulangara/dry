<?php
require_once 'includes/header.php'; // Includes session_start(), db connection, and common header

// The cart is stored in $_SESSION['cart'] as [product_id => quantity]
$cart_items = $_SESSION['cart'] ?? [];
$products_in_cart = [];
$cart_subtotal = 0.00;

if (!empty($cart_items)) {
    // Get all product IDs from the cart to fetch their details in one query
    $product_ids = array_keys($cart_items);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?')); // Creates ?,?,?
    $types = str_repeat('i', count($product_ids)); // Creates 'iii'

    $sql = "SELECT id, name, price, image, stock_quantity FROM products WHERE id IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, $types, ...$product_ids);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($product = mysqli_fetch_assoc($result)) {
            $quantity_in_cart = $cart_items[$product['id']];
            $product['quantity'] = $quantity_in_cart;
            $product['line_total'] = $product['price'] * $quantity_in_cart;
            $cart_subtotal += $product['line_total'];
            $products_in_cart[] = $product;
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div class="container-fluid py-5">
    <div class="section-title">
        <h2>Your Shopping Cart</h2>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($products_in_cart)): ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" colspan="2">Product</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products_in_cart as $item): ?>
                                <tr>
                                    <td style="width: 100px;">
                                        <a href="product_details.php?id=<?php echo $item['id']; ?>">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid" style="max-height: 75px;">
                                        </a>
                                    </td>
                                    <td>
                                        <a href="product_details.php?id=<?php echo $item['id']; ?>" class="text-dark text-decoration-none">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                        <small class="d-block text-muted">Stock: <?php echo $item['stock_quantity']; ?></small>
                                    </td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <!-- Update Quantity Form -->
                                        <form action="cart_actions.php" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock_quantity']; ?>" class="form-control form-control-sm" style="width: 70px;" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td>$<?php echo number_format($item['line_total'], 2); ?></td>
                                    <td>
                                        <!-- Remove Item Form -->
                                        <form action="cart_actions.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove item">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Cart Summary</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Subtotal
                                <span>$<?php echo number_format($cart_subtotal, 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Shipping
                                <span class="text-muted">Calculated at checkout</span>
                            </li>
                        </ul>
                        <div class="d-flex justify-content-between align-items-center fw-bold mt-3">
                            Total
                            <span>$<?php echo number_format($cart_subtotal, 2); ?></span>
                        </div>
                        <div class="d-grid mt-4">
                            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h3>Your cart is empty.</h3>
            <p>Looks like you haven't added anything to your cart yet.</p>
            <a href="index.php" class="btn btn-primary mt-3">Continue Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
