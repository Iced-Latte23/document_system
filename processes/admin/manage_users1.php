<?php

// Database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();

// Initialize variables
$name = $email = $password = $role = '';
$success_message = '';
$error_message = '';
$email_exists_error = false; // Flag for email exists error

// Function to log activity
function logActivity($conn, $user_id, $action, $details = '')
{
    // Insert activity into tbl_activity_log
    $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $_SESSION['user_id'], $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Handle form submission to add a new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } elseif (strlen($password) < 8) { // Check password length
        $error_message = "Password must be at least 8 characters long!";
    } else {
        // Check if email already exists
        $sql = "SELECT id FROM tbl_users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $email_exists_error = true; // Set flag for email exists error
                $error_message = "Email already exists!";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert data into the database
                $sql = "INSERT INTO tbl_users (name, email, password, role) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

                if ($stmt->execute()) {
                    $success_message = "User added successfully!";

                    // Log the activity
                    logActivity($conn, $_SESSION['user_id'], 'add_user', "Added user: $name ($email)");

                    // Clear form fields
                    $name = $email = $password = $role = '';
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
            }
            $stmt->close();
        } else {
            $error_message = "Database error: " . $conn->error;
        }
    }
}

// Handle form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format!";
    } else {
        // Update user details in the database
        $sql = "UPDATE tbl_users SET name = ?, email = ?, role = ?, updated_at = now() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $role, $user_id);

        if ($stmt->execute()) {
            $success_message = "User updated successfully!";

            // Log the activity
            logActivity($conn, $_SESSION['user_id'], 'edit_user', "Updated user: $name ($email)");
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Fetch user details before deletion for logging
    $sql = "SELECT name, email FROM tbl_users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Delete user from the database
    $sql = "DELETE FROM tbl_users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $success_message = "User deleted successfully!";

        // Log the activity
        logActivity($conn, $_SESSION['user_id'], 'delete_user', "Deleted user: " . $user['name'] . " (" . $user['email'] . ")");
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all users from the database
$sql = "SELECT * FROM tbl_users";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
