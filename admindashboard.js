

let pendingAction = null;
let pendingActionData = null;

// Password verification function
function verifyPassword(action, data = null) {
    pendingAction = action;
    pendingActionData = data;
    
    const modal = new bootstrap.Modal(document.getElementById('passwordVerifyModal'));
    modal.show();
    
    // Clear previous errors and input
    document.getElementById('verify_password').value = '';
    document.getElementById('passwordVerifyError').classList.add('d-none');
}

// Handle password verification
document.getElementById('confirmPasswordVerify').addEventListener('click', function() {
    const password = document.getElementById('verify_password').value;
    const errorDiv = document.getElementById('passwordVerifyError');
    
    if (!password) {
        errorDiv.textContent = 'Please enter your password.';
        errorDiv.classList.remove('d-none');
        return;
    }
    
    // Verify password via AJAX
    fetch('verify_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({password: password})
    })
    .then(response => response.json())
    .then(data => {
        
        if (data.success) {
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('passwordVerifyModal'));
            modal.hide();
            
            // Execute the pending action
            executePendingAction();
        } else {
            errorDiv.textContent = data.message || 'Invalid password. Please try again.';
            errorDiv.classList.remove('d-none');
        }
    })
    .catch(error => {
        errorDiv.textContent = 'Error verifying password. Please try again.';
        errorDiv.classList.remove('d-none');
    });
});

// Execute the pending action after password verification
function executePendingAction() {
    if (!pendingAction) return;
    
    switch (pendingAction) {
        // Delete operations
        case 'deleteNews':
            performDeleteNews(pendingActionData);
            break;
        case 'deleteUser':
            performDeleteUser(pendingActionData);
            break;
        case 'deleteEvents':
            performDeleteEvents(pendingActionData);
            break;
        case 'deleteGalleryItem':
            performDeleteGalleryItem(pendingActionData);
            break;
        case 'deleteCalendarEvent':
            performDeleteCalendarEvent(pendingActionData);
            break;
        case 'deleteAchievement':
            performDeleteAchievement(pendingActionData);
            break;
        case 'deleteAcademicProgram':
            performDeleteAcademicProgram(pendingActionData);
            break;
        case 'deleteRequirement':
            performDeleteRequirement(pendingActionData);
            break;
        case 'deleteProcessStep':
            performDeleteProcessStep(pendingActionData);
            break;
        case 'deleteChatbotResponse':
            performDeleteChatbotResponse(pendingActionData);
            break;
        
        // Add/Create operations
        case 'addNews':
            performAddNews();
            break;
        case 'addUser':
            performAddUser();
            break;
        case 'addEvent':
            performAddEvent();
            break;
        case 'addGalleryItem':
            performAddGalleryItem();
            break;
        case 'addCalendarEvent':
            performAddCalendarEvent();
            break;
        case 'addAchievement':
            performAddAchievement();
            break;
        case 'addAcademicProgram':
            performAddAcademicProgram();
            break;
        case 'addRequirement':
            performAddRequirement();
            break;
        case 'addProcessStep':
            performAddProcessStep();
            break;
        case 'addChatbotResponse':
            performAddChatbotResponse();
            break;
        
        // Edit/Update operations
        case 'editNews':
            performEditNews(pendingActionData);
            break;
        case 'editUser':
            performEditUser(pendingActionData);
            break;
        case 'editEvent':
            performEditEvent(pendingActionData);
            break;
        case 'editGalleryItem':
            performEditGalleryItem(pendingActionData);
            break;
        case 'editCalendarEvent':
            performEditCalendarEvent(pendingActionData);
            break;
        case 'editAchievement':
            performEditAchievement(pendingActionData);
            break;
        case 'editAcademicProgram':
            performEditAcademicProgram(pendingActionData);
            break;
        case 'editRequirement':
            performEditRequirement(pendingActionData);
            break;
        case 'editProcessStep':
            performEditProcessStep(pendingActionData);
            break;
        case 'editChatbotResponse':
            performEditChatbotResponse(pendingActionData);
            break;
           // Save operations (MISSING - add these)
        case 'saveGalleryItem':
            performSaveGalleryItem(pendingActionData);
            break;
        case 'saveCalendarEvent':
            performSaveCalendarEvent(pendingActionData);
            break;
        case 'saveAchievement':
            performSaveAchievement(pendingActionData);
            break;
        case 'saveAcademicProgram':
            performSaveAcademicProgram(pendingActionData);
            break;
        case 'saveChatbotResponse':
            performSaveChatbotResponse(pendingActionData);
            break;
        case 'saveRequirement':
            performSaveRequirement(pendingActionData);
            break;
        case 'saveProcessStep':
            performSaveProcessStep(pendingActionData);
            break;

        default:
            console.error('Unknown action:', pendingAction);
    }
    
    // Reset pending action
    pendingAction = null;
    pendingActionData = null;
}

