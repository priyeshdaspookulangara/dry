<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php';       // Database connection

$page_title = "Add New Product";
$product = []; // Empty array for a new product

// Fetch categories for the dropdown in the form
$categories_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_sql);
$categories = [];
if ($categories_result) {
    while ($cat = mysqli_fetch_assoc($categories_result)) {
        $categories[] = $cat;
    }
    mysqli_free_result($categories_result);
} else {
    // Handle error if categories can't be fetched, though unlikely if DB is up
    $_SESSION['error_message'] = "Could not fetch categories: " . mysqli_error($conn);
    // Potentially redirect or show error within the form page
}


// Repopulate form data from session if validation failed on previous attempt
if (isset($_SESSION['form_data'])) {
    $product = array_merge($product, $_SESSION['form_data']); // Merge to keep existing empty fields if any
    // Be careful with htmlspecialchars if data is already escaped or if it's an array
    foreach ($product as $key => $value) {
        if (is_string($value)) {
            $product[$key] = htmlspecialchars($value, ENT_QUOTES);
        }
    }
    unset($_SESSION['form_data']);
}


// Include the reusable form
// $conn (database connection) is available
include 'product_form.php';

mysqli_close($conn);
require_once '../includes/footer.php';
?>
