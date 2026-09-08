<?php
/**
 * Authentication & Session Management
 * 
 * Handles admin authentication, session management, and access control
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// ============================================
// SESSION TIMEOUT
// ============================================

if (isset($_SESSION['admin_id'])) {
    $current_time = time();
    $session_created = $_SESSION['session_created'] ?? $current_time;
    
    // Check if session has expired
    if ($current_time - $session_created > SESSION_TIMEOUT) {
        session_destroy();
        header('Location: ' . ADMIN_URL . 'login.php?session=expired');
        exit;
    }
    
    // Update session created time to keep session alive
    $_SESSION['session_created'] = $current_time;
}

// ============================================
// AUTHENTICATION FUNCTIONS
// ============================================

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Get current logged-in admin data
 */
function getCurrentAdmin($pdo) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("
        SELECT id, username, email, full_name, role, is_active
        FROM admins
        WHERE id = ? AND is_active = 1
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

/**
 * Authenticate admin login
 */
function authenticateAdmin($pdo, $username, $password) {
    // Validate input
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username and password required'];
    }
    
    // Fetch admin by username
    $stmt = $pdo->prepare("
        SELECT id, username, email, password, full_name, role, is_active
        FROM admins
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    // Verify admin exists and is active
    if (!$admin || !$admin['is_active']) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    // Verify password using bcrypt
    if (!password_verify($password, $admin['password'])) {
        return ['success' => false, 'message' => 'Invalid username or password'];
    }
    
    // Set session data
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['admin_name'] = $admin['full_name'];
    $_SESSION['admin_role'] = $admin['role'];
    $_SESSION['session_created'] = time();
    
    return ['success' => true, 'message' => 'Login successful', 'admin' => $admin];
}

/**
 * Logout admin
 */
function logoutAdmin() {
    session_destroy();
    return true;
}

/**
 * Hash password for storage
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

/**
 * Change admin password
 */
function changePassword($pdo, $adminId, $oldPassword, $newPassword, $confirmPassword) {
    // Validate input
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        return ['success' => false, 'message' => 'All fields required'];
    }
    
    // Check if new passwords match
    if ($newPassword !== $confirmPassword) {
        return ['success' => false, 'message' => 'New passwords do not match'];
    }
    
    // Check password length
    if (strlen($newPassword) < MIN_PASSWORD_LENGTH) {
        return ['success' => false, 'message' => 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters'];
    }
    
    // Fetch current admin
    $stmt = $pdo->prepare("
        SELECT password
        FROM admins
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        return ['success' => false, 'message' => 'Admin not found'];
    }
    
    // Verify old password
    if (!password_verify($oldPassword, $admin['password'])) {
        return ['success' => false, 'message' => 'Current password is incorrect'];
    }
    
    // Update password
    $hashedPassword = hashPassword($newPassword);
    $stmt = $pdo->prepare("
        UPDATE admins
        SET password = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$hashedPassword, $adminId]);
    
    return ['success' => true, 'message' => 'Password changed successfully'];
}

// ============================================
// AUTHORIZATION FUNCTIONS
// ============================================

/**
 * Check if user has permission for role
 */
function hasRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userRole = $_SESSION['admin_role'] ?? '';
    
    // Superadmin can do everything
    if ($userRole === 'superadmin') {
        return true;
    }
    
    // Define role hierarchy
    $roleHierarchy = [
        'superadmin' => 3,
        'editor' => 2,
        'moderator' => 1
    ];
    
    // Check if user role meets requirement
    $userLevel = $roleHierarchy[$userRole] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
    
    return $userLevel >= $requiredLevel;
}

/**
 * Require authentication (redirect if not logged in)
 */
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: ' . ADMIN_URL . 'login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Require specific role (redirect if insufficient permissions)
 */
function requireRole($role) {
    requireAuth();
    
    if (!hasRole($role)) {
        header('HTTP/1.0 403 Forbidden');
        die('Access denied. Insufficient permissions.');
    }
}

/**
 * Require superadmin role
 */
function requireSuperAdmin() {
    requireRole('superadmin');
}

/**
 * Require editor or higher
 */
function requireEditor() {
    requireRole('editor');
}

/**
 * Require moderator or higher
 */
function requireModerator() {
    requireRole('moderator');
}

// ============================================
// PERMISSION HELPER FUNCTIONS
// ============================================

/**
 * Can edit content (editors and superadmins)
 */
function canEditContent() {
    return hasRole('editor');
}

