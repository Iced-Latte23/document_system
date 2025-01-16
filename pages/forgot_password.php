<?php
require '../processes/db_connect.php';

// Set the default timezone to Cambodia (Phnom Penh)
date_default_timezone_set('Asia/Phnom_Penh');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    if (empty($email)) {
        $error = "Please enter your email address.";
    } else {
        // Check if the email exists in the users table
        $stmt = $conn->prepare("SELECT id FROM tbl_users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id);
            $stmt->fetch();

            // Generate a unique token and set expiry time
            $token = bin2hex(random_bytes(50));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token expires in 1 hour
            $created_at = date("Y-m-d H:i:s");

            // Insert the token into the password_resets table
            $stmt = $conn->prepare("INSERT INTO tbl_password_resets (user_id, token, expires_at, created_at) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $token, $expiry, $created_at);
            $stmt->execute();

            // Send the reset link to the user's email
            $reset_link = "http://localhost:3000/pages/reset_password.php?token=$token"; // Use localhost for testing
            $subject = "Password Reset Request";
            $body = "Click the link below to reset your password:\n\n$reset_link";
            $headers = "From: no-reply@yourwebsite.com";

            // Configure PHP to use MailHog's SMTP server
            ini_set("SMTP", "localhost");
            ini_set("smtp_port", "1025");
            ini_set("sendmail_from", "no-reply@yourwebsite.com");

            // Send the email
            if (mail($email, $subject, $body, $headers)) {
                $message = "A password reset link has been sent to your email. Check MailHog at <a href='http://localhost:8025' target='_blank'>http://localhost:8025</a>.";
            } else {
                $error = "Failed to send the reset link. Please try again.";
            }
        } else {
            $error = "No account found with that email address.";
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
    <title>Forgot Password</title>
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

        .forgot-password-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .forgot-password-container h1 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #1abc9c;
        }

        .forgot-password-container p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 30px;
        }

        .forgot-password-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .forgot-password-form input:focus {
            border-color: #1abc9c;
            outline: none;
        }

        .forgot-password-form button {
            width: 100%;
            padding: 12px;
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }

        .forgot-password-form button:hover {
            background-color: #15967D;
        }

        .message {
            color: green;
            margin-bottom: 20px;
        }

        .error-message {
            color: #ff0000;
            background-color: #ffe6e6;
            padding: 10px;
            border: 1px solid #ff0000;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        a {
            color: #1A9CBC;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="forgot-password-container">
        <h1>Forgot Password</h1>

        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p>
            <p><a href="login.php">Back to Login</a></p>
        <?php else: ?>
            <p>Enter your email address to reset your password.</p>

            <?php if (!empty($error)): ?>
                <p style="color: #ff0000; background-color: #ffe6e6; padding: 10px; border: 1px solid #ff0000; border-radius: 5px; margin-bottom: 20px; font-size: 0.9rem; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <form class="forgot-password-form" action="forgot_password.php" method="POST">
                <input type="email" name="email" placeholder="Email Address" required>
                <button type="submit">Reset Password</button>
            </form>

            <p><a href="login.php">Back to Login</a></p>
        <?php endif; ?>
    </div>
</body>

</html>