// Reset password modal when hidden
document.getElementById('passwordVerifyModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('verify_password').value = '';
    document.getElementById('passwordVerifyError').classList.add('d-none');
    pendingAction = null;
    pendingActionData = null;
});

// Allow Enter key to trigger verification
document.getElementById('verify_password').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('confirmPasswordVerify').click();
    }
});

function deleteNews(id) {
    if (confirm('Are you sure you want to delete this news article?')) {
        verifyPassword('deleteNews', id);
    }
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        verifyPassword('deleteUser', id);
    }
}

function deleteEvents(id) {
    if (confirm('Are you sure you want to delete this event?')) {
        verifyPassword('deleteEvents', id);
    }
}

function deleteGalleryItem(id) {
    if (confirm('Are you sure you want to delete this gallery item?')) {
        verifyPassword('deleteGalleryItem', id);
    }
}

function deleteCalendarEvent(id) {
    if (confirm('Are you sure you want to delete this calendar event?')) {
        verifyPassword('deleteCalendarEvent', id);
    }
}

function deleteAchievement(id) {
    if (confirm('Are you sure you want to delete this achievement?')) {
        verifyPassword('deleteAchievement', id);
    }
}

function deleteAcademicProgram(id) {
    if (confirm('Are you sure you want to delete this academic program?')) {
        verifyPassword('deleteAcademicProgram', id);
    }
}

function deleteRequirement(id) {
    if (confirm('Are you sure you want to delete this requirement?')) {
        verifyPassword('deleteRequirement', id);
    }
}

function deleteProcessStep(id) {
    if (confirm('Are you sure you want to delete this process step?')) {
        verifyPassword('deleteProcessStep', id);
    }
}

function deleteChatbotResponse(id) {
    if (confirm('Are you sure you want to delete this chatbot response?')) {
        verifyPassword('deleteChatbotResponse', id);
    }
}

// Actual delete functions (called after password verification)
function performDeleteNews(id) {
    fetch('news_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting news: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

function performDeleteUser(id) {
    fetch('user_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting user: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

function performDeleteEvents(id) {
    fetch('event_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting event: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

function performDeleteGalleryItem(id) {
    fetch('gallery_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`#galleryTable tr[data-id="${id}"]`);
            if (row) row.remove();
            alert('Gallery item deleted successfully');
        } else {
            alert('Error deleting gallery item: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting gallery item: ' + error);
    });
}

function performDeleteCalendarEvent(id) {
    fetch('academic_calendar_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`#calendarTable tr[data-id="${id}"]`);
            if (row) row.remove();
            alert('Calendar event deleted successfully');
        } else {
            alert('Error deleting calendar event: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting calendar event: ' + error);
    });
}

function performDeleteAchievement(id) {
    fetch('achievement_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`#achievementsTable tr[data-id="${id}"]`);
            if (row) row.remove();
            alert('Achievement deleted successfully');
        } else {
            alert('Error deleting achievement: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting achievement: ' + error);
    });
}

function performDeleteAcademicProgram(id) {
    fetch('academic_program_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`#academicProgramsTable tr[data-id="${id}"]`);
            if (row) row.remove();
            alert('Academic program deleted successfully');
        } else {
            alert('Error deleting program: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting program: ' + error);
    });
}

function performDeleteRequirement(id) {
    fetch('admission_delete.php?id=' + id, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting requirement: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting requirement: ' + error);
    });
}

function performDeleteProcessStep(id) {
    fetch('enrollment_process_delete.php?id=' + id, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error deleting process step: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting process step: ' + error);
    });
}

