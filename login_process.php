<?php
// login_process.php - Handle user authentication
session_start();
require_once __DIR__ . '/config.php';

// Function to redirect with message
function redirectWithMessage($location, $type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
    header("Location: $location");
    exit;
}
 

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithMessage('login.php', 'error', 'Invalid request method.');
}

// Get form data
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';
$department = $_POST['department'] ?? '';

// Validate required fields
// if (empty($email) || empty($password) || empty($role)) {
//     redirectWithMessage('login.php', 'error', 'Please fill in all required fields.');
// }

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithMessage('login.php', 'error', 'Please enter a valid email address.');
}

try {
 
        $sql = "SELECT id, email, password, role, department, office, is_active 
                FROM users 
                WHERE email = ?   AND is_active = 1";
        $stmt = dbPrepare($sql);
        $stmt->bind_param('s', $email);
    
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        redirectWithMessage('login.php', 'error', 'Invalid credentials or account not found.');
    }

    $user = $result->fetch_assoc();
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        redirectWithMessage('login.php', 'error', 'Invalid credentials.');
    }

    // Update last login
    $updateSql = "UPDATE users SET last_login = NOW() WHERE id = ?";
    $updateStmt = dbPrepare($updateSql);
    $updateStmt->bind_param('i', $user['id']);
    $updateStmt->execute();

    // Store user data in session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'department' => $user['department'],
        'office' => $user['office']
    ];

    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    // Redirect based on role
    switch ($user['role']) {
        case 'admin':
            // redirectWithMessage('admin/dashboard.php', 'success', 'Welcome back, Admin!');
        redirectWithMessage('AdminDashboard.php', 'success', 'Welcome back, Admin!');
            break;
        case 'department':
            $dept = strtolower($user['department']);
            redirectWithMessage("DEPARTMENT{$dept}_dashboard.php", 'success', "Welcome back, {$user['department']} Department!");
            break;
        case 'office':
            $office = strtolower($user['office']);
            redirectWithMessage("OFFICE{$office}_dashboard.php", 'success', "Welcome back, {$user['office']} Office!");
            break;
        default:
            redirectWithMessage('login.php', 'error', 'Invalid user role.');
    }

} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    redirectWithMessage('login.php', 'error', 'An error occurred during login. Please try again.');
}
?>