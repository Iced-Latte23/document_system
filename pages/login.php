<?php require '../processes/login.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Document Management System</title>
    <link rel="stylesheet" href="../assets/styles/login.css">
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
</body>

</html>