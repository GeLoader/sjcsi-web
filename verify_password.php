<?php
// verify_password.php - Verify user password for sensitive operations
session_start();
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$password = $input['password'] ?? '';

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

try {
    // Initialize attempt counter if not exists
    if (!isset($_SESSION['password_attempts'])) {
        $_SESSION['password_attempts'] = 0;
    }
    
if ($_SESSION['password_attempts'] >= 3) {
    // Clear session and logout
    session_destroy();
    echo json_encode([
        'success' => false, 
        'message' => 'Maximum password attempts reached. Please login again.',
        'logout' => true,
        'redirect_url' => 'login.php'
    ]);
     echo "<script type='text/javascript'> document.location ='login.php'; </script>";
    //exit;
}
    
    // Get current user's password hash from database
    $userId = $_SESSION['user']['id'];
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = dbPrepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Password is correct - reset attempts and set verification session
        $_SESSION['password_attempts'] = 0;
        $_SESSION['password_verified'] = time();
        echo json_encode(['success' => true, 'message' => 'Password verified successfully']);
    } else {
        // Increment failed attempts
        $_SESSION['password_attempts']++;
        $remaining_attempts = 3 - $_SESSION['password_attempts'];
        
        if ($remaining_attempts > 0) {
            $message = "Invalid password. {$remaining_attempts} attempt(s) remaining.";
        } else {
            $message = "Maximum password attempts reached. Please login again.";
            session_destroy();
        }
        
        echo json_encode([
            'success' => false, 
            'message' => $message,
            'attempts_remaining' => $remaining_attempts,
            'logout' => ($remaining_attempts <= 0)
        ]);
    }
    
} catch (Exception $e) {
    error_log("Password Verification Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred during password verification']);
}
?>