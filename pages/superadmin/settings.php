<?php require '../../processes/superadmin/setting.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings</title>
    <link rel="stylesheet" href="../../assets/styles/superadmin/settings.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <?php include 'header.php' ?>

    <div class="dashboard-container">
        <?php include 'sidebar.php' ?>

        <div class="main-content">
            <?php require '../show_message.php'; ?>

            <!-- System Configuration Section -->
            <div class="settings-section">
                <h2>System Configuration</h2>
                <form action="settings.php" method="POST">
                    <div class="form-group">
                        <label for="app_name">Application Name:</label>
                        <input type="text" id="app_name" name="app_name" value="<?php echo htmlspecialchars($settings['app_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="max_upload_size">Maximum File Upload Size (MB):</label>
                        <input type="number" id="max_upload_size" name="max_upload_size" value="<?php echo htmlspecialchars($settings['max_upload_size']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="allowed_file_types">Allowed File Types (comma-separated):</label>
                        <input type="text" id="allowed_file_types" name="allowed_file_types" value="<?php echo htmlspecialchars($settings['allowed_file_types']); ?>" required>
                    </div>
                    <div class="form-group maintenance-mode">
                        <label for="maintenance_mode">Maintenance Mode:</label>
                        <input type="checkbox" id="maintenance_mode" name="maintenance_mode" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                    </div>
                    <button type="submit" name="update_settings" class="btn">Update System Settings</button>
                </form>
            </div>

            <!-- Backup and Restore Section -->
            <div class="settings-section">
                <h2>Backup and Restore</h2>
                <form action="settings.php" method="POST">
                    <button type="submit" name="create_backup" class="btn">Create Backup</button>
                </form>
                <form action="system_settings.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="backup_file">Restore Backup:</label>
                        <input type="file" id="backup_file" name="backup_file" accept=".sql" required>
                    </div>
                    <button type="submit" name="restore_backup" class="btn">Restore Backup</button>
                </form>
            </div>

            <!-- System Logs Section -->
            <div class="settings-section system-logs">
                <h2>System Logs</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Detail</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_activity_logs->num_rows > 0) {
                            while ($row = $result_activity_logs->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . $row["id"] . "</td>
                                        <td>" . ($row["user_name"]) . "</td>
                                        <td>" . $row["action"] . "</td>
                                        <td>" . $row["details"] . "</td>
                                        <td>" . $row["timestamp"] . "</td>
                              </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No system logs found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>