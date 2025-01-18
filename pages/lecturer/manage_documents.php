<?php require '../../processes/lecturer/manage_documents.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/styles/lecturer/manage_documents.css">
    <link rel="stylesheet" href="../../assets/styles/search.css">
</head>

<body>
    <?php include 'header.php' ?>

    <div class="dashboard-container">
        <!-- Left Navigation Bar -->
        <?php include 'sidebar.php' ?>

        <div class="main-content">
            <!-- Display error or success messages -->
            <?php require '../show_message.php'; ?>

            <!-- Search Bar and Upload Button -->
            <div class="search-bar">
                <!-- Search Input -->
                <div class="search-input-container">
                    <input type="text" id="searchInput" placeholder="Search by Title..." oninput="filterDocuments()">
                </div>

                <!-- Date Input -->
                <div class="date-input-container">
                    <input type="date" id="filterDate" placeholder="Select Date" onchange="filterDocuments()">
                </div>

                <!-- Clear Filters and Add Document Buttons -->
                <div class="button-container">
                    <button class="button clear-filters-button" onclick="clearFilters()">
                        Clear Filters
                    </button>
                    <button class="button add-button" onclick="openUploadModal()">
                        Upload Document
                    </button>
                </div>
            </div>

            <!-- Document List -->
            <table class="document-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Author</th>
                        <th>Public Date</th>
                        <th>Uploaded At</th>
                        <th>Accessible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="tr-data">
                                <td class="description"> <a class="title-a" href="../view_with_download.php?id=<?php echo intval($row['id']); ?>"> <?php echo htmlspecialchars($row['title']); ?></a></td>
                                <td class="description"><?php echo htmlspecialchars($row['description']); ?></td>
                                <td class="description"><?php echo htmlspecialchars($row['author']) ?></td>
                                <td><?php echo htmlspecialchars($row['public_date']) ?></td>
                                <td class="upload-date"><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
                                <td class="accessible">
                                    <form method="POST" action="manage_documents.php" style="display:inline;" class="toggle-accessibility-form" data-document-id="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="document_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="is_accessible" value="<?php echo $row['is_accessible'] ? '0' : '1'; ?>">
                                        <input type="hidden" name="toggle_accessibility" value="1">
                                        <label class="switch">
                                            <input type="checkbox" <?php echo $row['is_accessible'] ? 'checked' : ''; ?> onchange="toggleAccessibility(this)">
                                            <span class="slider"></span>
                                        </label>
                                    </form>
                                </td>
                                <td class="actions">
                                    <a onclick="openDetailModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['public_date'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8'); ?>')" class="btn view">Detail</a>
                                    <a href="#" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['title']); ?>', '<?php echo htmlspecialchars($row['author']); ?>', '<?php echo htmlspecialchars($row['description']); ?>', '<?php echo htmlspecialchars($row['public_date']); ?>')" class="btn edit">Edit</a>
                                    <a href="#" class="btn delete" onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No documents found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Document Modal -->
    <div id="uploadModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Document</div>
            <form id="addDocumentForm" method="POST" enctype="multipart/form-data">
                <div class="form-container">
                    <!-- Left Column -->
                    <div class="left-column">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="author">Author:</label>
                            <input type="text" id="author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="public_date">Public Date:</label>
                            <input type="date" id="public_date" name="public_date" required>
                        </div>
                        <div class="form-group">
                            <label for="file">File:</label>
                            <input type="file" id="file" name="file" required>
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="right-column">
                        <div class="form-group" style="height: 332px;">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-button" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="add-button" name="add_document">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Document Modal -->
    <div id="editDocumentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Edit Document</div>
            <form id="editDocumentForm" method="POST" enctype="multipart/form-data">
                <div class="form-container">
                    <!-- Left Column -->
                    <div class="left-column">
                        <input type="hidden" id="document_id" name="document_id">
                        <div class="form-group">
                            <label for="edit_title">Title:</label>
                            <input type="text" id="edit_title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_author">Author:</label>
                            <input type="text" id="edit_author" name="author" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_public_date">Public Date:</label>
                            <input type="date" id="edit_public_date" name="public_date" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_file">File (Leave blank to keep current file):</label>
                            <input type="file" id="edit_file" name="file">
                        </div>
                    </div>
                    <!-- Right Column -->
                    <div class="right-column">
                        <div class="form-group" style="height: 332px;">
                            <label for="edit_description">Description:</label>
                            <textarea id="edit_description" name="description" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-button" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="add-button" name="edit_document">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Document Modal -->
    <div id="detailDocumentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Document Detail</div>
            <form id="detailDocumentForm" method="POST" enctype="multipart/form-data">

                <div class="form-container">
                    <div class="left-column">
                        <input type="hidden" id="document_id" name="document_id">
                        <div class="form-group">
                            <label for="detail_title">Title:</label>
                            <input type="text" id="detail_title" name="title" readonly>
                        </div>
                        <div class="form-group">
                            <label for="detail_author">Author:</label>
                            <input type="text" id="detail_author" name="author" readonly>
                        </div>
                        <div class="form-group">
                            <label for="detail_public_date">Public Date:</label>
                            <input type="date" id="detail_public_date" name="public_date" readonly>
                        </div>
                        <div class="form-group">
                            <label for="detail_file">File:</label>
                            <input type="text" id="detail-file" name="file" readonly>
                        </div>
                    </div>
                    <div class="right-column">
                        <div class="form-group" style="height: 332px;">
                            <label for="detail_description">Description:</label>
                            <textarea id="detail_description" name="description" readonly></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-button" onclick="closeDetailModal()">Close</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to delete the document titled "<span id="documentTitle"></span>"? This action cannot be undone.</p>
            <button class="btn-delete" onclick="deleteDocument()">Yes, Delete</button>
            <button class="btn btn-cancel" onclick="closeModal()">Cancel</button>
        </div>
    </div>

    <script>
        // Search Functionality
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('.document-table tbody tr');

        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.toLowerCase();

            tableRows.forEach(row => {
                const title = row.cells[0].textContent.toLowerCase(); // Title column
                const description = row.cells[1].textContent.toLowerCase(); // Description column
                const author = row.cells[2].textContent.toLowerCase(); // Author column

                // Check if the search term matches any of the columns
                if (title.includes(searchTerm) || description.includes(searchTerm) || author.includes(searchTerm)) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; // Hide the row
                }
            });
        });

        // Filter documents based on search input and selected date
        function filterDocuments() {
            // Get filter values
            const searchInput = document.getElementById("searchInput").value.toLowerCase();
            const filterDate = document.getElementById("filterDate").value;

            // Debugging logs to verify filter values
            console.log("Search Input:", searchInput);
            console.log("Filter Date:", filterDate);

            // Loop through each row in the documents table
            document.querySelectorAll(".document-table tbody tr").forEach(row => {
                // Extract data from the row
                const title = row.cells[0].textContent.toLowerCase(); // Title column (index 0)
                const uploadedAt = row.cells[3].textContent.split(' ')[0]; // Uploaded At column (index 3)

                // Debugging logs to verify row data
                console.log("Row Title:", title);
                console.log("Row Uploaded Date:", uploadedAt);

                // Check if the row matches the search input (title only)
                const matchesSearch = title.includes(searchInput);

                // Check if the row matches the date filter
                let matchesDate = true;
                if (filterDate) {
                    matchesDate = uploadedAt === filterDate;
                }

                // Debugging logs to verify the matchesSearch and matchesDate conditions
                console.log("Matches Search:", matchesSearch);
                console.log("Matches Date:", matchesDate);

                // Show or hide the row based on all filters
                if (matchesSearch && matchesDate) {
                    row.style.display = ""; // Show the row
                } else {
                    row.style.display = "none"; // Hide the row
                }
            });
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById("searchInput").value = "";
            document.getElementById("filterDate").value = "";
            filterDocuments(); // Reapply filtering to show all rows
        }

        // Function to toggle accessibility without refreshing the page
        function toggleAccessibility(checkbox) {
            const form = checkbox.closest('.toggle-accessibility-form');
            const documentId = form.getAttribute('data-document-id');
            const isAccessible = checkbox.checked ? 1 : 0;

            // Create a FormData object
            const formData = new FormData();
            formData.append('document_id', documentId);
            formData.append('is_accessible', isAccessible);
            formData.append('toggle_accessibility', 1);

            // Send the AJAX request
            fetch('manage_documents.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    console.log('Success:', data);
                    // Optionally, update the UI or show a success message
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Optionally, revert the checkbox state or show an error message
                });
        }

        // Open Upload Modal
        function openUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.style.display = 'flex';
        }

        // Close Upload Modal
        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.style.display = 'none';
        }

        // Open Edit Document Modal
        function openEditModal(id, title, author, description, public_date) {
            document.getElementById('document_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_author').value = author;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_public_date').value = public_date
            document.getElementById('edit_file').value = '';
            document.getElementById('editDocumentModal').style.display = 'flex';
        }

        // Close Edit Document Modal
        function closeEditModal() {
            document.getElementById('editDocumentModal').style.display = 'none';
        }

        function openDetailModal(id, title, author, description, public_date, file_path) {
            // Set the values in the detail modal form
            document.getElementById('document_id').value = id;
            document.getElementById('detail_title').value = title;
            document.getElementById('detail_author').value = author;
            document.getElementById('detail_description').value = description;
            document.getElementById('detail_public_date').value = public_date;
            document.getElementById('detail-file').value = file_path; // Clear the file field
            // Display the detail modal
            document.getElementById('detailDocumentModal').style.display = 'flex';
        }

        // Close Edit Document Modal
        function closeDetailModal() {
            document.getElementById('detailDocumentModal').style.display = 'none';
        }


        // Confirmation Modal
        let documentIdToDelete;
        let documentTitleToDelete;

        function confirmDelete(id, title) {
            documentIdToDelete = id;
            documentTitleToDelete = title;

            // Update the modal content with the document title
            document.getElementById('documentTitle').textContent = title;

            // Display the modal
            document.getElementById('confirmationModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        function deleteDocument() {
            if (documentIdToDelete) {
                // Create a form dynamically
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'manage_documents.php';

                // Add the document ID as a hidden input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'document_id';
                input.value = documentIdToDelete;
                form.appendChild(input);

                // Add the delete_document flag
                const deleteFlag = document.createElement('input');
                deleteFlag.type = 'hidden';
                deleteFlag.name = 'delete_document';
                deleteFlag.value = '1';
                form.appendChild(deleteFlag);

                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const uploadModal = document.getElementById('uploadModal');
            const editModal = document.getElementById('editDocumentModal');
            const confirmationModal = document.getElementById('confirmationModal');
            if (event.target === uploadModal) {
                uploadModal.style.display = 'none';
            }
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
            if (event.target === confirmationModal) {
                confirmationModal.style.display = 'none';
            }
        };

        // Hide success and error messages after 3 seconds
        setTimeout(function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }

            var errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        }, 2000);
    </script>
</body>

</html>