<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php';       // Database connection

$page_title = "Edit Product";
$product = []; // Initialize product array
$categories = []; // Initialize categories array

// Validate Product ID from URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error_message'] = "Invalid product ID specified.";
    header("Location: index.php");
    exit;
}
$product_id = $_GET['id'];

// Fetch product details from the database
// Using prepared statement for security, though GET param is validated as INT
$stmt_product = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
if (!$stmt_product) {
    $_SESSION['error_message'] = "Failed to prepare statement for fetching product: " . mysqli_error($conn);
    header("Location: index.php");
    exit;
}
mysqli_stmt_bind_param($stmt_product, "i", $product_id);
mysqli_stmt_execute($stmt_product);
$result_product = mysqli_stmt_get_result($stmt_product);

if ($result_product && mysqli_num_rows($result_product) > 0) {
    $product = mysqli_fetch_assoc($result_product);
    mysqli_free_result($result_product);
} else {
    $_SESSION['error_message'] = "Product not found with ID: " . htmlspecialchars($product_id);
    header("Location: index.php");
    exit;
}
mysqli_stmt_close($stmt_product);


// Fetch categories for the dropdown in the form
$categories_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_sql);
if ($categories_result) {
    while ($cat = mysqli_fetch_assoc($categories_result)) {
        $categories[] = $cat;
    }
    mysqli_free_result($categories_result);
} else {
    $_SESSION['error_message'] = "Could not fetch categories: " . mysqli_error($conn);
    // Allow form to load but category dropdown might be empty or show error
}

// Repopulate form data from session if validation failed on previous attempt for THIS product
if (isset($_SESSION['form_data']) && isset($_SESSION['form_data']['product_id']) && $_SESSION['form_data']['product_id'] == $product_id) {
    // Merge session data over fetched data to show user's attempted changes
    // Be careful with htmlspecialchars if data is already escaped
    $session_form_data = $_SESSION['form_data'];
    foreach ($session_form_data as $key => $value) {
        if (is_string($value)) {
            // Ensure values from session are also properly escaped for display in form fields
            $product[$key] = htmlspecialchars($value, ENT_QUOTES);
        } else {
             $product[$key] = $value; // For arrays or other types if any
        }
    }
    // Important: We need to ensure specific fields that might not be in $_POST (like checkboxes if unchecked)
    // are correctly handled. The product_form.php already handles this by checking $product['is_featured'] ?? 0;
    unset($_SESSION['form_data']);
}


// Include the reusable form
// $conn (database connection) is available
include 'product_form.php';

mysqli_close($conn);
require_once '../includes/footer.php';
?>
