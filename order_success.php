<?php
require_once 'includes/header.php'; // Includes session_start(), db connection, and common header

// --- Authentication Check ---
// While not strictly necessary if they just came from place_order,
// it's good practice to protect pages that assume a logged-in state.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get order number from URL
$order_number = isset($_GET['order_number']) ? htmlspecialchars($_GET['order_number']) : null;
?>

<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                    <h1 class="display-5">Thank You For Your Order!</h1>

                    <?php if ($order_number): ?>
                        <p class="lead">Your order has been placed successfully.</p>
                        <p>Your order number is: <strong class="text-primary"><?php echo $order_number; ?></strong></p>
                        <p>You will receive an email confirmation shortly. You can also view your order details in your account dashboard.</p>
                    <?php else: ?>
                        <p class="lead">Your order has been placed successfully.</p>
                        <p>You can view your order details in your account dashboard.</p>
                    <?php endif; ?>

                    <div class="mt-5">
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </a>
                        <a href="account.php" class="btn btn-outline-secondary">
                            <i class="fas fa-user me-2"></i>Go to My Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
