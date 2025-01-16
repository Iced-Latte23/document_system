<?php
// Start the session
session_start();

// Set the default timezone to Cambodia (Phnom Penh)
date_default_timezone_set('Asia/Phnom_Penh');

// Include the database connection file
require 'db_connect.php'; // Ensure this path is correct

// Initialize error messages
$error = '';
$field_errors = [
    'name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
];

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($name)) {
        $field_errors['name'] = "Name is required.";
    }
    if (empty($email)) {
        $field_errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = "Invalid email format.";
    }
    if (empty($password)) {
        $field_errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $field_errors['password'] = "Password must be at least 8 characters.";
    }
    if (empty($confirm_password)) {
        $field_errors['confirm_password'] = "Confirm Password is required.";
    } elseif ($password !== $confirm_password) {
        $field_errors['confirm_password'] = "Passwords do not match.";
    }

    // If no field errors, proceed to database operations
    if (empty(array_filter($field_errors))) {
        // Check if the email already exists
        $stmt = $conn->prepare("SELECT id FROM tbl_users WHERE email = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $field_errors['email'] = "Email already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO tbl_users (name, email, password, role) VALUES (?, ?, ?, 'outsider')");
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                // Redirect to the login page
                header("Location: login.php");
                exit();
            } else {
                $error = "Error creating account. Please try again.";
            }
        }

        $stmt->close();
    }
}
?>