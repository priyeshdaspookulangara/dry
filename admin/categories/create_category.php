<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php';       // Database connection (needed for footer)

$page_title = "Add New Category";
$category = []; // Empty array for a new category

// Repopulate form data from session if validation failed on previous attempt
if (isset($_SESSION['form_data'])) {
    $category = array_merge($category, $_SESSION['form_data']);
    // Ensure values from session are escaped for display
    foreach ($category as $key => $value) {
        if (is_string($value)) {
            $category[$key] = htmlspecialchars($value, ENT_QUOTES);
        }
    }
    unset($_SESSION['form_data']);
}

// Include the reusable form
// $conn is available and might be used by footer or other includes
include 'category_form.php';

mysqli_close($conn); // Close connection after form is rendered
require_once '../includes/footer.php';
?>
