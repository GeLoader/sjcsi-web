<?php
// events.php - View events
require_once __DIR__ . '/config.php';

// For single event view
if (isset($_GET['id'])) {
    $event_id = $_GET['id'] ?? 0;
    if (!$event_id || !is_numeric($event_id)) {
        header('Location: index.php');
        exit;
    }

    try {
        // Get event details
        $sql = "SELECT * FROM events WHERE id = ?";
        $stmt = dbPrepare($sql);
        $stmt->bind_param('i', $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            header('Location: index.php');
            exit;
        }
        
        $event = $result->fetch_assoc();
        
        // Update view count
        $updateSql = "UPDATE events SET views = views + 1 WHERE id = ?";
        $updateStmt = dbPrepare($updateSql);
        $updateStmt->bind_param('i', $event_id);
        $updateStmt->execute();
        
    } catch (Exception $e) {
        error_log("Event View Error: " . $e->getMessage());
        header('Location: index.php');
        exit;
    }

    $page_title = $event['title'];
    require_once BASE_PATH . '/header.php';
    ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article class="event-details">
                    <div class="mb-4">
                        <a href="events.php" class="btn btn-outline-secondary mb-3">
                            <i class="fas fa-arrow-left me-2"></i>Back to Events
                        </a>
                        
                        <?php if (!empty($event['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($event['image_url']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary"><?php echo htmlspecialchars($event['event_type'] ?? 'Event'); ?></span>
                            <span class="badge <?php 
                                echo $event['status'] === 'completed' ? 'bg-success' : 
                                     ($event['status'] === 'upcoming' ? 'bg-primary' : 
                                     ($event['status'] === 'ongoing' ? 'bg-warning' : 'bg-danger')); ?>">
                                <?php echo ucfirst($event['status']); ?>
                            </span>
                        </div>
                        
                        <h1 class="mb-3"><?php echo htmlspecialchars($event['title']); ?></h1>
                        
                        <div class="event-meta mb-4">
                            <div class="mb-2">
                                <i class="fas fa-calendar-day me-2 text-primary"></i>
                                <strong>Date:</strong> 
                                <?php echo date('l, F j, Y', strtotime($event['event_date'])); ?>
                                <?php if ($event['event_time']): ?>
                                    at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($event['location']): ?>
                                <div class="mb-2">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <i class="fas fa-eye me-2 text-primary"></i>
                                <strong>Views:</strong> <?php echo number_format($event['views']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($event['description'])): ?>
                        <div class="lead mb-4 p-3 bg-light rounded">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="event-content mb-5">
                        <?php echo nl2br(htmlspecialchars($event['content'])); ?>
                    </div>
                    
                    <div class="border-top pt-4">
                        <a href="events.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Events
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <?php require_once BASE_PATH . '/footer.php';
    exit;
}

// For events listing
$page_title = 'Events';
require_once BASE_PATH . '/header.php';

try {
    // Get all events
    $sql = "SELECT * FROM events ORDER BY event_date DESC";
    $result = dbQuery($sql);
    $events = [];
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
} catch (Exception $e) {
    error_log("Events Page Error: " . $e->getMessage());
    $events = [];
}
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 class="mb-0">Upcoming Events</h1>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Home
        </a>
    </div>

    <?php if (empty($events)): ?>
        <div class="text-center py-5">
            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No events found</h4>
            <p class="text-muted">Check back later for upcoming events.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($events as $event): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <?php if (!empty($event['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($event['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($event['event_type']); ?></span>
                                <span class="badge <?php 
                                    echo $event['status'] === 'completed' ? 'bg-success' : 
                                         ($event['status'] === 'upcoming' ? 'bg-primary' : 
                                         ($event['status'] === 'ongoing' ? 'bg-warning' : 'bg-danger')); ?>">
                                    <?php echo ucfirst($event['status']); ?>
                                </span>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text text-muted"><?php 
$description = $event['description'] ?? '';
if (strlen($description) > 100) {
    echo htmlspecialchars(substr($description, 0, 100) . '...');
} else {
    echo htmlspecialchars($description);
}
?></p>
                            
                            <div class="small text-muted mb-3">
                                <i class="fas fa-calendar-day me-1"></i>
                                <?php echo date('M j, Y', strtotime($event['event_date'])); ?>
                                <?php if ($event['event_time']): ?>
                                    at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                <?php endif; ?>
                            </div>
                            
                            <a href="events.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/footer.php'; ?>