<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the index page if the user is not logged in
    header("Location: ../../../index.php");
    exit(); // Ensure no further code is executed
}
