<?php
// Database connection
require __DIR__ . '/../db_connect.php';
require __DIR__ . '/../session_check.php';

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();

// Fetch total number of documents
$sql_total_documents = "SELECT COUNT(*) AS total_documents FROM tbl_documents";
$result_total_documents = $conn->query($sql_total_documents);
$row_total_documents = $result_total_documents->fetch_assoc();
$total_documents = $row_total_documents['total_documents'];

// Fetch number of documents uploaded in the last 7 days
$sql_recent_documents = "SELECT COUNT(*) AS recent_documents FROM tbl_documents WHERE uploaded_at >= NOW() - INTERVAL 7 DAY";
$result_recent_documents = $conn->query($sql_recent_documents);
$row_recent_documents = $result_recent_documents->fetch_assoc();
$recent_documents = $row_recent_documents['recent_documents'];

// Fetch number of documents by user
$sql_documents_by_user = "SELECT u.name, COUNT(d.id) AS document_count FROM tbl_documents d INNER JOIN tbl_users u ON d.uploaded_by = u.id GROUP BY u.name";
$result_documents_by_user = $conn->query($sql_documents_by_user);

// Fetch number of documents by file type (corrected query)
$sql_documents_by_type = "SELECT
                            CASE
                                WHEN LOWER(file_path) LIKE '%.pdf' THEN 'PDF'
                                WHEN LOWER(file_path) LIKE '%.jpg' OR LOWER(file_path) LIKE '%.jpeg' THEN 'JPEG'
                                WHEN LOWER(file_path) LIKE '%.png' THEN 'PNG'
                                WHEN LOWER(file_path) LIKE '%.doc' THEN 'DOC'
                                WHEN LOWER(file_path) LIKE '%.docx' THEN 'DOCX'
                                WHEN LOWER(file_path) LIKE '%.ppt' THEN 'PPT'
                                WHEN LOWER(file_path) LIKE '%.pptx' THEN 'PPTX'
                                WHEN LOWER(file_path) LIKE '%.xls' THEN 'XLS'
                                WHEN LOWER(file_path) LIKE '%.xlsx' THEN 'XLSX'
                                ELSE 'Other'
                            END AS file_type,
                            COUNT(*) AS type_count
                          FROM tbl_documents
                          GROUP BY file_type";
$result_documents_by_type = $conn->query($sql_documents_by_type);

// Fetch activity logs
$sql_activity_logs = "SELECT a.*, u.name AS user_name
                      FROM tbl_activity_log a
                      LEFT JOIN tbl_users u ON a.user_id = u.id
                      ORDER BY a.timestamp DESC
                      LIMIT 10";
$result_activity_logs = $conn->query($sql_activity_logs);

// Close the database connection
$conn->close();
