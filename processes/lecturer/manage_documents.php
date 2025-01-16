<?php
// Set the default timezone to Cambodia (Phnom Penh)
date_default_timezone_set('Asia/Phnom_Penh');

// Database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Initialize variables
$title = $description = $file_path = $uploaded_by = '';
$success_message = '';
$error_message = '';

// Function to log activity
function logActivity($conn, $user_id, $action, $details = '')
{
    $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}

// Get the logged-in user's ID and role from the session
if (isset($_SESSION['user_id'])) {
    $uploaded_by = $_SESSION['user_id']; // Automatically get the user ID
    $current_user_role = $_SESSION['role']; // Get the user's role
} else {
    // Redirect to login page if the user is not logged in
    header("Location: ../../pages/login.php");
    exit();
}

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);

if ($result_settings && $result_settings->num_rows > 0) {
    $settings = $result_settings->fetch_assoc(); // Fetch the settings row
    $fileTypes = explode(',', $settings['allowed_file_types']); // Convert comma-separated string to array
} else {
    // Default file types if no settings are found
    $fileTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png'];
    error_log("No system settings found for id = 1.");
}

// Handle form submission to add a new document
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_document'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $author = trim($_POST['author']);

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file_type = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)); // Get the file extension
        $max_size = $settings['max_upload_size'] * 1024 * 1024; // Convert MB to bytes

        // Validate file type and size
        if (in_array($file_type, $fileTypes) && $_FILES['file']['size'] <= $max_size) {
            // Insert document metadata into the database first (without file path)
            $sql = "INSERT INTO tbl_documents (title, description, author, uploaded_by, file_type) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssis", $title, $description, $author, $uploaded_by, $file_type);

            if ($stmt->execute()) {
                // Get the auto-generated document ID
                $document_id = $stmt->insert_id;

                // Generate a unique file name using the document ID
                $unique_file_name = "doc_" . $document_id . "." . $file_type; // Example: doc_123.pdf
                $file_tmp = $_FILES['file']['tmp_name'];
                $file_path = "../../uploads/documents/" . $unique_file_name;

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($file_tmp, $file_path)) {
                    // Update the database with the file path
                    $update_sql = "UPDATE tbl_documents SET file_path = ? WHERE id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("si", $file_path, $document_id);

                    if ($update_stmt->execute()) {
                        $success_message = "Document added successfully!";
                        logActivity($conn, $_SESSION['user_id'], 'Add Document', "Added document: $title");
                    } else {
                        // Rollback: Delete the record if the file path update fails
                        $error_message = "Error updating file path: " . $update_stmt->error;
                        $delete_sql = "DELETE FROM tbl_documents WHERE id = ?";
                        $delete_stmt = $conn->prepare($delete_sql);
                        $delete_stmt->bind_param("i", $document_id);
                        $delete_stmt->execute();
                        $delete_stmt->close();

                        // Delete the uploaded file if the database update fails
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    $update_stmt->close();
                } else {
                    // Rollback: Delete the record if the file upload fails
                    $error_message = "Error uploading file!";
                    $delete_sql = "DELETE FROM tbl_documents WHERE id = ?";
                    $delete_stmt = $conn->prepare($delete_sql);
                    $delete_stmt->bind_param("i", $document_id);
                    $delete_stmt->execute();
                    $delete_stmt->close();
                }
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Invalid file type or size. Allowed types: " . implode(', ', $fileTypes) . " (up to " . $settings['max_upload_size'] . " MB).";
        }
    } else {
        $error_message = "No file uploaded or file upload error!";
    }
}

// Handle form submission to update document details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_document'])) {
    $document_id = $_POST['document_id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);

    // Fetch the current file path, type, and uploader from the database
    $sql = "SELECT file_path, file_type, uploaded_by FROM tbl_documents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $document_id);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $stmt->bind_result($current_file_path, $current_file_type, $document_uploader);
    $stmt->fetch();
    $stmt->close();

    // Check if the current user is the uploader or a superadmin
    if ($current_user_role === 'superadmin' || $uploaded_by == $document_uploader) {
        $new_file_path = $current_file_path; // Default to the current file path
        $new_file_type = $current_file_type; // Default to the current file type

        // Handle file upload if a new file is provided
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file_type = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)); // Get the file extension
            $max_size = $settings['max_upload_size'] * 1024 * 1024; // Convert MB to bytes

            // Validate file type and size
            if (in_array($file_type, $fileTypes) && $_FILES['file']['size'] <= $max_size) {
                // Generate a unique file name using the document ID and a timestamp
                $new_file_name = "doc_" . $document_id . "_" . time() . "." . $file_type; // Example: doc_18_1698765432.pdf
                $new_file_relative_path = "../../uploads/documents/" . $new_file_name; // Relative path for database
                $new_file_absolute_path = realpath("../../uploads/documents") . "/" . $new_file_name; // Absolute path for file operations

                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($_FILES['file']['tmp_name'], $new_file_absolute_path)) {
                    // Delete the old file if it exists
                    if (file_exists($current_file_path)) {
                        unlink($current_file_path);
                    }
                    $new_file_path = $new_file_relative_path; // Use relative path for database
                    $new_file_type = $file_type;
                } else {
                    $error_message = "Error uploading file!";
                    $new_file_path = $current_file_path; // Revert to the current file path
                    $new_file_type = $current_file_type; // Revert to the current file type
                }
            } else {
                $error_message = "Invalid file type or size. Allowed types: " . implode(', ', $fileTypes) . " (up to " . $settings['max_upload_size'] . " MB).";
                $new_file_path = $current_file_path; // Revert to the current file path
                $new_file_type = $current_file_type; // Revert to the current file type
            }
        }

        // Update document details in the database
        $updated_at = date('Y-m-d H:i:s'); // Current timestamp
        $sql = "UPDATE tbl_documents SET title = ?, description = ?, author = ?, file_path = ?, file_type = ?, updated_at = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing update statement: " . $conn->error);
        }
        $stmt->bind_param("ssssssi", $title, $description, $author, $new_file_path, $new_file_type, $updated_at, $document_id);

        if ($stmt->execute()) {
            $success_message = "Document updated successfully!";
            logActivity($conn, $_SESSION['user_id'], 'Update Document', "Updated document: $title");
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "You do not have permission to edit this document.";
    }
}

// Fetch all documents from the database
$sql = "SELECT d.*, u.name AS uploaded_by_name
        FROM tbl_documents d
        LEFT JOIN tbl_users u ON d.uploaded_by = u.id
        WHERE d.uploaded_by = $uploaded_by"; // Filter by the current user's ID
$result = $conn->query($sql);

// Close the database connection
$conn->close();
