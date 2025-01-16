<?php

require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();

// Fetch activity logs
$sql_activity_logs = "SELECT a.*, u.name AS user_name
                      FROM tbl_activity_log a
                      LEFT JOIN tbl_users u ON a.user_id = u.id
                      ORDER BY a.timestamp DESC
                      LIMIT 10";
$result_activity_logs = $conn->query($sql_activity_logs);

// Fetch metrics
$total_users = $conn->query("SELECT COUNT(*) as total_users FROM tbl_users")->fetch_assoc()['total_users'];
$total_documents = $conn->query("SELECT COUNT(*) as total_documents FROM tbl_documents")->fetch_assoc()['total_documents'];

// Fetch user roles data
$user_roles = $conn->query("SELECT role, COUNT(*) as count FROM tbl_users GROUP BY role")->fetch_all(MYSQLI_ASSOC);

// Prepare data for the user roles pie chart
$roles_labels = [];
$roles_data = [];
foreach ($user_roles as $role) {
    $roles_labels[] = $role['role'];
    $roles_data[] = $role['count'];
}

// Fetch document types data
$document_types = $conn->query("SELECT file_type, COUNT(*) as count FROM tbl_documents GROUP BY file_type")->fetch_all(MYSQLI_ASSOC);
$document_types_labels = [];
$document_types_data = [];
foreach ($document_types as $type) {
    $document_types_labels[] = $type['file_type'];
    $document_types_data[] = $type['count'];
}
