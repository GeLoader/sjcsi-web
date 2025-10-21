<?php
// user_edit.php - Edit existing user with email verification
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

$user = $_SESSION['user'];
$page_title = 'Edit User';

// Get user ID from URL
$user_id = $_GET['id'] ?? 0;
if (!$user_id || !is_numeric($user_id)) {
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Invalid user ID'
    ];
    header('Location: AdminDashboard.php');
    exit;
}

// Function to send password change verification email
function sendPasswordChangeVerification($email, $verification_code, $userName) {
    $mail_sent = false;
    $mail_error = '';
    
    // Try multiple SMTP configurations
    $smtp_configs = [
        [
            'host' => 'smtp.gmail.com',
            'username' => 'sjcsiweb@gmail.com',
            'password' => 'msel xfrr ymns tsav',
            'secure' => PHPMailer::ENCRYPTION_STARTTLS,
            'port' => 587
        ],
        [
            'host' => 'smtp.gmail.com',
            'username' => 'sjcsiweb@gmail.com',
            'password' => 'msel xfrr ymns tsav',
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
            $mail->setFrom('sjcsiweb@gmail.com', 'SJCSI');
            $mail->addAddress($email, $userName);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Change Verification Code';
    $mail->Body = '
                <div style="font-family: Arial, sans-serif; max-width: 450px;  padding: 20px;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e0e0e0;">
                        <tr>
                            <td style="padding: 30px; text-align: center; background-color: #f5f5f5; border-bottom: 3px solid #2d5016;">
                                <h1 style="margin: 0; color: #2d5016; font-size: 28px;">SJCSI</h1>
                                <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Password Change Verification</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 40px 30px;">
                                <p style="margin: 0 0 20px 0; color: #333; font-size: 16px;">Hello <strong>' . htmlspecialchars($userName) . '</strong>,</p>
                                <p style="margin: 0 0 20px 0; color: #333; line-height: 1.6;">A password change has been requested for your account. Please use the verification code below to confirm this change:</p>
                                
                                <table width="100%" cellpadding="0" cellspacing="0" style="margin: 30px 0;">
                                    <tr>
                                        <td style="text-align: center;">
                                            <div style="background-color: #f8f8f8;  padding: 20px; display: inline-block;">
                                                <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #2d5016; font-family: monospace;">' . $verification_code . '</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                
                                <p style="margin: 0 0 20px 0; color: #666; font-size: 14px; line-height: 1.6;">
                                    <strong>Important:</strong> If you did not request this change, please contact the administrator.
                                </p>
                            </td>
                        </tr>
                        
                    </table>
                </div>
            ';
            
            // Plain text version
            $mail->AltBody = "SJCSI - Password Change Verification\n\n" .
                           "Hello $userName,\n\n" .
                           "A password change has been requested for your account.\n\n" .
                           "Your verification code is: $verification_code\n\n" .
                           "Please enter this code on the verification page to complete the password change process.\n\n" .
                           "If you did not request this password change, please contact the administrator immediately.";
            
            $mail->send();
            $mail_sent = true;
            break;
            
        } catch (Exception $e) {
            $mail_error = $mail->ErrorInfo;
            error_log("PHPMailer Error (Config: {$config['port']}): " . $mail_error);
            continue;
        }
    }
    
    return ['success' => $mail_sent, 'error' => $mail_error];
}

// Get departments and offices for dropdown
try {
    $departmentsResult = dbQuery("SELECT code, name FROM departments WHERE is_active = 1 ORDER BY name");
    $departments = [];
    while ($row = $departmentsResult->fetch_assoc()) {
        $departments[] = $row;
    }

    $officesResult = dbQuery("SELECT code, name FROM offices WHERE is_active = 1 ORDER BY name");
    $offices = [];
    while ($row = $officesResult->fetch_assoc()) {
        $offices[] = $row;
    }
} catch (Exception $e) {
    $departments = [];
    $offices = [];
}

// Get existing user data
try {
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = dbPrepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'User not found'
        ];
        header('Location: AdminDashboard.php');
        exit;
    }
    
    $editUser = $result->fetch_assoc();
} catch (Exception $e) {
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Error fetching user: ' . $e->getMessage()
    ];
    header('Location: AdminDashboard.php');
    exit;
}

