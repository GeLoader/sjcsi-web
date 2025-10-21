<?php
// events_all.php - Display all events
require_once __DIR__ . '/config.php';
 

try {
    // Update event status based on date
    $today = date('Y-m-d');
    dbQuery("UPDATE events SET status = 'completed' WHERE event_date < '$today' AND status = 'upcoming'");
    dbQuery("UPDATE events SET status = 'ongoing' WHERE event_date = '$today' AND status = 'upcoming'");

    $eventsQuery = "
        SELECT e.*, u.email as author_email
        FROM events e
        LEFT JOIN users u ON e.author_id = u.id
        ORDER BY e.event_date DESC
    ";
    $eventsResult = dbQuery($eventsQuery);
    $allEvents = [];
    while ($row = $eventsResult->fetch_assoc()) {
        $allEvents[] = $row;
    }
} catch (Exception $e) {
    error_log("Events All Page Error: " . $e->getMessage());
    $allEvents = [];
}

$page_title = 'All Events';
require_once BASE_PATH . '/header.php';
?>

<div class="min-vh-100 bg-light">
    <div class="container py-5">
        <h1 class="h2 mb-4">All Events</h1>
        
        <?php if (empty($allEvents)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No events found</h4>
                <p class="text-muted">Check back later for upcoming events and activities.</p>
                <a href="index.php" class="btn btn-primary">Back to Home</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($allEvents as $event): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="me-3 text-center">
                                        <div class="bg-primary text-white rounded p-2" style="min-width: 60px;">
                                            <div class="fw-bold"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                            <div class="small"><?php echo strtoupper(date('M', strtotime($event['event_date']))); ?></div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="badge <?php 
                                            switch($event['status']) {
                                                case 'upcoming': echo 'bg-primary'; break;
                                                case 'ongoing': echo 'bg-success'; break;
                                                case 'completed': echo 'bg-secondary'; break;
                                                case 'cancelled': echo 'bg-danger'; break;
                                            }
                                        ?>">
                                            <?php echo ucfirst($event['status']); ?>
                                        </span>
                                        <h5 class="card-title" style="color: #094b3d;"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    </div>
                                </div>
                                
                                <?php if ($event['image_url']): ?>
                                    <img src="<?php echo url($event['image_url']); ?>" class="img-fluid rounded mb-3" alt="<?php echo htmlspecialchars($event['title']); ?>">
                                <?php endif; ?>
                                
                                <p class="card-text text-muted mb-3"><?php echo htmlspecialchars($event['description']); ?></p>
                                
                                <div class="small text-muted mb-2">
                                    <i class="fas fa-clock me-2"></i>
                                    <?php 
                                    echo date('l, F j, Y', strtotime($event['event_date']));
                                    if ($event['event_time']) {
                                        echo ' at ' . date('g:i A', strtotime($event['event_time']));
                                    }
                                    ?>
                                </div>
                                
                                <?php if ($event['location']): ?>
                                    <div class="small text-muted mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <?php echo htmlspecialchars($event['location']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between">
                                    <a href="events.php?id=<?php echo $event['id']; ?>" class="btn btn-link text-decoration-none" style="color: #094b3d;">
                                        Learn More <i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                                        <div>
                                            <a href="event_edit.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-outline-primary">
                Back to Home <i class="fas fa-home ms-2"></i>
            </a>
        </div>
    </div>
</div>

<script>
function deleteEvent(id) {
    if (confirm('Are you sure you want to delete this event?')) {
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
}
</script>

<?php include __DIR__ . '/footer.php'; ?>