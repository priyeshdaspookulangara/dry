<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php';       // Database connection

$page_title = "Edit Offer";
$offer = []; // Initialize offer array

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error_message'] = "Invalid offer ID.";
    header("Location: index.php");
    exit;
}

$offer_id = $_GET['id'];

// Fetch offer details from the database
$stmt = mysqli_prepare($conn, "SELECT * FROM offers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $offer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $offer = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
} else {
    $_SESSION['error_message'] = "Offer not found.";
    header("Location: index.php");
    exit;
}
mysqli_stmt_close($stmt);

// Include the reusable form
// $conn is still open and needed by offer_form.php for fetching categories/products
include 'offer_form.php';

mysqli_close($conn);
require_once '../includes/footer.php';
?>
