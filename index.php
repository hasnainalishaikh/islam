<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Homepage
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Home');
define('META_DESCRIPTION', 'Your comprehensive Islamic resource for Quran, Hadith, Prayer Times, Duas, and more. Noor al-Islam - Guiding light to the straight path.');

include __DIR__ . '/includes/header.php';

// Get prayer times
$prayerTimes = getPrayerTimes();
$hijriDate = getHijriDate();

// Get latest articles
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, title, slug, category, excerpt, featured_image, author, published_at FROM articles WHERE status = 'published' ORDER BY published_at DESC LIMIT 3");
    $stmt->execute();
    $latestArticles = $stmt->fetchAll();
} catch (Exception $e) {
    $latestArticles = [];
}
?>

<!-- ============================================
HERO SECTION
============================================ -->
<section class="hero" id="home">
    <div class="hero-bg">
        <div class="hero-particles" id="heroParticles"></div>
    </div>
    
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-star"></i>
            <?php echo sanitizeOutput($hijriDate['display']); ?> AH
            <i class="fas fa-star"></i>
        </div>
        
        <h1 class="hero-title">
            Welcome to <span class="highlight">Noor al-Islam</span>
        </h1>
        
        <p class="hero-subtitle">
            Your comprehensive Islamic resource. Explore the Quran, Hadith, Prayer Times, 
            Duas, and more. May Allah guide us all to the straight path.
        </p>
        
        <div class="hero-actions">
            <a href="<?php echo SITE_URL; ?>/quran.php" class="btn btn-gold btn-lg">
                <i class="fas fa-book-quran"></i>
                Read Quran
            </a>
            <a href="<?php echo SITE_URL; ?>/prayer-times.php" class="btn btn-ghost btn-lg" style="color: white; border-color: rgba(255,255,255,0.3);">
                <i class="fas fa-clock"></i>
                Prayer Times
            </a>
            <a href="<?php echo SITE_URL; ?>/duas.php" class="btn btn-ghost btn-lg" style="color: white; border-color: rgba(255,255,255,0.3);">
                <i class="fas fa-hand-praying"></i>
                Daily Duas
            </a>
        </div>
        
        <div class="hero-stats">
            <div class="hero-stat-item">
                <span class="hero-stat-number">114</span>
                <span class="hero-stat-label">Quran Surahs</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-number">6,236</span>
                <span class="hero-stat-label">Total Verses</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-number">5</span>
                <span class="hero-stat-label">Daily Prayers</span>
            </div>
            <div class="hero-stat-item">
                <span class="hero-stat-number">99</span>
                <span class="hero-stat-label">Names of Allah</span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
PRAYER TIMES SECTION
============================================ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Prayer Times</span>
            <h2 class="section-title">Today's Prayer Schedule</h2>
            <p class="section-desc">Stay connected with your Creator through the five daily prayers.</p>
        </div>
        
        <div class="prayer-header-card">
            <div class="prayer-date">
                <i class="fas fa-calendar"></i>
                <?php echo date('l, F j, Y'); ?>
            </div>
            <div class="prayer-city">
                <i class="fas fa-location-dot"></i>
                <?php echo sanitizeOutput(DEFAULT_CITY . ', ' . DEFAULT_COUNTRY); ?>
                | <i class="fas fa-moon"></i> <?php echo sanitizeOutput($hijriDate['display']); ?>
            </div>
            <div class="prayer-countdown">
                <span class="prayer-countdown-label">Next Prayer:</span>
                <span class="prayer-countdown-time" id="prayerCountdown">--:--:--</span>
            </div>
        </div>
        
        <div class="prayer-grid">
            <?php
            $prayerNames = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
            $prayerIcons = ['sun', 'sun', 'cloud-sun', 'sunset', 'moon'];
            foreach ($prayerNames as $index => $name):
                $time = $prayerTimes[$name] ?? '--:--';
            ?>
            <div class="prayer-card">
                <div class="prayer-icon">
                    <i class="fas fa-<?php echo $prayerIcons[$index]; ?>"></i>
                </div>
                <div class="prayer-name"><?php echo $name; ?></div>
                <div class="prayer-time"><?php echo sanitizeOutput($time); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
FEATURED SURAHS
============================================ -->
<section class="section" style="background: var(--white);">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Quran</span>
            <h2 class="section-title">Featured Surahs</h2>
            <p class="section-desc">Explore the most recited chapters of the Holy Quran.</p>
        </div>
        
        <div class="surah-grid">
            <?php
            $featuredSurahs = [
                ['id' => 1, 'name' => 'الفاتحة', 'translation' => 'Al-Fatihah', 'english' => 'The Opening', 'verses' => 7, 'type' => 'Meccan'],
                ['id' => 36, 'name' => 'يس', 'translation' => 'Ya-Sin', 'english' => 'Ya-Sin', 'verses' => 83, 'type' => 'Meccan'],
                ['id' => 67, 'name' => 'الملك', 'translation' => 'Al-Mulk', 'english' => 'The Sovereignty', 'verses' => 30, 'type' => 'Meccan'],
                ['id' => 55, 'name' => 'الرحمن', 'translation' => 'Ar-Rahman', 'english' => 'The Beneficent', 'verses' => 78, 'type' => 'Medinan'],
                ['id' => 18, 'name' => 'الكهف', 'translation' => 'Al-Kahf', 'english' => 'The Cave', 'verses' => 110, 'type' => 'Meccan'],
                ['id' => 112, 'name' => 'الإخلاص', 'translation' => 'Al-Ikhlas', 'english' => 'The Sincerity', 'verses' => 4, 'type' => 'Meccan'],
            ];
            foreach ($featuredSurahs as $surah):
            ?>
            <div class="surah-card" onclick="window.location.href='quran.php?surah=<?php echo $surah['id']; ?>'">
                <div class="surah-number"><?php echo $surah['id']; ?></div>
                <div class="surah-name-arabic"><?php echo $surah['name']; ?></div>
                <div class="surah-name-translation"><?php echo $surah['translation']; ?></div>
                <div class="surah-name-english"><?php echo $surah['english']; ?></div>
                <div class="surah-info">
                    <span><i class="fas fa-quran"></i> <?php echo $surah['verses']; ?> Verses</span>
                    <span><i class="fas fa-map-pin"></i> <?php echo $surah['type']; ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 32px;">
            <a href="<?php echo SITE_URL; ?>/quran.php" class="btn btn-primary btn-lg">
                <i class="fas fa-book-quran"></i>
                Browse All Surahs
            </a>
        </div>
    </div>
