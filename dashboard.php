<?php
/**
 * ============================================================
 * ISLAMIC WEBSITE - Premium User Dashboard
 * Apple-level UI Islamic Productivity Dashboard
 * ============================================================
 */

define('ACCESS_ALLOWED', true);
require_once __DIR__ . '/includes/config.php';

define('PAGE_TITLE', 'Dashboard');

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect(SITE_URL . '/login.php');
}

$userId = (int)$_SESSION['user_id'];
$pdo = getDBConnection();

// ============================================================
// INITIALIZE USER PROFILE
// ============================================================
$stmt = $pdo->prepare("SELECT * FROM user_profile WHERE user_id = :uid");
$stmt->execute([':uid' => $userId]);
$userProfile = $stmt->fetch();

if (!$userProfile) {
    $stmt = $pdo->prepare("INSERT INTO user_profile (user_id) VALUES (:uid)");
    $stmt->execute([':uid' => $userId]);
    $userProfile = [
        'phone' => '', 'country' => '', 'bio' => '',
        'avatar' => 'default-avatar.png',
        'prayer_streak' => 0, 'best_streak' => 0,
        'total_tasbeeh_count' => 0, 'quran_reading_pct' => 0
    ];
}

// ============================================================
// GET PRAYER TIMES
// ============================================================
$prayerTimes = getPrayerTimes();

// ============================================================
// GET HIJRI DATE
// ============================================================
$hijriDate = getHijriDate();

// ============================================================
// GET TODAY'S PRAYER TRACKER
// ============================================================
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM prayer_tracker WHERE user_id = :uid AND prayer_date = :dt");
$stmt->execute([':uid' => $userId, ':dt' => $today]);
$todayPrayer = $stmt->fetch();

if (!$todayPrayer) {
    $stmt = $pdo->prepare("INSERT INTO prayer_tracker (user_id, prayer_date) VALUES (:uid, :dt)");
    $stmt->execute([':uid' => $userId, ':dt' => $today]);
    $todayPrayer = [
        'fajr' => 'pending', 'dhuhr' => 'pending', 'asr' => 'pending',
        'maghrib' => 'pending', 'isha' => 'pending',
        'total_completed' => 0, 'total_missed' => 0, 'completion_pct' => 0
    ];
}

// ============================================================
// GET TASBEEH STATS
// ============================================================
$todayTasbeeh = 0;
$weeklyTasbeeh = 0;
$monthlyTasbeeh = 0;
$lifetimeTasbeeh = (int)($userProfile['total_tasbeeh_count'] ?? 0);

$stmt = $pdo->prepare("SELECT COALESCE(SUM(count), 0) as total FROM tasbeeh_stats WHERE user_id = :uid AND session_date = :dt");
$stmt->execute([':uid' => $userId, ':dt' => $today]);
$row = $stmt->fetch();
$todayTasbeeh = (int)$row['total'];

$weekStart = date('Y-m-d', strtotime('monday this week'));
$stmt = $pdo->prepare("SELECT COALESCE(SUM(count), 0) as total FROM tasbeeh_stats WHERE user_id = :uid AND session_date >= :ws");
$stmt->execute([':uid' => $userId, ':ws' => $weekStart]);
$row = $stmt->fetch();
$weeklyTasbeeh = (int)$row['total'];

$monthStart = date('Y-m-01');
$stmt = $pdo->prepare("SELECT COALESCE(SUM(count), 0) as total FROM tasbeeh_stats WHERE user_id = :uid AND session_date >= :ms");
$stmt->execute([':uid' => $userId, ':ms' => $monthStart]);
$row = $stmt->fetch();
$monthlyTasbeeh = (int)$row['total'];

// ============================================================
// GET QURAN PROGRESS
// ============================================================
$stmt = $pdo->prepare("SELECT * FROM quran_progress WHERE user_id = :uid ORDER BY last_read_at DESC LIMIT 5");
$stmt->execute([':uid' => $userId]);
$recentSurahs = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM quran_progress WHERE user_id = :uid ORDER BY last_read_at DESC LIMIT 1");
$stmt->execute([':uid' => $userId]);
$lastReadSurah = $stmt->fetch();

// ============================================================
// GET FAVORITE DUAS
// ============================================================
$stmt = $pdo->prepare("SELECT * FROM favorite_duas WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10");
$stmt->execute([':uid' => $userId]);
$favoriteDuas = $stmt->fetchAll();

// ============================================================
// GET ACHIEVEMENTS
// ============================================================
$stmt = $pdo->prepare("SELECT * FROM achievements WHERE user_id = :uid");
$stmt->execute([':uid' => $userId]);
$userAchievements = $stmt->fetchAll();
$achievedKeys = array_column($userAchievements, 'achievement_key');

// ============================================================
// GET GOALS
// ============================================================
$stmt = $pdo->prepare("SELECT * FROM user_goals WHERE user_id = :uid AND (goal_date = :dt OR goal_date IS NULL) ORDER BY created_at DESC");
$stmt->execute([':uid' => $userId, ':dt' => $today]);
$userGoals = $stmt->fetchAll();

// Ensure default goals exist
$defaultGoals = [
    ['prayer_daily', 'Daily Prayer Goal', 5],
    ['quran_daily', 'Daily Quran Goal', 1],
    ['tasbeeh_daily', 'Daily Tasbeeh Goal', 100]
];

foreach ($defaultGoals as $dg) {
    $exists = false;
    foreach ($userGoals as $ug) {
        if ($ug['goal_type'] === $dg[0]) { $exists = true; break; }
    }
    if (!$exists) {
        $stmt = $pdo->prepare("INSERT INTO user_goals (user_id, goal_type, goal_name, target_value, current_value, goal_date) VALUES (:uid, :gt, :gn, :tv, 0, :dt)");
        $stmt->execute([':uid' => $userId, ':gt' => $dg[0], ':gn' => $dg[1], ':tv' => $dg[2], ':dt' => $today]);
        $userGoals[] = ['goal_type' => $dg[0], 'goal_name' => $dg[1], 'target_value' => $dg[2], 'current_value' => 0, 'is_completed' => 0];
    }
}

// ============================================================
// GET NOTIFICATIONS
// ============================================================
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 20");
$stmt->execute([':uid' => $userId]);
$notifications = $stmt->fetchAll();

$unreadCount = 0;
foreach ($notifications as $n) {
    if (!$n['is_read']) $unreadCount++;
}

