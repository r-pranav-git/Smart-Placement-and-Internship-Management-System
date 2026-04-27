<?php
/**
 * Smart Placement and Internship Management System
 * Central Utility Functions
 */

/**
 * Escapes HTML for output protection (XSS)
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Checks if the user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Checks if user has a specific role
 */
function has_role($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Redirects if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: /placement/auth/login.php");
        exit();
    }
}

/**
 * Redirects if user doesn't have the required role
 */
function require_role($role) {
    require_login();
    if (!has_role($role)) {
        header("Location: /placement/index.php"); // Or a restricted access page
        exit();
    }
}

/**
 * Generates a CSRF token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifies a CSRF token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }
    return true;
}

/**
 * Renders a CSRF hidden input field
 */
function csrf_field() {
    $token = generate_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}
?>
