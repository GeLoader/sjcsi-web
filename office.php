<?php
// office.php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$code = $_GET['code'] ?? '';

// Fetch office details
$office = null;
if ($code) {
    $sql = "SELECT * FROM offices WHERE code = ? AND is_active = 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $office = $result->fetch_assoc();
    $stmt->close();
}

if (!$office) {
    $page_title = 'Office Not Found';
    include 'header.php';
    echo '<div class="container mt-4"><div class="alert alert-danger">Office not found.</div></div>';
    include 'footer.php';
    exit;
}

$page_title = $office['name'];
include 'header.php';
?>

<div class="container mt-4 fade-in">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('#') ?>">Offices</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($office['name']) ?></li>
        </ol>
    </nav>

    <div class="office-header mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold text-primary mb-3"><?= htmlspecialchars($office['name']) ?></h1>
                <?php if (!empty($office['description'])): ?>
                <p class="lead text-muted"><?= htmlspecialchars($office['description']) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="office-badge">
                    <span class="badge bg-primary fs-6 p-3">Office</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Office Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-info-circle me-2"></i>Office Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (!empty($office['head_name'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user-tie me-2 text-primary"></i>Head of Office:</strong>
                            <p class="mb-0"><?= htmlspecialchars($office['head_name']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($office['contact_email'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-envelope me-2 text-primary"></i>Email:</strong>
                            <p class="mb-0">
                                <a href="mailto:<?= htmlspecialchars($office['contact_email']) ?>">
                                    <?= htmlspecialchars($office['contact_email']) ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($office['contact_phone'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-phone me-2 text-primary"></i>Phone:</strong>
                            <p class="mb-0">
                                <a href="tel:<?= htmlspecialchars($office['contact_phone']) ?>">
                                    <?= htmlspecialchars($office['contact_phone']) ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($office['location'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-map-marker-alt me-2 text-primary"></i>Location:</strong>
                            <p class="mb-0"><?= htmlspecialchars($office['location']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($office['office_hours'])): ?>
                        <div class="col-12 mb-3">
                            <strong><i class="fas fa-clock me-2 text-primary"></i>Office Hours:</strong>
                            <p class="mb-0"><?= htmlspecialchars($office['office_hours']) ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-code me-2 text-primary"></i>Office Code:</strong>
                            <p class="mb-0"><?= htmlspecialchars($office['code']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="fas fa-concierge-bell me-2"></i>Services Offered</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">This office provides various services to students, faculty, and staff. Please visit during office hours for assistance.</p>
                    
                    <div class="services-list">
                        <!-- You can dynamically populate services based on office type -->
                        <?php if (strpos(strtolower($office['name']), 'registrar') !== false): ?>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Student Registration</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Transcript Requests</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Enrollment Services</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Records Management</li>
                        </ul>
                        <?php elseif (strpos(strtolower($office['name']), 'accounting') !== false): ?>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Tuition Payment Processing</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Financial Assistance</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Fee Assessment</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Payment Plans</li>
                        </ul>
                        <?php else: ?>
                        <p class="text-muted mb-0">Specific services information coming soon.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sidebar">
                <!-- Quick Contact -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Quick Contact</h5>
                    </div>
                    <div class="card-body">
                        <div class="contact-info">
                            <?php if (!empty($office['contact_email'])): ?>
                            <div class="mb-3">
                                <a href="mailto:<?= htmlspecialchars($office['contact_email']) ?>" 
                                   class="btn btn-outline-primary w-100 text-start">
                                    <i class="fas fa-envelope me-2"></i>
                                    Send Email
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($office['contact_phone'])): ?>
                            <div class="mb-3">
                                <a href="tel:<?= htmlspecialchars($office['contact_phone']) ?>" 
                                   class="btn btn-outline-success w-100 text-start">
                                    <i class="fas fa-phone me-2"></i>
                                    Call Office
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($office['location'])): ?>
                            <div class="mb-3">
                                <button class="btn btn-outline-info w-100 text-start" onclick="alert('Office Location: <?= htmlspecialchars($office['location']) ?>')">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    View Location
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Related Offices -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Other Offices</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql = "SELECT code, name FROM offices 
                                WHERE code != ? AND is_active = 1 
                                ORDER BY name ASC LIMIT 5";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("s", $code);
                        $stmt->execute();
                        $other_offices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();
                        
                        if (!empty($other_offices)):
                        ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($other_offices as $off): ?>
                            <a href="<?= url('office.php?code=' . $off['code']) ?>" 
                               class="list-group-item list-group-item-action">
                                <i class="fas fa-arrow-right me-2 text-muted"></i>
                                <?= htmlspecialchars($off['name']) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">No other offices found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.office-header {
    border-bottom: 2px solid var(--primary-color);
    padding-bottom: 2rem;
}

.office-badge .badge {
    border-radius: 50px;
}

.services-list ul li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.services-list ul li:last-child {
    border-bottom: none;
}

.contact-info .btn {
    transition: all 0.2s ease;
}

.contact-info .btn:hover {
    transform: translateX(5px);
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
    transition: padding-left 0.2s ease;
}

.list-group-item:hover {
    padding-left: 0.5rem;
}

.list-group-item:first-child {
    padding-top: 0;
}

.list-group-item:last-child {
    padding-bottom: 0;
}
</style>

<?php include 'footer.php'; ?>