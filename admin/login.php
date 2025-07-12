<?php
// admin/login.php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

// Hardcoded credentials (FOR DEMONSTRATION ONLY - REPLACE WITH DATABASE LOOKUP)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'password123'); // In a real app, use hashed passwords

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = "Username and password are required.";
    }
    // In a real app, you would fetch the user from DB and use password_verify()
    // For example:
    // $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE username = ?");
    // $stmt->bind_param("s", $username);
    // $stmt->execute();
    // $result = $stmt->get_result();
    // if ($user = $result->fetch_assoc()) {
    //    if (password_verify($password, $user['password_hash'])) { ... }
    // }
    else if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) { // Plain text comparison (BAD PRACTICE)
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error_message = "Invalid username or password.";
    }
}
// Include header but it won't show sidebar/navbar due to session check
include 'includes/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4">Admin Login</h3>
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
             <p class="text-center mt-3">
                <small class="text-muted">Default: admin / password123</small>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
