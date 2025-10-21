<?php
// index.php (root version) -  
require_once __DIR__ . '/config.php';

// Get statistics from database
try {
    // Get student count from settings
    $studentCountResult = dbQuery("SELECT setting_value FROM settings WHERE setting_key = 'student_count'");
    $studentCount = $studentCountResult->num_rows > 0 ? $studentCountResult->fetch_assoc()['setting_value'] : '2,500+';

    // Get faculty count from settings
    $facultyCountResult = dbQuery("SELECT setting_value FROM settings WHERE setting_key = 'faculty_count'");
    $facultyCount = $facultyCountResult->num_rows > 0 ? $facultyCountResult->fetch_assoc()['setting_value'] : '150+';

    // Get program count from settings
    $programCountResult = dbQuery("SELECT setting_value FROM settings WHERE setting_key = 'program_count'");
    $programCount = $programCountResult->num_rows > 0 ? $programCountResult->fetch_assoc()['setting_value'] : '25+';

    // Get years of excellence from settings
    $yearsResult = dbQuery("SELECT setting_value FROM settings WHERE setting_key = 'years_excellence'");
    $yearsExcellence = $yearsResult->num_rows > 0 ? $yearsResult->fetch_assoc()['setting_value'] : '30+';

    // Get latest published news
    $newsQuery = "
    SELECT id, title, excerpt, content, image_url, category, published_at, views
    FROM news 
    WHERE status = 'published' 
    AND published_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    AND published_at <= CURDATE()  
    ORDER BY published_at DESC 
";
    $newsResult = dbQuery($newsQuery);
    $latestNews = [];
    while ($row = $newsResult->fetch_assoc()) {
        $latestNews[] = $row;
    }

    // Get upcoming events
    $eventsQuery = "
        SELECT *
        FROM events 
        WHERE status = 'upcoming'  
        ORDER BY event_date ASC 
        LIMIT 3
    ";
    $eventsResult = dbQuery($eventsQuery);
    $upcomingEvents = [];
    while ($row = $eventsResult->fetch_assoc()) {
        $upcomingEvents[] = $row;
    }

       // Get past news (increase limit and check for more)
   $pastNewsQuery = "
    SELECT id, title, published_at, category, views
    FROM news 
    WHERE status = 'published' 
    AND published_at < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ORDER BY published_at DESC 
    LIMIT 7
";
    $pastNewsResult = dbQuery($pastNewsQuery);
    $pastNews = [];
    while ($row = $pastNewsResult->fetch_assoc()) {
        $pastNews[] = $row;
    }
    
  // Check if there are more past news items
  $morePastNewsQuery = "
  SELECT COUNT(*) as total
    FROM news 
    WHERE status = 'published' 
    AND published_at < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
";
    $morePastNewsResult = dbQuery($morePastNewsQuery);
    $totalPastNews = $morePastNewsResult->fetch_assoc()['total'];
    $hasMorePastNews = $totalPastNews > 6;
    
    // Show only first 6 for display, keep the 7th for "more" indicator
    $displayPastNews = array_slice($pastNews, 0, 6);

    // Get past events (increase limit and check for more)
    $pastEventsQuery = "
        SELECT id, title, event_date, event_type, location, views
        FROM events 
        WHERE status = 'published' OR (event_date < CURDATE() AND status != 'cancelled')
        ORDER BY event_date DESC 
        LIMIT 7
    ";
    $pastEventsResult = dbQuery($pastEventsQuery);
    $pastEvents = [];
    while ($row = $pastEventsResult->fetch_assoc()) {
        $pastEvents[] = $row;
    }
    
    // Check if there are more past events
    $morePastEventsQuery = "
        SELECT COUNT(*) as total
        FROM events 
        WHERE status = 'published' OR (event_date < CURDATE() AND status != 'cancelled')
    ";
    $morePastEventsResult = dbQuery($morePastEventsQuery);
    $totalPastEvents = $morePastEventsResult->fetch_assoc()['total'];
    $hasMorePastEvents = $totalPastEvents > 6;
    
    // Show only first 6 for display
    $displayPastEvents = array_slice($pastEvents, 0, 6);

} catch (Exception $e) {
    error_log("Archives Error: " . $e->getMessage());
    $displayPastNews = [];
    $displayPastEvents = [];
    $hasMorePastNews = false;
    $hasMorePastEvents = false;
    $totalPastNews = 0;
    $totalPastEvents = 0;
}

