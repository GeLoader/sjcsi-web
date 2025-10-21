<?php
// search.php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'Search Results';
include 'header.php';

// Get search query
$query = $_GET['q'] ?? '';
$results = [];

// If there's a search query, process it
if (!empty($query)) {
    // Sanitize the query
    $search_query = trim($query);
    
    // Perform search across multiple tables
    $results = performSearch($search_query);
}

function performSearch($query) {
    global $mysqli;
    $results = [];
    
    try {
        $search_term = "%" . $mysqli->real_escape_string($query) . "%";
        
        // Search in news table
        $sql = "
            SELECT 
                id, 
                title, 
                excerpt as content, 
                'news' as type,
                CONCAT('news.php?id=', id) as url,
                created_at
            FROM news 
            WHERE (title LIKE ? OR excerpt LIKE ? OR content LIKE ?)
            AND status = 'published'
            ORDER BY created_at DESC
            LIMIT 10
        ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();
        $news_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Search in events table
        $sql = "
            SELECT 
                id, 
                title, 
                description as content, 
                'event' as type,
                CONCAT('events.php?id=', id) as url,
                created_at
            FROM events 
            WHERE (title LIKE ? OR description LIKE ? OR content LIKE ?)
            AND status IN ('upcoming', 'ongoing')
            ORDER BY event_date DESC
            LIMIT 10
        ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();
        $event_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Search in academic_programs table
        $sql = "
            SELECT 
                id, 
                name as title, 
                description as content, 
                'program' as type,
                CONCAT('academic_programs.php?id=', id) as url,
                created_at
            FROM academic_programs 
            WHERE (name LIKE ? OR description LIKE ?)
            ORDER BY name ASC
            LIMIT 10
        ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $program_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Search in departments table
        $sql = "
            SELECT 
                id, 
                name as title, 
                description as content, 
                'department' as type,
                CONCAT('department.php?code=', code) as url,
                created_at
            FROM departments 
            WHERE (name LIKE ? OR description LIKE ? OR code LIKE ?)
            AND is_active = 1
            ORDER BY name ASC
            LIMIT 10
        ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();
        $department_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Search in offices table
        $sql = "
            SELECT 
                id, 
                name as title, 
                description as content, 
                'office' as type,
                CONCAT('office.php?code=', code) as url,
                created_at
            FROM offices 
            WHERE (name LIKE ? OR description LIKE ? OR code LIKE ?)
            AND is_active = 1
            ORDER BY name ASC
            LIMIT 10
        ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();
        $office_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Search in achievements table
        $sql = "
            SELECT 
                id, 
                title, 
                description as content, 
                'achievement' as type,
                CONCAT('achievements.php?id=', id) as url,
                created_at
            FROM achievements 
            WHERE (title LIKE ? OR description LIKE ?)
            AND is_published = 1
            ORDER BY achievement_date DESC
            LIMIT 10
        ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $achievement_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Search in gallery table
        $sql = "
            SELECT 
                id, 
                title, 
                description as content, 
                'gallery' as type,
                CONCAT('gallery.php?id=', id) as url,
                created_at
            FROM gallery 
            WHERE (title LIKE ? OR description LIKE ?)
            ORDER BY date DESC
            LIMIT 10
        ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $gallery_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Search in academic_calendar table
        $sql = "
            SELECT 
                id, 
                title, 
                description as content, 
                'calendar' as type,
                CONCAT('academic_calendar.php') as url,
                created_at
            FROM academic_calendar 
            WHERE (title LIKE ? OR description LIKE ?)
            ORDER BY start_date DESC
            LIMIT 10
        ";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $search_term, $search_term);
        $stmt->execute();
        $calendar_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Combine all results
        $all_results = array_merge(
            $news_results,
            $event_results,
            $program_results,
            $department_results,
            $office_results,
            $achievement_results,
            $gallery_results,
            $calendar_results
        );
        
        // Sort by relevance
        usort($all_results, function($a, $b) use ($query) {
            $a_score = calculateRelevanceScore($a, $query);
            $b_score = calculateRelevanceScore($b, $query);
            return $b_score - $a_score;
        });
        
        return $all_results;
        
    } catch (Exception $e) {
        error_log("Search error: " . $e->getMessage());
        return [];
    }
}

