<?php
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

      
        <!-- Login Tabs -->
        <ul class="nav nav-tabs mb-4" id="loginTabs" role="tablist" >
            <li class="nav-item" role="presentation" >
                <button class="nav-link active" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" style="color:black;">
                    <i class="fas fa-shield-alt me-2"></i>Admin
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="department-tab" data-bs-toggle="tab" data-bs-target="#department" type="button" style="color:black;">
                    <i class="fas fa-users me-2"></i>Department
                </button>
            </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="office-tab" data-bs-toggle="tab" data-bs-target="#office" type="button" style="color:black;">
                    <i class="fas fa-users me-2"></i>Office
                </button>
            </li>
        </ul>

        <div class="tab-content" id="loginTabsContent">
            <!-- Admin Login -->
            <div class="tab-pane fade show active" id="admin" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title h4">Admin Login</h2>
                        <p class="card-subtitle text-muted mb-3">Access administrative functions and manage the entire website</p>
                        
                        <form action="login_process.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="role" value="admin">
                            
                            <div class="mb-3">
                                <label for="admin-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="admin-email" name="email" placeholder="admin@sjcsi.edu.ph" value="admin@sjcsi.edu.ph" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="admin-password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <span id="admin-loading" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                Sign In as Admin
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Department Login -->
            <div class="tab-pane fade" id="department" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title h4">Department Login</h2>
                        <p class="card-subtitle text-muted mb-3">Access your department's section and manage department-specific content</p>
                        
                        <form action="login_process.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="role" value="department">
                            
                            <div class="mb-3">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department" required>
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
                            
                            <div class="mb-3">
                                <label for="dept-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="dept-email" name="email" placeholder="department@sjcsi.edu.ph" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="dept-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="dept-password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <span id="dept-loading" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                Sign In to Department
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- OFFICE -->
               <div class="tab-pane fade" id="office" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title h4">Office Login</h2>
                        <p class="card-subtitle text-muted mb-3">Access your office's section and manage office-specific content</p>
                        
                        <form action="login_process.php" method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="role" value="office">
                            
                            <div class="mb-3">
                                <label for="department" class="form-label">Office</label>
                                <select class="form-select" id="department" name="department" required>
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
                            
                            <div class="mb-3">
                                <label for="dept-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="dept-email" name="email" placeholder="department@sjcsi.edu.ph" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="dept-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="dept-password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <span id="dept-loading" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                Sign In to Office
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <p class="small text-muted">
                Forgot your password? <a href="#" class="text-primary">Contact IT Support</a>
            </p>
        </div>
    </div>
</div>

<!-- Login Processing Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                // Show loading spinner
                const role = form.querySelector('input[name="role"]').value;
                const loadingSpinner = document.getElementById(`${role}-loading`);
                if (loadingSpinner) {
                    loadingSpinner.classList.remove('d-none');
                }
                
                // Disable submit button
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Demo credentials fill
    document.getElementById('admin-tab').addEventListener('click', function() {
        document.getElementById('admin-email').value = 'admin@sjcsi.edu.ph';
        document.getElementById('admin-password').value = 'admin123';
    });
    
    document.getElementById('department-tab').addEventListener('click', function() {
        document.getElementById('dept-email').value = 'cit@sjcsi.edu.ph';
        document.getElementById('dept-password').value = 'cit123';
        document.getElementById('department').value = 'cit';
    });
});
</script>

<?php include 'footer.php'; ?>