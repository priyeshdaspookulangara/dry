<?php
include 'includes/header.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to proceed to checkout.";
    header('Location: login.php');
    exit;
}

// Redirect to cart if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}
?>

<div class="container mt-5">
    <h1>Checkout</h1>
    <form action="place_order.php" method="POST">
        <div class="row">
            <!-- Shipping Address -->
            <div class="col-md-6">
                <h2>Shipping Address</h2>
                <div class="mb-3">
                    <label for="shipping_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="shipping_name" name="shipping_name" required>
                </div>
                <div class="mb-3">
                    <label for="shipping_address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
                </div>
                <div class="mb-3">
                    <label for="shipping_city" class="form-label">City</label>
                    <input type="text" class="form-control" id="shipping_city" name="shipping_city" required>
                </div>
                <div class="mb-3">
                    <label for="shipping_state" class="form-label">State</label>
                    <input type="text" class="form-control" id="shipping_state" name="shipping_state" required>
                </div>
                <div class="mb-3">
                    <label for="shipping_zip" class="form-label">Zip Code</label>
                    <input type="text" class="form-control" id="shipping_zip" name="shipping_zip" required>
                </div>
                <div class="mb-3">
                    <label for="shipping_phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="shipping_phone" name="shipping_phone" required>
                </div>
            </div>

            <!-- Billing Address -->
            <div class="col-md-6">
                <h2>Billing Address</h2>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="same_as_shipping" name="same_as_shipping" checked>
                    <label class="form-check-label" for="same_as_shipping">
                        Same as shipping address
                    </label>
                </div>
                <div id="billing_address_form">
                    <div class="mb-3">
                        <label for="billing_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="billing_name" name="billing_name">
                    </div>
                    <div class="mb-3">
                        <label for="billing_address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="billing_address" name="billing_address">
                    </div>
                    <div class="mb-3">
                        <label for="billing_city" class="form-label">City</label>
                        <input type="text" class="form-control" id="billing_city" name="billing_city">
                    </div>
                    <div class="mb-3">
                        <label for="billing_state" class="form-label">State</label>
                        <input type="text" class="form-control" id="billing_state" name="billing_state">
                    </div>
                    <div class="mb-3">
                        <label for="billing_zip" class="form-label">Zip Code</label>
                        <input type="text" class="form-control" id="billing_zip" name="billing_zip">
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="row mt-5">
            <div class="col-md-6">
                <h2>Payment Method</h2>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                    <label class="form-check-label" for="cod">
                        Cash on Delivery
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                    <label class="form-check-label" for="paypal">
                        PayPal
                    </label>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2 mt-5">
            <button type="submit" class="btn btn-primary btn-lg">Place Order</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sameAsShipping = document.getElementById('same_as_shipping');
    const billingAddressForm = document.getElementById('billing_address_form');
    const billingInputs = billingAddressForm.querySelectorAll('input');

    function toggleBillingForm() {
        if (sameAsShipping.checked) {
            billingAddressForm.style.display = 'none';
            billingInputs.forEach(input => input.required = false);
        } else {
            billingAddressForm.style.display = 'block';
            billingInputs.forEach(input => input.required = true);
        }
    }

    sameAsShipping.addEventListener('change', toggleBillingForm);
    toggleBillingForm();
});
</script>

<?php
include 'includes/footer.php';
?>
