<?php
// admin/dashboard.php
include 'includes/header.php'; // This will handle session check and redirect if not logged in
require_once '../db.php'; // For potential database interactions on dashboard
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> -->
    </div>

    <div class="row">
        <!-- Example Stat Card: Total Products -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Products</div>
                            <?php
                                $product_count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
                                $product_count = mysqli_fetch_assoc($product_count_result)['count'];
                            ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $product_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example Stat Card: Total Categories -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Categories</div>
                            <?php
                                $category_count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM categories");
                                $category_count = mysqli_fetch_assoc($category_count_result)['count'];
                            ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $category_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Example Stat Card: Active Offers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Offers</div>
                            <?php
                                $offer_count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM offers WHERE is_active = 1 AND start_date <= NOW() AND end_date >= NOW()");
                                $offer_count = mysqli_fetch_assoc($offer_count_result)['count'] ?? 0; // Default to 0 if table doesn't exist yet or no offers
                            ?>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $offer_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- More cards can be added here -->

    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Links</h6>
                </div>
                <div class="card-body">
                    <a href="offers/index.php" class="btn btn-primary mb-2"><i class="fas fa-tags me-2"></i> Manage Offers</a>
                    <!-- Add more quick links as features are developed -->
                    <p class="mt-3">Welcome to the admin dashboard. From here you can manage your store's content and settings.</p>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
mysqli_close($conn);
include 'includes/footer.php';
?>
