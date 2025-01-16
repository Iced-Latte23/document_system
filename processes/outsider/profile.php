<?php
// Start the session
session_start();

// Set the default timezone to Cambodia (Phnom Penh)
date_default_timezone_set('Asia/Phnom_Penh');

// Database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Function to log activities
function logActivity($conn, $user_id, $action, $details = '')
{
    // Insert activity into tbl_activity_log
    $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $_SESSION['user_id'], $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();

// Initialize variables
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$profile_picture = $_SESSION['image_path'];
$error = '';
$success = '';

// Fetch user details from the database (for all roles)
$sql_user = "SELECT name, email, image_path, two_factor_auth FROM tbl_users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $_SESSION['user_id']);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

if ($user) {
    $name = $user['name'];
    $email = $user['email'];
    if ($user['image_path'] != "") {
        $profile_picture = $user['image_path'];
    } else {
        $profile_picture = '../../uploads/profiles/guest.jpg';
    }
    $two_factor_auth = $user['two_factor_auth'];
}

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);

    // Validate inputs
    if (empty($new_name) || empty($new_email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update user profile in the database (for all roles)
        $sql = "UPDATE tbl_users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $new_name, $new_email, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            $_SESSION['name'] = $new_name;
            $_SESSION['email'] = $new_email;
            $name = $new_name;
            $email = $new_email;

            // Log the activity
            logActivity($conn, $_SESSION['name'], 'Update Profile', "Updated profile information.");
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    }
}

// Handle form submission for updating profile picture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (in_array($_FILES['profile_picture']['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
            $file_name = basename($_FILES['profile_picture']['name']);
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $file_path = "../../uploads/profiles/" . $file_name;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Update profile picture in the database (for all roles)
                $sql = "UPDATE tbl_users SET image_path = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $file_name, $_SESSION['user_id']);

                if ($stmt->execute()) {
                    $success = "Profile picture updated successfully!";
                    $_SESSION['image_path'] = $file_name;
                    $profile_picture = $file_name;

                    // Log the activity
                    logActivity($conn, $_SESSION['user_id'], 'Update Profile Picture', "Updated profile picture.");
                } else {
                    $error = "Failed to update profile picture. Please try again.";
                }
            } else {
                $error = "Error uploading file!";
            }
        } else {
            $error = "Invalid file type or size!";
        }
    } else {
        $error = "No file uploaded or file upload error!";
    }
}

// Handle form submission for updating profile picture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile_picture'])) {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (in_array($_FILES['profile_picture']['type'], $allowed_types) && $_FILES['profile_picture']['size'] <= $max_size) {
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $upload_dir = "../../uploads/profiles/";

            // Ensure the upload directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true); // Create the directory with proper permissions
            }

            // Get the file extension
            $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);

            // Rename the file to the user's ID
            $file_name = $_SESSION['user_id'] . '.' . $file_extension;
            $file_path = $upload_dir . $file_name; // Update the file path after renaming

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Update profile picture in the database (for all roles)
                $sql = "UPDATE tbl_users SET image_path = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $file_path, $_SESSION['user_id']);

                if ($stmt->execute()) {
                    $success = "Profile picture updated successfully!";
                    $_SESSION['image_path'] = $file_name;
                    $profile_picture = $file_name;

                    // Log the activity
                    logActivity($conn, $_SESSION['user_id'], 'Update Profile Picture', "Updated profile picture.");
                } else {
                    $error = "Failed to update profile picture. Please try again.";
                }
            } else {
                $error = "Error uploading file!";
            }
        } else {
            $error = "Invalid file type or size!";
        }
    } else {
        $error = "No file uploaded or file upload error!";
    }
}

// Handle form submission for toggling 2FA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_2fa'])) {
    $two_factor_auth = $two_factor_auth ? 0 : 1; // Toggle 2FA status

    // Update 2FA status in the database (for all roles)
    $sql = "UPDATE tbl_users SET two_factor_auth = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $two_factor_auth, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $success = "Two-factor authentication " . ($two_factor_auth ? "enabled" : "disabled") . " successfully!";

        // Log the activity
        logActivity($conn, $_SESSION['user_id'], 'Toggle 2FA', ($two_factor_auth ? "Enabled" : "Disabled") . " two-factor authentication.");
    } else {
        $error = "Failed to update two-factor authentication. Please try again.";
    }
}

// Fetch recent activity logs
$sql_activity = "SELECT * FROM tbl_activity_log WHERE user_id = ? ORDER BY timestamp DESC LIMIT 5";
$stmt_activity = $conn->prepare($sql_activity);
$stmt_activity->bind_param("i", $_SESSION['user_id']);
$stmt_activity->execute();
$result_activity = $stmt_activity->get_result();

$stmt_activity->close();
