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

// --- Minimal Header ---
// In a full implementation, this would be a shared header file.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - BN Dry Fruits & Nuts</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .account-sidebar { border-right: 1px solid #dee2e6; }
        .account-sidebar .nav-link { color: #333; border-radius: .25rem; margin-bottom: 0.5rem;}
        .account-sidebar .nav-link.active { color: #fff; background-color: var(--primary-color); }
        .account-sidebar .nav-link:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-3">
                <div class="account-sidebar p-3">
                    <h5 class="mb-3">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="account.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a class="nav-link" href="#"><i class="fas fa-box me-2"></i>My Orders</a>
                        <a class="nav-link" href="#"><i class="fas fa-map-marker-alt me-2"></i>My Addresses</a>
                        <a class="nav-link" href="#"><i class="fas fa-user-edit me-2"></i>Account Details</a>
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </nav>
                </div>
            </div>
            <div class="col-md-9">
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
                            Account Information
                        </div>
                        <div class="card-body">
                           <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                           <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                           <p><strong>Account Created:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>

                    <a href="index.php" class="btn btn-primary mt-4"><i class="fas fa-shopping-bag me-2"></i>Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
