<?php require '../../processes/superadmin/manage_documents.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Documents</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/styles/superadmin/manage_documents.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <h3>Manage Documents</h3>

            <!-- Search Bar -->
            <div class="search-bar">
                <!-- Search Input -->
                <div class="search-input-container">
                    <input type="text" id="searchInput" placeholder="Search by ID, Title, Description, or Author..." oninput="filterDocuments()">
                </div>

                <!-- Filters, Date Range, and Buttons -->
                <div class="filters-and-buttons-row">
                    <!-- Filter by File Type -->
                    <div class="filter-container">
                        <select id="filterFileType" onchange="filterDocuments()">
                            <option value="">All File Types</option>
                            <?php
                            // Loop through the file types and create options
                            foreach ($fileTypes as $fileType) {
                                echo "<option value='$fileType'>$fileType</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Select Date Type (Created At or Updated At) -->
                    <div class="filter-container">
                        <select id="filterDateType" onchange="filterDocuments()">
                            <option value="">Select Date Type</option>
                            <option value="created_at">Uploaded Date</option>
                            <option value="updated_at">Updated Date</option>
                        </select>
                    </div>

                    <!-- Date Range Inputs -->
                    <div class="date-range-container">
                        <input type="text" id="datePicker" placeholder="Select Date or Date Range">
                        <input type="hidden" id="filterStartDate">
                        <input type="hidden" id="filterEndDate">
                    </div>

                    <!-- Clear Filters and Add Document Buttons -->
                    <div class="button-container">
                        <button class="button clear-filters-button" onclick="clearFilters()">
                            Clear Filters
                        </button>
                        <button class="button add-button" onclick="openAddModal()">
                            Upload Document
                        </button>
                    </div>
                </div>
            </div>

            <!-- Display Messages -->
            <?php if ($success_message): ?>
                <div class="notification success-message" id="successMessage"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="notification error-message" id="errorMessage"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Documents Table -->
            <table id="documentsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Author</th>
                        <th>File Path</th>
                        <th>File Type</th>
                        <th>Uploaded By</th>
                        <th>Accessible</th>
                        <th>Uploaded At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row["id"]); ?></td>
                                <td class="description"><?php echo htmlspecialchars($row["title"]); ?></td>
                                <td class="description"><?php echo htmlspecialchars($row["description"]); ?></td>
                                <td class="description"><?php echo htmlspecialchars($row["author"]); ?></td>
                                <td>
                                    <a href="../view_with_download.php?id=<?php echo intval($row['id']); ?>" class="btn view" target="_blank">View</a>
                                </td>
                                <td><?php echo htmlspecialchars($row["file_type"]); ?></td>
                                <td><?php echo htmlspecialchars($row["uploaded_by_name"]); ?></td>
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
                                <td><?php echo (new DateTime($row["uploaded_at"]))->format('Y-m-d'); ?></td>
                                <td><?php echo (new DateTime($row["updated_at"]))->format('Y-m-d'); ?></td>
                                <td class="actions">
                                    <button class="button detail-button" onclick="openDetailModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['file_path'], ENT_QUOTES, 'UTF-8'); ?>')">Detail</button>
                                    <button class="button edit-button" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?>')">Edit</button>
                                    <button class="button delete-button" onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>')">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">No documents found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Document Modal -->
    <div id="addDocumentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add Document</div>
            <form id="addDocumentForm" method="POST" enctype="multipart/form-data">
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
                    <label for="file">File:</label>
                    <input type="file" id="file" name="file" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="button add-button" name="add_document">Upload</button>
                    <button type="button" class="button cancel-button" onclick="closeAddModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Document Modal -->
    <div id="editDocumentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Edit Document</div>
            <form id="editDocumentForm" method="POST" enctype="multipart/form-data">
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
                    <label for="edit_description">Description:</label>
                    <textarea id="edit_description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_file">File (Leave blank to keep current file):</label>
                    <input type="file" id="edit_file" name="file">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="button add-button" name="edit_document">Update</button>
                    <button type="button" class="button cancel-button" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Document Modal -->
    <div id="detailDocumentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Document Detail</div>
            <form id="detailDocumentForm" method="POST" enctype="multipart/form-data">
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
                    <label for="detail_description">Description:</label>
                    <textarea id="detail_description" name="description" readonly></textarea>
                </div>
                <div class="form-group">
                    <label for="detail_file">File:</label>
                    <input type="text" id="detail-file" name="file" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button cancel-button" onclick="closeDetailModal()">Close</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="confirmation-modal">
        <div class="modal-content">
            <p>Are you sure you want to delete the document titled "<span id="documentTitle"></span>"? This action cannot be undone.</p>
            <hr>
            <div class="confirm-footer">
                <button class="btn btn-cancel" onclick="closeModal()">Cancel</button>
                <button class="btn-delete" onclick="deleteDocument()">Yes, Delete</button>
            </div>
        </div>
    </div>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
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

        let lastClickedDate = null; // Track the last clicked date
        let clickTimeout = null; // Track the timeout for double-click detection

        // Initialize Flatpickr for date range selection
        flatpickr("#datePicker", {
            mode: "range", // Enable range selection
            dateFormat: "Y-m-d", // Set date format to YYYY-MM-DD
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                // Add double-click event listener to each date element
                dayElem.addEventListener("dblclick", function() {
                    const selectedDate = dayElem.dateObj; // Get the selected date object
                    const utcDate = new Date(selectedDate.getTime() - (selectedDate.getTimezoneOffset() * 60000)).toISOString().split('T')[0];

                    // Set both start and end dates to the selected date
                    document.getElementById("filterStartDate").value = utcDate;
                    document.getElementById("filterEndDate").value = utcDate;

                    // Close the date picker
                    fp.close();

                    // Call the filter function
                    filterDocuments();
                });

                // Add single-click event listener to each date element
                dayElem.addEventListener("click", function() {
                    const selectedDate = dayElem.dateObj; // Get the selected date object
                    const utcDate = new Date(selectedDate.getTime() - (selectedDate.getTimezoneOffset() * 60000)).toISOString().split('T')[0];

                    // Check if the same date was clicked twice within a short time (double-click)
                    if (lastClickedDate === utcDate) {
                        // Clear the timeout
                        clearTimeout(clickTimeout);

                        // Set both start and end dates to the selected date
                        document.getElementById("filterStartDate").value = utcDate;
                        document.getElementById("filterEndDate").value = utcDate;

                        // Close the date picker
                        fp.close();

                        // Call the filter function
                        filterDocuments();
                    } else {
                        // Set the last clicked date
                        lastClickedDate = utcDate;

                        // Set a timeout to reset the last clicked date
                        clickTimeout = setTimeout(() => {
                            lastClickedDate = null;
                        }, 300); // 300ms delay for double-click detection
                    }
                });
            },
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    // Single date selected
                    const selectedDate = selectedDates[0]; // Get the selected date object
                    const utcDate = new Date(selectedDate.getTime() - (selectedDate.getTimezoneOffset() * 60000)).toISOString().split('T')[0];

                    document.getElementById("filterStartDate").value = utcDate;
                    document.getElementById("filterEndDate").value = utcDate;
                } else if (selectedDates.length === 2) {
                    // Date range selected
                    const startDate = new Date(selectedDates[0].getTime() - (selectedDates[0].getTimezoneOffset() * 60000)).toISOString().split('T')[0];
                    const endDate = new Date(selectedDates[1].getTime() - (selectedDates[1].getTimezoneOffset() * 60000)).toISOString().split('T')[0];

                    document.getElementById("filterStartDate").value = startDate;
                    document.getElementById("filterEndDate").value = endDate;
                } else {
                    // No date selected
                    document.getElementById("filterStartDate").value = "";
                    document.getElementById("filterEndDate").value = "";
                }

                // Call the filter function
                filterDocuments();
            }
        });

        // Filter documents based on search input, file type, and date range
        function filterDocuments() {
            // Get filter values
            const searchInput = document.getElementById("searchInput").value.toLowerCase();
            const filterFileType = document.getElementById("filterFileType").value.toLowerCase();
            const filterDateType = document.getElementById("filterDateType").value;
            const filterStartDate = document.getElementById("filterStartDate").value;
            const filterEndDate = document.getElementById("filterEndDate").value;

            // Debugging logs to verify filter values
            console.log("Filter Start Date:", filterStartDate);
            console.log("Filter End Date:", filterEndDate);

            // Loop through each row in the documents table
            document.querySelectorAll("#documentsTable tbody tr").forEach(row => {
                // Extract data from the row
                const id = row.cells[0].textContent.toLowerCase();
                const title = row.cells[1].textContent.toLowerCase();
                const description = row.cells[2].textContent.toLowerCase();
                const author = row.cells[3].textContent.toLowerCase();
                const fileType = row.cells[5].textContent.toLowerCase(); // File type column
                const uploadedAt = row.cells[8].textContent.split(' ')[0]; // Extract date part only
                const updatedAt = row.cells[9].textContent.split(' ')[0]; // Extract date part only

                // Determine which date to use based on the selected date type
                const dateToFilter = filterDateType === "created_at" ? uploadedAt : updatedAt;

                // Debugging logs to verify the date being filtered
                console.log("Date to Filter:", dateToFilter);

                // Check if the row matches the search input
                const matchesSearch = id.includes(searchInput) ||
                    title.includes(searchInput) ||
                    description.includes(searchInput) ||
                    author.includes(searchInput);

                // Check if the row matches the selected file type
                const matchesFileType = filterFileType === "" || fileType === filterFileType;

                // Check if the row matches the date filter
                let matchesDate = true;
                if (filterDateType && filterStartDate && filterEndDate) {
                    if (filterStartDate === filterEndDate) {
                        // Single date filter
                        matchesDate = dateToFilter === filterStartDate;
                    } else {
                        // Date range filter
                        matchesDate = dateToFilter >= filterStartDate && dateToFilter <= filterEndDate;
                    }
                }

                // Debugging logs to verify the matchesDate condition
                console.log("Matches Date:", matchesDate);

                // Show or hide the row based on all filters
                if (matchesSearch && matchesFileType && matchesDate) {
                    row.style.display = ""; // Show the row
                } else {
                    row.style.display = "none"; // Hide the row
                }
            });
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById("searchInput").value = "";
            document.getElementById("filterFileType").value = "";
            document.getElementById("filterDateType").value = "";
            document.getElementById("filterStartDate").value = "";
            document.getElementById("filterEndDate").value = "";
            flatpickr("#datePicker").clear(); // Clear Flatpickr input
            filterDocuments(); // Reapply filtering to show all rows
        }

        // Open Add Document Modal
        function openAddModal() {
            document.getElementById('addDocumentModal').style.display = 'flex';
        }

        // Close Add Document Modal
        function closeAddModal() {
            document.getElementById('addDocumentModal').style.display = 'none';
        }

        function openEditModal(id, title, author, description) {
            console.log("Opening edit modal for document ID:", id);
            console.log("Title:", title);
            console.log("Author:", author);
            console.log("Description:", description);

            // Set the values in the edit modal form
            document.getElementById('document_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_author').value = author;
            document.getElementById('edit_description').value = description;

            // Display the edit modal
            document.getElementById('editDocumentModal').style.display = 'flex';
        }

        // Close Edit Document Modal
        function closeEditModal() {
            document.getElementById('editDocumentModal').style.display = 'none';
        }

        // Detail Modal
        function openDetailModal(id, title, author, description, file_path) {
            console.log("Opening detail modal for document ID:", id);
            console.log("Title:", title);
            console.log("Author:", author);
            console.log("Description:", description);
            console.log("File Path:", file_path);

            // Set the values in the detail modal form
            document.getElementById('document_id').value = id;
            document.getElementById('detail_title').value = title;
            document.getElementById('detail_author').value = author;
            document.getElementById('detail_description').value = description;
            document.getElementById('detail-file').value = file_path; // Populate the file input field

            // Display the detail modal
            document.getElementById('detailDocumentModal').style.display = 'flex';
        }

        // Close Detail Document Modal
        function closeDetailModal() {
            document.getElementById('detailDocumentModal').style.display = 'none';
        }

        let documentIdToDelete = null;

        // Function to open the confirmation modal
        function confirmDelete(documentId, documentTitle) {
            documentIdToDelete = documentId; // Store the document ID to delete
            document.getElementById('documentTitle').textContent = documentTitle; // Set the document title in the modal
            document.getElementById('confirmationModal').style.display = 'block'; // Show the modal
        }

        // Function to close the confirmation modal
        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none'; // Hide the modal
        }

        // Function to handle document deletion
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
            const uploadModal = document.getElementById('addDocumentModal');
            const editModal = document.getElementById('editDocumentModal');
            const confirmationModal = document.getElementById('confirmationModal');
            const detailModal = document.getElementById('detailDocumentModal');
            if (event.target === uploadModal) {
                uploadModal.style.display = 'none';
            }
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
            if (event.target === confirmationModal) {
                confirmationModal.style.display = 'none';
            }
            if (event.target === detailModal) {
                detailModal.style.display = 'none';
            }
        };

        // Hide messages after 3 seconds
        setTimeout(() => {
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            if (successMessage) successMessage.style.display = 'none';
            if (errorMessage) errorMessage.style.display = 'none';
        }, 2000);
    </script>
</body>

</html>