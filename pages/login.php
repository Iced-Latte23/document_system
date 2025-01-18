<?php require '../processes/login.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Document Management System</title>
    <link rel="stylesheet" href="../assets/styles/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Login Container -->
    <div class="login-container">
        <h1>Login</h1>
        <p>Welcome back! Please log in to access your account.</p>

        <!-- Display error message -->
        <?php require 'show_message.php'; ?>

        <!-- Login Form -->
        <form class="login-form" action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        <p><a href="forgot_password.php">Forgot Password?</a></p>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpModalLabel">OTP Verification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>An OTP has been sent to your email. Please enter it below:</p>
                    <form id="otpForm" method="POST" action="../processes/verify_otp.php">
                        <div class="mb-3">
                            <label for="otp" class="form-label">OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Verify OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Bootstrap JS for modal functionality -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show the OTP modal if 2FA is required
        <?php if (isset($_SESSION['2fa_required']) && $_SESSION['2fa_required']): ?>
            document.addEventListener('DOMContentLoaded', function() {
                var otpModal = new bootstrap.Modal(document.getElementById('otpModal'));
                otpModal.show();
            });
        <?php endif; ?>
    </script>
</body>

</html>