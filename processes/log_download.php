<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    http_response_code(401); // Unauthorized
    die('Unauthorized');
}

require 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['fileId'])) {
    http_response_code(400); // Bad request
    die('Invalid request');
}

$file_id = intval($data['fileId']);
$user_id = $_SESSION['user_id'];

error_log("Log request received for file ID: $file_id"); // Debugging

function logActivity($conn, $user_id, $action, $details = '')
{
    $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
    return true;
}

if (logActivity($conn, $user_id, 'download', "Downloaded file with ID: $file_id")) {
    http_response_code(200); // Success
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500); // Internal server error
    echo json_encode(['status' => 'error', 'message' => 'Failed to log activity']);
}
