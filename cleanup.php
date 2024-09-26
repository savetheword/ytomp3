<?php
session_start();
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

// Session Security Measures: Locking to IP and User-Agent
if (!isset($_SESSION['ip_address'])) {
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
} elseif ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    session_regenerate_id(true); // Regenerate session ID
    session_destroy(); // Destroy session
    die(json_encode(['success' => false, 'error' => 'Session hijacking attempt detected.']));
}

if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
} elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_regenerate_id(true);
    session_destroy();
    die(json_encode(['success' => false, 'error' => 'Session hijacking attempt detected.']));
}

// Rate Limiting
$rateLimit = 10; // Allow 10 requests
$timeFrame = 60; // Within 60 seconds

if (!isset($_SESSION['rate_limit'])) {
    $_SESSION['rate_limit'] = [
        'requests' => 1,
        'start_time' => time()
    ];
} else {
    $_SESSION['rate_limit']['requests']++;
    if (time() - $_SESSION['rate_limit']['start_time'] > $timeFrame) {
        $_SESSION['rate_limit'] = [
            'requests' => 1,
            'start_time' => time()
        ];
    } elseif ($_SESSION['rate_limit']['requests'] > $rateLimit) {
        http_response_code(429); // Too Many Requests
        die(json_encode(['success' => false, 'error' => 'Too many requests. Please try again later.']));
    }
}

// CSRF Protection
if (empty($_POST['csrf_token_clean']) || $_POST['csrf_token_clean'] !== $_SESSION['csrf_token_clean']) {
    die(json_encode(['success' => false, 'error' => 'Invalid CSRF token.']));
}

// File Cleanup Logic
$dir = __DIR__ . '/downloads/';
$files = glob($dir . '*');

foreach ($files as $file) {
    if (is_file($file)) {
        if (time() - filemtime($file) > 100) { // 600 seconds = 10 minutes
            unlink($file);
        }
    }
}

echo json_encode(['success' => true, 'message' => 'Old files cleaned up successfully.']);
?>
