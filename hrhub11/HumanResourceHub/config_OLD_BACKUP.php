<?php
// HR Management System - MySQL Configuration
// Update these credentials to match your MySQL server
$host = 'localhost';
$username = 'hruser';
$password = 'password';
$database = 'hr_management_fresh';

// Create MySQL connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");



// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function sanitizeInput($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    return $_SESSION['user_id'];
}

function getCurrentUser() {
    global $conn;
    $userId = sanitizeInput($_SESSION['user_id']);
    $sql = "SELECT * FROM users WHERE id = '$userId'";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function hasPermission($requiredRole) {
    $user = getCurrentUser();
    $roles = ['employee' => 1, 'section_head' => 2, 'manager' => 3, 'dept_head' => 4, 'hr_manager' => 5, 'super_admin' => 6];
    $userLevel = $roles[$user['role']] ?? 0;
    $requiredLevel = $roles[$requiredRole] ?? 0;
    return $userLevel >= $requiredLevel;
}

function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

function redirectWithMessage($url, $message, $type = 'info') {
    setFlashMessage($message, $type);
    header("Location: $url");
    exit();
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function getRoleBadge($role) {
    switch ($role) {
        case 'super_admin': return 'badge-danger';
        case 'hr_manager': return 'badge-warning';
        case 'dept_head': return 'badge-info';
        case 'section_head': return 'badge-primary';
        case 'manager': return 'badge-secondary';
        default: return 'badge-light';
    }
}
?>