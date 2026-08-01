<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Tasbeeh Counter Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Tasbeeh Counter');
define('META_DESCRIPTION', 'Digital Tasbeeh counter for your daily dhikr. Count SubhanAllah, Alhamdulillah, Allahu Akbar and more.');

include __DIR__ . '/includes/header.php';
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Tasbeeh Counter</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Tasbeeh</span>
            </nav>
        </div>
    </div>
</section>

<section class="tasbeeh-page">
    <div class="container">
        <div class="tasbeeh-container">
            <div class="tasbeeh-header">
                <p style="font-size: 1rem; color: var(--gray); line-height: 1.6;">
                    "Those who remember Allah standing, sitting, and lying on their sides..."
                    <br><span style="font-size: 0.85rem;">— Surah Al-Imran, 3:191</span>
                </p>
            </div>
            
            <!-- Zikr Selection -->
            <div class="tasbeeh-zikr-select">
                <select id="zikrSelect" aria-label="Select Zikr">
                    <option value="SubhanAllah">SubhanAllah (سبحان الله)</option>
                    <option value="Alhamdulillah">Alhamdulillah (الحمد لله)</option>
                    <option value="AllahuAkbar">Allahu Akbar (الله أكبر)</option>
                    <option value="LaIlahaIllallah">La Ilaha Illallah (لا إله إلا الله)</option>
                    <option value="Astaghfirullah">Astaghfirullah (أستغفر الله)</option>
                    <option value="SubhanAllahWaBihamdihi">SubhanAllah wa Bihamdihi (سبحان الله وبحمده)</option>
                </select>
            </div>
            
            <!-- Counter Display -->
            <div class="tasbeeh-display" id="tasbeehDisplay" role="button" tabindex="0" aria-label="Tap to count">
                <div class="tasbeeh-circle"></div>
                <div class="tasbeeh-count" id="tasbeehCount">0</div>
                <div class="tasbeeh-label" id="tasbeehLabel">سبحان الله</div>
                <div class="tasbeeh-progress">
                    <div class="tasbeeh-progress-bar" id="tasbeehProgress" style="width: 0%;"></div>
                </div>
                <div class="tasbeeh-target" id="tasbeehTarget">Target: 33</div>
            </div>
            
            <p style="text-align: center; font-size: 0.85rem; color: var(--gray); margin-bottom: 24px;">
                <i class="fas fa-hand-pointer"></i> Tap the circle to count &nbsp;|&nbsp; 
                <i class="fas fa-keyboard"></i> Press Space to count
            </p>
            
            <!-- Actions -->
            <div class="tasbeeh-actions">
                <button class="btn btn-ghost" id="tasbeehReset">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button class="btn btn-primary" id="tasbeehSave">
                    <i class="fas fa-save"></i> Save Progress
                </button>
            </div>
            
            <!-- History -->
            <div class="glass-card" style="margin-top: 32px;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-history" style="color: var(--primary);"></i>
                    Today's History
                </h3>
                <div id="tasbeehHistory">
                    <p style="font-size: 0.9rem; color: var(--gray);">No history recorded yet. Save your progress to see history.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/tasbeeh.js'];
include __DIR__ . '/includes/footer.php';
?>
