<link rel="stylesheet" href="/assets/styles/search.css">

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

<script>
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
</script>