<?php
require_once '../includes/header.php'; // For session start
require_once '../../db.php';

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error_message'] = "Invalid offer ID specified for deletion.";
    header("Location: index.php");
    exit;
}

$offer_id = $_GET['id'];

// CSRF protection would be good here (e.g., check a token passed in URL or POST)
// For simplicity, it's omitted in this basic version.

mysqli_begin_transaction($conn);

try {
    // offer_applicability entries are deleted automatically due to ON DELETE CASCADE constraint
    // So, we only need to delete from the 'offers' table.

    $sql = "DELETE FROM offers WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement for deleting offer: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "i", $offer_id);

    if (mysqli_stmt_execute($stmt)) {
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            mysqli_commit($conn);
            $_SESSION['success_message'] = "Offer deleted successfully.";
        } else {
            // Offer might have been deleted by another process or ID was invalid initially
            mysqli_rollback($conn); // Rollback if no rows affected, though not strictly necessary if it didn't exist
            $_SESSION['error_message'] = "Offer not found or already deleted.";
        }
    } else {
        throw new Exception("Error deleting offer: " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);

} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error_message'] = "Failed to delete offer: " . $e->getMessage();
}

mysqli_close($conn);
header("Location: index.php");
exit;
?>
