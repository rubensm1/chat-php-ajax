<?php
// Include session and functions
require_once '../includes/session.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get room ID
$room_id = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

if ($room_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid room ID']);
    exit;
}

// Get users in the room
$users = getUsersInRoom($room_id);

// Return users as JSON
echo json_encode([
    'success' => true,
    'users' => $users
]);
?>
