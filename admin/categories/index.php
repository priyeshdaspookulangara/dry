<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php'; // Database connection

// Fetch all categories along with the count of products in each category
$sql = "SELECT c.id, c.name, c.slug, c.description, COUNT(p.id) AS product_count
        FROM categories c
        LEFT JOIN products p ON c.id = p.category_id
        GROUP BY c.id, c.name, c.slug, c.description
        ORDER BY c.name ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching categories: " . mysqli_error($conn));
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manage Categories</h1>
        <a href="create_category.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Category
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0">Current Categories</h5>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Product Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($category = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo $category['id']; ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['slug']); ?></td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill"><?php echo $category['product_count']; ?></span>
                                    </td>
                                    <td>
                                        <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($category['product_count'] == 0): ?>
                                            <a href="delete_category.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-danger" title="Cannot delete category with products" disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">No categories found. <a href="create_category.php">Add one now!</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result);
mysqli_close($conn);
require_once '../includes/footer.php';
?>