function performDeleteChatbotResponse(id) {
    fetch('chatbot_delete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`#chatbotTable tr[data-id="${id}"]`).remove();
            alert('Response deleted successfully');
        } else {
            alert('Error deleting response: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting response: ' + error);
    });
}

// ========== UPDATED FUNCTIONS WITH PASSWORD VERIFICATION ==========

// News Functions
function editNews(id) {
    verifyPassword('editNews', id);
}

function deleteNews(id) {
    if (confirm('Are you sure you want to delete this news article?')) {
        verifyPassword('deleteNews', id);
    }
}

function addNews() {
    verifyPassword('addNews');
}

function viewNews(id) {
    window.open(`news.php?id=${id}`, '_blank');
}

// User Functions
function editUser(id) {
    verifyPassword('editUser', id);
}

function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user?')) {
        verifyPassword('deleteUser', id);
    }
}

function addUser() {
    verifyPassword('addUser');
}

// Events Functions
function editEvents(id) {
    verifyPassword('editEvent', id);
}

function deleteEvents(id) {
    if (confirm('Are you sure you want to delete this event?')) {
        verifyPassword('deleteEvents', id);
    }
}

function addEvent() {
    verifyPassword('addEvent');
}

function viewEvents(id) {
    window.open(`events.php?id=${id}`, '_blank');
}

// Chatbot Functions
function editChatbotResponse(id) {
    verifyPassword('editChatbotResponse', id);
}

function deleteChatbotResponse(id) {
    if (confirm('Are you sure you want to delete this chatbot response?')) {
        verifyPassword('deleteChatbotResponse', id);
    }
}

function addChatbotResponse() {
    verifyPassword('addChatbotResponse');
}

// Gallery Functions
function editGalleryItem(id) {
    verifyPassword('editGalleryItem', id);
}

function deleteGalleryItem(id) {
    if (confirm('Are you sure you want to delete this gallery item?')) {
        verifyPassword('deleteGalleryItem', id);
    }
}

function addGalleryItem() {
    verifyPassword('addGalleryItem');
}

// Calendar Functions
function editCalendarEvent(id) {
    verifyPassword('editCalendarEvent', id);
}

function deleteCalendarEvent(id) {
    if (confirm('Are you sure you want to delete this calendar event?')) {
        verifyPassword('deleteCalendarEvent', id);
    }
}

function addCalendarEvent() {
    verifyPassword('addCalendarEvent');
}

// Requirements Functions
function editRequirement(id) {
    verifyPassword('editRequirement', id);
}

function deleteRequirement(id) {
    if (confirm('Are you sure you want to delete this requirement?')) {
        verifyPassword('deleteRequirement', id);
    }
}

function addRequirement() {
    verifyPassword('addRequirement');
}

// Process Steps Functions
function editProcessStep(id) {
    verifyPassword('editProcessStep', id);
}

function deleteProcessStep(id) {
    if (confirm('Are you sure you want to delete this process step?')) {
        verifyPassword('deleteProcessStep', id);
    }
}

function addProcessStep() {
    verifyPassword('addProcessStep');
}

// Achievements Functions
function editAchievement(id) {
    verifyPassword('editAchievement', id);
}

function deleteAchievement(id) {
    if (confirm('Are you sure you want to delete this achievement?')) {
        verifyPassword('deleteAchievement', id);
    }
}

function addAchievement() {
    verifyPassword('addAchievement');
}

// Academic Programs Functions
function editAcademicProgram(id) {
    verifyPassword('editAcademicProgram', id);
}

function deleteAcademicProgram(id) {
    if (confirm('Are you sure you want to delete this academic program?')) {
        verifyPassword('deleteAcademicProgram', id);
    }
}

function addAcademicProgram() {
    verifyPassword('addAcademicProgram');
}

// ========== PERFORM ACTIONS AFTER PASSWORD VERIFICATION ==========

// Perform Edit Operations
function performEditNews(id) {
    window.location.href = `news_edit.php?id=${id}`;
}