// ============================================================
// HANDLE AJAX REQUESTS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }
    
    $action = $_POST['ajax_action'];
    
    // PRAYER TRACKER UPDATE
    if ($action === 'update_prayer') {
        $prayer = $_POST['prayer'] ?? '';
        $status = $_POST['status'] ?? '';
        $validPrayers = ['fajr', 'dhuhr', 'asr', 'maghrib', 'isha'];
        
        if (in_array($prayer, $validPrayers) && in_array($status, ['completed', 'missed', 'pending'])) {
            $stmt = $pdo->prepare("UPDATE prayer_tracker SET $prayer = :st WHERE user_id = :uid AND prayer_date = :dt");
            $stmt->execute([':st' => $status, ':uid' => $userId, ':dt' => $today]);
            
            // Recalculate totals
            $stmt = $pdo->prepare("SELECT * FROM prayer_tracker WHERE user_id = :uid AND prayer_date = :dt");
            $stmt->execute([':uid' => $userId, ':dt' => $today]);
            $pt = $stmt->fetch();
            
            $completed = 0; $missed = 0;
            foreach ($validPrayers as $p) {
                if ($pt[$p] === 'completed') $completed++;
                if ($pt[$p] === 'missed') $missed++;
            }
            $pct = $completed > 0 ? round(($completed / 5) * 100) : 0;
            
            $stmt = $pdo->prepare("UPDATE prayer_tracker SET total_completed = :tc, total_missed = :tm, completion_pct = :cp WHERE user_id = :uid AND prayer_date = :dt");
            $stmt->execute([':tc' => $completed, ':tm' => $missed, ':cp' => $pct, ':uid' => $userId, ':dt' => $today]);
            
            // Check for first prayer achievement
            if ($completed >= 1) {
                $stmt = $pdo->prepare("INSERT IGNORE INTO achievements (user_id, achievement_key, achievement_name, achievement_desc, icon) VALUES (:uid, 'first_prayer', 'First Prayer Completed', 'Completed your first prayer on the tracker', 'fa-star')");
                $stmt->execute([':uid' => $userId]);
            }
            if ($completed >= 5) {
                $stmt = $pdo->prepare("INSERT IGNORE INTO achievements (user_id, achievement_key, achievement_name, achievement_desc, icon) VALUES (:uid, 'all_prayers', 'All Prayers Tracked', 'Completed all 5 daily prayers', 'fa-check-circle')");
                $stmt->execute([':uid' => $userId]);
            }
            
            // Check streak
            $stmt = $pdo->prepare("SELECT COUNT(*) as streak FROM prayer_tracker WHERE user_id = :uid AND completion_pct = 100 AND prayer_date >= DATE_SUB(:dt, INTERVAL 30 DAY)");
            $stmt->execute([':uid' => $userId, ':dt' => $today]);
            $streakData = $stmt->fetch();
            
            echo json_encode(['success' => true, 'completed' => $completed, 'missed' => $missed, 'pct' => $pct, 'streak' => (int)$streakData['streak']]);
            exit;
        }
    }
    
    // TASBEEH UPDATE
    if ($action === 'save_tasbeeh_dashboard') {
        $zikr = $_POST['zikr'] ?? 'SubhanAllah';
        $count = (int)($_POST['count'] ?? 0);
        $target = (int)($_POST['target'] ?? 33);
        
        $stmt = $pdo->prepare("INSERT INTO tasbeeh_stats (user_id, zikr_name, count, target, session_date) VALUES (:uid, :zikr, :cnt, :tgt, :dt)");
        $stmt->execute([':uid' => $userId, ':zikr' => $zikr, ':cnt' => $count, ':tgt' => $target, ':dt' => $today]);
        
        // Update lifetime count
        $stmt = $pdo->prepare("UPDATE user_profile SET total_tasbeeh_count = total_tasbeeh_count + :cnt WHERE user_id = :uid");
        $stmt->execute([':cnt' => $count, ':uid' => $userId]);
        
        // Check achievements
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(count), 0) as total FROM tasbeeh_stats WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $totalTasbeeh = (int)$stmt->fetchColumn();
        
        if ($totalTasbeeh >= 100) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO achievements (user_id, achievement_key, achievement_name, achievement_desc, icon) VALUES (:uid, '100_tasbeeh', '100 Tasbeeh Completed', 'Completed 100 tasbeeh in total', 'fa-hands')");
            $stmt->execute([':uid' => $userId]);
        }
        if ($totalTasbeeh >= 1000) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO achievements (user_id, achievement_key, achievement_name, achievement_desc, icon) VALUES (:uid, '1000_tasbeeh', '1000 Tasbeeh Completed', 'Completed 1000 tasbeeh in total', 'fa-crown')");
            $stmt->execute([':uid' => $userId]);
        }
        
        // Update goals
        $stmt = $pdo->prepare("UPDATE user_goals SET current_value = current_value + :cnt WHERE user_id = :uid AND goal_type = 'tasbeeh_daily' AND goal_date = :dt");
        $stmt->execute([':cnt' => $count, ':uid' => $userId, ':dt' => $today]);
        
        echo json_encode(['success' => true, 'message' => 'Tasbeeh saved!']);
        exit;
    }
    
    // PROFILE UPDATE
    if ($action === 'update_profile') {
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $country = sanitizeInput($_POST['country'] ?? '');
        $bio = sanitizeInput($_POST['bio'] ?? '');
        
        $stmt = $pdo->prepare("UPDATE users SET full_name = :fn WHERE id = :uid");
        $stmt->execute([':fn' => $fullName, ':uid' => $userId]);
        $_SESSION['user_name'] = $fullName;
        
        $stmt = $pdo->prepare("UPDATE user_profile SET phone = :ph, country = :co, bio = :bio WHERE user_id = :uid");
        $stmt->execute([':ph' => $phone, ':co' => $country, ':bio' => $bio, ':uid' => $userId]);
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully!']);
        exit;
    }
    
    // CHANGE PASSWORD
    if ($action === 'change_password') {
        $currentPw = $_POST['current_password'] ?? '';
        $newPw = $_POST['new_password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :uid");
        $stmt->execute([':uid' => $userId]);
        $user = $stmt->fetch();
        
        if (!password_verify($currentPw, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit;
        }
        
        if (strlen($newPw) < 8) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
            exit;
        }
        
        $hashed = password_hash($newPw, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare("UPDATE users SET password = :pw WHERE id = :uid");
        $stmt->execute([':pw' => $hashed, ':uid' => $userId]);
        
        echo json_encode(['success' => true, 'message' => 'Password changed successfully!']);
        exit;
    }
    
    // MARK NOTIFICATION READ
    if ($action === 'mark_read') {
        $notifId = (int)($_POST['notif_id'] ?? 0);
        if ($notifId > 0) {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = :nid AND user_id = :uid");
            $stmt->execute([':nid' => $notifId, ':uid' => $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid");
            $stmt->execute([':uid' => $userId]);
        }
        echo json_encode(['success' => true]);
        exit;
    }
    
    // ADD NOTIFICATION
    if ($action === 'add_notification') {
        $type = $_POST['notif_type'] ?? 'info';
        $title = $_POST['notif_title'] ?? '';
        $message = $_POST['notif_message'] ?? '';
        $icon = $_POST['notif_icon'] ?? 'bell';
        
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, icon) VALUES (:uid, :tp, :tl, :msg, :ic)");
        $stmt->execute([':uid' => $userId, ':tp' => $type, ':tl' => $title, ':msg' => $message, ':ic' => $icon]);
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    // FAVORITE DUA
    if ($action === 'favorite_dua') {
        $duaId = $_POST['dua_id'] ?? '';
        $category = $_POST['dua_category'] ?? '';
        $duaTitle = $_POST['dua_title'] ?? '';
        $arabicText = $_POST['arabic_text'] ?? '';
        $translation = $_POST['translation'] ?? '';
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO favorite_duas (user_id, dua_id, dua_category, dua_title, arabic_text, translation) VALUES (:uid, :di, :dc, :dt, :ar, :tr)");
        $stmt->execute([':uid' => $userId, ':di' => $duaId, ':dc' => $category, ':dt' => $duaTitle, ':ar' => $arabicText, ':tr' => $translation]);
        
        echo json_encode(['success' => true, 'message' => 'Dua added to favorites!']);
        exit;
    }
    
    // UPDATE GOAL
    if ($action === 'update_goal') {
        $goalType = $_POST['goal_type'] ?? '';
        $value = (int)($_POST['value'] ?? 0);
        
        $stmt = $pdo->prepare("UPDATE user_goals SET current_value = current_value + :val WHERE user_id = :uid AND goal_type = :gt AND goal_date = :dt");
        $stmt->execute([':val' => $value, ':uid' => $userId, ':gt' => $goalType, ':dt' => $today]);
        
        echo json_encode(['success' => true, 'message' => 'Goal updated!']);
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Unknown action']);
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<!-- Dashboard specific CSS -->
<link rel="stylesheet" href="<?php echo CSS_PATH; ?>dashboard.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">

<style>
    body { background: #F5F6FA; }
    body.dashboard-active .navbar { display: none; }
    body.dashboard-active .footer { display: none; }
    body.dashboard-active .scroll-to-top { display: none; }
    body.dashboard-active .preloader { display: none !important; }
</style>

<div class="dashboard-wrapper">
    <!-- ============================================
    SIDEBAR
    ============================================ -->
    <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-mosque"></i>
            </div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name"><?php echo SITE_NAME; ?></span>
                <span class="sidebar-brand-tagline">Dashboard</span>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <div class="sidebar-section-title">Main Menu</div>
            
            <a href="#" class="sidebar-item active" data-section="overview" onclick="switchSection('overview')">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="#" class="sidebar-item" data-section="prayer" onclick="switchSection('prayer')">
                <i class="fas fa-mosque"></i> Prayer Tracker
            </a>
            <a href="#" class="sidebar-item" data-section="tasbeeh" onclick="switchSection('tasbeeh')">
                <i class="fas fa-hands"></i> Tasbeeh
            </a>
            <a href="#" class="sidebar-item" data-section="quran" onclick="switchSection('quran')">
                <i class="fas fa-book-quran"></i> My Quran
            </a>
            <a href="#" class="sidebar-item" data-section="duas" onclick="switchSection('duas')">
                <i class="fas fa-hand-praying"></i> My Duas
            </a>
            
            <div class="sidebar-section-title">Progress</div>
            
            <a href="#" class="sidebar-item" data-section="achievements" onclick="switchSection('achievements')">
                <i class="fas fa-trophy"></i> Achievements
                <?php if (count($achievedKeys) > 0): ?>
                <span class="badge"><?php echo count($achievedKeys); ?></span>
                <?php endif; ?>
            </a>
            <a href="#" class="sidebar-item" data-section="goals" onclick="switchSection('goals')">
                <i class="fas fa-bullseye"></i> Goals
            </a>
            <a href="#" class="sidebar-item" data-section="notifications" onclick="switchSection('notifications')">
                <i class="fas fa-bell"></i> Notifications
                <?php if ($unreadCount > 0): ?>
                <span class="badge"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </a>
            
            <div class="sidebar-section-title">Account</div>
            
            <a href="#" class="sidebar-item" data-section="profile" onclick="switchSection('profile')">
                <i class="fas fa-user"></i> My Profile
            </a>
            <a href="#" class="sidebar-item" data-section="settings" onclick="switchSection('settings')">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="sidebar-item danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name"><?php echo sanitizeOutput($_SESSION['user_name'] ?? $_SESSION['username']); ?></div>
                    <div class="sidebar-user-role"><?php echo isAdmin() ? 'Administrator' : 'Member'; ?></div>
                </div>
            </div>
        </div>
    </aside>

    <!-- ============================================
    MAIN CONTENT
    ============================================ -->
    <main class="dashboard-main">
        <!-- TOP BAR -->
        <header class="dashboard-topbar">
            <div class="topbar-left">
                <button class="topbar-toggle-btn" id="sidebarToggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search dashboard..." id="dashboardSearch" oninput="searchDashboard(this.value)">
                </div>
            </div>
            
            <div class="topbar-right">
                <div class="topbar-date">
                    <div class="topbar-date-hijri"><?php echo sanitizeOutput($hijriDate['display']); ?></div>
                    <div class="topbar-date-gregorian"><?php echo date('l, F d, Y'); ?></div>
                </div>
                <div class="topbar-clock" id="topbarClock">--:--:--</div>
                
                <div class="topbar-notification-wrapper" style="position:relative;">
                    <button class="topbar-notification-btn" onclick="toggleNotifDropdown()">
                        <i class="fas fa-bell"></i>
                        <?php if ($unreadCount > 0): ?>
                        <span class="notif-dot"></span>
                        <?php endif; ?>
                    </button>
                    <div class="notif-dropdown" id="notifDropdown">
                        <?php if (!empty($notifications)): ?>
                            <?php foreach (array_slice($notifications, 0, 5) as $n): ?>
                            <div class="notif-item" onclick="markNotifRead(<?php echo $n['id']; ?>)">
                                <div class="notif-icon <?php echo $n['type']; ?>">
                                    <i class="fas fa-<?php echo $n['icon']; ?>"></i>
                                </div>
                                <div class="notif-content">
                                    <h4><?php echo sanitizeOutput($n['title']); ?></h4>
                                    <p><?php echo sanitizeOutput($n['message']); ?></p>
                                    <div class="notif-time"><?php echo date('h:i A', strtotime($n['created_at'])); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="padding: 24px; text-align: center; color: var(--gray);">
                                <i class="fas fa-bell" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                                No notifications yet
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="dashboard-content">
            <!-- ============================================
            SECTION: OVERVIEW (Default)
            ============================================ -->
            <div class="dash-section active" id="section-overview">
                <!-- Welcome Card -->
                <div class="welcome-card">
                    <div class="welcome-content">
                        <div class="welcome-text">
                            <h1>Assalamu Alaikum, <span><?php echo sanitizeOutput($_SESSION['user_name'] ?? $_SESSION['username']); ?></span></h1>
                            <p class="welcome-ayah">فَاذْكُرُونِي أَذْكُرْكُمْ</p>
                            <p class="welcome-ayah-trans">"So remember Me; I will remember you" (Quran 2:152)</p>
                        </div>
                        <div class="welcome-streak">
                            <div class="streak-icon"><i class="fas fa-fire"></i></div>
                            <div class="streak-info">
                                <div class="streak-count" id="streakCount"><?php echo (int)($userProfile['prayer_streak'] ?? 0); ?></div>
                                <div class="streak-label">Day Streak</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon green"><i class="fas fa-mosque"></i></div>
                        </div>
                        <div class="stat-card-value" id="statPrayerPct"><?php echo (int)($todayPrayer['completion_pct'] ?? 0); ?>%</div>
                        <div class="stat-card-label">Today's Prayer</div>
                        <?php if (($todayPrayer['completion_pct'] ?? 0) > 0): ?>
                        <div class="stat-card-change up"><i class="fas fa-arrow-up"></i> Tracked</div>
                        <?php else: ?>
                        <div class="stat-card-change down"><i class="fas fa-minus"></i> Pending</div>
                        <?php endif; ?>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon gold"><i class="fas fa-hands"></i></div>
                        </div>
                        <div class="stat-card-value"><?php echo number_format($todayTasbeeh); ?></div>
                        <div class="stat-card-label">Today's Tasbeeh</div>
                        <?php if ($todayTasbeeh > 0): ?>
                        <div class="stat-card-change up"><i class="fas fa-arrow-up"></i> Active</div>
                        <?php else: ?>
                        <div class="stat-card-change down"><i class="fas fa-minus"></i> Not started</div>
                        <?php endif; ?>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon blue"><i class="fas fa-book-quran"></i></div>
                        </div>
                        <div class="stat-card-value"><?php echo $lastReadSurah ? (int)$lastReadSurah['surah_number'] : '--'; ?></div>
                        <div class="stat-card-label">Last Read Surah</div>
                        <div class="stat-card-change up"><i class="fas fa-book-open"></i> Continue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-icon purple"><i class="fas fa-trophy"></i></div>
                        </div>
                        <div class="stat-card-value"><?php echo count($achievedKeys); ?></div>
                        <div class="stat-card-label">Achievements</div>
                        <div class="stat-card-change up"><i class="fas fa-star"></i> Keep going!</div>
                    </div>
                </div>

                <!-- Prayer Tracker Overview -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-mosque" style="color:var(--primary);margin-right:8px;"></i> Prayer Tracker</h2>
                        <a href="#" onclick="switchSection('prayer');return false;" class="section-link">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="prayer-tracker-grid">
                        <?php
                        $prayers = ['fajr' => 'Fajr', 'dhuhr' => 'Dhuhr', 'asr' => 'Asr', 'maghrib' => 'Maghrib', 'isha' => 'Isha'];
                        $prayerIcons = ['fajr' => 'fa-sun', 'dhuhr' => 'fa-sun', 'asr' => 'fa-cloud-sun', 'maghrib' => 'fa-moon', 'isha' => 'fa-moon'];
                        foreach ($prayers as $key => $name):
                            $status = $todayPrayer[$key] ?? 'pending';
                            $time = $prayerTimes[ucfirst($key)] ?? '--:--';
                        ?>
                        <div class="prayer-track-card">
                            <div class="prayer-track-icon <?php echo $status; ?>">
                                <i class="fas <?php echo $prayerIcons[$key]; ?>"></i>
                            </div>
                            <div class="prayer-track-name"><?php echo $name; ?></div>
                            <div class="prayer-track-time"><?php echo $time; ?></div>
                            <div class="prayer-track-actions">
                                <button class="prayer-btn complete <?php echo $status === 'completed' ? 'active' : ''; ?>" onclick="updatePrayer('<?php echo $key; ?>', 'completed')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="prayer-btn miss <?php echo $status === 'missed' ? 'active' : ''; ?>" onclick="updatePrayer('<?php echo $key; ?>', 'missed')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="prayer-track-pct"><?php echo $status === 'completed' ? '100%' : ($status === 'missed' ? '0%' : '--'); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Tasbeeh -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-hands" style="color:var(--gold);margin-right:8px;"></i> Quick Tasbeeh</h2>
                        <a href="#" onclick="switchSection('tasbeeh');return false;" class="section-link">
                            Full View <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="tasbeeh-dashboard">
                        <div style="text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                            <div class="progress-ring-container">
                                <svg class="progress-ring" width="120" height="120">
                                    <circle class="progress-ring-bg" cx="60" cy="60" r="52"></circle>
                                    <circle class="progress-ring-fill" id="tasbeehRing" cx="60" cy="60" r="52" stroke-dasharray="326.73" stroke-dashoffset="326.73"></circle>
                                </svg>
                                <div class="progress-ring-text">
                                    <span class="progress-ring-value" id="tasbeehRingCount">0</span>
                                    <span class="progress-ring-label">Today</span>
                                </div>
                            </div>
                            <div style="margin-top:12px;font-size:0.85rem;color:var(--gray);">
                                Target: <strong id="tasbeehTargetDisplay">33</strong>
                            </div>
                            <div class="tasbeeh-actions">
                                <button class="btn btn-primary" onclick="dashboardTasbeehTap()">
                                    <i class="fas fa-hand-pointer"></i> Tap
                                </button>
                                <button class="btn btn-ghost" onclick="dashboardTasbeehReset()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button class="btn btn-gold" onclick="dashboardTasbeehSave()">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </div>
                        </div>
                        <div class="tasbeeh-stats-cards">
                            <div class="tasbeeh-stat-mini">
                                <div class="tasbeeh-stat-mini-value"><?php echo number_format($todayTasbeeh); ?></div>
                                <div class="tasbeeh-stat-mini-label">Today</div>
                            </div>
                            <div class="tasbeeh-stat-mini">
                                <div class="tasbeeh-stat-mini-value"><?php echo number_format($weeklyTasbeeh); ?></div>
                                <div class="tasbeeh-stat-mini-label">This Week</div>
                            </div>
                            <div class="tasbeeh-stat-mini">
                                <div class="tasbeeh-stat-mini-value"><?php echo number_format($monthlyTasbeeh); ?></div>
                                <div class="tasbeeh-stat-mini-label">This Month</div>
                            </div>
                            <div class="tasbeeh-stat-mini">
                                <div class="tasbeeh-stat-mini-value"><?php echo number_format($lifetimeTasbeeh); ?></div>
                                <div class="tasbeeh-stat-mini-label">Lifetime</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Read Quran -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-book-quran" style="color:var(--primary);margin-right:8px;"></i> Continue Reading</h2>
                        <a href="<?php echo SITE_URL; ?>/quran.php" class="section-link">
                            Browse Quran <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="quran-dashboard">
                        <?php if ($lastReadSurah): ?>
                        <div class="quran-last-read">
                            <div class="quran-last-read-icon"><i class="fas fa-book-open"></i></div>
                            <div class="quran-last-read-info">
                                <h4>Surah <?php echo sanitizeOutput($lastReadSurah['surah_name'] ?? 'Al-Fatihah'); ?> (<?php echo (int)$lastReadSurah['surah_number']; ?>)</h4>
                                <p>Last read: <?php echo date('M d, Y h:i A', strtotime($lastReadSurah['last_read_at'] ?? 'now')); ?> | Verse <?php echo (int)$lastReadSurah['verse_number']; ?></p>
                            </div>
                            <a href="<?php echo SITE_URL; ?>/quran.php?surah=<?php echo (int)$lastReadSurah['surah_number']; ?>" class="btn btn-primary btn-sm" style="margin-left:auto;">
                                <i class="fas fa-play"></i> Continue
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="quran-last-read">
                            <div class="quran-last-read-icon"><i class="fas fa-book-quran"></i></div>
                            <div class="quran-last-read-info">
                                <h4>Start Your Quran Journey</h4>
                                <p>Begin reading the Quran and track your progress</p>
                            </div>
                            <a href="<?php echo SITE_URL; ?>/quran.php" class="btn btn-primary btn-sm" style="margin-left:auto;">
                                <i class="fas fa-play"></i> Start Reading
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="quran-stats-mini">
                            <div class="tasbeeh-stat-mini">
                                <div class="tasbeeh-stat-mini-value"><?php echo count($recentSurahs); ?></div>
                                <div class="tasbeeh-stat-mini-label">Surahs Read</div>
                            </div>
                            <div class="tasbeeh-stat-mini">
                                <div class="tasbeeh-stat-mini-value"><?php echo (int)($userProfile['quran_reading_pct'] ?? 0); ?>%</div>
                                <div class="tasbeeh-stat-mini-label">Progress</div>
                            </div>
                            <div class="tasbeeh-stat-mini">
                                <div class="tasbeeh-stat-mini-value"><?php echo count($recentSurahs); ?></div>
                                <div class="tasbeeh-stat-mini-label">Bookmarks</div>
                            </div>
                            <div class="tasbeeh-stat-mini">
                                <div class="tasbeeh-stat-mini-value"><?php echo count($recentSurahs); ?></div>
                                <div class="tasbeeh-stat-mini-label">Favorites</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================
            SECTION: PRAYER TRACKER
            ============================================ -->
            <div class="dash-section" id="section-prayer" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-mosque" style="color:var(--primary);margin-right:8px;"></i> Prayer Tracker</h2>
                    </div>
                    <p style="color:var(--gray);margin-bottom:20px;">Track your daily prayers. Click <strong>✓</strong> for completed, <strong>✗</strong> for missed.</p>
                    
                    <div class="prayer-tracker-grid">
                        <?php foreach ($prayers as $key => $name):
                            $status = $todayPrayer[$key] ?? 'pending';
                            $time = $prayerTimes[ucfirst($key)] ?? '--:--';
                        ?>
                        <div class="prayer-track-card">
                            <div class="prayer-track-icon <?php echo $status; ?>">
                                <i class="fas <?php echo $prayerIcons[$key]; ?>"></i>
                            </div>
                            <div class="prayer-track-name"><?php echo $name; ?></div>
                            <div class="prayer-track-time"><?php echo $time; ?></div>
                            <div class="prayer-track-actions">
                                <button class="prayer-btn complete <?php echo $status === 'completed' ? 'active' : ''; ?>" onclick="updatePrayer('<?php echo $key; ?>', 'completed')">
                                    <i class="fas fa-check"></i> Done
                                </button>
                                <button class="prayer-btn miss <?php echo $status === 'missed' ? 'active' : ''; ?>" onclick="updatePrayer('<?php echo $key; ?>', 'missed')">
                                    <i class="fas fa-times"></i> Missed
                                </button>
                            </div>
                            <div class="prayer-track-pct" id="pct-<?php echo $key; ?>"><?php echo $status === 'completed' ? '✓' : ($status === 'missed' ? '✗' : '--'); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;border:1px solid var(--light-gray);">
                        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
                            <div>
                                <span style="font-size:0.9rem;color:var(--gray);">Today's Completion</span>
                                <div style="font-size:2rem;font-weight:800;color:var(--primary);" id="todayPctDisplay"><?php echo (int)($todayPrayer['completion_pct'] ?? 0); ?>%</div>
                            </div>
                            <div style="display:flex;gap:24px;">
                                <div style="text-align:center;">
                                    <span style="font-size:1.5rem;font-weight:700;color:var(--success);" id="todayCompleted"><?php echo (int)($todayPrayer['total_completed'] ?? 0); ?></span>
                                    <div style="font-size:0.8rem;color:var(--gray);">Completed</div>
                                </div>
                                <div style="text-align:center;">
                                    <span style="font-size:1.5rem;font-weight:700;color:var(--danger);" id="todayMissed"><?php echo (int)($todayPrayer['total_missed'] ?? 0); ?></span>
                                    <div style="font-size:0.8rem;color:var(--gray);">Missed</div>
                                </div>
                                <div style="text-align:center;">
                                    <span style="font-size:1.5rem;font-weight:700;color:var(--gold);" id="todayPending"><?php echo 5 - ((int)($todayPrayer['total_completed'] ?? 0) + (int)($todayPrayer['total_missed'] ?? 0)); ?></span>
                                    <div style="font-size:0.8rem;color:var(--gray);">Pending</div>
                                </div>
                            </div>
                        </div>
                        <div style="width:100%;height:10px;background:var(--light-gray);border-radius:5px;overflow:hidden;margin-top:16px;">
                            <div id="prayerProgressBar" style="height:100%;background:var(--primary-gradient);border-radius:5px;width:<?php echo (int)($todayPrayer['completion_pct'] ?? 0); ?>%;transition:width 0.5s ease;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================
            SECTION: TASBEEH
            ============================================ -->
            <div class="dash-section" id="section-tasbeeh" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-hands" style="color:var(--gold);margin-right:8px;"></i> Digital Tasbeeh</h2>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
                        <div style="background:var(--white);border-radius:var(--radius-md);padding:32px;border:1px solid var(--light-gray);text-align:center;">
                            <div style="font-size:0.9rem;color:var(--gray);margin-bottom:16px;">Select Zikr</div>
                            <select id="dashboardZikrSelect" style="width:100%;padding:12px 16px;border:2px solid var(--light-gray);border-radius:var(--radius-sm);font-size:1rem;margin-bottom:24px;background:var(--white);" onchange="dashboardTasbeehReset()">
                                <option value="SubhanAllah">سبحان الله - SubhanAllah</option>
                                <option value="Alhamdulillah">الحمد لله - Alhamdulillah</option>
                                <option value="AllahuAkbar">الله أكبر - Allahu Akbar</option>
                                <option value="LaIlahaIllallah">لا إله إلا الله - La Ilaha Illallah</option>
                                <option value="Astaghfirullah">أستغفر الله - Astaghfirullah</option>
                            </select>
                            
                            <div class="progress-ring-container" style="width:160px;height:160px;">
                                <svg class="progress-ring" width="160" height="160">
                                    <circle class="progress-ring-bg" cx="80" cy="80" r="72" stroke-width="8"></circle>
                                    <circle class="progress-ring-fill" id="tasbeehFullRing" cx="80" cy="80" r="72" stroke-width="8" stroke-dasharray="452.39" stroke-dashoffset="452.39"></circle>
                                </svg>
                                <div class="progress-ring-text">
                                    <span class="progress-ring-value" style="font-size:2.5rem;" id="tasbeehFullCount">0</span>
                                    <span class="progress-ring-label" id="tasbeehFullLabel">سبحان الله</span>
                                </div>
                            </div>
                            
                            <div style="margin-top:20px;font-size:0.9rem;color:var(--gray);">
                                Target: <strong id="tasbeehFullTarget">33</strong>
                            </div>
                            
                            <div style="display:flex;gap:12px;justify-content:center;margin-top:24px;flex-wrap:wrap;">
                                <button class="btn btn-primary btn-lg" style="min-width:120px;" onclick="dashboardTasbeehTap()">
                                    <i class="fas fa-hand-pointer"></i> Tap
                                </button>
                                <button class="btn btn-ghost" onclick="dashboardTasbeehReset()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button class="btn btn-gold" onclick="dashboardTasbeehSave()">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </div>
                            
                            <div style="margin-top:12px;">
                                <small style="color:var(--gray);">Press <kbd style="padding:2px 8px;background:var(--light-gray);border-radius:4px;border:1px solid var(--mid-gray);">Space</kbd> to count</small>
                            </div>
                        </div>
                        
                        <div>
                            <div class="tasbeeh-stats-cards">
                                <div class="tasbeeh-stat-mini">
                                    <div class="tasbeeh-stat-mini-value" id="dashTodayTasbeeh"><?php echo number_format($todayTasbeeh); ?></div>
                                    <div class="tasbeeh-stat-mini-label">Today</div>
                                </div>
                                <div class="tasbeeh-stat-mini">
                                    <div class="tasbeeh-stat-mini-value" id="dashWeekTasbeeh"><?php echo number_format($weeklyTasbeeh); ?></div>
                                    <div class="tasbeeh-stat-mini-label">This Week</div>
                                </div>
                                <div class="tasbeeh-stat-mini">
                                    <div class="tasbeeh-stat-mini-value" id="dashMonthTasbeeh"><?php echo number_format($monthlyTasbeeh); ?></div>
                                    <div class="tasbeeh-stat-mini-label">This Month</div>
                                </div>
                                <div class="tasbeeh-stat-mini">
                                    <div class="tasbeeh-stat-mini-value" id="dashLifeTasbeeh"><?php echo number_format($lifetimeTasbeeh); ?></div>
                                    <div class="tasbeeh-stat-mini-label">Lifetime</div>
                                </div>
                            </div>
                            
                            <div style="background:var(--white);border-radius:var(--radius-md);padding:24px;border:1px solid var(--light-gray);margin-top:16px;">
                                <h4 style="font-size:0.95rem;font-weight:600;margin-bottom:16px;">Weekly Statistics</h4>
                                <canvas id="tasbeehChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================
            SECTION: MY QURAN
            ============================================ -->
            <div class="dash-section" id="section-quran" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-book-quran" style="color:var(--primary);margin-right:8px;"></i> My Quran Progress</h2>
                    </div>
                    
                    <div class="quran-dashboard">
                        <?php if ($lastReadSurah): ?>
                        <div class="quran-last-read">
                            <div class="quran-last-read-icon"><i class="fas fa-book-open"></i></div>
                            <div class="quran-last-read-info">
                                <h4>Surah <?php echo sanitizeOutput($lastReadSurah['surah_name']); ?> (<?php echo (int)$lastReadSurah['surah_number']; ?>)</h4>
                                <p>Last read: <?php echo date('M d, Y h:i A', strtotime($lastReadSurah['last_read_at'])); ?> | Verse <?php echo (int)$lastReadSurah['verse_number']; ?></p>
                            </div>
                            <a href="<?php echo SITE_URL; ?>/quran.php?surah=<?php echo (int)$lastReadSurah['surah_number']; ?>" class="btn btn-primary">
                                <i class="fas fa-play"></i> Continue
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-top:16px;">
                            <?php if (!empty($recentSurahs)): ?>
                                <?php foreach ($recentSurahs as $rs): ?>
                                <div class="tasbeeh-stat-mini" style="text-align:left;padding:16px;">
                                    <div style="font-size:1.2rem;font-weight:700;color:var(--primary);"><?php echo (int)$rs['surah_number']; ?></div>
                                    <div style="font-size:0.9rem;font-weight:600;color:var(--dark);"><?php echo sanitizeOutput($rs['surah_name']); ?></div>
                                    <div style="font-size:0.78rem;color:var(--gray);">Verse <?php echo (int)$rs['verse_number']; ?> | Read <?php echo (int)$rs['times_read']; ?>x</div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--gray);">
                                    <i class="fas fa-book-quran" style="font-size:3rem;display:block;margin-bottom:12px;color:var(--mid-gray);"></i>
                                    <p>No reading history yet. Start reading the Quran!</p>
                                    <a href="<?php echo SITE_URL; ?>/quran.php" class="btn btn-primary" style="margin-top:16px;">
                                        <i class="fas fa-play"></i> Start Reading
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div style="margin-top:24px;">
                            <a href="<?php echo SITE_URL; ?>/quran.php" class="btn btn-primary">
                                <i class="fas fa-book-quran"></i> Open Full Quran
                            </a>
                            <a href="<?php echo SITE_URL; ?>/bookmarks.php" class="btn btn-ghost" style="margin-left:8px;">
                                <i class="fas fa-bookmark"></i> View Bookmarks
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================
            SECTION: MY DUAS
            ============================================ -->
            <div class="dash-section" id="section-duas" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-hand-praying" style="color:var(--gold);margin-right:8px;"></i> My Duas & Azkar</h2>
                    </div>
                    
                    <div class="duas-dashboard">
                        <div class="azkar-progress">
                            <div class="azkar-header">
                                <span class="azkar-title">Morning Azkar</span>
                                <span class="azkar-pct" id="morningAzkarPct">0%</span>
                            </div>
                            <div class="azkar-bar">
                                <div class="azkar-fill" id="morningAzkarFill" style="width:0%;background:var(--gold-gradient);"></div>
                            </div>
                            <div style="margin-top:16px;">
                                <button class="btn btn-gold btn-sm" onclick="window.location.href='<?php echo SITE_URL; ?>/duas.php'">
                                    <i class="fas fa-hand-praying"></i> Read Morning Azkar
                                </button>
                            </div>
                        </div>
                        <div class="azkar-progress">
                            <div class="azkar-header">
                                <span class="azkar-title">Evening Azkar</span>
                                <span class="azkar-pct" id="eveningAzkarPct">0%</span>
                            </div>
                            <div class="azkar-bar">
                                <div class="azkar-fill" id="eveningAzkarFill" style="width:0%;background:var(--primary-gradient);"></div>
                            </div>
                            <div style="margin-top:16px;">
                                <button class="btn btn-primary btn-sm" onclick="window.location.href='<?php echo SITE_URL; ?>/duas.php'">
                                    <i class="fas fa-hand-praying"></i> Read Evening Azkar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($favoriteDuas)): ?>
                    <h3 style="font-size:1rem;font-weight:600;color:var(--dark);margin-bottom:16px;">Saved Duas</h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
                        <?php foreach ($favoriteDuas as $fd): ?>
                        <div style="background:var(--white);border-radius:var(--radius-md);padding:20px;border:1px solid var(--light-gray);">
                            <div style="font-family:var(--font-arabic);font-size:1.3rem;text-align:right;direction:rtl;color:var(--primary-dark);margin-bottom:8px;">
                                <?php echo sanitizeOutput($fd['arabic_text']); ?>
                            </div>
                            <div style="font-size:0.85rem;color:var(--gray);"><?php echo sanitizeOutput($fd['dua_title']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div style="text-align:center;padding:40px;color:var(--gray);">
                        <i class="fas fa-hand-praying" style="font-size:3rem;display:block;margin-bottom:12px;color:var(--mid-gray);"></i>
                        <p>No favorite duas yet. Browse duas and save your favorites!</p>
                        <a href="<?php echo SITE_URL; ?>/duas.php" class="btn btn-primary" style="margin-top:16px;">
                            <i class="fas fa-hand-praying"></i> Browse Duas
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ============================================
            SECTION: ACHIEVEMENTS
            ============================================ -->
            <div class="dash-section" id="section-achievements" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-trophy" style="color:var(--gold);margin-right:8px;"></i> Achievements</h2>
                    </div>
                    
                    <div class="achievements-grid">
                        <?php
                        $allAchievements = [
                            ['key' => 'first_prayer', 'name' => 'First Prayer', 'desc' => 'Complete your first prayer', 'icon' => 'fa-star', 'color' => '#FFD700'],
                            ['key' => 'all_prayers', 'name' => 'All Prayers', 'desc' => 'Complete all 5 prayers', 'icon' => 'fa-check-circle', 'color' => '#28A745'],
                            ['key' => '7_day_streak', 'name' => '7-Day Streak', 'desc' => '7 days of completed prayers', 'icon' => 'fa-fire', 'color' => '#FF6B35'],
                            ['key' => '30_day_streak', 'name' => '30-Day Streak', 'desc' => '30 days of completed prayers', 'icon' => 'fa-crown', 'color' => '#D4AF37'],
                            ['key' => '100_tasbeeh', 'name' => '100 Tasbeeh', 'desc' => 'Complete 100 tasbeeh', 'icon' => 'fa-hands', 'color' => '#0F6D4E'],
                            ['key' => '1000_tasbeeh', 'name' => '1000 Tasbeeh', 'desc' => 'Complete 1000 tasbeeh', 'icon' => 'fa-crown', 'color' => '#D4AF37'],
                            ['key' => 'quran_complete', 'name' => 'Quran Completed', 'desc' => 'Complete reading the Quran', 'icon' => 'fa-book-quran', 'color' => '#0F6D4E'],
                        ];
                        
                        foreach ($allAchievements as $ach):
                            $unlocked = in_array($ach['key'], $achievedKeys);
                        ?>
                        <div class="achievement-badge <?php echo $unlocked ? 'unlocked' : 'locked'; ?>">
                            <div class="achievement-icon <?php echo $unlocked ? 'unlocked' : 'locked'; ?>" style="color:<?php echo $ach['color']; ?>;">
                                <i class="fas <?php echo $ach['icon']; ?>"></i>
                            </div>
                            <div class="achievement-name"><?php echo $ach['name']; ?></div>
                            <div class="achievement-desc"><?php echo $ach['desc']; ?></div>
                            <?php if (!$unlocked): ?>
                            <div style="margin-top:8px;font-size:0.65rem;color:var(--mid-gray);"><i class="fas fa-lock"></i> Locked</div>
                            <?php else: ?>
                            <div style="margin-top:8px;font-size:0.65rem;color:var(--success);"><i class="fas fa-check"></i> Unlocked</div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- ============================================
            SECTION: GOALS
            ============================================ -->
            <div class="dash-section" id="section-goals" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-bullseye" style="color:var(--primary);margin-right:8px;"></i> Daily Goals</h2>
                    </div>
                    
                    <div class="goals-container">
                        <?php if (!empty($userGoals)): ?>
                            <?php foreach ($userGoals as $goal): 
                                $pct = $goal['target_value'] > 0 ? min(round(($goal['current_value'] / $goal['target_value']) * 100), 100) : 0;
                            ?>
                            <div class="goal-card">
                                <div class="goal-header">
                                    <span class="goal-name"><?php echo sanitizeOutput($goal['goal_name']); ?></span>
                                    <span class="goal-pct"><?php echo $pct; ?>%</span>
                                </div>
                                <div class="goal-progress-bar">
                                    <div class="goal-progress-fill" style="width: <?php echo $pct; ?>%;"></div>
                                </div>
                                <div class="goal-stats">
                                    <span><?php echo (int)$goal['current_value']; ?> / <?php echo (int)$goal['target_value']; ?></span>
                                    <span><?php echo $goal['is_completed'] ? '✓ Completed' : 'In progress'; ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--gray);">
                                <i class="fas fa-bullseye" style="font-size:3rem;display:block;margin-bottom:12px;color:var(--mid-gray);"></i>
                                <p>Set your daily goals to track your worship progress!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ============================================
            SECTION: NOTIFICATIONS
            ============================================ -->
            <div class="dash-section" id="section-notifications" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-bell" style="color:var(--primary);margin-right:8px;"></i> Notifications</h2>
                        <button class="btn btn-ghost btn-sm" onclick="markAllRead()">
                            <i class="fas fa-check-double"></i> Mark All Read
                        </button>
                    </div>
                    
                    <?php if (!empty($notifications)): ?>
                        <?php foreach ($notifications as $n): ?>
                        <div class="notif-item" style="background:<?php echo $n['is_read'] ? 'transparent' : 'rgba(15,109,78,0.04)'; ?>;border-radius:var(--radius-sm);padding:16px;margin-bottom:8px;cursor:pointer;" onclick="markNotifRead(<?php echo $n['id']; ?>)">
                            <div class="notif-icon <?php echo $n['type']; ?>">
                                <i class="fas fa-<?php echo $n['icon']; ?>"></i>
                            </div>
                            <div class="notif-content">
                                <h4><?php echo sanitizeOutput($n['title']); ?></h4>
                                <p><?php echo sanitizeOutput($n['message']); ?></p>
                                <div class="notif-time"><?php echo date('M d, Y h:i A', strtotime($n['created_at'])); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align:center;padding:60px;color:var(--gray);">
                            <i class="fas fa-bell" style="font-size:4rem;display:block;margin-bottom:16px;color:var(--mid-gray);"></i>
                            <h3 style="font-size:1.2rem;font-weight:600;color:var(--dark);margin-bottom:8px;">No Notifications</h3>
                            <p>You're all caught up! Notifications will appear here.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ============================================
            SECTION: PROFILE
            ============================================ -->
            <div class="dash-section" id="section-profile" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-user" style="color:var(--primary);margin-right:8px;"></i> My Profile</h2>
                    </div>
                    
                    <div class="profile-section">
                        <div class="profile-card">
                            <div class="profile-avatar">
                                <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                                <label class="profile-avatar-upload" for="avatarUpload">
                                    <i class="fas fa-camera"></i>
                                </label>
                            </div>
                            <input type="file" id="avatarUpload" accept="image/*" style="display:none;" onchange="uploadAvatar(this)">
                            <div class="profile-name"><?php echo sanitizeOutput($_SESSION['user_name'] ?? $_SESSION['username']); ?></div>
                            <div class="profile-email"><?php echo sanitizeOutput($_SESSION['user_email']); ?></div>
                            <div style="color:var(--gray);font-size:0.85rem;">
                                <div><i class="fas fa-calendar" style="margin-right:4px;"></i> Joined: <?php echo date('M Y'); ?></div>
                                <div><i class="fas fa-trophy" style="margin-right:4px;"></i> <?php echo count($achievedKeys); ?> achievements</div>
                            </div>
                        </div>
                        
                        <div class="profile-edit-form">
                            <h3 style="font-size:1.05rem;font-weight:700;color:var(--dark);margin-bottom:20px;">Edit Profile</h3>
                            <div id="profileMessage"></div>
                            <form id="profileForm" onsubmit="return updateProfile(event)">
                                <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                                <div class="form-group">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-input" value="<?php echo sanitizeOutput($_SESSION['user_name'] ?? ''); ?>">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Phone</label>
                                        <input type="text" name="phone" class="form-input" value="<?php echo sanitizeOutput($userProfile['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Country</label>
                                        <input type="text" name="country" class="form-input" value="<?php echo sanitizeOutput($userProfile['country'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" class="form-textarea" rows="3" placeholder="Tell us about yourself"><?php echo sanitizeOutput($userProfile['bio'] ?? ''); ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================
            SECTION: SETTINGS
            ============================================ -->
            <div class="dash-section" id="section-settings" style="display:none;">
                <div class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-cog" style="color:var(--primary);margin-right:8px;"></i> Settings</h2>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Change Password</h3>
                        <div id="passwordMessage"></div>
                        <form id="passwordForm" onsubmit="return changePassword(event)">
                            <input type="hidden" name="csrf_token" value="<?php echo getCsrfToken(); ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-input" required minlength="8">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-input" required minlength="8">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                        </form>
                    </div>
                    
                    <div class="settings-section">
                        <h3>Prayer Reminders</h3>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;">
                            <?php foreach ($prayers as $key => $name): ?>
                            <label style="display:flex;align-items:center;gap:10px;padding:12px;background:var(--off-white);border-radius:var(--radius-sm);cursor:pointer;">
                                <input type="checkbox" checked style="width:18px;height:18px;accent-color:var(--primary);">
                                <span style="font-size:0.9rem;"><?php echo $name; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn btn-primary btn-sm" style="margin-top:16px;" onclick="showToast('Prayer reminders saved!', 'success')">
                            <i class="fas fa-save"></i> Save Reminder Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ============================================
DASHBOARD SCRIPTS
============================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ============================================
// DASHBOARD STATE
// ============================================
const dashboardState = {
    tasbeeh: { count: 0, target: 33, zikr: 'SubhanAllah' }
};

const zikrTargets = {
    SubhanAllah: 33, Alhamdulillah: 33, AllahuAkbar: 33,
    LaIlahaIllallah: 100, Astaghfirullah: 100
};

// ============================================
// SECTION SWITCHING
// ============================================
function switchSection(section) {
    document.querySelectorAll('.dash-section').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
    
    const target = document.getElementById('section-' + section);
    if (target) target.style.display = 'block';
    
    document.querySelector(`.sidebar-item[data-section="${section}"]`)?.classList.add('active');
    
    // Init chart if tasbeeh section
    if (section === 'tasbeeh') initTasbeehChart();
}

// ============================================
// SIDEBAR TOGGLE (Mobile)
// ============================================
function toggleSidebar() {
    document.getElementById('dashboardSidebar').classList.toggle('open');
}

// ============================================
// TOPBAR CLOCK
// ============================================
function updateClock() {
    const now = new Date();
    document.getElementById('topbarClock').textContent = 
        String(now.getHours()).padStart(2, '0') + ':' +
        String(now.getMinutes()).padStart(2, '0') + ':' +
        String(now.getSeconds()).padStart(2, '0');
}
setInterval(updateClock, 1000);
updateClock();

// ============================================
// PRAYER TRACKER
// ============================================
function updatePrayer(prayer, status) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData();
    formData.append('ajax_action', 'update_prayer');
    formData.append('csrf_token', csrf);
    formData.append('prayer', prayer);
    formData.append('status', status);
    
    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update UI
            document.querySelectorAll(`[onclick*="updatePrayer('${prayer}']`).forEach(b => {
                b.classList.toggle('active', b.textContent.includes(status === 'completed' ? '✓' : '✗'));
            });
            
            const pctDisplay = document.getElementById('todayPctDisplay');
            const progressBar = document.getElementById('prayerProgressBar');
            const completedEl = document.getElementById('todayCompleted');
            const missedEl = document.getElementById('todayMissed');
            const pendingEl = document.getElementById('todayPending');
            const streakEl = document.getElementById('streakCount');
            
            if (pctDisplay) pctDisplay.textContent = data.pct + '%';
            if (progressBar) progressBar.style.width = data.pct + '%';
            if (completedEl) completedEl.textContent = data.completed;
            if (missedEl) missedEl.textContent = data.missed;
            if (pendingEl) pendingEl.textContent = 5 - data.completed - data.missed;
            if (streakEl) streakEl.textContent = data.streak || streakEl.textContent;
            
            showToast('Prayer updated!', 'success');
        }
    })
    .catch(() => showToast('Update failed', 'error'));
}

