<?php
// logout.php - For customers
session_start();

// Unset all of the session variables specific to the user.
// It's good practice to unset specific keys rather than the whole $_SESSION array
// in case there are other session variables you might want to keep (e.g., cart).
// However, for a full logout, destroying the session is most effective.
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);

// If you want to kill the session completely, which is usually the case for logout:
// 1. Unset all session variables
$_SESSION = array();

// 2. Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finally, destroy the session.
session_destroy();

// Redirect to the homepage after logout
header("Location: index.php");
exit;
?>
