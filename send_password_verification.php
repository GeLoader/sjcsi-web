<?php
session_start();
require_once __DIR__ . '/config.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$action = $data['action'] ?? '';

if (!$user_id || !$email) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Generate 6-digit verification code
$verification_code = sprintf("%06d", mt_rand(1, 999999));

// Store verification data in session
$_SESSION['password_verification'] = [
    'user_id' => $user_id,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'code' => $verification_code,
    'expires' => time() + 600, // 10 minutes
    'action' => $action
];

// Get username for email
$userResult = dbQuery("SELECT username FROM users WHERE id = ?", [$user_id]);
$user = $userResult->fetch_assoc();
$username = $user['username'] ?? 'User';

// Send verification email
$emailResult = sendPasswordChangeVerificationEmail($email, $username, $verification_code);

if ($emailResult['success']) {
    echo json_encode([
        'success' => true,
        'token' => session_id(),
        'message' => 'Verification code sent to email'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Failed to send verification email: ' . $emailResult['error']
    ]);
}

function sendPasswordChangeVerificationEmail($email, $username, $verification_code) {
    $mail_sent = false;
    $mail_error = '';
    
    $smtp_configs = [
        [
            'host' => 'smtp.gmail.com',
            'username' => 'sjclibrary08@gmail.com',
            'password' => 'msyd niuu yayk kvje',
            'secure' => PHPMailer::ENCRYPTION_STARTTLS,
            'port' => 587
        ],
        [
            'host' => 'smtp.gmail.com',
            'username' => 'sjclibrary08@gmail.com',
            'password' => 'msyd niuu yayk kvje',
            'secure' => PHPMailer::ENCRYPTION_SMTPS,
            'port' => 465
        ]
    ];
    
    foreach ($smtp_configs as $config) {
        try {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = $config['secure'];
            $mail->Port = $config['port'];
            
            // SSL verification settings
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
            // Timeout settings
            $mail->Timeout = 30;
            
            // Recipients
            $mail->setFrom('sjclibrary08@gmail.com', 'SJCSI Admin System');
            $mail->addAddress($email, $username);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Change Verification - SJCSI Admin System';
            $mail->Body = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px; border-radius: 10px;">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h2 style="color: #2d5016; margin: 0;">SJCSI Admin System</h2>
                        <p style="color: #666; margin: 5px 0;">Password Change Verification</p>
                    </div>
                    
                    <div style="background: white; padding: 25px; border-radius: 8px; border-left: 4px solid #2d5016;">
                        <p style="color: #333; margin-bottom: 15px;">Hello <strong>' . htmlspecialchars($username) . '</strong>,</p>
                        
                        <p style="color: #333; margin-bottom: 20px;">You are attempting to change your password in the SJCSI Admin System. Please use the verification code below to complete this action:</p>
                        
                        <div style="text-align: center; margin: 30px 0;">
                            <div style="font-size: 32px; font-weight: bold; padding: 20px; background: #f0f7f0; border: 2px dashed #4a7c59; border-radius: 8px; display: inline-block;">
                                ' . $verification_code . '
                            </div>
                        </div>
                        
                        <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
                            <strong>Important:</strong> If you did not do this, please contact the support.
                        </p>
                        
                        
                    </div>
                    
                    <div style="text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                        
                    </div>
                </div>
            ';
            
            // Plain text version
            $mail->AltBody = "SJCSI Admin System - Password Change Verification\n\nHello " . $username . ",\n\nYou are attempting to change your password in the SJCSI Admin System. Please use the verification code below to complete this action:\n\nVerification Code: " . $verification_code . "\n\nThis code will expire in 10 minutes. If you did not request this password change, please contact the system administrator immediately.\n\nThis is an automated message. Please do not reply to this email.";
            
            $mail->send();
            $mail_sent = true;
            break;
            
        } catch (Exception $e) {
            $mail_error = $mail->ErrorInfo;
            error_log("PHPMailer Error (Password Change): " . $mail_error);
            continue;
        }
    }
    
    return ['success' => $mail_sent, 'error' => $mail_error];
}
?>