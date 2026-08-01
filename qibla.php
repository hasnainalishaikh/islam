<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Qibla Direction Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Qibla Direction');
define('META_DESCRIPTION', 'Find the accurate Qibla direction for prayer. Interactive compass showing the direction of Kaaba in Mecca.');

include __DIR__ . '/includes/header.php';
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Qibla Direction</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Qibla Direction</span>
            </nav>
        </div>
    </div>
</section>

<section class="qibla-page">
    <div class="container">
        <div class="qibla-container">
            <!-- Compass -->
            <div class="compass-container" id="compassContainer">
                <div class="compass-outer">
                    <div class="compass-direction north">N</div>
                    <div class="compass-direction south">S</div>
                    <div class="compass-direction east">E</div>
                    <div class="compass-direction west">W</div>
                    <div class="compass-inner">
                        <div class="compass-needle" id="qiblaNeedle"></div>
                        <div class="compass-center"></div>
                    </div>
                    <div class="qibla-marker">Qibla</div>
                </div>
            </div>
            
            <!-- Qibla Info -->
            <div class="compass-degree" id="qiblaDegree">--°</div>
            <div class="compass-degree-label">Qibla Direction</div>
            <div class="compass-degree-label" id="qiblaLocation" style="font-size: 0.85rem; color: var(--gray); margin-top: 8px;">
                Enable location access for accurate Qibla direction
            </div>
            
            <!-- Instructions -->
            <div class="glass-card" style="margin-top: 32px; text-align: left;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-info-circle" style="color: var(--primary);"></i>
                    How to Use
                </h3>
                <ol style="padding-left: 20px; font-size: 0.95rem; color: var(--gray); line-height: 2;">
                    <li>Allow location access when prompted (or rotate manually)</li>
                    <li>Hold your device flat, pointing forward</li>
                    <li>The red needle points to the Qibla direction</li>
                    <li>Face the direction indicated by the needle</li>
                    <li>The degree reading shows the exact Qibla angle</li>
                </ol>
            </div>
            
            <!-- Manual Controls -->
            <div class="d-flex" style="display: flex; gap: 12px; justify-content: center; margin-top: 24px;">
                <button class="btn btn-ghost btn-sm qibla-rotate-btn" data-delta="-15">
                    <i class="fas fa-undo"></i> -15°
                </button>
                <button class="btn btn-ghost btn-sm qibla-rotate-btn" data-delta="-5">
                    <i class="fas fa-chevron-left"></i> -5°
                </button>
                <button class="btn btn-ghost btn-sm" onclick="location.reload()">
                    <i class="fas fa-redo"></i> Reset
                </button>
                <button class="btn btn-ghost btn-sm qibla-rotate-btn" data-delta="5">
                    <i class="fas fa-chevron-right"></i> +5°
                </button>
                <button class="btn btn-ghost btn-sm qibla-rotate-btn" data-delta="15">
                    <i class="fas fa-redo"></i> +15°
                </button>
            </div>
        </div>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/qibla.js'];
include __DIR__ . '/includes/footer.php';
?>
