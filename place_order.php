<?php
session_start();
require_once 'db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Redirect to cart if cart is empty or request method is not POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Sanitize and retrieve form data
$shipping_name = filter_input(INPUT_POST, 'shipping_name', FILTER_SANITIZE_STRING);
$shipping_address = filter_input(INPUT_POST, 'shipping_address', FILTER_SANITIZE_STRING);
$shipping_city = filter_input(INPUT_POST, 'shipping_city', FILTER_SANITIZE_STRING);
$shipping_state = filter_input(INPUT_POST, 'shipping_state', FILTER_SANITIZE_STRING);
$shipping_zip = filter_input(INPUT_POST, 'shipping_zip', FILTER_SANITIZE_STRING);
$shipping_phone = filter_input(INPUT_POST, 'shipping_phone', FILTER_SANITIZE_STRING);
$payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

// Billing address
if (isset($_POST['same_as_shipping'])) {
    $billing_name = $shipping_name;
    $billing_address = $shipping_address;
    $billing_city = $shipping_city;
    $billing_state = $shipping_state;
    $billing_zip = $shipping_zip;
} else {
    $billing_name = filter_input(INPUT_POST, 'billing_name', FILTER_SANITIZE_STRING);
    $billing_address = filter_input(INPUT_POST, 'billing_address', FILTER_SANITIZE_STRING);
    $billing_city = filter_input(INPUT_POST, 'billing_city', FILTER_SANITIZE_STRING);
    $billing_state = filter_input(INPUT_POST, 'billing_state', FILTER_SANITIZE_STRING);
    $billing_zip = filter_input(INPUT_POST, 'billing_zip', FILTER_SANITIZE_STRING);
}

// Calculate total price
$total_price = 0;
$product_ids = array_keys($cart);
$sql = "SELECT id, price FROM products WHERE id IN (" . implode(',', $product_ids) . ")";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

foreach ($products as $product) {
    $total_price += $product['price'] * $cart[$product['id']];
}

// Insert order into database
$sql = "INSERT INTO orders (user_id, total_price, shipping_name, shipping_address, shipping_city, shipping_state, shipping_zip, shipping_phone, billing_name, billing_address, billing_city, billing_state, billing_zip, payment_method, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'idsssssssssssss', $user_id, $total_price, $shipping_name, $shipping_address, $shipping_city, $shipping_state, $shipping_zip, $shipping_phone, $billing_name, $billing_address, $billing_city, $billing_state, $billing_zip, $payment_method);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($conn);

// Insert order items into database
$sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

foreach ($products as $product) {
    $quantity = $cart[$product['id']];
    $price = $product['price'];
    mysqli_stmt_bind_param($stmt, 'iiid', $order_id, $product['id'], $quantity, $price);
    mysqli_stmt_execute($stmt);
}

// Clear cart
unset($_SESSION['cart']);

// Redirect to order success page
$_SESSION['success_message'] = "Your order has been placed successfully!";
header('Location: order_success.php?order_id=' . $order_id);
exit;
?>
