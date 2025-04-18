<?php
// Include session and functions
require_once '../includes/session.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get parameters
$room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
$message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
$is_private = isset($_POST['is_private']) ? (bool)$_POST['is_private'] : false;
$recipient_id = isset($_POST['recipient_id']) && !empty($_POST['recipient_id']) ? intval($_POST['recipient_id']) : null;

// Validate parameters
if ($room_id <= 0 || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// If it's a private message, make sure recipient is specified
if ($is_private && !$recipient_id) {
    echo json_encode(['success' => false, 'message' => 'Recipient required for private message']);
    exit;
}

// Send the message
$message_id = sendMessage($room_id, $_SESSION['user_id'], $message, $is_private, $recipient_id);

if ($message_id) {
    echo json_encode(['success' => true, 'message_id' => $message_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}
?>
