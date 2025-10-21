<?php
// complete_user_update.php - Complete user update after verification
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

// Verify session and pending update
if ($token !== session_id() || !isset($_SESSION['pending_user_update'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid session or no pending update']);
    exit;
}

$pending_update = $_SESSION['pending_user_update'];

// Verify the code first
$verify_data = [
    'token' => $token,
    'code' => $code
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://' . $_SERVER['HTTP_HOST'] . '/verify_password_code.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($verify_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$verify_result = json_decode($response, true);

if ($verify_result['success']) {
    // Update user with all changes
    try {
        $hashedPassword = password_hash($pending_update['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET email = ?, password = ?, role = ?, department = ?, office = ?, is_active = ?, updated_at = NOW() WHERE id = ?";
        $stmt = dbPrepare($sql);
        $stmt->bind_param('sssssii', 
            $pending_update['email'], 
            $hashedPassword, 
            $pending_update['role'], 
            $pending_update['department'], 
            $pending_update['office'], 
            $pending_update['is_active'], 
            $pending_update['user_id']
        );
        $stmt->execute();

        // Clear pending update
        unset($_SESSION['pending_user_update']);

        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully with new password!'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error updating user: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => $verify_result['error']]);
}
?>