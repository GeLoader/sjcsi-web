<?php 
include 'header.php';
require_once 'config.php';

// Fetch gallery items from database using the same query structure as admin dashboard
try {
    $galleryItemsResult = dbQuery("
        SELECT g.*, 
               (SELECT COUNT(*) FROM gallery_images WHERE gallery_id = g.id) as image_count 
        FROM gallery g 
        WHERE g.type = 'image'  -- Only show image galleries on public page
        ORDER BY g.date DESC
    ");
    $galleryItems = [];
    while ($row = $galleryItemsResult->fetch_assoc()) {
        $galleryItems[] = $row;
    }
} catch (Exception $e) {
    error_log("Gallery Page Error: " . $e->getMessage());
    $galleryItems = [];
}

// Define categories based on admin dashboard categories
$categories = [
    ['id' => 'all', 'label' => 'All', 'count' => count($galleryItems)],
    ['id' => 'events', 'label' => 'Events', 'count' => 0],
    ['id' => 'department', 'label' => 'Department', 'count' => 0],
    ['id' => 'institutional', 'label' => 'Institutional', 'count' => 0],
    ['id' => 'facilities', 'label' => 'Facilities', 'count' => 0],
    ['id' => 'campus', 'label' => 'Campus', 'count' => 0],
    ['id' => 'activities', 'label' => 'Student Activities', 'count' => 0]
];

// Calculate category counts
foreach ($galleryItems as $item) {
    foreach ($categories as &$category) {
        if ($category['id'] === $item['category']) {
            $category['count']++;
        }
    }
}
?>

<!-- Main Content -->
<div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="fw-bold primary-color mb-3">Campus Gallery</h1>
        <p class="lead">
            Explore life at SJCSI through our collection of photos and videos showcasing campus facilities, events, and
            student activities.
        </p>
    </div>

    <!-- Category Tabs -->
    <ul class="nav nav-tabs justify-content-center mb-4" id="galleryTabs" role="tablist">
        <?php
        foreach ($categories as $category) {
            if ($category['count'] > 0 || $category['id'] === 'all') {
                $active = $category['id'] === 'all' ? 'active' : '';
                echo '<li class="nav-item" role="presentation">
                    <button class="nav-link '.$active.'" id="'.$category['id'].'-tab" data-bs-toggle="tab" 
                    data-bs-target="#'.$category['id'].'" type="button">
                        '.$category['label'].' ('.$category['count'].')
                    </button>
                </li>';
            }
        }
        ?>
    </ul>

    <!-- Gallery Content -->
    <div class="tab-content" id="galleryTabsContent">
        <?php
        foreach ($categories as $category) {
            if ($category['count'] > 0 || $category['id'] === 'all') {
                $active = $category['id'] === 'all' ? 'show active' : '';
                echo '<div class="tab-pane fade '.$active.'" id="'.$category['id'].'" role="tabpanel">';
                
                echo '<div class="row g-4">';
                $filteredItems = ($category['id'] === 'all') 
                    ? $galleryItems 
                    : array_filter($galleryItems, function($item) use ($category) { 
                        return $item['category'] === $category['id']; 
                    });
                
                if (empty($filteredItems)) {
                    echo '<div class="col-12 text-center py-5">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No gallery items found in this category.</p>
                    </div>';
                } else {
                    foreach ($filteredItems as $item) {
                        // Get first image for thumbnail
                        $thumbnailResult = dbQuery("SELECT image_path FROM gallery_images WHERE gallery_id = ? ORDER BY id LIMIT 1", [$item['id']]);
                        $thumbnail = $thumbnailResult->fetch_assoc();
                        
                        $videoId = '';
                        if ($item['type'] === 'video' && $item['video_url']) {
                            // Extract YouTube video ID from URL
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $item['video_url'], $matches)) {
                                $videoId = $matches[1];
                            }
                        }
                        
                        echo '
                        <div class="col-md-6 col-lg-4">
                            <div class="card gallery-item h-100" data-bs-toggle="modal" data-bs-target="#galleryModal'.$item['id'].'">
                                <div class="card-img-top position-relative overflow-hidden">
                                    <img src="'.($thumbnail ? $thumbnail['image_path'] : 'images/default-gallery.jpg').'" 
                                         class="img-fluid w-100" alt="'.$item['title'].'" 
                                         style="height: 200px; object-fit: cover;">
                                    <div class="overlay d-flex align-items-center justify-content-center">
                                        '.($item['type'] === 'video' 
                                            ? '<i class="fas fa-play text-white fa-3x"></i>' 
                                            : '<i class="fas fa-images text-white fa-3x"></i>').'
                                    </div>
                                    <span class="badge position-absolute top-0 end-0 m-2 '.($item['type'] === 'video' ? 'bg-danger' : 'bg-primary').'">
                                        '.($item['type'] === 'video' ? 'Video' : $item['image_count'].' Photos').'
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h3 class="h5 card-title">'.$item['title'].'</h3>
                                    <p class="card-text text-muted">'.substr($item['description'], 0, 100).(strlen($item['description']) > 100 ? '...' : '').'</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> '.date('M d, Y', strtotime($item['date'])).'</small>
                                        <span class="badge bg-light text-dark">'.ucfirst($item['category']).'</span>
                                    </div>
                                </div>
                            </div>
                        </div>';

                        // Modal for each item
                        echo '
                        <div class="modal fade" id="galleryModal'.$item['id'].'" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">'.$item['title'].'</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">';
                                        
                                        if ($item['type'] === 'video' && $videoId) {
                                            echo '<div class="ratio ratio-16x9">
                                                <iframe src="https://www.youtube.com/embed/'.$videoId.'" allowfullscreen></iframe>
                                            </div>';
                                        } elseif ($item['type'] === 'video') {
                                            echo '<div class="alert alert-info">Video URL not properly formatted</div>';
                                        } else {
                                            // Display image carousel for image galleries
                                            $imagesResult = dbQuery("SELECT * FROM gallery_images WHERE gallery_id = ? ORDER BY display_order", [$item['id']]);
                                            $images = [];
                                            while ($image = $imagesResult->fetch_assoc()) {
                                                $images[] = $image;
                                            }
                                            
                                            if (!empty($images)) {
                                                echo '<div id="carousel'.$item['id'].'" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">';
                                                foreach ($images as $index => $image) {
                                                    $active = $index === 0 ? 'active' : '';
                                                    echo '<button type="button" data-bs-target="#carousel'.$item['id'].'" data-bs-slide-to="'.$index.'" class="'.$active.'"></button>';
                                                }
                                                echo '</div>
                                                    <div class="carousel-inner">';
                                                foreach ($images as $index => $image) {
                                                    $active = $index === 0 ? 'active' : '';
                                                    echo '<div class="carousel-item '.$active.'">
                                                        <img src="'.$image['image_path'].'" class="d-block w-100" alt="'.$item['title'].'" style="max-height: 500px; object-fit: contain;">
                                                    </div>';
                                                }
                                                echo '</div>
                                                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel'.$item['id'].'" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon"></span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel'.$item['id'].'" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon"></span>
                                                    </button>
                                                </div>';
                                            }
                                        }
                                        
                                        echo '<div class="mt-3">
                                            <p>'.$item['description'].'</p>
                                            <div class="d-flex gap-3 flex-wrap">
                                                <span class="badge bg-secondary"><i class="far fa-calendar-alt me-1"></i> '.date('M d, Y', strtotime($item['date'])).'</span>
                                                <span class="badge bg-primary">'.ucfirst($item['category']).'</span>
                                                <span class="badge '.($item['type'] === 'video' ? 'bg-danger' : 'bg-success').'">
                                                    '.($item['type'] === 'video' ? 'Video' : $item['image_count'].' Photos').'
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                }
                echo '</div></div>';
            }
        }
        ?>
    </div>

    <!-- Load More Button (optional for pagination) -->
    <?php if (count($galleryItems) > 6): ?>
    <div class="text-center mt-5">
        <button class="btn btn-outline-primary btn-lg" id="loadMoreBtn">Load More Items</button>
    </div>
    <?php endif; ?>