function performEditUser(id) {
    window.location.href = `user_edit.php?id=${id}`;
}

function performEditEvent(id) {
    window.location.href = `event_edit.php?id=${id}`;
}

function performEditGalleryItem(id) {
    // Fetch gallery item data via AJAX
    fetch('gallery_get.php?id=' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Populate the form
                document.getElementById('galleryId').value = data.item.id;
                document.getElementById('title').value = data.item.title;
                document.getElementById('category').value = data.item.category;
                document.getElementById('type').value = data.item.type;
                document.getElementById('date').value = data.item.date;
                document.getElementById('description').value = data.item.description;
                
                // Handle media fields based on type
                toggleMediaField();
                
                if (data.item.type === 'video') {
                    document.getElementById('video_url').value = data.item.video_url || '';
                }
                
                // Show the modal
                var modal = new bootstrap.Modal(document.getElementById('addGalleryItemModal'));
                modal.show();
            } else {
                alert('Error fetching gallery item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching gallery item: ' + error.message);
        });
}

function performEditCalendarEvent(id) {
    // Fetch calendar event data via AJAX
    fetch('academic_calendar_get.php?id=' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Populate the form
                document.getElementById('calendarId').value = data.event.id;
                document.getElementById('event_title').value = data.event.title;
                document.getElementById('event_type').value = data.event.type;
                document.getElementById('start_date').value = data.event.start_date;
                document.getElementById('end_date').value = data.event.end_date || '';
                document.getElementById('event_description').value = data.event.description || '';
                
                // Show the modal
                var modal = new bootstrap.Modal(document.getElementById('addCalendarEventModal'));
                modal.show();
            } else {
                alert('Error fetching calendar event: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching calendar event: ' + error.message);
        });
}

function performEditRequirement(id) {
    fetch('get_requirement.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const requirement = data.data;
                const modal = new bootstrap.Modal(document.getElementById('addRequirementModal'));
                
                document.getElementById('requirementId').value = requirement.id;
                document.getElementById('requirementLevel').value = requirement.level;
                document.getElementById('display_order').value = requirement.display_order;
                document.getElementById('requirement').value = requirement.requirement;
                
                document.getElementById('addRequirementModalLabel').textContent = 'Edit ' + requirement.level.toUpperCase() + ' Requirement';
                
                modal.show();
            } else {
                alert('Error loading requirement: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading requirement');
        });
}

function performEditProcessStep(id) {
    fetch('get_process_step.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const step = data.data;
                const modal = new bootstrap.Modal(document.getElementById('addProcessStepModal'));
                
                // Populate all form fields with unique IDs
                document.getElementById('processStepId').value = step.id;
                document.getElementById('process_step_level').value = step.level;
                document.getElementById('process_step_number').value = step.step_number;
                document.getElementById('process_step_title').value = step.title;
                document.getElementById('process_step_description').value = step.description;
                document.getElementById('process_step_icon_class').value = step.icon_class || 'fas fa-circle';
                document.getElementById('process_step_color_class').value = step.color_class || 'bg-primary';
                
                document.getElementById('addProcessStepModalLabel').textContent = 'Edit Process Step';
                
                modal.show();
            } else {
                alert('Error loading process step: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading process step');
        });
}

function performEditAchievement(id) {
    // Fetch achievement data via AJAX
    fetch('achievement_get.php?id=' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Populate the form
                document.getElementById('achievementId').value = data.achievement.id;
                document.getElementById('achievement_title').value = data.achievement.title;
                document.getElementById('achievement_category').value = data.achievement.category;
                document.getElementById('achievement_date').value = data.achievement.achievement_date;
                document.getElementById('awarded_to').value = data.achievement.awarded_to || '';
                document.getElementById('achievement_description').value = data.achievement.description;
                document.getElementById('is_published').checked = data.achievement.is_published;
                
                // Show the modal
                var modal = new bootstrap.Modal(document.getElementById('addAchievementModal'));
                modal.show();
            } else {
                alert('Error fetching achievement: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching achievement: ' + error.message);
        });
}

