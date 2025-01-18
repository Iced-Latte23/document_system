<?php
// Include required files
require '../processes/db_connect.php';
require '../processes/session_check.php';

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
}

// Handle comment submission via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        // Insert the comment into the database
        $sql = "INSERT INTO tbl_comments (document_id, user_id, comment, timestamp) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iis', $file_id, $user_id, $comment);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Fetch the username and timestamp for the new comment
            $sql = "SELECT u.name, c.timestamp 
                    FROM tbl_comments c 
                    JOIN tbl_users u ON c.user_id = u.id 
                    WHERE c.id = LAST_INSERT_ID()";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            // Return a JSON response
            echo json_encode([
                'success' => true,
                'username' => $row['name'],
                'timestamp' => $row['timestamp'],
                'comment' => $comment
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit comment.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty.']);
    }
    exit; // Stop further execution for AJAX requests
}
?>
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
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .container {
            width: 90%;
            max-width: 1550px;
            height: 90vh;
            display: flex;
            gap: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
        }

        /* Left Column (File Display) */
        .file-display {
            flex: 4;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-sizing: border-box;
        }

        /* Iframe for PDF */
        iframe {
            width: 100%;
            height: 605px;
            border: none;
            background-color: #fff;
        }

        /* Error Message */
        .error-message {
            color: red;
            font-size: 18px;
            margin-top: 20px;
        }

        /* Right Column (Comment Section) */
        .comment-section {
            flex: 1.2;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 100%;
            /* Take full height of the container */
            position: relative;
            box-sizing: border-box;
        }

        .comment-section h2 {
            margin-top: 0;
        }

        .comment-form {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            background-color: #fff;
            padding: 10px;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        .comment-form textarea {
            width: 100%;
            height: 80px;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: none;
            box-sizing: border-box;
        }

        .comment-form button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .comment-form button:hover {
            background-color: #0056b3;
        }

        .comments-list {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 165px;
            padding-right: 10px;
        }

        .comment {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .comment:last-child {
            border-bottom: none;
        }

        .comment .author {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .comment .timestamp {
            font-size: 12px;
            color: #888;
        }

        /* Media Queries for Responsiveness */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
            }

            .file-display,
            .comment-section {
                width: 100%;
                height: auto;
            }

            iframe {
                height: 500px;
            }

            .comment-section {
                position: static;
            }

            .comment-form {
                position: static;
                margin-top: 20px;
            }

            .comments-list {
                margin-bottom: 0;
            }
        }

        @media (max-width: 480px) {
            iframe {
                height: 400px;
                /* Adjust iframe height for smaller screens */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Left Column: File Display -->
        <div class="file-display">
            <?php
            if (isset($file_path)) {
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
                            // Display the PDF in an iframe
                            echo '<iframe src="' . $file_path . '"></iframe>';
                        } elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            // Display the image
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
                // No file ID specified
                echo '<p class="error-message">No file specified.</p>';
            }
            ?>
        </div>

        <!-- Right Column: Comment Section -->
        <div class="comment-section">
            <h2>Comments</h2>
            <!-- Comments List -->
            <div id="comments-list" class="comments-list">
                <?php
                // Fetch comments for this file
                if (isset($file_id)) {
                    $sql = "SELECT c.comment, u.name, c.timestamp
                            FROM tbl_comments c
                            JOIN tbl_users u ON c.user_id = u.id
                            WHERE c.document_id = ?
                            ORDER BY c.timestamp DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $file_id);
                    $stmt->execute();
                    $stmt->bind_result($comment, $username, $timestamp);

                    while ($stmt->fetch()) {
                        echo '<div class="comment">';
                        echo '<div class="author">' . htmlspecialchars($username) . '</div>';
                        echo '<div class="timestamp">' . htmlspecialchars($timestamp) . '</div>';
                        echo '<div class="comment-text">' . htmlspecialchars($comment) . '</div>';
                        echo '</div>';
                    }

                    $stmt->close();
                }
                ?>
            </div>

            <!-- Comment Form -->
            <form id="comment-form" class="comment-form" method="POST">
                <textarea name="comment" placeholder="Add a comment..." required></textarea>
                <input type="hidden" name="file_id" value="<?php echo isset($file_id) ? $file_id : ''; ?>">
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('comment-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Get form data
            const formData = new FormData(this);

            // Send AJAX request
            fetch('', { // Submit to the same file
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json()) // Parse the JSON response
                .then(data => {
                    if (data.success) {
                        // Create a new comment element
                        const commentDiv = document.createElement('div');
                        commentDiv.className = 'comment';

                        const authorDiv = document.createElement('div');
                        authorDiv.className = 'author';
                        authorDiv.textContent = data.username; // Use the username from the response

                        const timestampDiv = document.createElement('div');
                        timestampDiv.className = 'timestamp';
                        timestampDiv.textContent = data.timestamp; // Use the timestamp from the response

                        const commentTextDiv = document.createElement('div');
                        commentTextDiv.className = 'comment-text';
                        commentTextDiv.textContent = data.comment; // Use the comment from the response

                        // Append elements to the comment container
                        commentDiv.appendChild(authorDiv);
                        commentDiv.appendChild(timestampDiv);
                        commentDiv.appendChild(commentTextDiv);

                        // Prepend the new comment to the comments list
                        const commentsList = document.getElementById('comments-list');
                        commentsList.prepend(commentDiv);

                        // Clear the textarea
                        document.querySelector('#comment-form textarea').value = '';

                        // Scroll the comments list to the top smoothly
                        commentsList.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    } else {
                        alert('Error: ' + data.message); // Show an error message
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>
