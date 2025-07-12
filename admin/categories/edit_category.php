<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php';       // Database connection

$page_title = "Edit Category";
$category = []; // Initialize category array

// Validate Category ID from URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error_message'] = "Invalid category ID specified.";
    header("Location: index.php");
    exit;
}
$category_id = $_GET['id'];

// Fetch category details from the database
$stmt_category = mysqli_prepare($conn, "SELECT id, name, description, slug FROM categories WHERE id = ?");
if (!$stmt_category) {
    $_SESSION['error_message'] = "Failed to prepare statement for fetching category: " . mysqli_error($conn);
    header("Location: index.php");
    exit;
}
mysqli_stmt_bind_param($stmt_category, "i", $category_id);
mysqli_stmt_execute($stmt_category);
$result_category = mysqli_stmt_get_result($stmt_category);

if ($result_category && mysqli_num_rows($result_category) > 0) {
    $category = mysqli_fetch_assoc($result_category);
    mysqli_free_result($result_category);
} else {
    $_SESSION['error_message'] = "Category not found with ID: " . htmlspecialchars($category_id);
    header("Location: index.php");
    exit;
}
mysqli_stmt_close($stmt_category);

// Repopulate form data from session if validation failed on previous attempt for THIS category
if (isset($_SESSION['form_data']) && isset($_SESSION['form_data']['category_id']) && $_SESSION['form_data']['category_id'] == $category_id) {
    $session_form_data = $_SESSION['form_data'];
    // Merge session data over fetched data
    foreach ($session_form_data as $key => $value) {
        if (is_string($value)) {
            $category[$key] = htmlspecialchars($value, ENT_QUOTES);
        } else {
            $category[$key] = $value;
        }
    }
    unset($_SESSION['form_data']);
}


// Include the reusable form
// $conn is available
include 'category_form.php';

mysqli_close($conn);
require_once '../includes/footer.php';
?>
