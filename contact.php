<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Contact Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Contact Us');
define('META_DESCRIPTION', 'Get in touch with us. Send us your questions, feedback, or suggestions.');

include __DIR__ . '/includes/header.php';
?>

<!-- ============================================
PAGE HEADER
============================================ -->
<section class="page-header">
    <div class="container">
        <div class="page-header-content">
            <h1 class="page-title">Contact Us</h1>
            <nav class="page-breadcrumb">
                <a href="index.php">Home</a>
                <span class="separator">/</span>
                <span>Contact</span>
            </nav>
        </div>
    </div>
</section>

<section class="contact-page">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-card">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Send us a Message</h2>
                <p style="font-size: 0.95rem; color: var(--gray); margin-bottom: 24px;">Have a question or feedback? We'd love to hear from you.</p>
                
                <div class="form-success" id="formSuccess">
                    <i class="fas fa-check-circle"></i> Your message has been sent successfully! We'll get back to you soon.
                </div>
                
                <form id="contactForm" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                    <input type="hidden" name="action" value="contact">
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input" required placeholder="Enter your full name">
                        <div class="form-error">Please enter your name</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your email address">
                        <div class="form-error">Please enter a valid email address</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" class="form-input" required placeholder="What is this about?">
                        <div class="form-error">Please enter a subject</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Message</label>
                        <textarea id="message" name="message" class="form-textarea" required placeholder="Write your message here..." rows="5"></textarea>
                        <div class="form-error">Please enter your message</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info-card">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--white); margin-bottom: 24px;">Get in Touch</h2>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-mosque"></i>
                    </div>
                    <div>
                        <div class="contact-info-label">Our Location</div>
                        <div class="contact-info-value">123 Islamic Center, Karachi, Pakistan</div>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <div class="contact-info-label">Email Address</div>
                        <div class="contact-info-value">info@nooralislam.com</div>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <div class="contact-info-label">Phone Number</div>
                        <div class="contact-info-value">+92 300 1234567</div>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="contact-info-label">Working Hours</div>
                        <div class="contact-info-value">Mon - Fri: 9:00 AM - 6:00 PM</div>
                    </div>
                </div>
                
                <div class="contact-social">
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="Twitter">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
                
                <!-- Map Placeholder -->
                <div style="margin-top: 24px; padding: 20px; background: rgba(255,255,255,0.08); border-radius: var(--radius-md); text-align: center;">
                    <i class="fas fa-map-marked-alt" style="font-size: 2rem; color: rgba(255,255,255,0.3); margin-bottom: 8px; display: block;"></i>
                    <span style="font-size: 0.85rem; color: rgba(255,255,255,0.5);">Islamic Center Map</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$extraScripts = [JS_PATH . 'pages/contact.js'];
include __DIR__ . '/includes/footer.php';
?>
