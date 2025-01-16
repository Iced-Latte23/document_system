<?php
// log_activity.php

require 'db_connect.php';

function logActivity($conn, $user_id, $action, $details = '')
{

    if ($user_id === NULL) {
        return;
    }
    
    $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Failed to prepare log activity statement: " . $conn->error);
        return false;
    }
    $stmt->bind_param("iss", $user_id, $action, $details);
    $stmt->execute();
    $stmt->close();
    return true;
}
