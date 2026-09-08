<?php
/**
 * Application Configuration & Constants
 * 
 * Core application settings, URLs, paths, and constants
 * Update these values to match your deployment environment
 */

// ============================================
// SITE CONFIGURATION
// ============================================

define('SITE_NAME', 'Jollywood Buzz');
define('SITE_TAGLINE', 'Real-time updates on the celebrity\'s life, gossips, entertainment news of Assamese Cine and Television industry.');
define('SITE_DESCRIPTION', 'Jollywood Buzz - Entertainment news and gossip from Assamese cinema and television industry');

// ============================================
// BASE URLS
// ============================================

// Detect protocol (http or https)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

// Detect host
$host = $_SERVER['HTTP_HOST'];

// Base URL (adjust if in subdirectory)
define('BASE_URL', $protocol . '://' . $host . '/jollywood-buzz/');
define('ADMIN_URL', BASE_URL . 'admin/');
define('API_URL', BASE_URL . 'public/api/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('UPLOADS_URL', BASE_URL . 'uploads/');

// ============================================
// FILE PATHS
// ============================================

define('ROOT_PATH', __DIR__ . '/../');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('PUBLIC_PATH', ROOT_PATH . 'public/');
define('ADMIN_PATH', ROOT_PATH . 'admin/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('TEMP_PATH', ROOT_PATH . 'temp/');

// ============================================
// PAGINATION & LISTING
// ============================================

define('ITEMS_PER_PAGE', 10);
define('TRENDING_ITEMS', 5);
define('LATEST_NEWS_COUNT', 4);
define('HERO_SLIDES_COUNT', 5);
define('GALLERY_ITEMS_PER_PAGE', 12);
define('VIDEOS_PER_PAGE', 12);

// ============================================
// IMAGE UPLOAD CONFIGURATION
// ============================================

// Allowed image extensions
$ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
define('ALLOWED_IMAGE_EXTENSIONS', json_encode($ALLOWED_IMAGE_EXTENSIONS));

// Maximum file size (5MB)
define('MAX_FILE_SIZE', 5 * 1024 * 1024);

// Allowed MIME types
$ALLOWED_MIME_TYPES = [
    'image/jpeg',
    'image/png',
    'image/webp',
    'image/gif'
];
define('ALLOWED_MIME_TYPES', json_encode($ALLOWED_MIME_TYPES));

// ============================================
// RICH EDITOR CONFIGURATION
// ============================================

// Allowed HTML tags in article content
define('ALLOWED_HTML_TAGS', '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><a><img><hr>');

// ============================================
// SECURITY CONFIGURATION
// ============================================

// Session configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('SESSION_NAME', 'jollywood_buzz_admin');

// CSRF token name
define('CSRF_TOKEN_NAME', '_token');

// Password requirements
define('MIN_PASSWORD_LENGTH', 8);

// ============================================
// CONTACT & SOCIAL MEDIA
// ============================================

define('CONTACT_EMAIL', 'info@jollywood-buzz.local');
define('CONTACT_PHONE', '+91 XXXXXXXXXX');
define('CONTACT_ADDRESS', 'Assam, India');

// Social media URLs
define('SOCIAL_FACEBOOK', 'https://facebook.com/jollywoodbuzz');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/jollywoodbuzz');
define('SOCIAL_YOUTUBE', 'https://youtube.com/jollywoodbuzz');
define('SOCIAL_TWITTER', 'https://twitter.com/jollywoodbuzz');

// ============================================
// DATE & TIME CONFIGURATION
// ============================================

define('TIMEZONE', 'Asia/Kolkata');
date_default_timezone_set(TIMEZONE);

// ============================================
// ERROR HANDLING
// ============================================

// In development, show errors; in production, set to false
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL);
}

// ============================================
// CACHE CONFIGURATION
// ============================================

// Cache duration in seconds (1 hour)
define('CACHE_DURATION', 3600);

// ============================================
// ADMIN ROLES & PERMISSIONS
// ============================================

define('ADMIN_ROLES', json_encode([
    'superadmin' => 'Super Administrator',
    'editor' => 'Editor',
    'moderator' => 'Moderator'
]));

// ============================================
// CONTENT TYPES
// ============================================

define('CONTENT_TYPES', json_encode([
    'news',
    'gossip',
    'movies',
    'tv_shows',
    'celebrities',
    'events',
    'gallery',
    'videos'
]));

// ============================================
// STATUS CONSTANTS
// ============================================

define('STATUS_DRAFT', 'draft');
define('STATUS_PUBLISHED', 'published');
define('STATUS_UPCOMING', 'upcoming');
define('STATUS_COMPLETED', 'completed');
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_ONGOING', 'ongoing');

// ============================================
// COMMENT STATUSES
// ============================================

define('COMMENT_PENDING', 'pending');
define('COMMENT_APPROVED', 'approved');
define('COMMENT_REJECTED', 'rejected');

// ============================================
// NEWSLETTER STATUSES
// ============================================

define('SUBSCRIBER_ACTIVE', 'subscribed');
define('SUBSCRIBER_INACTIVE', 'unsubscribed');

// ============================================
// LOG FILE CONFIGURATION
// ============================================

define('LOG_PATH', ROOT_PATH . 'logs/');
define('ERROR_LOG', LOG_PATH . 'error.log');
define('ACTIVITY_LOG', LOG_PATH . 'activity.log');

// ============================================
// COLOR PALETTE
// ============================================

define('COLOR_PRIMARY', '#071A33');      // Dark navy
define('COLOR_SECONDARY', '#0B2345');   // Deep blue
define('COLOR_ACCENT_RED', '#E91E3D');  // Red
define('COLOR_ACCENT_YELLOW', '#FFC928'); // Gold/Yellow
define('COLOR_TEXT', '#10284A');        // Text color
define('COLOR_LIGHT_BG', '#F6F7F9');    // Light background
define('COLOR_WHITE', '#FFFFFF');       // White

// ============================================
// ENSURE REQUIRED DIRECTORIES EXIST
// ============================================

$required_dirs = [
    UPLOADS_PATH,
    UPLOADS_PATH . 'news/',
    UPLOADS_PATH . 'gossip/',
    UPLOADS_PATH . 'movies/',
    UPLOADS_PATH . 'tv-shows/',
    UPLOADS_PATH . 'celebrities/',
    UPLOADS_PATH . 'events/',
    UPLOADS_PATH . 'gallery/',
    UPLOADS_PATH . 'media/',
    LOG_PATH,
    TEMP_PATH
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

?>
