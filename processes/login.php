<?php
session_start();

require 'db_connect.php'; // Database connection
include 'log_activity.php'; // Activity logging

// Initialize error message
$error_message = '';
$success_message = '';

// Fetch system settings from the database
$settings_sql = "SELECT * FROM tbl_system_settings WHERE id = 1";
$settings_result = $conn->query($settings_sql);

if ($settings_result && $settings_result->num_rows > 0) {
    $settings = $settings_result->fetch_assoc();
    $maintenance_mode = $settings['maintenance_mode']; // Check if maintenance mode is enabled
} else {
    die("Error fetching system settings.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Password should not be sanitized

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Fetch user details from the database
        $stmt = $conn->prepare("SELECT id, password, role, name, image_path, two_factor_auth FROM tbl_users WHERE email = ?");
        if (!$stmt) {
            die("Database error: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // User exists, fetch their details
            $stmt->bind_result($id, $hashed_password, $role, $name, $image_path, $two_fa_enabled);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Check if the system is in maintenance mode
                if ($maintenance_mode == 1 && in_array($role, ['lecturer', 'student', 'outsider'])) {
                    // Log maintenance mode access attempt and redirect
                    logActivity($conn, $id, 'Maintenance Mode Access', "Attempted to access during maintenance mode.");
                    header("Location: ../pages/maintenance.php");
                    exit();
                }

                // Check if 2FA is enabled for the user
                if ($two_fa_enabled == 1) {
                    // Generate OTP
                    $otp = rand(100000, 999999); // Generate a 6-digit OTP
                    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes")); // OTP expires in 10 minutes
                    $created_at = date("Y-m-d H:i:s");

                    // Store the OTP in the database
                    $stmt = $conn->prepare("INSERT INTO tbl_validate (user_id, otp, expires_at, created_at) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $id, $otp, $expiry, $created_at);
                    $stmt->execute();

                    // Send the OTP to the user's email
                    $subject = "Your One-Time Password (OTP)";
                    $body = "Your OTP for login is: $otp\n\nThis OTP is valid for 10 minutes.";
                    $headers = "From: no-reply@". $settings['app_name']. ".com";

                    // Configure PHP to use MailHog's SMTP server
                    ini_set("SMTP", "localhost");
                    ini_set("smtp_port", "1025");
                    ini_set("sendmail_from", "no-reply@". $settings['app_name']. ".com");

                    if (mail($email, $subject, $body, $headers)) {
                        // Set session variable to trigger the OTP modal
                        $_SESSION['2fa_required'] = true;
                        $_SESSION['2fa_user_id'] = $id;
                    } else {
                        $error_message = "Failed to send OTP. Please try again.";
                    }
                } else {
                    // 2FA is not enabled, proceed with normal login
                    logActivity($conn, $id, 'Login', "Logged in successfully.");

                    // Set session variables
                    $_SESSION['user_id'] = $id;
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = $role;
                    $_SESSION['name'] = $name;
                    $_SESSION['image_path'] = empty($image_path) ? '../uploads/profiles/guest.jpg' : $image_path;

                    // Redirect based on role
                    switch ($role) {
                        case 'superadmin':
                            header("Location: ../pages/superadmin/dashboard.php");
                            exit();
                        case 'admin':
                            header("Location: ../pages/admin/dashboard.php");
                            exit();
                        case 'lecturer':
                            header("Location: ../pages/lecturer/dashboard.php");
                            exit();
                        case 'student':
                            header("Location: ../pages/student/dashboard.php");
                            exit();
                        case 'outsider':
                            header("Location: ../pages/outsider/dashboard.php");
                            exit();
                        default:
                            echo "<script>alert('Your role is not recognized. Please contact the admin or support team for assistance.');</script>";
                            break;
                    }
                }
            } else {
                // Log failed login attempt (wrong password)
                logActivity($conn, $id, 'Failed Login', "Failed login attempt for email: $email.");
                $error_message = "Invalid password.";
            }
        } else {
            // User does not exist
            $error_message = "Email does not exist.";
        }

        $stmt->close();
    }
}
