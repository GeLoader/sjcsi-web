<?php
// news_all.php - View all news articles
require_once __DIR__ . '/config.php';

// Get pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12; // Number of articles per page (multiple of 3 for 3-column layout)
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$date_filter = isset($_GET['date']) ? trim($_GET['date']) : '';

$page_title = 'All News & Announcements';
require_once BASE_PATH . '/header.php';

try {
    // Build the WHERE clause - FIXED: Use consistent field names and proper SQL construction
    $where_conditions = ["n.status = 'published'"];
    $params = [];
    $param_types = '';

    if (!empty($search_term)) {
        $where_conditions[] = "(n.title LIKE ? OR n.excerpt LIKE ? OR n.content LIKE ?)";
        $search_param = "%{$search_term}%";
        $params = array_merge($params, [$search_param, $search_param, $search_param]);
        $param_types .= 'sss';
    }

    if (!empty($category_filter)) {
        $where_conditions[] = "n.category = ?";
        $params[] = $category_filter;
        $param_types .= 's';
    }

    if (!empty($date_filter)) {
        // Use published_at instead of created_at to match news_archive
        switch ($date_filter) {
            case 'today':
                $where_conditions[] = "DATE(n.published_at) = CURDATE()";
                break;
            case 'week':
                $where_conditions[] = "n.published_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $where_conditions[] = "n.published_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
            case 'year':
                $where_conditions[] = "n.published_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;
        }
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(' AND ', $where_conditions) : "";

    // Get total count for pagination
    $countSql = "SELECT COUNT(*) as total FROM news n " . $where_clause;
    
    if (!empty($params)) {
        $countStmt = dbPrepare($countSql);
        $countStmt->bind_param($param_types, ...$params);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
    } else {
        $countResult = dbQuery($countSql);
    }
    $totalArticles = $countResult->fetch_assoc()['total'];
    $totalPages = ceil($totalArticles / $limit);
    
    // Get news articles with pagination - FIXED: Proper parameter binding
    $sql = "SELECT n.*, u.email as author_email 
            FROM news n 
            LEFT JOIN users u ON n.author_id = u.id 
            {$where_clause}
            ORDER BY n.published_at DESC 
            LIMIT ? OFFSET ?";
    
    // Prepare parameters for the main query
    $query_params = [];
    $query_types = '';
    
    // Add filter parameters if they exist
    if (!empty($params)) {
        $query_params = $params;
        $query_types = $param_types;
    }
    
    // Add pagination parameters
    $query_params[] = $limit;
    $query_params[] = $offset;
    $query_types .= 'ii';
    
    $stmt = dbPrepare($sql);
    if (!empty($query_params)) {
        $stmt->bind_param($query_types, ...$query_params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $news_articles = [];
    while ($row = $result->fetch_assoc()) {
        $news_articles[] = $row;
    }
    
    // Get available categories for filter
    $categorySql = "SELECT DISTINCT category FROM news WHERE status = 'published' AND category IS NOT NULL AND category != '' ORDER BY category";
    $categoryResult = dbQuery($categorySql);
    $categories = [];
    while ($cat = $categoryResult->fetch_assoc()) {
        $categories[] = $cat['category'];
    }
    
} catch (Exception $e) {
    error_log("News All Page Error: " . $e->getMessage());
    $news_articles = [];
    $categories = [];
    $totalArticles = 0;
    $totalPages = 0;
}
?>

<!-- Page Header -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="index.php" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">All News & Announcements</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0" style="color: #094b3d;">All News & Announcements</h1>
    </div>
</section>

<!-- Search and Filter Section -->
<section class="py-3 bg-white border-bottom">
    <div class="container">
        <form method="GET" action="" class="row g-3 align-items-end">
            <!-- Search Input -->
            <div class="col-md-4">
                <label for="search" class="form-label small">Search Articles</label>
                <input type="text" 
                       class="form-control form-control-sm" 
                       id="search" 
                       name="search" 
                       value="<?php echo htmlspecialchars($search_term); ?>" 
                       placeholder="Search titles, content...">
            </div>
            
            <!-- Category Filter -->
            <div class="col-md-3">
                <label for="category" class="form-label small">Category</label>
                <select class="form-select form-select-sm" id="category" name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" 
                                <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Date Filter -->
            <div class="col-md-3">
                <label for="date" class="form-label small">Date Range</label>
                <select class="form-select form-select-sm" id="date" name="date">
                    <option value="">All Time</option>
                    <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Today</option>
                    <option value="week" <?php echo $date_filter === 'week' ? 'selected' : ''; ?>>This Week</option>
                    <option value="month" <?php echo $date_filter === 'month' ? 'selected' : ''; ?>>This Month</option>
                    <option value="year" <?php echo $date_filter === 'year' ? 'selected' : ''; ?>>This Year</option>
                </select>
            </div>
            
            <!-- Filter Buttons -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm me-1">Filter</button>
                <a href="news_all.php" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</section>

<!-- News Cards Section -->
<section class="py-4">
    <div class="container">
        <?php if (empty($news_articles)): ?>
            <div class="text-center py-5">
                <p class="text-muted">No articles found.</p>
                <?php if (!empty($search_term) || !empty($category_filter) || !empty($date_filter)): ?>
                    <p><a href="news_all.php" class="text-primary">Clear all filters</a> to see all articles.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Results Summary -->
            <?php if (!empty($search_term) || !empty($category_filter) || !empty($date_filter)): ?>
                <div class="mb-4">
                    <small class="text-muted">
                        Showing <?php echo number_format($totalArticles); ?> results
                        <?php if (!empty($search_term)): ?>
                            for: "<strong><?php echo htmlspecialchars($search_term); ?></strong>"
                        <?php endif; ?>
                        <?php if (!empty($category_filter)): ?>
                            in category: <strong><?php echo htmlspecialchars($category_filter); ?></strong>
                        <?php endif; ?>
                        <?php if (!empty($date_filter)): ?>
                            from: <strong><?php echo htmlspecialchars($date_filter); ?></strong>
                        <?php endif; ?>
                    </small>
                </div>
            <?php endif; ?>

            <!-- News Cards Grid - 3 columns per row -->
            <div class="row g-4">
                <?php foreach ($news_articles as $article): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card shadow-sm h-100 news-card" style="transition: all 0.3s ease;">
                            <!-- News Image -->
                            <?php if (!empty($article['image_url'])): ?>
                                <div class="position-relative overflow-hidden" style="height: 200px;">
                                    <img src="<?php echo htmlspecialchars($article['image_url']); ?>" 
                                         class="card-img-top w-100 h-100" 
                                         alt="<?php echo htmlspecialchars($article['title']); ?>"
                                         style="object-fit: cover; transition: transform 0.3s ease;">
                                </div>
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-newspaper fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <!-- Category and Date -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <?php if (!empty($article['category'])): ?>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($article['category']); ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">News</span>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?php echo date('M d, Y', strtotime($article['published_at'])); ?>
                                    </small>
                                </div>
                                
                                <!-- Title -->
                                <h5 class="card-title mb-3" style="color: #094b3d; line-height: 1.3;">
                                    <a href="news.php?id=<?php echo $article['id']; ?>" class="text-decoration-none stretched-link">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </a>
                                </h5>
                                
                                <!-- Excerpt -->
                                <div class="flex-grow-1 mb-3">
                                    <?php if (!empty($article['excerpt'])): ?>
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars(strlen($article['excerpt']) > 120 ? substr($article['excerpt'], 0, 120) . '...' : $article['excerpt']); ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars(strlen(strip_tags($article['content'])) > 120 ? substr(strip_tags($article['content']), 0, 120) . '...' : strip_tags($article['content'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Meta Info -->
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <span>
                                            <i class="fas fa-eye me-1"></i>
                                            <?php echo number_format($article['views']); ?> views
                                        </span>
                                        <?php if (!empty($article['author_email'])): ?>
                                            <span>
                                                <i class="fas fa-user me-1"></i>
                                                <?php echo htmlspecialchars(explode('@', $article['author_email'])[0]); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="News pagination" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <!-- Previous Page -->
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => ($page - 1)])); ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Page Numbers -->
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($totalPages, $page + 2);
                        
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $totalPages): ?>
                            <?php if ($end_page < $totalPages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $totalPages])); ?>"><?php echo $totalPages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Page -->
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => ($page + 1)])); ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Page info -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Page <?php echo $page; ?> of <?php echo $totalPages; ?> 
                            (<?php echo number_format($totalArticles); ?> total articles)
                        </small>
                    </div>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.news-card img {
    transition: transform 0.3s ease;
}

.news-card:hover img {
    transform: scale(1.05);
}

.news-card .stretched-link {
    color: inherit;
    transition: color 0.3s ease;
}

.news-card:hover .stretched-link {
    color: #094b3d !important;
}

.pagination .page-link {
    color: #094b3d;
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: #094b3d;
    border-color: #094b3d;
    color: white;
}

.pagination .page-link:hover:not(.active) {
    background-color: rgba(9, 75, 61, 0.1);
    border-color: #094b3d;
    color: #094b3d;
}

.form-control:focus,
.form-select:focus {
    border-color: #094b3d;
    box-shadow: 0 0 0 0.2rem rgba(9, 75, 61, 0.25);
}

.btn-primary {
    background-color: #094b3d;
    border-color: #094b3d;
}

.btn-primary:hover {
    background-color: #0d5d4a;
    border-color: #0d5d4a;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .news-card {
        margin-bottom: 1.5rem;
    }
    
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}
</style>

<?php require_once BASE_PATH . '/footer.php'; ?>