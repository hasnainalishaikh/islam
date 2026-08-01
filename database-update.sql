-- ============================================================
-- ISLAMIC WEBSITE - Dashboard Database Schema Update
-- Premium User Dashboard Tables
-- ============================================================

USE `islamic_db`;

-- ============================================================
-- PRAYER TRACKER TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `prayer_tracker` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `prayer_date` DATE NOT NULL,
    `fajr` ENUM('pending','completed','missed') DEFAULT 'pending',
    `dhuhr` ENUM('pending','completed','missed') DEFAULT 'pending',
    `asr` ENUM('pending','completed','missed') DEFAULT 'pending',
    `maghrib` ENUM('pending','completed','missed') DEFAULT 'pending',
    `isha` ENUM('pending','completed','missed') DEFAULT 'pending',
    `total_completed` INT(2) DEFAULT 0,
    `total_missed` INT(2) DEFAULT 0,
    `completion_pct` DECIMAL(5,2) DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_date` (`user_id`, `prayer_date`),
    INDEX `idx_user_date` (`user_id`, `prayer_date`),
    INDEX `idx_user_stats` (`user_id`, `completion_pct`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TASBEEH STATS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `tasbeeh_stats` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `zikr_name` VARCHAR(100) NOT NULL DEFAULT 'SubhanAllah',
    `count` INT(11) UNSIGNED DEFAULT 0,
    `target` INT(11) UNSIGNED DEFAULT 33,
    `session_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_date` (`user_id`, `session_date`),
    INDEX `idx_user_zikr` (`user_id`, `zikr_name`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- QURAN PROGRESS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `quran_progress` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `surah_number` INT(3) NOT NULL,
    `surah_name` VARCHAR(100) NOT NULL,
    `verse_number` INT(4) DEFAULT 0,
    `total_verses` INT(4) DEFAULT 0,
    `last_read_at` DATETIME DEFAULT NULL,
    `times_read` INT(5) UNSIGNED DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_surah` (`user_id`, `surah_number`),
    INDEX `idx_user_recent` (`user_id`, `last_read_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FAVORITE DUAS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `favorite_duas` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `dua_id` VARCHAR(50) NOT NULL,
    `dua_category` VARCHAR(50) NOT NULL,
    `dua_title` VARCHAR(255) DEFAULT NULL,
    `arabic_text` TEXT DEFAULT NULL,
    `translation` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_dua` (`user_id`, `dua_id`),
    INDEX `idx_user_category` (`user_id`, `dua_category`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ACHIEVEMENTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `achievements` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `achievement_key` VARCHAR(50) NOT NULL,
    `achievement_name` VARCHAR(255) NOT NULL,
    `achievement_desc` TEXT DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT 'trophy',
    `unlocked_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_achievement` (`user_id`, `achievement_key`),
    INDEX `idx_user` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- USER GOALS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_goals` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `goal_type` VARCHAR(50) NOT NULL COMMENT 'prayer_daily, quran_daily, tasbeeh_daily',
    `goal_name` VARCHAR(255) NOT NULL,
    `target_value` INT(11) DEFAULT 0,
    `current_value` INT(11) DEFAULT 0,
    `goal_date` DATE DEFAULT NULL,
    `is_completed` TINYINT(1) DEFAULT 0,
    `completed_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user_type` (`user_id`, `goal_type`),
    INDEX `idx_user_date` (`user_id`, `goal_date`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- NOTIFICATIONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `type` VARCHAR(50) NOT NULL DEFAULT 'info' COMMENT 'prayer_reminder, goal_completed, achievement_unlocked, info',
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT DEFAULT NULL,
    `icon` VARCHAR(50) DEFAULT 'bell',
    `is_read` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_read` (`user_id`, `is_read`),
    INDEX `idx_user_recent` (`user_id`, `created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- USER PROFILE EXTENDED TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS `user_profile` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED NOT NULL UNIQUE,
    `phone` VARCHAR(20) DEFAULT NULL,
    `country` VARCHAR(100) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
    `prayer_streak` INT(5) DEFAULT 0,
    `best_streak` INT(5) DEFAULT 0,
    `total_tasbeeh_count` BIGINT(20) DEFAULT 0,
    `quran_reading_pct` DECIMAL(5,2) DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 
