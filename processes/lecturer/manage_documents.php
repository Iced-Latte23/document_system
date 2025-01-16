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

function logActivity($conn, $user_id, $action, $details = '')
{
    // Insert activity into tbl_activity_log
    $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $_SESSION['user_id'], $action, $details);
    $stmt->execute();
    $stmt->close();
}



// Get the logged-in user's name from the session
if (isset($_SESSION['user_id'])) {
    $uploaded_by = $_SESSION['user_id']; // Automatically get the username
} else {
    // Redirect to login page if the user is not logged in
    header("Location: ../../pages/login.php");
    exit();
}

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);

if ($result_settings) {
    if ($result_settings->num_rows > 0) {
        $settings = $result_settings->fetch_assoc(); // Fetch the settings row
        $fileTypes = explode(',', $settings['allowed_file_types']); // Convert comma-separated string to array
    } else {
        // Handle case where no settings are found
        $fileTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png']; // Default file types
        error_log("No system settings found for id = 1.");
    }
} else {
    // Handle query execution error
    error_log("Error fetching system settings: " . $conn->error);
    $fileTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png']; // Default file types
}

// Handle accessibility toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_accessibility'])) {
    error_log("Toggle accessibility form submitted."); // Debugging statement

    $document_id = $_POST['document_id'];
    $is_accessible = (int)$_POST['is_accessible']; // Ensure it's an integer

    error_log("is_accessible value: " . $is_accessible); // Debugging statement

    // Fetch the document name from the database
    $sql_fetch_document = "SELECT title FROM tbl_documents WHERE id = ?";
    $stmt_fetch_document = $conn->prepare($sql_fetch_document);
    $stmt_fetch_document->bind_param("i", $document_id);
    $stmt_fetch_document->execute();
    $result_fetch_document = $stmt_fetch_document->get_result();

    if ($result_fetch_document->num_rows > 0) {
        $document = $result_fetch_document->fetch_assoc();
        $document_name = $document['title']; // Get the document name
    } else {
        $document_name = "Unknown Document"; // Fallback if document not found
    }

    $stmt_fetch_document->close();

    // Update the accessibility status in the database
    $sql_update = "UPDATE tbl_documents SET is_accessible = ?, updated_at = NOW() WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $is_accessible, $document_id); // Use "iis" if uploaded_by is a string

    if ($stmt_update->execute()) {
        // Log the activity with the document name
        $action = $is_accessible ? "Made document accessible" : "Made document inaccessible";
        logActivity($conn, $_SESSION['user_id'], $action, "Document: $document_name");

        // Debug: Success message
        error_log("Document accessibility updated successfully.");
    } else {
        // Debug: Error message
        error_log("Failed to update document accessibility: " . $stmt_update->error);
    }

    $stmt_update->close();

    // Redirect to refresh the page
    header("Location: ../../pages/lecturer/manage_documents.php");
    exit();
}

// Handle form submission to add a new document
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_document'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $author = trim($_POST['author']);

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        // Get the file name and extension
        $file_name = basename($_FILES['file']['name']);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // Get the file extension

        // Define allowed file types from system settings
        $allowed_types = explode(',', $settings['allowed_file_types']);
        $max_size = $settings['max_upload_size'] * 1024 * 1024; // Convert MB to bytes

        // Validate file type and size
        if (in_array($file_type, $allowed_types) && $_FILES['file']['size'] <= $max_size) {
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_path = "../../uploads/documents/" . $file_name;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Insert data into the database
                $sql = "INSERT INTO tbl_documents (title, description, author, file_path, uploaded_by, file_type) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssis", $title, $description, $author, $file_path, $uploaded_by, $file_type);

                if ($stmt->execute()) {
                    $success_message = "Document added successfully!";
                    // Log the activity
                    logActivity($conn, $_SESSION['user_id'], 'Add Document', "Added document: $title");
                    // Clear form fields
                    $title = $description = '';
                } else {
                    $error_message = "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Error uploading file!";
            }
        } else {
            $error_message = "Invalid file type or size. Allowed types: " . $settings['allowed_file_types'] . " (up to " . $settings['max_upload_size'] . " MB).";
        }
    } else {
        $error_message = "No file uploaded or file upload error!";
    }
}

