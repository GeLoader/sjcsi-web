<?php
// academic_programs.php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? 0;

// Fetch program details
$program = null;
if ($id) {
    $sql = "SELECT ap.*, d.name as department_name 
            FROM academic_programs ap 
            LEFT JOIN departments d ON ap.department_code = d.code 
            WHERE ap.id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $program = $result->fetch_assoc();
    $stmt->close();
}

if (!$program) {
    $page_title = 'Program Not Found';
    include 'header.php';
    echo '<div class="container mt-4"><div class="alert alert-danger">Academic program not found.</div></div>';
    include 'footer.php';
    exit;
}

$page_title = $program['name'] . ' | Academic Programs';
include 'header.php';
?>

<div class="container mt-4 fade-in">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('academic.php') ?>">Academic</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($program['name']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <article class="program-detail">
                <header class="mb-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="display-5 fw-bold text-primary"><?= htmlspecialchars($program['name']) ?></h1>
                            <?php if (!empty($program['department_name'])): ?>
                            <p class="lead text-muted">Offered by <?= htmlspecialchars($program['department_name']) ?> Department</p>
                            <?php endif; ?>
                        </div>
                        <span class="badge bg-<?= $program['level'] == 'college' ? 'primary' : ($program['level'] == 'shs' ? 'success' : 'info') ?> fs-6">
                            <?= strtoupper($program['level']) ?>
                        </span>
                    </div>
                </header>

                <div class="program-overview mb-5">
                    <h2 class="h3 mb-3">Program Overview</h2>
                    <div class="content-text">
                        <?= nl2br(htmlspecialchars($program['description'] ?? 'No description available.')) ?>
                    </div>
                </div>

                <div class="program-details mb-5">
                    <h2 class="h3 mb-4">Program Details</h2>
                    <div class="row">
                        <?php if (!empty($program['duration'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                                    <h5 class="card-title">Duration</h5>
                                    <p class="card-text"><?= htmlspecialchars($program['duration']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($program['units'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-book fa-2x text-success mb-3"></i>
                                    <h5 class="card-title">Total Units</h5>
                                    <p class="card-text"><?= htmlspecialchars($program['units']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($program['tuition_fee'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-money-bill-wave fa-2x text-warning mb-3"></i>
                                    <h5 class="card-title">Tuition Fee</h5>
                                    <p class="card-text"><?= htmlspecialchars($program['tuition_fee']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($program['level'])): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <i class="fas fa-graduation-cap fa-2x text-info mb-3"></i>
                                    <h5 class="card-title">Level</h5>
                                    <p class="card-text"><?= htmlspecialchars(ucfirst($program['level'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($program['learn_more_link'])): ?>
                <div class="program-actions mt-4">
                    <a href="<?= htmlspecialchars($program['learn_more_link']) ?>" 
                       class="btn btn-primary btn-lg" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Learn More About This Program
                    </a>
                </div>
                <?php endif; ?>
            </article>
        </div>

        <div class="col-lg-4">
            <div class="sidebar">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Related Programs</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql = "SELECT id, name, level FROM academic_programs 
                                WHERE id != ? AND level = ? 
                                ORDER BY name ASC LIMIT 5";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("is", $id, $program['level']);
                        $stmt->execute();
                        $related_programs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();
                        
                        if (!empty($related_programs)):
                        ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($related_programs as $related): ?>
                            <a href="<?= url('academic_programs.php?id=' . $related['id']) ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="fw-semibold"><?= htmlspecialchars($related['name']) ?></div>
                                <small class="text-muted"><?= ucfirst($related['level']) ?></small>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">No related programs found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Admission Information</h5>
                    </div>
                    <div class="card-body">
                        <p>Interested in this program? Learn about our admission requirements and process.</p>
                        <a href="<?= url('admission.php') ?>" class="btn btn-outline-success btn-sm">
                            View Admission Requirements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.program-detail {
    line-height: 1.7;
}

.content-text {
    font-size: 1.1rem;
    color: #333;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.list-group-item {
    border: none;
    padding: 0.75rem 0;
}

.list-group-item:first-child {
    padding-top: 0;
}

.list-group-item:last-child {
    padding-bottom: 0;
}
</style>

<?php include 'footer.php'; ?>