<?php require '../../processes/superadmin/dashboard.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/styles/superadmin/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <h3>Dashboard Overview</h3>

            <!-- Metrics Row -->
            <div class="metrics-row">
                <div class="card">
                    <h4>Total Users</h4>
                    <p><?php echo htmlspecialchars($total_users); ?></p>
                </div>
                <div class="card">
                    <h4>Total Documents</h4>
                    <p><?php echo htmlspecialchars($total_documents); ?></p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="charts-container">
                <div class="chart-container">
                    <h4>User Roles</h4>
                    <canvas id="userRolesChart"></canvas>
                </div>
                <div class="chart-container">
                    <h4>Document Types</h4>
                    <canvas id="documentTypesChart"></canvas>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_activity_logs->num_rows > 0): ?>
                            <?php while ($row = $result_activity_logs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row["id"]; ?></td>
                                    <td><?php echo htmlspecialchars($row["user_name"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["action"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["details"]); ?></td>
                                    <td><?php echo $row["timestamp"]; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">No activity logs found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // User Roles Pie Chart
        const rolesCtx = document.getElementById('userRolesChart').getContext('2d');
        new Chart(rolesCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($roles_labels); ?>,
                datasets: [{
                    label: 'User Roles',
                    data: <?php echo json_encode($roles_data); ?>,
                    backgroundColor: ['#1abc9c', '#4fd1b5', '#81C784', '#A5D6A7', '#C8E6C9'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 15, // Reduce box width
                            padding: 15, // Reduce padding
                        }
                    },
                    tooltip: {
                        enabled: true,
                    }
                }
            }
        });

        // Document Types Bar Chart
        const documentTypesCtx = document.getElementById('documentTypesChart').getContext('2d');
        new Chart(documentTypesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($document_types_labels); ?>,
                datasets: [{
                    label: 'Document Types',
                    data: <?php echo json_encode($document_types_data); ?>,
                    backgroundColor: '#1abc9c',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 15, // Reduce box width
                            padding: 15, // Reduce padding
                        }
                    },
                    tooltip: {
                        enabled: true,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
</body>

</html>