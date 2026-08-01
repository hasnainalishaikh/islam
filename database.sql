-- ============================================================
-- ISLAMIC WEBSITE - Complete Database Schema
-- Premium Islamic Web Application
-- ============================================================

CREATE DATABASE IF NOT EXISTS `islamic_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `islamic_db`;

-- ============================================================
-- USERS TABLE
-- ============================================================
CREATE TABLE `users` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT 'default-avatar.png',
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `last_login` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_email` (`email`),
    INDEX `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BOOKMARKS TABLE (Quran Verses)
-- ============================================================
CREATE TABLE `bookmarks` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED DEFAULT NULL,
    `session_id` VARCHAR(64) DEFAULT NULL,
    `surah_number` INT(3) NOT NULL,
    `verse_number` INT(4) NOT NULL,
    `surah_name` VARCHAR(100) NOT NULL,
    `arabic_text` TEXT DEFAULT NULL,
    `urdu_text` TEXT DEFAULT NULL,
    `english_text` TEXT DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_session_id` (`session_id`),
    INDEX `idx_surah_verse` (`surah_number`, `verse_number`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TASBEEH HISTORY TABLE
-- ============================================================
CREATE TABLE `tasbeeh_history` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED DEFAULT NULL,
    `session_id` VARCHAR(64) DEFAULT NULL,
    `zikr_name` VARCHAR(100) DEFAULT 'SubhanAllah',
    `count` INT(11) UNSIGNED DEFAULT 0,
    `target` INT(11) UNSIGNED DEFAULT 33,
    `date` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_date` (`user_id`, `date`),
    INDEX `idx_session_date` (`session_id`, `date`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FAVORITES TABLE (Hadith, Duas, Articles)
-- ============================================================
CREATE TABLE `favorites` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT(11) UNSIGNED DEFAULT NULL,
    `session_id` VARCHAR(64) DEFAULT NULL,
    `item_type` ENUM('hadith', 'dua', 'article', 'surah') NOT NULL,
    `item_id` VARCHAR(50) NOT NULL,
    `item_title` VARCHAR(255) DEFAULT NULL,
    `item_data` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_type` (`user_id`, `item_type`),
    INDEX `idx_session_type` (`session_id`, `item_type`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CONTACT MESSAGES TABLE
-- ============================================================
CREATE TABLE `contact_messages` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `is_replied` TINYINT(1) DEFAULT 0,
    `reply_message` TEXT DEFAULT NULL,
    `replied_at` DATETIME DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ARTICLES TABLE
-- ============================================================
CREATE TABLE `articles` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `category` VARCHAR(50) NOT NULL,
    `excerpt` TEXT DEFAULT NULL,
    `content` LONGTEXT NOT NULL,
    `featured_image` VARCHAR(255) DEFAULT 'default-article.jpg',
    `author` VARCHAR(100) DEFAULT 'Admin',
    `status` ENUM('draft', 'published') DEFAULT 'published',
    `views` INT(11) UNSIGNED DEFAULT 0,
    `meta_title` VARCHAR(255) DEFAULT NULL,
    `meta_description` TEXT DEFAULT NULL,
    `meta_keywords` VARCHAR(255) DEFAULT NULL,
    `published_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_category` (`category`),
    INDEX `idx_status` (`status`),
    INDEX `idx_published_at` (`published_at`),
    FULLTEXT INDEX `ft_search` (`title`, `excerpt`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- ARTICLE COMMENTS TABLE
-- ============================================================
CREATE TABLE `article_comments` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `article_id` INT(11) UNSIGNED NOT NULL,
    `user_id` INT(11) UNSIGNED DEFAULT NULL,
    `name` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `comment` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_article_id` (`article_id`),
    INDEX `idx_status` (`status`),
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PRAYER TIMES CACHE TABLE
-- ============================================================
CREATE TABLE `prayer_times_cache` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `city` VARCHAR(100) NOT NULL,
    `country` VARCHAR(100) NOT NULL,
    `prayer_date` DATE NOT NULL,
    `fajr` VARCHAR(10) NOT NULL,
    `sunrise` VARCHAR(10) NOT NULL,
    `dhuhr` VARCHAR(10) NOT NULL,
    `asr` VARCHAR(10) NOT NULL,
    `maghrib` VARCHAR(10) NOT NULL,
    `isha` VARCHAR(10) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE INDEX `idx_city_date` (`city`, `country`, `prayer_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- INSERT SAMPLE ARTICLES
-- ============================================================
INSERT INTO `articles` (`title`, `slug`, `category`, `excerpt`, `content`, `featured_image`, `author`, `published_at`) VALUES
('The Importance of Salah in Islam', 'importance-of-salah', 'Worship', 'Discover the profound significance of Salah (prayer) in the life of a Muslim and its spiritual benefits.', '<h2>The Pillar of Islam</h2><p>Salah is the second pillar of Islam and the most important act of worship after the declaration of faith. It is a direct connection between the servant and their Creator.</p><p>Allah says in the Quran: \"Indeed, prayer prohibits immorality and wrongdoing\" (Surah Al-Ankabut, 29:45).</p><p>Prayer is not just a ritual; it is a spiritual journey that nourishes the soul, disciplines the mind, and brings peace to the heart. The five daily prayers serve as constant reminders of our purpose in life.</p><h3>Benefits of Salah</h3><ul><li>Strengthens faith and taqwa (God-consciousness)</li><li>Provides spiritual peace and tranquility</li><li>Teaches discipline and punctuality</li><li>Creates unity among Muslims worldwide</li><li>Brings blessings into daily life</li></ul>', 'salah.jpg', 'Dr. Muhammad Ali', NOW() - INTERVAL 2 DAY),
('Understanding the Beauty of Quranic Recitation', 'beauty-of-quranic-recitation', 'Quran', 'Explore the art of Tajweed and the profound impact of reciting the Quran with proper pronunciation and melody.', '<h2>The Science of Tajweed</h2><p>Tajweed is the science of reciting the Quran correctly, giving each letter its right and its due. The word \"Tajweed\" comes from the Arabic root \"j-w-d,\" meaning to improve or make better.</p><p>When the Quran is recited with Tajweed, it touches the hearts of listeners and enhances understanding. The Prophet Muhammad (peace be upon him) said: \"The best among you are those who learn the Quran and teach it.\"</p><h3>Levels of Recitation</h3><ul><li><strong>Tahqeeq:</strong> Slow, careful recitation with full application of Tajweed rules</li><li><strong>Hadr:</strong> Faster recitation while maintaining Tajweed rules</li><li><strong>Tadweer:</strong> Moderate pace between Tahqeeq and Hadr</li></ul>', 'quran.jpg', 'Hafiz Abdullah', NOW() - INTERVAL 1 DAY),
('The Power of Dua: Connecting with Allah', 'power-of-dua', 'Spirituality', 'Learn about the etiquette of making dua and how it can transform your life and strengthen your relationship with Allah.', '<h2>The Essence of Supplication</h2><p>Dua is the essence of worship. It is a powerful tool that allows believers to communicate directly with Allah, seeking His guidance, mercy, and blessings.</p><p>Allah promises: \"Call upon Me; I will respond to you\" (Surah Ghafir, 40:60). This direct line to the Creator is a gift that every Muslim should cherish and utilize regularly.</p><h3>Etiquette of Making Dua</h3><ul><li>Begin with praising Allah and sending blessings upon the Prophet</li><li>Face the Qibla direction</li><li>Raise your hands with humility</li><li>Make dua with certainty and conviction</li><li>Repeat the dua three times</li><li>Be persistent and patient</li></ul>', 'dua.jpg', 'Ustadha Aisha', NOW()),
('The Significance of Ramadan: A Month of Blessings', 'significance-of-ramadan', 'Worship', 'Understand the spiritual significance of Ramadan, the month of fasting, prayer, and spiritual reflection.', '<h2>The Blessed Month</h2><p>Ramadan is the ninth month of the Islamic calendar and the most sacred month for Muslims worldwide. It is the month in which the Holy Quran was revealed as guidance for mankind.</p><p>Fasting during Ramadan is one of the Five Pillars of Islam and is mandatory for all adult Muslims who are physically able.</p><h3>Spiritual Benefits</h3><ul><li>Develops self-discipline and self-control</li><li>Purifies the soul and body</li><li>Increases empathy for the less fortunate</li><li>Strengthens community bonds through Iftar gatherings</li><li>Multiplies rewards for good deeds</li></ul>', 'ramadan.jpg', 'Imam Yusuf', NOW() - INTERVAL 3 DAY),
('The Life of Prophet Muhammad (PBUH): A Role Model for Humanity', 'life-of-prophet-muhammad', 'History', 'Explore the exemplary life of the Prophet Muhammad (peace be upon him) and his timeless teachings.', '<h2>The Best of Creation</h2><p>Prophet Muhammad (peace be upon him) is the final messenger of Allah and the greatest example for humanity. His life, known as the Seerah, is a complete guide for all aspects of life.</p><p>Allah says: \"Indeed, in the Messenger of Allah you have an excellent example\" (Surah Al-Ahzab, 33:21).</p><h3>Key Aspects of His Character</h3><ul><li>Truthfulness and honesty in all matters</li><li>Compassion and mercy towards all creation</li><li>Justice and fairness in dealings</li><li>Patience and perseverance in adversity</li><li>Generosity and care for the needy</li></ul>', 'prophet.jpg', 'Shaykh Omar', NOW() - INTERVAL 4 DAY);

-- ============================================================
-- INSERT ADMIN USER (password: admin123)
-- ============================================================
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@islamicwebsite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');
