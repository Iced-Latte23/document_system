<link rel="stylesheet" href="../../assets/styles/header_sidebar.css">

<nav class="sidebar">
    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="manage_documents.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'manage_documents.php' ? 'active' : ''; ?>">
        <i class="fas fa-file"></i> Manage Documents
    </a>
    <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
        <i class="fas fa-chart-bar"></i> Reports
    </a>
    <hr> <!-- Divider -->
    <a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>"">
        <i class="fas fa-user"></i> Profile
    </a>
    <a class="signout-button" href="../../processes/logout.php">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</nav>