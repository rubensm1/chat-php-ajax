<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include functions
require_once 'functions.php';

/**
 * Session Functions
 */

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Set user session
function setUserSession($user) {
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['last_activity'] = time();
}

// End user session
function endUserSession() {
    // Update user status to offline if logged in
    if (isset($_SESSION['user_id'])) {
        updateUserStatus($_SESSION['user_id'], 'offline');
    }
    
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
}

// Check session timeout (30 minutes)
function checkSessionTimeout() {
    $timeout = 30 * 60; // 30 minutes in seconds
    
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        endUserSession();
        return true;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    return false;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
    
    // Check for session timeout
    if (checkSessionTimeout()) {
        header("Location: login.php?timeout=1");
        exit;
    }
}

// Redirect if already logged in
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: index.php");
        exit;
    }
}
?>
