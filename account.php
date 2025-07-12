<?php
session_start();
require_once 'db.php';

// --- Authentication Check ---
// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user details from the database to ensure data is fresh
$user_id = $_SESSION['user_id'];
$user = null;

$stmt = mysqli_prepare($conn, "SELECT name, email, created_at FROM users WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// If user is not found in DB (e.g., deleted after session started), log them out
if (!$user) {
    header("Location: logout.php");
    exit;
}

// Fetch recent orders for the dashboard summary
$recent_orders = [];
$sql_recent = "SELECT id, order_number, created_at, total_amount, order_status
               FROM orders
               WHERE user_id = ?
               ORDER BY created_at DESC
               LIMIT 5";
$stmt_recent = mysqli_prepare($conn, $sql_recent);
if ($stmt_recent) {
    mysqli_stmt_bind_param($stmt_recent, "i", $user_id);
    mysqli_stmt_execute($stmt_recent);
    $result_recent = mysqli_stmt_get_result($stmt_recent);
    while ($row = mysqli_fetch_assoc($result_recent)) {
        $recent_orders[] = $row;
    }
    mysqli_stmt_close($stmt_recent);
}


// This page now uses the main header
require_once 'includes/header.php';
?>
<style>
    /* Styles specific to account pages */
    .account-sidebar { border-right: 1px solid #dee2e6; }
    .account-sidebar .nav-link { color: #333; border-radius: .25rem; margin-bottom: 0.5rem;}
    .account-sidebar .nav-link.active { color: #fff; background-color: var(--primary-color); }
    .account-sidebar .nav-link:hover { background-color: #f8f9fa; }
</style>
<div class="container-fluid py-5">
    <div class="row">
        <div class="col-lg-3">
            <!-- Account Sidebar -->
            <div class="account-sidebar p-3 border rounded">
                <h5 class="mb-3">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h5>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="account.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a class="nav-link" href="orders.php"><i class="fas fa-box me-2"></i>My Orders</a>
                    <a class="nav-link" href="#"><i class="fas fa-map-marker-alt me-2"></i>My Addresses</a>
                    <a class="nav-link" href="#"><i class="fas fa-user-edit me-2"></i>Account Details</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                </nav>
            </div>
        </div>
        <div class="col-lg-9">
            <!-- Main Dashboard Content -->
            <div class="p-3">
                <h3>My Dashboard</h3>
                <hr>

                <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
                    <div class="alert alert-success">
                        Thank you for registering! Welcome to your account dashboard.
                    </div>
                <?php endif; ?>

                <p>Hello, <strong><?php echo htmlspecialchars($user['name']); ?></strong> (not you? <a href="logout.php">Log out</a>)</p>
                <p>From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>

                <div class="card mt-4">
                    <div class="card-header">
                        Recent Orders
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recent_orders)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr><th>Order #</th><th>Date</th><th>Status</th><th>Total</th><th></th></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($recent_orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></span></td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td><a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>You have no recent orders.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
