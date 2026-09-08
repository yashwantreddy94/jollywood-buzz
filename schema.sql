-- ============================================
-- JOLLYWOOD BUZZ DATABASE
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS jollywood_buzz;
USE jollywood_buzz;

-- ============================================
-- 1. ADMINS TABLE
-- ============================================
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100),
  `role` ENUM('superadmin', 'editor', 'moderator') DEFAULT 'editor',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_username` (`username`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. CATEGORIES TABLE
-- ============================================
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) UNIQUE NOT NULL,
  `description` TEXT,
  `icon` VARCHAR(255),
  `color` VARCHAR(7),
  `type` ENUM('news', 'gossip', 'movies', 'tv_shows', 'celebrities', 'events', 'gallery', 'videos') DEFAULT 'news',
  `sort_order` INT DEFAULT 0,
  `status` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. NEWS TABLE
-- ============================================
CREATE TABLE `news` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `category_id` INT NOT NULL,
  `excerpt` VARCHAR(500),
  `content` LONGTEXT NOT NULL,
  `featured_image` VARCHAR(255),
  `author_id` INT,
  `meta_title` VARCHAR(160),
  `meta_description` VARCHAR(160),
  `meta_keywords` VARCHAR(255),
  `status` ENUM('draft', 'published') DEFAULT 'draft',
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_trending` TINYINT(1) DEFAULT 0,
  `published_at` DATETIME,
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`author_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_published_at` (`published_at`),
  INDEX `idx_views` (`views`),
  FULLTEXT `idx_search` (`title`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. GOSSIP TABLE
-- ============================================
CREATE TABLE `gossip` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `category_id` INT NOT NULL,
  `excerpt` VARCHAR(500),
  `content` LONGTEXT NOT NULL,
  `featured_image` VARCHAR(255),
  `author_id` INT,
  `meta_title` VARCHAR(160),
  `meta_description` VARCHAR(160),
  `meta_keywords` VARCHAR(255),
  `status` ENUM('draft', 'published') DEFAULT 'draft',
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_trending` TINYINT(1) DEFAULT 0,
  `published_at` DATETIME,
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`author_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  INDEX `idx_published_at` (`published_at`),
  FULLTEXT `idx_search` (`title`, `content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. MOVIES TABLE
-- ============================================
CREATE TABLE `movies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `description` LONGTEXT,
  `poster` VARCHAR(255),
  `banner` VARCHAR(255),
  `release_date` DATE,
  `director` VARCHAR(255),
  `producer` VARCHAR(255),
  `cast` TEXT,
  `genre` VARCHAR(255),
  `trailer_url` VARCHAR(500),
  `meta_title` VARCHAR(160),
  `meta_description` VARCHAR(160),
  `meta_keywords` VARCHAR(255),
  `status` ENUM('upcoming', 'released') DEFAULT 'upcoming',
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  FULLTEXT `idx_search` (`title`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 6. TV_SHOWS TABLE
-- ============================================
CREATE TABLE `tv_shows` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `description` LONGTEXT,
  `poster` VARCHAR(255),
  `banner` VARCHAR(255),
  `channel` VARCHAR(100),
  `genre` VARCHAR(255),
  `cast` TEXT,
  `start_date` DATE,
  `meta_title` VARCHAR(160),
  `meta_description` VARCHAR(160),
  `meta_keywords` VARCHAR(255),
  `status` ENUM('ongoing', 'completed') DEFAULT 'ongoing',
  `is_featured` TINYINT(1) DEFAULT 0,
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  FULLTEXT `idx_search` (`title`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 7. CELEBRITIES TABLE
-- ============================================
CREATE TABLE `celebrities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `profile_image` VARCHAR(255),
  `cover_image` VARCHAR(255),
  `bio` LONGTEXT,
  `profession` VARCHAR(100),
  `date_of_birth` DATE,
  `social_facebook` VARCHAR(255),
  `social_instagram` VARCHAR(255),
  `social_youtube` VARCHAR(255),
  `website` VARCHAR(255),
  `meta_title` VARCHAR(160),
  `meta_description` VARCHAR(160),
  `meta_keywords` VARCHAR(255),
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  FULLTEXT `idx_search` (`name`, `bio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 8. CELEBRITY_MOVIES TABLE
-- ============================================
CREATE TABLE `celebrity_movies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `celebrity_id` INT NOT NULL,
  `movie_id` INT NOT NULL,
  `role` VARCHAR(100),
  FOREIGN KEY (`celebrity_id`) REFERENCES `celebrities` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_celebrity_movie` (`celebrity_id`, `movie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 9. CELEBRITY_TV_SHOWS TABLE
-- ============================================
CREATE TABLE `celebrity_tv_shows` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `celebrity_id` INT NOT NULL,
  `tv_show_id` INT NOT NULL,
  `role` VARCHAR(100),
  FOREIGN KEY (`celebrity_id`) REFERENCES `celebrities` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tv_show_id`) REFERENCES `tv_shows` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_celebrity_tvshow` (`celebrity_id`, `tv_show_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 10. EVENTS TABLE
-- ============================================
CREATE TABLE `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `description` LONGTEXT,
  `featured_image` VARCHAR(255),
  `event_date` DATE NOT NULL,
  `event_time` TIME,
  `venue` VARCHAR(255),
  `location` VARCHAR(255),
  `organizer` VARCHAR(255),
  `meta_title` VARCHAR(160),
  `meta_description` VARCHAR(160),
  `meta_keywords` VARCHAR(255),
  `status` ENUM('upcoming', 'completed') DEFAULT 'upcoming',
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_event_date` (`event_date`),
  FULLTEXT `idx_search` (`title`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 11. GALLERIES TABLE
-- ============================================
CREATE TABLE `galleries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `description` TEXT,
  `cover_image` VARCHAR(255),
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `views` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 12. GALLERY_IMAGES TABLE
-- ============================================
CREATE TABLE `gallery_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `gallery_id` INT NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255),
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE,
  INDEX `idx_gallery_id` (`gallery_id`),
  INDEX `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 13. VIDEOS TABLE
-- ============================================
CREATE TABLE `videos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) UNIQUE NOT NULL,
  `description` TEXT,
  `thumbnail` VARCHAR(255),
  `video_url` VARCHAR(500),
  `youtube_url` VARCHAR(500),
  `duration` INT,
  `category_id` INT,
  `meta_title` VARCHAR(160),
  `meta_description` VARCHAR(160),
  `meta_keywords` VARCHAR(255),
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_trending` TINYINT(1) DEFAULT 0,
  `status` ENUM('draft', 'published') DEFAULT 'draft',
  `views` INT DEFAULT 0,
  `published_at` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  INDEX `idx_slug` (`slug`),
  INDEX `idx_status` (`status`),
  FULLTEXT `idx_search` (`title`, `description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 14. TRENDING TABLE
-- ============================================
CREATE TABLE `trending` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `content_type` ENUM('news', 'gossip', 'movies', 'tv_shows', 'videos') NOT NULL,
  `content_id` INT NOT NULL,
  `rank` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_trending` (`content_type`, `content_id`),
  INDEX `idx_rank` (`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 15. HERO_SLIDES TABLE
-- ============================================
CREATE TABLE `hero_slides` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100),
  `content_url` VARCHAR(500),
  `button_text` VARCHAR(50),
  `sort_order` INT DEFAULT 0,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_sort_order` (`sort_order`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 16. COMMENTS TABLE
-- ============================================
CREATE TABLE `comments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `content_type` ENUM('news', 'gossip', 'movies', 'tv_shows', 'celebrities', 'events', 'videos') NOT NULL,
  `content_id` INT NOT NULL,
  `author_name` VARCHAR(100) NOT NULL,
  `author_email` VARCHAR(100) NOT NULL,
  `comment_text` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_content_type_id` (`content_type`, `content_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 17. NEWSLETTER_SUBSCRIBERS TABLE
-- ============================================
CREATE TABLE `newsletter_subscribers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `status` ENUM('subscribed', 'unsubscribed') DEFAULT 'subscribed',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email` (`email`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 18. MEDIA TABLE
-- ============================================
CREATE TABLE `media` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `original_filename` VARCHAR(255),
  `file_path` VARCHAR(500) NOT NULL,
  `file_type` VARCHAR(50),
  `file_size` INT,
  `uploaded_by` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`uploaded_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  INDEX `idx_filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 19. SETTINGS TABLE
-- ============================================
CREATE TABLE `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) UNIQUE NOT NULL,
  `setting_value` LONGTEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 20. PAGE_VIEWS TABLE
-- ============================================
CREATE TABLE `page_views` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `page_url` VARCHAR(500),
  `referer` VARCHAR(500),
  `user_agent` TEXT,
  `ip_address` VARCHAR(45),
  `viewed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_page_url` (`page_url`),
  INDEX `idx_viewed_at` (`viewed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DEFAULT DATA INSERTION
-- ============================================

-- Insert default admin user (PASSWORD: admin123)
INSERT INTO `admins` (`username`, `email`, `password`, `full_name`, `role`, `is_active`) 
VALUES ('admin', 'admin@jollywood-buzz.local', '$2y$10$92IXUNpkm0ll2/yF5PWKeOQlVkbVi9BYvMvhRn.EEfXr1OsgKOlkC', 'Administrator', 'superadmin', 1);

-- Insert default categories
INSERT INTO `categories` (`name`, `slug`, `description`, `type`, `sort_order`, `status`) VALUES
('Latest News', 'latest-news', 'Latest entertainment news', 'news', 1, 1),
('Gossip Buzz', 'gossip-buzz', 'Celebrity gossip and rumors', 'gossip', 1, 1),
('Movies', 'movies', 'Assamese movies', 'movies', 1, 1),
('TV Shows', 'tv-shows', 'Assamese television shows', 'tv_shows', 1, 1),
('Celebrities', 'celebrities', 'Celebrity profiles', 'celebrities', 1, 1),
('Events', 'events', 'Entertainment events', 'events', 1, 1),
('Gallery', 'gallery', 'Photo galleries', 'gallery', 1, 1),
('Videos', 'videos', 'Entertainment videos', 'videos', 1, 1);

-- Insert default settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Jollywood Buzz'),
('site_tagline', 'Real-time updates on the celebrity\'s life, gossips, entertainment news of Assamese Cine and Television industry.'),
('site_email', 'info@jollywood-buzz.local'),
('site_phone', '+91 XXXXXXXXXX'),
('site_address', 'Assam, India'),
('site_facebook', 'https://facebook.com/jollywoodbuzz'),
('site_instagram', 'https://instagram.com/jollywoodbuzz'),
('site_youtube', 'https://youtube.com/jollywoodbuzz'),
('site_twitter', 'https://twitter.com/jollywoodbuzz'),
('articles_per_page', '10'),
('items_per_page', '12');
