<?php
session_start();
require_once 'db.php';

// --- Guards ---
// 1. Must be a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}
// 2. Must be logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to place an order.";
    header("Location: login.php");
    exit;
}
// 3. Cart must not be empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

// --- Sanitize and Validate Address Input ---
$user_id = $_SESSION['user_id'];
$full_name = trim($_POST['full_name'] ?? '');
$address_line_1 = trim($_POST['address_line_1'] ?? '');
$address_line_2 = trim($_POST['address_line_2'] ?? null); // Optional
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');
$postal_code = trim($_POST['postal_code'] ?? '');
$country = trim($_POST['country'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$payment_method = $_POST['payment_method'] ?? 'cod';

// Basic validation (more can be added)
if (empty($full_name) || empty($address_line_1) || empty($city) || empty($state) || empty($postal_code) || empty($country) || empty($phone)) {
    $_SESSION['error_message'] = "All required address fields must be filled out.";
    header("Location: checkout.php");
    exit;
}


// --- Database Transaction ---
mysqli_begin_transaction($conn);

try {
    // 1. Save the shipping address
    $sql_address = "INSERT INTO user_addresses (user_id, address_type, full_name, address_line_1, address_line_2, city, state, postal_code, country, phone) VALUES (?, 'shipping', ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_address = mysqli_prepare($conn, $sql_address);
    mysqli_stmt_bind_param($stmt_address, "issssssss", $user_id, $full_name, $address_line_1, $address_line_2, $city, $state, $postal_code, $country, $phone);
    mysqli_stmt_execute($stmt_address);
    $shipping_address_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt_address);

    if(!$shipping_address_id) throw new Exception("Failed to save shipping address.");

    // 2. Fetch product details and calculate totals again on the server-side
    $cart_items = $_SESSION['cart'];
    $product_ids = array_keys($cart_items);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $types = str_repeat('i', count($product_ids));

    $sql_products = "SELECT id, price, stock_quantity FROM products WHERE id IN ($placeholders)";
    $stmt_products = mysqli_prepare($conn, $sql_products);
    mysqli_stmt_bind_param($stmt_products, $types, ...$product_ids);
    mysqli_stmt_execute($stmt_products);
    $result_products = mysqli_stmt_get_result($stmt_products);

    $fetched_products = [];
    while($row = mysqli_fetch_assoc($result_products)){
        $fetched_products[$row['id']] = $row;
    }
    mysqli_stmt_close($stmt_products);

    // Final stock check and total calculation
    $subtotal = 0;
    foreach($cart_items as $product_id => $quantity){
        if(!isset($fetched_products[$product_id])){
            throw new Exception("Product with ID $product_id not found in database.");
        }
        if($fetched_products[$product_id]['stock_quantity'] < $quantity){
            throw new Exception("Not enough stock for product ID $product_id.");
        }
        $subtotal += $fetched_products[$product_id]['price'] * $quantity;
    }
    $total_amount = $subtotal; // Assuming free shipping for now

    // 3. Create the order
    $order_number = 'ORD-' . time() . '-' . $user_id;
    $sql_order = "INSERT INTO orders (order_number, user_id, shipping_address_id, subtotal, total_amount, payment_method) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_order = mysqli_prepare($conn, $sql_order);
    mysqli_stmt_bind_param($stmt_order, "siidds", $order_number, $user_id, $shipping_address_id, $subtotal, $total_amount, $payment_method);
    mysqli_stmt_execute($stmt_order);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt_order);

    if(!$order_id) throw new Exception("Failed to create order.");

    // 4. Insert order items and update stock
    $sql_order_item = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $stmt_order_item = mysqli_prepare($conn, $sql_order_item);

    $sql_update_stock = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
    $stmt_update_stock = mysqli_prepare($conn, $sql_update_stock);

    foreach($cart_items as $product_id => $quantity){
        $product = $fetched_products[$product_id];
        // Insert order item
        mysqli_stmt_bind_param($stmt_order_item, "iisid", $order_id, $product_id, $product['name'], $quantity, $product['price']);
        mysqli_stmt_execute($stmt_order_item);

        // Update stock
        mysqli_stmt_bind_param($stmt_update_stock, "ii", $quantity, $product_id);
        mysqli_stmt_execute($stmt_update_stock);
    }
    mysqli_stmt_close($stmt_order_item);
    mysqli_stmt_close($stmt_update_stock);

    // If all queries were successful, commit the transaction
    mysqli_commit($conn);

    // 5. Clear the cart
    unset($_SESSION['cart']);

    // 6. Redirect to success page
    header("Location: order_success.php?order_number=" . urlencode($order_number));
    exit;

} catch (Exception $e) {
    // If any query fails, roll back all changes
    mysqli_rollback($conn);
    $_SESSION['error_message'] = "Failed to place order: " . $e->getMessage();
    header("Location: checkout.php");
    exit;
} finally {
    mysqli_close($conn);
}
?>
