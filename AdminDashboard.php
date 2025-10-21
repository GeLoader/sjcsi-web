<?php
// admin/dashboard.php - Admin Dashboard with Database Integration
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config.php';

$user = $_SESSION['user'];

// Get statistics from database
try {
    // Count total users
    $userCountResult = dbQuery("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $userCount = $userCountResult->fetch_assoc()['count'];

    // Count published news
    $newsCountResult = dbQuery("SELECT COUNT(*) as count FROM news WHERE status = 'published'");
    $newsCount = $newsCountResult->fetch_assoc()['count'];

    // Count upcoming events
    $eventsCountResult = dbQuery("SELECT COUNT(*) as count FROM events WHERE status = 'upcoming'");
    $eventsCount = $eventsCountResult->fetch_assoc()['count'];

    // Count active departments
    $deptCountResult = dbQuery("SELECT COUNT(*) as count FROM departments WHERE is_active = 1");
    $deptCount = $deptCountResult->fetch_assoc()['count'];

    // Get recent news
    $recentNewsResult = dbQuery("
        SELECT * FROM news  
        ORDER BY published_at  ASC 
        
    ");
    $recentNews = [];
    while ($row = $recentNewsResult->fetch_assoc()) {
        $recentNews[] = $row;
    }

       // Get recent news
    $recentEventsResult = dbQuery("
        SELECT *
        FROM events  
        
        ORDER BY created_at ASC   
      
    ");
    $recentEvents = [];
    while ($row = $recentEventsResult->fetch_assoc()) {
        $recentEvents[] = $row;
    }

    // Get recent users
    $recentUsersResult = dbQuery("
        SELECT u.*, d.name as dept_name, o.name as office_name
        FROM users u 
        LEFT JOIN departments d ON u.department = d.code 
        LEFT JOIN offices o ON u.office = o.code 
        WHERE u.role != 'admin' AND u.is_active = 1 
        ORDER BY u.created_at DESC 
        
    ");
    $recentUsers = [];
    while ($row = $recentUsersResult->fetch_assoc()) {
        $recentUsers[] = $row;
    }

} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $userCount = $newsCount = $eventsCount = $deptCount = 0;
    $recentNews = $recentUsers = [];
}

$page_title = 'Admin Dashboard';
?>
<?php include BASE_PATH . '/header.php'; ?>

<div class="min-vh-100 bg-light">
    <!-- Header -->
    <header class="bg-white shadow-sm border-bottom py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0 text-dark">Admin Dashboard</h1>
                    <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
              
            </div>
        </div>
    </header>

    <div class="container py-4">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_message']['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['flash_message']['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['flash_message']); ?>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Total Users</p>
                                <h3 class="mb-0"><?php echo number_format($userCount); ?></h3>
                            </div>
                            <i class="fas fa-users text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Published News</p>
                                <h3 class="mb-0"><?php echo number_format($newsCount); ?></h3>
                            </div>
                            <i class="fas fa-file-alt text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Upcoming Events</p>
                                <h3 class="mb-0"><?php echo number_format($eventsCount); ?></h3>
                            </div>
                            <i class="fas fa-calendar text-purple fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted small mb-1">Active Departments</p>
                                <h3 class="mb-0"><?php echo number_format($deptCount); ?></h3>
                            </div>
                            <i class="fas fa-building text-warning fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content TABS -->
        <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
           
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="news-tab" data-bs-toggle="tab" data-bs-target="#news" type="button" style="color:black;">News</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" style="color:black;">Events</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" style="color:black;">User</button>
            </li>
             <li class="nav-item" role="presentation">
                <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button" style="color:black;">Calendar</button>
            </li>
           
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" data-bs-target="#gallery" type="button" style="color:black;">Gallery</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="achievements-tab" data-bs-toggle="tab" data-bs-target="#achievements" type="button" style="color:black;">Achievements</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" style="color:black;">Programs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="admission-tab" data-bs-toggle="tab" data-bs-target="#admission" type="button" style="color:black;">Admission & Enrollment</button>
            </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="chatbot-tab" data-bs-toggle="tab" data-bs-target="#chatbot" type="button" style="color:black;">Chatbot</button>
            </li>
          <!--   <li class="nav-item" role="presentation">
                <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" style="color:black;">Settings</button>
            </li> -->
        </ul>

        <div class="tab-content" id="dashboardTabsContent">
         
   <!-- News Management Tab -->
<div class="tab-pane fade show active" id="news" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">News Management</h2>
            <a href="javascript:void(0);" onclick="addNews()" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create New Article
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Manage news articles and announcements for the website</p>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Published Date</th>
                            <th>Views</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentNews)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No news articles found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentNews as $news): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                        <?php if ($news['excerpt']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($news['excerpt'], 0, 100)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($news['category'] ?? 'Uncategorized'); ?></td>
                                    <td>
                                        <span class="badge <?php echo $news['status'] === 'published' ? 'bg-success' : ($news['status'] === 'draft' ? 'bg-secondary' : 'bg-warning'); ?>">
                                            <?php echo ucfirst($news['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        // Use published_at if available, otherwise use created_at
                                        $displayDate = !empty($news['published_at']) && $news['published_at'] != '0000-00-00 00:00:00' 
                                            ? $news['published_at'] 
                                            : $news['created_at'];
                                        echo date('M d, Y', strtotime($displayDate)); 
                                        ?>
                                    </td>
                                    <td><?php echo number_format($news['views']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary" onclick="viewNews(<?php echo $news['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" onclick="editNews(<?php echo $news['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteNews(<?php echo $news['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


                <!-- Events Management Tab -->
            <div class="tab-pane fade" id="events" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">Events Management</h2>
                    <!--     <a href="event_create.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Event
                        </a> -->
                        <a href="javascript:void(0);" onclick="addEvent()" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Event
                        </a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Manage events for the website</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Views</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentEvents)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No events found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentEvents as $events): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($events['title']); ?></strong>
                                                    <?php if ($events['description']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($events['description'], 0, 100)) . '...'; ?></small>
                                                    <?php endif; ?>
                                                </td>
                                               
                                                <td>
                                                    <span class="badge <?php echo $events['status'] === 'completed' ? 'bg-success' : ($events['status'] === 'upcoming' ? 'bg-success' : 'bg-warning'); ?>">
                                                        <?php echo ucfirst($events['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($events['created_at'])); ?></td>
                                                <td><?php echo number_format($events['views']); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button class="btn btn-outline-primary" onclick="viewEvents(<?php echo $events['id']; ?>)">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-secondary" onclick="editEvents(<?php echo $events['id']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" onclick="deleteEvents(<?php echo $events['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Management Tab -->
            <div class="tab-pane fade" id="users" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0">User Management</h2>
                       <!--  <a href="user_create.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add User
                        </a> -->
                        <a href="javascript:void(0);" onclick="addUser()" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add User
                    </a>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Manage department and office account access</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Department/Office</th>
                                        <th>Last Login</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentUsers)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No users found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentUsers as $userItem): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($userItem['email']); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo ucfirst($userItem['role']); ?></span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if ($userItem['role'] === 'department' && $userItem['dept_name']) {
                                                        echo htmlspecialchars($userItem['dept_name']);
                                                    } elseif ($userItem['role'] === 'office' && $userItem['office_name']) {
                                                        echo htmlspecialchars($userItem['office_name']);
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    echo $userItem['last_login'] ? 
                                                        date('M d, Y H:i', strtotime($userItem['last_login'])) : 
                                                        'Never';
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $userItem['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo $userItem['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button class="btn btn-outline-secondary" onclick="editUser(<?php echo $userItem['id']; ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                              
                                                        <button class="btn btn-outline-danger" onclick="deleteUser(<?php echo $userItem['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

<!-- Gallery Management Tab -->
<div class="tab-pane fade" id="gallery" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Gallery Management</h2>
            <button class="btn btn-primary" onclick="addGalleryItem()">
                <i class="fas fa-plus me-2"></i>Add Gallery Item
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Manage gallery items for the website</p>
            
            <!-- Gallery Items Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="galleryTable">
                    <thead>
                        <tr>
                            <th>Thumbnail</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Images Count</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch gallery items from database
                        try {
                            $galleryItemsResult = dbQuery("
                                SELECT g.*, 
                                       (SELECT COUNT(*) FROM gallery_images WHERE gallery_id = g.id) as image_count 
                                FROM gallery g 
                                ORDER BY g.date DESC
                            ");
                            $galleryItems = [];
                            while ($row = $galleryItemsResult->fetch_assoc()) {
                                $galleryItems[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("Gallery Items Error: " . $e->getMessage());
                            $galleryItems = [];
                        }
                        
                        if (empty($galleryItems)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No gallery items found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($galleryItems as $item): ?>
                                <tr data-id="<?php echo $item['id']; ?>">
                                    <td>
                                        <?php
                                        // Get first image for thumbnail
                                        $thumbnailResult = dbQuery("SELECT image_path FROM gallery_images WHERE gallery_id = ? ORDER BY id LIMIT 1", [$item['id']]);
                                        $thumbnail = $thumbnailResult->fetch_assoc();
                                        ?>
                                        <img src="<?php echo htmlspecialchars($thumbnail ? $thumbnail['image_path'] : 'images/default-thumbnail.jpg'); ?>" 
                                             alt="Thumbnail" style="width: 80px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo ucfirst($item['category']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $item['type'] === 'video' ? 'bg-danger' : 'bg-info'; ?>">
                                            <?php echo ucfirst($item['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $item['image_count']; ?> images</span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($item['date'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary" onclick="viewGalleryItem(<?php echo $item['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" onclick="editGalleryItem(<?php echo $item['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteGalleryItem(<?php echo $item['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Academic Calendar Management Tab -->
<div class="tab-pane fade" id="calendar" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Academic Calendar Management</h2>
        <!--     <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCalendarEventModal">
                <i class="fas fa-plus me-2"></i>Add Event
            </button> -->
            <button class="btn btn-primary" onclick="addCalendarEvent()">
    <i class="fas fa-plus me-2"></i>Add Event
</button>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Manage academic calendar events for the website</p>
            
            <!-- Calendar Events Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="calendarTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch calendar events from database
                        try {
                            $calendarEventsResult = dbQuery("SELECT * FROM academic_calendar ORDER BY start_date ASC");
                            $calendarEvents = [];
                            while ($row = $calendarEventsResult->fetch_assoc()) {
                                $calendarEvents[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("Calendar Events Error: " . $e->getMessage());
                            $calendarEvents = [];
                        }
                        
                        if (empty($calendarEvents)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No calendar events found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($calendarEvents as $event): ?>
                                <tr data-id="<?php echo $event['id']; ?>">
                                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($event['start_date'])); ?></td>
                                    <td><?php echo $event['end_date'] ? date('M d, Y', strtotime($event['end_date'])) : '-'; ?></td>
                                    <td>
                                        <span class="badge <?php 
                                        switch($event['type']) {
                                            case 'enrollment': echo 'bg-primary'; break;
                                            case 'classes': echo 'bg-success'; break;
                                            case 'exams': echo 'bg-warning'; break;
                                            case 'holiday': echo 'bg-danger'; break;
                                            case 'event': echo 'bg-info'; break;
                                            case 'break': echo 'bg-secondary'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?>">
                                            <?php echo ucfirst($event['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" onclick="editCalendarEvent(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteCalendarEvent(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Achievements Management Tab -->
<div class="tab-pane fade" id="achievements" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Achievements Management</h2>
          <!--   <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAchievementModal">
                <i class="fas fa-plus me-2"></i>Add Achievement
            </button> -->
            <button class="btn btn-primary" onclick="addAchievement()">
                <i class="fas fa-plus me-2"></i>Add Achievement
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Manage achievements and awards for the website</p>
            
            <!-- Achievements Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="achievementsTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch achievements from database
                        try {
                            $achievementsResult = dbQuery("SELECT * FROM achievements ORDER BY achievement_date DESC");
                            $achievements = [];
                            while ($row = $achievementsResult->fetch_assoc()) {
                                $achievements[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("Achievements Error: " . $e->getMessage());
                            $achievements = [];
                        }
                        
                        if (empty($achievements)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No achievements found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($achievements as $achievement): ?>
                                <tr data-id="<?php echo $achievement['id']; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($achievement['title']); ?></strong>
                                        <?php if ($achievement['description']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($achievement['description'], 0, 100)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo ucfirst($achievement['category']); ?></span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($achievement['achievement_date'])); ?></td>
                                    <td>
                                        <span class="badge <?php echo $achievement['is_published'] ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $achievement['is_published'] ? 'Published' : 'Draft'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" onclick="editAchievement(<?php echo $achievement['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteAchievement(<?php echo $achievement['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Academic Programs Management Tab -->
<div class="tab-pane fade" id="academic" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Academic Programs Management</h2>
       <!--      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProgramModal">
                <i class="fas fa-plus me-2"></i>Add Program
            </button> -->
            <button class="btn btn-primary" onclick="addAcademicProgram()">
    <i class="fas fa-plus me-2"></i>Add Program
</button>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Manage academic programs for the website</p>
            
            <!-- Programs Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="academicProgramsTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Level</th>
                            <th>Duration</th>
                            <th>Tuition</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch academic programs from database
                        try {
                            $academicProgramsResult = dbQuery("
                                SELECT ap.*, d.name as dept_name 
                                FROM academic_programs ap 
                                LEFT JOIN departments d ON ap.department_code = d.code 
                                ORDER BY ap.level, ap.department_code, ap.name
                            ");
                            $academicPrograms = [];
                            while ($row = $academicProgramsResult->fetch_assoc()) {
                                $academicPrograms[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("Academic Programs Error: " . $e->getMessage());
                            $academicPrograms = [];
                        }
                        
                        if (empty($academicPrograms)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No academic programs found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($academicPrograms as $program): ?>
                                <tr data-id="<?php echo $program['id']; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($program['name']); ?></strong>
                                        <?php if ($program['description']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($program['description'], 0, 100)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($program['dept_name']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo ucfirst($program['level']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($program['duration']); ?></td>
                                    <td><?php echo htmlspecialchars($program['tuition_fee']); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" onclick="editAcademicProgram(<?php echo $program['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteAcademicProgram(<?php echo $program['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Admission & Enrollment Tab -->
<div class="tab-pane fade" id="admission" role="tabpanel">
    <div class="card">
        <div class="card-header">
            <h2 class="h5 mb-0">Admission Requirements & Enrollment Process</h2>
        </div>
        <div class="card-body">
            <ul class="nav nav-pills mb-4" id="admissionTabs" role="tablist" >
                <li class="nav-item" role="presentation" >
                    <button class="nav-link active" id="college-req-tab" data-bs-toggle="pill" data-bs-target="#college-req-admin" type="button" >College Requirements</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shs-req-tab" data-bs-toggle="pill" data-bs-target="#shs-req-admin" type="button">SHS Requirements</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="jhs-req-tab" data-bs-toggle="pill" data-bs-target="#jhs-req-admin" type="button">JHS Requirements</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="enrollment-process-tab" data-bs-toggle="pill" data-bs-target="#enrollment-process-admin" type="button">Enrollment Process</button>
                </li>
            </ul>

            <div class="tab-content" id="admissionTabsContent">
                <!-- College Requirements -->
                <div class="tab-pane fade show active" id="college-req-admin" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h4">College Admission Requirements</h3>
                        <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRequirementModal" data-level="college">
                            <i class="fas fa-plus me-2"></i>Add Requirement
                        </button> -->
                        <button class="btn btn-primary" onclick="addRequirement('college')">
    <i class="fas fa-plus me-2"></i>Add Requirement
</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="collegeRequirementsTable">
                            <thead>
                                <tr>
                                    <th width="50">Order</th>
                                    <th>Requirement</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $collegeReqs = [];
                                try {
                                    $collegeReqsResult = dbQuery("SELECT * FROM admission_requirements WHERE level = 'college' ORDER BY display_order ASC");
                                    while ($row = $collegeReqsResult->fetch_assoc()) {
                                        $collegeReqs[] = $row;
                                    }
                                } catch (Exception $e) {
                                    error_log("College Requirements Error: " . $e->getMessage());
                                }
                                
                                if (empty($collegeReqs)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No requirements found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($collegeReqs as $req): ?>
                                        <tr data-id="<?php echo $req['id']; ?>">
                                            <td><?php echo $req['display_order']; ?></td>
                                            <td><?php echo htmlspecialchars($req['requirement']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-secondary" onclick="editRequirement(<?php echo $req['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="deleteRequirement(<?php echo $req['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SHS Requirements -->
                <div class="tab-pane fade" id="shs-req-admin" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h4">Senior High Admission Requirements</h3>
                      <!--   <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRequirementModal" data-level="shs">
                            <i class="fas fa-plus me-2"></i>Add Requirement
                        </button> -->
                        <button class="btn btn-primary" onclick="addRequirement('shs')">
    <i class="fas fa-plus me-2"></i>Add Requirement
</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="shsRequirementsTable">
                            <thead>
                                <tr>
                                    <th width="50">Order</th>
                                    <th>Requirement</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $shsReqs = [];
                                try {
                                    $shsReqsResult = dbQuery("SELECT * FROM admission_requirements WHERE level = 'shs' ORDER BY display_order ASC");
                                    while ($row = $shsReqsResult->fetch_assoc()) {
                                        $shsReqs[] = $row;
                                    }
                                } catch (Exception $e) {
                                    error_log("SHS Requirements Error: " . $e->getMessage());
                                }
                                
                                if (empty($shsReqs)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No requirements found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($shsReqs as $req): ?>
                                        <tr data-id="<?php echo $req['id']; ?>">
                                            <td><?php echo $req['display_order']; ?></td>
                                            <td><?php echo htmlspecialchars($req['requirement']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-secondary" onclick="editRequirement(<?php echo $req['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="deleteRequirement(<?php echo $req['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- JHS Requirements -->
                <div class="tab-pane fade" id="jhs-req-admin" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h4">Junior High Admission Requirements</h3>
                    <!--     <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRequirementModal" data-level="jhs">
                            <i class="fas fa-plus me-2"></i>Add Requirement
                        </button> -->
                        <button class="btn btn-primary" onclick="addRequirement('jhs')">
    <i class="fas fa-plus me-2"></i>Add Requirement
</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="jhsRequirementsTable">
                            <thead>
                                <tr>
                                    <th width="50">Order</th>
                                    <th>Requirement</th>
                                    <th width="100">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $jhsReqs = [];
                                try {
                                    $jhsReqsResult = dbQuery("SELECT * FROM admission_requirements WHERE level = 'jhs' ORDER BY display_order ASC");
                                    while ($row = $jhsReqsResult->fetch_assoc()) {
                                        $jhsReqs[] = $row;
                                    }
                                } catch (Exception $e) {
                                    error_log("JHS Requirements Error: " . $e->getMessage());
                                }
                                
                                if (empty($jhsReqs)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No requirements found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($jhsReqs as $req): ?>
                                        <tr data-id="<?php echo $req['id']; ?>">
                                            <td><?php echo $req['display_order']; ?></td>
                                            <td><?php echo htmlspecialchars($req['requirement']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-secondary" onclick="editRequirement(<?php echo $req['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="deleteRequirement(<?php echo $req['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

<!-- Enrollment Process -->
<div class="tab-pane fade" id="enrollment-process-admin" role="tabpanel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h4">Enrollment Process Steps</h3>
     <!--    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProcessStepModal">
            <i class="fas fa-plus me-2"></i>Add Step
        </button> -->
        <button class="btn btn-primary" onclick="addProcessStep()">
    <i class="fas fa-plus me-2"></i>Add Step
</button>
    </div>
    
    <ul class="nav nav-pills mb-4" id="enrollmentLevelTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="college-process-tab" data-bs-toggle="pill" data-bs-target="#college-process" type="button">College</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="shs-process-tab" data-bs-toggle="pill" data-bs-target="#shs-process" type="button">Senior High</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="jhs-process-tab" data-bs-toggle="pill" data-bs-target="#jhs-process" type="button">Junior High</button>
        </li>
    </ul>

    <div class="tab-content" id="enrollmentLevelTabsContent">
        <!-- College Process -->
        <div class="tab-pane fade show active" id="college-process" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover" id="collegeProcessTable">
                    <thead>
                        <tr>
                            <th width="50">Step</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $collegeProcess = [];
                        try {
                            $collegeProcessResult = dbQuery("SELECT * FROM enrollment_process WHERE level = 'college' ORDER BY step_number ASC");
                            while ($row = $collegeProcessResult->fetch_assoc()) {
                                $collegeProcess[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("College Process Error: " . $e->getMessage());
                        }
                        
                        if (empty($collegeProcess)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No process steps found for College.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($collegeProcess as $step): ?>
                                <tr data-id="<?php echo $step['id']; ?>">
                                    <td><?php echo $step['step_number']; ?></td>
                                    <td><?php echo htmlspecialchars($step['title']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($step['description'], 0, 100) . (strlen($step['description']) > 100 ? '...' : '')); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" onclick="editProcessStep(<?php echo $step['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteProcessStep(<?php echo $step['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SHS Process -->
        <div class="tab-pane fade" id="shs-process" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover" id="shsProcessTable">
                    <thead>
                        <tr>
                            <th width="50">Step</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $shsProcess = [];
                        try {
                            $shsProcessResult = dbQuery("SELECT * FROM enrollment_process WHERE level = 'shs' ORDER BY step_number ASC");
                            while ($row = $shsProcessResult->fetch_assoc()) {
                                $shsProcess[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("SHS Process Error: " . $e->getMessage());
                        }
                        
                        if (empty($shsProcess)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No process steps found for Senior High School.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($shsProcess as $step): ?>
                                <tr data-id="<?php echo $step['id']; ?>">
                                    <td><?php echo $step['step_number']; ?></td>
                                    <td><?php echo htmlspecialchars($step['title']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($step['description'], 0, 100) . (strlen($step['description']) > 100 ? '...' : '')); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" onclick="editProcessStep(<?php echo $step['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteProcessStep(<?php echo $step['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- JHS Process -->
        <div class="tab-pane fade" id="jhs-process" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover" id="jhsProcessTable">
                    <thead>
                        <tr>
                            <th width="50">Step</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $jhsProcess = [];
                        try {
                            $jhsProcessResult = dbQuery("SELECT * FROM enrollment_process WHERE level = 'jhs' ORDER BY step_number ASC");
                            while ($row = $jhsProcessResult->fetch_assoc()) {
                                $jhsProcess[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("JHS Process Error: " . $e->getMessage());
                        }
                        
                        if (empty($jhsProcess)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No process steps found for Junior High School.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($jhsProcess as $step): ?>
                                <tr data-id="<?php echo $step['id']; ?>">
                                    <td><?php echo $step['step_number']; ?></td>
                                    <td><?php echo htmlspecialchars($step['title']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($step['description'], 0, 100) . (strlen($step['description']) > 100 ? '...' : '')); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" onclick="editProcessStep(<?php echo $step['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteProcessStep(<?php echo $step['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>

<!-- Chatbot Management Tab -->
<div class="tab-pane fade" id="chatbot" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Chatbot Management</h2>
            <button class="btn btn-primary" onclick="addChatbotResponse()">
                <i class="fas fa-plus me-2"></i>Add Response
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Manage chatbot responses and keywords. Click the star icon to show/hide suggestions in the chatbot.</p>
            
            <!-- Chatbot Responses Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="chatbotTable">
                    <thead>
                        <tr>
                            <th width="80" class="text-center">Suggested</th>
                            <th>Keywords</th>
                            <th>Response</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch chatbot responses from database
                        try {
                            $chatbotResponsesResult = dbQuery("SELECT * FROM chatbot_responses ORDER BY is_suggested DESC, id DESC");
                            $chatbotResponses = [];
                            while ($row = $chatbotResponsesResult->fetch_assoc()) {
                                $chatbotResponses[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("Chatbot Responses Error: " . $e->getMessage());
                            $chatbotResponses = [];
                        }
                        
                        if (empty($chatbotResponses)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-robot fs-1 mb-3 d-block"></i>
                                    No chatbot responses found. Click "Add Response" to create one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($chatbotResponses as $response): ?>
                                <tr data-id="<?php echo $response['id']; ?>">
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-link p-0 toggle-suggestion" 
                                                type="button"
                                                data-id="<?php echo $response['id']; ?>"
                                                data-suggested="<?php echo isset($response['is_suggested']) ? $response['is_suggested'] : 0; ?>"
                                                title="<?php echo (isset($response['is_suggested']) && $response['is_suggested']) ? 'Remove from suggestions' : 'Add to suggestions'; ?>"
                                                style="font-size: 1.5rem; text-decoration: none;">
                                            <i class="fas fa-star <?php echo (isset($response['is_suggested']) && $response['is_suggested']) ? 'text-warning' : 'text-muted'; ?>"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <?php 
                                        $keywords = json_decode($response['keywords'], true);
                                        if (is_array($keywords)) {
                                            echo '<span class="badge bg-primary me-1">' . htmlspecialchars($keywords[0]) . '</span>';
                                            if (count($keywords) > 1) {
                                                echo '<span class="badge bg-secondary">+' . (count($keywords) - 1) . ' more</span>';
                                            }
                                        } else {
                                            echo '<span class="badge bg-primary">' . htmlspecialchars($response['keywords']) . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div style="max-width: 400px;">
                                            <?php 
                                            $responseText = $response['response'];
                                            echo htmlspecialchars(substr($responseText, 0, 100));
                                            if (strlen($responseText) > 100) echo '...';
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" onclick="editChatbotResponse(<?php echo $response['id']; ?>)" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteChatbotResponse(<?php echo $response['id']; ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

            <!-- Settings Tab -->
            <div class="tab-pane fade" id="settings" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0">System Settings</h2>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">Configure website settings and preferences</p>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h3 class="h6 mb-3">Website Configuration</h3>
                                <div class="d-grid gap-2">
                                    <a href="settings_general.php" class="btn btn-outline-secondary text-start">
                                        <i class="fas fa-cog me-2"></i>General Settings
                                    </a>
                                    <a href="settings_content.php" class="btn btn-outline-secondary text-start">
                                        <i class="fas fa-file-alt me-2"></i>Content Management
                                    </a>
                                    <a href="settings_permissions.php" class="btn btn-outline-secondary text-start">
                                        <i class="fas fa-users me-2"></i>User Permissions
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="h6 mb-3">Database Management</h3>
                                <div class="d-grid gap-2">
                                    <a href="database_backup.php" class="btn btn-outline-warning text-start">
                                        <i class="fas fa-download me-2"></i>Backup Database
                                    </a>
                                    <a href="database_cleanup.php" class="btn btn-outline-danger text-start">
                                        <i class="fas fa-broom me-2"></i>Cleanup Old Data
                                    </a>
                                    <a href="database_stats.php" class="btn btn-outline-info text-start">
                                        <i class="fas fa-chart-line me-2"></i>Database Statistics
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Gallery Item Modal -->
<div class="modal fade" id="addGalleryItemModal" tabindex="-1" aria-labelledby="addGalleryItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addGalleryItemModalLabel">Add Gallery Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="galleryItemForm" action="gallery_save.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="galleryId" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="events">Events</option>
                                    <option value="department">Department</option>
                                    <option value="institutional">Institutional</option>
                                    <option value="facilities">Facilities</option>
                                    <option value="campus">Campus</option>
                                    <option value="activities">Student Activities</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select class="form-select" id="type" name="type" required onchange="toggleMediaField()">
                                    <option value="">Select Type</option>
                                    <option value="image">Image Gallery</option>
                                    <!-- <option value="video">Video</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Multiple Images Upload Field -->
                    <div class="mb-3" id="imageField">
                        <label for="gallery_images" class="form-label">Gallery Images</label>
                        <input type="file" class="form-control" id="gallery_images" name="gallery_images[]" 
                               multiple accept="image/*" required>
                        <div class="form-text">
                            Select multiple images for the gallery. Supported formats: JPG, PNG, GIF. 
                            Maximum file size: 5MB per image.
                        </div>
                        <div id="imagePreview" class="mt-2 row g-2"></div>
                    </div>
                    
                    <div class="mb-3" id="videoField" style="display: none;">
                        <label for="video_url" class="form-label">Video URL</label>
                        <input type="url" class="form-control" id="video_url" name="video_url" placeholder="https://www.youtube.com/embed/...">
                        <div class="form-text">Enter the embed URL for the video</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Gallery Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Calendar Event Modal -->
<div class="modal fade" id="addCalendarEventModal" tabindex="-1" aria-labelledby="addCalendarEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCalendarEventModalLabel">Add Calendar Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="calendarEventForm" action="calendar_save.php" method="POST">
                <input type="hidden" name="id" id="calendarId" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_title" class="form-label">Event Title</label>
                                <input type="text" class="form-control" id="event_title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_type" class="form-label">Event Type</label>
                                <select class="form-select" id="event_type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="enrollment">Enrollment</option>
                                    <option value="classes">Classes</option>
                                    <option value="exams">Examinations</option>
                                    <option value="holiday">Holiday</option>
                                    <option value="event">Event</option>
                                    <option value="break">Break</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date (optional)</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                                <div class="form-text">Leave empty if it's a single-day event</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="event_description" class="form-label">Description (optional)</label>
                        <textarea class="form-control" id="event_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Achievements Management Tab -->
<div class="tab-pane fade" id="achievements" role="tabpanel">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Achievements Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAchievementModal">
                <i class="fas fa-plus me-2"></i>Add Achievement
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Manage achievements and awards for the website</p>
            
            <!-- Achievements Table -->
            <div class="table-responsive">
                <table class="table table-hover" id="achievementsTable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch achievements from database
                        try {
                            $achievementsResult = dbQuery("SELECT * FROM achievements ORDER BY achievement_date DESC");
                            $achievements = [];
                            while ($row = $achievementsResult->fetch_assoc()) {
                                $achievements[] = $row;
                            }
                        } catch (Exception $e) {
                            error_log("Achievements Error: " . $e->getMessage());
                            $achievements = [];
                        }
                        
                        if (empty($achievements)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No achievements found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($achievements as $achievement): ?>
                                <tr data-id="<?php echo $achievement['id']; ?>">
                                    <td>
                                        <strong><?php echo htmlspecialchars($achievement['title']); ?></strong>
                                        <?php if ($achievement['description']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($achievement['description'], 0, 100)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo ucfirst($achievement['category']); ?></span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($achievement['achievement_date'])); ?></td>
                                    <td>
                                        <span class="badge <?php echo $achievement['is_published'] ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo $achievement['is_published'] ? 'Published' : 'Draft'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-secondary" onclick="editAchievement(<?php echo $achievement['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" onclick="deleteAchievement(<?php echo $achievement['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Requirement Modal -->
<div class="modal fade" id="addRequirementModal" tabindex="-1" aria-labelledby="addRequirementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRequirementModalLabel">Add Requirement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="requirementForm" action="admission_save.php" method="POST">
                <input type="hidden" name="id" id="requirementId" value="">
                <!-- <input type="hidden" name="level" id="requirementLevel" value=""> -->
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="step_level" class="form-label">Level</label>
                        <select class="form-select" id="requirementLevel" name="level" required>
                            <option value="college">College</option>
                            <option value="shs">Senior High School</option>
                            <option value="jhs">Junior High School</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="display_order" class="form-label">Display Order</label>
                        <input type="number" class="form-control" id="display_order" name="display_order" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="requirement" class="form-label">Requirement</label>
                        <textarea class="form-control" id="requirement" name="requirement" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add/Edit Process Step Modal -->
<div class="modal fade" id="addProcessStepModal" tabindex="-1" aria-labelledby="addProcessStepModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProcessStepModalLabel">Add Process Step</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="processStepForm" action="enrollment_process_save.php" method="POST">
                <input type="hidden" name="id" id="processStepId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="process_step_level" class="form-label">Level</label>
                        <select class="form-select" id="process_step_level" name="level" required>
                            <option value="">Select Level</option>
                            <option value="college">College</option>
                            <option value="shs">Senior High School</option>
                            <option value="jhs">Junior High School</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="process_step_number" class="form-label">Step Number</label>
                        <input type="number" class="form-control" id="process_step_number" name="step_number" required min="1">
                    </div>
                    <div class="mb-3">
                        <label for="process_step_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="process_step_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="process_step_description" class="form-label">Description</label>
                        <textarea class="form-control" id="process_step_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="process_step_icon_class" class="form-label">Icon</label>
                                <select class="form-select" id="process_step_icon_class" name="icon_class" required>
                                    <option value="fas fa-user">👤 User</option>
                                    <option value="fas fa-file-alt">📄 Document</option>
                                    <option value="fas fa-check-circle">✅ Check Circle</option>
                                    <option value="fas fa-clipboard-list">📋 Clipboard</option>
                                    <option value="fas fa-id-card">🪪 ID Card</option>
                                    <option value="fas fa-money-bill">💰 Payment</option>
                                    <option value="fas fa-graduation-cap">🎓 Graduation</option>
                                    <option value="fas fa-book">📚 Book</option>
                                    <option value="fas fa-pencil-alt">✏️ Pencil</option>
                                    <option value="fas fa-calendar">📅 Calendar</option>
                                    <option value="fas fa-clock">⏰ Clock</option>
                                    <option value="fas fa-envelope">✉️ Envelope</option>
                                    <option value="fas fa-phone">📞 Phone</option>
                                    <option value="fas fa-map-marker">📍 Location</option>
                                    <option value="fas fa-laptop">💻 Laptop</option>
                                    <option value="fas fa-chart-line">📈 Chart</option>
                                    <option value="fas fa-cog">⚙️ Settings</option>
                                    <option value="fas fa-star">⭐ Star</option>
                                    <option value="fas fa-flag">🚩 Flag</option>
                                    <option value="fas fa-trophy">🏆 Trophy</option>
                                    <option value="fas fa-certificate">📜 Certificate</option>
                                    <option value="fas fa-shield">🛡️ Shield</option>
                                    <option value="fas fa-lock">🔒 Lock</option>
                                    <option value="fas fa-key">🔑 Key</option>
                                    <option value="fas fa-home">🏠 Home</option>
                                    <option value="fas fa-building">🏢 Building</option>
                                    <option value="fas fa-school">🏫 School</option>
                                    <option value="fas fa-university">🎓 University</option>
                                </select>
                                <div class="form-text">Select an icon for this step</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="process_step_color_class" class="form-label">Color</label>
                                <select class="form-select" id="process_step_color_class" name="color_class">
                                    <option value="bg-primary">🔵 Primary (Blue)</option>
                                    <option value="bg-success">🟢 Success (Green)</option>
                                    <option value="bg-purple">🟣 Purple</option>
                                    <option value="bg-warning">🟡 Warning (Yellow)</option>
                                    <option value="bg-danger">🔴 Danger (Red)</option>
                                    <option value="bg-info">🔵 Info (Teal)</option>
                                    <option value="bg-secondary">⚫ Secondary (Gray)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Icon Preview -->
                    <div class="mb-3">
                        <label class="form-label">Icon Preview</label>
                        <div class="d-flex align-items-center">
                            <div id="iconPreview" class="me-3 p-3 rounded" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user fs-4 text-white"></i>
                            </div>
                            <div>
                                <small class="text-muted" id="iconPreviewText">Selected icon will appear here</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Step</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Achievement Modal -->
<div class="modal fade" id="addAchievementModal" tabindex="-1" aria-labelledby="addAchievementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAchievementModalLabel">Add Achievement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="achievementForm" action="achievement_save.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="achievementId" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="achievement_title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="achievement_title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="achievement_category" class="form-label">Category</label>
                                <select class="form-select" id="achievement_category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="academic">Academic</option>
                                    <option value="sports">Sports</option>
                                    <option value="cultural">Cultural</option>
                                    <option value="research">Research</option>
                                    <option value="community">Community Service</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="achievement_date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="achievement_date" name="achievement_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="awarded_to" class="form-label">Awarded To (optional)</label>
                                <input type="text" class="form-control" id="awarded_to" name="awarded_to" placeholder="e.g., Computer Science Department, John Doe">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="achievement_description" class="form-label">Description</label>
                        <textarea class="form-control" id="achievement_description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="achievement_image" class="form-label">Image (optional)</label>
                        <input type="file" class="form-control" id="achievement_image" name="image" accept="image/*">
                        <div class="form-text">Upload an image for this achievement</div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" checked>
                        <label class="form-check-label" for="is_published">Publish immediately</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Achievement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Chatbot Response Modal -->
<div class="modal fade" id="addChatbotResponseModal" tabindex="-1" aria-labelledby="addChatbotResponseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addChatbotResponseModalLabel">Add Chatbot Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="chatbotResponseForm" action="chatbot_save.php" method="POST">
                <input type="hidden" name="id" id="chatbotId" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="keywords" class="form-label">Keywords (comma separated)</label>
                        <input type="text" class="form-control" id="keywords" name="keywords" required>
                        <div class="form-text">Enter keywords that will trigger this response (e.g., admission, tuition, programs)</div>
                    </div>
                    <div class="mb-3">
                        <label for="response" class="form-label">Response</label>
                        <textarea class="form-control" id="response" name="response" rows="5" required></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_suggested" name="is_suggested" value="1">
                        <label class="form-check-label" for="is_suggested">
                            Show as suggested question in chatbot
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Response</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Academic Program Modal -->
<div class="modal fade" id="addProgramModal" tabindex="-1" aria-labelledby="addProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProgramModalLabel">Add Academic Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="academicProgramForm" action="academic_program_save.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="programId" value="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_name" class="form-label">Program Name</label>
                                <input type="text" class="form-control" id="program_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_department" class="form-label">Department</label>
                                <select class="form-select" id="program_department" name="department_code" required>
                                    <option value="">Select Department</option>
                                    <?php
                                    // Fetch departments from database
                                    try {
                                        $deptsResult = dbQuery("SELECT code, name FROM departments WHERE is_active = 1 ORDER BY name");
                                        while ($dept = $deptsResult->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($dept['code']) . '">' . htmlspecialchars($dept['name']) . '</option>';
                                        }
                                    } catch (Exception $e) {
                                        error_log("Departments Error: " . $e->getMessage());
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_level" class="form-label">Level</label>
                                <select class="form-select" id="program_level" name="level" required>
                                    <option value="">Select Level</option>
                                    <option value="college">College</option>
                                    <option value="shs">Senior High School</option>
                                    <option value="jhs">Junior High School</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="program_duration" name="duration" required placeholder="e.g., 4 years">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_units" class="form-label">Units/Credits</label>
                                <input type="text" class="form-control" id="program_units" name="units" placeholder="e.g., 120 units">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_tuition" class="form-label">Tuition Fee</label>
                                <input type="text" class="form-control" id="program_tuition" name="tuition_fee" placeholder="e.g., ₱25,000/semester">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="program_description" class="form-label">Description</label>
                        <textarea class="form-control" id="program_description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <!-- PDF Upload Field -->
                    <div class="mb-3">
                        <label for="program_pdf" class="form-label">Upload PDF</label>
                        <input type="file" class="form-control" id="program_pdf" name="pdf_file" accept=".pdf">
                         
                        <div id="currentPdfFile" class="mt-2" style="display: none;">
                            <small class="text-muted">Current file: <span id="currentPdfFileName"></span></small>
                        </div>
                    </div>
                    
              <!--       <div class="mb-3">
                        <label for="program_link" class="form-label">Department Page Link</label>
                        <select class="form-select" id="program_link" name="learn_more_link">
                            <option value="">Select Department Page</option>
                            <option value="DEPARTMENTcaste.php">CASTE Department Page</option>
                            <option value="DEPARTMENTcit.php">CIT Department Page</option>
                            <option value="DEPARTMENTcoa.php">COA Department Page</option>
                            <option value="DEPARTMENTcba.php">CBA Department Page</option>
                            <option value="DEPARTMENTcje.php">CJE Department Page</option>
                            <option value="DEPARTMENTshs.php">SHS Department Page</option>
                            <option value="DEPARTMENTjhs.php">JHS Department Page</option>
                            <option value="custom">Custom URL</option>
                        </select>
                        <div class="form-text">Select the department page for this program or choose "Custom URL" to enter a specific link</div>
                    </div> -->
                    
                    <div class="mb-3" id="customUrlField" style="display: none;">
                        <label for="program_custom_link" class="form-label">Custom URL</label>
                        <input type="text" class="form-control" id="program_custom_link" name="custom_link" placeholder="e.g., https://example.com/program-details">
                        <div class="form-text">Enter a custom URL for this program</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Program</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Password Verification Modal -->
<div class="modal fade" id="passwordVerifyModal" tabindex="-1" aria-labelledby="passwordVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordVerifyModalLabel">Verify Your Identity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">For security reasons, please enter your password to continue.</p>
                <div class="mb-3">
                    <label for="verify_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="verify_password" required>
                    <div class="form-text">You need to verify your identity to perform this action.</div>
                </div>
                 <div class="invalid-feedback" id="password-error"></div>
                <div id="passwordVerifyError" class="alert alert-danger d-none" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmPasswordVerify">Verify & Continue</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for admin functions -->
<script>

</script>
 
<script src="admindashboard.js"></script>
<style>
/* Admin Dashboard Tab Styles */
.nav-pills .nav-link {
    color: #000 !important; /* Black text for inactive tabs */
    background-color: #f8f9fa; /* Light background for inactive tabs */
    margin: 0 5px;
    border-radius: 20px;
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
    background-color: #e9ecef;
    color: #000 !important;
}

.nav-pills .nav-link.active {
    background-color: #0d6efd !important; /* Blue background for active tab */
    color: #fff !important; /* White text for active tab */
    font-weight: 600;
}

/* Specific styles for admission tabs in admin dashboard */
#admissionTabs .nav-link {
    color: #000 !important;
    background-color: #f8f9fa;
}

#admissionTabs .nav-link:hover {
    background-color: #e9ecef;
    color: #000 !important;
}

#admissionTabs .nav-link.active {
    background-color: #0d6efd !important;
    color: #fff !important;
}

/* Make sure the tab content is visible */
.tab-pane {
    padding: 20px 0;
}


</style>

<script>
// Enhanced toggle function with better error handling
// Enhanced toggle function with better error handling
document.addEventListener('DOMContentLoaded', function() {
    // Handle suggestion toggle buttons
    document.querySelectorAll('.toggle-suggestion').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const currentStatus = parseInt(this.dataset.suggested);
            const newStatus = currentStatus ? 0 : 1;
            const icon = this.querySelector('i');
            const row = this.closest('tr');
            
            console.log('Toggling suggestion:', { id, currentStatus, newStatus });
            
            // Optimistic UI update
            const originalClasses = icon.className;
            const originalTitle = this.title;
            
            if (newStatus) {
                icon.classList.remove('text-muted');
                icon.classList.add('text-warning');
                this.title = 'Remove from suggestions';
                row.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
            } else {
                icon.classList.remove('text-warning');
                icon.classList.add('text-muted');
                this.title = 'Add to suggestions';
                row.style.backgroundColor = '';
            }
            
            // Send request with better error handling
            fetch('chatbot_toggle_suggestion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${id}&is_suggested=${newStatus}`
            })
            .then(response => {
                // First, check if response is OK
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                // Get response as text first to handle non-JSON responses
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (parseError) {
                        console.error('JSON Parse Error:', parseError);
                        console.error('Response text:', text);
                        throw new Error(`Invalid JSON response: ${text.substring(0, 100)}...`);
                    }
                });
            })
            .then(data => {
                console.log('Toggle response:', data);
                
                if (data.success) {
                    // Update dataset
                    this.dataset.suggested = newStatus;
                    
                    // Show success toast
                    showToast(data.message, 'success');
                    
                } else {
                    // Revert UI changes on error
                    revertUIToggle(this, originalClasses, originalTitle, currentStatus);
                    showToast('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Toggle Error:', error);
                // Revert UI changes on error
                revertUIToggle(this, originalClasses, originalTitle, currentStatus);
                showToast('Failed to update: ' + error.message, 'danger');
                
                // Log the full error for debugging
                console.error('Full error:', error);
            });
        });
    });
});

// Function to revert UI changes on error
function revertUIToggle(button, originalClasses, originalTitle, originalStatus) {
    const icon = button.querySelector('i');
    const row = button.closest('tr');
    
    icon.className = originalClasses;
    button.title = originalTitle;
    button.dataset.suggested = originalStatus;
    
    if (originalStatus) {
        row.style.backgroundColor = 'rgba(255, 193, 7, 0.1)';
    } else {
        row.style.backgroundColor = '';
    }
}

// Function to refresh chatbot suggestions (optional)
function refreshChatbotSuggestions() {
    // This would trigger the footer chatbot to reload its suggestions
    if (typeof window.refreshChatbot === 'function') {
        window.refreshChatbot();
    }
}

// Make refresh function globally available
window.refreshChatbotSuggestions = refreshChatbotSuggestions;

// Helper function to show toast notifications
function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    // Remove toast after it's hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

// Helper function to show alerts
function showAlert(type, message) {
    const alertContainer = document.querySelector('.container > .alert');
    if (alertContainer) {
        alertContainer.remove();
    }
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.setAttribute('role', 'alert');
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container');
    container.insertBefore(alert, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }, 5000);
}
  

</script>
<?php include  'footer.php'; ?>