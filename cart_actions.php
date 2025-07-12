<?php
session_start();
require_once 'db.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- Helper function to redirect back to the previous page ---
function redirect_back($default = 'index.php') {
    $previous_page = $_SERVER['HTTP_REFERER'] ?? $default;
    header("Location: $previous_page");
    exit;
}

// --- Main Logic ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = isset($_POST['product_id']) ? filter_var($_POST['product_id'], FILTER_VALIDATE_INT) : null;
    $quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : null;

    if (!$product_id) {
        $_SESSION['error_message'] = "Invalid product specified.";
        redirect_back();
    }

    // --- Action: Add to Cart ---
    if ($action === 'add') {
        if ($quantity === null || $quantity <= 0) {
            $_SESSION['error_message'] = "Invalid quantity specified.";
            redirect_back();
        }

        // Check product existence and stock
        $stmt = mysqli_prepare($conn, "SELECT name, stock_quantity FROM products WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$product) {
            $_SESSION['error_message'] = "Product not found.";
            redirect_back();
        }

        $current_cart_qty = $_SESSION['cart'][$product_id] ?? 0;
        $requested_total_qty = $current_cart_qty + $quantity;

        if ($product['stock_quantity'] < $requested_total_qty) {
            $_SESSION['error_message'] = "Cannot add quantity. Not enough stock for '" . htmlspecialchars($product['name']) . "'. Available: " . $product['stock_quantity'];
            redirect_back();
        }

        // Add or update the quantity in the cart
        $_SESSION['cart'][$product_id] = $requested_total_qty;
        $_SESSION['success_message'] = htmlspecialchars($product['name']) . " has been added to your cart.";
        redirect_back();
    }

    // --- Action: Update Quantity ---
    else if ($action === 'update') {
        if ($quantity === null || $quantity < 0) { // Allow 0 quantity to effectively remove item
            $_SESSION['error_message'] = "Invalid quantity specified.";
            redirect_back('cart.php');
        }

        if ($quantity == 0) {
            // If quantity is set to 0, remove the item
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success_message'] = "Item removed from cart.";
        } else {
            // Check stock for the new quantity
            $stmt = mysqli_prepare($conn, "SELECT name, stock_quantity FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if (!$product) {
                $_SESSION['error_message'] = "Product not found.";
                unset($_SESSION['cart'][$product_id]); // Remove invalid product from cart
            } elseif ($product['stock_quantity'] < $quantity) {
                // Not enough stock, set quantity to max available
                $_SESSION['cart'][$product_id] = $product['stock_quantity'];
                $_SESSION['error_message'] = "Not enough stock for '" . htmlspecialchars($product['name']) . "'. Quantity has been adjusted to max available.";
            } else {
                // Update quantity
                $_SESSION['cart'][$product_id] = $quantity;
                $_SESSION['success_message'] = "Cart updated successfully.";
            }
        }
        redirect_back('cart.php');
    }

    // --- Action: Remove Item ---
    else if ($action === 'remove') {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success_message'] = "Item removed from your cart.";
        }
        redirect_back('cart.php');
    }

    else {
        $_SESSION['error_message'] = "Invalid cart action.";
        redirect_back();
    }

} else {
    // If accessed via GET, just redirect to homepage
    header("Location: index.php");
    exit;
}
?>
