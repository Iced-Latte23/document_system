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

                <div class="search-bar">
                    <!-- Search Input -->
                    <div class="search-input-container">
                        <input type="text" id="searchInput" placeholder="Search by Title..." oninput="filterDocuments()">
                    </div>

                    <div class="filter-container">
                        <select id="filterActionType" onchange="filterDocuments()">
                            <option value="">All Action Types</option>
                            <?php
                            // Loop through the action types and create options
                            foreach ($actionTypes as $actionType) {
                                echo "<option value='$actionType'>$actionType</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Date Input -->
                    <div class="date-input-container">
                        <input type="date" id="filterDate" placeholder="Select Date" onchange="filterDocuments()">
                    </div>
                    <!-- Clear Filter Button -->
                    <button id="clearFilterButton" onclick="clearFilters()">Clear Filters</button>
                </div>
                <table id="documentsTable">
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

        function filterDocuments() {
            // Get input values
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const filterActionType = document.getElementById('filterActionType').value.toLowerCase();
            const filterDate = document.getElementById('filterDate').value;

            // Get all table rows
            const table = document.getElementById('documentsTable');
            const rows = table.getElementsByTagName('tr');

            // Loop through all rows (skip the header row)
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');

                // Extract data from each cell
                const action = cells[0].textContent.toLowerCase(); // Action column (index 0)
                const details = cells[1].textContent.toLowerCase(); // Details column (index 1)
                const timestamp = cells[2].textContent; // Timestamp column (index 2)

                // Check if the row matches the search criteria
                const matchesSearch = details.includes(searchInput); // Search in the Details column
                const matchesActionType = filterActionType === '' || action === filterActionType; // Filter by Action column
                const matchesDate = filterDate === '' || timestamp.includes(filterDate); // Filter by Timestamp column

                // Show or hide the row based on the criteria
                if (matchesSearch && matchesActionType && matchesDate) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterActionType').value = '';
            document.getElementById('filterDate').value = '';
            filterDocuments();
        }
    </script>
</body>

</html>