<?php
// Set the default timezone to Cambodia (Phnom Penh)
date_default_timezone_set('Asia/Phnom_Penh');

// Database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Initialize variables
$error_message= '';
$success_message = '';

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();

// Function to log activities
function logActivity($conn, $user_id, $action, $details = '')
{
    $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Handle form submission for updating system settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $app_name = trim($_POST['app_name']);
    $max_upload_size = intval($_POST['max_upload_size']);
    $allowed_file_types = trim($_POST['allowed_file_types']);
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;

    // Validate inputs
    if (empty($app_name) || empty($max_upload_size) || empty($allowed_file_types)) {
        $error_message = "All system settings fields are required.";
    } elseif ($max_upload_size <= 0) {
        $error_message = "Maximum upload size must be greater than 0.";
    } else {
        // Update system settings in the database
        $sql = "UPDATE tbl_system_settings
                SET app_name = ?, max_upload_size = ?, allowed_file_types = ?, maintenance_mode = ?
                WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisi", $app_name, $max_upload_size, $allowed_file_types, $maintenance_mode);

        if ($stmt->execute()) {
            $success_message = "System settings updated successfully!";
            // Log the activity
            logActivity($conn, $_SESSION['name'], 'Update Settings', "Updated system settings.");
            // Refresh the settings
            $settings['app_name'] = $app_name;
            $settings['max_upload_size'] = $max_upload_size;
            $settings['allowed_file_types'] = $allowed_file_types;
            $settings['maintenance_mode'] = $maintenance_mode;
        } else {
            $error_message = "Failed to update system settings. Please try again.";
        }
    }
}

// Handle form submission for creating a backup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_backup'])) {
    $backup_dir = '../../backups/';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true); // Create the backup directory if it doesn't exist
    }

    $backup_file = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    $command = "C:/xampp/mysql/bin/mysqldump --user=root --password= --host=localhost db_doc_management_system > " . $backup_file;

    exec($command, $output, $return_var);

    if ($return_var === 0) {
        $success_message = "Backup created successfully!";
        // Log the activity
        logActivity($conn, $_SESSION['user_id'], 'Create Backup', "Created a database backup: $backup_file");
    } else {
        $error_message = "Failed to create backup. Please check your database credentials and permissions.";
    }
}

// Handle form submission for restoring a backup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore_backup'])) {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == 0) {
        $command = "mysqldump --user=root --password= --host=localhost db_doc_management_system > " . $backup_file;

        exec($command, $output, $return_var);

        if ($return_var === 0) {
            $success_message = "Backup restored successfully!";
            // Log the activity
            logActivity($conn, $_SESSION['user_id'], 'Restore Backup', "Restored database from backup: " . $_FILES['backup_file']['name']);
        } else {
            $error_message = "Failed to restore backup. Please check your database credentials and permissions.";
        }
    } else {
        $error_message = "No backup file uploaded or file upload error!";
    }
}

// Fetch activity logs
$sql_activity_logs = "SELECT a.*, u.name AS user_name
                      FROM tbl_activity_log a
                      LEFT JOIN tbl_users u ON a.user_id = u.id
                      ORDER BY a.timestamp DESC
                      LIMIT 10";
$result_activity_logs = $conn->query($sql_activity_logs);

// Close the database connection
$conn->close();