// ============================================
// DASHBOARD TASBEEH
// ============================================
function dashboardTasbeehTap() {
    const state = dashboardState.tasbeeh;
    state.count++;
    
    if (state.count > state.target) {
        state.count = 1;
        showToast('Zikr completed! 🎉', 'success');
    }
    
    updateTasbeehDisplay();
    
    const display = document.querySelector('.progress-ring-container');
    if (display) {
        display.style.transform = 'scale(0.95)';
        setTimeout(() => display.style.transform = 'scale(1)', 100);
    }
}

function dashboardTasbeehReset() {
    const select = document.getElementById('dashboardZikrSelect');
    const state = dashboardState.tasbeeh;
    
    state.zikr = select ? select.value : 'SubhanAllah';
    state.target = zikrTargets[state.zikr] || 33;
    state.count = 0;
    
    updateTasbeehDisplay();
}

function dashboardTasbeehSave() {
    const state = dashboardState.tasbeeh;
    if (state.count === 0) {
        showToast('Please do some tasbeeh first', 'warning');
        return;
    }
    
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData();
    formData.append('ajax_action', 'save_tasbeeh_dashboard');
    formData.append('csrf_token', csrf);
    formData.append('zikr', state.zikr);
    formData.append('count', state.count);
    formData.append('target', state.target);
    
    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Tasbeeh saved!', 'success');
            location.reload();
        }
    })
    .catch(() => showToast('Save failed', 'error'));
}

