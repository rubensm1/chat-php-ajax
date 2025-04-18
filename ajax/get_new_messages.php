<?php
// Include session and functions
require_once '../includes/session.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get room ID and timestamp
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$since = isset($_GET['since']) ? $_GET['since'] : null;

if ($room_id <= 0 || !$since) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Get new messages
$messages = getNewMessages($room_id, $since);

// Return messages as JSON
echo json_encode([
    'success' => true,
    'messages' => $messages
]);
?>
