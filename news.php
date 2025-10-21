<?php
// news.php - View a single news article
require_once __DIR__ . '/config.php';

$news_id = $_GET['id'] ?? 0;
if (!$news_id || !is_numeric($news_id)) {
    header('Location: index.php');
    exit;
}

try {
    // Get news article
    $sql = "SELECT n.*, u.email as author_email 
            FROM news n 
            LEFT JOIN users u ON n.author_id = u.id 
            WHERE n.id = ?";
    $stmt = dbPrepare($sql);
    $stmt->bind_param('i', $news_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('Location: index.php');
        exit;
    }
    
    $news = $result->fetch_assoc();
    
    // Update view count
    $updateSql = "UPDATE news SET views = views + 1 WHERE id = ?";
    $updateStmt = dbPrepare($updateSql);
    $updateStmt->bind_param('i', $news_id);
    $updateStmt->execute();
    
} catch (Exception $e) {
    error_log("News View Error: " . $e->getMessage());
    header('Location: index.php');
    exit;
}

$page_title = $news['title'];
require_once BASE_PATH . '/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <article class="news-article">
                <div class="mb-4">
                    <a href="index.php" class="btn btn-outline-secondary mb-3">
                        <i class="fas fa-arrow-left me-2"></i>Back to News
                    </a>
                    
                    <?php if (!empty($news['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($news['image_url']); ?>" class="img-fluid rounded mb-4" alt="<?php echo htmlspecialchars($news['title']); ?>">
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-primary"><?php echo htmlspecialchars($news['category'] ?? 'General'); ?></span>
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <?php echo date('M d, Y', strtotime($news['published_at'])); ?>
                            <span class="mx-2">|</span>
                            <i class="fas fa-eye me-1"></i>
                            <?php echo number_format($news['views']); ?> views
                        </small>
                    </div>
                    
                    <h1 class="mb-3"><?php echo htmlspecialchars($news['title']); ?></h1>
                    
                    <?php if (!empty($news['author_email'])): ?>
                        <p class="text-muted mb-4">
                            <i class="fas fa-user me-1"></i>
                            Posted by <?php echo htmlspecialchars($news['author_email']); ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($news['excerpt'])): ?>
                    <div class="lead mb-4 p-3 bg-light rounded">
                        <?php echo nl2br(htmlspecialchars($news['excerpt'])); ?>
                    </div>
                <?php endif; ?>
                
                <div class="news-content mb-5">
                    <?php echo nl2br(htmlspecialchars($news['content'])); ?>
                </div>
                
                <div class="border-top pt-4">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to News
                    </a>
                </div>
            </article>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/footer.php'; ?>