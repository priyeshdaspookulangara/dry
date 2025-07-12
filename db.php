<?php
// Include configuration
require_once 'config.php';

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character set to utf8 (optional, but good practice)
mysqli_set_charset($conn, "utf8");
?>