require_once BASE_PATH . '/header.php';
?>
<!-- Hero Section -->
<section class="hero-section position-relative text-white">
    <!-- Background Image Container -->
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background-image: url('images/cover-page.png'); 
                background-size: cover; 
                background-position: center;
                background-repeat: no-repeat;
                opacity: 1.8; ">
    </div>
    <!-- Dark overlay div - this goes OVER the background image -->
    <!-- <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-20"></div> -->
    
    <div class="container position-relative h-100 d-flex flex-column justify-content-center align-items-center text-center">
        <img src="images/sjcsi-logo.png" alt="School logo" class="mb-4 rounded-circle shadow" style="width: 240px; height: 240px; object-fit: cover;">
        <h1 class="display-4 fw-bold mb-4" style="color:#094b3de6">Saint Joseph College of Sindangan Incorporated</h1>
    </div>
</section>

<!-- Decorative Section -->
<!-- Decorative Section -->
<section class="py-4" style="background-color: #094B3D; height: 15vh; min-height: 100px;">
    <div class="container h-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 text-center text-white">
            <div class="col-4">
                <div class="motto-item">
                    <h4 class="fw-bold mb-1" style="font-size: 1.8rem; letter-spacing: 2px;color:#E3FFAB">OPUS</h4>
                    <p class="mb-0" style="font-size: 1rem; font-weight: 500; opacity: 0.9;">(WORK)</p>
                </div>
            </div>
            <div class="col-4">
                <div class="motto-item">
                    <h4 class="fw-bold mb-1" style="font-size: 1.8rem; letter-spacing: 2px;color:#E3FFAB">VITA</h4>
                    <p class="mb-0" style="font-size: 1rem; font-weight: 500; opacity: 0.9;">(LIFE)</p>
                </div>
            </div>
            <div class="col-4">
                <div class="motto-item">
                    <h4 class="fw-bold mb-1" style="font-size: 1.8rem; letter-spacing: 2px;color:#E3FFAB">LUX</h4>
                    <p class="mb-0" style="font-size: 1rem; font-weight: 500; opacity: 0.9;">(LIGHT)</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                        <h3 class="card-title fs-2 fw-bold mb-2"><?php echo htmlspecialchars($studentCount); ?></h3>
                        <p class="card-text text-muted">Students Enrolled</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-chalkboard-teacher fa-3x mb-3 text-primary"></i>
                        <h3 class="card-title fs-2 fw-bold mb-2"><?php echo htmlspecialchars($facultyCount); ?></h3>
                        <p class="card-text text-muted">Faculty Members</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-book-open fa-3x mb-3 text-primary"></i>
                        <h3 class="card-title fs-2 fw-bold mb-2"><?php echo htmlspecialchars($programCount); ?></h3>
                        <p class="card-text text-muted">Academic Programs</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="fas fa-award fa-3x mb-3 text-primary"></i>
                        <h3 class="card-title fs-2 fw-bold mb-2"><?php echo htmlspecialchars($yearsExcellence); ?></h3>
                        <p class="card-text text-muted">Years of Excellence</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest News & Announcements -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-3" style="color: #094b3d;">Latest News & Announcements</h2>
                <p class="text-muted">Stay updated with the latest happenings at SJCSI</p>
            </div>
            <a href="news_all.php" class="btn btn-outline-primary">
                View All News <i class="fas fa-chevron-right ms-2"></i>
            </a>
        </div>

        <?php if (empty($latestNews)): ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No news available</h4>
                <p class="text-muted">Check back later for updates and announcements.</p>
            </div>
        <?php else: ?>
            <!-- News Carousel -->
            <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-indicators">
                    <?php
                    $newsGroups = array_chunk($latestNews, 2);
                    for ($i = 0; $i < count($newsGroups); $i++) {
                        $active = $i === 0 ? 'class="active"' : '';
                        echo '<button type="button" data-bs-target="#newsCarousel" data-bs-slide-to="'.$i.'" '.$active.' aria-label="Slide '.($i+1).'"></button>';
                    }
                    ?>
                </div>
                
                <div class="carousel-inner">
                    <?php
                    foreach ($newsGroups as $groupIndex => $newsGroup) {
                        $active = $groupIndex === 0 ? 'active' : '';
                        echo '<div class="carousel-item '.$active.'">';
                        echo '<div class="row g-4">';
                        
                        foreach ($newsGroup as $news) {
                            // Format date nicely
                            $formattedDate = date('M d, Y', strtotime($news['published_at']));
                            
                            // Truncate excerpt if too long
                            $truncatedExcerpt = !empty($news['excerpt']) ? 
                                (strlen($news['excerpt']) > 120 ? substr($news['excerpt'], 0, 120) . '...' : $news['excerpt']) :
                                (strlen($news['content']) > 120 ? substr(strip_tags($news['content']), 0, 120) . '...' : strip_tags($news['content']));
                            
                            // Use placeholder image if no image_url
                            $imageUrl = !empty($news['image_url']) ? $news['image_url'] : 'images/placeholder-news.jpg';
                            
                            echo '
<div class="col-md-6">
    <a href="news.php?id='.$news['id'].'" class="text-decoration-none">
        <div class="news-card position-relative overflow-hidden rounded shadow-lg" style="height: 350px; transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer;">
            <img src="'.$imageUrl.'" class="w-100 h-100" alt="'.htmlspecialchars($news['title']).'" style="object-fit: cover; transition: transform 0.3s ease;">
            
            <!-- Dark overlay for better text readability -->
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 100%);"></div>
            
            <!-- Category badge -->
            <div class="position-absolute top-0 end-0 m-3">
                <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">'.htmlspecialchars($news['category'] ?? 'News').'</span>
            </div>
            
            <!-- Content overlay -->
            <div class="position-absolute bottom-0 start-0 end-0 p-4 text-white">
                <div class="mb-2">
                    <small class="text-white-50 fw-semibold">
                        <i class="fas fa-calendar-alt me-1"></i>'.$formattedDate.'
                        <span class="ms-3">
                            <i class="fas fa-eye me-1"></i>'.number_format($news['views']).' views
                        </span>
                    </small>
                </div>
                <h4 class="text-white fw-bold mb-3" style="line-height: 1.3;">'.htmlspecialchars($news['title']).'</h4>
                <p class="text-white-75 mb-3" style="line-height: 1.5; font-size: 0.95rem;">'.nl2br(htmlspecialchars($truncatedExcerpt)).'</p>
                <span class="btn btn-light btn-sm rounded-pill px-3 text-decoration-none fw-semibold" style="transition: all 0.3s ease; color: #094b3d;">
                    <i class="fas fa-arrow-right me-1"></i>Read More
                </span>
            </div>
            
            <!-- Hover overlay -->
            <div class="position-absolute top-0 start-0 w-100 h-100 news-hover-overlay" style="background: rgba(9, 75, 61, 0.2); opacity: 0; transition: opacity 0.3s ease;"></div>
        </div>
    </a>
</div>';
                        }
                        
                        // If odd number of news items, fill the remaining space
                        if (count($newsGroup) === 1) {
                            echo '<div class="col-md-6"></div>';
                        }
                        
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
                
                <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev" style="width: 5%;">
                    <div class="carousel-control-icon-wrapper">
                        <span class="carousel-control-prev-icon bg-primary rounded-circle p-3 shadow-lg" aria-hidden="true" style="width: 50px; height: 50px; transition: all 0.3s ease;"></span>
                    </div>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next" style="width: 5%;">
                    <div class="carousel-control-icon-wrapper">
                        <span class="carousel-control-next-icon bg-primary rounded-circle p-3 shadow-lg" aria-hidden="true" style="width: 50px; height: 50px; transition: all 0.3s ease;"></span>
                    </div>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Upcoming Events -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Upcoming Events</h2>
            <p class="text-muted">Don't miss these important dates and events</p>
        </div>
        
        <?php if (empty($upcomingEvents)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No upcoming events</h4>
                <p class="text-muted">Check back later for upcoming events and activities.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($upcomingEvents as $event): ?>
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
                                        <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($event['event_type']); ?></span>
                                        <h5 class="card-title" style="color: #094b3d;"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    </div>
                                </div>
                                
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
                                
                                <a href="events.php?id=<?php echo $event['id']; ?>" class="btn btn-link text-decoration-none" style="color: #094b3d;">
                                    Learn More <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-5">
            <a href="events_all.php" class="btn btn-outline-primary">
                More Events <i class="fas fa-chevron-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Achievements Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3" style="color: #094b3d;">Our Achievements</h2>
            <p class="text-muted">Celebrating excellence and success at SJCSI</p>
        </div>
        
        <?php
        // Fetch achievements from database
        try {
            $achievementsQuery = "
                SELECT * FROM achievements 
                WHERE is_published = 1 
                ORDER BY achievement_date DESC 
                LIMIT 6
            ";
            $achievementsResult = dbQuery($achievementsQuery);
            $achievements = [];
            while ($row = $achievementsResult->fetch_assoc()) {
                $achievements[] = $row;
            }
        } catch (Exception $e) {
            error_log("Achievements Error: " . $e->getMessage());
            $achievements = [];
        }
        ?>
        
        <?php if (empty($achievements)): ?>
            <div class="text-center py-5">
                <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No achievements to display</h4>
                <p class="text-muted">Check back later for our latest achievements.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($achievements as $achievement): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100 achievement-card">
                            <?php if (!empty($achievement['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($achievement['image_url']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($achievement['title']); ?>"
                                     style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
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
                                <p class="card-text">
                                    <?php echo htmlspecialchars($achievement['description']); ?>
                                </p>
                                <?php if (!empty($achievement['awarded_to'])): ?>
                                    <div class="mt-auto">
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
            
            <div class="text-center mt-5">
                <a href="achievements_all.php" class="btn btn-outline-primary">
                    View All Achievements <i class="fas fa-chevron-right ms-2"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Past News & Events Section -->
<section class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3" style="color: #094b3d;">Archives</h2>
            <p class="text-muted">Browse through our past news and events</p>
        </div>

        <div class="row">
            <!-- Past News Column -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #094b3d; color: white;">
                        <h4 class="mb-0">
                            <i class="fas fa-newspaper me-2"></i>
                            Past News & Articles
                        </h4>
                        <?php if ($totalPastNews > 0): ?>
                            <span class="badge bg-light text-dark"><?php echo number_format($totalPastNews); ?> total</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($displayPastNews)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-archive fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No archived news available</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($displayPastNews as $index => $news): ?>
                                    <div class="list-group-item border-0 px-0 py-3 past-item" style="animation-delay: <?php echo ($index * 0.1); ?>s;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="news.php?id=<?php echo $news['id']; ?>" 
                                                       class="text-decoration-none stretched-link past-link" 
                                                       style="color: #094b3d;"
                                                       onclick="incrementNewsViews(<?php echo $news['id']; ?>)">
                                                        <?php echo htmlspecialchars($news['title']); ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted d-flex align-items-center flex-wrap">
                                                    <span class="me-3">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        <?php echo date('M d, Y', strtotime($news['published_at'])); ?>
                                                    </span>
                                                    <span class="me-3">
                                                        <i class="fas fa-tag me-1"></i>
                                                        <?php echo htmlspecialchars($news['category'] ?? 'News'); ?>
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-eye me-1"></i>
                                                        <?php echo number_format($news['views']); ?>
                                                    </span>
                                                </small>
                                            </div>
                                            <div class="ms-2">
                                                <i class="fas fa-chevron-right text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($hasMorePastNews): ?>
                                <div class="text-center mt-4">
                                    <a href="news_archive.php" class="btn btn-outline-primary btn-sm view-more-btn">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        View More News 
                                        <span class="badge bg-primary ms-2"><?php echo $totalPastNews - 6; ?>+</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Past Events Column -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #6f42c1; color: white;">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            Past Events
                        </h4>
                        <?php if ($totalPastEvents > 0): ?>
                            <span class="badge bg-light text-dark"><?php echo number_format($totalPastEvents); ?> total</span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($displayPastEvents)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No past events available</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($displayPastEvents as $index => $event): ?>
                                    <div class="list-group-item border-0 px-0 py-3 past-item" style="animation-delay: <?php echo ($index * 0.1); ?>s;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <a href="events.php?id=<?php echo $event['id']; ?>" 
                                                       class="text-decoration-none stretched-link past-link" 
                                                       style="color: #6f42c1;"
                                                       onclick="incrementEventViews(<?php echo $event['id']; ?>)">
                                                        <?php echo htmlspecialchars($event['title']); ?>
                                                    </a>
                                                </h6>
                                                <small class="text-muted d-flex align-items-center flex-wrap">
                                                    <span class="me-3">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                                    </span>
                                                    <span class="me-3">
                                                        <i class="fas fa-tag me-1"></i>
                                                        <?php echo htmlspecialchars($event['event_type'] ?? 'Event'); ?>
                                                    </span>
                                                    <?php if ($event['location']): ?>
                                                        <span class="me-3 d-none d-md-inline">
                                                            <i class="fas fa-map-marker-alt me-1"></i>
                                                            <?php echo htmlspecialchars(substr($event['location'], 0, 15) . (strlen($event['location']) > 15 ? '...' : '')); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span>
                                                        <i class="fas fa-eye me-1"></i>
                                                        <?php echo number_format($event['views'] ?? 0); ?>
                                                    </span>
                                                </small>
                                            </div>
                                            <div class="ms-2">
                                                <i class="fas fa-chevron-right text-muted"></i>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if ($hasMorePastEvents): ?>
                                <div class="text-center mt-4">
                                    <a href="events_archive.php" class="btn btn-outline-secondary btn-sm view-more-btn">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        View More Events 
                                        <span class="badge bg-secondary ms-2"><?php echo $totalPastEvents - 6; ?>+</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.view-more-btn {
    transition: all 0.3s ease;
    border-radius: 25px;
    font-weight: 500;
    padding: 0.5rem 1.5rem;
}

.view-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.past-item {
    border-bottom: 1px solid #e9ecef !important;
    position: relative;
    transition: all 0.3s ease;
    opacity: 0;
    animation: slideInLeft 0.5s ease-out forwards;
}

.past-item:last-child {
    border-bottom: none !important;
}

.past-item:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: translateX(5px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.past-link {
    transition: all 0.3s ease;
    font-weight: 500;
    line-height: 1.4;
}

.past-link:hover {
    text-decoration: underline !important;
    color: #0056b3 !important;
}

.card-header .badge {
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
}

/* Improved responsive design */
@media (max-width: 768px) {
    .past-item small {
        font-size: 0.7rem;
    }
    
    .past-item small .d-none.d-md-inline {
        display: none !important;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .card-header .badge {
        align-self: flex-end;
    }
}

/* Animation improvements */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Badge pulse animation */
.view-more-btn .badge {
    animation: badgePulse 2s infinite;
}

@keyframes badgePulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

/* Loading state for dynamic content */
.loading-item {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 4px;
    height: 20px;
    margin: 5px 0;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}

/* Enhanced hover effects */
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
}

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.7) 50%, rgba(255,255,255,0.3) 100%);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
</style>

<script>
// Enhanced JavaScript for better interactivity
document.addEventListener('DOMContentLoaded', function() {
    // Add click tracking for archive items
    function incrementNewsViews(newsId) {
        // Optional: Send AJAX request to increment view count
        fetch('increment_views.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                type: 'news',
                id: newsId
            })
        }).catch(err => console.log('View tracking error:', err));
    }
    
    function incrementEventViews(eventId) {
        // Optional: Send AJAX request to increment view count
        fetch('increment_views.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                type: 'event',
                id: eventId
            })
        }).catch(err => console.log('View tracking error:', err));
    }
    
    // Make functions globally available
    window.incrementNewsViews = incrementNewsViews;
    window.incrementEventViews = incrementEventViews;
    
    // Enhanced scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                entry.target.style.opacity = '1';
            }
        });
    }, observerOptions);

    // Observe all archive items
    document.querySelectorAll('.past-item').forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.style.animationPlayState = 'paused';
        observer.observe(item);
    });
    
    // Add loading states for view more buttons
    document.querySelectorAll('.view-more-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
            this.style.pointerEvents = 'none';
            
            // Re-enable after a short delay (in real implementation, this would be after the page loads)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }, 1000);
        });
    });
    
    // Add smooth hover effects
    document.querySelectorAll('.past-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(9, 75, 61, 0.05)';
            this.querySelector('.fas.fa-chevron-right').style.transform = 'translateX(5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
            this.querySelector('.fas.fa-chevron-right').style.transform = 'translateX(0)';
        });
    });
});
</script>

