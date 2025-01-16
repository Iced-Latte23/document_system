<?php require '../../processes/student/history.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/styles/student/history.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="dashboard-container">
        <!-- Left Navigation Bar -->
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <h2>Activity History</h2>

            <!-- History Table -->
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Details</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_activity->num_rows > 0): ?>
                        <?php while ($row = $result_activity->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['action']); ?></td>
                                <td><?php echo htmlspecialchars($row['details']); ?></td>
                                <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No activity history found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Toggle dropdown for profile
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdown');
            const profile = document.querySelector('.profile');
            if (!profile.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>

</html>