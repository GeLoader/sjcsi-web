 
$(document).ready(function() {
    // Global variables to store pending actions
    let pendingAction = null;
    let pendingSectionKey = null;
    let pendingFacultyId = null;
    let pendingMessageId = null;
    let pendingActionData = null;
    // Check if password verification is still valid (5 minutes)
    function isPasswordVerified() {
        const verifiedTime = <?= $_SESSION['password_verified'] ?? 0 ?>;
        const currentTime = Math.floor(Date.now() / 1000);
        return (currentTime - verifiedTime) < 300; // 5 minutes
    }
    
    // Show password verification modal
    function requirePasswordVerification(action, sectionKey = null, facultyId = null, messageId = null) {
        if (isPasswordVerified()) {
            // Password already verified recently, proceed directly
            executePendingAction(action, sectionKey, facultyId, messageId);
            return;
        }
        
        // Store pending action details
        pendingAction = action;
        pendingSectionKey = sectionKey;
        pendingFacultyId = facultyId;
        pendingMessageId = messageId;
        
        // Reset and show modal
        $('#verify-password').val('').removeClass('is-invalid');
        $('#password-error').text('');
        $('#pending-action').val(action);
        $('#pending-section-key').val(sectionKey);
        $('#pending-faculty-id').val(facultyId);
        $('#pending-message-id').val(messageId);
        $('#passwordVerifyModal').modal('show');
    }
    
    // Execute the pending action after verification
    function executePendingAction(action, sectionKey, facultyId, messageId) {
        switch(action) {
            case 'edit_section':
                loadSectionForEdit(sectionKey);
                break;
            case 'add_faculty':
                showFacultyModal();
                break;
            case 'edit_faculty':
                loadFacultyForEdit(facultyId);
                break;
            case 'delete_faculty':
                showDeleteFacultyModal(facultyId);
                break;
            case 'delete_message':
                showDeleteMessageModal(messageId);
                break;
        }
    }
    
    // Password verification handler
    $('#confirm-password').click(function() {
        const password = $('#verify-password').val();
        
        if (!password) {
            $('#verify-password').addClass('is-invalid');
            $('#password-error').text('Password is required');
            return;
        }
        
        // Verify password via AJAX
        $.ajax({
            url: 'verify_password.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ password: password }),
            success: function(response) {
                if (response.success) {
                    $('#passwordVerifyModal').modal('hide');
                    // Execute the pending action
                    executePendingAction(pendingAction, pendingSectionKey, pendingFacultyId, pendingMessageId);
                    
                    // Clear pending variables
                    pendingAction = null;
                    pendingSectionKey = null;
                    pendingFacultyId = null;
                    pendingMessageId = null;
                } else {
                    $('#verify-password').addClass('is-invalid');
                    $('#password-error').text(response.message);
                }
            },
            error: function() {
                $('#verify-password').addClass('is-invalid');
                $('#password-error').text('An error occurred during verification');
            }
        });
    });
    
    // Enhanced Edit Section Handler with Password Verification
    $('.edit-section').click(function() {
        const sectionKey = $(this).data('section-key');
        requirePasswordVerification('edit_section', sectionKey);
    });
    
    // Enhanced Add Faculty Button with Password Verification
    $('#add-faculty-btn').click(function() {
        requirePasswordVerification('add_faculty');
    });
    
    // Enhanced Edit Faculty Button with Password Verification
    $(document).on('click', '.edit-faculty', function() {
        const facultyId = $(this).data('id');
        requirePasswordVerification('edit_faculty', null, facultyId);
    });
    
    // Enhanced Delete Faculty Button with Password Verification
    $(document).on('click', '.delete-faculty', function() {
        const facultyId = $(this).data('id');
        requirePasswordVerification('delete_faculty', null, facultyId);
    });
    
    // Enhanced Delete Message Button with Password Verification
    $(document).on('click', '.delete-message', function() {
        const messageId = $(this).data('id');
        requirePasswordVerification('delete_message', null, null, messageId);
    });
    
    // Handle modal close events - FIXED CLOSE BUTTONS
    $('#passwordVerifyModal .btn-secondary, #passwordVerifyModal .close').click(function() {
        $('#passwordVerifyModal').modal('hide');
        // Clear pending variables when modal is closed
        pendingAction = null;
        pendingSectionKey = null;
        pendingFacultyId = null;
        pendingMessageId = null;
    });
    
    // Also handle when modal is hidden by other means (clicking backdrop, etc.)
    $('#passwordVerifyModal').on('hidden.bs.modal', function() {
        // Clear pending variables when modal is hidden
        pendingAction = null;
        pendingSectionKey = null;
        pendingFacultyId = null;
        pendingMessageId = null;
    });
    
    // Original functions (modified to remove direct event handlers)
    function loadSectionForEdit(sectionKey) {
        // Reset form and show loading
        $('#section-form')[0].reset();
        $('#current-image').empty();
        $('#current-faculty-image').empty();
        
        // Hide all special fields
        $('#hero-fields, #contact-fields, #events-fields, #academic-programs-fields, #graduate-programs-fields, #academic-calendar-fields').hide();
        
        // Show loading
        $('#section-key').val(sectionKey);
        $('#editSectionModal .modal-title').text('Loading...');
        
        // Fetch section data
        $.post('', {
            action: 'get_section',
            section_key: sectionKey
        }, function(response) {
            const section = response;
            $('#editSectionModal .modal-title').text('Edit ' + section.section_name);
            $('#section-title').val(section.title);
            $('#section-content').val(section.content);
            
            // Show current image if exists
            if (section.image_url) {
                $('#current-image').html(`<img src="${section.image_url}" class="img-thumbnail" style="max-height: 150px;">`);
            }
            
            // Parse meta data if exists
            let metaData = null;
            if (section.meta_data) {
                try {
                    metaData = JSON.parse(section.meta_data);
                } catch (e) {
                    console.error('Error parsing meta data:', e);
                }
            }
            
            // Show appropriate fields based on section type
            switch(sectionKey) {
                case 'hero_title':
                    $('#hero-fields').show();
                    if (metaData && metaData.logo_image) {
                        $('#current-logo-image').html(`<img src="${metaData.logo_image}" class="img-thumbnail" style="max-height: 150px;">`);
                    }
                    break;
                    
                case 'contact_info':
                    $('#contact-fields').show();
                    if (metaData) {
                        $('#office-hours').val(metaData.office_hours || '');
                        $('#days').val(metaData.days || '');
                        $('#location').val(metaData.location || '');
                        $('#phone').val(metaData.phone || '');
                        $('#office-phone').val(metaData.office_phone || '');
                        $('#email').val(metaData.email || '');
                    }
                    break;
                    
                case 'upcoming_events':
                    $('#events-fields').show();
                    if (metaData && metaData.events) {
                        metaData.events.forEach((event, index) => {
                            const i = index + 1;
                            $(`[name="event_name_${i}"]`).val(event.name || '');
                            $(`[name="event_date_${i}"]`).val(event.date || '');
                        });
                    }
                    break;
                    
                case 'academic_programs':
                    $('#academic-programs-fields').show();
                    if (metaData && metaData.programs) {
                        metaData.programs.forEach((program, index) => {
                            const i = index + 1;
                            $(`[name="academic_program_name_${i}"]`).val(program.name || '');
                            $(`[name="academic_program_desc_${i}"]`).val(program.description || '');
                        });
                    }
                    break;
                    
                case 'academic_calendar':
                    $('#academic-calendar-fields').show();
                    if (metaData && metaData.events) {
                        metaData.events.forEach((event, index) => {
                            const i = index + 1;
                            $(`[name="calendar_event_name_${i}"]`).val(event.name || '');
                            $(`[name="calendar_event_date_${i}"]`).val(event.date || '');
                        });
                    }
                    break;
            }
            
            $('#editSectionModal').modal('show');
        });
    }
    
    function showFacultyModal() {
        $('#faculty-form')[0].reset();
        $('#faculty-id').val('new');
        $('#faculty-modal-title').text('Add Faculty Member');
        $('#current-faculty-image').empty();
        $('#facultyModal').modal('show');
    }
    
    function loadFacultyForEdit(facultyId) {
        $.post('', {
            action: 'get_faculty'
        }, function(response) {
            const faculty = response.find(f => f.id == facultyId);
            if (faculty) {
                $('#faculty-id').val(faculty.id);
                $('#faculty-name').val(faculty.name);
                $('#faculty-position').val(faculty.position);
                $('#faculty-email').val(faculty.email);
                $('#faculty-phone').val(faculty.phone);
                $('#faculty-specialization').val(faculty.specialization);
                $('#faculty-display-order').val(faculty.display_order);
                $('#faculty-chairperson').prop('checked', faculty.is_chairperson == 1);
                
                if (faculty.profile_image) {
                    $('#current-faculty-image').html(`<img src="${faculty.profile_image}" class="img-thumbnail" style="max-height: 150px;">`);
                } else {
                    $('#current-faculty-image').empty();
                }
                
                $('#faculty-modal-title').text('Edit Faculty Member');
                $('#facultyModal').modal('show');
            }
        });
    }
    
    function showDeleteFacultyModal(facultyId) {
        $('#delete-id').val(facultyId);
        $('#deleteModal').modal('show');
    }
    
    function showDeleteMessageModal(messageId) {
        $('#delete-message-id').val(messageId);
        $('#deleteMessageModal').modal('show');
    }
    
    // Section Form Submission (unchanged)
    $('#section-form').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Section updated successfully!');
                    $('#editSectionModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while updating the section.');
            }
        });
    });
    
    // Faculty Form Submission (unchanged)
    $('#faculty-form').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Faculty member saved successfully!');
                    $('#facultyModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while saving the faculty member.');
            }
        });
    });
    
    // Confirm Delete Faculty (unchanged)
    $('#confirm-delete').click(function() {
        const facultyId = $('#delete-id').val();
        
        $.post('', {
            action: 'delete_faculty',
            id: facultyId
        }, function(response) {
            if (response.success) {
                alert('Faculty member deleted successfully!');
                $('#deleteModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        });
    });
    
    // View Message Handler (unchanged)
    $('.view-message').click(function() {
        const messageId = $(this).data('id');
        const $clickedButton = $(this);
        
        // Fetch message details
        $.post('', {
            action: 'get_message',
            id: messageId
        }, function(response) {
            if (response.success) {
                const message = response.message;
                
                // Populate modal with message data
                $('#message-from').text(message.name);
                $('#message-contact_no').text(message.contact_no);
                $('#message-date').text(new Date(message.created_at).toLocaleString());
                $('#message-subject').text(message.subject || '(No Subject)');
                $('#message-content').text(message.message);
                $('#message-department').text(message.department_code);
                
                // Update UI to show it's been read if it was new
                if (message.status === 'new') {
                    $clickedButton.closest('tr').removeClass('font-weight-bold');
                    $clickedButton.closest('tr').find('.badge')
                        .removeClass('bg-warning text-dark')
                        .addClass('bg-success text-white')
                        .text('Read');
                }
                
                $('#viewMessageModal').modal('show');
            } else {
                alert('Error loading message: ' + response.message);
            }
        }).fail(function() {
            alert('An error occurred while loading the message.');
        });
    });

    // Handle cancel button click for view message modal
    $('#viewMessageModal .btn-secondary, #viewMessageModal .close').click(function() {
        $('#viewMessageModal').modal('hide');
    });

    // Filter Messages (unchanged)
    $('.filter-message').click(function(e) {
        e.preventDefault();
        const status = $(this).data('status');
        
        $('#messagesTable tbody tr').each(function() {
            if (status === 'all') {
                $(this).show();
            } else {
                const rowStatus = $(this).find('.badge').text().toLowerCase();
                $(this).toggle(rowStatus === status);
            }
        });
    });

    // Confirm Message Delete (unchanged)
    $('#confirm-message-delete').click(function() {
        const messageId = $('#delete-message-id').val();
        
        $.post('', {
            action: 'delete_message',
            id: messageId
        }, function(response) {
            if (response.success) {
                alert('Message deleted successfully!');
                $('#deleteMessageModal').modal('hide');
                // Remove the message row from the table
                $(`button[data-id="${messageId}"]`).closest('tr').remove();
            } else {
                alert('Error: ' + response.message);
            }
        });
    });

    // Handle cancel button click for delete message modal
    $('#deleteMessageModal .btn-secondary, #deleteMessageModal .close').click(function() {
        $('#deleteMessageModal').modal('hide');
    });
    
    // Handle cancel button click for delete faculty modal
    $('#deleteModal .btn-secondary, #deleteModal .close').click(function() {
        $('#deleteModal').modal('hide');
    });
    
    // Handle cancel button click for edit section modal
    $('#editSectionModal .btn-secondary, #editSectionModal .close').click(function() {
        $('#editSectionModal').modal('hide');
    });
    
    // Handle cancel button click for faculty modal
    $('#facultyModal .btn-secondary, #facultyModal .close').click(function() {
        $('#facultyModal').modal('hide');
    });
    
    // Allow Enter key in password verification
    $('#verify-password').keypress(function(e) {
        if (e.which === 13) {
            $('#confirm-password').click();
        }
    });
});
  
  // ========== EMAIL VERIFICATION FOR PASSWORD CHANGES ==========

let pendingUserData = null;
let pendingUserId = null;
let pendingUserEmail = null;
let verificationToken = null;

// Handle user form submission with email verification for password changes
function handleUserFormSubmit(formData, password, confirmPassword, action, userId, userEmail) {
    // Validate passwords if provided
    if (password !== '') {
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return false;
        }
        if (password.length < 6) {
            alert('Password must be at least 6 characters long!');
            return false;
        }
        
        // If password is being changed and it's an edit action, show email verification
        if (action === 'edit') {
            // Store the form data for later submission
            pendingUserData = formData;
            pendingUserId = userId;
            pendingUserEmail = userEmail;
            
            // Send verification code to user's email
            sendPasswordChangeVerificationCode(userId, userEmail, password);
            return false; // Prevent default submission
        }
    }
    
    // If no password change or it's a new user, submit directly
    return true;
}

function sendPasswordChangeVerificationCode(userId, userEmail, newPassword) {
    fetch('send_password_verification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            user_id: userId,
            email: userEmail,
            password: newPassword,
            action: 'password_change'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show verification code modal
            const modal = new bootstrap.Modal(document.getElementById('verificationCodeModal'));
            modal.show();
            
            // Store verification token
            verificationToken = data.token;
            
            // Start timers
            startCodeExpiryTimer();
            startResendTimer();
            
        } else {
            alert('Error sending verification code: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending verification code');
    });
}