<style>
/* Past News and Events styling */
.past-news-item:hover,
.past-event-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
}

.past-news-item .stretched-link:hover,
.past-event-item .stretched-link:hover {
    text-decoration: underline !important;
}

/* Compact card styling for 5-column layout */
@media (min-width: 992px) {
    .past-news-item,
    .past-event-item {
        min-height: 120px;
    }
}

@media (max-width: 991px) {
    /* Stack items on smaller screens */
    .past-news-item .col,
    .past-event-item .col {
        min-width: 250px;
        flex: 0 0 auto;
    }
    
    .row.g-2 {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    
    .row.g-2::-webkit-scrollbar {
        height: 8px;
    }
    
    .row.g-2::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .row.g-2::-webkit-scrollbar-thumb {
        background: #094b3d;
        border-radius: 10px;
    }
}

/* Event cards compact styling */
.card.shadow-sm h-100 {
    transition: all 0.3s ease;
}

.card.shadow-sm:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

/* Custom styles for the news carousel */
.news-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
}

.news-card:hover img {
    transform: scale(1.08);
}

.news-card:hover .news-hover-overlay {
    opacity: 1 !important;
}

.news-card .btn:hover {
    transform: scale(1.05);
    background-color: #094b3d !important;
    border-color: #094b3d !important;
    color: white !important;
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.85) !important;
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.7) !important;
}

