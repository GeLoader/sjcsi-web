<?php
// user_create.php - Create new user
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config.php';

$user = $_SESSION['user'];
$page_title = 'Create User';

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    if (empty($password)) $errors[] = 'Password is required';
    if (empty($role)) $errors[] = 'Role is required';
    
    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }

    // Validate password confirmation
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    // Validate password strength
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }

    // Validate role-specific requirements
    if ($role === 'department' && empty($department)) {
        $errors[] = 'Department is required for department role';
    }
    if ($role === 'office' && empty($office)) {
        $errors[] = 'Office is required for office role';
    }

    // Check if email already exists
    if (empty($errors)) {
        try {
            $checkSql = "SELECT id FROM users WHERE email = ?";
            $checkStmt = dbPrepare($checkSql);
            $checkStmt->bind_param('s', $email);
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
        try {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Set department/office based on role
            $dept = ($role === 'department') ? $department : null;
            $off = ($role === 'office') ? $office : null;

            $sql = "INSERT INTO users (email, password, role, department, office, is_active) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = dbPrepare($sql);
            $stmt->bind_param('sssssi', $email, $hashedPassword, $role, $dept, $off, $is_active);
            $stmt->execute();

            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'User created successfully!'
            ];
            
            header('Location: AdminDashboard.php');
            exit;

        } catch (Exception $e) {
            $errors[] = 'Error creating user: ' . $e->getMessage();
        }
    }
}
?>
<?php include BASE_PATH . '/header.php'; ?>

<div class="min-vh-100 bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h4 mb-0">Create User</h2>
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

                        <form method="POST" id="userForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required
                                           minlength="6" placeholder="Minimum 6 characters">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                                           minlength="6" placeholder="Re-enter password">
                                </div>
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
                                           <?php echo (isset($_POST['is_active']) || !isset($_POST['email'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active User
                                    </label>
                                    <div class="form-text">Uncheck to create an inactive user account</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="AdminDashboard.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create User</button>
                            </div>
                        </form>
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
    
    // Password confirmation validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePasswords() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('change', validatePasswords);
    confirmPassword.addEventListener('keyup', validatePasswords);
});
</script>

<?php include __DIR__ . '/footer.php'; ?>