<?php
// logout.php - Handle user logout
session_start();

// Destroy all session data
session_destroy();

// Start a new session for flash message
session_start();

// Set logout message
$_SESSION['flash_message'] = [
    'type' => 'success',
    'message' => 'You have been successfully logged out.'
];

// Redirect to login page
header('Location: login.php');
exit;
?>