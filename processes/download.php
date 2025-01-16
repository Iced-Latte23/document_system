<?php
// Start session
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require 'db_connect.php';

// Check if the ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request. No file specified.");
}

$fileId = $_GET['id'];

// Fetch file information from the database
$sql = "SELECT title, file_path FROM tbl_documents WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $fileId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("File not found.");
}

$file = $result->fetch_assoc();
$filePath = $file['file_path'];
$fileTitle = $file['title'];

// Check if the file exists on the server
if (!file_exists($filePath)) {
    die("Error: File not found on the server.");
}

// Check if a download log already exists for this file and user (based on fileId and user_id)
$checkLogSql = "SELECT id FROM tbl_activity_log WHERE user_id = ? AND details LIKE ?";
$checkLogStmt = $conn->prepare($checkLogSql);
$details = '%Downloaded file: ' . $fileTitle . '%'; // Using LIKE to match all logs for this file
$checkLogStmt->bind_param("is", $_SESSION['user_id'], $details);
$checkLogStmt->execute();
$logResult = $checkLogStmt->get_result();

if ($logResult->num_rows === 0) {
    // Log the download
    $logSql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $logStmt = $conn->prepare($logSql);
    $action = 'download';
    $details = 'Downloaded file: ' . $fileTitle;  // Using the title instead of file ID
    $logStmt->bind_param("iss", $_SESSION['user_id'], $action, $details);
    $logStmt->execute();
}

// Serve the file
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($fileTitle) . '.' . pathinfo($filePath, PATHINFO_EXTENSION) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit();
