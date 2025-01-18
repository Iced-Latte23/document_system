<?php require '../../processes/lecturer/dashboard.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link rel="stylesheet" href="../../assets/styles/lecturer/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php include 'header.php' ?>
    <div class="dashboard-container">
        <?php include 'sidebar.php' ?>

        <div class="main-content">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="javascript:void(0);" class="btn" id="openModalLink">
                        <i class="fas fa-upload"></i> Upload Document
                    </a>
                    <a href="manage_documents.php" class="btn">
                        <i class="fas fa-file"></i> Manage Documents
                    </a>
                </div>
            </div>

            <!-- Document Statistics -->
            <div class="statistics">
                <h2>Available Documents</h2>
                <section class="document-list">
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $doc): ?>
                            <div class="document-item">
                                <h3><?php echo htmlspecialchars($doc['title']); ?></h3>
                                <p class="description"><?php echo htmlspecialchars($doc['description']); ?></p>
                                <p class="author"><strong>Author:</strong> <?php echo htmlspecialchars($doc['author']); ?></p>
                                <p class="author"><strong>Public Date:</strong> <?php echo htmlspecialchars($doc['public_date']); ?></p>
                                <button class="view-button" onclick="window.location.href='../view_with_download.php?id=<?php echo htmlspecialchars($doc['id']); ?>'">View</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No documents found.</p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>

    <!-- Upload Document Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Upload Document</h2>

            <!-- Display error or success messages -->
            <?php require '../show_message.php'; ?>

            <!-- Upload Form -->
            <form action="dashboard.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="document_file">Upload Document:</label>
                    <input type="file" id="document_file" name="document_file" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
                </div>
                <button type="submit" name="add_document" class="btn">Upload and Share</button>
            </form>
        </div>
    </div>

    <script>
        // Get the modal
        const modal = document.getElementById('uploadModal');

        // Get the button that opens the modal
        const openModalBtn = document.getElementById('openModalLink');

        // Get the <span> element that closes the modal
        const closeBtn = document.querySelector('.close');

        // Function to open the modal
        function openModal() {
            modal.style.display = 'block';
        }

        // Function to close the modal
        function closeModal() {
            modal.style.display = 'none';
        }

        // Event listeners
        openModalBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);

        // Close the modal when clicking outside of it
        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });
    </script>
</body>

</html>