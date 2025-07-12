<?php
// This file is intended to be included by create_product.php and edit_product.php
// It expects:
// $product (array of product data, empty for create)
// $page_title (string)
// $categories (array of category data from db)
// $conn (database connection) - though not directly used here, it's good practice if form needs dynamic elements from DB

$product_name = $product['name'] ?? '';
$category_id = $product['category_id'] ?? null;
$description = $product['description'] ?? '';
$price = $product['price'] ?? '';
$original_price = $product['original_price'] ?? '';
$stock_quantity = $product['stock_quantity'] ?? 0;
$current_image = $product['image'] ?? null; // For edit mode
$is_featured = $product['is_featured'] ?? 0;
$is_flash_sale = $product['is_flash_sale'] ?? 0;
// Rating and rating_count are usually not directly edited by admin in this manner
// $rating = $product['rating'] ?? 0.0;
// $rating_count = $product['rating_count'] ?? 0;

?>
<div class="container-fluid">
    <h1><?php echo htmlspecialchars($page_title); ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['validation_errors']) && !empty($_SESSION['validation_errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please correct the following errors:</strong>
            <ul>
                <?php foreach ($_SESSION['validation_errors'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['validation_errors']); ?>
    <?php endif; ?>

    <form action="save_product.php" method="POST" enctype="multipart/form-data">
        <?php if (isset($product['id'])): ?>
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <?php endif; ?>
        <?php if ($current_image): ?>
            <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($current_image); ?>">
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Product Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product_name); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0.01" value="<?php echo htmlspecialchars($price); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="original_price" class="form-label">Original Price ($) (Optional)</label>
                        <input type="number" class="form-control" id="original_price" name="original_price" step="0.01" min="0" value="<?php echo htmlspecialchars($original_price); ?>" placeholder="For sales display">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" step="1" min="0" value="<?php echo htmlspecialchars($stock_quantity); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/png, image/gif, image/webp">
                        <?php if (isset($product['id']) && $current_image && file_exists('../../' . $current_image)): ?>
                            <small class="form-text text-muted">Current image: <?php echo htmlspecialchars(basename($current_image)); ?>. Uploading a new image will replace it.</small>
                            <div class="mt-2">
                                <img src="../../<?php echo htmlspecialchars($current_image); ?>" alt="Current product image" style="max-height: 100px; border-radius: 0.25rem;">
                            </div>
                        <?php elseif (isset($product['id']) && $current_image): ?>
                             <small class="form-text text-danger">Current image (<?php echo htmlspecialchars(basename($current_image)); ?>) not found. Please re-upload.</small>
                        <?php else: ?>
                            <small class="form-text text-muted">Recommended size: 800x800px. Max 2MB. (JPG, PNG, GIF, WEBP)</small>
                        <?php endif; ?>
                    </div>
                </div>

                <hr>
                <h6 class="mt-3">Flags</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" <?php echo ($is_featured == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_featured">Featured Product</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_flash_sale" name="is_flash_sale" value="1" <?php echo ($is_flash_sale == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_flash_sale">Flash Sale Product</label>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Product</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- Optional: Add JS for image preview or rich text editor if needed later -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // You can add a preview here if desired
                // For now, just logging to console
                console.log('Selected file:', file.name, file.size, file.type);

                // Basic client-side validation example (can be more robust)
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file type. Please select a JPG, PNG, GIF or WEBP image.');
                    imageInput.value = ''; // Clear the input
                    return;
                }
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    alert('File is too large. Maximum size is 2MB.');
                    imageInput.value = ''; // Clear the input
                }
            }
        });
    }
});
</script>
