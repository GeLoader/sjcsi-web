<?php
// achievements.php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = $_GET['id'] ?? 0;

// Fetch achievement details
$achievement = null;
if ($id) {
    $sql = "SELECT * FROM achievements WHERE id = ? AND is_published = 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $achievement = $result->fetch_assoc();
    $stmt->close();
}

if (!$achievement) {
    $page_title = 'Achievement Not Found';
    include 'header.php';
    echo '<div class="container mt-4"><div class="alert alert-danger">Achievement not found.</div></div>';
    include 'footer.php';
    exit;
}

$page_title = $achievement['title'] . ' | Achievements';
include 'header.php';
?>

<div class="container mt-4 fade-in">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= url('achievements.php') ?>">Achievements</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($achievement['title']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <article class="achievement-detail">
                <header class="mb-4">
                    <h1 class="display-5 fw-bold text-primary"><?= htmlspecialchars($achievement['title']) ?></h1>
                    
                    <div class="d-flex flex-wrap gap-3 mt-3 text-muted">
                        <div>
                            <i class="fas fa-calendar me-1"></i>
                            <?= date('F j, Y', strtotime($achievement['achievement_date'])) ?>
                        </div>
                        <div>
                            <i class="fas fa-tag me-1"></i>
                            <?= htmlspecialchars(ucfirst($achievement['category'])) ?>
                        </div>
                        <?php if (!empty($achievement['awarded_to'])): ?>
                        <div>
                            <i class="fas fa-users me-1"></i>
                            <?= htmlspecialchars($achievement['awarded_to']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (!empty($achievement['image_url'])): ?>
                <div class="achievement-image mb-4">
                    <img src="<?= htmlspecialchars($achievement['image_url']) ?>" 
                         alt="<?= htmlspecialchars($achievement['title']) ?>" 
                         class="img-fluid rounded shadow">
                </div>
                <?php endif; ?>

                <div class="achievement-content">
                    <div class="description mb-4">
                        <h3 class="h4 mb-3">Achievement Description</h3>
                        <div class="content-text">
                            <?= nl2br(htmlspecialchars($achievement['description'])) ?>
                        </div>
                    </div>
                </div>

                <div class="achievement-meta mt-5 pt-4 border-top">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Achievement Details</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Category:</strong>
                                    <span class="badge bg-primary"><?= htmlspecialchars(ucfirst($achievement['category'])) ?></span>
                                </li>
                                <li class="mb-2">
                                    <strong>Date Achieved:</strong>
                                    <?= date('F j, Y', strtotime($achievement['achievement_date'])) ?>
                                </li>
                                <?php if (!empty($achievement['awarded_to'])): ?>
                                <li class="mb-2">
                                    <strong>Awarded To:</strong>
                                    <?= htmlspecialchars($achievement['awarded_to']) ?>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <div class="sidebar">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Recent Achievements</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql = "SELECT id, title, achievement_date FROM achievements 
                                WHERE is_published = 1 AND id != ? 
                                ORDER BY achievement_date DESC LIMIT 5";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $recent_achievements = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                        $stmt->close();
                        
                        if (!empty($recent_achievements)):
                        ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recent_achievements as $recent): ?>
                            <a href="<?= url('achievements.php?id=' . $recent['id']) ?>" 
                               class="list-group-item list-group-item-action">
                                <div class="fw-semibold"><?= htmlspecialchars($recent['title']) ?></div>
                                <small class="text-muted"><?= date('M j, Y', strtotime($recent['achievement_date'])) ?></small>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted mb-0">No other achievements found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-share me-2"></i>Share This Achievement</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(url('achievements.php?id=' . $id)) ?>" 
                               target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(url('achievements.php?id=' . $id)) ?>&text=<?= urlencode($achievement['title']) ?>" 
                               target="_blank" class="btn btn-outline-info btn-sm">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="mailto:?subject=<?= urlencode($achievement['title']) ?>&body=<?= urlencode(url('achievements.php?id=' . $id)) ?>" 
                               class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.achievement-detail {
    line-height: 1.7;
}

.content-text {
    font-size: 1.1rem;
    color: #333;
}

.achievement-image img {
    max-height: 400px;
    width: 100%;
    object-fit: cover;
}

.sidebar .card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
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