function performEditAcademicProgram(id) {
    // Fetch program data via AJAX
    fetch('academic_program_get.php?id=' + id)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Populate the form
                document.getElementById('programId').value = data.program.id;
                document.getElementById('program_name').value = data.program.name;
                document.getElementById('program_department').value = data.program.department_code;
                document.getElementById('program_level').value = data.program.level;
                document.getElementById('program_duration').value = data.program.duration;
                document.getElementById('program_units').value = data.program.units || '';
                document.getElementById('program_tuition').value = data.program.tuition_fee || '';
                document.getElementById('program_description').value = data.program.description;
                document.getElementById('program_link').value = data.program.learn_more_link || '';
                
                // Show current PDF file if exists
                const currentPdfFile = document.getElementById('currentPdfFile');
                const currentPdfFileName = document.getElementById('currentPdfFileName');
                
                if (data.program.learn_more_link && data.program.learn_more_link.includes('files/program_')) {
                    const fileName = data.program.learn_more_link.split('/').pop();
                    currentPdfFileName.textContent = fileName;
                    currentPdfFile.style.display = 'block';
                } else {
                    currentPdfFile.style.display = 'none';
                }
                
                // Show the modal
                var modal = new bootstrap.Modal(document.getElementById('addProgramModal'));
                modal.show();
            } else {
                alert('Error fetching program: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching program: ' + error.message);
        });
}

function performEditChatbotResponse(id) {
    // Fetch response data via AJAX
    fetch('chatbot_get.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the form
                document.getElementById('chatbotId').value = data.response.id;
                document.getElementById('keywords').value = Array.isArray(data.response.keywords) ? 
                    data.response.keywords.join(', ') : data.response.keywords;
                document.getElementById('response').value = data.response.response;
                document.getElementById('suggested_question_id').value = data.response.suggested_question_id || '';
                document.getElementById('add_as_suggested').checked = false;
                               document.getElementById('is_suggested').checked = data.chatbot.is_suggested == 1;
                
                
                document.getElementById('addChatbotResponseModalLabel').textContent = 'Edit Chatbot Response';
                
                // Show the modal
                var modal = new bootstrap.Modal(document.getElementById('addChatbotResponseModal'));
                modal.show();
            } else {
                alert('Error fetching response: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error);
        });
}

function performEditSuggestedQuestion(id) {
    // Fetch question data via AJAX
    fetch('get_suggested_question.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // For simplicity, using prompt - you can create a separate modal for this
                const newQuestion = prompt('Edit suggested question:', data.question.question);
                if (newQuestion && newQuestion.trim() !== '') {
                    updateSuggestedQuestion(id, newQuestion.trim());
                }
            } else {
                alert('Error loading question: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading question');
        });
}

function performDeleteSuggestedQuestion(id) {
    fetch('delete_suggested_question.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({id: id})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`#suggestedQuestionsTable tr[data-id="${id}"]`) || 
                        document.querySelector(`#allSuggestedQuestionsTable tr[data-id="${id}"]`);
            if (row) row.remove();
            alert('Suggested question deleted successfully');
        } else {
            alert('Error deleting suggested question: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting suggested question: ' + error);
    });
}

function performManageSuggestedQuestions() {
    const modal = new bootstrap.Modal(document.getElementById('manageSuggestedQuestionsModal'));
    modal.show();
}

// Perform Add Operations
function performAddNews() {
    window.location.href = 'news_create.php';
}

function performAddUser() {
    window.location.href = 'user_create.php';
}

function performAddEvent() {
    window.location.href = 'event_create.php';
}

function performAddGalleryItem() {
    const modal = new bootstrap.Modal(document.getElementById('addGalleryItemModal'));
    modal.show();
}

function performAddCalendarEvent() {
    const modal = new bootstrap.Modal(document.getElementById('addCalendarEventModal'));
    modal.show();
}

function performAddRequirement() {
    const modal = new bootstrap.Modal(document.getElementById('addRequirementModal'));
    modal.show();
}

function performAddProcessStep() {
    const modal = new bootstrap.Modal(document.getElementById('addProcessStepModal'));
    modal.show();
}

function performAddAchievement() {
    const modal = new bootstrap.Modal(document.getElementById('addAchievementModal'));
    modal.show();
}

