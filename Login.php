<?php
session_start();
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/database.php';
include BASE_PATH . '/header.php'; 
?>

<div class="min-vh-100 d-flex align-items-center justify-content-center p-4" style="background: linear-gradient(135deg, #f0f8ff 0%, #e6e6fa 100%);">
    <div class="w-100" style="max-width: 450px;">
        <div class="text-center mb-5">
            <div class="d-flex justify-content-center mb-4">
                <img src="images/sjcsi-logo.png" alt="School logo" class="rounded-circle shadow" style="width: 80px; height: 80px;">
            </div>
            <h1 class="h2 fw-bold text-dark">SJCSI Portal</h1>
            <p class="text-muted">Sign in to access your account</p>
        </div>

        <div class="card">
            <div class="card-body">
                  <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_message']['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>
                <h2 class="card-title h4">Login</h2>
                <p class="card-subtitle text-muted mb-3">Enter your credentials to access the portal</p>
                
                <form action="login_process.php" method="POST" class="needs-validation" novalidate id="loginForm">
                    <input type="hidden" name="role" id="roleInput" value="">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"   required>
                         
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <!-- Dynamic Department/Office Selection -->
                    <div class="mb-3 d-none" id="departmentSection" hidden>
                        <label for="department" class="form-label">Department</label>
                        <select class="form-select" id="department" name="department">
                            <option value="" selected disabled>Select your department</option>
                            <option value="CASTE">CASTE Department</option>
                            <option value="CIT">CIT Department</option>
                            <option value="COA">COA Department</option>
                            <option value="CBA">CBA Department</option>
                            <option value="CJE">CJE Department</option>
                            <option value="SHS">SHS Department</option>
                            <option value="JHS">JHS Department</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 d-none" id="officeSection" hidden>
                        <label for="office" class="form-label">Office</label>
                        <select class="form-select" id="office" name="department">
                            <option value="" selected disabled>Select office</option>
                            <option value="ACCOUNTING">Accounting Office</option>
                            <option value="ADMIN">Admin Office</option>
                            <option value="GUIDANCE">Guidance Office</option>
                            <option value="IT_SUPPORT">IT Support Office</option>
                            <option value="STUDENT_AFFAIRS">Student Affairs Office</option>
                            <option value="SCHOLARSHIP">Scholarship Office</option>
                            <option value="TESDA">TESDA Office</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info d-none" id="roleIndicator" hidden>
                        <i class="fas fa-info-circle me-2"></i>
                        
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100" id="submitButton">
                        <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                        <span id="buttonText">Sign In</span>
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="small text-muted">
                Forgot your password? <a href="#" class="text-primary">Contact IT Support</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const roleInput = document.getElementById('roleInput');
    const departmentSection = document.getElementById('departmentSection');
    const officeSection = document.getElementById('officeSection');
    const departmentSelect = document.getElementById('department');
    const officeSelect = document.getElementById('office');
    const roleIndicator = document.getElementById('roleIndicator');
    const roleText = document.getElementById('roleText');
    const submitButton = document.getElementById('submitButton');
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const form = document.getElementById('loginForm');
    
    // Function to detect role based on email
    function detectRole(email) {
        if (!email) return null;
        
        const domain = email.split('@')[1];
        if (domain !== 'sjcsi.edu.ph') return null;
        
        const username = email.split('@')[0].toLowerCase();
        
        // Admin detection
        if (username === 'admin') return 'admin';
        
        // Department detection
        const departments = ['caste', 'cit', 'coa', 'cba', 'cje', 'shs', 'jhs'];
        if (departments.includes(username)) return 'department';
        
        // Office detection
        const offices = ['accounting', 'admin', 'guidance', 'itsupport', 'studentaffairs', 'scholarship', 'tesda'];
        if (offices.includes(username)) return 'office';
        
        return null;
    }
    
    // Update UI based on detected role
    function updateRoleUI(role, email) {
        roleIndicator.classList.remove('d-none');
        
        if (!role) {
            roleText.textContent = 'Please enter your SJCSI email address';
            roleInput.value = '';
            departmentSection.classList.add('d-none');
            officeSection.classList.add('d-none');
            buttonText.textContent = 'Sign In';
            return;
        }
        
        roleInput.value = role;
        
        if (role === 'admin') {
            roleText.innerHTML = '<i class="fas fa-shield-alt me-2"></i> Admin account detected';
            departmentSection.classList.add('d-none');
            officeSection.classList.add('d-none');
            buttonText.textContent = 'Sign In';
        } 
        else if (role === 'department') {
            const dept = email.split('@')[0].toUpperCase();
            roleText.innerHTML = `<i class="fas fa-users me-2"></i> ${dept} Department account detected`;
            departmentSection.classList.remove('d-none');
            officeSection.classList.add('d-none');
            
            // Pre-select the department if it exists
            if (departmentSelect.querySelector(`option[value="${dept}"]`)) {
                departmentSelect.value = dept;
            }
            buttonText.textContent = `Sign In`;
        } 
        else if (role === 'office') {
            const office = email.split('@')[0].toUpperCase();
            roleText.innerHTML = `<i class="fas fa-building me-2"></i> ${office} Office account detected`;
            departmentSection.classList.add('d-none');
            officeSection.classList.remove('d-none');
            
            // Try to match office name with options
            const officeOptions = {
                'ACCOUNTING': 'ACCOUNTING',
                'ADMIN': 'ADMIN',
                'GUIDANCE': 'GUIDANCE',
                'ITSUPPORT': 'IT_SUPPORT',
                'STUDENTAFFAIRS': 'STUDENT_AFFAIRS',
                'SCHOLARSHIP': 'SCHOLARSHIP',
                'TESDA': 'TESDA'
            };
            
            if (officeOptions[office]) {
                officeSelect.value = officeOptions[office];
            }
            buttonText.textContent = `Sign In`;
        }
    }
    
    // Email input event listener
    emailInput.addEventListener('input', function() {
        const email = this.value.trim();
        const role = detectRole(email);
        updateRoleUI(role, email);
    });
    
    // Department/office change event
    departmentSelect.addEventListener('change', function() {
        buttonText.textContent = `Sign In to ${this.value} Department`;
    });
    
    officeSelect.addEventListener('change', function() {
        // Format the office name for display
        const officeName = this.options[this.selectedIndex].text;
        buttonText.textContent = `Sign In to ${officeName}`;
    });
    
    // Form validation
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            // Show loading spinner
            loadingSpinner.classList.remove('d-none');
            submitButton.disabled = true;
        }
        
        form.classList.add('was-validated');
    }, false);
    
    // Demo credentials (for testing)
    // setTimeout(function() {
    //     // Pre-fill with demo admin credentials (remove in production)
    //     emailInput.value = 'admin@sjcsi.edu.ph';
    //     passwordInput.value = 'admin123';
    //     updateRoleUI('admin', 'admin@sjcsi.edu.ph');
    // }, 500);
});
</script>

<?php include 'footer.php'; ?>