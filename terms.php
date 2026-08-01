<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Terms of Use Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Terms of Use');

include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Terms of Use</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Terms of Use</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 800px;">
        <div class="glass-card">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 16px;">Terms and Conditions</h2>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 24px;">By using Noor al-Islam, you agree to these terms. Please read them carefully.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Acceptance of Terms</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 16px;">By accessing this website, you agree to be bound by these terms. If you disagree, please do not use our services.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Use of Content</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 16px;">All Quranic verses, hadiths, and Islamic content are provided for personal and educational use. Commercial use is prohibited without permission.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">User Conduct</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 16px;">Users agree not to misuse the website, engage in harmful activities, or violate any applicable laws.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Disclaimer</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 16px;">Prayer times are approximate and should be verified locally. Islamic content is provided for informational purposes.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Changes</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8;">We reserve the right to modify these terms at any time. Continued use constitutes acceptance of changes.</p>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>
