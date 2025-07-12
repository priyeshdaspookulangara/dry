<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php'; // Database connection

// Fetch all products with their category names
$sql = "SELECT p.id, p.name, p.price, p.stock_quantity, p.image, p.is_featured, p.is_flash_sale, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.created_at DESC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching products: " . mysqli_error($conn));
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manage Products</h1>
        <a href="create_product.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Product
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
            <h5 class="mb-0">Current Products</h5>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Featured</th>
                                <th>Flash Sale</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($product['image']) && file_exists('../../' . $product['image'])): ?>
                                            <img src="../../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <img src="../../admin/uploads/default_product.jpg" alt="Default Image" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                                    <td>
                                        <?php
                                            $stock = intval($product['stock_quantity']);
                                            if ($stock > 10) {
                                                echo '<span class="badge bg-success">' . $stock . '</span>';
                                            } elseif ($stock > 0) {
                                                echo '<span class="badge bg-warning text-dark">' . $stock . '</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">' . $stock . ' (Out)</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($product['is_featured']): ?>
                                            <span class="badge bg-info">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['is_flash_sale']): ?>
                                            <span class="badge bg-warning text-dark">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary mb-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger mb-1" title="Delete" onclick="return confirm('Are you sure you want to delete this product? This will also delete its image and cannot be undone.');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">No products found. <a href="create_product.php">Add one now!</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result);
mysqli_close($conn);
require_once '../includes/footer.php';
?>
