<?php require '../../processes/superadmin/manage_users.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/styles/superadmin/manage_users.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <h3>Manage Users</h3>

            <!-- Search Bar -->
            <div class="search-bar">
                <!-- Search Input -->
                <div class="search-input-container">
                    <input type="text" id="searchInput" placeholder="Search by ID, Name, or Email..." oninput="filterUsers()">
                </div>

                <!-- Filters, Date Range, and Buttons -->
                <div class="filters-and-buttons-row">
                    <!-- Filter by Role -->
                    <div class="filter-container">
                        <select id="filterRole" onchange="filterUsers()">
                            <option value="">All Roles</option>
                            <option value="superadmin">Super Admin</option>
                            <option value="admin">Admin</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="student">Student</option>
                            <option value="outsider">Outsider</option>
                        </select>
                    </div>

                    <!-- Select Date Type (Created At or Updated At) -->
                    <div class="filter-container">
                        <select id="filterDateType" onchange="filterUsers()">
                            <option value="">Select Date Type</option>
                            <option value="created_at">Uploaded At</option>
                            <option value="updated_at">Updated At</option>
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
                            Add User
                        </button>
                    </div>
                </div>
            </div>

            <!-- Display Messages -->
            <?php require '../show_message.php'; ?>

            <!-- Users Table -->
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created Date</th>
                        <th>Updated Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr class='tr-data'>
                <td>" . $row["id"] . "</td>
                <td>" . $row["name"] . "</td>
                <td>" . $row["email"] . "</td>
                <td>" . $row["role"] . "</td>
                <td>" . $row["created_at"] . "</td>
                <td>" . $row["updated_at"] . "</td>
                <td class='actions'>";

                            // Check if the role is not 'superadmin'
                            if ($row["role"] !== "superadmin") {
                                echo "<button class='button edit-button' onclick='openEditModal(" . $row["id"] . ", \"" . $row["name"] . "\", \"" . $row["email"] . "\", \"" . $row["role"] . "\")'>Edit</button>
                      <button class='button delete-button' onclick='confirmDelete(" . $row["id"] . ", \"" . addslashes($row["email"]) . "\")'>Delete</button>";
                            } else {
                                echo "<span class='no-actions'>No actions allowed</span>";
                            }

                            echo "</td>
              </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Add User</div>
            <form id="addUserForm" method="POST">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Must be at least 8 characters">
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="superadmin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="student">Student</option>
                        <option value="outsider">Outsider</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="button add-button" name="add_user">Save</button>
                    <button type="button" class="button cancel-button" onclick="closeAddModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Edit User</div>
            <form id="editUserForm" method="POST">
                <input type="hidden" id="user_id" name="user_id">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="edit_role" name="role" required>
                        <option value="superadmin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="student">Student</option>
                        <option value="outsider">Outsider</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="button add-button" name="edit_user">Update</button>
                    <button type="button" class="button cancel-button" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="confirm-modal-content">
            <p>Are you sure you want to delete the user with email "<span id="userEmail"></span>"? This action cannot be undone.</p>
            <hr>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeModal()">Cancel</button>
                <button class="btn btn-delete" onclick="deleteUser()">Yes, Delete</button>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let userIdToDelete = null;

        // Function to open the confirmation modal
        function confirmDelete(userId, userEmail) {
            userIdToDelete = userId; // Store the user ID to delete
            document.getElementById('userEmail').textContent = userEmail; // Set the user email in the modal
            document.getElementById('confirmationModal').style.display = 'flex'; // Show the modal
        }

        // Function to close the confirmation modal
        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none'; // Hide the modal
        }

        // Function to handle user deletion
        function deleteUser() {
            if (userIdToDelete) {
                // Create a form dynamically
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'manage_users.php'; // Update the action to your user management script

                // Add the user ID as a hidden input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_id';
                input.value = userIdToDelete;
                form.appendChild(input);

                // Add the delete_user flag
                const deleteFlag = document.createElement('input');
                deleteFlag.type = 'hidden';
                deleteFlag.name = 'delete_user';
                deleteFlag.value = '1';
                form.appendChild(deleteFlag);

                // Submit the form
                document.body.appendChild(form);
                form.submit();
            }
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
                    filterUsers();
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
                        filterUsers();
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
                filterUsers();
            }
        });

        // Filter users based on search input, role, and date range
        function filterUsers() {
            // Get filter values
            const searchInput = document.getElementById("searchInput").value.toLowerCase();
            const filterRole = document.getElementById("filterRole").value.toLowerCase();
            const filterDateType = document.getElementById("filterDateType").value;
            const filterStartDate = document.getElementById("filterStartDate").value;
            const filterEndDate = document.getElementById("filterEndDate").value;

            // Debugging logs to verify filter values
            console.log("Filter Start Date:", filterStartDate);
            console.log("Filter End Date:", filterEndDate);

            // Loop through each row in the users table
            document.querySelectorAll("#usersTable tbody tr").forEach(row => {
                // Extract data from the row
                const id = row.cells[0].textContent.toLowerCase();
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                const role = row.cells[3].textContent.toLowerCase();
                const createdDate = row.cells[4].textContent.split(' ')[0]; // Extract date part only
                const updatedDate = row.cells[5].textContent.split(' ')[0]; // Extract date part only

                // Determine which date to use based on the selected date type
                const dateToFilter = filterDateType === "created_at" ? createdDate : updatedDate;

                // Debugging logs to verify the date being filtered
                console.log("Date to Filter:", dateToFilter);

                // Check if the row matches the search input
                const matchesSearch = id.includes(searchInput) ||
                    name.includes(searchInput) ||
                    email.includes(searchInput);

                // Check if the row matches the selected role
                const matchesRole = filterRole === "" || role === filterRole;

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
                if (matchesSearch && matchesRole && matchesDate) {
                    row.style.display = ""; // Show the row
                } else {
                    row.style.display = "none"; // Hide the row
                }
            });
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById("searchInput").value = "";
            document.getElementById("filterRole").value = "";
            document.getElementById("filterDateType").value = "";
            document.getElementById("filterStartDate").value = "";
            document.getElementById("filterEndDate").value = "";
            flatpickr("#datePicker").clear(); // Clear Flatpickr input
            filterUsers(); // Reapply filtering to show all rows
        }

        // Open Add User Modal
        function openAddModal() {
            document.getElementById('addUserModal').style.display = 'flex';
        }

        // Close Add User Modal
        function closeAddModal() {
            document.getElementById('addUserModal').style.display = 'none';
        }

        // Open Edit User Modal
        function openEditModal(id, name, email, role) {
            document.getElementById('user_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            document.getElementById('editUserModal').style.display = 'flex';
        }

        // Close Edit User Modal
        function closeEditModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }
    </script>
</body>

</html>