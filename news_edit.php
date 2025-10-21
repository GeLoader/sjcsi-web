<?php
// news_edit.php - Edit existing news article
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config.php';

$user = $_SESSION['user'];
$page_title = 'Edit News Article';

// Get news ID from URL
$news_id = $_GET['id'] ?? 0;
if (!$news_id || !is_numeric($news_id)) {
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Invalid news article ID'
    ];
    header('Location: AdminDashboard.php');
    exit;
}

// Get existing news data
try {
    $sql = "SELECT * FROM news WHERE id = ?";
    $stmt = dbPrepare($sql);
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'News article not found'
        ];
        header('Location: AdminDashboard.php');
        exit;
    }
    
    $news = $result->fetch_assoc();
} catch (Exception $e) {
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Error fetching news article: ' . $e->getMessage()
    ];
    header('Location: AdminDashboard.php');
    exit;
}

// Initialize variables
$status = $news['status'] ?? 'draft';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    // Determine which button was clicked
    $status = $news['status']; // Default to current status
    if (isset($_POST['publish'])) {
        $status = 'published';
    } elseif (isset($_POST['draft'])) {
        $status = 'draft';
    }
    
    $publish_date = $_POST['publish_date'] ?? '';
    $image_url = $news['image_url']; // Keep existing image URL by default

    // Validate required fields
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($content)) $errors[] = 'Content is required';
    
    // Validate publish date
    if (!empty($publish_date) && !strtotime($publish_date)) {
        $errors[] = 'Invalid publish date format';
    }

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $upload_dir = 'uploads/news/';
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_extension;
            $destination = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_url = $destination;
                
                // Delete old image if it exists and is in the uploads directory
                if (!empty($news['image_url']) && 
                    file_exists($news['image_url']) && 
                    strpos($news['image_url'], $upload_dir) === 0) {
                    unlink($news['image_url']);
                }
            } else {
                $errors[] = 'Failed to upload image';
            }
        } else {
            $errors[] = 'Invalid image format. Only JPG, PNG, and GIF are allowed.';
        }
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE news SET title = ?, excerpt = ?, content = ?, category = ?, status = ?, 
                    image_url = ?, updated_at = NOW(), published_at = ? WHERE id = ?";
            $stmt = dbPrepare($sql);
            
            // SIMPLIFIED: Handle published_at logic - no scheduled status needed
            if ($status === 'published') {
                if (!empty($publish_date)) {
                    // Use the specified publish date
                    $published_at = date('Y-m-d H:i:s', strtotime($publish_date));
                } elseif (!empty($news['published_at'])) {
                    // Keep existing publish date if no new date specified
                    $published_at = $news['published_at'];
                } else {
                    // Set to current date/time if publishing for the first time
                    $published_at = date('Y-m-d H:i:s');
                }
            } else {
                // For non-published status, use the specified date or null
                $published_at = !empty($publish_date) ? date('Y-m-d H:i:s', strtotime($publish_date)) : null;
            }
            
            $stmt->bind_param('sssssssi', $title, $excerpt, $content, $category, $status, $image_url, $published_at, $news_id);
            $stmt->execute();

            $_SESSION['flash_message'] = [
                'type' => 'success',
                'message' => 'News article updated successfully!'
            ];
            
            header('Location: AdminDashboard.php');
            exit;

        } catch (Exception $e) {
            $errors[] = 'Error updating news article: ' . $e->getMessage();
        }
    }
} else {
    // Pre-populate form with existing data
    $_POST = $news;
    
    // Format published_at for date input
    if (!empty($news['published_at'])) {
        $_POST['publish_date'] = date('Y-m-d', strtotime($news['published_at']));
    } else {
        $_POST['publish_date'] = '';
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
                        <h2 class="h4 mb-0">Edit News Article</h2>
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
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required
                                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Excerpt</label>
                                <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                                          placeholder="Brief summary of the article..."><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category">
                                        <option value="">Select Category</option>
                                        <option value="announcement" <?php echo ($_POST['category'] ?? '') === 'announcement' ? 'selected' : ''; ?>>Announcement</option>
                                        <option value="academic" <?php echo ($_POST['category'] ?? '') === 'academic' ? 'selected' : ''; ?>>Academic</option>
                                        <option value="events" <?php echo ($_POST['category'] ?? '') === 'events' ? 'selected' : ''; ?>>Events</option>
                                        <option value="student_life" <?php echo ($_POST['category'] ?? '') === 'student_life' ? 'selected' : ''; ?>>Student Life</option>
                                        <option value="faculty" <?php echo ($_POST['category'] ?? '') === 'faculty' ? 'selected' : ''; ?>>Faculty</option>
                                        <option value="administration" <?php echo ($_POST['category'] ?? '') === 'administration' ? 'selected' : ''; ?>>Administration</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="publish_date" class="form-label">Publish Date</label>
                                    <input type="date" class="form-control" id="publish_date" name="publish_date"
                                           value="<?php echo htmlspecialchars($_POST['publish_date'] ?? ''); ?>">
                                    <div class="form-text">
                                        Set the date when this article should appear on the website.
                                        <?php if (!empty($news['published_at'])): ?>
                                            <br>Currently set to: <?php echo date('M j, Y', strtotime($news['published_at'])); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">News Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Optional: Upload news image (max 5MB). Leave empty to keep existing image.</div>
                                
                                <?php if (!empty($news['image_url'])): ?>
                                    <div class="mt-2">
                                        <p class="mb-1">Current Image:</p>
                                        <?php if (file_exists($news['image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($news['image_url']); ?>" 
                                                 alt="Current news image" 
                                                 style="max-width: 200px; max-height: 150px; object-fit: cover;"
                                                 class="border rounded">
                                        <?php else: ?>
                                            <p class="text-muted">Image not found: <?php echo htmlspecialchars($news['image_url']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="10" required 
                                          placeholder="Write the full article content here..."><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="AdminDashboard.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" name="draft" value="1" class="btn btn-outline-primary">Save as Draft</button>
                                <button type="submit" name="publish" value="1" class="btn btn-primary">Update & Publish</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>