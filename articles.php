<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Articles Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Islamic Articles');
define('META_DESCRIPTION', 'Read insightful Islamic articles about faith, worship, history, and spirituality.');

include __DIR__ . '/includes/header.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, title, slug, category, excerpt, featured_image, author, published_at, views FROM articles WHERE status = 'published' ORDER BY published_at DESC");
    $stmt->execute();
    $articles = $stmt->fetchAll();
    
    // Get categories
    $catStmt = $pdo->prepare("SELECT DISTINCT category FROM articles WHERE status = 'published' ORDER BY category");
    $catStmt->execute();
    $categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $articles = [];
    $categories = [];
}
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Islamic Articles</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Articles</span>
            </nav>
        </div>
    </div>
</section>

<section class="articles-page">
    <div class="container">
        <!-- Search -->
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="articleSearch" placeholder="Search articles..." aria-label="Search articles">
        </div>
        
        <!-- Categories -->
        <div class="dua-categories" style="margin-bottom: 32px;">
            <button class="dua-category-btn article-category-btn active" data-category="all">All</button>
            <?php foreach ($categories as $category): ?>
            <button class="dua-category-btn article-category-btn" data-category="<?php echo sanitizeOutput($category); ?>">
                <?php echo sanitizeOutput($category); ?>
            </button>
            <?php endforeach; ?>
        </div>
        
        <?php if (!empty($articles)): ?>
        <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));">
            <?php foreach ($articles as $article): ?>
            <div class="article-card" data-category="<?php echo sanitizeOutput($article['category']); ?>">
                <div class="article-image-placeholder">
                    <i class="fas fa-mosque"></i>
                </div>
                <div class="article-content">
                    <span class="article-category"><?php echo sanitizeOutput($article['category']); ?></span>
                    <h3 class="article-title">
                        <a href="article.php?slug=<?php echo sanitizeOutput($article['slug']); ?>">
                            <?php echo sanitizeOutput($article['title']); ?>
                        </a>
                    </h3>
                    <p class="article-excerpt"><?php echo sanitizeOutput(substr($article['excerpt'], 0, 150)) . '...'; ?></p>
                    <div class="article-meta">
                        <span><i class="fas fa-user"></i> <?php echo sanitizeOutput($article['author']); ?></span>
                        <span><i class="fas fa-clock"></i> <?php echo date('M j, Y', strtotime($article['published_at'])); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo (int)$article['views']; ?> views</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-newspaper"></i></div>
            <h3 class="empty-state-title">No Articles Yet</h3>
            <p class="empty-state-desc">Articles are being prepared. Please check back soon for new Islamic content.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/articles.js'];
include __DIR__ . '/includes/footer.php';
?>
