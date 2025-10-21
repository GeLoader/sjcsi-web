<?php
// event_create.php - Create new event
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config.php';

$user = $_SESSION['user'];
$page_title = 'Create Event';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $event_type = $_POST['event_type'] ?? '';
    $status = $_POST['status'] ?? 'upcoming';

    // Validate required fields
    $errors = [];
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($description)) $errors[] = 'Description is required';
    if (empty($event_date)) $errors[] = 'Event date is required';
    if (empty($location)) $errors[] = 'Location is required';

    // Validate date is not in the past
    if (!empty($event_date) && strtotime($event_date) < strtotime(date('Y-m-d'))) {
        $errors[] = 'Event date cannot be in the past';
    }

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = BASE_PATH . '/uploads/events/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $image_path = 'uploads/events/' . $image_name;
        
        if (!move_uploaded_file($_FILES['image']['tmp_name'], BASE_PATH . '/' . $image_path)) {
            $errors[] = 'Failed to upload image';
        }
    }

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO events (title, description, content, event_date, event_time, location, event_type, status, image_url, author_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = dbPrepare($sql);
            $stmt->bind_param('sssssssssi', $title, $description, $content, $event_date, $event_time, $location, $event_type, $status, $image_path, $user['id']);
            $stmt->execute();

            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'Event created successfully!'
            ];
            
            header('Location: AdminDashboard.php');
            exit;

        } catch (Exception $e) {
            $errors[] = 'Error creating event: ' . $e->getMessage();
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
                        <h2 class="h4 mb-0">Create Event</h2>
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

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="3" required
                                          placeholder="Brief description of the event..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="event_date" class="form-label">Event Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="event_date" name="event_date" required
                                           min="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="event_time" class="form-label">Event Time</label>
                                    <input type="time" class="form-control" id="event_time" name="event_time"
                                           value="<?php echo htmlspecialchars($_POST['event_time'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="location" name="location" required
                                           placeholder="Event venue"
                                           value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="event_type" class="form-label">Event Type</label>
                                    <select class="form-select" id="event_type" name="event_type">
                                        <option value="">Select Type</option>
                                        <option value="academic" <?php echo ($_POST['event_type'] ?? '') === 'academic' ? 'selected' : ''; ?>>Academic</option>
                                        <option value="cultural" <?php echo ($_POST['event_type'] ?? '') === 'cultural' ? 'selected' : ''; ?>>Cultural</option>
                                        <option value="sports" <?php echo ($_POST['event_type'] ?? '') === 'sports' ? 'selected' : ''; ?>>Sports</option>
                                        <option value="seminar" <?php echo ($_POST['event_type'] ?? '') === 'seminar' ? 'selected' : ''; ?>>Seminar</option>
                                        <option value="workshop" <?php echo ($_POST['event_type'] ?? '') === 'workshop' ? 'selected' : ''; ?>>Workshop</option>
                                        <option value="ceremony" <?php echo ($_POST['event_type'] ?? '') === 'ceremony' ? 'selected' : ''; ?>>Ceremony</option>
                                        <option value="meeting" <?php echo ($_POST['event_type'] ?? '') === 'meeting' ? 'selected' : ''; ?>>Meeting</option>
                                        <option value="other" <?php echo ($_POST['event_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="upcoming" <?php echo ($_POST['status'] ?? 'upcoming') === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                    <option value="ongoing" <?php echo ($_POST['status'] ?? '') === 'ongoing' ? 'selected' : ''; ?>>Ongoing</option>
                                    <option value="completed" <?php echo ($_POST['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($_POST['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Event Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Optional: Upload event banner/poster (max 5MB)</div>
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label">Detailed Content</label>
                                <textarea class="form-control" id="content" name="content" rows="8"
                                          placeholder="Detailed event information, agenda, requirements, etc..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                                <div class="form-text">Optional: Provide detailed event information</div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="AdminDashboard.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Event</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>