function performAddAcademicProgram() {
    const modal = new bootstrap.Modal(document.getElementById('addProgramModal'));
    modal.show();
}

function performAddChatbotResponse() {

      // Reset form
    document.getElementById('chatbotResponseForm').reset();
    document.getElementById('chatbotId').value = '';
    document.getElementById('addChatbotResponseModalLabel').textContent = 'Add Chatbot Response';
    

    const modal = new bootstrap.Modal(document.getElementById('addChatbotResponseModal'));
    modal.show();
}

// Enhanced form submission handlers with password verification
document.getElementById('galleryItemForm').addEventListener('submit', function(e) {
    e.preventDefault();
    verifyPassword('saveGalleryItem', new FormData(this));
});

document.getElementById('calendarEventForm').addEventListener('submit', function(e) {
    e.preventDefault();
     const formData = new FormData(this);
    
    fetch('academic_calendar_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addCalendarEventModal'));
            modal.hide();
            
            // Reload the page to see changes
            location.reload();
        } else {
            alert('Error saving calendar event: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving calendar event: ' + error.message);
    });
});

document.getElementById('achievementForm').addEventListener('submit', function(e) {
    e.preventDefault();
   const formData = new FormData(this);
    
    fetch('achievement_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addAchievementModal'));
            modal.hide();
            
            // Reload the page to see changes
            location.reload();
        } else {
            alert('Error saving achievement: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving achievement: ' + error.message);
    });
});

document.getElementById('academicProgramForm').addEventListener('submit', function(e) {
    e.preventDefault();
   // verifyPassword('saveAcademicProgram', new FormData(this));
    performSaveAcademicProgram(new FormData(this))
});

document.getElementById('chatbotResponseForm').addEventListener('submit', function(e) {
    e.preventDefault();
   // verifyPassword('saveChatbotResponse', new FormData(this));
    performSaveChatbotResponse(new FormData(this))
});

document.getElementById('requirementForm').addEventListener('submit', function(e) {
    e.preventDefault();
    //verifyPassword('saveRequirement', new FormData(this));
    performSaveRequirement(new FormData(this))

});

document.getElementById('processStepForm').addEventListener('submit', function(e) {
    e.preventDefault();
    //verifyPassword('saveProcessStep', new FormData(this));
    performSaveProcessStep(new FormData(this))
});

// ========== PERFORM SAVE OPERATIONS AFTER PASSWORD VERIFICATION ==========

// Perform Save Operations
function performSaveGalleryItem(formData) {
    fetch('gallery_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addGalleryItemModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving gallery item: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving gallery item: ' + error.message);
    });
}

function performSaveCalendarEvent(formData) {
    fetch('academic_calendar_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addCalendarEventModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving calendar event: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving calendar event: ' + error.message);
    });
}

function performSaveAchievement(formData) {
    fetch('achievement_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addAchievementModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving achievement: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving achievement: ' + error.message);
    });
}

function performSaveAcademicProgram(formData) {
    fetch('academic_program_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProgramModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving program: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving program: ' + error.message);
    });
}

function performSaveChatbotResponse(formData) {
    fetch('chatbot_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addChatbotResponseModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving response: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error saving response: ' + error);
    });
}

function performSaveRequirement(formData) {
    fetch('admission_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addRequirementModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving requirement: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving requirement: ' + error.message);
    });
}

function performSaveProcessStep(formData) {
    fetch('enrollment_process_save.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = bootstrap.Modal.getInstance(document.getElementById('addProcessStepModal'));
            modal.hide();
            location.reload();
        } else {
            alert('Error saving process step: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving process step: ' + error.message);
    });
}

// ========== SUGGESTED QUESTIONS MANAGEMENT ==========

function updateSuggestedQuestion(id, question) {
    const formData = new FormData();
    formData.append('id', id);
    formData.append('question', question);
    
    fetch('update_suggested_question.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating question: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating question: ' + error);
    });
}

