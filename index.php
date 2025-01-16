<?php
// Database connection
require 'processes/db_connect.php';

// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);

if ($result_settings && $result_settings->num_rows > 0) {
    $settings = $result_settings->fetch_assoc();
} else {
    die("Error: System settings not found.");
}

// Fetch accessible documents from the database
$sql = "SELECT * FROM tbl_documents WHERE is_accessible = 1 ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

// Initialize an array to store documents
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Efficiently manage and access your documents with <?php echo htmlspecialchars($settings['app_name']); ?>.">
    <title><?php echo htmlspecialchars($settings['app_name']); ?> - Document Management System</title>
    <link rel="stylesheet" href="assets/styles/index.css">
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>

<body>
    <!-- Header Section -->
    <header>
        <div class="logo">
            <?php echo htmlspecialchars($settings['app_name']); ?>
        </div>
        <nav>
            <a href="pages/signup.php" class="signup-btn">Sign Up</a>
            <a href="pages/login.php" class="login-btn">Login</a>
        </nav>
    </header>

    <!-- Welcome Section -->
    <section class="welcome-section">
        <h1>Welcome to <?php echo htmlspecialchars($settings['app_name']); ?></h1>
        <p>Access and manage your documents efficiently. Sign up to access all the documents.</p>
    </section>

    <!-- Document List -->
    <section class="document-list">
        <h2>Featured Documents</h2>
        <?php if (!empty($displayDocuments)): ?>
            <?php foreach ($displayDocuments as $doc): ?>
                <div class="document-item">
                    <h3><?php echo htmlspecialchars($doc['title']); ?></h3>
                    <p><?php echo htmlspecialchars($doc['description']); ?></p>
                    <p><a href="pages/view_page_without_login.php?id=<?php echo ($doc['id']); ?>" target="_blank" rel="noopener noreferrer">View</a></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No documents found.</p>
        <?php endif; ?>
        <p><strong>Want to see more?</strong> <a href="pages/signup.php">Sign up now</a> to access the full document library.</p>
    </section>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2023 <?php echo htmlspecialchars($settings['app_name']); ?>. All rights reserved.</p>
    </footer>
</body>

</html>