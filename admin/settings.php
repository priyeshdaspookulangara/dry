<?php
include 'includes/header.php';
require_once '../db.php';

// Fetch the current banner image
$sql = "SELECT setting_value FROM settings WHERE setting_key = 'banner_image'";
$result = mysqli_query($conn, $sql);
$banner_image = mysqli_fetch_assoc($result)['setting_value'];
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Settings</h1>

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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Homepage Banner</h6>
        </div>
        <div class="card-body">
            <form action="save_settings.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="banner_image" class="form-label">Upload New Banner Image</label>
                    <input class="form-control" type="file" id="banner_image" name="banner_image">
                    <small class="form-text text-muted">Recommended dimensions: 1920x600 pixels.</small>
                </div>

                <?php if (!empty($banner_image)): ?>
                    <div class="mb-3">
                        <label class="form-label">Current Banner</label>
                        <div>
                            <img src="../<?php echo htmlspecialchars($banner_image); ?>" alt="Current Banner" class="img-fluid" style="max-height: 200px;">
                        </div>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="setting_key" value="banner_image">
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
