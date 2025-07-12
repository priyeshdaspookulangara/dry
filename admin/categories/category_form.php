<?php
// This file is intended to be included by create_category.php and edit_category.php
// It expects $category (array of category data, empty for create) and $page_title (string) to be set.

$category_name = $category['name'] ?? '';
$category_description = $category['description'] ?? '';

?>
<div class="container-fluid">
    <h1><?php echo htmlspecialchars($page_title); ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
     <?php if (isset($_SESSION['validation_errors'])): ?>
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

    <form action="save_category.php" method="POST">
        <?php if (isset($category['id'])): ?>
            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">Category Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category_name); ?>" required>
                    <small class="form-text text-muted">The slug will be automatically generated from this name.</small>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category_description); ?></textarea>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Category</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
