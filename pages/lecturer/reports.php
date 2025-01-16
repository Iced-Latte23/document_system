<?php require '../../processes/lecturer/reports.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../assets/styles/lecturer/reports.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="dashboard-container">
        <!-- Left Navigation Bar -->
        <?php include 'sidebar.php'; ?>

        <div class="main-content">

            <!-- Cards Grid -->
            <div class="cards-grid">
                <!-- Document Upload Trends Card -->
                <div class="card">
                    <h2>Document Upload Trends (Last 30 Days)</h2>
                    <div class="chart-container">
                        <canvas id="uploadTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Accessible vs Inaccessible Documents Card -->
                <div class="card">
                    <h2>Accessible vs Inaccessible Documents</h2>
                    <div class="chart-container">
                        <canvas id="accessibleChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Activity Logs Section -->
            <div class="activity-logs">
                <h2>Recent Activity (Last 30 Days)</h2>
                <table>
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
                                    <td style="text-align: center;"><?php echo htmlspecialchars($row['timestamp']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No recent activity found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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

        // Chart.js for Document Upload Trends
        const uploadTrendsData = {
            labels: <?php echo json_encode(array_column($upload_trends, 'upload_date')); ?>,
            datasets: [{
                label: 'Documents Uploaded',
                data: <?php echo json_encode(array_column($upload_trends, 'upload_count')); ?>,
                backgroundColor: 'rgba(26, 188, 156, 0.2)',
                borderColor: 'rgba(26, 188, 156, 1)',
                borderWidth: 2,
                fill: true
            }]
        };

        const uploadTrendsConfig = {
            type: 'line',
            data: uploadTrendsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Number of Uploads'
                        },
                        beginAtZero: true
                    }
                }
            }
        };

        const uploadTrendsChart = new Chart(
            document.getElementById('uploadTrendsChart'),
            uploadTrendsConfig
        );

        // Chart.js for Accessible vs Inaccessible Documents
        const accessibleData = {
            labels: ['Inaccessible', 'Accessible'],
            datasets: [{
                data: <?php echo json_encode($accessible_data); ?>,
                backgroundColor: ['rgba(231, 76, 60, 0.2)', 'rgba(26, 188, 156, 0.2)'],
                borderColor: ['rgba(231, 76, 60, 1)', 'rgba(26, 188, 156, 1)'],
                borderWidth: 2
            }]
        };

        const accessibleConfig = {
            type: 'pie',
            data: accessibleData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        };

        const accessibleChart = new Chart(
            document.getElementById('accessibleChart'),
            accessibleConfig
        );
    </script>
</body>

</html>