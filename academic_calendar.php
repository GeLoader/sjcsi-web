<?php
// academic_calendar.php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'Academic Calendar';
include 'header.php';

// Fetch academic calendar events
$sql = "SELECT * FROM academic_calendar ORDER BY start_date ASC";
$result = $mysqli->query($sql);
$events = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-4 fade-in">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('academic.php') ?>">Academic</a></li>
            <li class="breadcrumb-item active">Academic Calendar</li>
        </ol>
    </nav>

    <div class="calendar-header text-center mb-5">
        <h1 class="display-4 fw-bold text-primary mb-3">Academic Calendar</h1>
        <p class="lead text-muted">Important dates and events for the academic year</p>
    </div>

    <?php if (!empty($events)): ?>
    <div class="calendar-events">
        <div class="row">
            <?php
            $current_month = '';
            foreach ($events as $event):
                $event_month = date('F Y', strtotime($event['start_date']));
                if ($event_month !== $current_month):
                    $current_month = $event_month;
            ?>
            <div class="col-12">
                <h3 class="calendar-month-header text-primary border-bottom pb-2 mb-4 mt-5">
                    <i class="fas fa-calendar-alt me-2"></i><?= $current_month ?>
                </h3>
            </div>
            <?php endif; ?>

            <div class="col-lg-6 mb-4">
                <div class="calendar-event card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="event-date me-4 text-center">
                                <div class="bg-<?= 
                                    $event['type'] == 'enrollment' ? 'primary' : 
                                    ($event['type'] == 'classes' ? 'success' : 
                                    ($event['type'] == 'exams' ? 'warning' : 
                                    ($event['type'] == 'holiday' ? 'danger' : 'info')))
                                ?> text-white rounded p-3">
                                    <div class="fw-bold fs-5"><?= date('j', strtotime($event['start_date'])) ?></div>
                                    <div class="small"><?= date('M', strtotime($event['start_date'])) ?></div>
                                </div>
                            </div>
                            
                            <div class="event-details flex-grow-1">
                                <h5 class="card-title text-dark mb-2"><?= htmlspecialchars($event['title']) ?></h5>
                                
                                <div class="event-meta mb-2">
                                    <span class="badge bg-<?= 
                                        $event['type'] == 'enrollment' ? 'primary' : 
                                        ($event['type'] == 'classes' ? 'success' : 
                                        ($event['type'] == 'exams' ? 'warning' : 
                                        ($event['type'] == 'holiday' ? 'danger' : 'info')))
                                    ?>">
                                        <?= ucfirst($event['type']) ?>
                                    </span>
                                    
                                    <small class="text-muted ms-2">
                                        <i class="fas fa-clock me-1"></i>
                                        <?= date('M j', strtotime($event['start_date'])) ?>
                                        <?php if (!empty($event['end_date']) && $event['end_date'] != $event['start_date']): ?>
                                        - <?= date('M j, Y', strtotime($event['end_date'])) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                                
                                <?php if (!empty($event['description'])): ?>
                                <p class="card-text text-muted"><?= htmlspecialchars($event['description']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle me-2"></i>
        No academic calendar events found.
    </div>
    <?php endif; ?>
</div>

<style>
.calendar-event {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.calendar-event:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.calendar-month-header {
    font-weight: 600;
}

.event-date {
    min-width: 70px;
}

.event-date .bg-primary { background-color: var(--primary-color) !important; }
.event-date .bg-success { background-color: var(--success-color) !important; }
.event-date .bg-warning { background-color: var(--warning-color) !important; }
.event-date .bg-danger { background-color: var(--danger-color) !important; }
.event-date .bg-info { background-color: var(--info-color) !important; }
</style>

<?php include 'footer.php'; ?>