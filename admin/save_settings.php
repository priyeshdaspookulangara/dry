<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Invalid request method.";
    header("Location: settings.php");
    exit;
}

$setting_key = $_POST['setting_key'];

if ($setting_key === 'banner_image') {
    $upload_dir = '../uploads/banners/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == UPLOAD_ERR_OK) {
        // Fetch old image path to delete it
        $sql = "SELECT setting_value FROM settings WHERE setting_key = 'banner_image'";
        $result = mysqli_query($conn, $sql);
        $old_image = mysqli_fetch_assoc($result)['setting_value'];

        // Delete old image file
        if (!empty($old_image) && file_exists('../' . $old_image)) {
            unlink('../' . $old_image);
        }

        // Upload new image
        $file = $_FILES['banner_image'];
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $unique_filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
        $new_image_path = $upload_dir . $unique_filename;

        if (move_uploaded_file($file['tmp_name'], $new_image_path)) {
            $image_path_for_db = 'uploads/banners/' . $unique_filename;
            $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = 'banner_image'";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $image_path_for_db);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success_message'] = "Banner image updated successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to update banner image in database.";
            }
        } else {
            $_SESSION['error_message'] = "Failed to move uploaded file.";
        }
    } else {
        $_SESSION['error_message'] = "No file uploaded or an error occurred.";
    }
}

header("Location: settings.php");
exit;
?>
