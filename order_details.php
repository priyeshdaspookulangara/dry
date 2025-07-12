<?php
require_once 'includes/header.php'; // Includes session_start(), db connection, and common header

// --- Authentication & Authorization Check ---
// 1. Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// 2. Validate Order ID from URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $order = null;
} else {
    $order_id = $_GET['id'];

    // --- Fetch Order Details ---
    // CRITICAL: We must also check that the order belongs to the logged-in user
    $sql_order = "SELECT o.*, a.full_name, a.address_line_1, a.address_line_2, a.city, a.state, a.postal_code, a.country, a.phone
                  FROM orders o
                  JOIN user_addresses a ON o.shipping_address_id = a.id
                  WHERE o.id = ? AND o.user_id = ?";

    $stmt_order = mysqli_prepare($conn, $sql_order);
    if ($stmt_order) {
        mysqli_stmt_bind_param($stmt_order, "ii", $order_id, $user_id);
        mysqli_stmt_execute($stmt_order);
        $result_order = mysqli_stmt_get_result($stmt_order);
        $order = mysqli_fetch_assoc($result_order);
        mysqli_stmt_close($stmt_order);
    } else {
        $order = null;
    }

    // --- If order is valid, fetch order items ---
    if ($order) {
        $order_items = [];
        $sql_items = "SELECT oi.product_name, oi.quantity, oi.price, p.image, p.id as product_id
                      FROM order_items oi
                      LEFT JOIN products p ON oi.product_id = p.id
                      WHERE oi.order_id = ?";

        $stmt_items = mysqli_prepare($conn, $sql_items);
        if ($stmt_items) {
            mysqli_stmt_bind_param($stmt_items, "i", $order_id);
            mysqli_stmt_execute($stmt_items);
            $result_items = mysqli_stmt_get_result($stmt_items);
            while ($row = mysqli_fetch_assoc($result_items)) {
                $order_items[] = $row;
            }
            mysqli_stmt_close($stmt_items);
        }
    }
}
?>

<div class="container-fluid py-5">
     <div class="row">
        <div class="col-lg-3">
            <!-- Account Sidebar -->
            <div class="account-sidebar p-3 border rounded">
                 <h5 class="mb-3">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h5>
                 <nav class="nav flex-column">
                    <a class="nav-link" href="account.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a class="nav-link active" href="orders.php"><i class="fas fa-box me-2"></i>My Orders</a>
                    <a class="nav-link" href="#"><i class="fas fa-map-marker-alt me-2"></i>My Addresses</a>
                    <a class="nav-link" href="#"><i class="fas fa-user-edit me-2"></i>Account Details</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>
        </div>
        <div class="col-lg-9">
            <?php if ($order): ?>
                <h3>Order Details</h3>
                <hr>
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-0">Order #<?php echo htmlspecialchars($order['order_number']); ?></h5>
                        <small class="text-muted">Placed on <?php echo date('F j, Y, g:i A', strtotime($order['created_at'])); ?></small>
                    </div>
                     <span class="badge fs-6
                        <?php
                            switch(strtolower($order['order_status'])) {
                                case 'delivered': echo 'bg-success'; break;
                                case 'shipped': echo 'bg-info'; break;
                                case 'processing': echo 'bg-primary'; break;
                                case 'cancelled': echo 'bg-danger'; break;
                                default: echo 'bg-secondary'; // pending
                            }
                        ?>">
                        <?php echo htmlspecialchars(ucfirst($order['order_status'])); ?>
                    </span>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5>Items Ordered</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                            <tr>
                                                <td style="width: 100px;">
                                                    <a href="product_details.php?id=<?php echo $item['product_id']; ?>">
                                                        <img src="<?php echo htmlspecialchars($item['image'] ?? 'admin/uploads/default_product.jpg'); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="img-fluid" style="max-height: 75px;">
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="product_details.php?id=<?php echo $item['product_id']; ?>" class="text-dark text-decoration-none">
                                                        <?php echo htmlspecialchars($item['product_name']); ?>
                                                    </a>
                                                </td>
                                                <td>Qty: <?php echo $item['quantity']; ?></td>
                                                <td>Price: $<?php echo number_format($item['price'], 2); ?></td>
                                                <td class="text-end fw-bold">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row text-end">
                                    <div class="col-md-9"><strong>Subtotal:</strong></div>
                                    <div class="col-md-3">$<?php echo number_format($order['subtotal'], 2); ?></div>
                                    <div class="col-md-9"><strong>Shipping:</strong></div>
                                    <div class="col-md-3">$<?php echo number_format($order['shipping_cost'], 2); ?></div>
                                    <div class="col-md-9 fs-5"><strong>Total:</strong></div>
                                    <div class="col-md-3 fs-5 fw-bold">$<?php echo number_format($order['total_amount'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5>Shipping Address</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0"><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></p>
                                <p class="mb-0"><?php echo htmlspecialchars($order['address_line_1']); ?></p>
                                <?php if($order['address_line_2']): ?>
                                <p class="mb-0"><?php echo htmlspecialchars($order['address_line_2']); ?></p>
                                <?php endif; ?>
                                <p class="mb-0"><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?> <?php echo htmlspecialchars($order['postal_code']); ?></p>
                                <p class="mb-0"><?php echo htmlspecialchars($order['country']); ?></p>
                                <p class="mb-0">Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="orders.php" class="btn btn-outline-secondary mt-4"><i class="fas fa-arrow-left me-2"></i>Back to My Orders</a>

            <?php else: ?>
                <div class="text-center p-5">
                    <h3>Order Not Found</h3>
                    <p>Sorry, we couldn't find that order, or it may not belong to your account.</p>
                    <a href="orders.php" class="btn btn-primary">View Your Orders</a>
                </div>
            <?php endif; ?>
        </div>
     </div>
</div>

<?php require_once 'includes/footer.php'; ?>
