<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Register Page
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Register');

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    // Verify CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } elseif (!validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $error = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
            $error = 'Username must be 3-50 characters (letters, numbers, underscores).';
        } else {
            try {
                $pdo = getDBConnection();
                
                // Check if username or email exists
                $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1");
                $checkStmt->execute([':email' => $email, ':username' => $username]);
                
                if ($checkStmt->fetch()) {
                    $error = 'Username or email already exists.';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                    
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name) VALUES (:username, :email, :password, :full_name)");
                    $stmt->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':password' => $hashedPassword,
                        ':full_name' => $fullName ?: $username
                    ]);
                    
                    $success = 'Account created successfully! You can now login.';
                    logActivity('register', 'New user registered: ' . $email);
                    
                    regenerateCsrfToken();
                }
            } catch (Exception $e) {
                $error = 'Registration failed. Please try again later.';
                error_log('Registration error: ' . $e->getMessage());
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
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join our Islamic community</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo sanitizeOutput($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo sanitizeOutput($success); ?>
            </div>
            <?php endif; ?>
            
            <form id="registerForm" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                <input type="hidden" name="action" value="register">
                
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-input" required placeholder="Choose a username" pattern="[a-zA-Z0-9_]{3,50}">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="full_name">Full Name (Optional)</label>
                    <input type="text" id="full_name" name="full_name" class="form-input" placeholder="Enter your full name">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-input" required placeholder="Enter your email address">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="Create a strong password" minlength="8">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required placeholder="Confirm your password" minlength="8">
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="auth-footer">
                Already have an account? <a href="login.php">Sign in</a>
            </div>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>
