<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - User Profile Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'My Profile');

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token.';
    } else {
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("UPDATE users SET full_name = :full_name WHERE id = :id");
            $stmt->execute([':full_name' => $fullName, ':id' => $_SESSION['user_id']]);
            $_SESSION['user_name'] = $fullName;
            $message = 'Profile updated successfully!';
        } catch (Exception $e) {
            $error = 'Update failed. Please try again.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
    <div class="auth-container" style="max-width: 600px;">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h1 class="auth-title">My Profile</h1>
                <p class="auth-subtitle">Manage your account settings</p>
            </div>
            
            <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo sanitizeOutput($message); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitizeOutput($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-input" value="<?php echo sanitizeOutput($_SESSION['username']); ?>" disabled style="opacity: 0.7;">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="<?php echo sanitizeOutput($_SESSION['user_email']); ?>" disabled style="opacity: 0.7;">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-input" value="<?php echo sanitizeOutput($_SESSION['user_name'] ?? ''); ?>" placeholder="Enter your full name">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-input" value="<?php echo sanitizeOutput(ucfirst($_SESSION['user_role'] ?? 'user')); ?>" disabled style="opacity: 0.7;">
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="bookmarks.php" class="btn btn-ghost">
                    <i class="fas fa-bookmark"></i> My Bookmarks
                </a>
            </div>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>