function calculateRelevanceScore($item, $query) {
    $score = 0;
    $query = strtolower($query);
    $title = strtolower($item['title']);
    $content = isset($item['content']) ? strtolower($item['content']) : '';
    
    // Title matches are more important
    if (strpos($title, $query) !== false) {
        $score += 10;
    }
    
    // Content matches
    if ($content && strpos($content, $query) !== false) {
        $score += 5;
    }
    
    // Exact match bonus
    if ($title === $query) {
        $score += 15;
    }
    
    // Recent items get slight boost
    if (isset($item['created_at'])) {
        $days_ago = (time() - strtotime($item['created_at'])) / (60 * 60 * 24);
        if ($days_ago < 30) {
            $score += 2;
        }
    }
    
    return $score;
}

function getTypeBadgeClass($type) {
    $classes = [
        'news' => 'bg-primary',
        'event' => 'bg-success',
        'program' => 'bg-info',
        'department' => 'bg-warning',
        'office' => 'bg-secondary',
        'achievement' => 'bg-purple',
        'gallery' => 'bg-danger',
        'calendar' => 'bg-dark'
    ];
    
    return $classes[$type] ?? 'bg-secondary';
}

function getTypeDisplayName($type) {
    $names = [
        'news' => 'News',
        'event' => 'Event',
        'program' => 'Academic Program',
        'department' => 'Department',
        'office' => 'Office',
        'achievement' => 'Achievement',
        'gallery' => 'Gallery',
        'calendar' => 'Academic Calendar'
    ];
    
    return $names[$type] ?? ucfirst($type);
}

function highlightKeywords($text, $query) {
    if (empty($text)) return '';
    
    $keywords = explode(' ', $query);
    foreach ($keywords as $keyword) {
        $keyword = trim($keyword);
        if (strlen($keyword) > 2) {
            $text = preg_replace('/(' . preg_quote($keyword, '/') . ')/i', '<mark class="bg-warning">$1</mark>', $text);
        }
    }
    return $text;
}
?>