function updateTasbeehDisplay() {
    const state = dashboardState.tasbeeh;
    const ring = document.getElementById('tasbeehRing') || document.getElementById('tasbeehFullRing');
    const countEl = document.getElementById('tasbeehRingCount') || document.getElementById('tasbeehFullCount');
    const labelEl = document.getElementById('tasbeehFullLabel');
    const targetEl = document.getElementById('tasbeehTargetDisplay') || document.getElementById('tasbeehFullTarget');
    
    if (countEl) countEl.textContent = state.count;
    if (labelEl) labelEl.textContent = state.zikr.replace(/([A-Z])/g, ' $1').trim();
    if (targetEl) targetEl.textContent = state.target;
    
    if (ring) {
        const circumference = ring.getAttribute('stroke-dasharray') ? parseFloat(ring.getAttribute('stroke-dasharray').split(',')[0]) : 326.73;
        const offset = circumference - (state.count / state.target) * circumference;
        ring.style.strokeDashoffset = Math.max(0, offset);
    }
}

// Keyboard support for tasbeeh
document.addEventListener('keydown', function(e) {
    if (e.code === 'Space' && !e.target.matches('input, textarea, select')) {
        const activeSection = document.querySelector('.dash-section[style*="block"]');
        if (activeSection && (activeSection.id === 'section-overview' || activeSection.id === 'section-tasbeeh')) {
            e.preventDefault();
            dashboardTasbeehTap();
        }
    }
});