// Timer functions for verification code
function startCodeExpiryTimer() {
    let timeLeft = 600; // 10 minutes
    const timerElement = document.getElementById('codeExpiryTimer');
    const expiryTime = new Date().getTime() + (timeLeft * 1000);
    
    window.codeExpiryInterval = setInterval(function() {
        const now = new Date().getTime();
        const distance = expiryTime - now;
        
        if (distance <= 0) {
            clearInterval(window.codeExpiryInterval);
            if (timerElement) {
                timerElement.textContent = 'Code expired';
                timerElement.className = 'text-danger';
            }
            const verificationCodeInput = document.getElementById('verification_code');
            const confirmButton = document.getElementById('confirmVerification');
            if (verificationCodeInput) verificationCodeInput.disabled = true;
            if (confirmButton) confirmButton.disabled = true;
        } else {
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            if (timerElement) {
                timerElement.textContent = `Code expires in: ${minutes}:${seconds.toString().padStart(2, '0')}`;
            }
        }
    }, 1000);
}

function startResendTimer() {
    let timeLeft = 60; // 60 seconds
    const resendBtn = document.getElementById('resendCodeBtn');
    const resendTimer = document.getElementById('resendTimer');
    
    if (resendBtn) resendBtn.disabled = true;
    
    window.resendInterval = setInterval(function() {
        timeLeft--;
        if (resendTimer) resendTimer.textContent = `(${timeLeft}s)`;
        
        if (timeLeft <= 0) {
            clearInterval(window.resendInterval);
            if (resendBtn) resendBtn.disabled = false;
            if (resendTimer) resendTimer.textContent = '';
        }
    }, 1000);
}

