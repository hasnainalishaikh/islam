<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Quran Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Holy Quran');
define('META_DESCRIPTION', 'Read the Holy Quran with Arabic text, Urdu and English translations. Search, bookmark, and listen to audio recitation.');

include __DIR__ . '/includes/header.php';

$surahId = isset($_GET['surah']) ? (int)$_GET['surah'] : 0;
$verseId = isset($_GET['verse']) ? (int)$_GET['verse'] : 0;

// Quran API URLs
$quranApiBase = 'https://api.alquran.cloud/v1';
$surahListUrl = $quranApiBase . '/surah';
$surahDetailUrl = $surahId ? $quranApiBase . '/surah/' . $surahId . '/editions/ar,ur,en' : '';
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title"><?php echo $surahId ? 'Surah Reader' : 'Holy Quran'; ?></h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Quran</span>
                <?php if ($surahId): ?>
                <span class="separator">/</span>
                <span>Surah Reader</span>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</section>

<section class="quran-page">
    <div class="container">
        <?php if ($surahId > 0 && $surahId <= 114): ?>
        <!-- ============================================
        SURAH READER
        ============================================ -->
        <div class="quran-reader" id="quranReader">
            <div class="surah-header" id="surahHeader">
                <div class="spinner" style="margin: 20px auto; border-color: rgba(255,255,255,0.2); border-top-color: var(--gold);"></div>
            </div>
            <div id="versesContainer"></div>
            
            <div style="text-align: center; margin-top: 32px;">
                <a href="quran.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Surah List
                </a>
            </div>
        </div>
        <?php else: ?>
        <!-- ============================================
        SURAH LIST
        ============================================ -->
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="surahSearch" placeholder="Search surahs by name or number..." aria-label="Search surahs">
        </div>
        
        <div style="text-align: center; margin-bottom: 24px;">
            <select id="surahFilter" class="form-select" style="max-width: 200px; display: inline-block; padding: 10px 20px; border-radius: var(--radius-sm); border: 2px solid var(--light-gray);">
                <option value="all">All Types</option>
                <option value="meccan">Meccan</option>
                <option value="medinan">Medinan</option>
            </select>
        </div>
        
        <div class="surah-grid" id="surahGrid">
            <!-- Surah cards will be rendered by JavaScript -->
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/quran.js'];
include __DIR__ . '/includes/footer.php';
?>
