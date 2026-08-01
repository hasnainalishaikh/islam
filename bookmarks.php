<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Bookmarks Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'My Bookmarks');

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

$bookmarks = [];
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM bookmarks WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 50");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $bookmarks = $stmt->fetchAll();
} catch (Exception $e) {
    // Silently handle
}

include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">My Bookmarks</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Bookmarks</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (!empty($bookmarks)): ?>
        <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            <?php foreach ($bookmarks as $bookmark): ?>
            <div class="glass-card">
                <div class="d-flex" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <span class="article-category"><?php echo sanitizeOutput($bookmark['surah_name']); ?></span>
                    <span style="font-size: 0.85rem; color: var(--gray);">Verse <?php echo (int)$bookmark['verse_number']; ?></span>
                </div>
                <p style="font-family: var(--font-arabic); font-size: 1.3rem; text-align: right; direction: rtl; margin-bottom: 12px; color: var(--primary-dark);">
                    <?php echo sanitizeOutput($bookmark['arabic_text']); ?>
                </p>
                <p style="font-size: 0.9rem; color: var(--dark-gray); line-height: 1.6; margin-bottom: 8px;">
                    <?php echo sanitizeOutput(substr($bookmark['english_text'] ?? '', 0, 100)) . '...'; ?>
                </p>
                <div class="d-flex" style="display: flex; gap: 8px; margin-top: 12px;">
                    <a href="quran.php?surah=<?php echo (int)$bookmark['surah_number']; ?>&verse=<?php echo (int)$bookmark['verse_number']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-book-open"></i> Read
                    </a>
                    <button class="btn btn-ghost btn-sm" onclick="removeBookmark(<?php echo (int)$bookmark['id']; ?>)">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-bookmark"></i></div>
            <h3 class="empty-state-title">No Bookmarks Yet</h3>
            <p class="empty-state-desc">Start bookmarking your favorite Quran verses while reading.</p>
            <a href="quran.php" class="btn btn-primary" style="margin-top: 16px;">
                <i class="fas fa-book-quran"></i> Browse Quran
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
function removeBookmark(id) {
    if (!confirm('Remove this bookmark?')) return;
    // AJAX remove would go here
    location.reload();
}
</script>

<?php
include __DIR__ . '/includes/footer.php';
?>
