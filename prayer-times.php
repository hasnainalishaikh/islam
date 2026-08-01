<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Prayer Times Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Prayer Times');
define('META_DESCRIPTION', 'Accurate prayer times for your location. Fajr, Dhuhr, Asr, Maghrib, and Isha prayer schedules with countdown.');

include __DIR__ . '/includes/header.php';

$prayerTimes = getPrayerTimes();
$hijriDate = getHijriDate();

$prayerNames = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
$prayerIcons = ['sun', 'sunrise', 'sun', 'cloud-sun', 'sunset', 'moon'];
$prayerDisplay = ['Fajr', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Prayer Times</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Prayer Times</span>
            </nav>
        </div>
    </div>
</section>

<section class="prayer-page">
    <div class="container">
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
            <?php foreach ($prayerDisplay as $name): 
                $time = $prayerTimes[$name] ?? '--:--';
                $icon = $prayerIcons[array_search($name, $prayerNames)];
            ?>
            <div class="prayer-card">
                <div class="prayer-icon">
                    <i class="fas fa-<?php echo $icon; ?>"></i>
                </div>
                <div class="prayer-name"><?php echo $name; ?></div>
                <div class="prayer-time"><?php echo sanitizeOutput($time); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (isset($prayerTimes['fallback'])): ?>
        <div class="alert alert-info" style="margin-top: 24px; background: rgba(23, 162, 184, 0.1); border-color: var(--info); color: var(--info);">
            <i class="fas fa-info-circle"></i> 
            Using default prayer times. Enable location access for accurate times.
        </div>
        <?php endif; ?>
        
        <!-- Prayer Times Info -->
        <div class="glass-card" style="margin-top: 32px;">
            <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 16px; color: var(--primary-dark);">
                <i class="fas fa-info-circle"></i> About Prayer Times
            </h3>
            <div style="font-size: 0.95rem; line-height: 1.8; color: var(--gray);">
                <p>Prayer times are calculated based on the geographical location of your city. The five daily prayers are obligatory for every Muslim who has reached puberty and is of sound mind.</p>
                <ul style="margin-top: 12px; padding-left: 20px;">
                    <li style="list-style: disc; margin-bottom: 8px;"><strong>Fajr:</strong> Dawn before sunrise</li>
                    <li style="list-style: disc; margin-bottom: 8px;"><strong>Dhuhr:</strong> After midday when the sun passes its zenith</li>
                    <li style="list-style: disc; margin-bottom: 8px;"><strong>Asr:</strong> Afternoon, when the shadow of an object equals its height</li>
                    <li style="list-style: disc; margin-bottom: 8px;"><strong>Maghrib:</strong> Just after sunset</li>
                    <li style="list-style: disc; margin-bottom: 8px;"><strong>Isha:</strong> Nightfall, when the twilight disappears</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/prayer-times.js'];
include __DIR__ . '/includes/footer.php';
?>
