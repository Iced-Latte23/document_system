<?php require '../../processes/superadmin/report.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="../../assets/styles/superadmin/reports.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'header.php' ?>

    <div class="dashboard-container">
        <?php include 'sidebar.php' ?>

        <!-- Main Content -->
        <div class="container">
            <h2>Document Summary</h2>

            <!-- First Row: Total Documents and Recent Documents -->
            <div class="row">
                <div class="report-card">
                    <h3>Total Documents</h3>
                    <p><?php echo $total_documents; ?></p>
                </div>
                <div class="report-card">
                    <h3>Documents Uploaded in the Last 7 Days</h3>
                    <p><?php echo $recent_documents; ?></p>
                </div>
            </div>

            <!-- Second Row: Documents by User -->
            <div class="row">
                <div class="report-card">
                    <h3>Documents by User</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Document Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_documents_by_user->num_rows > 0) {
                                while ($row = $result_documents_by_user->fetch_assoc()) {
                                    echo "<tr>
                                    <td>" . htmlspecialchars($row['name']) . "</td>
                                    <td class='td-center'>" . $row['document_count'] . "</td>
                                  </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2'>No documents found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Third Row: Documents by File Type -->
            <div class="row">
                <div class="report-card">
                    <h3>Documents by File Type</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>File Type</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_documents_by_type->num_rows > 0) {
                                while ($row = $result_documents_by_type->fetch_assoc()) {
                                    echo "<tr>
                                    <td>" . htmlspecialchars($row['file_type']) . "</td>
                                    <td class='td-center'>" . $row['type_count'] . "</td>
                                  </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2'>No documents found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Fourth Row: Activity Logs -->
            <div class="row">
                <div class="report-card">
                    <h3>Recent Activity Logs</h3>
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
                                echo "<tr><td colspan='4'>No activity logs found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>