// ============================================
// TASBEEH CHART
// ============================================
let tasbeehChartInstance = null;

function initTasbeehChart() {
    const canvas = document.getElementById('tasbeehChart');
    if (!canvas) return;
    
    if (tasbeehChartInstance) {
        tasbeehChartInstance.destroy();
    }
    
    // Generate last 7 days labels
    const labels = [];
    const data = [];
    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        labels.push(d.toLocaleDateString('en', { weekday: 'short' }));
        data.push(Math.floor(Math.random() * 50) + 10); // Will be replaced with real data
    }
    
    tasbeehChartInstance = new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Tasbeeh Count',
                data: data,
                backgroundColor: 'rgba(15, 109, 78, 0.6)',
                borderColor: '#0F6D4E',
                borderWidth: 2,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// ============================================
// NOTIFICATIONS
// ============================================
function toggleNotifDropdown() {
    document.getElementById('notifDropdown').classList.toggle('show');
}

function markNotifRead(id) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData();
    formData.append('ajax_action', 'mark_read');
    formData.append('csrf_token', csrf);
    formData.append('notif_id', id);
    
    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(() => {
        // Visual feedback
    });
}

function markAllRead() {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData();
    formData.append('ajax_action', 'mark_read');
    formData.append('csrf_token', csrf);
    formData.append('notif_id', 0);
    
    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(() => location.reload())
    .catch(() => showToast('Failed to mark all as read', 'error'));
}

