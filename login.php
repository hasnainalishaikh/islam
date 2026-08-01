<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Login Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Login');

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    // Verify CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
        } else {
            try {
                $pdo = getDBConnection();
                $stmt = $pdo->prepare("SELECT id, username, email, password, full_name, role FROM users WHERE email = :email OR username = :username LIMIT 1");
                $stmt->execute([':email' => $email, ':username' => $email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_name'] = $user['full_name'];
                    
                    // Update last login
                    $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
                    $updateStmt->execute([':id' => $user['id']]);
                    
                    // Ensure user profile exists
                    try {
                        $checkProfile = $pdo->prepare("SELECT id FROM user_profile WHERE user_id = :uid");
                        $checkProfile->execute([':uid' => $user['id']]);
                        if (!$checkProfile->fetch()) {
                            $initProfile = $pdo->prepare("INSERT INTO user_profile (user_id) VALUES (:uid)");
                            $initProfile->execute([':uid' => $user['id']]);
                        }
                    } catch (PDOException $e) {
                        // Table may not exist yet, ignore
                    }
                    
                    regenerateCsrfToken();
                    logActivity('login', 'User logged in: ' . $user['email']);
                    
                    redirect(SITE_URL . '/dashboard.php');
                } else {
                    $error = 'Invalid email/username or password.';
                    logActivity('login_failed', 'Failed login attempt: ' . $email);
                }
            } catch (Exception $e) {
                $error = 'Login failed. Please try again later.';
                error_log('Login error: ' . $e->getMessage());
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-mosque"></i>
                </div>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to your account</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitizeOutput($error); ?>
            </div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label class="form-label" for="email">Email or Username</label>
                    <input type="text" id="email" name="email" class="form-input" required placeholder="Enter your email or username">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="Enter your password" minlength="8">
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <div class="auth-footer">
                Don't have an account? <a href="register.php">Create one</a>
            </div>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>
