<link rel="stylesheet" href="../../assets/styles/header_sidebar.css">

<?php
// Fetch system settings from the database
$sql_settings = "SELECT * FROM tbl_system_settings WHERE id = 1";
$result_settings = $conn->query($sql_settings);
$settings = $result_settings->fetch_assoc();
?>


<header>
    <div class="logo">
        <h1><?php echo $settings['app_name'] ?></h1>
    </div>
    <div class="header-right">
        <div class="profile">
            <div class="greeting" onclick="toggleDropdown()">
                Hello, <?php echo htmlspecialchars($_SESSION['name']); ?>
            </div>
        </div>
    </div>
</header>