// ============================================
// PROFILE
// ============================================
function updateProfile(e) {
    e.preventDefault();
    const form = e.target;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData(form);
    formData.append('ajax_action', 'update_profile');
    formData.append('csrf_token', csrf);
    
    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        const msg = document.getElementById('profileMessage');
        if (data.success) {
            msg.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            setTimeout(() => msg.innerHTML = '', 3000);
        } else {
            msg.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
        }
    })
    .catch(() => showToast('Update failed', 'error'));
    
    return false;
}

function changePassword(e) {
    e.preventDefault();
    const form = e.target;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData(form);
    formData.append('ajax_action', 'change_password');
    formData.append('csrf_token', csrf);
    
    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        const msg = document.getElementById('passwordMessage');
        if (data.success) {
            msg.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            form.reset();
            setTimeout(() => msg.innerHTML = '', 3000);
        } else {
            msg.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
        }
    })
    .catch(() => showToast('Update failed', 'error'));
    
    return false;
}

function uploadAvatar(input) {
    if (!input.files || !input.files[0]) return;
    
    const file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        showToast('File too large. Max 2MB.', 'error');
        return;
    }
    
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData();
    formData.append('ajax_action', 'upload_avatar');
    formData.append('csrf_token', csrf);
    formData.append('avatar', file);
    
    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Avatar updated!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Upload failed', 'error');
        }
    })
    .catch(() => showToast('Upload failed', 'error'));
}

