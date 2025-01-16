<?php
// Database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();


// Fetch document upload trends over the last 30 days
$sql_upload_trends = "SELECT DATE(uploaded_at) AS upload_date, COUNT(*) AS upload_count 
                      FROM tbl_documents 
                      WHERE uploaded_by = ? AND uploaded_at >= NOW() - INTERVAL 30 DAY 
                      GROUP BY DATE(uploaded_at) 
                      ORDER BY upload_date ASC";
$stmt_upload_trends = $conn->prepare($sql_upload_trends);
$stmt_upload_trends->bind_param("i", $_SESSION['user_id']);
$stmt_upload_trends->execute();
$result_upload_trends = $stmt_upload_trends->get_result();
$upload_trends = [];
while ($row = $result_upload_trends->fetch_assoc()) {
    $upload_trends[] = $row;
}

// Fetch accessible vs inaccessible documents
$sql_accessible = "SELECT is_accessible, COUNT(*) AS count 
                   FROM tbl_documents 
                   WHERE uploaded_by = ? 
                   GROUP BY is_accessible";
$stmt_accessible = $conn->prepare($sql_accessible);
$stmt_accessible->bind_param("i", $_SESSION['user_id']);
$stmt_accessible->execute();
$result_accessible = $stmt_accessible->get_result();
$accessible_data = [0, 0]; // [inaccessible, accessible]
while ($row = $result_accessible->fetch_assoc()) {
    $accessible_data[$row['is_accessible']] = $row['count'];
}

// Fetch activity logs over the last 30 days
$sql_activity = "SELECT action, details, timestamp 
                 FROM tbl_activity_log 
                 WHERE user_id = ? AND timestamp >= NOW() - INTERVAL 30 DAY 
                 ORDER BY timestamp DESC";
$stmt_activity = $conn->prepare($sql_activity);
$stmt_activity->bind_param("i", $_SESSION['user_id']);
$stmt_activity->execute();
$result_activity = $stmt_activity->get_result();
