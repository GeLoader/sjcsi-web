<?php
// achievements_all.php - Display all achievements
require_once __DIR__ . '/config.php';

$page_title = 'All Achievements - Saint Joseph College of Sindangan Incorporated';

// Pagination settings
$per_page = 12;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $per_page;

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : '';
$year = isset($_GET['year']) ? intval($_GET['year']) : '';

// Build query
$where_conditions = ["is_published = 1"];
$query_params = [];

if (!empty($category)) {
    $where_conditions[] = "category = ?";
    $query_params[] = $category;
}

if (!empty($year)) {
    $where_conditions[] = "YEAR(achievement_date) = ?";
    $query_params[] = $year;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get achievements
try {
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM achievements $where_clause";
    $count_stmt = dbPrepare($count_query);
    
    if (!empty($query_params)) {
        $types = str_repeat('s', count($query_params));
        $count_stmt->bind_param($types, ...$query_params);
    }
    
    $count_stmt->execute();
    $total_result = $count_stmt->get_result();
    $total_achievements = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_achievements / $per_page);

    // Get achievements for current page
    $achievements_query = "SELECT * FROM achievements $where_clause ORDER BY achievement_date DESC LIMIT ? OFFSET ?";
    $stmt = dbPrepare($achievements_query);
    
    if (!empty($query_params)) {
        $types = str_repeat('s', count($query_params)) . 'ii';
        $params = array_merge($query_params, [$per_page, $offset]);
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param('ii', $per_page, $offset);
    }
    
    $stmt->execute();
    $achievements_result = $stmt->get_result();
    $achievements = [];
    
    while ($row = $achievements_result->fetch_assoc()) {
        $achievements[] = $row;
    }
    
    // Get available categories for filter
    $categories_result = dbQuery("SELECT DISTINCT category FROM achievements WHERE is_published = 1 ORDER BY category");
    $categories = [];
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
    
    // Get available years for filter
    $years_result = dbQuery("SELECT DISTINCT YEAR(achievement_date) as year FROM achievements WHERE is_published = 1 ORDER BY year DESC");
    $years = [];
    while ($row = $years_result->fetch_assoc()) {
        $years[] = $row['year'];
    }
    
} catch (Exception $e) {
    error_log("Achievements Error: " . $e->getMessage());
    $achievements = [];
    $total_achievements = 0;
    $total_pages = 1;
    $categories = [];
    $years = [];
}

require_once BASE_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="py-5" style="background-color: #094B3D;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold text-white">Our Achievements</h1>
                <p class="lead text-white mb-0">Celebrating excellence and success at SJCSI</p>
            </div>
            <div class="col-md-4 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Achievements</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Filters Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Filter Achievements</h5>
                <form method="GET" action="achievements_all.php" class="row g-3">
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                    <?php echo ucfirst(htmlspecialchars($cat)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" id="year" name="year">
                            <option value="">All Years</option>
                            <?php foreach ($years as $y): ?>
                                <option value="<?php echo $y; ?>" <?php echo $year === $y ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
                        <a href="achievements_all.php" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Achievements Grid -->
<section class="py-5">
    <div class="container">
        <?php if (empty($achievements)): ?>
            <div class="text-center py-5">
                <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No achievements found</h4>
                <p class="text-muted"><?php echo (!empty($category) || !empty($year)) ? 'Try adjusting your filters.' : 'Check back later for our latest achievements.'; ?></p>
                <?php if (!empty($category) || !empty($year)): ?>
                    <a href="achievements_all.php" class="btn btn-primary mt-3">View All Achievements</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Results Count -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="text-muted mb-0">
                    Showing <?php echo count($achievements); ?> of <?php echo number_format($total_achievements); ?> achievements
                </p>
            </div>

            <!-- Achievements Grid -->
            <div class="row g-4">
                <?php foreach ($achievements as $achievement): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 achievement-card shadow-sm">
                            <?php if (!empty($achievement['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($achievement['image_url']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($achievement['title']); ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                    <i class="fas fa-trophy fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-warning me-2">
                                        <i class="fas fa-trophy me-1"></i>
                                        <?php echo htmlspecialchars(ucfirst($achievement['category'])); ?>
                                    </span>
                                    <small class="text-muted">
                                        <?php echo date('M Y', strtotime($achievement['achievement_date'])); ?>
                                    </small>
                                </div>
                                
                                <h5 class="card-title" style="color: #094b3d;">
                                    <?php echo htmlspecialchars($achievement['title']); ?>
                                </h5>
                                
                                <p class="card-text flex-grow-1">
                                    <?php echo htmlspecialchars($achievement['description']); ?>
                                </p>
                                
                                <?php if (!empty($achievement['awarded_to'])): ?>
                                    <div class="mt-auto pt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-user-graduate me-1"></i>
                                            Awarded to: <?php echo htmlspecialchars($achievement['awarded_to']); ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Achievements pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page -->
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($year) ? '&year=' . $year : ''; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $start_page + 4);
                        $start_page = max(1, $end_page - 4);
                        
                        for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($year) ? '&year=' . $year : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Next Page -->
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($year) ? '&year=' . $year : ''; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
.achievement-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(9, 75, 61, 0.1);
}

.achievement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(9, 75, 61, 0.15) !important;
    border-color: #094b3d;
}

.achievement-card .badge {
    font-size: 0.75rem;
}

.page-item.active .page-link {
    background-color: #094B3D;
    border-color: #094B3D;
}

.page-link {
    color: #094B3D;
}

.page-link:hover {
    color: #063027;
}
</style>

<?php require_once BASE_PATH . '/footer.php'; ?>