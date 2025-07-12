<?php
require_once 'includes/header.php'; // Includes session_start(), db connection, and common header

// --- Authentication Check ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = [];

// --- Fetch all orders for the current user ---
$sql = "SELECT id, order_number, created_at, total_amount, order_status
        FROM orders
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    mysqli_stmt_close($stmt);
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
            <!-- Order History Content -->
            <h3>My Orders</h3>
            <hr>
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td><?php echo date('F j, Y', strtotime($order['created_at'])); ?></td>
                                            <td>
                                                <span class="badge
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
                                            </td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <p>You have not placed any orders yet.</p>
                            <a href="index.php" class="btn btn-primary">Start Shopping</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
