<?php
require_once '../includes/header.php'; // Session check, layout
require_once '../../db.php'; // Database connection

// Fetch all offers
$sql = "SELECT id, name, type, value, coupon_code, start_date, end_date, is_active
        FROM offers
        ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
// Check for query errors
if (!$result) {
    die("Error fetching offers: " . mysqli_error($conn));
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Manage Offers</h1>
        <a href="create_offer.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Offer
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-header">
            <h5 class="mb-0">Current Offers</h5>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Coupon</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($offer = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($offer['name']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $offer['type']))); ?></td>
                                    <td>
                                        <?php
                                        if ($offer['type'] == 'percentage') {
                                            echo htmlspecialchars($offer['value']) . '%';
                                        } elseif ($offer['type'] == 'fixed_amount') {
                                            echo '$' . htmlspecialchars($offer['value']);
                                        } else {
                                            echo 'N/A'; // For Free Shipping
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($offer['coupon_code'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('M j, Y H:i', strtotime($offer['start_date'])); ?></td>
                                    <td><?php echo date('M j, Y H:i', strtotime($offer['end_date'])); ?></td>
                                    <td>
                                        <?php if ($offer['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_offer.php?id=<?php echo $offer['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_offer.php?id=<?php echo $offer['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this offer? This action cannot be undone.');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <!-- Toggle Active Status Button (Advanced - requires another script) -->
                                        <!-- <a href="toggle_offer_status.php?id=<?php echo $offer['id']; ?>" class="btn btn-sm <?php echo $offer['is_active'] ? 'btn-warning' : 'btn-info'; ?>" title="<?php echo $offer['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas fa-power-off"></i>
                                        </a> -->
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center">No offers found. <a href="create_offer.php">Add one now!</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
mysqli_free_result($result);
mysqli_close($conn);
require_once '../includes/footer.php';
?>
