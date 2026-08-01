<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Article Detail Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

$slug = isset($_GET['slug']) ? sanitizeInput($_GET['slug']) : '';

if (empty($slug)) {
    redirect(SITE_URL . '/articles.php');
}

try {
    $pdo = getDBConnection();
    
    // Get article
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = :slug AND status = 'published' LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    $article = $stmt->fetch();
    
    if (!$article) {
        redirect(SITE_URL . '/articles.php');
    }
    
    // Update view count
    $updateStmt = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
    $updateStmt->execute([':id' => $article['id']]);
    
    // Get related articles
    $relStmt = $pdo->prepare("SELECT id, title, slug, excerpt, published_at FROM articles WHERE category = :category AND id != :id AND status = 'published' ORDER BY published_at DESC LIMIT 3");
    $relStmt->execute([':category' => $article['category'], ':id' => $article['id']]);
    $relatedArticles = $relStmt->fetchAll();
    
} catch (Exception $e) {
    redirect(SITE_URL . '/articles.php');
}

define('PAGE_TITLE', $article['title']);
define('META_DESCRIPTION', $article['meta_description'] ?: $article['excerpt']);

include __DIR__ . '/includes/header.php';
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title"><?php echo sanitizeOutput($article['title']); ?></h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <a href="articles.php">Articles</a>
                <span class="separator">/</span>
                <span><?php echo sanitizeOutput(substr($article['title'], 0, 50)) . '...'; ?></span>
            </nav>
        </div>
    </div>
</section>

<section class="article-detail">
    <div class="container">
        <div class="contact-grid" style="grid-template-columns: 2fr 1fr;">
            <!-- Main Content -->
            <div>
                <div class="article-detail-header">
                    <span class="article-category" style="margin-bottom: 12px; display: inline-block;">
                        <?php echo sanitizeOutput($article['category']); ?>
                    </span>
                    <h1 style="font-size: 2rem; font-weight: 800; color: var(--dark); margin-bottom: 12px; line-height: 1.3;">
                        <?php echo sanitizeOutput($article['title']); ?>
                    </h1>
                    <div class="article-meta" style="font-size: 0.9rem;">
                        <span><i class="fas fa-user"></i> <?php echo sanitizeOutput($article['author']); ?></span>
                        <span><i class="fas fa-clock"></i> <?php echo date('F j, Y', strtotime($article['published_at'])); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo (int)$article['views']; ?> views</span>
                    </div>
                </div>
                
                <div class="article-detail-image-placeholder" style="width: 100%; height: 300px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center; font-size: 4rem; color: rgba(255,255,255,0.2); margin-bottom: 24px;">
                    <i class="fas fa-mosque"></i>
                </div>
                
                <div class="article-detail-content">
                    <?php echo $article['content']; ?>
                </div>
                
                <!-- Share -->
                <div class="d-flex" style="display: flex; align-items: center; gap: 16px; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--light-gray);">
                    <span style="font-weight: 600; color: var(--dark);">Share this article:</span>
                    <button class="verse-action-btn" onclick="shareContent('<?php echo sanitizeOutput($article['title']); ?>')">
                        <i class="fas fa-share-alt"></i> Share
                    </button>
                    <button class="verse-action-btn" onclick="copyToClipboard(window.location.href)">
                        <i class="fas fa-copy"></i> Copy Link
                    </button>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- Related Articles -->
                <div class="glass-card" style="position: sticky; top: 100px;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; color: var(--primary-dark);">
                        <i class="fas fa-link"></i> Related Articles
                    </h3>
                    
                    <?php if (!empty($relatedArticles)): ?>
                        <?php foreach ($relatedArticles as $related): ?>
                        <div style="padding: 12px 0; border-bottom: 1px solid var(--light-gray);">
                            <a href="article.php?slug=<?php echo sanitizeOutput($related['slug']); ?>" style="font-size: 0.95rem; font-weight: 600; color: var(--dark); transition: var(--transition); display: block;">
                                <?php echo sanitizeOutput($related['title']); ?>
                            </a>
                            <span style="font-size: 0.8rem; color: var(--gray);"><?php echo date('M j, Y', strtotime($related['published_at'])); ?></span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="font-size: 0.9rem; color: var(--gray);">No related articles found.</p>
                    <?php endif; ?>
                    
                    <div style="margin-top: 16px;">
                        <a href="articles.php" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
                            <i class="fas fa-newspaper"></i> All Articles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/article.js'];
include __DIR__ . '/includes/footer.php';
?>
