<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php';       // Database connection

$page_title = "Create New Offer";
$offer = []; // Empty array for a new offer

// Include the reusable form
include 'offer_form.php';

mysqli_close($conn);
require_once '../includes/footer.php';
?>
