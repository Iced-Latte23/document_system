<?php
// Start session
session_start();

// Include database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Fetch documents from the database
$documents = [];
$sql = "SELECT id, title, description, author, uploaded_at FROM tbl_documents";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
} else {
    // Log or handle the error if the query fails
    error_log("Error fetching documents: " . $conn->error);
}