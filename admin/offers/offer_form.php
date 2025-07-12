<?php
// This file is intended to be included by create_offer.php and edit_offer.php
// It expects $offer (array of offer data, empty for create) and $page_title (string) to be set.
// It also expects $conn (database connection) to be available.

// Fetch categories and products for applicability selection
$categories_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$categories_result = mysqli_query($conn, $categories_sql);
$categories = [];
if ($categories_result) {
    while ($cat = mysqli_fetch_assoc($categories_result)) {
        $categories[] = $cat;
    }
    mysqli_free_result($categories_result);
}

$products_sql = "SELECT id, name FROM products ORDER BY name ASC";
$products_result = mysqli_query($conn, $products_sql);
$products = [];
if ($products_result) {
    while ($prod = mysqli_fetch_assoc($products_result)) {
        $products[] = $prod;
    }
    mysqli_free_result($products_result);
}

// For edit mode, get existing applicability rules
$existing_applicability = ['product' => [], 'category' => [], 'all' => false];
if (isset($offer['id'])) {
    $app_sql = "SELECT applicable_to, applicable_id FROM offer_applicability WHERE offer_id = ?";
    $stmt_app = mysqli_prepare($conn, $app_sql);
    mysqli_stmt_bind_param($stmt_app, "i", $offer['id']);
    mysqli_stmt_execute($stmt_app);
    $app_result = mysqli_stmt_get_result($stmt_app);
    if ($app_result) {
        while ($rule = mysqli_fetch_assoc($app_result)) {
            if ($rule['applicable_to'] == 'all') {
                $existing_applicability['all'] = true;
            } elseif ($rule['applicable_id']) {
                 $existing_applicability[$rule['applicable_to']][] = $rule['applicable_id'];
            }
        }
        mysqli_free_result($app_result);
    }
    mysqli_stmt_close($stmt_app);
}

$offer_name = $offer['name'] ?? '';
$offer_description = $offer['description'] ?? '';
$offer_type = $offer['type'] ?? 'percentage';
$offer_value = $offer['value'] ?? '';
$offer_coupon_code = $offer['coupon_code'] ?? '';
// Format dates for datetime-local input
$offer_start_date = isset($offer['start_date']) ? date('Y-m-d\TH:i', strtotime($offer['start_date'])) : date('Y-m-d\TH:i');
$offer_end_date = isset($offer['end_date']) ? date('Y-m-d\TH:i', strtotime($offer['end_date'])) : date('Y-m-d\TH:i', strtotime('+1 month'));
$offer_min_purchase_amount = $offer['min_purchase_amount'] ?? '0.00';
$offer_is_active = $offer['is_active'] ?? 1;
$offer_usage_limit = $offer['usage_limit'] ?? '';

?>

