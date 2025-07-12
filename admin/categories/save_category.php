<?php
require_once '../includes/header.php'; // For session start
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: index.php");
    exit;
}

// --- Function to generate a URL-friendly slug ---
function generateSlug($text) {
    // Remove unwanted characters
    $text = preg_replace('~[^-\w\s]+~', '', $text); // Allow alphanumeric, hyphens, whitespace
    // Replace whitespace with a single hyphen
    $text = preg_replace('~\s+~', '-', $text);
    // Trim hyphens from start and end
    $text = trim($text, '-');
    // Convert to lowercase
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a-' . time(); // Fallback for empty or all-special-char strings
    }
    return $text;
}

// --- Form Data Sanitization and Validation ---
$category_id = isset($_POST['category_id']) ? filter_var($_POST['category_id'], FILTER_VALIDATE_INT) : null;
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

$errors = [];

// Basic Validations
if (empty($name)) {
    $errors[] = "Category name is required.";
}

// Generate slug
$slug = generateSlug($name);

// Check for unique name and slug (only if different from current if editing)
// For name:
$sql_check_name = "SELECT id FROM categories WHERE name = ? AND (? IS NULL OR id != ?)";
$stmt_check_name = mysqli_prepare($conn, $sql_check_name);
mysqli_stmt_bind_param($stmt_check_name, "sii", $name, $category_id, $category_id);
mysqli_stmt_execute($stmt_check_name);
$result_check_name = mysqli_stmt_get_result($stmt_check_name);
if (mysqli_num_rows($result_check_name) > 0) {
    $errors[] = "This category name already exists. Please choose a different one.";
}
mysqli_stmt_close($stmt_check_name);

// For slug (if name was valid, slug might still clash if another name generated the same slug)
// This check is more complex if you allow manual slug editing, but for auto-generated, it's less likely to clash if name is unique.
// However, good to have a basic check or a mechanism to append a number if slug exists.
$sql_check_slug = "SELECT id FROM categories WHERE slug = ? AND (? IS NULL OR id != ?)";
$stmt_check_slug = mysqli_prepare($conn, $sql_check_slug);
mysqli_stmt_bind_param($stmt_check_slug, "sii", $slug, $category_id, $category_id);
mysqli_stmt_execute($stmt_check_slug);
$result_check_slug = mysqli_stmt_get_result($stmt_check_slug);
if (mysqli_num_rows($result_check_slug) > 0) {
    // If slug exists, try appending a unique number. This is a simple approach.
    // A more robust solution might involve a loop and checking db until unique slug is found.
    $slug .= '-' . time();
}
mysqli_stmt_close($stmt_check_slug);


if (!empty($errors)) {
    $_SESSION['validation_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    if ($category_id) {
        header("Location: edit_category.php?id=" . $category_id);
    } else {
        header("Location: create_category.php");
    }
    exit;
}

// --- Database Operation ---
if ($category_id) { // Update existing category
    $sql = "UPDATE categories SET name = ?, description = ?, slug = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $description, $slug, $category_id);
} else { // Insert new category
    $sql = "INSERT INTO categories (name, description, slug) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $description, $slug);
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = "Category saved successfully!";
} else {
    $_SESSION['error_message'] = "Failed to save category: " . mysqli_stmt_error($stmt);
     // If it's a duplicate entry error for slug/name on unique constraint despite checks, handle it
    if (mysqli_errno($conn) == 1062) { // 1062 is error code for duplicate entry
        $_SESSION['error_message'] = "Failed to save category. The name or generated slug might already exist. Please try a different name.";
    }
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Clear form data session on success or if not needed
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}

header("Location: index.php");
exit;
?>