// Handle resend code
document.getElementById('resendCodeBtn').addEventListener('click', function() {
    if (pendingUserId && pendingUserEmail) {
        // Get the password from the stored form data
        const password = pendingUserData.get('password');
        sendPasswordChangeVerificationCode(pendingUserId, pendingUserEmail, password);
        
        // Restart timers
        startResendTimer();
    }
});

// Handle verification code submission
document.getElementById('confirmVerification').addEventListener('click', function() {
    const verificationCode = document.getElementById('verification_code').value;
    const errorDiv = document.getElementById('verificationError');
    
    if (!verificationCode || verificationCode.length !== 6) {
        errorDiv.textContent = 'Please enter a valid 6-digit verification code';
        errorDiv.classList.remove('d-none');
        return;
    }
    
    // Verify the code
    fetch('verify_password_code.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            token: verificationToken,
            code: verificationCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Submit the user form
            submitUserForm(pendingUserData);
            
            // Clear timers
            clearInterval(window.codeExpiryInterval);
            clearInterval(window.resendInterval);
            
            // Close modal
            const verifyModal = bootstrap.Modal.getInstance(document.getElementById('verificationCodeModal'));
            verifyModal.hide();
            
            // Clear stored data
            pendingUserData = null;
            pendingUserId = null;
            pendingUserEmail = null;
            verificationToken = null;
            
            // Clear verification fields
            document.getElementById('verification_code').value = '';
            const timerElement = document.getElementById('codeExpiryTimer');
            if (timerElement) {
                timerElement.textContent = 'Code expires in: 10:00';
                timerElement.className = 'text-muted';
            }
            
        } else {
            errorDiv.textContent = data.error || 'Invalid verification code';
            errorDiv.classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.textContent = 'Error verifying code';
        errorDiv.classList.remove('d-none');
    });
});

function submitUserForm(formData) {
    fetch('user_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User saved successfully!');
            location.reload();
        } else {
            alert('Error saving user: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving user');
    });
}

// Reset verification modal when hidden
document.getElementById('verificationCodeModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('verification_code').value = '';
    document.getElementById('verificationError').classList.add('d-none');
    
    // Clear timers
    clearInterval(window.codeExpiryInterval);
    clearInterval(window.resendInterval);
    
    // Reset UI elements
    const timerElement = document.getElementById('codeExpiryTimer');
    const resendBtn = document.getElementById('resendCodeBtn');
    const resendTimer = document.getElementById('resendTimer');
    
    if (timerElement) {
        timerElement.textContent = 'Code expires in: 10:00';
        timerElement.className = 'text-muted';
    }
    if (resendBtn) resendBtn.disabled = false;
    if (resendTimer) resendTimer.textContent = '';
    
    // Clear pending data
    pendingUserData = null;
    pendingUserId = null;
    pendingUserEmail = null;
    verificationToken = null;
});