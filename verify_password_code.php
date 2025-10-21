<?php
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'] ?? '';
$code = $data['code'] ?? '';

if (!$token || !$code) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Verify session
if ($token !== session_id()) {
    echo json_encode(['success' => false, 'error' => 'Invalid session']);
    exit;
}

// Check if verification data exists
if (!isset($_SESSION['password_verification'])) {
    echo json_encode(['success' => false, 'error' => 'No pending verification']);
    exit;
}

$verification = $_SESSION['password_verification'];

// Check expiration
if (time() > $verification['expires']) {
    unset($_SESSION['password_verification']);
    echo json_encode(['success' => false, 'error' => 'Verification code expired']);
    exit;
}

// Verify code
if ($verification['code'] !== $code) {
    echo json_encode(['success' => false, 'error' => 'Invalid verification code']);
    exit;
}

// Code is valid - update password in database
try {
    $updateQuery = dbQuery(
        "UPDATE users SET password = ? WHERE id = ?",
        [$verification['password'], $verification['user_id']]
    );
    
    if ($updateQuery) {
        // Clear verification data
        unset($_SESSION['password_verification']);
        
        echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update password']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>