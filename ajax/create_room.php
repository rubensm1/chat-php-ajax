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

// Get room name
$room_name = isset($_POST['room_name']) ? sanitizeInput($_POST['room_name']) : '';

if (empty($room_name)) {
    echo json_encode(['success' => false, 'message' => 'Room name is required']);
    exit;
}

// Create the room
$room_id = createChatRoom($room_name, $_SESSION['user_id']);

if ($room_id) {
    echo json_encode(['success' => true, 'room_id' => $room_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create room']);
}
?>
