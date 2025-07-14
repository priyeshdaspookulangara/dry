<?php
// Include the database connection file
require_once 'db.php';

// SQL to create admins table
$createTableSQL = "
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);";

// Execute the create table query
if (mysqli_query($conn, $createTableSQL)) {
    echo "Table 'admins' created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// --- Inserting the admin user ---

// Admin credentials
$admin_username = 'admin';
$admin_password = 'admin123';

// Hash the password
$password_hash = password_hash($admin_password, PASSWORD_DEFAULT);

// SQL to insert admin user - using a prepared statement to prevent SQL injection
$insertAdminSQL = "INSERT INTO admins (username, password_hash) VALUES (?, ?)";

// Prepare the statement
$stmt = mysqli_prepare($conn, $insertAdminSQL);

if ($stmt) {
    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ss", $admin_username, $password_hash);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        echo "Admin user 'admin' inserted successfully.<br>";
    } else {
        // Check if the user already exists
        if (mysqli_errno($conn) == 1062) { // 1062 is the error code for duplicate entry
            echo "Admin user 'admin' already exists.<br>";
        } else {
            echo "Error inserting admin user: " . mysqli_stmt_error($stmt) . "<br>";
        }
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement: " . mysqli_error($conn) . "<br>";
}

// Close the database connection
mysqli_close($conn);
?>
