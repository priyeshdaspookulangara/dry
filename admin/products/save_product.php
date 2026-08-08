<?php
session_start(); // Start session for messages
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: index.php");
    exit;
}

// --- Form Data Sanitization and Validation ---
$product_id = isset($_POST['product_id']) ? filter_var($_POST['product_id'], FILTER_VALIDATE_INT) : null;
$name = trim($_POST['name'] ?? '');
$category_id = filter_var($_POST['category_id'] ?? null, FILTER_VALIDATE_INT);
$description = trim($_POST['description'] ?? '');
$price_str = trim($_POST['price'] ?? '');
$original_price_str = trim($_POST['original_price'] ?? '');
$stock_quantity_str = trim($_POST['stock_quantity'] ?? '');
$existing_image = $_POST['existing_image'] ?? null;

$is_featured = isset($_POST['is_featured']) ? 1 : 0;
$is_flash_sale = isset($_POST['is_flash_sale']) ? 1 : 0;

$errors = [];

// Basic Validations
if (empty($name)) $errors[] = "Product name is required.";
if (empty($category_id)) $errors[] = "Category is required.";
if (!is_numeric($price_str) || floatval($price_str) <= 0) $errors[] = "Price must be a positive number.";
if (!is_numeric($stock_quantity_str) || intval($stock_quantity_str) < 0) $errors[] = "Stock quantity must be a non-negative integer.";

// Handle optional original price
$original_price = null;
if (!empty($original_price_str)) {
    if (!is_numeric($original_price_str) || floatval($original_price_str) <= 0) {
        $errors[] = "Original price, if provided, must be a positive number.";
    } else {
        $original_price = floatval($original_price_str);
    }
}

$price = floatval($price_str);
$stock_quantity = intval($stock_quantity_str);

// --- Image Upload Handling ---
$image_path = $existing_image; // Default to existing image
$upload_dir = '../../admin/uploads/'; // Go up two levels to root, then to admin/uploads
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024; // 2MB

if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $file = $_FILES['image'];

    // Validate file type and size
    if (!in_array($file['type'], $allowed_types)) {
        $errors[] = "Invalid file type. Please upload a JPG, PNG, GIF, or WEBP.";
    }
    if ($file['size'] > $max_size) {
        $errors[] = "File is too large. Maximum size is 2MB.";
    }

    if (empty($errors)) {
        // Generate a unique filename to prevent overwriting
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $unique_filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
        $new_image_path = $upload_dir . $unique_filename;

        if (move_uploaded_file($file['tmp_name'], $new_image_path)) {
            // New image uploaded successfully
            // Delete old image if it exists and is different from the new one
            if ($existing_image && file_exists($upload_dir . basename($existing_image))) {
                unlink($upload_dir . basename($existing_image));
            }
            // Set image_path to the relative path from the project root
            $image_path = 'admin/uploads/' . $unique_filename;
        } else {
            $errors[] = "Failed to move uploaded file. Check directory permissions.";
        }
    }
} elseif (isset($_FILES['image']) && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE) {
    // Handle other upload errors
    $errors[] = "An error occurred during file upload. Error code: " . $_FILES['image']['error'];
}

// Redirect back with errors if any
if (!empty($errors)) {
    $_SESSION['validation_errors'] = $errors;
    $_SESSION['form_data'] = $_POST; // Preserve form data
    if ($product_id) {
        header("Location: edit_product.php?id=" . $product_id);
    } else {
        header("Location: create_product.php");
    }
    exit;
}

// --- Database Operation ---
if ($product_id) { // Update existing product
    $sql = "UPDATE products SET name = ?, category_id = ?, description = ?, price = ?, original_price = ?,
            stock_quantity = ?, image = ?, is_featured = ?, is_flash_sale = ?, updated_at = NOW()
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sisddisiis", $name, $category_id, $description, $price, $original_price, $stock_quantity, $image_path, $is_featured, $is_flash_sale, $product_id);
} else { // Insert new product
    $sql = "INSERT INTO products (name, category_id, description, price, original_price, stock_quantity, image, is_featured, is_flash_sale, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sisddisii", $name, $category_id, $description, $price, $original_price, $stock_quantity, $image_path, $is_featured, $is_flash_sale);
}

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['success_message'] = "Product saved successfully!";
} else {
    $_SESSION['error_message'] = "Failed to save product: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Clear form data session on success
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}

header("Location: index.php");
exit;
?>
