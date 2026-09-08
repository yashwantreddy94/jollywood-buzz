<?php
/**
 * Reusable Functions & Database Query Helpers
 * 
 * Common functions used throughout the application
 * Including database queries, data processing, and utilities
 */

// ============================================
// DATABASE QUERY HELPERS
// ============================================

/**
 * Get single article by slug
 */
function getArticleBySlug($pdo, $slug, $type = 'news') {
    $table = ($type === 'gossip') ? 'gossip' : 'news';
    
    $stmt = $pdo->prepare("
        SELECT n.*, c.name as category_name, c.slug as category_slug
        FROM $table n
        LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.slug = ? AND n.status = 'published'
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get all published articles with pagination
 */
function getArticles($pdo, $page = 1, $limit = ITEMS_PER_PAGE, $type = 'news') {
    $table = ($type === 'gossip') ? 'gossip' : 'news';
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT n.*, c.name as category_name, c.slug as category_slug
        FROM $table n
        LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status = 'published'
        ORDER BY n.published_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get total article count
 */
function getArticleCount($pdo, $type = 'news') {
    $table = ($type === 'gossip') ? 'gossip' : 'news';
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM $table
        WHERE status = 'published'
    ");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

/**
 * Get articles by category
 */
function getArticlesByCategory($pdo, $categorySlug, $page = 1, $limit = ITEMS_PER_PAGE, $type = 'news') {
    $table = ($type === 'gossip') ? 'gossip' : 'news';
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT n.*, c.name as category_name, c.slug as category_slug
        FROM $table n
        LEFT JOIN categories c ON n.category_id = c.id
        WHERE c.slug = ? AND n.status = 'published'
        ORDER BY n.published_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$categorySlug, $limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get trending articles
 */
function getTrendingArticles($pdo, $limit = TRENDING_ITEMS) {
    $stmt = $pdo->prepare("
        SELECT n.*, c.name as category_name, c.slug as category_slug, t.rank
        FROM trending t
        LEFT JOIN news n ON (t.content_type = 'news' AND t.content_id = n.id)
        WHERE t.is_active = 1 AND (n.status = 'published' OR n.id IS NULL)
        ORDER BY t.rank ASC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get hero slider slides
 */
function getHeroSlides($pdo) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM hero_slides
        WHERE status = 'active'
        ORDER BY sort_order ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get latest news
 */
function getLatestNews($pdo, $limit = LATEST_NEWS_COUNT) {
    $stmt = $pdo->prepare("
        SELECT n.*, c.name as category_name, c.slug as category_slug
        FROM news n
        LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status = 'published'
        ORDER BY n.published_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get featured articles
 */
function getFeaturedArticles($pdo, $limit = 3, $type = 'news') {
    $table = ($type === 'gossip') ? 'gossip' : 'news';
    
    $stmt = $pdo->prepare("
        SELECT n.*, c.name as category_name, c.slug as category_slug
        FROM $table n
        LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status = 'published' AND n.is_featured = 1
        ORDER BY n.published_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Search articles
 */
function searchArticles($pdo, $keyword, $page = 1, $limit = ITEMS_PER_PAGE) {
    $offset = ($page - 1) * $limit;
    $searchTerm = '%' . $keyword . '%';
    
    $stmt = $pdo->prepare("
        SELECT 'news' as type, n.id, n.title, n.slug, n.excerpt, n.featured_image, n.published_at, c.name as category_name
        FROM news n
        LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.status = 'published' AND (n.title LIKE ? OR n.content LIKE ? OR n.excerpt LIKE ?)
        
        UNION
        
        SELECT 'gossip' as type, g.id, g.title, g.slug, g.excerpt, g.featured_image, g.published_at, c.name as category_name
        FROM gossip g
        LEFT JOIN categories c ON g.category_id = c.id
        WHERE g.status = 'published' AND (g.title LIKE ? OR g.content LIKE ? OR g.excerpt LIKE ?)
        
        UNION
        
        SELECT 'movie' as type, m.id, m.title, m.slug, m.description as excerpt, m.poster as featured_image, m.release_date as published_at, 'Movies' as category_name
        FROM movies m
        WHERE m.status = 'released' AND (m.title LIKE ? OR m.description LIKE ?)
        
        UNION
        
        SELECT 'tv_show' as type, t.id, t.title, t.slug, t.description as excerpt, t.poster as featured_image, t.start_date as published_at, 'TV Shows' as category_name
        FROM tv_shows t
        WHERE (t.title LIKE ? OR t.description LIKE ?)
        
        UNION
        
        SELECT 'celebrity' as type, c.id, c.name as title, c.slug, c.bio as excerpt, c.profile_image as featured_image, c.created_at as published_at, 'Celebrities' as category_name
        FROM celebrities c
        WHERE c.status = 'active' AND (c.name LIKE ? OR c.bio LIKE ?)
        
        ORDER BY published_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([
        $searchTerm, $searchTerm, $searchTerm,
        $searchTerm, $searchTerm, $searchTerm,
        $searchTerm, $searchTerm,
        $searchTerm, $searchTerm,
        $searchTerm, $searchTerm,
        $limit, $offset
    ]);
    
    return $stmt->fetchAll();
}

/**
 * Get movies
 */
function getMovies($pdo, $page = 1, $limit = 12) {
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT *
        FROM movies
        ORDER BY release_date DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get single movie by slug
 */
function getMovieBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT m.*, 
               GROUP_CONCAT(DISTINCT c.name) as celebrity_names
        FROM movies m
        LEFT JOIN celebrity_movies cm ON m.id = cm.movie_id
        LEFT JOIN celebrities c ON cm.celebrity_id = c.id
        WHERE m.slug = ?
        GROUP BY m.id
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get TV shows
 */
function getTVShows($pdo, $page = 1, $limit = 12) {
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT *
        FROM tv_shows
        ORDER BY start_date DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get single TV show by slug
 */
function getTVShowBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT t.*,
               GROUP_CONCAT(DISTINCT c.name) as celebrity_names
        FROM tv_shows t
        LEFT JOIN celebrity_tv_shows cts ON t.id = cts.tv_show_id
        LEFT JOIN celebrities c ON cts.celebrity_id = c.id
        WHERE t.slug = ?
        GROUP BY t.id
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get celebrities
 */
function getCelebrities($pdo, $page = 1, $limit = 12) {
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT *
        FROM celebrities
        WHERE status = 'active'
        ORDER BY name ASC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get single celebrity by slug
 */
function getCelebrityBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM celebrities
        WHERE slug = ? AND status = 'active'
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get celebrity movies
 */
function getCelebrityMovies($pdo, $celebrityId, $limit = 6) {
    $stmt = $pdo->prepare("
        SELECT m.*
        FROM movies m
        INNER JOIN celebrity_movies cm ON m.id = cm.movie_id
        WHERE cm.celebrity_id = ?
        ORDER BY m.release_date DESC
        LIMIT ?
    ");
    $stmt->execute([$celebrityId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get celebrity TV shows
 */
function getCelebrityTVShows($pdo, $celebrityId, $limit = 6) {
    $stmt = $pdo->prepare("
        SELECT t.*
        FROM tv_shows t
        INNER JOIN celebrity_tv_shows cts ON t.id = cts.tv_show_id
        WHERE cts.celebrity_id = ?
        ORDER BY t.start_date DESC
        LIMIT ?
    ");
    $stmt->execute([$celebrityId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get events
 */
function getEvents($pdo, $page = 1, $limit = 12) {
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT *
        FROM events
        WHERE status = 'upcoming' OR status = 'completed'
        ORDER BY event_date DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get single event by slug
 */
function getEventBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM events
        WHERE slug = ?
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get galleries
 */
function getGalleries($pdo, $page = 1, $limit = 12) {
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT *
        FROM galleries
        WHERE status = 'active'
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get single gallery by slug
 */
function getGalleryBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM galleries
        WHERE slug = ? AND status = 'active'
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get gallery images
 */
function getGalleryImages($pdo, $galleryId) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM gallery_images
        WHERE gallery_id = ?
        ORDER BY sort_order ASC
    ");
    $stmt->execute([$galleryId]);
    return $stmt->fetchAll();
}

/**
 * Get videos
 */
function getVideos($pdo, $page = 1, $limit = 12) {
    $offset = ($page - 1) * $limit;
    
    $stmt = $pdo->prepare("
        SELECT v.*, c.name as category_name
        FROM videos v
        LEFT JOIN categories c ON v.category_id = c.id
        WHERE v.status = 'published'
        ORDER BY v.published_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get featured videos
 */
function getFeaturedVideos($pdo, $limit = 4) {
    $stmt = $pdo->prepare("
        SELECT v.*, c.name as category_name
        FROM videos v
        LEFT JOIN categories c ON v.category_id = c.id
        WHERE v.status = 'published' AND v.is_featured = 1
        ORDER BY v.published_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get single video by slug
 */
function getVideoBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT v.*, c.name as category_name
        FROM videos v
        LEFT JOIN categories c ON v.category_id = c.id
        WHERE v.slug = ? AND v.status = 'published'
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get categories
 */
function getCategories($pdo, $type = null) {
    if ($type) {
        $stmt = $pdo->prepare("
            SELECT *
            FROM categories
            WHERE type = ? AND status = 1
            ORDER BY sort_order ASC
        ");
        $stmt->execute([$type]);
    } else {
        $stmt = $pdo->prepare("
            SELECT *
            FROM categories
            WHERE status = 1
            ORDER BY sort_order ASC
        ");
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

/**
 * Get category by slug
 */
function getCategoryBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM categories
        WHERE slug = ? AND status = 1
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}

/**
 * Get approved comments for content
 */
function getApprovedComments($pdo, $contentType, $contentId) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM comments
        WHERE content_type = ? AND content_id = ? AND status = 'approved'
        ORDER BY created_at DESC
    ");
    $stmt->execute([$contentType, $contentId]);
    return $stmt->fetchAll();
}

/**
 * Add newsletter subscriber
 */
function addNewsletterSubscriber($pdo, $email) {
    try {
        // Check if already subscribed
        $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Already subscribed'];
        }
        
        // Add new subscriber
        $stmt = $pdo->prepare("
            INSERT INTO newsletter_subscribers (email, status, created_at)
            VALUES (?, 'subscribed', NOW())
        ");
        $stmt->execute([$email]);
        
        return ['success' => true, 'message' => 'Successfully subscribed to newsletter'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error subscribing'];
    }
}

/**
 * Add comment (pending approval)
 */
function addComment($pdo, $contentType, $contentId, $name, $email, $comment) {
    try {
        // Basic validation
        if (strlen($comment) < 5) {
            return ['success' => false, 'message' => 'Comment too short'];
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO comments (content_type, content_id, author_name, author_email, comment_text, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$contentType, $contentId, $name, $email, $comment]);
        
        return ['success' => true, 'message' => 'Comment submitted for review'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error submitting comment'];
    }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

/**
 * Format time ago (e.g., "2 hours ago")
 */
function timeAgo($date) {
    if (!$date) return '';
    
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' hour' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}

/**
 * Create URL-friendly slug
 */
function createSlug($text) {
    // Convert to lowercase
    $text = strtolower($text);
    
    // Replace spaces with hyphens
    $text = preg_replace('/\s+/', '-', $text);
    
    // Remove special characters
    $text = preg_replace('/[^a-z0-9\-]/', '', $text);
    
    // Remove multiple hyphens
    $text = preg_replace('/-+/', '-', $text);
    
    // Trim hyphens from start and end
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Sanitize input string
 */
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text
 */
function truncate($text, $length = 150, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Increment view count
 */
function incrementViews($pdo, $contentType, $contentId) {
    $table = '';
    switch ($contentType) {
        case 'news':
            $table = 'news';
            break;
        case 'gossip':
            $table = 'gossip';
            break;
        case 'movie':
            $table = 'movies';
            break;
        case 'tv_show':
            $table = 'tv_shows';
            break;
        case 'celebrity':
            $table = 'celebrities';
            break;
        case 'event':
            $table = 'events';
            break;
        case 'video':
            $table = 'videos';
            break;
    }
    
    if ($table) {
        $stmt = $pdo->prepare("UPDATE $table SET views = views + 1 WHERE id = ?");
        $stmt->execute([$contentId]);
    }
}

/**
 * Get random related articles
 */
function getRelatedArticles($pdo, $categoryId, $currentId, $limit = 3, $type = 'news') {
    $table = ($type === 'gossip') ? 'gossip' : 'news';
    
    $stmt = $pdo->prepare("
        SELECT n.*, c.name as category_name, c.slug as category_slug
        FROM $table n
        LEFT JOIN categories c ON n.category_id = c.id
        WHERE n.category_id = ? AND n.id != ? AND n.status = 'published'
        ORDER BY RAND()
        LIMIT ?
    ");
    $stmt->execute([$categoryId, $currentId, $limit]);
    return $stmt->fetchAll();
}

?>
