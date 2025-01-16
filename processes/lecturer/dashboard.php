<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

$success_message = '';
$error_message = '';

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();

// Fetch document statistics
$sql_documents = "SELECT COUNT(*) AS total_documents FROM tbl_documents WHERE uploaded_by = ?";
$stmt_documents = $conn->prepare($sql_documents);
$stmt_documents->bind_param("i", $_SESSION['user_id']);
$stmt_documents->execute();
$result_documents = $stmt_documents->get_result();
$document_stats = $result_documents->fetch_assoc();

// Fetch shared document statistics
$sql_shared_documents = "SELECT COUNT(*) AS total_shared_documents FROM tbl_documents WHERE uploaded_by = ? AND is_accessible = 1";
$stmt_shared_documents = $conn->prepare($sql_shared_documents);
$stmt_shared_documents->bind_param("i", $_SESSION['user_id']);
$stmt_shared_documents->execute();
$result_shared_documents = $stmt_shared_documents->get_result();
$shared_document_stats = $result_shared_documents->fetch_assoc();

// Fetch recent activity logs
$sql_activity = "SELECT * FROM tbl_activity_log WHERE user_id = ? ORDER BY timestamp DESC LIMIT 5";
$stmt_activity = $conn->prepare($sql_activity);
$stmt_activity->bind_param("s", $_SESSION['user_id']);
$stmt_activity->execute();
$result_activity = $stmt_activity->get_result();

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_document'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $author = trim($_POST['author']);

    // Handle file upload
    if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] == 0) {
        // Get the file name and extension
        $file_name = basename($_FILES['document_file']['name']);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // Get the file extension in lowercase

        // Define allowed file types from system settings
        $allowed_types = explode(',', $settings['allowed_file_types']);
        $max_size = $settings['max_upload_size'] * 1024 * 1024; // Convert MB to bytes

        // Validate file type and size
        if (in_array($file_type, $allowed_types) && $_FILES['document_file']['size'] <= $max_size) {
            $file_tmp = $_FILES['document_file']['tmp_name'];
            $file_path = "../../uploads/documents/" . $file_name;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Insert data into the database
                $sql = "INSERT INTO tbl_documents (title, description, author, file_path, uploaded_by, file_type) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssis", $title, $description, $author, $file_path, $_SESSION['user_id'], $file_type);

                if ($stmt->execute()) {
                    $success_message = "Document added successfully!";
                    logActivity($conn, $_SESSION['user_id'], 'Add Document', "Added document: $title");
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

// Fetch documents from the database
$documents = [];
$sql = "SELECT id, title, description, author, uploaded_at, file_path FROM tbl_documents";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
} else {
    // Log or handle the error if the query fails
    error_log("Error fetching documents: " . $conn->error);
}


// Function to log activity
function logActivity($conn, $user_id, $action, $details = '')
{
    $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
}
