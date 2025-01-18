<?php
// Include database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Fetch documents from the database
$documents = [];
$sql = "SELECT id, title, public_date, description, author, uploaded_at FROM tbl_documents WHERE is_accessible = 1";
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row;
    }
} else {
    // Log or handle the error if the query fails
    error_log("Error fetching documents: " . $conn->error);
}