function saveSuggestedQuestionsOrder() {
    const rows = document.querySelectorAll('#allSuggestedQuestionsTable tbody tr');
    const updates = [];
    
    rows.forEach(row => {
        const id = row.getAttribute('data-id');
        const order = row.querySelector('input[name="display_order"]').value;
        const isActive = row.querySelector('input[name="is_active"]').checked;
        
        updates.push({
            id: id,
            display_order: order,
            is_active: isActive ? 1 : 0
        });
    });
    
    // Send updates via AJAX
    fetch('update_suggested_questions_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ updates: updates })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Questions updated successfully');
            location.reload();
        } else {
            alert('Error updating questions: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating questions: ' + error);
    });
}

// Add new suggested question form
document.getElementById('addSuggestedQuestionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('add_suggested_question.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            this.reset();
            alert('Question added successfully');
            location.reload();
        } else {
            alert('Error adding question: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error adding question: ' + error);
    });
});

function toggleMediaField() {
    const type = document.getElementById('type').value;
    const imageField = document.getElementById('imageField');
    const videoField = document.getElementById('videoField');
    
    if (type === 'video') {
        imageField.style.display = 'none';
        videoField.style.display = 'block';
        document.getElementById('thumbnail').removeAttribute('required');
        document.getElementById('video_url').setAttribute('required', 'true');
    } else {
        imageField.style.display = 'block';
        videoField.style.display = 'none';
        document.getElementById('thumbnail').setAttribute('required', 'true');
        document.getElementById('video_url').removeAttribute('required');
    }
}

document.getElementById('addGalleryItemModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('galleryItemForm').reset();
    document.getElementById('galleryId').value = '';
    document.getElementById('imageField').style.display = 'block';
    document.getElementById('videoField').style.display = 'none';
});

document.getElementById('addCalendarEventModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('calendarEventForm').reset();
    document.getElementById('calendarId').value = '';
});

document.getElementById('addAchievementModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('achievementForm').reset();
    document.getElementById('achievementId').value = '';
});

document.getElementById('addProgramModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('academicProgramForm').reset();
    document.getElementById('programId').value = '';
    document.getElementById('currentPdfFile').style.display = 'none';
});

document.getElementById('addChatbotResponseModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('chatbotResponseForm').reset();
    document.getElementById('chatbotId').value = '';
    //document.getElementById('add_as_suggested').checked = false;
});

document.getElementById('manageSuggestedQuestionsModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('addSuggestedQuestionForm').reset();
});

// ========== TOGGLE SUGGESTED QUESTIONS VISIBILITY ==========

document.getElementById('showSuggestedQuestions').addEventListener('change', function() {
    const isEnabled = this.checked;
    
    fetch('toggle_suggested_questions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ enabled: isEnabled })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Error updating settings: ' + data.message);
            this.checked = !isEnabled; // Revert the toggle
        }
    })
    .catch(error => {
        alert('Error updating settings: ' + error);
        this.checked = !isEnabled; // Revert the toggle
    });
});

document.getElementById('addRequirementModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('requirementForm').reset();
    document.getElementById('requirementId').value = '';
});

document.getElementById('addProcessStepModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('processStepForm').reset();
    document.getElementById('processStepId').value = '';
});

// Handle modal show events
document.getElementById('addRequirementModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const level = button.getAttribute('data-level');
    const modal = this;
    
    modal.querySelector('#requirementLevel').value = level;
    modal.querySelector('#addRequirementModalLabel').textContent = 'Add ' + level.toUpperCase() + ' Requirement';
    
    // Reset form
    modal.querySelector('#requirementId').value = '';
    modal.querySelector('#display_order').value = '';
    modal.querySelector('#requirement').value = '';
});

document.getElementById('addProcessStepModal').addEventListener('show.bs.modal', function (event) {
    const modal = this;
    
    // Reset form
    modal.querySelector('#processStepId').value = '';
    modal.querySelector('#step_number').value = '';
    modal.querySelector('#title').value = '';
    modal.querySelector('#description').value = '';
    modal.querySelector('#icon_class').value = 'fas fa-circle';
    modal.querySelector('#color_class').value = 'bg-primary';
});

function viewNews(id) {
    window.open(`news.php?id=${id}`, '_blank');
}

function viewEvents(id) {
    window.open(`events.php?id=${id}`, '_blank');
}

