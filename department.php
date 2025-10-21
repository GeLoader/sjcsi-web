<?php
// department.php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$code = $_GET['code'] ?? '';

// Fetch department details
$department = null;
if ($code) {
    $sql = "SELECT * FROM departments WHERE code = ? AND is_active = 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $department = $result->fetch_assoc();
    $stmt->close();
}

if (!$department) {
    $page_title = 'Department Not Found';
    include 'header.php';
    echo '<div class="container mt-4"><div class="alert alert-danger">Department not found.</div></div>';
    include 'footer.php';
    exit;
}

// Fetch department programs
$sql = "SELECT * FROM academic_programs WHERE department_code = ? ORDER BY name ASC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $code);
$stmt->execute();
$programs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$page_title = $department['name'] . ' Department';
include 'header.php';
?>

<div class="container mt-4 fade-in">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('#') ?>">Departments</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($department['name']) ?></li>
        </ol>
    </nav>

    <div class="department-header mb-5 text-center">
        <h1 class="display-4 fw-bold text-primary mb-3"><?= htmlspecialchars($department['name']) ?> Department</h1>
        <?php if (!empty($department['description'])): ?>
        <p class="lead text-muted max-w-800 mx-auto"><?= htmlspecialchars($department['description']) ?></p>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Department Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-info-circle me-2"></i>Department Information</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if (!empty($department['head_name'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user-tie me-2 text-primary"></i>Department Head:</strong>
                            <p class="mb-0"><?= htmlspecialchars($department['head_name']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($department['contact_email'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-envelope me-2 text-primary"></i>Email:</strong>
                            <p class="mb-0">
                                <a href="mailto:<?= htmlspecialchars($department['contact_email']) ?>">
                                    <?= htmlspecialchars($department['contact_email']) ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($department['contact_phone'])): ?>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-phone me-2 text-primary"></i>Phone:</strong>
                            <p class="mb-0">
                                <a href="tel:<?= htmlspecialchars($department['contact_phone']) ?>">
                                    <?= htmlspecialchars($department['contact_phone']) ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-code me-2 text-primary"></i>Department Code:</strong>
                            <p class="mb-0"><?= htmlspecialchars($department['code']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Programs -->
            <?php if (!empty($programs)): ?>
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h3 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic Programs</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($programs as $program): ?>
                        <div class="col-md-6 mb-3">
                            <div class="program-card card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-primary"><?= htmlspecialchars($program['name']) ?></h5>
                                    <p class="card-text text-muted small">
                                        <?= !empty($program['description']) ? substr(htmlspecialchars($program['description']), 0, 100) . '...' : 'No description available.' ?>
                                    </p>
                                    <div class="program-meta">
                                        <?php if (!empty($program['level'])): ?>
                                        <span class="badge bg-<?= $program['level'] == 'college' ? 'primary' : ($program['level'] == 'shs' ? 'success' : 'info') ?>">
                                            <?= strtoupper($program['level']) ?>
                                        </span>
                                        <?php endif; ?>
                                        <?php if (!empty($program['duration'])): ?>
                                        <small class="text-muted ms-2"><?= htmlspecialchars($program['duration']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-top-0">
                                    <a href="<?= url('academic_programs.php?id=' . $program['id']) ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        Learn More
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="sidebar">
                <!-- Quick Contact -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Contact Department</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($department['contact_email']) || !empty($department['contact_phone'])): ?>
                        <div class="contact-info">
                            <?php if (!empty($department['contact_email'])): ?>
                            <div class="mb-3">
                                <strong>Email:</strong><br>
                                <a href="mailto:<?= htmlspecialchars($department['contact_email']) ?>" class="text-decoration-none">
                                    <i class="fas fa-envelope me-2 text-primary"></i>
                                    <?= htmlspecialchars($department['contact_email']) ?>
                                </a>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($department['contact_phone'])): ?>
                            <div class="mb-3">
                                <strong>Phone:</strong><br>
                                <a href="tel:<?= htmlspecialchars($department['contact_phone']) ?>" class="text-decoration-none">
                                    <i class="fas fa-phone me-2 text-primary"></i>
                                    <?= htmlspecialchars($department['contact_phone']) ?>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">Contact information not available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Related Departments -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Other Departments</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql = "SELECT code, name FROM departments 
                                WHERE code != ? AND is_active = 1 
                                ORDER BY name ASC LIMIT 5";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("s", $code);
                        $stmt->execute();
                        $other_departments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();
                        
                        if (!empty($other_departments)):
                        ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($other_departments as $dept): ?>
                            <a href="<?= url('department.php?code=' . $dept['code']) ?>" 
                               class="list-group-item list-group-item-action">
                                <i class="fas fa-arrow-right me-2 text-muted"></i>
                                <?= htmlspecialchars($dept['name']) ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">No other departments found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.department-header .max-w-800 {
    max-width: 800px;
}

.program-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.program-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.contact-info a {
    transition: color 0.2s ease;
}

.contact-info a:hover {
    color: var(--primary-color) !important;
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