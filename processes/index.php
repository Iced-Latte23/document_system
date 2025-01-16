<?php
// Database connection
require 'db_connect.php';

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();

// Fetch documents from the database
$sql = "SELECT * FROM tbl_documents WHERE is_accessible = 1 ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

// Initialize an array to store  documents
$documents = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $documents[] = $row; // Add each document to the array
    }
}

// Limit the number of documents displayed on the homepage
$displayDocuments = array_slice($documents, 0, 3); // Show only 3 documents

// Close the database connection
$conn->close();
?>