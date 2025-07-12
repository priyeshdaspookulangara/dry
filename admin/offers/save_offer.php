<?php
require_once '../includes/header.php'; // For session start, not for layout here as it's a processing script
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: index.php");
    exit;
}

// --- Form Data Sanitization and Validation ---
$offer_id = isset($_POST['offer_id']) ? filter_var($_POST['offer_id'], FILTER_VALIDATE_INT) : null;
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$type = $_POST['type'] ?? 'percentage';
$value_str = trim($_POST['value'] ?? '0');
$coupon_code = !empty(trim($_POST['coupon_code'] ?? '')) ? trim($_POST['coupon_code']) : null; // Null if empty
$start_date_str = $_POST['start_date'] ?? '';
$end_date_str = $_POST['end_date'] ?? '';
$min_purchase_amount_str = trim($_POST['min_purchase_amount'] ?? '0');
$is_active = isset($_POST['is_active']) ? 1 : 0;
$usage_limit_str = trim($_POST['usage_limit'] ?? '');

$applicability_type = $_POST['applicability_type'] ?? 'all';
$applicable_categories = $_POST['applicable_categories'] ?? [];
$applicable_products = $_POST['applicable_products'] ?? [];

$errors = [];

// Basic Validations
if (empty($name)) {
    $errors[] = "Offer name is required.";
}
if (!in_array($type, ['percentage', 'fixed_amount', 'free_shipping'])) {
    $errors[] = "Invalid offer type.";
}

// Value validation based on type
if (!is_numeric($value_str)) {
    $errors[] = "Offer value must be a number.";
} else {
    $value = floatval($value_str);
    if ($type === 'percentage' && ($value < 0 || $value > 100)) {
        $errors[] = "Percentage value must be between 0 and 100.";
    } elseif ($type === 'fixed_amount' && $value <= 0) {
        $errors[] = "Fixed amount value must be greater than 0.";
    } elseif ($type === 'free_shipping' && $value != 0) {
        $errors[] = "Value for Free Shipping type must be 0.";
        $value = 0; // Correct it
    }
}
if (!is_numeric($min_purchase_amount_str) || floatval($min_purchase_amount_str) < 0) {
    $errors[] = "Minimum purchase amount must be a non-negative number.";
} else {
    $min_purchase_amount = floatval($min_purchase_amount_str);
}

// Date Validations
$start_date_obj = DateTime::createFromFormat('Y-m-d\TH:i', $start_date_str);
$end_date_obj = DateTime::createFromFormat('Y-m-d\TH:i', $end_date_str);

if (!$start_date_obj) {
    $errors[] = "Invalid start date format.";
}
if (!$end_date_obj) {
    $errors[] = "Invalid end date format.";
}
if ($start_date_obj && $end_date_obj && $end_date_obj <= $start_date_obj) {
    $errors[] = "End date must be after the start date.";
}

$start_date = $start_date_obj ? $start_date_obj->format('Y-m-d H:i:s') : null;
$end_date = $end_date_obj ? $end_date_obj->format('Y-m-d H:i:s') : null;


if (!empty($usage_limit_str)) {
    if (!filter_var($usage_limit_str, FILTER_VALIDATE_INT) || intval($usage_limit_str) < 0) {
        $errors[] = "Usage limit must be a non-negative integer.";
    }
    $usage_limit = intval($usage_limit_str);
} else {
    $usage_limit = null; // NULL for unlimited
}


// Coupon code uniqueness check (if provided and different from current if editing)
if ($coupon_code) {
    $sql_check_coupon = "SELECT id FROM offers WHERE coupon_code = ? AND (? IS NULL OR id != ?)";
    $stmt_check_coupon = mysqli_prepare($conn, $sql_check_coupon);
    mysqli_stmt_bind_param($stmt_check_coupon, "sii", $coupon_code, $offer_id, $offer_id);
    mysqli_stmt_execute($stmt_check_coupon);
    $result_check_coupon = mysqli_stmt_get_result($stmt_check_coupon);
    if (mysqli_num_rows($result_check_coupon) > 0) {
        $errors[] = "This coupon code is already in use. Please choose a different one.";
    }
    mysqli_stmt_close($stmt_check_coupon);
}


