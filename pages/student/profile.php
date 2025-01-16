<?php
require '../../processes/lecturer/profile.php'
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Update</title>
    <link rel="stylesheet" href="../../assets/styles/lecturer/profile.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="dashboard-container">
        <!-- Left Navigation Bar -->
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <!-- Display error or success messages -->
            <?php if ($error): ?>
                <div class="alert error" id="error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert success" id="success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Profile Picture Section -->
            <div class="settings-section">
                <h2>Profile Picture</h2>
                <div class="profile-picture">
                    <img src="../../uploads/profiles/<?php echo $profile_picture ?>" alt="Profile Picture">
                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_picture" accept="image/*" required>
                        <button type="submit" name="update_profile_picture" class="btn">Update Profile Picture</button>
                    </form>
                </div>
            </div>

            <!-- Profile Update Section -->
            <div class="settings-section">
                <h2>Update Profile</h2>
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn">Update Profile</button>
                </form>
            </div>

            <!-- Password Change Section -->
            <div class="settings-section">
                <h2>Change Password</h2>
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn">Change Password</button>
                </form>
            </div>

            <!-- Two-Factor Authentication Section -->
            <div class="settings-section">
                <h2>Two-Factor Authentication</h2>
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label>Status: <?php echo $two_factor_auth ? "Enabled" : "Disabled"; ?></label>
                    </div>
                    <button type="submit" name="toggle_2fa" class="btn">
                        <?php echo $two_factor_auth ? "Disable 2FA" : "Enable 2FA"; ?>
                    </button>
                </form>
            </div>

            <!-- Activity Log Section -->
            <div class="settings-section">
                <h2>Recent Activity</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_activity->num_rows > 0) {
                            while ($row = $result_activity->fetch_assoc()) {
                                echo "<tr>
                                <td>" . htmlspecialchars($row['action']) . "</td>
                                <td>" . htmlspecialchars($row['timestamp']) . "</td>
                              </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>No recent activity.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Hide success and error messages after 3 seconds
        setTimeout(function() {
            var successMessage = document.getElementById('success');
            if (successMessage) {
                successMessage.style.display = 'none';
            }

            var errorMessage = document.getElementById('error');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 3000);
    </script>

</body>

</html>