<?php

// Database connection
require '../../processes/db_connect.php';
require __DIR__ . '/../session_check.php';

// Fetch all activity logs for the student
$sql_activity = "SELECT action, details, timestamp
                 FROM tbl_activity_log
                 WHERE user_id = ?
                 ORDER BY timestamp DESC";
$stmt_activity = $conn->prepare($sql_activity);
$stmt_activity->bind_param("i", $_SESSION['user_id']);
$stmt_activity->execute();
$result_activity = $stmt_activity->get_result();
