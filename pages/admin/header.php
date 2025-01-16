<link rel="stylesheet" href="/../assets/styles/header_sidebar.css">

<header>
    <div class="logo">
        <h1><?php echo htmlspecialchars($settings['app_name']); ?></h1>
    </div>
    <div class="header-right">
        <div class="profile">
            <div class="greeting" onclick="toggleDropdown()">
                Hello, <?php echo htmlspecialchars($_SESSION['name']); ?>
            </div>
        </div>
    </div>
</header>