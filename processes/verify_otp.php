<?php
session_start();

require 'db_connect.php'; // Database connection
include 'log_activity.php'; // Activity logging

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    $user_id = $_SESSION['2fa_user_id'];

    // Fetch the latest OTP for the user
    $stmt = $conn->prepare("SELECT otp, expires_at FROM tbl_validate WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_otp, $expires_at);
        $stmt->fetch();

        // Check if OTP is valid and not expired
        if ($otp == $stored_otp && strtotime($expires_at) > time()) {
            // OTP is valid, proceed with login
            logActivity($conn, $user_id, 'OTP Verification', "OTP verified successfully.");

            // Fetch user details
            $stmt = $conn->prepare("SELECT email, role, name, image_path FROM tbl_users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($email, $role, $name, $image_path);
            $stmt->fetch();

            logActivity($conn, $id, 'Login', "Logged in successfully.");
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
            $_SESSION['name'] = $name;
            $_SESSION['image_path'] = empty($image_path) ? '../uploads/profiles/guest.jpg' : $image_path;

            // Clear 2FA session variables
            unset($_SESSION['2fa_required']);
            unset($_SESSION['2fa_user_id']);

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
        } else {
            $error_message = "Invalid or expired OTP.";
        }
    } else {
        $error_message = "OTP not found.";
    }

    $stmt->close();
}