// Handle verification code submission
if (isset($_POST['verify_code'])) {
    $entered_code = trim($_POST['verification_code'] ?? '');
    
    if (isset($_SESSION['pending_user_update'])) {
        $pending = $_SESSION['pending_user_update'];
        
        // Check if verification code matches
        $code_matched = false;
        $code_expired = false;
        
        // Check session verification code
        if (isset($_SESSION['user_edit_verification_code']) && 
            $_SESSION['user_edit_verification_code'] == $entered_code) {
            // Check if code is not expired (10 minutes)
            if (time() - $_SESSION['code_generated_time'] <= 600) {
                $code_matched = true;
            } else {
                $code_expired = true;
            }
        }
        
        if ($code_matched) {
            // Code is correct, proceed with update
            try {
                $hashedPassword = password_hash($pending['password'], PASSWORD_DEFAULT);
                $sql = "UPDATE users SET email = ?, password = ?, role = ?, department = ?, office = ?, is_active = ?, updated_at = NOW() WHERE id = ?";
                $stmt = dbPrepare($sql);
                $stmt->bind_param('sssssii', 
                    $pending['email'], 
                    $hashedPassword, 
                    $pending['role'], 
                    $pending['department'], 
                    $pending['office'], 
                    $pending['is_active'], 
                    $user_id
                );
                
                $stmt->execute();

                // Clear session data
                unset($_SESSION['pending_user_update']);
                unset($_SESSION['user_edit_verification_code']);
                unset($_SESSION['code_generated_time']);

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'User password updated successfully!'
                ];
                
                header('Location: AdminDashboard.php');
                exit;

            } catch (Exception $e) {
                $errors[] = 'Error updating user: ' . $e->getMessage();
            }
        } else {
            if ($code_expired) {
                $verification_error = 'The verification code has expired. Please request a new code by submitting the form again.';
                // Clear expired session data
                unset($_SESSION['pending_user_update']);
                unset($_SESSION['user_edit_verification_code']);
                unset($_SESSION['code_generated_time']);
            } else {
                $verification_error = 'Invalid verification code. Please check the code and try again.';
            }
            // Keep showing verification form
            $show_verification = true;
        }
    } else {
        $verification_error = 'Session expired. Please submit the form again.';
        $show_verification = false;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['verify_code'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    $department = $_POST['department'] ?? '';
    $office = $_POST['office'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Validate required fields
    $errors = [];
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($role)) $errors[] = 'Role is required';
    
    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    // Validate password if provided
    if (!empty($password)) {
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }
    }

    // Validate role-specific requirements
    if ($role === 'department' && empty($department)) {
        $errors[] = 'Department is required for department role';
    }
    if ($role === 'office' && empty($office)) {
        $errors[] = 'Office is required for office role';
    }

    // Check if email already exists (excluding current user)
    if (empty($errors) && $email !== $editUser['email']) {
        try {
            $checkSql = "SELECT id FROM users WHERE email = ? AND id != ?";
            $checkStmt = dbPrepare($checkSql);
            $checkStmt->bind_param('si', $email, $user_id);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) {
                $errors[] = 'Email already exists';
            }
        } catch (Exception $e) {
            $errors[] = 'Error checking email: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        // Set department/office based on role
        $dept = ($role === 'department') ? $department : null;
        $off = ($role === 'office') ? $office : null;

        // If password is being changed, send verification email
        if (!empty($password)) {
            // Generate 6-digit verification code
            $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Send verification email
            $email_result = sendPasswordChangeVerification($email, $verification_code, $email);
            
            if ($email_result['success']) {
                // Store pending update in session
                $_SESSION['pending_user_update'] = [
                    'email' => $email,
                    'password' => $password,
                    'role' => $role,
                    'department' => $dept,
                    'office' => $off,
                    'is_active' => $is_active
                ];
                $_SESSION['user_edit_verification_code'] = $verification_code;
                $_SESSION['code_generated_time'] = time();
                
                $show_verification = true;
            } else {
                // Email failed, store code in session as backup
                $_SESSION['pending_user_update'] = [
                    'email' => $email,
                    'password' => $password,
                    'role' => $role,
                    'department' => $dept,
                    'office' => $off,
                    'is_active' => $is_active
                ];
                $_SESSION['user_edit_verification_code'] = $verification_code;
                $_SESSION['code_generated_time'] = time();
                
                $errors[] = 'Failed to send verification email. Please use code: ' . $verification_code;
                $show_verification = true;
            }
        } else {
            // No password change, update directly
            try {
                $sql = "UPDATE users SET email = ?, role = ?, department = ?, office = ?, is_active = ?, updated_at = NOW() WHERE id = ?";
                $stmt = dbPrepare($sql);
                $stmt->bind_param('ssssii', $email, $role, $dept, $off, $is_active, $user_id);
                
                $stmt->execute();

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'User updated successfully!'
                ];
                
                header('Location: AdminDashboard.php');
                exit;

            } catch (Exception $e) {
                $errors[] = 'Error updating user: ' . $e->getMessage();
            }
        }
    }
} else {
    // Pre-populate form with existing data
    $_POST = $editUser;
    $_POST['is_active'] = $editUser['is_active'] ? 'on' : '';
}
?>
<?php include BASE_PATH . '/header.php'; ?>

