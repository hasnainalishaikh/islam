<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Privacy Policy Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Privacy Policy');

include __DIR__ . '/includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Privacy Policy</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Privacy Policy</span>
            </nav>
        </div>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 800px;">
        <div class="glass-card">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-dark); margin-bottom: 16px;">Our Commitment to Privacy</h2>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 24px;">At Noor al-Islam, we take your privacy seriously. This policy describes how we collect, use, and protect your personal information.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Information We Collect</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 16px;">We collect information you provide when registering, such as your name and email address. We also collect anonymous usage data to improve our services.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">How We Use Your Information</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 16px;">Your information is used to personalize your experience, process your requests, and send periodic emails regarding Islamic content if you subscribe.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Data Protection</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 16px;">We implement security measures to protect your personal information. We do not sell, trade, or transfer your information to third parties.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Cookies</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8; margin-bottom: 16px;">We use cookies to enhance your browsing experience. You can choose to disable cookies in your browser settings.</p>
            
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Contact Us</h3>
            <p style="font-size: 0.95rem; color: var(--gray); line-height: 1.8;">If you have questions about this policy, please contact us through our contact page.</p>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>
