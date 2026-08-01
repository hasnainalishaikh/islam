<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Logout Handler
 * Premium Islamic Web Application
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

// Log the logout
if (isLoggedIn()) {
    logActivity('logout', 'User logged out: ' . ($_SESSION['user_email'] ?? 'unknown'));
}

// Destroy session
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect to home
redirect(SITE_URL . '/index.php');
?>
