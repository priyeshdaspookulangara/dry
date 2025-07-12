<?php
require_once 'includes/header.php'; // Includes session_start(), db connection, and common header

// --- Authentication & Cart Check ---
// 1. If user is not logged in, redirect to login page.
//    Store the intended destination to redirect back after login.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'checkout.php';
    $_SESSION['error_message'] = "You must be logged in to proceed to checkout.";
    header("Location: login.php");
    exit;
}

// 2. If cart is empty, redirect to cart page (which will show 'cart is empty' message).
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// --- Fetch cart details for summary ---
$cart_items = $_SESSION['cart'];
$products_in_cart = [];
$cart_subtotal = 0.00;

$product_ids = array_keys($cart_items);
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$types = str_repeat('i', count($product_ids));
$sql = "SELECT id, name, price, image FROM products WHERE id IN ($placeholders)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$product_ids);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($product = mysqli_fetch_assoc($result)) {
    $product['quantity'] = $cart_items[$product['id']];
    $cart_subtotal += $product['price'] * $product['quantity'];
    $products_in_cart[] = $product;
}
mysqli_stmt_close($stmt);
?>

<div class="container-fluid py-5">
    <div class="section-title">
        <h2>Checkout</h2>
    </div>

    <div class="row">
        <!-- Shipping Address Form -->
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h4><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h4>
                </div>
                <div class="card-body">
                    <form action="place_order.php" method="POST" id="checkoutForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="address_line_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address_line_1" name="address_line_1" placeholder="Street address, P.O. box, etc." required>
                        </div>
                        <div class="mb-3">
                            <label for="address_line_2" class="form-label">Address Line 2 (Optional)</label>
                            <input type="text" class="form-control" id="address_line_2" name="address_line_2" placeholder="Apartment, suite, unit, building, floor, etc.">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="state" class="form-label">State / Province / Region <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="postal_code" class="form-label">ZIP / Postal Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="country" name="country" value="United States" required>
                            </div>
                        </div>
                         <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="For delivery questions" required>
                        </div>
                        <hr>
                        <!-- Payment method simulation -->
                        <h4 class="mt-4">Payment Method</h4>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">
                                Cash on Delivery
                            </label>
                            <small class="d-block text-muted">Pay with cash upon delivery. (No actual payment required for this demo).</small>
                        </div>
                        <!-- A real site would have Stripe/PayPal elements here -->

                        <!-- The submit button is in the Order Summary column -->
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-5">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header">
                    <h4><i class="fas fa-shopping-cart me-2"></i>Order Summary</h4>
                </div>
                <div class="card-body">
                    <?php foreach ($products_in_cart as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="d-flex">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <small><?php echo htmlspecialchars($item['name']); ?></small>
                                    <br>
                                    <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                </div>
                            </div>
                            <small>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></small>
                        </div>
                        <hr class="my-1">
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-3">
                        <p class="mb-2">Subtotal</p>
                        <p class="mb-2">$<?php echo number_format($cart_subtotal, 2); ?></p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="mb-2">Shipping</p>
                        <p class="mb-2">Free</p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <p>Total</p>
                        <p>$<?php echo number_format($cart_subtotal, 2); ?></p>
                    </div>
                    <div class="d-grid mt-3">
                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-lg">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