/**
 * Can publish content (editors and superadmins)
 */
function canPublishContent() {
    return hasRole('editor');
}

/**
 * Can moderate comments (moderators and editors)
 */
function canModerateComments() {
    return hasRole('moderator');
}

/**
 * Can manage settings (superadmin only)
 */
function canManageSettings() {
    return hasRole('superadmin');
}

/**
 * Can manage users (superadmin only)
 */
function canManageUsers() {
    return hasRole('superadmin');
}

// ============================================
// SECURITY FUNCTIONS
// ============================================

/**
 * Log admin activity
 */
function logActivity($pdo, $action, $details = '') {
    if (!isLoggedIn()) {
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO activity_log (admin_id, action, details, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt->execute([
            $_SESSION['admin_id'],
            $action,
            $details,
            $ipAddress,
            $userAgent
        ]);
    } catch (Exception $e) {
        // Silently fail - activity logging is not critical
        error_log('Activity log error: ' . $e->getMessage());
    }
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate username format
 */
function isValidUsername($username) {
    // Username: 3-50 characters, alphanumeric and underscore only
    return preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username) === 1;
}

/**
 * Check if username already exists
 */
function usernameExists($pdo, $username, $excludeId = null) {
    if ($excludeId) {
        $stmt = $pdo->prepare("
            SELECT id FROM admins
            WHERE username = ? AND id != ?
            LIMIT 1
        ");
        $stmt->execute([$username, $excludeId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT id FROM admins
            WHERE username = ?
            LIMIT 1
        ");
        $stmt->execute([$username]);
    }
    
    return $stmt->fetch() !== false;
}

/**
 * Check if email already exists
 */
function emailExists($pdo, $email, $excludeId = null) {
    if ($excludeId) {
        $stmt = $pdo->prepare("
            SELECT id FROM admins
            WHERE email = ? AND id != ?
            LIMIT 1
        ");
        $stmt->execute([$email, $excludeId]);
    } else {
        $stmt = $pdo->prepare("
            SELECT id FROM admins
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
    }
    
    return $stmt->fetch() !== false;
}

/**
 * Get admin by ID
 */
function getAdminById($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT id, username, email, full_name, role, is_active, created_at, updated_at
        FROM admins
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get all admins
 */
function getAllAdmins($pdo) {
    $stmt = $pdo->prepare("
        SELECT id, username, email, full_name, role, is_active, created_at, updated_at
        FROM admins
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Create new admin
 */
function createAdmin($pdo, $username, $email, $password, $fullName, $role = 'editor') {
    // Validate input
    if (!isValidUsername($username)) {
        return ['success' => false, 'message' => 'Invalid username format'];
    }
    
    if (!isValidEmail($email)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    if (strlen($password) < MIN_PASSWORD_LENGTH) {
        return ['success' => false, 'message' => 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters'];
    }
    
    if (usernameExists($pdo, $username)) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    if (emailExists($pdo, $email)) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Hash password
    $hashedPassword = hashPassword($password);
    
    // Insert admin
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admins (username, email, password, full_name, role, is_active, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())
        ");
        $stmt->execute([$username, $email, $hashedPassword, $fullName, $role]);
        
        return ['success' => true, 'message' => 'Admin created successfully', 'id' => $pdo->lastInsertId()];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error creating admin'];
    }
}

/**
 * Update admin
 */
function updateAdmin($pdo, $id, $email, $fullName, $role, $isActive) {
    // Validate input
    if (!isValidEmail($email)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    if (emailExists($pdo, $email, $id)) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Update admin
    try {
        $stmt = $pdo->prepare("
            UPDATE admins
            SET email = ?, full_name = ?, role = ?, is_active = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$email, $fullName, $role, $isActive ? 1 : 0, $id]);
        
        return ['success' => true, 'message' => 'Admin updated successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error updating admin'];
    }
}

/**
 * Delete admin
 */
function deleteAdmin($pdo, $id) {
    // Prevent deleting only superadmin
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM admins WHERE role = 'superadmin'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    $stmt = $pdo->prepare("SELECT role FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    $admin = $stmt->fetch();
    
    if ($admin['role'] === 'superadmin' && $result['count'] <= 1) {
        return ['success' => false, 'message' => 'Cannot delete the only superadmin'];
    }
    
    // Delete admin
    try {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        
        return ['success' => true, 'message' => 'Admin deleted successfully'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error deleting admin'];
    }
}

?>
