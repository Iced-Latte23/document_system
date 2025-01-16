<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View File</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: start;
            min-height: 100vh;
        }

        .container {
            width: 75%;
            max-width: 100%;
            text-align: center;
            position: relative;
        }

        /* Blank div at the top */
        .blank-div {
            width: 100%;
            height: 50px;
            background-color: #f4f4f4;
            position: relative;
            z-index: 2;
        }

        /* Iframe for PDF */
        iframe {
            width: 100%;
            height: 700px;
            border: none;
            margin: 0 auto;
            margin-top: -55px;
            display: block;
            background-color: #fff;
            position: relative;
            z-index: 1;
        }

        /* Download Link */
        .download-link {
            margin-top: 20px;
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
        }

        .download-link:hover {
            text-decoration: underline;
        }

        /* Error Message */
        .error-message {
            color: red;
            font-size: 18px;
            margin-top: 20px;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .container {
                width: 85%;
            }

            iframe {
                height: 500px;
                margin-top: -45px;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 95%;
            }

            iframe {
                height: 400px;
            }

            .blank-div {
                height: 40px;
            }

            iframe {
                margin-top: -45px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php

        session_start();
        // Database connection
        require '../processes/db_connect.php';

        // Function to log activity
        function logActivity($conn, $user_id, $action, $details = '')
        {
            $sql = "INSERT INTO tbl_activity_log (user_id, action, details, timestamp) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iss", $user_id, $action, $details);
            $stmt->execute();
            $stmt->close();
        }

        // Fetch allowed file types from tbl_system_setting
        $sql_settings = "SELECT allowed_file_types FROM tbl_system_settings LIMIT 1";
        $result_settings = $conn->query($sql_settings);

        if ($result_settings && $result_settings->num_rows > 0) {
            $row_settings = $result_settings->fetch_assoc();
            $allowed_file_types = explode(',', $row_settings['allowed_file_types']); // Convert comma-separated string to array
        } else {
            $allowed_file_types = []; // Default to empty array if no settings are found
        }

        // Get the file ID from the query string
        if (isset($_GET['id'])) {
            $file_id = intval($_GET['id']); // Sanitize the file ID

            // Fetch the file path from the database
            $sql = "SELECT file_path, title FROM tbl_documents WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $file_id);
            $stmt->execute();
            $stmt->bind_result($file_path, $file_title);
            $stmt->fetch();
            $stmt->close();

            if ($file_path) {
                // Adjust the file path if needed
                $file_path = str_replace('../../', '../', $file_path);

                // Resolve the full file path
                $real_file_path = realpath($file_path);

                // Check if the file exists
                if ($real_file_path && file_exists($real_file_path)) {
                    // Get the file extension
                    $file_extension = strtolower(pathinfo($real_file_path, PATHINFO_EXTENSION));

                    // Check if the file type is allowed
                    if (in_array($file_extension, $allowed_file_types)) {
                        // Log the file view activity
                        logActivity($conn, $_SESSION['user_id'], 'view', "Viewed file: $file_title");

                        // Display the file based on its type
                        if ($file_extension === 'pdf') {
                            // Add the blank div at the top
                            echo '<div class="blank-div"></div>';
                            // Display the PDF in an iframe
                            echo '<iframe src="' . $file_path . '"></iframe>';
                        } elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            // Display the image with a blank div
                            echo '<div class="blank-div"></div>';
                            echo '<img src="' . $file_path . '" alt="File" style="width: 100%; height: auto;">';
                        } else {
                            // Display a download link for other allowed file types
                            echo '<p>This file type is supported but cannot be previewed. <a href="' . $file_path . '" download class="download-link">Download the file</a>.</p>';
                            // Log the file download activity
                            logActivity($conn, $_SESSION['user_id'], 'download', "Downloaded file with ID: $file_id");
                        }
                    } else {
                        // File type is not allowed
                        echo '<p class="error-message">This file type is not allowed for preview or download.</p>';
                    }
                } else {
                    // File not found on the server
                    echo '<p class="error-message">File not found on the server.</p>';
                }
            } else {
                // File not found in the database
                echo '<p class="error-message">File not found in the database.</p>';
            }
        } else {
            // No file ID specified
            echo '<p class="error-message">No file specified.</p>';
        }
        ?>
    </div>
</body>

</html>