</section>

<!-- ============================================
DAILY HADITH & DUA
============================================ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Daily Reminders</span>
            <h2 class="section-title">Hadith & Dua of the Day</h2>
        </div>
        
        <div class="cards-grid" style="grid-template-columns: 1fr 1fr;">
            <!-- Daily Hadith -->
            <div class="glass-card">
                <div class="d-flex" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div class="prayer-icon" style="width: 48px; height: 48px; background: rgba(212, 175, 55, 0.1); color: var(--gold);">
                        <i class="fas fa-scroll"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 700;">Hadith of the Day</h3>
                </div>
                <blockquote style="font-style: italic; color: var(--dark-gray); line-height: 1.8; margin-bottom: 16px; font-size: 0.95rem;">
                    "The best of you are those who are best to their families, and I am the best to my family."
                </blockquote>
                <p style="font-size: 0.85rem; color: var(--gray);">
                    — Prophet Muhammad (PBUH) - <span style="color: var(--primary); font-weight: 500;">Tirmidhi</span>
                </p>
            </div>
            
            <!-- Daily Dua -->
            <div class="glass-card">
                <div class="d-flex" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div class="prayer-icon" style="width: 48px; height: 48px; background: rgba(15, 109, 78, 0.1);">
                        <i class="fas fa-hand-praying"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; font-weight: 700;">Dua of the Day</h3>
                </div>
                <p style="font-family: var(--font-arabic); font-size: 1.5rem; text-align: right; direction: rtl; margin-bottom: 12px; color: var(--primary-dark);">
                    رَبَّنَا آتِنَا فِي الدُّنْيَا حَسَنَةً وَفِي الْآخِرَةِ حَسَنَةً وَقِنَا عَذَابَ النَّارِ
                </p>
                <p style="font-size: 0.95rem; color: var(--dark-gray); line-height: 1.6; margin-bottom: 8px;">
                    "Our Lord, give us in this world good and in the Hereafter good, and protect us from the punishment of the Fire."
                </p>
                <p style="font-size: 0.85rem; color: var(--gray);">
                    — Surah Al-Baqarah, 2:201
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
LATEST ARTICLES
============================================ -->
<section class="section" style="background: var(--white);">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Articles</span>
            <h2 class="section-title">Latest Islamic Articles</h2>
            <p class="section-desc">Explore insightful articles about Islam, faith, and spirituality.</p>
        </div>
        
        <?php if (!empty($latestArticles)): ?>
        <div class="cards-grid" style="grid-template-columns: repeat(3, 1fr);">
            <?php foreach ($latestArticles as $article): ?>
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
                    <p class="article-excerpt"><?php echo sanitizeOutput(substr($article['excerpt'], 0, 120)) . '...'; ?></p>
                    <div class="article-meta">
                        <span><i class="fas fa-user"></i> <?php echo sanitizeOutput($article['author']); ?></span>
                        <span><i class="fas fa-clock"></i> <?php echo date('M j, Y', strtotime($article['published_at'])); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-newspaper"></i></div>
            <h3 class="empty-state-title">Articles Coming Soon</h3>
            <p class="empty-state-desc">We're preparing insightful articles. Check back soon!</p>
        </div>
        <?php endif; ?>
        
        <div style="text-align: center; margin-top: 32px;">
            <a href="<?php echo SITE_URL; ?>/articles.php" class="btn btn-primary btn-lg">
                <i class="fas fa-newspaper"></i>
                View All Articles
            </a>
        </div>
    </div>
</section>

<!-- ============================================
NEWSLETTER SECTION
============================================ -->
<section class="section" style="background: var(--primary-gradient);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title" style="color: var(--white);">Stay Connected</h2>
            <p class="section-desc" style="color: rgba(255,255,255,0.8);">Subscribe to receive daily Islamic reminders, duas, and updates.</p>
        </div>
        
        <div style="max-width: 500px; margin: 0 auto;">
            <form class="newsletter-form" method="POST" action="<?php echo SITE_URL; ?>/api/newsletter.php" style="display: flex; gap: 0; border-radius: var(--radius-md); overflow: hidden;">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address" required style="flex: 1; padding: 16px 20px; border: none; font-size: 1rem; background: rgba(255,255,255,0.15); color: var(--white);">
                <button type="submit" class="newsletter-btn" style="padding: 16px 32px; background: var(--gold); color: var(--dark); font-weight: 700; font-size: 1rem; border: none; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i> Subscribe
                </button>
            </form>
        </div>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/home.js'];
include __DIR__ . '/includes/footer.php';
?>
