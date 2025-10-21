<?php
// DEPARTMENTCIT_dashboard.php - CIT Department Dashboard
session_start();
require_once __DIR__ . '/config.php';
require_once BASE_PATH . '/database.php';

// Check if user is logged in and has the right role
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['user']['role'] !== 'department' || $_SESSION['user']['department'] !== 'CIT') {
    header('Location: login.php');
    exit;
}

// Handle AJAX requests FIRST - before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'get_section':
                $section_key = $_POST['section_key'];
                $stmt = dbPrepare("SELECT * FROM CIT_page WHERE section_key = ?");
                $stmt->bind_param('s', $section_key);
                $stmt->execute();
                $result = $stmt->get_result();
                echo json_encode($result->fetch_assoc());
                exit;
                
            case 'update_section':
                $section_key = $_POST['section_key'];
                $title = $_POST['title'];
                $content = $_POST['content'];
                $user_id = $_SESSION['user']['id'];
                
                // Handle image upload
                $image_url = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = BASE_PATH . '/uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $image_name = uniqid() . '-' . basename($_FILES['image']['name']);
                    $image_path = $upload_dir . $image_name;
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                        $image_url = 'uploads/' . $image_name;
                    }
                }
                
                // Handle logo image upload for hero section
                $logo_image = null;
                if ($section_key === 'hero_title' && isset($_FILES['logo_image']) && $_FILES['logo_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = BASE_PATH . '/uploads/';
                    $logo_name = uniqid() . '-' . basename($_FILES['logo_image']['name']);
                    $logo_path = $upload_dir . $logo_name;
                    if (move_uploaded_file($_FILES['logo_image']['tmp_name'], $logo_path)) {
                        $logo_image = 'uploads/' . $logo_name;
                    }
                }
                
                // Handle contact information fields
                $meta_data = null;
                if ($section_key === 'contact_info') {
                    $contact_meta = [
                        'office_hours' => $_POST['office_hours'] ?? '',
                        'days' => $_POST['days'] ?? '',
                        'location' => $_POST['location'] ?? '',
                        'phone' => $_POST['phone'] ?? '',
                        'office_phone' => $_POST['office_phone'] ?? '',
                        'email' => $_POST['email'] ?? ''
                    ];
                    $meta_data = json_encode($contact_meta);
                } 
                // Handle upcoming events fields
                elseif ($section_key === 'upcoming_events') {
                    $events_meta = ['events' => []];
                    
                    // Process up to 5 events
                    for ($i = 1; $i <= 5; $i++) {
                        $event_name = $_POST["event_name_$i"] ?? '';
                        $event_date = $_POST["event_date_$i"] ?? '';
                        
                        if (!empty($event_name) && !empty($event_date)) {
                            $events_meta['events'][] = [
                                'name' => $event_name,
                                'date' => $event_date
                            ];
                        }
                    }
                    
                    $meta_data = json_encode($events_meta);
                }
                // Handle academic programs fields
                elseif ($section_key === 'academic_programs') {
                    $programs_meta = ['programs' => []];
                    
                    // Process up to 6 academic programs
                    for ($i = 1; $i <= 6; $i++) {
                        $program_name = $_POST["academic_program_name_$i"] ?? '';
                        $program_desc = $_POST["academic_program_desc_$i"] ?? '';
                        
                        if (!empty($program_name)) {
                            $programs_meta['programs'][] = [
                                'name' => $program_name,
                                'description' => $program_desc
                            ];
                        }
                    }
                    
                    $meta_data = json_encode($programs_meta);
                }
                // Handle graduate programs fields
                elseif ($section_key === 'graduate_programs') {
                    $programs_meta = ['programs' => []];
                    
                    // Process up to 6 graduate programs
                    for ($i = 1; $i <= 6; $i++) {
                        $program_name = $_POST["graduate_program_name_$i"] ?? '';
                        $program_desc = $_POST["graduate_program_desc_$i"] ?? '';
                        
                        if (!empty($program_name)) {
                            $programs_meta['programs'][] = [
                                'name' => $program_name,
                                'description' => $program_desc
                            ];
                        }
                    }
                    
                    $meta_data = json_encode($programs_meta);
                }
                // Handle academic calendar fields
                elseif ($section_key === 'academic_calendar') {
                    $calendar_meta = ['events' => []];
                    
                    // Process up to 10 calendar events
                    for ($i = 1; $i <= 10; $i++) {
                        $event_name = $_POST["calendar_event_name_$i"] ?? '';
                        $event_date = $_POST["calendar_event_date_$i"] ?? '';
                        
                        if (!empty($event_name) && !empty($event_date)) {
                            $calendar_meta['events'][] = [
                                'name' => $event_name,
                                'date' => $event_date
                            ];
                        }
                    }
                    
                    $meta_data = json_encode($calendar_meta);
                } else {
                    // For other sections, preserve existing meta data
                    $stmt = dbPrepare("SELECT meta_data FROM CIT_page WHERE section_key = ?");
                    $stmt->bind_param('s', $section_key);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $existing_meta = $result->fetch_assoc();
                    $meta_data = $existing_meta['meta_data'] ?? null;
                    
                    // If we have a logo image, update the meta data for hero section
                    if ($logo_image && $meta_data) {
                        $meta_array = json_decode($meta_data, true);
                        $meta_array['logo_image'] = $logo_image;
                        $meta_data = json_encode($meta_array);
                    } elseif ($logo_image) {
                        // Create new meta data with logo image
                        $meta_data = json_encode(['logo_image' => $logo_image]);
                    }
                }
                
                // Prepare the update query
                if ($image_url) {
                    $stmt = dbPrepare("UPDATE CIT_page SET title = ?, content = ?, meta_data = ?, image_url = ?, updated_by = ? WHERE section_key = ?");
                    $stmt->bind_param('ssssis', $title, $content, $meta_data, $image_url, $user_id, $section_key);
                } else {
                    $stmt = dbPrepare("UPDATE CIT_page SET title = ?, content = ?, meta_data = ?, updated_by = ? WHERE section_key = ?");
                    $stmt->bind_param('sssis', $title, $content, $meta_data, $user_id, $section_key);
                }
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Section updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update section: ' . $stmt->error]);
                }
                exit;
                
            case 'get_faculty':
                $stmt = dbPrepare("SELECT * FROM CIT_faculty WHERE is_active = 1 ORDER BY display_order");
                $stmt->execute();
                $result = $stmt->get_result();
                $faculty = [];
                while ($row = $result->fetch_assoc()) {
                    $faculty[] = $row;
                }
                echo json_encode($faculty);
                exit;
                
            case 'update_faculty':
                $id = $_POST['id'];
                $name = $_POST['name'];
                $position = $_POST['position'];
                $email = $_POST['email'];
                $phone = $_POST['phone'] ?? null;
                $specialization = $_POST['specialization'];
                $is_chairperson = isset($_POST['is_chairperson']) ? 1 : 0;
                $display_order = $_POST['display_order'] ?? 0;
                
                // If this faculty is being set as chairperson, remove chairperson status from others
                if ($is_chairperson) {
                    $reset_stmt = dbPrepare("UPDATE CIT_faculty SET is_chairperson = 0 WHERE is_chairperson = 1");
                    $reset_stmt->execute();
                }
                
                // Handle profile image upload
                $profile_image = null;
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = BASE_PATH . '/uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $image_name = uniqid() . '-' . basename($_FILES['profile_image']['name']);
                    $image_path = $upload_dir . $image_name;
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $image_path)) {
                        $profile_image = 'uploads/' . $image_name;
                    }
                }
                
                if ($id === 'new') {
                    $stmt = dbPrepare("INSERT INTO CIT_faculty (name, position, email, phone, specialization, is_chairperson, profile_image, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param('sssssisi', $name, $position, $email, $phone, $specialization, $is_chairperson, $profile_image, $display_order);
                } else {
                    // If updating existing faculty, check if we need to keep the existing image
                    if ($profile_image === null) {
                        $stmt = dbPrepare("UPDATE CIT_faculty SET name = ?, position = ?, email = ?, phone = ?, specialization = ?, is_chairperson = ?, display_order = ? WHERE id = ?");
                        $stmt->bind_param('sssssiii', $name, $position, $email, $phone, $specialization, $is_chairperson, $display_order, $id);
                    } else {
                        $stmt = dbPrepare("UPDATE CIT_faculty SET name = ?, position = ?, email = ?, phone = ?, specialization = ?, is_chairperson = ?, profile_image = ?, display_order = ? WHERE id = ?");
                        $stmt->bind_param('sssssisii', $name, $position, $email, $phone, $specialization, $is_chairperson, $profile_image, $display_order, $id);
                    }
                }
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Faculty updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update faculty']);
                }
                exit;
                
            case 'delete_faculty':
                $id = $_POST['id'];
                $stmt = dbPrepare("UPDATE CIT_faculty SET is_active = 0 WHERE id = ?");
                $stmt->bind_param('i', $id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Faculty deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete faculty']);
                }
                exit;
             
             case 'get_message':
    $id = $_POST['id'];
    $stmt = dbPrepare("SELECT * FROM department_messages WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();
    
    // Update status to 'read' if it's currently 'new'
    if ($message && $message['status'] === 'new') {
        $update_stmt = dbPrepare("UPDATE department_messages SET status = 'read' WHERE id = ?");
        $update_stmt->bind_param('i', $id);
        $update_stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => $message]);
    exit;