</div>

<style>
    .gallery-item {
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .gallery-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0,0,0,0.3);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .gallery-item:hover .overlay {
        opacity: 1;
    }
    .primary-color {
        color: #094b3d;
    }
    .nav-tabs .nav-link {
        color: #094b3d;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #094b3d;
        font-weight: 600;
        border-bottom: 3px solid #094b3d;
    }
    .carousel-item img {
        border-radius: 8px;
    }
        /* Fix for carousel controls */
    .carousel-control-prev,
    .carousel-control-next {
        width: 5%;
        background-color: rgba(0, 0, 0, 0.3);
        opacity: 0.8;
    }
    .carousel-control-prev:hover,
    .carousel-control-next:hover {
        opacity: 1;
    }
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        width: 2rem;
        height: 2rem;
        background-size: 100% 100%;
    }
     .carousel-control-prev {
        border-radius: 0 8px 8px 0;
    }
    .carousel-control-next {
        border-radius: 8px 0 0 8px;
    }
</style>

<script>
// Optional: Load more functionality
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        let itemsToShow = 6;
        const galleryItems = document.querySelectorAll('.gallery-item');
        
        // Initially hide items beyond the first 6
        galleryItems.forEach((item, index) => {
            if (index >= itemsToShow) {
                item.parentElement.style.display = 'none';
            }
        });
        
        loadMoreBtn.addEventListener('click', function() {
            itemsToShow += 6;
            galleryItems.forEach((item, index) => {
                if (index < itemsToShow) {
                    item.parentElement.style.display = 'block';
                }
            });
            
            // Hide button if all items are shown
            if (itemsToShow >= galleryItems.length) {
                loadMoreBtn.style.display = 'none';
            }
        });
    }
});
</script>

<?php include 'footer.php'; ?>