.bg-gradient-to-top {
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
}

.carousel-control-prev:hover .carousel-control-prev-icon,
.carousel-control-next:hover .carousel-control-next-icon {
    background-color: #094b3d !important;
    transform: scale(1.1);
}

.carousel-indicators [data-bs-target] {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #094b3d;
    border: 2px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.carousel-indicators .active {
    background-color: #dc3545;
    transform: scale(1.2);
}

/* Event cards styling */
.card:hover {
    transform: translateY(-2px);
    transition: transform 0.3s ease;
}

/* Past Items Styling */
.past-item {
    border-bottom: 1px solid #e9ecef !important;
    position: relative;
    transition: all 0.3s ease;
}

.past-item:last-child {
    border-bottom: none !important;
}

.past-item:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: translateX(5px);
}

.past-link {
    transition: color 0.3s ease;
    font-weight: 500;
}

.past-link:hover {
    text-decoration: underline !important;
}

/* Archive cards */
.card-header {
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .news-card {
        margin-bottom: 2rem;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 8%;
    }
    
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        width: 40px !important;
        height: 40px !important;
    }

    .past-item small {
        font-size: 0.7rem;
    }

    .past-item small span {
        display: none;
    }

    .past-item small span:first-child {
        display: inline;
    }
}

/* Animation for carousel items */
.carousel-item {
    transition: transform 0.6s ease-in-out;
}