// Handle form submission to update document details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_document'])) {
    $document_id = $_POST['document_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Fetch the current file path and type from the database
    $sql = "SELECT file_path, file_type FROM tbl_documents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $document_id);
    $stmt->execute();
    $stmt->bind_result($current_file_path, $current_file_type);
    $stmt->fetch();
    $stmt->close();

    $new_file_path = $current_file_path; // Default to the current file path
    $new_file_type = $current_file_type; // Default to the current file type

    // Handle file upload if a new file is provided
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        // Get the file name and type
        $file_name = basename($_FILES['file']['name']);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // Get the file extension

        // Define allowed file types from system settings
        $allowed_types = explode(',', $settings['allowed_file_types']);
        $max_size = $settings['max_upload_size'] * 1024 * 1024; // Convert MB to bytes

        // Validate file type and size
        if (in_array($file_type, $allowed_types) && $_FILES['file']['size'] <= $max_size) {
            $file_tmp = $_FILES['file']['tmp_name'];
            $new_file_path = "../uploads/documents/" . $file_name;
            $new_file_type = $file_type;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp, $new_file_path)) {
                // Delete the old file if it exists
                if (file_exists($current_file_path)) {
                    unlink($current_file_path);
                }
            } else {
                $error_message = "Error uploading file!";
                $new_file_path = $current_file_path; // Revert to the current file path
                $new_file_type = $current_file_type; // Revert to the current file type
            }
        } else {
            $error_message = "Invalid file type or size. Allowed types: " . $settings['allowed_file_types'] . " (up to " . $settings['max_upload_size'] . " MB).";
            $new_file_path = $current_file_path; // Revert to the current file path
            $new_file_type = $current_file_type; // Revert to the current file type
        }
    }

    // Update document details in the database
    $updated_at = date('Y-m-d H:i:s'); // Current timestamp in Cambodia time zone
    $sql = "UPDATE tbl_documents SET title = ?, description = ?, file_path = ?, file_type = ?, updated_at = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $description, $new_file_path, $new_file_type, $updated_at, $document_id);

    if ($stmt->execute()) {
        $success_message = "Document updated successfully!";
        // Log the activity
        logActivity($conn, $_SESSION['user_id'], 'Update Document', "Updated document: $title");
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Handle document deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_document'])) {
    $document_id = intval($_POST['document_id']);

    // Fetch the document title from the database
    $sql_fetch_title = "SELECT title FROM tbl_documents WHERE id = ?";
    $stmt_fetch_title = $conn->prepare($sql_fetch_title);
    $stmt_fetch_title->bind_param("i", $document_id);
    $stmt_fetch_title->execute();
    $stmt_fetch_title->bind_result($document_title);
    $stmt_fetch_title->fetch();
    $stmt_fetch_title->close();

    // Fetch the file path from the database
    $sql = "SELECT file_path FROM tbl_documents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $document_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    // Delete the file from the server
    if (file_exists($file_path)) {
        unlink($file_path);
    }

    // Delete document from the database
    $sql = "DELETE FROM tbl_documents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $document_id);

    if ($stmt->execute()) {
        $success_message = "Document deleted successfully!";
        // Log the activity with the document title
        logActivity($conn, $_SESSION['user_id'], 'Delete Document', "Deleted document: $document_title");
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();

    // Redirect to refresh the page
    header("Location: manage_documents.php");
    exit();
}

// Fetch all documents from the database
$sql = "SELECT d.*, u.name AS uploaded_by_name
        FROM tbl_documents d
        LEFT JOIN tbl_users u ON d.uploaded_by = u.id";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