<div class="container mt-4 fade-in">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Search Results</h1>
            
            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="<?= url('search.php') ?>" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" 
                                   name="q" 
                                   class="form-control form-control-lg" 
                                   placeholder="Enter your search terms..." 
                                   value="<?= htmlspecialchars($query) ?>"
                                   required
                                   autofocus>
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Search Results -->
            <?php if (!empty($query)): ?>
                <div class="mb-4">
                    <p class="text-muted mb-2">
                        Found <strong><?= count($results) ?></strong> results for "<strong><?= htmlspecialchars($query) ?></strong>"
                    </p>
                    
                    <!-- Quick Filters -->
                    <?php if (!empty($results)): ?>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <small class="text-muted">Filter by type:</small>
                            <a href="javascript:void(0)" class="badge bg-primary text-decoration-none" onclick="filterResults('all')">
                                All (<?= count($results) ?>)
                            </a>
                            <?php
                            $types = [];
                            foreach ($results as $result) {
                                $types[$result['type']] = isset($types[$result['type']]) ? $types[$result['type']] + 1 : 1;
                            }
                            foreach ($types as $type => $count): ?>
                                <a href="javascript:void(0)" class="badge <?= getTypeBadgeClass($type) ?> text-decoration-none" onclick="filterResults('<?= $type ?>')">
                                    <?= getTypeDisplayName($type) ?> (<?= $count ?>)
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (empty($results)): ?>
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>
                                <h5 class="alert-heading">No results found</h5>
                                <p class="mb-0">No results found for "<strong><?= htmlspecialchars($query) ?></strong>". Try different keywords or check your spelling.</p>
                                <ul class="mt-2 mb-0">
                                    <li>Try more general keywords</li>
                                    <li>Check for spelling errors</li>
                                    <li>Search for related terms</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Suggested Searches -->
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Suggested Searches:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="<?= url('search.php') ?>?q=admission" class="btn btn-outline-primary btn-sm">Admission</a>
                                <a href="<?= url('search.php') ?>?q=academic+programs" class="btn btn-outline-primary btn-sm">Academic Programs</a>
                                <a href="<?= url('search.php') ?>?q=events" class="btn btn-outline-primary btn-sm">Events</a>
                                <a href="<?= url('search.php') ?>?q=news" class="btn btn-outline-primary btn-sm">News</a>
                                <a href="<?= url('search.php') ?>?q=departments" class="btn btn-outline-primary btn-sm">Departments</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="search-results">
                        <?php foreach ($results as $index => $result): ?>
                            <div class="card mb-3 result-item" data-type="<?= $result['type'] ?>" style="animation-delay: <?= $index * 0.1 ?>s">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-1">
                                            <a href="<?= $result['url'] ?>" class="text-decoration-none text-dark">
                                                <?= highlightKeywords(htmlspecialchars($result['title']), $query) ?>
                                            </a>
                                        </h5>
                                        <span class="badge <?= getTypeBadgeClass($result['type']) ?>">
                                            <?= getTypeDisplayName($result['type']) ?>
                                        </span>
                                    </div>
                                    
                                    <?php if (!empty($result['content'])): ?>
                                        <p class="card-text text-muted mb-2">
                                            <?= highlightKeywords(htmlspecialchars(substr($result['content'], 0, 250)), $query) ?>
                                            <?php if (strlen($result['content']) > 250): ?>...<?php endif; ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('M j, Y', strtotime($result['created_at'])) ?>
                                        </small>
                                        <a href="<?= $result['url'] ?>" class="btn btn-sm btn-outline-primary">
                                            View Details <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading">Search Required</h5>
                            <p class="mb-0">Please enter a search term to find content across the website.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Popular Search Categories -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-newspaper fa-3x text-primary mb-3"></i>
                                <h5>News & Announcements</h5>
                                <p class="text-muted">Latest updates and announcements</p>
                                <a href="<?= url('search.php') ?>?q=news" class="btn btn-outline-primary">Browse News</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt fa-3x text-success mb-3"></i>
                                <h5>Events</h5>
                                <p class="text-muted">Upcoming and ongoing events</p>
                                <a href="<?= url('search.php') ?>?q=events" class="btn btn-outline-success">Browse Events</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-graduation-cap fa-3x text-info mb-3"></i>
                                <h5>Academic Programs</h5>
                                <p class="text-muted">Courses and degree programs</p>
                                <a href="<?= url('search.php') ?>?q=academic+programs" class="btn btn-outline-info">Browse Programs</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-building fa-3x text-warning mb-3"></i>
                                <h5>Departments & Offices</h5>
                                <p class="text-muted">Academic departments and offices</p>
                                <a href="<?= url('search.php') ?>?q=departments" class="btn btn-outline-warning">Browse Departments</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Filter results by type
function filterResults(type) {
    const results = document.querySelectorAll('.result-item');
    let visibleCount = 0;
    
    results.forEach(result => {
        if (type === 'all' || result.getAttribute('data-type') === type) {
            result.style.display = 'block';
            visibleCount++;
        } else {
            result.style.display = 'none';
        }
    });
    
    // Update result count
    const resultCount = document.querySelector('.text-muted strong');
    if (resultCount) {
        resultCount.textContent = visibleCount;
    }
}

// Add animation to search results
document.addEventListener('DOMContentLoaded', function() {
    const results = document.querySelectorAll('.result-item');
    results.forEach((result, index) => {
        result.style.animationDelay = `${index * 0.1}s`;
        result.classList.add('fade-in');
    });
});
</script>

<style>
.result-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    opacity: 0;
    animation: fadeInUp 0.5s ease forwards;
}

.result-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

mark {
    padding: 0.1em 0.2em;
    border-radius: 0.25em;
}

.bg-purple {
    background-color: var(--purple) !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<?php include 'footer.php'; ?>