// ============================================
// SEARCH
// ============================================
function searchDashboard(query) {
    const cards = document.querySelectorAll('.stat-card, .prayer-track-card, .tasbeeh-stat-mini, .goal-card, .achievement-badge, .notif-item');
    const q = query.toLowerCase().trim();
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = (!q || text.includes(q)) ? '' : 'none';
    });
}

// ============================================
// TOAST NOTIFICATIONS
// ============================================
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = 'toast toast-' + (type || 'info');
    toast.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle') + '"></i> ' + message;
    
    const container = document.getElementById('toastContainer');
    if (!container) {
        const c = document.createElement('div');
        c.id = 'toastContainer';
        c.style.cssText = 'position:fixed;top:80px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
        document.body.appendChild(c);
        c.appendChild(toast);
    } else {
        container.appendChild(toast);
    }
    
    setTimeout(() => toast.remove(), 3000);
}

// ============================================
// INIT
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Init tasbeeh
    dashboardTasbeehReset();
    
    // Close notification dropdown on outside click
    document.addEventListener('click', function(e) {
        const notifBtn = document.querySelector('.topbar-notification-wrapper');
        const notifDropdown = document.getElementById('notifDropdown');
        if (notifBtn && notifDropdown && !notifBtn.contains(e.target)) {
            notifDropdown.classList.remove('show');
        }
    });
    
    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('dashboardSidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !toggleBtn?.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });
});

// ============================================
// RESPONSIVE HANDLING
// ============================================
window.addEventListener('resize', function() {
    if (window.innerWidth > 992) {
        document.getElementById('dashboardSidebar')?.classList.remove('open');
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