case 'update_message_status':
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $stmt = dbPrepare("UPDATE department_messages SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
    exit;

      case 'delete_message':
                $id = $_POST['id'];
                $stmt = dbPrepare("DELETE FROM department_messages WHERE id = ?");
                $stmt->bind_param('i', $id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Message deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete message']);
                }
                exit;   

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// If we reach here, it's not an AJAX request, so show the dashboard
$page_title = 'CIT Department Dashboard';
require_once 'header.php';

// Get page sections
$sections_query = dbPrepare("SELECT * FROM CIT_page WHERE is_active = 1 ORDER BY display_order");
$sections_query->execute();
$sections = $sections_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Get faculty
$faculty_query = dbPrepare("SELECT * FROM CIT_faculty WHERE is_active = 1 ORDER BY display_order");
$faculty_query->execute();
$faculty = $faculty_query->get_result()->fetch_all(MYSQLI_ASSOC);

// Get recent updates
$recent_query = dbPrepare("SELECT sp.section_name, sp.updated_at, u.email as updated_by_email 
                          FROM CIT_page sp 
                          LEFT JOIN users u ON sp.updated_by = u.id 
                          WHERE sp.updated_at IS NOT NULL 
                          ORDER BY sp.updated_at DESC LIMIT 5");
$recent_query->execute();
$recent_updates = $recent_query->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">CIT Department Dashboard</h1>
            <p class="text-muted">Manage your department's page content and information</p>
        </div>
        <div>
            <a href="<?= url('DEPARTMENTCIT.php') ?>" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-external-link-alt me-2"></i>View Live Page
            </a>
        </div>
    </div>
 
    <div class="row">
        <!-- Page Sections Management -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Page Sections</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Section</th>
                                    <th>Title</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sections as $section): ?>
                                <tr>
                                    <td><?= htmlspecialchars($section['section_name']) ?></td>
                                    <td><?= htmlspecialchars($section['title']) ?></td>
                                    <td><?= $section['updated_at'] ? date('M j, Y', strtotime($section['updated_at'])) : 'Never' ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-section" 
                                                data-section-key="<?= $section['section_key'] ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>



 
            </div>

            <!-- Messages Inbox -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Messages Inbox</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                 aria-labelledby="dropdownMenuLink">
                <div class="dropdown-header">Filter:</div>
                <a class="dropdown-item filter-message" href="#" data-status="all">All Messages</a>
                <a class="dropdown-item filter-message" href="#" data-status="new">Unread Only</a>
                <a class="dropdown-item filter-message" href="#" data-status="replied">Replied</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="messagesTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $messages_query = dbPrepare("SELECT * FROM department_messages WHERE department_code = 'CIT' ORDER BY created_at DESC");
                    $messages_query->execute();
                    $messages = $messages_query->get_result()->fetch_all(MYSQLI_ASSOC);
                    
                    foreach ($messages as $msg):
                        $status_class = '';
                        if ($msg['status'] == 'new') {
                            $status_class = 'bg-warning text-dark';
                        } elseif ($msg['status'] == 'replied') {
                            $status_class = 'bg-success text-white';
                        }
                    ?>
                    <tr class="<?= $msg['status'] == 'new' ? 'font-weight-bold' : '' ?>">
                        <td><?= htmlspecialchars($msg['name']) ?><br>
                            <small class="text-muted"><?= htmlspecialchars($msg['contact_no']) ?></small>
                        </td>
                        <td><?= !empty($msg['subject']) ? htmlspecialchars($msg['subject']) : '(No Subject)' ?></td>
                        <td><?= date('M j, Y g:i A', strtotime($msg['created_at'])) ?></td>
                        <td><span class="badge <?= $status_class ?>"><?= ucfirst($msg['status']) ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-primary view-message" data-id="<?= $msg['id'] ?>">
                                <i class="fas fa-eye"></i> View
                            </button>
                             <button class="btn btn-sm btn-danger delete-message" data-id="<?= $msg['id'] ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div class="modal fade" id="viewMessageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>From:</strong>
                        <p id="message-from" class="mb-1"></p>
                        <small id="message-contact_no" class="text-muted"></small>
                    </div>
                    <div class="col-md-6">
                        <strong>Received:</strong>
                        <p id="message-date" class="mb-1"></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Subject:</strong>
                    <p id="message-subject"></p>
                </div>
                
                <div class="mb-3">
                    <strong>Message:</strong>
                    <div id="message-content" class="border p-3 bg-light rounded"></div>
                </div>
                
                <div class="mb-3">
                    <strong>Department:</strong>
                    <p id="message-department" class="mb-0"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
</div>

<!-- Delete Message Confirmation Modal -->
<div class="modal fade" id="deleteMessageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this message? This action cannot be undone.</p>
                <input type="hidden" id="delete-message-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-message-delete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
// View Message Handler
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
            
            // Set up reply link
            $('#reply-email').attr('href', 'mailto:' + message.email + '?subject=Re: ' + (message.subject || 'Your Inquiry'));
            
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

 // Handle cancel button click - FIX ADDED HERE
    $('#viewMessageModal .btn-secondary').click(function() {
        $('#viewMessageModal').modal('hide');
    });

// Filter Messages
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
// Delete Message Button
$(document).on('click', '.delete-message', function() {
    const messageId = $(this).data('id');
    $('#delete-message-id').val(messageId);
    $('#deleteMessageModal').modal('show');
});

// Confirm Message Delete
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

    $('#deleteMessageModal .btn-secondary').click(function() {
        $('#deleteMessageModal').modal('hide');
    });
</script>
        </div>

        <!-- Activity Feed & Quick Actions -->
        <div class="col-lg-4">
           <!-- Faculty Management -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-success">Faculty Management</h6>
        <button class="btn btn-sm btn-success" id="add-faculty-btn">
            <i class="fas fa-plus"></i> Add Faculty
        </button>
    </div>
    <div class="card-body">
        <div id="faculty-list">
            <?php foreach ($faculty as $member): ?>
            <div class="faculty-item mb-3 p-2 border rounded">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-1">
                            <?= htmlspecialchars($member['name']) ?>
                            <?php if ($member['is_chairperson']): ?>
                                <span class="badge bg-primary ms-2">Chairperson</span>
                            <?php endif; ?>
                        </h6>
                        <small class="text-muted"><?= htmlspecialchars($member['position']) ?></small>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary edit-faculty" 
                                data-id="<?= $member['id'] ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-faculty" 
                                data-id="<?= $member['id'] ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

            <!-- Recent Activity -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Recent Updates</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_updates)): ?>
                        <?php foreach ($recent_updates as $update): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-edit text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-gray-800"><?= htmlspecialchars($update['section_name']) ?></div>
                                <div class="small text-muted">
                                    <?= date('M j, Y g:i A', strtotime($update['updated_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No recent updates</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Section</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="section-form" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_section">
                    <input type="hidden" name="section_key" id="section-key">
                    
                    <div class="form-group">
                        <label for="section-title">Section Title</label>
                        <input type="text" class="form-control" id="section-title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="section-content">Content</label>
                        <textarea class="form-control" id="section-content" name="content" rows="6"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="section-image">Image (Optional)</label>
                        <input type="file" class="form-control-file" id="section-image" name="image">
                        <small class="form-text text-muted">Leave empty to keep current image</small>
                        <div id="current-image" class="mt-2"></div>
                    </div>
                    
                    <!-- Hero Section Special Fields -->
                    <div id="hero-fields" style="display: none;">
                        <div class="form-group">
                            <label for="logo-image">Logo Image (Optional)</label>
                            <input type="file" class="form-control-file" id="logo-image" name="logo_image">
                            <small class="form-text text-muted">Upload a logo for the hero section</small>
                        </div>
                    </div>
                    
                    <!-- Contact Information Special Fields -->
                    <div id="contact-fields" style="display: none;">
                        <div class="form-group">
                            <label for="office-hours">Office Hours</label>
                            <input type="text" class="form-control" id="office-hours" name="office_hours">
                        </div>
                        <div class="form-group">
                            <label for="days">Days</label>
                            <input type="text" class="form-control" id="days" name="days" placeholder="e.g., Monday-Friday">
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" class="form-control" id="location" name="location">
                        </div>
                        <div class="form-group">
                            <label for="phone">Department Line</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="office-phone">Department Office</label>
                            <input type="text" class="form-control" id="office-phone" name="office_phone">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>
                    
                    <!-- Upcoming Events Special Fields -->
                    <div id="events-fields" style="display: none;">
                        <h6 class="mt-3 mb-3">Upcoming Events</h6>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="event_name_<?= $i ?>" 
                                       placeholder="Event Name <?= $i ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="event_date_<?= $i ?>" 
                                       placeholder="Event Date <?= $i ?>">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Academic Programs Special Fields -->
                    <div id="academic-programs-fields" style="display: none;">
                        <h6 class="mt-3 mb-3">Academic Programs</h6>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="mb-2">
                            <input type="text" class="form-control mb-1" name="academic_program_name_<?= $i ?>" 
                                   placeholder="Program Name <?= $i ?>">
                            <textarea class="form-control" name="academic_program_desc_<?= $i ?>" 
                                      rows="2" placeholder="Program Description <?= $i ?>"></textarea>
                        </div>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Graduate Programs Special Fields -->
                    <div id="graduate-programs-fields" style="display: none;">
                        <h6 class="mt-3 mb-3">Graduate Programs</h6>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                        <div class="mb-2">
                            <input type="text" class="form-control mb-1" name="graduate_program_name_<?= $i ?>" 
                                   placeholder="Program Name <?= $i ?>">
                            <textarea class="form-control" name="graduate_program_desc_<?= $i ?>" 
                                      rows="2" placeholder="Program Description <?= $i ?>"></textarea>
                        </div>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Academic Calendar Special Fields -->
                    <div id="academic-calendar-fields" style="display: none;">
                        <h6 class="mt-3 mb-3">Academic Calendar Events</h6>
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="calendar_event_name_<?= $i ?>" 
                                       placeholder="Event Name <?= $i ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="calendar_event_date_<?= $i ?>" 
                                       placeholder="Event Date <?= $i ?>">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Faculty Modal -->
<div class="modal fade" id="facultyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="faculty-modal-title">Add Faculty Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="faculty-form" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_faculty">
                    <input type="hidden" name="id" id="faculty-id" value="new">
                    
                    <div class="form-group">
                        <label for="faculty-name">Name</label>
                        <input type="text" class="form-control" id="faculty-name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="faculty-position">Position</label>
                        <input type="text" class="form-control" id="faculty-position" name="position" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="faculty-email">Email</label>
                        <input type="email" class="form-control" id="faculty-email" name="email">
                    </div>
                    
                    <div class="form-group">
                        <label for="faculty-phone">Phone</label>
                        <input type="text" class="form-control" id="faculty-phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="faculty-specialization">Specialization</label>
                        <input type="text" class="form-control" id="faculty-specialization" name="specialization">
                    </div>
                    
                    <div class="form-group">
                        <label for="faculty-display-order">Display Order</label>
                        <input type="number" class="form-control" id="faculty-display-order" name="display_order" value="0">
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="faculty-chairperson" name="is_chairperson">
                            <label class="form-check-label" for="faculty-chairperson">Department Chairperson</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="faculty-profile-image">Profile Image</label>
                        <input type="file" class="form-control-file" id="faculty-profile-image" name="profile_image">
                        <small class="form-text text-muted">Leave empty to keep current image</small>
                        <div id="current-faculty-image" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this faculty member?</p>
                <input type="hidden" id="delete-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- Password Verification Modal -->
<div class="modal fade" id="passwordVerifyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify Your Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please enter your password to continue with this action:</p>
                <div class="form-group">
                    <label for="verify-password">Password</label>
                    <input type="password" class="form-control" id="verify-password" required>
                    <div class="invalid-feedback" id="password-error"></div>
                </div>
                <input type="hidden" id="pending-action">
                <input type="hidden" id="pending-section-key">
                <input type="hidden" id="pending-faculty-id">
                <input type="hidden" id="pending-message-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-password">Verify & Continue</button>
            </div>
        </div>
    </div>
</div>

<!-- Password Verification Modal -->
<div class="modal fade" id="passwordVerifyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verify Your Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please enter your password to continue with this action:</p>
                <div class="form-group">
                    <label for="verify-password">Password</label>
                    <input type="password" class="form-control" id="verify-password" required>
                    <div class="invalid-feedback" id="password-error"></div>
                </div>
                <input type="hidden" id="pending-action">
                <input type="hidden" id="pending-section-key">
                <input type="hidden" id="pending-faculty-id">
                <input type="hidden" id="pending-message-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm-password">Verify & Continue</button>
            </div>
        </div>
    </div>
</div>

<style>
    .faculty-item:hover {
        background-color: #f8f9fa;
    }
</style>

  <script>
$(document).ready(function() {
    // Global variables to store pending actions
    let pendingAction = null;
    let pendingSectionKey = null;
    let pendingFacultyId = null;
    let pendingMessageId = null;
    
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
 
  </script>
<?php require_once 'footer.php'; ?>