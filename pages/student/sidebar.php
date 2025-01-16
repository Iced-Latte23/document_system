<link rel="stylesheet" href="../../assets/styles/header_sidebar.css">

<nav class="sidebar">
    <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="history.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>">
        <i class="fas fa-history"></i> History
    </a>
    <a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
        <i class="fas fa-user"></i> Profile
    </a>
    <a class="signout-button" href="../../processes/logout.php">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</nav>