/* Badge animation */
.badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(13, 110, 253, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
    }
}

/* Achievements styling */
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

/* Department cards */
.card.border-0:hover {
    box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
}

.chatbot-message {
    max-width: 80%;
    margin-bottom: 10px;
    position: relative;
}

.chatbot-response {
    align-self: flex-start;
    margin-right: auto;
}

.user-message {
    align-self: flex-end;
    margin-left: auto;
    background-color: #094B3D;
    color: white;
}

.chatbot-body {
    display: flex;
    flex-direction: column;
}

.quick-question {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.chatbot-toggler {
    transition: all 0.3s ease;
}

.chatbot-toggler:hover {
    transform: scale(1.1);
}

/* Animation for new messages */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.chatbot-message, .user-message {
    animation: fadeIn 0.3s ease-out;
}

/* Archive section animations */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.past-item {
    animation: slideInLeft 0.5s ease-out;
}

.past-item:nth-child(even) {
    animation: slideInRight 0.5s ease-out;
}

/* Staggered animation delay */
.past-item:nth-child(1) { animation-delay: 0.1s; }
.past-item:nth-child(2) { animation-delay: 0.2s; }
.past-item:nth-child(3) { animation-delay: 0.3s; }
.past-item:nth-child(4) { animation-delay: 0.4s; }
.past-item:nth-child(5) { animation-delay: 0.5s; }
.past-item:nth-child(6) { animation-delay: 0.6s; }

/* Loading effect for archive cards */
.card-header {
    position: relative;
    overflow: hidden;
}

.card-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .chatbot-window {
        width: 300px !important;
        right: 20px !important;
        bottom: 80px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggler = document.querySelector('.chatbot-toggler');
    const chatbotWindow = document.querySelector('.chatbot-window');
    const closeChatbot = document.querySelector('.close-chatbot');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatbotBody = document.querySelector('.chatbot-body');
    const quickQuestions = document.querySelectorAll('.quick-question');
    
    // Toggle chatbot window
    if (chatbotToggler) {
        chatbotToggler.addEventListener('click', function() {
            if (chatbotWindow) {
                chatbotWindow.classList.toggle('d-none');
            }
        });
    }
    
    // Close chatbot window
    if (closeChatbot) {
        closeChatbot.addEventListener('click', function() {
            if (chatbotWindow) {
                chatbotWindow.classList.add('d-none');
            }
        });
    }
    
    // Handle form submission
    if (chatbotForm) {
        chatbotForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = this.querySelector('input');
            const message = input.value.trim();
            
            if (message) {
                // Add user message
                addMessage(message, 'user');
                input.value = '';
                
                // Simulate bot response (in a real implementation, this would be an AJAX call)
                setTimeout(() => {
                    const response = getBotResponse(message);
                    addMessage(response, 'bot');
                }, 1000);
            }
        });
    }
    
    // Quick question buttons
    quickQuestions.forEach(button => {
        button.addEventListener('click', function() {
            const question = this.textContent;
            addMessage(question, 'user');
            
            setTimeout(() => {
                const response = getBotResponse(question);
                addMessage(response, 'bot');
            }, 1000);
        });
    });
    
    // Add message to chat
    function addMessage(text, sender) {
        if (chatbotBody) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('chatbot-message');
            messageDiv.classList.add(sender === 'user' ? 'user-message' : 'chatbot-response');
            messageDiv.innerHTML = `<p class="mb-0">${text}</p>`;
            chatbotBody.appendChild(messageDiv);
            chatbotBody.scrollTop = chatbotBody.scrollHeight;
        }
    }
    
    // Simple bot response logic
    function getBotResponse(question) {
        question = question.toLowerCase();
        
        if (question.includes('admission') || question.includes('requirements')) {
            return "For admission requirements, please visit our Admissions page or contact the Registrar's Office at registrar@sjcsi.edu.ph.";
        } 
        else if (question.includes('tuition') || question.includes('fee')) {
            return "Tuition fees vary by program. You can find detailed fee structures on our website under the 'Tuition and Fees' section or contact our Accounting Office.";
        }
        else if (question.includes('program') || question.includes('course')) {
            return "SJCSI offers various programs including Education, Business, IT, and more. Visit our Academics page for complete details.";
        }
        else if (question.includes('contact') || question.includes('email') || question.includes('phone')) {
            return "You can reach us at:<br>Phone: (123) 456-7890<br>Email: info@sjcsi.edu.ph<br>Address: Saint Joseph College of Sindangan, Zamboanga del Norte";
        }
        else if (question.includes('archive') || question.includes('past') || question.includes('old')) {
            return "You can view our archived news and past events in the Archives section on this page, or visit our dedicated archive pages for more historical content.";
        }
        else {
            const responses = [
                "I'm sorry, I didn't understand that. Could you please rephrase your question?",
                "For more specific information, you might want to check our website or contact the relevant department.",
                "I'm still learning! That question is a bit beyond my current capabilities, but I can direct you to someone who can help.",
                "Thank you for your question. For detailed assistance, please visit our campus or contact our administration office."
            ];
            return responses[Math.floor(Math.random() * responses.length)];
        }
    }

    // Archive section scroll animation
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);

    // Observe archive items
    document.querySelectorAll('.past-item').forEach(item => {
        item.style.animationPlayState = 'paused';
        observer.observe(item);
    });

    // Add hover effects for past items
    document.querySelectorAll('.past-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(9, 75, 61, 0.05)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>
<?php require_once 'footer.php'; ?>