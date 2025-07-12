<?php
require_once '../includes/header.php'; // For session start
require_once '../../db.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error_message'] = "Invalid category ID specified for deletion.";
    header("Location: index.php");
    exit;
}

$category_id = $_GET['id'];

// --- Crucial Check: Prevent deletion if category has products ---
$check_sql = "SELECT COUNT(*) as product_count FROM products WHERE category_id = ?";
$stmt_check = mysqli_prepare($conn, $check_sql);

if (!$stmt_check) {
    $_SESSION['error_message'] = "Failed to prepare statement for checking products: " . mysqli_error($conn);
    header("Location: index.php");
    exit;
}

mysqli_stmt_bind_param($stmt_check, "i", $category_id);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$row = mysqli_fetch_assoc($result_check);
$product_count = $row['product_count'];
mysqli_stmt_close($stmt_check);

if ($product_count > 0) {
    $_SESSION['error_message'] = "Cannot delete this category because it contains " . $product_count . " product(s). Please reassign or delete these products first.";
    header("Location: index.php");
    exit;
}

// --- Proceed with deletion ---
$delete_sql = "DELETE FROM categories WHERE id = ?";
$stmt_delete = mysqli_prepare($conn, $delete_sql);

if (!$stmt_delete) {
    $_SESSION['error_message'] = "Failed to prepare statement for deleting category: " . mysqli_error($conn);
    header("Location: index.php");
    exit;
}

mysqli_stmt_bind_param($stmt_delete, "i", $category_id);

if (mysqli_stmt_execute($stmt_delete)) {
    if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
        $_SESSION['success_message'] = "Category deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Category not found or already deleted.";
    }
} else {
    $_SESSION['error_message'] = "Error deleting category: " . mysqli_stmt_error($stmt_delete);
}

mysqli_stmt_close($stmt_delete);
mysqli_close($conn);

header("Location: index.php");
exit;
?>
