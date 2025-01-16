<?php require '../processes/signup.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../assets/styles/signup.css">
</head>

<body>
    <div class="signup-container">
        <h1>Sign Up</h1>
        <p>Create an account to access the <?php echo $settings['app_name'] ?>.</p>

        <!-- Display general error message -->
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form class="signup-form" action="signup.php" method="POST">
            <!-- Name Field -->
            <input
                type="text"
                name="name"
                placeholder="Full Name"
                value="<?php echo htmlspecialchars($name ?? ''); ?>"
                class="<?php echo !empty($field_errors['name']) ? 'error' : ''; ?>"
                required>
            <?php if (!empty($field_errors['name'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($field_errors['name']); ?></p>
            <?php endif; ?>

            <!-- Email Field -->
            <input
                type="email"
                name="email"
                placeholder="Email Address"
                value="<?php echo htmlspecialchars($email ?? ''); ?>"
                class="<?php echo !empty($field_errors['email']) ? 'error' : ''; ?>"
                required>
            <?php if (!empty($field_errors['email'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($field_errors['email']); ?></p>
            <?php endif; ?>

            <!-- Password Field -->
            <input
                type="password"
                name="password"
                placeholder="Password (min 8 characters)"
                class="<?php echo !empty($field_errors['password']) ? 'error' : ''; ?>"
                required>
            <?php if (!empty($field_errors['password'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($field_errors['password']); ?></p>
            <?php endif; ?>

            <!-- Confirm Password Field -->
            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm Password"
                class="<?php echo !empty($field_errors['confirm_password']) ? 'error' : ''; ?>"
                required>
            <?php if (!empty($field_errors['confirm_password'])): ?>
                <p class="error-message"><?php echo htmlspecialchars($field_errors['confirm_password']); ?></p>
            <?php endif; ?>

            <button type="submit">Sign Up</button>
        </form>

        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>

</html>