if (!empty($errors)) {
    $_SESSION['validation_errors'] = $errors;
    // Preserve form data in session to repopulate (simplified here)
    $_SESSION['form_data'] = $_POST;
    if ($offer_id) {
        header("Location: edit_offer.php?id=" . $offer_id);
    } else {
        header("Location: create_offer.php");
    }
    exit;
}

// --- Database Operation ---
mysqli_begin_transaction($conn);

try {
    if ($offer_id) { // Update existing offer
        $sql = "UPDATE offers SET name = ?, description = ?, type = ?, value = ?, coupon_code = ?,
                start_date = ?, end_date = ?, min_purchase_amount = ?, is_active = ?, usage_limit = ?, updated_at = NOW()
                WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssdsstdiii", $name, $description, $type, $value, $coupon_code, $start_date, $end_date, $min_purchase_amount, $is_active, $usage_limit, $offer_id);
    } else { // Insert new offer
        $sql = "INSERT INTO offers (name, description, type, value, coupon_code, start_date, end_date, min_purchase_amount, is_active, usage_limit, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssdsstdii", $name, $description, $type, $value, $coupon_code, $start_date, $end_date, $min_purchase_amount, $is_active, $usage_limit);
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error saving offer details: " . mysqli_stmt_error($stmt));
    }

    $current_offer_id = $offer_id ? $offer_id : mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // --- Manage Offer Applicability ---
    // 1. Delete existing applicability rules for this offer
    $delete_app_sql = "DELETE FROM offer_applicability WHERE offer_id = ?";
    $stmt_delete_app = mysqli_prepare($conn, $delete_app_sql);
    mysqli_stmt_bind_param($stmt_delete_app, "i", $current_offer_id);
    mysqli_stmt_execute($stmt_delete_app);
    mysqli_stmt_close($stmt_delete_app);

    // 2. Insert new applicability rules
    $insert_app_sql = "INSERT INTO offer_applicability (offer_id, applicable_to, applicable_id) VALUES (?, ?, ?)";
    $stmt_insert_app = mysqli_prepare($conn, $insert_app_sql);

    if ($applicability_type === 'all') {
        $applicable_id_null = null; // Explicitly null for applicable_id when 'all'
        mysqli_stmt_bind_param($stmt_insert_app, "iss", $current_offer_id, $applicability_type, $applicable_id_null);
        if(!mysqli_stmt_execute($stmt_insert_app)) {
            throw new Exception("Error saving 'all' applicability: " . mysqli_stmt_error($stmt_insert_app));
        }
    } elseif ($applicability_type === 'categories' && !empty($applicable_categories)) {
        foreach ($applicable_categories as $category_id) {
            $cat_id_int = filter_var($category_id, FILTER_VALIDATE_INT);
            if ($cat_id_int) {
                mysqli_stmt_bind_param($stmt_insert_app, "isi", $current_offer_id, $applicability_type, $cat_id_int);
                 if(!mysqli_stmt_execute($stmt_insert_app)) {
                    throw new Exception("Error saving category applicability: " . mysqli_stmt_error($stmt_insert_app));
                }
            }
        }
    } elseif ($applicability_type === 'products' && !empty($applicable_products)) {
        foreach ($applicable_products as $product_id) {
            $prod_id_int = filter_var($product_id, FILTER_VALIDATE_INT);
            if ($prod_id_int) {
                mysqli_stmt_bind_param($stmt_insert_app, "isi", $current_offer_id, $applicability_type, $prod_id_int);
                 if(!mysqli_stmt_execute($stmt_insert_app)) {
                    throw new Exception("Error saving product applicability: " . mysqli_stmt_error($stmt_insert_app));
                }
            }
        }
    }
    mysqli_stmt_close($stmt_insert_app);

    mysqli_commit($conn);
    $_SESSION['success_message'] = "Offer saved successfully!";

} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error_message'] = "Failed to save offer: " . $e->getMessage();
    // Preserve form data in session to repopulate (simplified)
    $_SESSION['form_data'] = $_POST;
    if ($offer_id) {
        header("Location: edit_offer.php?id=" . $offer_id);
    } else {
        header("Location: create_offer.php");
    }
    exit;
} finally {
    if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']); // Clear form data on success or if not needed
}

mysqli_close($conn);
header("Location: index.php");
exit;
?>
