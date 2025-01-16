<?php
// Include the database connection file
require '../processes/db_connect.php';

// Set the default timezone to Cambodia (Phnom Penh)
date_default_timezone_set('Asia/Phnom_Penh');

// Initialize messages
$success_message = '';
$error_message = '';

// Initialize variables to retain form values
$new_password = '';
$confirm_password = '';

// Check if the token is provided in the URL
if (!isset($_GET['token'])) {
    $error_message = "Invalid reset link.";
} else {
    $token = $_GET['token'];

    // Validate the token
    $stmt = $conn->prepare("SELECT user_id, expires_at FROM tbl_password_resets WHERE token = ?");
    if (!$stmt) {
        $error_message = "Database error. Please try again later.";
    } else {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $expiry);
            $stmt->fetch();

            // Check if the token has expired
            if (strtotime($expiry) < time()) {
                $error_message = "The reset link has expired. Please request a new one.";
            } else {
                // Check if the form is submitted
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $new_password = $_POST['new_password'];
                    $confirm_password = $_POST['confirm_password'];

                    // Validate passwords
                    if (empty($new_password) || empty($confirm_password)) {
                        $error_message = "Please fill in all fields.";
                    } elseif ($new_password !== $confirm_password) {
                        $error_message = "Passwords do not match.";
                    } elseif (strlen($new_password) < 8) {
                        $error_message = "Password must be at least 8 characters long.";
                    } else {
                        // Hash the new password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                        // Update the user's password
                        $stmt = $conn->prepare("UPDATE tbl_users SET password = ? WHERE id = ?");
                        if (!$stmt) {
                            $error_message = "Database error. Please try again later.";
                        } else {
                            $stmt->bind_param("si", $hashed_password, $user_id);
                            if ($stmt->execute()) {
                                // Delete the token from the password_resets table
                                $stmt = $conn->prepare("DELETE FROM tbl_password_resets WHERE token = ?");
                                $stmt->bind_param("s", $token);
                                $stmt->execute();

                                $success_message = "Your password has been reset successfully. <a href='login.php'>Login here</a>.";
                            } else {
                                $error_message = "Failed to update password. Please try again.";
                            }
                        }
                    }
                }
            }
        } else {
            $error_message = "Invalid reset link.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .reset-password-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .reset-password-container h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #1abc9c;
        }

        .reset-password-container p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 30px;
        }

        .reset-password-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .reset-password-form input:focus {
            border-color: #1abc9c;
            outline: none;
        }

        .reset-password-form button {
            width: 100%;
            padding: 12px;
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .reset-password-form button:hover {
            background-color: #45a049;
        }

        .notify {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 1rem;
            text-align: left;
            padding: 0.5rem;
            background-color: #f9f9f9;
            border-left: 4px solid #1abc9c;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notify::before {
            content: "ℹ️";
            font-size: 1rem;
        }

        /* Error Message Container */
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ef9a9a;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Success Message Container */
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #a5d6a7;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Close Button */
        .close-button {
            margin-left: auto;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        .error-message .close-button {
            color: #c62828;
        }

        .error-message .close-button:hover {
            color: #b71c1c;
        }

        .success-message .close-button {
            color: #2e7d32;
        }

        .success-message .close-button:hover {
            color: #1b5e20;
        }
    </style>
</head>

<body>
    <div class="reset-password-container">
        <h1>Reset Password</h1>

        <!-- Display Success Message -->
        <?php if ($success_message): ?>
            <div class="success-message" id="successMessage">
                <span><?php echo $success_message; ?></span>
            </div>
        <?php endif; ?>

        <!-- Display Error Message -->
        <?php if ($error_message): ?>
            <div class="error-message" id="errorMessage">
                <span><?php echo $error_message; ?></span>
            </div>
        <?php endif; ?>

        <!-- Reset Password Form -->
        <?php if (empty($success_message)): ?>
            <form class="reset-password-form" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <p class="notify">Must be at least 8 characters length</p>
                <input type="password" name="new_password" placeholder="New Password" value="<?php echo htmlspecialchars($new_password); ?>" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" value="<?php echo htmlspecialchars($confirm_password); ?>" required>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>

        <!-- Back to Login Link (only show if not successful) -->
        <?php if (empty($success_message)): ?>
            <p><a href="login.php">Back to Login</a></p>
        <?php endif; ?>
    </div>

    <script>
        // Automatically hide messages after 5 seconds
        setTimeout(() => {
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage) errorMessage.style.display = 'none';
        }, 2500);
    </script>
</body>

</html>