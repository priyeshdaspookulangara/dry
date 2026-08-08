<?php
session_start(); // Start session for messages
require_once '../../db.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error_message'] = "Invalid product ID specified for deletion.";
    header("Location: index.php");
    exit;
}

$product_id = $_GET['id'];

// CSRF protection would be good here (e.g., check a token passed in URL or POST)
// For simplicity, it's omitted.

// First, fetch the image path to delete the file
$image_path = null;
$stmt_select_image = mysqli_prepare($conn, "SELECT image FROM products WHERE id = ?");
if ($stmt_select_image) {
    mysqli_stmt_bind_param($stmt_select_image, "i", $product_id);
    mysqli_stmt_execute($stmt_select_image);
    $result_image = mysqli_stmt_get_result($stmt_select_image);
    if ($row = mysqli_fetch_assoc($result_image)) {
        $image_path = $row['image'];
    }
    mysqli_stmt_close($stmt_select_image);
} else {
    $_SESSION['error_message'] = "Failed to prepare statement for fetching product image: " . mysqli_error($conn);
    header("Location: index.php");
    exit;
}


// Begin transaction
mysqli_begin_transaction($conn);

try {
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement for deleting product: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $product_id);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            // Product deleted from DB, now delete the image file if it exists
            if ($image_path) {
                $full_image_path = '../../' . $image_path; // Relative path from this script's location
                if (file_exists($full_image_path) && is_file($full_image_path)) {
                    if (!unlink($full_image_path)) {
                        // Image deletion failed, but product is deleted. Log this or notify admin.
                        // For now, we'll commit the DB change but set an error message about the image.
                        mysqli_commit($conn);
                        $_SESSION['error_message'] = "Product deleted from database, but failed to delete image file: " . htmlspecialchars(basename($image_path)) . ". Please check file permissions or delete manually.";
                        header("Location: index.php");
                        exit;
                    }
                }
            }
            mysqli_commit($conn);
            $_SESSION['success_message'] = "Product and its image deleted successfully.";
        } else {
            // Product might have been deleted by another process or ID was invalid initially
            mysqli_rollback($conn);
            $_SESSION['error_message'] = "Product not found or already deleted.";
        }
    } else {
        throw new Exception("Error deleting product from database: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error_message'] = "Failed to delete product: " . $e->getMessage();
}

mysqli_close($conn);
header("Location: index.php");
exit;
?>
