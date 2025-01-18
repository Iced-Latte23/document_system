<?php require '../../processes/outsider/dashboard.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document List</title>
    <link rel="stylesheet" href="../../assets/styles/student/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search documents...">
            </div>

            <h2>Available Documents</h2>
            <hr>

            <section class="document-list">
                <?php if (!empty($documents)): ?>
                    <?php foreach ($documents as $doc): ?>
                        <div class="document-item">
                            <h3><?php echo htmlspecialchars($doc['title']); ?></h3>
                            <p class="description"><?php echo htmlspecialchars($doc['description']); ?></p>
                            <p class="author"><strong>Author:</strong> <?php echo htmlspecialchars($doc['author']); ?></p>
                            <p class="author"><strong>Public Date:</strong> <?php echo htmlspecialchars($doc['public_date']); ?></p>
                            <button class="view-button" onclick="window.location.href='../view_page.php?id=<?php echo htmlspecialchars($doc['id']); ?>'">View</button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No documents found.</p>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <script>
        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        const documentItems = document.querySelectorAll('.document-item');

        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();

            documentItems.forEach(item => {
                const title = item.querySelector('h3').textContent.toLowerCase();
                const description = item.querySelector('p').textContent.toLowerCase();

                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>