<div class="min-vh-100 bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h4 mb-0">Edit User</h2>
                        <a href="AdminDashboard.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($show_verification) && $show_verification): ?>
                            <!-- Verification Form -->
                            <div class="alert alert-info">
                                <i class="fas fa-envelope me-2"></i>
                                A verification code has been sent to the email
                            </div>

                            <?php if (isset($verification_error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Error:</strong> <?php echo htmlspecialchars($verification_error); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-3">
                                    <label for="verification_code" class="form-label">Enter Verification Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg text-center" 
                                           id="verification_code" name="verification_code" 
                                           maxlength="6" pattern="[0-9]{6}" required
                                           placeholder="000000" style="letter-spacing: 10px; font-size: 24px;">
                                    <div class="form-text">Enter the 6-digit code sent to your email</div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="user_edit.php?id=<?php echo $user_id; ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" name="verify_code" class="btn btn-primary">
                                        <i class="fas fa-check me-2"></i>Verify & Update
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <!-- Regular Edit Form -->
                            <form method="POST" id="userForm">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                               minlength="6" placeholder="Leave blank to keep current password">
                                        <div class="form-text">Leave blank to keep current password</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                               minlength="6" placeholder="Re-enter new password if changing">
                                    </div>
                                </div>

                                <div class="alert alert-warning" id="passwordChangeAlert" style="display: none;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Note:</strong> Changing the password will require email verification before the update is applied.
                                </div>

                                <div class="mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role" name="role" required onchange="toggleRoleFields()">
                                        <option value="">Select Role</option>
                                        <option value="admin" <?php echo ($_POST['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="department" <?php echo ($_POST['role'] ?? '') === 'department' ? 'selected' : ''; ?>>Department</option>
                                        <option value="office" <?php echo ($_POST['role'] ?? '') === 'office' ? 'selected' : ''; ?>>Office</option>
                                    </select>
                                </div>

                                <div id="departmentField" class="mb-3" style="display: none;">
                                    <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" id="department" name="department">
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?php echo htmlspecialchars($dept['code']); ?>" 
                                                    <?php echo ($_POST['department'] ?? '') === $dept['code'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dept['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div id="officeField" class="mb-3" style="display: none;">
                                    <label for="office" class="form-label">Office <span class="text-danger">*</span></label>
                                    <select class="form-select" id="office" name="office">
                                        <option value="">Select Office</option>
                                        <?php foreach ($offices as $off): ?>
                                            <option value="<?php echo htmlspecialchars($off['code']); ?>"
                                                    <?php echo ($_POST['office'] ?? '') === $off['code'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($off['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               <?php echo !empty($_POST['is_active']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">
                                            Active User
                                        </label>
                                        <div class="form-text">Uncheck to deactivate this user account</div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <small>
                                        <strong>User Info:</strong><br>
                                        Created: <?php echo date('M d, Y H:i', strtotime($editUser['created_at'])); ?><br>
                                        Last Updated: <?php echo date('M d, Y H:i', strtotime($editUser['updated_at'])); ?><br>
                                        Last Login: <?php echo $editUser['last_login'] ? date('M d, Y H:i', strtotime($editUser['last_login'])) : 'Never'; ?>
                                    </small>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="AdminDashboard.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update User</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('role').value;
    const departmentField = document.getElementById('departmentField');
    const officeField = document.getElementById('officeField');
    const departmentSelect = document.getElementById('department');
    const officeSelect = document.getElementById('office');

    // Hide all fields first
    departmentField.style.display = 'none';
    officeField.style.display = 'none';
    departmentSelect.required = false;
    officeSelect.required = false;

    // Show relevant field based on role
    if (role === 'department') {
        departmentField.style.display = 'block';
        departmentSelect.required = true;
    } else if (role === 'office') {
        officeField.style.display = 'block';
        officeSelect.required = true;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleRoleFields();
    
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordAlert = document.getElementById('passwordChangeAlert');
    
    // Show alert when password field is filled
    if (password) {
        password.addEventListener('input', function() {
            if (this.value.length > 0) {
                passwordAlert.style.display = 'block';
            } else {
                passwordAlert.style.display = 'none';
            }
        });
    }
    
    // Password confirmation validation
    function validatePasswords() {
        if (password.value.length > 0) {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('change', validatePasswords);
    confirmPassword.addEventListener('keyup', validatePasswords);

    // Auto-format verification code input
    const verificationInput = document.getElementById('verification_code');
    if (verificationInput) {
        verificationInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>