<?php
// news_archive.php - Simple news archive with list layout
require_once __DIR__ . '/config.php';

// Pagination settings
$items_per_page = 15;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Search and filter parameters
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$date_filter = isset($_GET['date']) ? trim($_GET['date']) : '';

try {
    // Build the WHERE clause
    $where_conditions = ["status = 'published'"];
    $params = [];
    $param_types = '';

    if (!empty($search_term)) {
        $where_conditions[] = "(title LIKE ? OR excerpt LIKE ? OR content LIKE ?)";
        $search_param = "%{$search_term}%";
        $params = array_merge($params, [$search_param, $search_param, $search_param]);
        $param_types .= 'sss';
    }

    if (!empty($category_filter)) {
        $where_conditions[] = "category = ?";
        $params[] = $category_filter;
        $param_types .= 's';
    }

    if (!empty($date_filter)) {
        switch ($date_filter) {
            case 'today':
                $where_conditions[] = "DATE(published_at) = CURDATE()";
                break;
            case 'week':
                $where_conditions[] = "published_at >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $where_conditions[] = "published_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
            case 'year':
                $where_conditions[] = "published_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
                break;
        }
    }

    $where_clause = implode(' AND ', $where_conditions);

    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total FROM news WHERE {$where_clause}";
    if (!empty($params)) {
        $count_stmt = $mysqli->prepare($count_query);
        if (!empty($param_types)) {
            $count_stmt->bind_param($param_types, ...$params);
        }
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
    } else {
        $count_result = $mysqli->query($count_query);
    }
    $total_items = $count_result->fetch_assoc()['total'];
    $total_pages = ceil($total_items / $items_per_page);

    // Get news items for current page
    $news_query = "
        SELECT id, title, excerpt, content, category, published_at, views
        FROM news 
        WHERE {$where_clause}
        ORDER BY published_at DESC 
        LIMIT {$items_per_page} OFFSET {$offset}
    ";
    
    if (!empty($params)) {
        $news_stmt = $mysqli->prepare($news_query);
        if (!empty($param_types)) {
            $news_stmt->bind_param($param_types, ...$params);
        }
        $news_stmt->execute();
        $news_result = $news_stmt->get_result();
    } else {
        $news_result = $mysqli->query($news_query);
    }
    
    $news_items = [];
    while ($row = $news_result->fetch_assoc()) {
        $news_items[] = $row;
    }

    // Get available categories for filter
    $categories_query = "SELECT DISTINCT category FROM news WHERE status = 'published' AND category IS NOT NULL ORDER BY category";
    $categories_result = $mysqli->query($categories_query);
    $categories = [];
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row['category'];
    }

} catch (Exception $e) {
    error_log("News Archive Error: " . $e->getMessage());
    $news_items = [];
    $categories = [];
    $total_items = 0;
    $total_pages = 0;
}

$page_title = "News Archive";
require_once BASE_PATH . '/header.php';
?>

<!-- Page Header -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="index.php" class="text-decoration-none">Home</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">News Archive</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0" style="color: #094b3d;">News Archive</h1>
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
                <a href="news_archive.php" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </div>
</section>

<!-- News List Section -->
<section class="py-4">
    <div class="container">
        <?php if (empty($news_items)): ?>
            <div class="text-center py-5">
                <p class="text-muted">No articles found.</p>
                <?php if (!empty($search_term) || !empty($category_filter) || !empty($date_filter)): ?>
                    <p><a href="news_archive.php" class="text-primary">Clear all filters</a> to see all articles.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Results Summary -->
            <?php if (!empty($search_term)): ?>
                <div class="mb-3">
                    <small class="text-muted">
                        Showing <?php echo number_format($total_items); ?> results for: 
                        "<strong><?php echo htmlspecialchars($search_term); ?></strong>"
                    </small>
                </div>
            <?php endif; ?>

            <!-- News Items List -->
            <div class="news-list">
                <?php foreach ($news_items as $news): ?>
                    <div class="news-item py-3 border-bottom">
                        <!-- Date -->
                        <div class="text-muted small mb-1">
                            <?php echo date('M j, Y', strtotime($news['published_at'])); ?>
                        </div>
                        
                        <!-- Title Link -->
                        <h5 class="mb-2">
                            <a href="news.php?id=<?php echo $news['id']; ?>" 
                               class="text-decoration-none"
                               style="color: #094b3d;">
                                <?php echo htmlspecialchars($news['title']); ?>
                            </a>
                        </h5>
                        
                        <!-- Excerpt (if available) -->
                        <?php if (!empty($news['excerpt'])): ?>
                            <p class="text-muted mb-2 small">
                                <?php echo htmlspecialchars(substr($news['excerpt'], 0, 200) . (strlen($news['excerpt']) > 200 ? '...' : '')); ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Meta info -->
                        <div class="d-flex align-items-center text-muted small">
                            <?php if (!empty($news['category'])): ?>
                                <span class="me-3">
                                    <i class="fas fa-folder me-1"></i>
                                    <?php echo htmlspecialchars($news['category']); ?>
                                </span>
                            <?php endif; ?>
                            <span>
                                <i class="fas fa-eye me-1"></i>
                                <?php echo number_format($news['views']); ?> views
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="News pagination" class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center">
                        <!-- Previous Button -->
                        <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => max(1, $current_page - 1)])); ?>">
                                <span aria-hidden="true">&lsaquo;</span>
                            </a>
                        </li>
                        
                        <!-- Page Numbers -->
                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        // Show first page if not in range
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <!-- Show last page if not in range -->
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>"><?php echo $total_pages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Button -->
                        <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => min($total_pages, $current_page + 1)])); ?>">
                                <span aria-hidden="true">&rsaquo;</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Page info -->
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Page <?php echo $current_page; ?> of <?php echo $total_pages; ?> 
                            (<?php echo number_format($total_items); ?> total articles)
                        </small>
                    </div>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<style>
/* Simple News Archive Styles */
.news-item {
    transition: background-color 0.2s ease;
}

.news-item:hover {
    background-color: rgba(9, 75, 61, 0.02);
    border-radius: 4px;
}

.news-item h5 a:hover {
    text-decoration: underline !important;
}

.pagination .page-link {
    color: #094b3d;
    border-color: #dee2e6;
    padding: 0.375rem 0.75rem;
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
    .news-item h5 {
        font-size: 1.1rem;
    }
    
    .pagination {
        font-size: 0.875rem;
    }
}
</style>

<?php require_once BASE_PATH . '/footer.php'; ?>