<div class="container-fluid">
    <h1><?php echo htmlspecialchars($page_title); ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
     <?php if (isset($_SESSION['validation_errors'])): ?>
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul>
                <?php foreach ($_SESSION['validation_errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['validation_errors']); ?>
    <?php endif; ?>


    <form action="save_offer.php" method="POST">
        <?php if (isset($offer['id'])): ?>
            <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Offer Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Offer Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($offer_name); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="coupon_code" class="form-label">Coupon Code (Optional)</label>
                        <input type="text" class="form-control" id="coupon_code" name="coupon_code" value="<?php echo htmlspecialchars($offer_coupon_code); ?>" placeholder="e.g., SUMMER20">
                        <small class="form-text text-muted">Leave blank if the offer applies automatically.</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($offer_description); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="type" class="form-label">Offer Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="percentage" <?php echo ($offer_type == 'percentage') ? 'selected' : ''; ?>>Percentage Discount</option>
                            <option value="fixed_amount" <?php echo ($offer_type == 'fixed_amount') ? 'selected' : ''; ?>>Fixed Amount Discount</option>
                            <option value="free_shipping" <?php echo ($offer_type == 'free_shipping') ? 'selected' : ''; ?>>Free Shipping</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="value" class="form-label">Value <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="value" name="value" step="0.01" value="<?php echo htmlspecialchars($offer_value); ?>" required>
                        <small class="form-text text-muted" id="valueHelp">Enter percentage (e.g., 10 for 10%), fixed amount (e.g., 5.50), or 0 for Free Shipping.</small>
                    </div>
                     <div class="col-md-4 mb-3">
                        <label for="min_purchase_amount" class="form-label">Minimum Purchase Amount</label>
                        <input type="number" class="form-control" id="min_purchase_amount" name="min_purchase_amount" step="0.01" value="<?php echo htmlspecialchars($offer_min_purchase_amount); ?>" placeholder="0.00">
                         <small class="form-text text-muted">Minimum cart total for offer to apply. 0 for no minimum.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_date" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="<?php echo $offer_start_date; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_date" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="<?php echo $offer_end_date; ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="usage_limit" class="form-label">Usage Limit (Optional)</label>
                        <input type="number" class="form-control" id="usage_limit" name="usage_limit" min="0" step="1" value="<?php echo htmlspecialchars($offer_usage_limit); ?>" placeholder="e.g., 100. Leave blank for unlimited.">
                    </div>
                    <div class="col-md-6 mb-3 align-self-center">
                         <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" <?php echo ($offer_is_active == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">Offer is Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Offer Applicability</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="applicability_type" id="apply_all" value="all" <?php echo ($existing_applicability['all'] || empty($offer['id']) && empty($existing_applicability['product']) && empty($existing_applicability['category'])) ? 'checked' : '';?>>
                        <label class="form-check-label" for="apply_all">
                            Apply to entire cart (subject to minimum purchase if set)
                        </label>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                     <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="applicability_type" id="apply_categories" value="categories" <?php echo (!empty($existing_applicability['category'])) ? 'checked' : '';?>>
                        <label class="form-check-label" for="apply_categories">
                            Apply to specific categories:
                        </label>
                    </div>
                    <select class="form-select" id="applicable_categories" name="applicable_categories[]" multiple size="5" <?php echo (empty($existing_applicability['category'])) ? 'disabled' : '';?>>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $existing_applicability['category']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                     <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple.</small>
                </div>
                <hr>
                 <div class="mb-3">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="applicability_type" id="apply_products" value="products" <?php echo (!empty($existing_applicability['product'])) ? 'checked' : '';?>>
                        <label class="form-check-label" for="apply_products">
                            Apply to specific products:
                        </label>
                    </div>
                    <select class="form-select" id="applicable_products" name="applicable_products[]" multiple size="5" <?php echo (empty($existing_applicability['product'])) ? 'disabled' : '';?>>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" <?php echo in_array($product['id'], $existing_applicability['product']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($product['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple.</small>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Offer</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('type');
    const valueHelp = document.getElementById('valueHelp');
    const valueInput = document.getElementById('value');

    function updateValueHelp() {
        if (typeSelect.value === 'percentage') {
            valueHelp.textContent = 'Enter percentage (e.g., 10 for 10%). Value must be between 0 and 100.';
            valueInput.step = "0.01";
            valueInput.min = "0";
            valueInput.max = "100";
        } else if (typeSelect.value === 'fixed_amount') {
            valueHelp.textContent = 'Enter fixed discount amount (e.g., 5.50 for $5.50).';
            valueInput.step = "0.01";
            valueInput.min = "0.01";
            valueInput.max = ""; // No max
        } else if (typeSelect.value === 'free_shipping') {
            valueHelp.textContent = 'Value should be 0 for Free Shipping (it will be ignored).';
            valueInput.value = 0;
            valueInput.min = "0";
            valueInput.max = "0";
            // valueInput.disabled = true; // Or make it readonly
        }
    }
    updateValueHelp(); // Initial call
    typeSelect.addEventListener('change', updateValueHelp);

    // Applicability radio buttons logic
    const applicabilityRadios = document.querySelectorAll('input[name="applicability_type"]');
    const categoriesSelect = document.getElementById('applicable_categories');
    const productsSelect = document.getElementById('applicable_products');

    function toggleApplicabilitySelects() {
        const selectedType = document.querySelector('input[name="applicability_type"]:checked').value;
        categoriesSelect.disabled = (selectedType !== 'categories');
        productsSelect.disabled = (selectedType !== 'products');

        if (selectedType !== 'categories') categoriesSelect.selectedIndex = -1; // Clear selection
        if (selectedType !== 'products') productsSelect.selectedIndex = -1; // Clear selection
    }

    applicabilityRadios.forEach(radio => {
        radio.addEventListener('change', toggleApplicabilitySelects);
    });
    toggleApplicabilitySelects(); // Initial call
});
</script>
